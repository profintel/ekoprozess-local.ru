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
        client_id = 43 -- 43
      ORDER BY `date`, `order`, id
      LIMIT 10
      ')->result_array();

    foreach ($expenditures as $key => $expenditure) {
      // список приходов клиента по продукту с остатком > 0 
      $movement_products = $this->db->query('
        SELECT pr_movement.*, comings.weight_defect
        FROM pr_store_movement_products as pr_movement
        LEFT JOIN pr_store_comings comings ON comings.id = pr_movement.coming_child_id

        WHERE 
          pr_movement.client_id = ' . $expenditure['client_id'] . ' AND 
          pr_movement.store_type_id = ' . $expenditure['store_type_id'] . ' AND 
          pr_movement.product_id = ' . $expenditure['product_id'] . ' AND 
          DATE_FORMAT(pr_movement.date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($expenditure['date'])) . '" AND 
          pr_movement.order < ' . $expenditure['order'] . ' 
        GROUP BY pr_movement.id 
        ORDER BY DATE_FORMAT(pr_movement.`date`,"%Y-%m-%d"), pr_movement.`order`, pr_movement.expenditure
      ')->result_array();

      echo '<br><br> START expenditure <br><br>';
      // echo $this->db->last_query() . '<br><br>';
      // var_dump($expenditure);
      var_dump($expenditure['expenditure']);
      var_dump($expenditure['date']);
      var_dump($expenditure['order']);
      echo '<br><br>';
      // var_dump($movement_products);

      $movement_comings = $movement_expenditures = array();
      foreach ($movement_products as $movement){
        // собираем приходы в массив и сохраняем остатки по каждому приходу в отдельное поле
        if($movement['coming']){
          $movement_comings[$movement['id']] = array_merge($movement, array('rest_item' => $movement['coming']));
        }
        if($movement['expenditure']){
          foreach ($movement_comings as $id => &$movement_coming) {
            if($movement_coming['rest_item'] > 0){
              // смотрим есть ли не учтенные расходы
              if($movement_expenditures){

                foreach ($movement_expenditures as $key => &$movement_expenditure) {
                  if($movement_coming['rest_item'] >= $movement_expenditure['rest_item']){
                    $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement_expenditure['rest_item'];
                    unset($movement_expenditures[$movement_expenditure['id']]);

                    if($movement_expenditure['id'] == $movement['id']){
                      $movement['expenditure'] = 0;
                    }
                  } else {
                    $movement_expenditure['rest_item'] = $movement_expenditure['rest_item'] - $movement_coming['rest_item'];
                    $movement_coming['rest_item'] = 0;

                    if($movement_expenditure['id'] == $movement['id']){
                      $movement['expenditure'] = $movement_expenditure['rest_item'];
                    }
                  }
                }

              }

              if($movement_coming['rest_item'] >= $movement['expenditure']){
                echo('action1 rest ' . $movement_coming['rest_item'] . '-' . $movement['expenditure'].'<br>');
                $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement['expenditure'];
                $movement['expenditure'] = 0;
              } else {
                echo('action1 rest ' . $movement['expenditure'] . '-' . $movement_coming['rest_item'].'<br>');
                $movement['expenditure'] = $movement['expenditure'] - $movement_coming['rest_item'];
                $movement_expenditures[$movement['id']] = array_merge($movement, array('rest_item' => $movement['expenditure']));
                $movement_coming['rest_item'] = 0;
              }

            }
          }
        }

        // echo 'movement<br><br>';
        // var_dump($movement);
        // echo 'movement_comings<br><br>';
        // var_dump($movement_comings);
        // echo 'movement_expenditures<br>';
        // var_dump($movement_expenditures);
      }

      echo 'movement_comings<br>';
      var_dump($movement_comings);
      // echo 'movement_expenditures<br><br>';
      // var_dump($movement_expenditures);
      echo 'expenditure<br>';
      var_dump($expenditure['expenditure']);

      foreach ($movement_comings as $id => &$movement_coming) {
        if($movement_coming['rest_item'] > 0){
          // смотрим есть ли не учтенные расходы
          if($movement_expenditures){

            foreach ($movement_expenditures as $key => &$movement_expenditure) {
              if($movement_coming['rest_item'] >= $movement_expenditure['rest_item']){
                $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement_expenditure['rest_item'];
                unset($movement_expenditures[$movement_expenditure['id']]);
              } else {
                $movement_expenditure['rest_item'] = $movement_expenditure['rest_item'] - $movement_coming['rest_item'];
                $movement_coming['rest_item'] = 0;
              }
            }

          }

          if($movement_coming['rest_item'] >= $expenditure['expenditure']){
            echo('action2 rest '. $movement_coming['rest_item'] . '-' . $expenditure['expenditure'].'<br>');
            $movement_coming['rest_item'] = $movement_coming['rest_item'] - $expenditure['expenditure'];
            $expenditure['expenditure'] = 0;
          } else {
            echo('action2 rest ' . $expenditure['expenditure'] . '-' . $movement_coming['rest_item'].'<br>');
            $expenditure['expenditure'] = $expenditure['expenditure'] - $movement_coming['rest_item'];

            // $movement_expenditures[$expenditure['id']] = array_merge($expenditure, array('rest_item' => $expenditure['expenditure']));

            $movement_coming['rest_item'] = 0;
          }

        }
      }

      echo '<br><br>EXP RESULT<br><br>';
      echo 'movement_comings<br>';
      var_dump($movement_comings);
      echo 'movement_expenditures<br>';
      var_dump($movement_expenditures);
    }
  }

}
?>