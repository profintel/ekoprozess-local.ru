<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
    $this->load->model('cities/models/cities_model');
    $this->load->model('clients/models/clients_model');
    $this->load->model('products/models/products_model');
    $this->load->model('acceptances/models/acceptances_model');
  }

  /**
  * Типы продукции
  * @param $where - массив с параметрами поиска
  *        $limit - к-во строк в результате
  *        $offset - стартовая строка поиска
  *        $order_by - массив с параметрами сортировки результата
  * @return array
  */  
  function get_store_types($where = array(), $limit = 0, $offset = 0, $order_by = array()) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }      
    } else {
      $this->db->order_by('order','asc');
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('store_types')->result_array();
    
    return $items;
  }

  /**
  * Тип продукции
  * @param $where - массив с параметрами поиска
  * @return array
  */
  function get_store_type($where = array()) {
    return $this->db->get_where('store_types', $where)->row_array();
  }

  /**
  * Список приходов
  */
  function get_comings($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = array()) {
    $this->db->select('store_comings.*');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_comings.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_comings t2','t2.parent_id = store_comings.id');
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field,$dest);
      }
    } else {
      $this->db->order_by('date_second','desc');
      $this->db->order_by('id','asc');
    }
    $this->db->group_by('store_comings.id');
    $items = $this->db->get('store_comings')->result_array();
    unset($where);
    foreach ($items as $key => &$item) {
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title_full'];
        }
      }
      if($item['store_workshop_id']){
        $item['workshop'] = $this->workshops_model->get_workshop(array('id'=>$item['store_workshop_id']));
      }
      // акт приемки
      $item['acceptance'] = $this->acceptances_model->get_acceptance(array('client_acceptances.store_coming_id'=>$item['id']));
      //считаем общие параметры
      if(is_null($item['parent_id'])){
        // Делаем запрос на дочерние, для отображения видов сырья
        $this->db->select('store_comings.*,t2.title_full as product_title');
        // join-им чтобы вывести название товара и отчет по группе продукции
        $this->db->join('products t2','t2.id = store_comings.product_id');
        $where = 'store_comings.parent_id = '.$item['id'];
        if ($product_id) {          
          $where .= ' AND (';
          foreach ($product_id as $key => $value) {
            if($key != 0){
              $where .= ' OR ';
            }
            $where .= 't2.id = '.$value.' OR t2.parent_id = '.$value;
          }
          $where .= ')';
        }
        $this->db->where($where);
        $this->db->order_by('store_comings.order','asc');
        $this->db->order_by('store_comings.id','asc');
        $item['childs'] = $this->db->get('store_comings')->result_array();


        $item['gross'] = $item['net'] = $item['price'] = $item['sum'] = 0;
        foreach ($item['childs'] as $key => &$child) {
          $item['gross'] += $child['gross'];
          $item['net'] += $child['net'];
        }
        unset($child);
      }
    }
    unset($item);
    
    return $items;
  }
  
  /**
  * Количество приходов по условию
  */
  function get_comings_cnt($where = '', $product_id = array()) {
    $this->db->select('COUNT(DISTINCT(pr_store_comings.id)) as cnt');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_comings.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_comings t2','t2.parent_id = store_comings.id');
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }
    return $this->db->get('store_comings')->row()->cnt;
  }

  /*
  * Возвращает сумму указанного параметра
  * @params: $field - поле, по которму считаем сумму
  *          $where - условие запроса
  *          $product_id - условие с учетом определенных видов вторсырья
  */
  function get_comming_sum_field($field = '',$where = array(), $product_id = array()){
    if(!$field) return false;
    // берем родительcкие элементы, т.к. дата прихода хранится только в них
    $this->db->where(array('store_comings.parent_id'=>null));
    if ($where) {
      $this->db->where($where);
    }
    // включаем в запрос дочерние строки, т.к. цифры по весу хранятся в них
    $this->db->join('store_comings t2','t2.parent_id = store_comings.id');
    $this->db->select('SUM(t2.'.$field.') as sum');

    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }

    $item = $this->db->get('store_comings')->row()->sum;

    return $item;
  }

  /**
  * Одна единица прихода
  */
  function get_coming($where = array(),$full = true) {
    $this->db->select('store_comings.*');
    $item = $this->db->get_where('store_comings', $where)->row_array();
    if($item){
      $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/store/comings/'.$item['id'].'/'));
      if($full){
        if($item['client_id']){
          $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
          if($item['client']){
            $item['client_title'] = $item['client']['title'];
            if($item['client']['city_id']){
              $item['city'] = $this->cities_model->get_city(array('id' => $item['client']['city_id']));
            }
          }
        }
        // акт приемки
        $item['acceptance'] = $this->acceptances_model->get_acceptance(array('client_acceptances.store_coming_id'=>$item['id'])); 
      }

      if($item['parent_id']){
        $item['product'] = $this->products_model->get_product(array('id' => $item['product_id']));
      } elseif($full) {
        // вторсырье
        $item['childs'] = $this->get_comings(0,0,array('parent_id'=>$item['id']),array('order'=>'asc','id'=>'asc'));
        foreach ($item['childs'] as $key => &$child) {
          $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
          // остаток сырья на складе по клиенту
          if($child['active']){
            // если расход отправлен на склад, выводим остатки на момент добавления расхода на склад
            $rest = $this->get_rest(array('coming_child_id' => $child['id']));
            $child['rest'] = ($rest ? $rest['rest'] : 0.00);
            $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
          } else {
            // если расход НЕ отправлен на склад, выводим остатки по последней строке из движения по сырью и клиенту
            $rest = $this->get_rest(array('store_type_id' => $child['store_type_id'],'client_id' => $child['client_id'],'product_id' => $child['product_id']));
            $child['rest'] = ($rest ? $rest['rest'] : 0.00);
            $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
          }
        }
        unset($child);
      }
    }

    return $item;
  }

  function create_coming($params) {
    if ($this->db->insert('store_comings', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_coming($id, $params) {
    if ($this->db->update('store_comings', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_coming($cond) {
    if(!$cond){
      return false;
    }
    if(is_int($cond)){
      $this->db->where(array('id' => $cond));
    } else {
      $this->db->where($cond);
    }
    if ($this->db->delete('store_comings')) {
      return true;
    }
    return false;
  }
  
  /*
  * Подсчитывает остаток сырья на складе
  * @param params - тип склада, вид вторсырья, ...
  *        product_id - массив с id видов вторсырья
  *        rest_clients - отметка, разбивать остатки по клиентам или нет
  * @return array - если rest_clients=true возвращает массив остатков по клиентам
  *         integer - если rest_clients=false возвращает общий остаток
  */
  function calculate_rest($params, $product_id = false, $rest_clients = false, $net = false) {
    if($net){
      $this->db->select('(SUM(coming_net)-SUM(expenditure_net)) as sum');
    } else {
      $this->db->select('(SUM(coming)-SUM(expenditure)) as sum');
    }
    $this->db->select('DATE_FORMAT(pr_store_movement_products.date,"%Y-%m-%d") as date_format', false);
    // join-им чтобы вывести отчет по группе продукции или подробные остатки
    if($product_id || $rest_clients){
      $this->db->join('products','pr_products.id = store_movement_products.product_id');
    }
    if($product_id){
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->where('(pr_products.id IN ('.implode(',', $product_id).') OR pr_products.parent_id IN ('.implode(',', $product_id).'))');
    }
    $this->db->where($params);
    if($rest_clients){
      $this->db->select('clients.title_full as client, products.title_full as product');
      $this->db->join('clients','clients.id = store_movement_products.client_id');
      $this->db->group_by('product_id');
      $this->db->group_by('client_id');
      $this->db->having('sum > 0');
      $this->db->order_by('clients.title_full');
      $result = $this->db->get('store_movement_products')->result_array();
    } else {
      $this->db->order_by('date_format');
      $this->db->order_by('store_movement_products.order');
      $result = $this->db->get('store_movement_products')->row()->sum;
    }
    
    // echo "<br>calculate_rest<br>";
    // echo $this->db->last_query();
    return $result;
  }
  
  /*
  * Подсчитывает приход сырья на складе
  * @param params - тип склада, вид вторсырья, ...
  * $net - считать нетто
  */
  function calculate_coming($params, $product_id = false, $net = false) {
    if($net){
      $this->db->select('SUM(coming_net) as sum');
    } else {
      $this->db->select('SUM(coming) as sum');
    }
    
    if($product_id){
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products','pr_products.id = store_movement_products.product_id');
      $this->db->where('(pr_products.id IN ('.implode(',', $product_id).') OR pr_products.parent_id IN ('.implode(',', $product_id).'))');
    }
    $this->db->where($params);
    $item = $this->db->get('store_movement_products')->row()->sum;
    // echo "<br>calculate_coming<br>";
    // echo $this->db->last_query();
    return $item;
  }
  
  /*
  * Подсчитывает расход сырья на складе
  * @param params - тип склада, вид вторсырья, ...
  * $net - считать нетто
  */
  function calculate_expenditure($params, $product_id = false, $net = false) {
    if($net){
      $this->db->select('SUM(expenditure_net) as sum');
    } else {
      $this->db->select('SUM(expenditure) as sum');
    }
    
    if($product_id){
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products','pr_products.id = store_movement_products.product_id');
      $this->db->where('(pr_products.id IN ('.implode(',', $product_id).') OR pr_products.parent_id IN ('.implode(',', $product_id).'))');
    }
    $this->db->where($params);
    $item = $this->db->get('store_movement_products')->row()->sum;
    // echo "<br>calculate_expenditure<br>";
    // echo $this->db->last_query();
    return $item;
  }
  
  /*
  * Подсчитывает расход в нетто на основании приходов
  * @param expenditure - расход, по которому считаем нетто
  */
  function calculate_expenditure_net($expenditure, $cron = false) {
    $expenditure_expenditure = $expenditure['expenditure'];
    $movement_products = $this->db->query('
      SELECT pr_movement.*
      FROM pr_store_movement_products as pr_movement
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
        // вычитаем сразу вес упаковки
        $movement_comings[$movement['id']] = array_merge($movement, array('rest_item' => $movement['coming'] - $movement['coming_weight_pack']));
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
                  if($cron){
                    file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest1 ' . $movement_coming['coming'] . ': ' . $movement_coming['rest_item'] . '-' . $movement_expenditure['rest_item'].''."\n".'',FILE_APPEND);                    
                  }
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
                  if($cron){
                    file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest2 ' . $movement_coming['coming'] . ': ' . $movement_expenditure['rest_item'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);
                  }
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
                if($cron){
                  file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest3 ' . $movement_coming['coming'] . ': ' . $movement_coming['rest_item'] . '-' . $movement['expenditure'].''."\n".'',FILE_APPEND);
                }
                // вычитаем из текущего прихода расход
                $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement['expenditure'];
                // расход учтен, обнуляем в основном движении
                $movement['expenditure'] = 0;
              } else {
                // расход превышает приход, сохраняем в массив остатков по расходам
                if($cron){
                  file_put_contents(FCPATH .'uploads/expendituresRest.txt','action1 rest4 ' . $movement_coming['coming'] . ': ' . $movement['expenditure'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);
                }
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
            if($cron){
              file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2_1 rest1 ' . $movement_coming['coming'] . ': ' . $movement_coming['rest_item'] . '-' . $movement_expenditure['rest_item'].''."\n".'',FILE_APPEND);
            }
            // вычитаем из текущего прихода остаток по расходу
            $movement_coming['rest_item'] = $movement_coming['rest_item'] - $movement_expenditure['rest_item'];
            
            // остаток по расходу учтен полностью
            // удаляем элемент из массива остатков по расходам
            unset($movement_expenditures[$movement_expenditure['id']]);
          } else {
            if($cron){
              file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2_1 rest2 ' . $movement_coming['coming'] . ': ' . $movement_expenditure['rest_item'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);
            }
            // если остаток по приходу меньше остатка по расходу, учитываем остатки прихода и пересчитываем остаток по расходу
            $movement_expenditure['rest_item'] = $movement_expenditure['rest_item'] - $movement_coming['rest_item'];
            // остаток по приходу учтен, обнуляем
            $movement_coming['rest_item'] = 0;
          }
        }
        unset($movement_expenditure);
      }

      // остатков по расходам остаться не должно, иначе общие остатки уйдут в минус
      if($movement_expenditures && $cron){
        file_put_contents(FCPATH .'uploads/expendituresRest.txt',"\n".'ERROR расход превышает приход expenditureID=' . $expenditure['id'] . ' расход без прихода:' . serialize($movement_expenditures) ."\n",FILE_APPEND);
        echo '<br>ERROR расход превышает приход expenditureID=' . $expenditure['id'] . ' расход без прихода:' . serialize($movement_expenditures).'\n';
        // var_dump($expenditure,$movement_expenditures);
      }

      // если остаток по приходу больше расхода
      if($movement_coming['rest_item'] >= $expenditure['expenditure']){
        if($cron){
          file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2 rest1 ' . $movement_coming['coming'] . '('.$movement_coming['coming_weight_defect'].'): ' . $movement_coming['rest_item'] . '-' . $expenditure['expenditure'].''."\n".'',FILE_APPEND);
        }

        // записываем % засора и к-во брутто
        $expenditure_weight_defect[] = array(
          'weight_defect' => $movement_coming['coming_weight_defect'],
          'gross' => $expenditure['expenditure']
        );

        // расход полностью учтен
        $expenditure['expenditure'] = 0;
      } else {
        // иначе берем часть прихода на данный расход и идем дальше по приходам
        if($cron){
          file_put_contents(FCPATH .'uploads/expendituresRest.txt','action2 rest2 ' . $movement_coming['coming'] . '('.$movement_coming['coming_weight_defect'].'): '  . $expenditure['expenditure'] . '-' . $movement_coming['rest_item'].''."\n".'',FILE_APPEND);
        }

        // записываем % засора и к-во брутто
        $expenditure_weight_defect[] = array(
          'weight_defect' => $movement_coming['coming_weight_defect'],
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
      // считаем без упаковки, т.к. упаковку учли ранее
      $expenditure_net += round($value['gross'] - $value['gross']*$value['weight_defect']/100);
    }
    if($cron){
      file_put_contents(FCPATH .'uploads/expendituresRest.txt','expenditure_net = ' . $expenditure_net,FILE_APPEND);
    }

    // остатков по расходу остаться не должно, иначе общие остатки уйдут в минус
    if($expenditure['expenditure'] > 0 && $cron){
      file_put_contents(FCPATH .'uploads/expendituresRest.txt',"\n".'ERROR расход превышает приход expenditure=' . serialize($expenditure) ."\n",FILE_APPEND);
      echo 'ERROR расход превышает приход expenditure=' . serialize($expenditure) ."\n";
      // var_dump($expenditure);
    }

    // корректировка нетто
    // считаем остаток нетто, если брутто 0 а нетто нет, то корректируем нетто
    $rest_net = $this->store_model->calculate_rest(
      'id != ' . $expenditure['id'] . ' AND ' .
      'client_id = ' . $expenditure['client_id'] . ' AND ' .
      'store_type_id = ' . $expenditure['store_type_id'] . ' AND ' .
      'product_id = ' . $expenditure['product_id'] . ' AND ' .
      'DATE_FORMAT(pr_store_movement_products.date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($expenditure['date'])) . '" AND ' .
      'pr_store_movement_products.order < ' . $expenditure['order'] . ' ', 
      false, 
      false, 
      true
    );
    if ($expenditure['rest'] == 0 && $rest_net != $expenditure_net) {
      // if($cron){
      //   echo 'rest ' . $expenditure['rest'] . ' rest_net ' . $rest_net . 
      //   ' product_id ' . $expenditure['product_id'] . 
      //   ' expenditure ' . $expenditure_expenditure . 
      //   ' expenditure_net ' . $expenditure_net ."\n";
      // }
      // из нетто вычитаем разницу в остатках
      $expenditure_net += $rest_net - $expenditure_net;
      // if($cron){
      //   echo '!!!!!!!!!! expenditure_net ' . $expenditure_net ."\n";
      // }
    }

    return array(
      "expenditure_net" => $expenditure_net,
      "expenditure_weight_defect" => $expenditure_weight_defect
    );
  }
  
  /*
  * Выводит последний подсчитанный остаток сырья на складе
  * @param params - тип склада, вид вторсырья, ...
  */
  function get_rest($params = array(), $product_id = false) {
    $this->db->select('date, rest, rest_product, rest_all');
    // date_new для того, чтобы учесть полностью весь день, без учета времени
    $this->db->select("DATE_FORMAT(date,'%Y-%m-%d') as date_new", false);
    if($product_id){
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products','pr_products.id = store_movement_products.product_id');
      $this->db->where('(pr_products.id IN ('.implode(',', $product_id).') OR pr_products.parent_id IN ('.implode(',', $product_id).'))');
    }
    if($params){
      $this->db->where($params);
    }
    $this->db->limit(1, 0);
    $this->db->order_by('date_new','DESC');
    $this->db->order_by('order','DESC');
    $item = $this->db->get('store_movement_products')->row_array();
    // echo "<br>get_rest<br>";
    // echo $this->db->last_query();
    return $item;
  } 

  /*
  * Формирует отет движения товара на складе
  * @param params - тип склада, клиент, вид вторсырья, ...
  */
  function get_rests($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = false) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    $this->db->select("store_movement_products.*");
    $this->db->select("DATE_FORMAT(date,'%Y-%m-%d') as date_new", false);
    $this->db->order_by("date_new",'asc');
    $this->db->order_by('order','asc');
    
    if($product_id){
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products','pr_products.id = store_movement_products.product_id');
      $this->db->where('(pr_products.id IN ('.implode(',', $product_id).') OR pr_products.parent_id IN ('.implode(',', $product_id).'))');
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('store_movement_products')->result_array();
    // echo $this->db->last_query();
    foreach ($items as $key => &$item) {
      $item['product'] = $this->products_model->get_product(array('id' => $item['product_id']));
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
      }
      if($item['store_workshop_id']){
        $item['workshop'] = $this->workshops_model->get_workshop(array('id'=>$item['store_workshop_id']));
      }
    }
    unset($item);
    return $items;
  }
  
  function get_rests_cnt($where = '', $product_id = false) {
    if($product_id){
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products','pr_products.id = store_movement_products.product_id');
      $this->db->where('(pr_products.id IN ('.implode(',', $product_id).') OR pr_products.parent_id IN ('.implode(',', $product_id).'))');
    }
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('store_movement_products');
  }

  /** 
  * Перезаписывает остатки Брутто на складе
  */
  function set_rests($where = array(), $cron = false){
    $this->db->select("store_movement_products.*");
    $this->db->select("DATE_FORMAT(date,'%Y-%m-%d') as date_new", false);
    $this->db->order_by("date_new",'asc');
    $this->db->order_by('order','asc');
    
    if ($where) {
      $this->db->where($where);
    }

    $items = $this->db->get('store_movement_products')->result_array();
    // echo $this->db->last_query();
    $cnt = count($items);
    foreach ($items as $key => $item) {
      if($cron) echo $key * 100 / $cnt . "%\r";

      if(!$this->update_movement_products($item['id'], array(
          // считаем остатки по клиенту и вторсырью
          'rest'  => $this->calculate_rest(array(
              'store_movement_products.store_type_id' => $item['store_type_id'],
              'store_movement_products.client_id'     => $item['client_id'],
              'store_movement_products.order <='      => $item['order']
            ),
            $item['product_id']),
          // общие остатки по сырью
          'rest_product'  => $this->calculate_rest(array(
              'store_movement_products.store_type_id' => $item['store_type_id'],
              'store_movement_products.order <='      => $item['order']
            ),
            $item['product_id']), 
          // общие остатки всего сырья на складе
          'rest_all' => $this->calculate_rest(array(
            'store_movement_products.store_type_id' => $item['store_type_id'],
            'store_movement_products.order <='      => $item['order']
          ))
        ))){
        return false;
      }
    }

    return true;
  }

  /** 
  * Перезаписывает остатки Нетто на складе
  */
  function set_rests_net($where = array(), $cron = false){
    $this->db->select("store_movement_products.*");
    $this->db->select("DATE_FORMAT(date,'%Y-%m-%d') as date_new", false);
    $this->db->order_by("date_new",'asc');
    $this->db->order_by('order','asc');
    
    if ($where) {
      $this->db->where($where);
    }

    $items = $this->db->get('store_movement_products')->result_array();
    // echo $this->db->last_query();
    $cnt = count($items);
    foreach ($items as $key => $item) {
      if($cron) echo $key * 100 / $cnt . "%\r";

      if(!$this->update_movement_products($item['id'], array(
          // считаем остатки по клиенту и вторсырью
          'rest_net'  => $this->calculate_rest(array(
              'store_movement_products.store_type_id' => $item['store_type_id'],
              'store_movement_products.client_id'     => $item['client_id'],
              'store_movement_products.order <='      => $item['order']
            ),
            $item['product_id'],
            false,
            true),
          // общие остатки по сырью
          'rest_product_net'  => $this->calculate_rest(array(
              'store_movement_products.store_type_id' => $item['store_type_id'],
              'store_movement_products.order <='      => $item['order']
            ),
            $item['product_id'],
            false,
            true), 
          // общие остатки всего сырья на складе
          'rest_all_net' => $this->calculate_rest(array(
              'store_movement_products.store_type_id' => $item['store_type_id'],
              'store_movement_products.order <='      => $item['order']
            ),
            false,
            false,
            true)
        ))){
        return false;
      }
      // echo $this->db->last_query();
    }

    return true;
  }

  /*
  * Перезаписывает order в движении сырья
  * $cron - запуск из cron
  */
  function set_order_movement($where = array(), $cron = false){
    $order_max = 1;
    if ($where) {
      // если пересчитываем не для всей таблицы, учитываем максимальную цифру в order
      $order_max = $this->get_movement_max_order();
    }
    $this->db->select("store_movement_products.*");
    $this->db->select("DATE_FORMAT(date,'%Y-%m-%d') as date_new", false);
    $this->db->order_by("date_new",'asc');
    // coming_child_id для того, чтобы сначала приход встал в движении, потом расход
    $this->db->order_by("coming_child_id",'desc');
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('store_movement_products')->result_array();
    // echo $this->db->last_query();
    $cnt = count($items);
    foreach ($items as $key => $item) {
      if($cron) echo $key * 100 / $cnt . "%\r";

      if(!$this->update_movement_products($item['id'],array('order'=>$key+$order_max))){
        return false;
      }
    }
    return true;
  }

  /*
  * Поиск строки по движению сырья на складе
  */
  function get_movement_max_order($where = array()) {
    $this->db->select('MAX(`order`) as max_order');
    return $this->db->get('store_movement_products')->row()->max_order;
  }

  /*
  * Поиск строки по движению сырья на складе
  */
  function get_movement_products($where = array()) {
    $this->db->order_by('order','desc');
    return $this->db->get_where('store_movement_products', $where)->row_array();
  }

  /*
  * Добавление строки по движению сырья на складе
  */
  function create_movement_products($params) {
    if ($this->db->insert('store_movement_products', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_movement_products($id, $params) {
    if ($this->db->update('store_movement_products', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_movement_products($params) {
    if ($this->db->delete('store_movement_products', $params)) {
      return true;
    }
    return false;
  }
  
  // Список клиентов с остатками на складе, с учетом указанного сырья
  function get_clients_movements($where = array(),$products = array()) {
    $this->db->select('pr_clients.id, pr_clients.title_full, SUM(pr_store_movement_products.coming-pr_store_movement_products.expenditure) AS `sum`');
    if($where){
      $this->db->where($where);
    }
    $this->db->join('pr_store_movement_products','pr_store_movement_products.client_id = pr_clients.id');
    if($products){
      $this->db->where_in('pr_store_movement_products.product_id',$products);
    }
    $this->db->group_by('pr_store_movement_products.client_id');
    $this->db->having('sum > 0');
    $this->db->order_by('pr_clients.title_full');
    $items = $this->db->get('pr_clients')->result_array();
    foreach ($items as $key => &$item) {
      $item['title_full'] = $item['title_full'].' ('.$item['sum'].')';
    }
    // echo $this->db->last_query();
    return $items;
  }

  /**
  * Список расходов
  */
  function get_expenditures($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = array()) {
    $this->db->select('store_expenditures.*');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_expenditures.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_expenditures t2','t2.parent_id = store_expenditures.id');
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field,$dest);
      }
    } else {
      $this->db->order_by('date','desc');
      $this->db->order_by('id','desc');
    }
    $this->db->group_by('store_expenditures.id');
    $items = $this->db->get('store_expenditures')->result_array();
    unset($where);
    foreach ($items as $key => &$item) {
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title_full'];
        }
      }
      if($item['store_workshop_id']){
        $item['workshop'] = $this->workshops_model->get_workshop(array('id'=>$item['store_workshop_id']));
      }
      //считаем общие параметры
      if(is_null($item['parent_id'])){
        // Делаем запрос на дочерние, для отображения видов сырья
        $this->db->select('store_expenditures.*,t2.title_full as product_title,store_movement_products.rest');
        // join-им чтобы вывести название товара и отчет по группе продукции
        $this->db->join('products t2','t2.id = store_expenditures.product_id');
        // join-им таблицу с остатками, чтобы показывать остатки в таблице расхода
        $this->db->join('store_movement_products','store_movement_products.expenditure_child_id = store_expenditures.id','left');

        $where = 'store_expenditures.parent_id = '.$item['id'];
        if ($product_id) {          
          $where .= ' AND (';
          foreach ($product_id as $key => $value) {
            if($key != 0){
              $where .= ' OR ';
            }
            $where .= 't2.id = '.$value.' OR t2.parent_id = '.$value;
          }
          $where .= ')';
        }
        $this->db->where($where);
        $this->db->order_by('store_expenditures.order','asc');
        $this->db->order_by('store_expenditures.id','asc');
        $item['childs'] = $this->db->get('store_expenditures')->result_array();
        // echo $this->db->last_query();
        // exit;
        $item['gross'] = $item['net'] = $item['price'] = $item['sum'] = 0;
        foreach ($item['childs'] as $key => &$child) {
          $item['gross'] += $child['gross'];
          $item['net'] += $child['net'];
        }
        unset($child);
      }
    }
    unset($item);
    
    return $items;
  }
  
  /**
  * Количество расходов по условию
  */
  function get_expenditures_cnt($where = '', $product_id = array()) {
    $this->db->select('COUNT(DISTINCT(pr_store_expenditures.id)) as cnt');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_expenditures.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_expenditures t2','t2.parent_id = store_expenditures.id');
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }
    return $this->db->get('store_expenditures')->row()->cnt;
  }

  /*
  * Возвращает сумму указанного параметра
  * @params: $field - поле, по которму считаем сумму
  *          $where - условие запроса
  *          $product_id - условие с учетом определенных видов вторсырья
  */
  function get_expenditure_sum_field($field = '',$where = array(), $product_id = array()){
    if(!$field) return false;
    // берем родительcкие элементы, т.к. дата прихода хранится только в них
    $this->db->where(array('store_expenditures.parent_id'=>null));
    if ($where) {
      $this->db->where($where);
    }
    // включаем в запрос дочерние строки, т.к. цифры по весу хранятся в них
    $this->db->join('store_expenditures t2','t2.parent_id = store_expenditures.id');
    $this->db->select('SUM(t2.'.$field.') as sum');

    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }

    $item = $this->db->get('store_expenditures')->row()->sum;

    return $item;
  }

  /**
  * Одна единица расхода
  */
  function get_expenditure($where = array()) {
    $this->db->select('store_expenditures.*');
    $item = $this->db->get_where('store_expenditures', $where)->row_array();
    if($item){
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title'];
          if($item['client']['city_id']){
            $item['city'] = $this->cities_model->get_city(array('id' => $item['client']['city_id']));
          }
        }
      }
      $item['childs'] = $this->get_expenditures(0,0,array('parent_id'=>$item['id']),array('id'=>'asc'));
      foreach ($item['childs'] as $key => &$child) {
        $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
        // остаток сырья на складе по клиенту
        if($child['active']){
          // если расход отправлен на склад, выводим остатки на момент добавления расхода на склад
          $rest = $this->get_rest(array('expenditure_child_id'  => $child['id']));
          $child['rest'] = ($rest ? $rest['rest'] : 0.00);
          $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
        } else {
          // если расход НЕ отправлен на склад, выводим остатки по последней строке из движения по сырью и клиенту
          $rest = $this->get_rest(array(
            'store_type_id' => $child['store_type_id'],
            'client_id'     => $child['client_id'],
            'product_id'    => $child['product_id'],
            'date <='       => $item['date']
          ));
          $child['rest'] = ($rest ? $rest['rest'] : 0.00);
          $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
        }
      }
      unset($child);
    }

    return $item;
  }

  function create_expenditure($params) {
    if ($this->db->insert('store_expenditures', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_expenditure($id, $params) {
    if ($this->db->update('store_expenditures', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_expenditure($cond) {
    if(!$cond || (!is_int($cond) && !is_array($cond))){
      return false;
    }
    if(is_int($cond)){
      $this->db->where(array('id' => $cond));
    } else {
      $this->db->where($cond);
    }
    if ($this->db->delete('store_expenditures')) {
      return true;
    }
    return false;
  }
}