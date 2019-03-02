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

    // таблица движения с которой работаем
    $table = 'pr_store_movement_products';

    // получаем весь список расходов
    $expenditures = $this->db->query('
      SELECT id, store_type_id, client_id, product_id, expenditure, DATE_FORMAT(`date`,"%Y-%m-%d") as `date`, `order` 
      FROM '.$table.' 
      WHERE 
        store_type_id = 1  AND 
        expenditure_id IS NOT NULL
      ORDER BY `date`, `order`, id
      ')->result_array();
    $cnt = count($expenditures);
    foreach ($expenditures as $key => $expenditure) {
      echo round($key * 100 / $cnt) . "%\r"; 
      file_put_contents(FCPATH .'uploads/expendituresRest.txt',''."\n".'START client_id='.$expenditure['client_id'].' product_id='.$expenditure['product_id'].' expenditure='.$expenditure['expenditure'].' date: '.$expenditure['date'].' '."\n".''."\n".'',FILE_APPEND);

      // полное движение товара до основного расхода с добавлением %засора по приходу, т.к. в движении этого параметра нет
      $movement_products = $this->db->query('
        SELECT pr_movement.*, comings.weight_defect
        FROM '.$table.' as pr_movement
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

      $movement_comings = $movement_expenditures = array();
      // проходим по всему движению товара, определяем из какого прихода был текущий основной расход
      foreach ($movement_products as $movement){
        // собираем приходы в массив и сохраняем остатки по каждому приходу в отдельное поле
        if($movement['coming']){
          $movement_comings[$movement['id']] = array_merge($movement, array('rest_item' => $movement['coming']));
        }
        // если расход, перебираем приходы, которые были ранее по движению
        if($movement['expenditure']){
          // если приходов не было, записываем расход в movement_expenditures - для странных расходов по которым нет прихода в базе например id = 165
          if(!$movement_comings){
            $movement_expenditures[$movement['id']] = array_merge($movement, array('rest_item' => $movement['expenditure']));
          }
          foreach ($movement_comings as $id => &$movement_coming) {
            // если остаток по приходу есть, вычитаем текущий расход

            if($movement_coming['rest_item'] > 0){
              
              // учитываем остатки по расходам, которые были ранее по движению
              // вычитаем сначала остатки из текущего прихода
              if($movement_expenditures){
                foreach ($movement_expenditures as $key => &$movement_expenditure) {
                  // если остаток по приходу больше остатка по расходу
                  if($movement_coming['rest_item'] >= $movement_expenditure['rest_item']){
                    file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest1 ' . $movement_coming['coming'] . ': ' . $movement_coming['rest_item'] . '-' . $movement_expenditure['rest_item'].''."\n".'',FILE_APPEND);
                    // вычитаем из текущего прихода остаток по расходу
                    $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement_expenditure['rest_item'];
                    // остаток по расходу учтен полностью
                    // удаляем элемент из массива остатков по расходам
                    unset($movement_expenditures[$movement_expenditure['id']]);

                    // записываем остаток по расходу в общем движении товара
                    if($movement_expenditure['id'] == $movement['id']){
                      $movement['expenditure'] = 0;
                    }
                  } else {
                    file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest2 ' . $movement_coming['coming'] . ': ' . $movement_expenditure['rest_item'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);
                    // если остаток по приходу меньше остатка по расходу, учитываем остатки прихода и пересчитываем остаток по расходу
                    $movement_expenditure['rest_item'] = $movement_expenditure['rest_item'] - $movement_coming['rest_item'];
                    // остаток по приходу учтен, обнуляем
                    $movement_coming['rest_item'] = 0;

                    // записываем остаток по расходу в общем движении товара
                    if($movement_expenditure['id'] == $movement['id']){
                      $movement['expenditure'] = $movement_expenditure['rest_item'];
                    }
                  }
                }
                unset($movement_expenditure);
              }

              // считаем остатки по основному движению товара
              if($movement['expenditure'] > 0){
                if($movement_coming['rest_item'] >= $movement['expenditure']){
                  file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest3 ' . $movement_coming['coming'] . ': ' . $movement_coming['rest_item'] . '-' . $movement['expenditure'].''."\n".'',FILE_APPEND);
                  // вычитаем из текущего прихода расход
                  $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement['expenditure'];
                  // расход учтен, обнуляем в основном движении
                  $movement['expenditure'] = 0;
                } else {
                  // расход превышает приход, сохраняем в массив остатков по расходам
                  file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest4 ' . $movement_coming['coming'] . ': ' . $movement['expenditure'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);
                  $movement['expenditure'] = $movement['expenditure'] - $movement_coming['rest_item'];
                  $movement_expenditures[$movement['id']] = array_merge($movement, array('rest_item' => $movement['expenditure']));
                  // приход полностью учтен, обнуляем
                  $movement_coming['rest_item'] = 0;
                }
              }

            }

            // если приходов нет, а расход есть
            if(end($movement_comings) == $movement_coming && $movement_coming['rest_item'] <= 0 && $movement['expenditure'] > 0){
              $movement_expenditures[$movement['id']] = array_merge($movement, array('rest_item' => $movement['expenditure']));
            }

          }
          unset($movement_coming);
        }
      }

      // массив с %засора по расходу
      $expenditure_weight_defect = array();
      // перебираем собранный массив приходов с высчитанными остатками по товару
      foreach ($movement_comings as $id => &$movement_coming) {        
        if($movement_coming['rest_item'] == 0) continue;

        // учитываем остатки по расходам, которые были ранее по движению              
        // вычитаем сначала остатки из текущего прихода
        if($movement_expenditures){
          foreach ($movement_expenditures as $key => &$movement_expenditure) {
            // если остаток по приходу больше остатка по расходу
            if($movement_coming['rest_item'] >= $movement_expenditure['rest_item']){
              file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2_1 rest1 ' . $movement_coming['coming'] . ': ' . $movement_coming['rest_item'] . '-' . $movement_expenditure['rest_item'].''."\n".'',FILE_APPEND);
              // вычитаем из текущего прихода остаток по расходу
              $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement_expenditure['rest_item'];
              // остаток по расходу учтен полностью
              // удаляем элемент из массива остатков по расходам
              unset($movement_expenditures[$movement_expenditure['id']]);
            } else {
              file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2_1 rest2 ' . $movement_coming['coming'] . ': ' . $movement_expenditure['rest_item'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);
              // если остаток по приходу меньше остатка по расходу, учитываем остатки прихода и пересчитываем остаток по расходу
              $movement_expenditure['rest_item'] = $movement_expenditure['rest_item'] - $movement_coming['rest_item'];
              // остаток по приходу учтен, обнуляем
              $movement_coming['rest_item'] = 0;
            }
          }
          unset($movement_expenditure);
        }

        // остатков по расходам остаться не должно, иначе общие остатки уйдут в минус
        if($movement_expenditures){
          file_put_contents(FCPATH .'uploads/expendituresRest.txt',"\n".'ERROR расход превышает приход expenditureID=' . $expenditure['id'] . ' расход без прихода:' . serialize($movement_expenditures) ."\n",FILE_APPEND);
          echo '<br>ERROR расход превышает приход expenditureID=' . $expenditure['id'] . ' расход без прихода:' . serialize($movement_expenditures).'\n';
          // var_dump($expenditure,$movement_expenditures);
        }

        // если остаток по приходу больше расхода
        if($movement_coming['rest_item'] >= $expenditure['expenditure']){
          file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2 rest1 ' . $movement_coming['coming'] . '('.$movement_coming['weight_defect'].'): ' . $movement_coming['rest_item'] . '-' . $expenditure['expenditure'].''."\n".'',FILE_APPEND);

          // записываем % засора и к-во брутто
          $expenditure_weight_defect[] = array(
            'weight_defect' => $movement_coming['weight_defect'],
            'gross' => $expenditure['expenditure']
          );

          // расход полностью учтен
          $expenditure['expenditure'] = 0;
        } else {
          // иначе берем часть прихода на данный расход и идем дальше по приходам
          file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2 rest2 ' . $movement_coming['coming'] . '('.$movement_coming['weight_defect'].'): '  . $expenditure['expenditure'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);

          // записываем % засора и к-во брутто
          $expenditure_weight_defect[] = array(
            'weight_defect' => $movement_coming['weight_defect'],
            'gross' => $movement_coming['rest_item']
          );

          // записываем остаток по расходу
          $expenditure['expenditure'] = $expenditure['expenditure'] - $movement_coming['rest_item'];
        }

      }
      // var_dump('expenditure_weight_defect', $expenditure_weight_defect);
      
      // считаем нетто по расходу
      $expenditure_net = 0;
      foreach ($expenditure_weight_defect as $key => $value) {
        $expenditure_net += round($value['gross'] - $value['gross']*$value['weight_defect']/100);
      }
      file_put_contents(FCPATH .'uploads/expendituresRest.txt','expenditure_net = ' . $expenditure_net,FILE_APPEND);

      $this->db->query("UPDATE ". $table . " SET expenditure_weight_defect = '". serialize($expenditure_weight_defect) ."', expenditure_net = ".$expenditure_net." WHERE id=".$expenditure['id'].";");

      // остатков по расходу остаться не должно, иначе общие остатки уйдут в минус
      if($expenditure['expenditure'] > 0){
        file_put_contents(FCPATH .'uploads/expendituresRest.txt',"\n".'ERROR расход превышает приход expenditure=' . serialize($expenditure) ."\n",FILE_APPEND);
        echo 'ERROR расход превышает приход expenditure=' . serialize($expenditure) ."\n";
        // var_dump($expenditure);
      }
      
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