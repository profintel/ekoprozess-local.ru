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
  * Скрипт считает %засора в расходах 
  * исходя из приходов и остатков
  * проставляет нетто в табл pr_store_expenditures
  * проставляет нетто в табл pr_store_movement_products
  */
  function expendituresRest(){
    // получаем весь список расходов
    $expenditures = $this->db
      ->select('parent.store_type_id, parent.client_id, parent.date, store_expenditures.product_id, store_expenditures.gross, parent.id')
      ->join('store_expenditures parent','parent.id = store_expenditures.parent_id')
      ->where('store_expenditures.parent_id IS NOT NULL')
      ->where('store_expenditures.product_id = 7')
      ->where('store_expenditures.client_id = 43')
      // ->where('store_expenditures.id > 3500')
      ->order_by('parent.date')
      ->order_by('store_expenditures.id')
      ->limit(10)
      ->get('store_expenditures')->result_array();

    foreach ($expenditures as $key => $expenditure) {
      // список приходов клиента по продукту с остатком > 0  
      $movement_products = $this->db
        ->select('pr_movement.*, comings.weight_defect, pr_movement.coming coming1, pr_movement2.coming coming2, pr_movement.date date1, pr_movement2.date date2, SUM(pr_movement3.coming-pr_movement3.expenditure) as sum_rest ')

        ->join('pr_store_comings comings','comings.id = pr_movement.coming_child_id')

        // следующий приход
        ->join('pr_store_movement_products pr_movement2','pr_movement2.date > pr_movement.date AND pr_movement2.date < "'.date('Y-m-d H:i:s',strtotime($expenditure['date'])).'" AND pr_movement2.client_id = ' . $expenditure['client_id'] . ' AND pr_movement2.store_type_id = ' . $expenditure['store_type_id'] . ' AND pr_movement2.product_id = ' . $expenditure['product_id'] . ' AND pr_movement2.coming_id IS NOT NULL','left')
        
        // остаток между приходами
        ->join('pr_store_movement_products pr_movement3','pr_movement3.date >= pr_movement.date AND pr_movement3.date < pr_movement2.date AND pr_movement3.client_id = ' . $expenditure['client_id'] . ' AND pr_movement3.store_type_id = ' . $expenditure['store_type_id'] . ' AND pr_movement3.product_id = ' . $expenditure['product_id'],'left')

        // смотрим расходы по найденному приходу, чтобы учесть остаток от прихода

        ->where(array(
          // 'pr_movement.coming_id IS NOT NULL ' => null,
          'pr_movement.client_id' => $expenditure['client_id'],
          'pr_movement.store_type_id' => $expenditure['store_type_id'],
          'pr_movement.product_id' => $expenditure['product_id'],
          'pr_movement.date < ' => date('Y-m-d H:i:s',strtotime($expenditure['date'])),
        ))
        ->group_by('movement.coming_id')
        ->having('sum_rest > 0 OR sum_rest IS NULL')
        ->get('pr_store_movement_products as pr_movement')->result_array();
      
      echo '<br><br>';
      echo $this->db->last_query() . '<br><br>';
      var_dump($expenditure['id']);
      var_dump($expenditure['gross']);
      var_dump($expenditure['date']);
      echo '<br><br>';
      var_dump($movement_products);
    }
  }
}
?>