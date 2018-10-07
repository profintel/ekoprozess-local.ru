<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Класс для кроновских скриптов
*/
class Cron extends PR_Controller {
  
  function __construct() {
    parent::__construct();
    
    if (!$this->input->is_cli_request()) {
      // show_404();
    }
  }
  
  /**
  * Скрипт считает %засора в расходах Первичной продукции
  * исходя из приходов и остатков
  * проставляет нетто в табл pr_store_expenditures
  * проставляет нетто в табл pr_store_movement_products
  */
  function expendituresRest(){
    // получаем весь список расходов
    $expenditures = $this->db->query('
      SELECT id, store_type_id, client_id, product_id, expenditure, DATE_FORMAT(`date`,"%Y-%m-%d") as `date`, `order`
      FROM pr_store_movement_products 
      WHERE 
        store_type_id = 1  AND 
        expenditure_id IS NOT NULL AND 
        product_id = 7 AND 
        client_id = 60 -- 43
      ORDER BY `date`, `order`, id
      LIMIT 10
      ')->result_array();

    $expenditure_exc = array();
    //приходы которые исключаем, если по ним остаток = 0
    $movement_exc = $movement_rests = array();
    foreach ($expenditures as $key => $expenditure) {
      // список приходов клиента по продукту с остатком > 0  
      $movement_products = $this->db->query('
        SELECT pr_movement.*, 
              comings.weight_defect, 
              pr_movement.id id1, 
              pr_movement2.id id2, 
              pr_movement.coming coming1, 
              pr_movement2.coming coming2, 
              DATE_FORMAT(pr_movement.date,"%Y-%m-%d") date1, 
              DATE_FORMAT(pr_movement2.date,"%Y-%m-%d") date2
        FROM pr_store_movement_products as pr_movement
        INNER JOIN pr_store_comings comings ON comings.id = pr_movement.coming_child_id
        -- следующий приход
        LEFT JOIN ( 
            SELECT * FROM pr_store_movement_products pr_movement_inner 
            WHERE
              DATE_FORMAT(pr_movement_inner.date,"%Y-%m-%d") <= "' . date('Y-m-d',strtotime($expenditure['date'])) . '" AND
              pr_movement_inner.client_id = ' . $expenditure['client_id'] . ' AND 
              pr_movement_inner.store_type_id = ' . $expenditure['store_type_id'] . ' AND 
              pr_movement_inner.product_id = ' . $expenditure['product_id'] . '
            ORDER BY pr_movement_inner.order
          ) 
          as pr_movement2 ON 
          (
            pr_movement2.id != pr_movement.id AND 
            pr_movement2.coming_id IS NOT NULL AND 
            DATE_FORMAT(pr_movement2.date,"%Y-%m-%d") >= DATE_FORMAT(pr_movement.date,"%Y-%m-%d") AND 
            pr_movement2.order > pr_movement.order 
          )

        WHERE 
          pr_movement.client_id = ' . $expenditure['client_id'] . ' AND 
          pr_movement.store_type_id = ' . $expenditure['store_type_id'] . ' AND 
          pr_movement.product_id = ' . $expenditure['product_id'] . ' AND 
          DATE_FORMAT(pr_movement.date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($expenditure['date'])) . '"  
        GROUP BY pr_movement.coming_id 
        ORDER BY DATE_FORMAT(pr_movement.`date`,"%Y-%m-%d"), pr_movement.`order`, pr_movement.id

      ')->result_array();


      echo '<br><br> expenditure <br><br>';
      echo $this->db->last_query() . '<br><br>';
      // var_dump($expenditure);
      var_dump($expenditure['id']);
      var_dump($expenditure['expenditure']);
      var_dump($expenditure['date']);
      echo '<br><br>';
      // var_dump($movement_products);

      $movement_prev = array();
      foreach ($movement_products as $movement){
        if(in_array($movement['id'], $movement_exc)) continue;
        
        // считаем остаток на момент следующего прихода
        // чтобы понять из текущего прихода в этот расход что-то идет или нет
        $sum_exp = $this->db->query('
            SELECT SUM(expenditure) as `sum` FROM pr_store_movement_products pr_movement3
            WHERE 
            DATE_FORMAT(pr_movement3.date,"%Y-%m-%d") >= "'.date('Y-m-d',strtotime((!empty($movement_prev) ? ($movement_prev['date2'] ? $movement_prev['date2'] : $movement_prev['date']) : $movement['date']))).'" AND 
            DATE_FORMAT(pr_movement3.date,"%Y-%m-%d") <= 
            "'.($movement['date2'] ? date('Y-m-d',strtotime($movement['date2'])) : date('Y-m-d',strtotime($expenditure['date'])) ).'" AND
            '.($movement['date2'] ? 'pr_movement3.id != '. $movement['id2'] . ' AND ' : '').'
            pr_movement3.id != '. $expenditure['id'] .' AND 
            '. ($expenditure_exc ? 'pr_movement3.id NOT IN ('. implode(',', $expenditure_exc) .') AND ' : '') .'
            pr_movement3.client_id = ' . $expenditure['client_id'] . ' AND 
            pr_movement3.store_type_id = ' . $expenditure['store_type_id'] . ' AND 
            pr_movement3.product_id = ' . $expenditure['product_id'] . ' AND
            pr_movement3.expenditure IS NOT NULL AND 
            pr_movement3.order < '.$expenditure['order'].'
          ')->row()->sum;
        echo 'movement_prev<br><br>';
        var_dump($movement_prev);
        if(!empty($movement_prev)){
          // $movement['rest'] = $movement_prev['rest'] + $movement['coming'];
        }
        if(!empty($movement_rests[$movement['id']])){
          $movement['rest'] = $movement_rests[$movement['id']];
          echo 'movement_rests<br><br>';
          var_dump($movement['rest']);
        }
        $rest_commin = $movement['rest'] - $sum_exp;
        if($rest_commin > 0){
          // если остаток больше 0 определяем % засора для текущего расхода
          if($rest_commin > $expenditure['expenditure']){
            // записываем процент засора и нетто

            // смотрим остаток по приходу
            $rest_commin = $rest_commin - $expenditure['expenditure'];
          } else {
            // высчитываем % засора для части расхода


            // оставшаяся часть расхода
            $expenditure['expenditure'] = $expenditure['expenditure'] - $rest_commin;
            $rest_commin = 0;
          }
          
        } 

        if($rest_commin === 0) {
          $movement_exc[] = $movement['id'];
          // $expenditure_exc[] = $expenditure['id'];
        }
        // записываем остаток прихода
        $movement_rests[$movement['id']] = $rest_commin;

        // исключаем из расчета расход
        $expenditure_exc[] = $expenditure['id'];

        $movement['rest'] = $rest_commin;
        $movement_prev = $movement;


        echo 'movement<br><br>';
        var_dump($movement);
        echo '<br><br>'.$this->db->last_query() . '<br><br>';
        var_dump($movement['rest'], $sum_exp, $rest_commin);
        echo '<br><br>';
      }
    }
  }
}
?>