<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Класс для кроновских скриптов
*/
class Cron extends PR_Controller {
  
  function __construct() {
    parent::__construct();
    
    if (!$this->input->is_cli_request()) {
      show_404();
    }

    $this->load->model('store/models/store_model');
  }
  
  /**
  * Скрипт считает %засора в расходах Первичной продукции
  * исходя из приходов и остатков
  * проставляет нетто в табл pr_store_expenditures
  * проставляет нетто в табл pr_store_movement_products
  */
  function expendituresRest(){
    file_put_contents(FCPATH .'uploads/expendituresRest.txt', 'START SCRIPT '.date('d.m.Y H:i:s'));
    // получаем весь список расходов
    $expenditures = $this->db->query('
      SELECT id, store_type_id, client_id, product_id, expenditure, DATE_FORMAT(`date`,"%Y-%m-%d") as `date`, `order` 
      FROM pr_store_movement_products
      WHERE 
        store_type_id = 1 AND 
        expenditure_id IS NOT NULL
      ORDER BY `date`, `order`, id
      ')->result_array();
    $cnt = count($expenditures);
    foreach ($expenditures as $key => $expenditure) {
      echo round($key * 100 / $cnt) . "%\r";
      file_put_contents(FCPATH .'uploads/expendituresRest.txt',''."\n".'START client_id='.$expenditure['client_id'].' product_id='.$expenditure['product_id'].' expenditure='.$expenditure['expenditure'].' date: '.$expenditure['date'].' '."\n".''."\n".'',FILE_APPEND);
      // считаем нетто по расходу
      $result = $this->store_model->calculate_expenditure_net($expenditure);
      if(!$result){
        file_put_contents(FCPATH .'uploads/expendituresRest.txt',''."\n".''."\n".' error calculate_expenditure_net');
      }

      $this->db->query("UPDATE pr_store_movement_products SET expenditure_weight_defect = '". serialize($result['expenditure_weight_defect']) ."', expenditure_net = ".$result['expenditure_net']." WHERE id=".$expenditure['id'].";");
      
      file_put_contents(FCPATH .'uploads/expendituresRest.txt',''."\n".''."\n".'END client_id='.$expenditure['client_id'].' product_id='.$expenditure['product_id'].' expenditure='.$expenditure['expenditure'].' date: '.$expenditure['date'].' '."\n\n",FILE_APPEND);
      unset($movement_comings, $movement_expenditures);
    }

    echo "\nOK\n";
  }

  /**
  * Скрипт прописывает в таблице движения товара
  * нетто прихода, % засора и упаковку
  */
  function setCommingsNet(){
    $this->db->query(
      '
        UPDATE pr_store_movement_products INNER JOIN pr_store_comings
        ON pr_store_comings.id = pr_store_movement_products.coming_child_id
        SET 
        pr_store_movement_products.coming_weight_defect = pr_store_comings.weight_defect,
        pr_store_movement_products.coming_weight_pack = pr_store_comings.weight_pack,
        pr_store_movement_products.coming_net = ROUND(pr_store_comings.gross - pr_store_comings.weight_pack - pr_store_comings.gross * pr_store_comings.weight_defect / 100)
      '
    );
  }

  /**
  * Перезаписывает order в движении сырья
  */
  function set_order_movement(){
    echo 'start set_order_movement'."\n";
    $this->store_model->set_order_movement(false, true);
    echo 'end set_order_movement'."\n";
  }
  
  /**
  * Пересчитывает все остатки брутто в движении сырья
  */
  function set_rests(){ 
    echo 'start set_rests'."\n";
    // пересчитывает все остатки в движении сырья
    $this->store_model->set_rests(false, true);
    echo 'end set_rests'."\n";
  }
  
  /**
  * Пересчитывает все остатки брутто в движении сырья
  */
  function set_rests_net(){ 
    echo 'start set_rests_net'."\n";
    // пересчитывает все остатки в движении сырья
    $this->store_model->set_rests_net(false, true);
    echo 'end set_rests_net'."\n";
  }



    /* Заполняет coming_child_id и expenditure_child_id

    UPDATE `pr_store_movement_products` t1 SET 
    coming_child_id = (SELECT id FROM `pr_store_comings` t2 WHERE t2.store_type_id = 1 AND t2.parent_id = t1.coming_id AND t2.product_id = t1.product_id AND t2.gross = t1.coming)
    WHERE t1.store_type_id = 1;

    UPDATE `pr_store_movement_products` t1 SET 
    coming_child_id = (SELECT id FROM `pr_store_comings` t2 WHERE t2.store_type_id = 2 AND t2.parent_id = t1.coming_id AND t2.product_id = t1.product_id AND t2.net = t1.coming)
    WHERE t1.store_type_id = 2;

    UPDATE `pr_store_movement_products` t1 SET 
    expenditure_child_id = (SELECT id FROM `pr_store_expenditures` t2 WHERE t2.store_type_id = 1 AND t2.parent_id = t1.expenditure_id AND t2.product_id = t1.product_id AND t2.gross = t1.expenditure)
    WHERE t1.store_type_id = 1;


    UPDATE `pr_store_movement_products` t1 SET 
    expenditure_child_id = (SELECT id FROM `pr_store_expenditures` t2 WHERE t2.store_type_id = 2 AND t2.parent_id = t1.expenditure_id AND t2.product_id = t1.product_id AND t2.net = t1.expenditure)
    WHERE t1.store_type_id = 2;
    
    */
}
?>