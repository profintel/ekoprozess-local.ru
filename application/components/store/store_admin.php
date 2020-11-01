<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('clients/models/clients_model');
    $this->load->model('store/models/store_model');
    $this->load->model('workshops/models/workshops_model');
    $this->load->model('acceptances/models/acceptances_model');
    $this->load->model('gallery/models/gallery_model');
  }

  /**
  * Просмотр меню компонента
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'Склад',
      'items' => $this->render_menu()
    ));
  }
  
  /**
  * Просмотр меню по типу склада
  */
  function store_types($type_id) {
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    return $this->render_template('admin/menu', array(
      'title' => 'Склад: '.$type['title'],
      'items' => $this->render_menu(false, array($type_id)),
      'back' => $this->lang_prefix.'/admin'. $this->params['path']
    ));
  }
  
  /**
  * Просмотр подменю разделов склада
  */
  function render_menu($method = false, $arguments = false) {
    if(!$method && $arguments[0]){
      $items = array(
        array(
          'title'   => 'Приход',
          'methods' => array('comings','create_coming','edit_coming'),
          'link'    => $this->lang_prefix.'/admin'. $this->params['path'] .'comings/'.$arguments[0].'/'
        ),
        array(
          'title'   => 'Расход',
          'methods' => array('expenditures','create_expenditure','edit_expenditure'),
          'link'    => $this->lang_prefix.'/admin'. $this->params['path'] .'expenditures/'.$arguments[0].'/'
        ),
        array(
          'title'   => 'Остаток',
          'methods' => array('rests'),
          'link'    => $this->lang_prefix.'/admin'. $this->params['path'] .'rests/'.$arguments[0].'/'
        ),
      );
    } else {
      // определяем типы склада
      $types = $this->store_model->get_store_types(array('active'=>1));
      if(!$types){
        show_error('Не найдены типы склада');
      }
      $items = array();
      foreach ($types as $key => $type) {
        $submenu = $this->render_menu(false, array($type['id']));
        foreach ($submenu as $key => &$value) {
          if(in_array($method, $value['methods']) && $type['id'] == $arguments[0]){
            $value['active'] = true;
          }
        }
        unset($value);
        $items[] = array(
          'title'    => $type['title'],
          'link'     => $this->lang_prefix.'/admin'. $this->params['path'] .'store_types/'.$type['id'] . '/',
          'active'   => ($method == 'store_types' && $type['id'] == $arguments[0]),
          'submenu'  => $submenu
        );
      }
    }
    return $items;
  }

  /**
  * Поиск клиентов с остатками по указанным параметрам вторсырья
  */
  function renderSelectClientsRests() {
    $where=array(
      'store_movement_products.store_type_id' => $this->input->post('store_type_id'),
    );
    if($this->input->post('date')){
      $where['store_movement_products.date <='] = date('Y-m-d H:i:s',strtotime($this->input->post('date')));
    }
    $products = $this->input->post('product_id');
    $clients = $this->store_model->get_clients_movements($where,$products);
    //Формирует html select
    $result = array();
    $vars = array(
      'view'      => 'fields/select',
      'title'     => 'Клиент:',
      'name'      => 'client_id',
      'text_field'=> 'title_full',
      'options'   => $clients,
      'onchange'  => 'updateRestProduct(this)',
      'empty'     => true
    );
    $result['clients'] = $this->load->view('fields/select', array('vars' => $vars), true);
    echo json_encode($result);
  }

  /**
  * Просмотр таблицы приходов продукции
  */
  function comings($type_id, $render_table = false) {
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    $product_id = $this->uri->getParam('product_id');
    $get_params = array(
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : date('Y-m-1')),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : ''),
      'client_id'   => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'store_workshop_id'   => ((int)$this->uri->getParam('store_workshop_id') ? (int)$this->uri->getParam('store_workshop_id') : ''),
      'product_id'  => ($product_id && @$product_id[0] ? $product_id : array()),
    );

    $data = array(
      'title'       => 'Склад: '.$type['title'].'. Приход',
      'section'     => 'coming',
      'type_id'     => $type_id,
      'get_params'  => $get_params,
      //формируем ссылку на создание объекта
      'link_create' => array(
          'title' => 'Создать приход',
          'path'  => $this->lang_prefix.'/admin'.$this->component['path'].'create_coming/'.$type_id.'/',
        ),
      'form' => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'comings/'.$type_id.'/?ajax=1',
        'enctype' => '',
        'blocks' => array(
          array(
            'title'         => 'Параметры поиска',
            'fields'   => array(
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата прихода (от):',
                'name'        => 'date_start',
                'value'       => ($get_params['date_start']? date('d.m.Y',strtotime($get_params['date_start'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата прихода (до):',
                'name'        => 'date_end',
                'value'       => ($get_params['date_end']? date('d.m.Y',strtotime($get_params['date_end'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'       => 'fields/select',
                'title'      => 'Поставщик:',
                'name'       => 'client_id',
                'text_field' => 'title_full',
                'value'      => $get_params['client_id'],
                'options'    => $this->clients_model->get_clients(),
                'empty'      => true,
                'onchange'   => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'     => 'fields/select',
                'title'    => 'Вид вторсырья:',
                'name'     => 'product_id[]',
                'multiple' => true,
                'empty'    => true,
                'optgroup' => false,
                'options'  => $this->products_model->get_products(array('parent_id' => null)),
                'value'    => $get_params['product_id'],
                'onchange' => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'       => 'fields/' . ($type_id == 2 ? 'select' : 'hidden'),
                'title'      => 'Цех:',
                'name'       => 'store_workshop_id',
                'text_field' => 'title',
                'value'      => $get_params['store_workshop_id'],
                'options'    => $this->workshops_model->get_workshops(),
                'empty'      => true,
                'onchange'   => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'          => 'fields/submit',
                'title'         => 'Сформировать',
                'type'          => 'ajax',
                'id'            => 'btn-form',
                'reaction_func' => true,
                'reaction'      => 'handle_ajaxResultAllData',
                'data_type'     => 'json'
              )
            )
          )
        )
      )),
    );
    
    // если запрос на формирование данных, иначе возвращаем шаблон - обертку
    if($render_table || $this->uri->getParam('ajax') == 1){
      $where = array('store_comings.parent_id'=>null);
      //условие по типу склада
      $where['store_comings.store_type_id'] = $type['id'];
      $error = '';
      if($get_params['date_start']){
        $where['store_comings.date_second >='] = $get_params['date_start'];
      }
      if($get_params['date_end']){
        // к дате окончания добавляем 1 день, т.к. показания за этот день должны быть включены в отчет
        $date_end = date_format(date_modify(date_create($get_params['date_end']), '+1 day'), 'Y-m-d H:i:s');
        $where['store_comings.date_second <='] = $date_end;
      }
      if($get_params['client_id']){
        $where['store_comings.client_id'] = $get_params['client_id'];
      }
      if($get_params['store_workshop_id']){
        $where['store_comings.store_workshop_id'] = $get_params['store_workshop_id'];
      }

      $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
      $limit = 20;
      $offset = $limit * ($page - 1);
      $cnt = $this->store_model->get_comings_cnt($where, $get_params['product_id']);
      $pages = get_pages($page, $cnt, $limit);
      $postfix = '';
      foreach ($get_params as $key => $get_param) {
        if(is_array($get_param)){
          $postfix .= $key.'[]='.implode('&'.$key.'[]=', $get_param).'&';
        } else {
          $postfix .= $key.'='.$get_param.'&';
        }
      }
      $pagination_data = array(
        'ajax'    => true,
        'pages' => $pages,
        'page' => $page,
        'prefix' => '/admin'. $this->params['path'].'comings/'.$type_id.'/',
        'postfix' => $postfix
      );

      $data = array_merge($data, array(
        'pagination'  => $this->load->view('templates/pagination', $pagination_data, true),
        'error'     => $error,
        'items'     => $this->store_model->get_comings($limit, $offset, $where, false, $get_params['product_id']),
         //общая сумма брутто 
        'all_gross' => ($type['id'] == 1 ? $this->store_model->get_comming_sum_field('gross', $where, $get_params['product_id']) : 0),
        //общая сумма нетто 
        'all_net'   => $this->store_model->get_comming_sum_field('net', $where, $get_params['product_id']),

      ));
      
      if($render_table){
        return $this->load->view('../../application/components/store/templates/admin_comins_table',$data,true);
      } else if($this->uri->getParam('ajax') == 1){
        send_answer(array(
          'page'  => (isset($page) ? $page : 1),
          'pages' => (isset($pages) ? count($pages) : 0),
          'html'  => $this->load->view('../../application/components/store/templates/admin_comins_table',$data,true),
        ));
      }
    }

    return $this->render_template('templates/admin_items', array('data'=>$data));
  }

  /**
  * Добавление нескольких видов вторсырья
  */
  function renderProductFields($return_type = 'array', $items = array(), $section = '', $type_id = 1, $acceptance = array()) {
    $result = array();
    if ($items) {
      foreach ($items as $key => $item) {
        $result[] = $this->_renderProductFields(($key==0?true:false), $item, $section, $type_id, $acceptance);
      }
    } else {
      $result[] = $this->_renderProductFields(($return_type=='array'?true:false),false,$section,$type_id, $acceptance);
    }
    $result[] = array(
      'title'   => '',
      'collapse'=> false,
      'fields'   => array(
        array(
          'view'     => 'fields/hidden',
          'title'    => 'Добавить еще вторсырье',
          'type'     => 'ajax',
          'class'    => 'btn-default',
          'icon'     => 'glyphicon-plus',
          'onclick'  => 'renderFieldsProducts("/admin/store/renderProductFields/html/'.false.'/'.$section.'/'.$type_id.'/", this);',
          'reaction' => ''
        )
      )
    );
    // var_dump($result);
    //$return_type - тип данных в результате
    if($return_type == 'html' && !$items){
      $html = '<div class="form_block">
        <div class="panel-heading clearfix">
        </div>
        <div class="panel-collapse collapse in" role="tabpanel" aria-labelledby="">
          <div class="panel-body clearfix">
            '.$this->view->render_fields($result[0]['fields']).'
          </div>
        </div>
      </div>';
      return $html;
    }
    
    return $result;
  }

  /**
  * Формирует поля блока с вторсырьем для формы
  * $label - указывает нади ли формировать заголовок
  * $item - массив с данными по вторсырью
  */ 
  function _renderProductFields($label = true, $item = array(), $section = '', $type_id = 1, $acceptance) {
    $errors = array(
      'weight_defect' => false,
      'net'           => false,
    );

    // проверяем для прихода, если в акте другой % засора, подсвечиваем это поле
    if(isset($acceptance['childs'])){
      foreach ($acceptance['childs'] as $key => $acceptance_item) {
        if($item['id'] == $acceptance_item['store_coming_id'] && $item['weight_defect'] != $acceptance_item['weight_defect']){
          $errors['weight_defect'] = 'В акте приемки % засора отличается';
        }
        if($item['id'] == $acceptance_item['store_coming_id'] && $item['net'] != $acceptance_item['net']){
          $errors['net'] = 'В акте приемки нетто отличается';
        }
      }
    }

    $fields = array(
      array(
        'view'    => 'fields/hidden',
        'title'   => 'item_id:',
        'name'    => 'item_id[]',
        'value'   => ($item ? $item['id'] : 0)
      ),
      array(
        'view'     => 'fields/select',
        'title'    => ($label ? 'Вид вторсырья' : ''),
        'name'     => 'product_id[]',
        'empty'    => true,
        'optgroup' => true,
        'options'  => $this->products_model->get_products(array('parent_id' => null)),
        'value'    => ($item ? $item['product_id'] : ''),
        'disabled' => ($item && $item['active'] ? true : false),
        'onchange' => 'updateRestProduct(this);'.($section == 'expenditure' ? 'updateClientsRests(this);' : ''),
        'form_group_class' => 'form_group_product_field form_group_w20',
      ),
      array(
        'view'  => 'fields/'.($type_id == 1 && $section == 'coming' ? 'text' : 'hidden'),
        'type'  => 'number',
        'title' => ($label ? 'Вес в ТТН Поставщика,&nbsp;(кг)' : ''),
        'name'  => 'weight_ttn[]',
        'value' => ($item && $section == 'coming' ? $item['weight_ttn'] : ''),
        'class' => 'number',
        'form_group_class' => 'form_group_product_field',
      ),
      array(
        'view'     => 'fields/'.($type_id == 1 ? 'text' : 'hidden'),
        'type'     => 'number',
        'title'    => ($label ? 'Брутто, (кг)' : ''),
        'name'     => 'gross[]',
        'value'    => ($item ? $item['gross'] : ''),
        'disabled' => ($item && $item['active'] ? true : false),
        'class'    => 'number',
        'form_group_class' => 'form_group_product_field',
        'onchange' => 'updateComingNet(this);',
      ),
      array(
        'view'  => 'fields/'.($type_id == 1 && $section == 'coming' ? 'text' : 'hidden'),
        'type'  => 'number',
        'title' => ($label ? 'Упаковка, (кг)' : ''),
        'name'  => 'weight_pack[]',
        'value' => ($item && $section == 'coming' ? $item['weight_pack'] : ''),
        'disabled' => ($item && $item['active'] ? true : false),
        'class' => 'number',
        'form_group_class' => 'form_group_product_field',
        'onchange' => 'updateComingNet(this);',
      ),
      array(
        'view'  => 'fields/'.($type_id == 1 && $section == 'coming' ? 'text' : 'hidden'),
        'type'  => 'number',
        'title' => ($label ? 'Засор, (%)' : ''),
        'name'  => 'weight_defect[]',
        'value' => ($item && $section == 'coming' ? $item['weight_defect'] : ''),
        'disabled' => ($item && $item['active'] ? true : false),
        'class' => 'number',
        'form_group_class' => 'form_group_product_field '.($errors['weight_defect'] ? ' has-warning el-tooltip' : ''),
        'onchange' => 'updateComingNet(this);',
        'form_group_title' => $errors['weight_defect'],
      ),
      array(
        'view'     => 'fields/text',
        'type'     => 'number',
        'title'    => ($label ? 'Нетто, (кг)' : ''),
        'name'     => 'net[]',
        'value'    => ($item ? $item['net'] : ''),
        'disabled' => (($type_id == 2 && $item && $item['active']) || $type_id == 1 ? true : false),
        'class'    => 'number',
        'form_group_class' => 'form_group_product_field'.($type_id == 2 ? ' form_group_w20' : '').($errors['net'] ? ' has-warning el-tooltip' : ''),
        'form_group_title' => $errors['net'],
      ),
      array(
        'view'  => 'fields/text',
        'type'  => 'number',
        'title' => ($label ? 'Кол-во мест' : ''),
        'name'  => 'cnt_places[]',
        'value' => ($item ? $item['cnt_places'] : ''),
        'class' => 'number',
        'form_group_class' => 'form_group_product_field'.($type_id == 2 ? ' form_group_w20' : ''),
      ),
      array(
        'view'  => 'fields/'.($type_id == 1 ? 'readonly' : 'hidden'),
        'title' => ($label ? 'Остаток от клиента' : ''),
        'value' => '<span class="rest h4">'.($item ? $item['rest'] : '0.00').'</span>',
        'form_group_class' => 'form_group_product_field',
      ),
      array(
        'view'  => 'fields/readonly',
        'title' => ($label ? 'Остаток на складе' : ''),
        'value' => '<span class="rest_product h4">'.($item ? $item['rest_product'] : '0.00').'</span>',
        'form_group_class' => 'form_group_product_field'.($type_id == 2 ? ' form_group_w20' : ''),
      ),
      array(
        'view'    => 'fields/submit',
        'title'   => '',
        'class'   => 'btn-default './*($item && $item['active'] ? ' disabled ' : '').*/($label ? 'form_group_product_field_btn' : 'form_group_product_field_btn_m5'),
        'icon'    => 'glyphicon-remove',
        // 'onclick' =>  ($item && $item['active'] ? 'return false;' : 'removeFormBlock(this,"'.($item ? '/admin/store/delete_'.$section.'/'.$item['id'] : '').'");'),
        'onclick' =>  'removeFormBlock(this,"'.($item ? '/admin/store/delete_'.$section.'/'.$item['id'] : '').'");',
      ),
      array(
        'view'     => 'fields/submit',
        'title'    => '',
        'type'     => 'ajax',
        'class'    => 'btn-primary '.($item && $item['active'] ? ' disabled ' : '').($label ? 'form_group_product_field_btn' : 'form_group_product_field_btn_m5'),
        'icon'     => 'glyphicon-plus',
        'onclick'  =>  ($item && $item['active'] ? 'return false;' : 'renderFieldsProducts("/admin/store/renderProductFields/html/0/'.$section.'/'.$type_id.'/", this);'),
        'reaction' => ''
      )
    );
    if($item && $item['active']){
      $fields[] = array(
        'view'    => 'fields/hidden',
        'title'   => 'product_id:',
        'name'    => 'product_id[]',
        'value'   => ($item ? $item['product_id'] : ''),
      );
    }
    return array(
      'title'    => ($label ? 'Вторсырье' : ''),
      'collapse' => false,
      'class'    => 'clearfix '.($label ? 'form_block_label' : ''),
      'fields'   => $fields
    );
  }

  /**
  *  Создание прихода.
  */
  function create_coming($type_id){
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    // последняя строка движения, для datepicker
    $rest = $this->store_model->get_rest(array('store_type_id' => $type['id']));
    $blocks = array(array(
      'title'   => 'Основные параметры',
      'fields'   => array(
        array(
          'view'      => 'fields/'.($type_id == 1 ? 'select' : 'hidden'),
          'title'     => 'Клиент:',
          'name'      => 'client_id',
          'text_field'=> 'title_full',
          'options'   => $this->clients_model->get_clients(),
          'value'     => ($this->uri->getParam('client_id') ? mysql_prepare($this->uri->getParam('client_id')) : 0),
          'empty'     => true,
          'onchange'  => 'updateRestProduct(this)',
        ),
        array(
          'view'        => 'fields/'.($type_id == 1 ? 'text' : 'hidden'),
          'title'       => 'Поставщик:',
          'description' => 'Укажите в случае, если поставщика нет в базе клиентов',
          'name'        => 'company',
          'onkeyup'     => 'updateRestProduct(this)',
        ),
        array(
          'view'  => 'fields/'.($type_id == 1 ? 'text' : 'hidden'),
          'title' => 'ТТН и пункт загрузки:',
          'name'  => 'date_num',
        ),
        array(
          'view'  => 'fields/'.($type_id == 1 ? 'text' : 'hidden'),
          'title' => 'Транспорт:',
          'name'  => 'transport',
        ),
        array(
          'view'    => 'fields/'.($type_id == 1 ? 'datetime' : 'hidden'),
          'title'   => 'Дата прибытия машины:',
          'name'    => 'date_primary',
        ),
        array(
          'view'    => 'fields/datetime',
          'title'   => 'Дата прихода на склад:',
          'name'    => 'date_second',
        ),
        array(
          'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'select'),
          'title'   => 'Цех:',
          'name'    => 'store_workshop_id',
          'options' => $this->workshops_model->get_workshops(),
        ),
        array(
          'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'textarea'),
          'title'   => 'Примечания',
          'name'    => 'comment',
        ),
        array(
          'view'      => 'fields/'.($type_id == 1 ? 'file' : 'hidden'),
          'title'     => 'Фото (jpg, gif, png):',
          'multiple'  => true,
          'name'      => 'images[]'
        ),
        array(
          'view'  => 'fields/hidden',
          'title' => 'Тип склада:',
          'name'  => 'store_type_id',
          'value' => $type_id
        ),
        array(
          'view'  => 'fields/hidden',
          'title' => 'Тип формы (приход или расход):',
          'name'  => 'section',
          'value' => 'coming'
        ),
        array(
          'view'  => 'fields/hidden',
          'title' => 'Наличие объекта в остатках:',
          'name'  => 'active',
          'value' => false
        )
      )
    ));

    $productsFields = $this->renderProductFields('array', array(), 'coming', $type_id);
    foreach ($productsFields as $key => $productField) {
      $blocks[] = $productField;
    }

    $blocks[] = array(
      'title'   => '&nbsp;',
      'collapse'=> false,
      'fields'   => array(
        array(
          'view'     => 'fields/submit',
          'title'    => 'Создать',
          'type'     => 'ajax',
          'reaction' => $this->lang_prefix.'/admin'. $this->params['path'] . 'comings/'. $type['id'] .'/'
        )
      )
    );

    return $this->render_template('admin/inner', array(
      'title' => 'Склад: '.$type['title'].'. Добавление прихода',
      'html' => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix.'/admin'. $this->params['path'] .'_create_coming_process/'.$type['id'].'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix.'/admin'. $this->params['path'] . 'comings/'.$type['id'].'/'
    ), TRUE);
  }
  
  function _create_coming_process($type_id){
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      send_answer(array('errors' => array('Не найден тип склада')));
    }

    // Если поставщик не указан, создаем нового поставщика в базе с отметкой "Разовый поставщик"
    $client_id = ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : NULL);
    if($type_id == 1 && !$client_id && !$this->input->post('company')){
      send_answer(array('errors' => array('client_id'=>'Не указан поставщик')));
    }
    if($type_id == 1 && !$client_id){
      $client_id = $this->clients_model->create_client(array(
        'title'       => htmlspecialchars($this->input->post('company')),
        'title_full'  => htmlspecialchars($this->input->post('company')),
        'one_time'    => true
      ));
    }
    // var_dump($_FILES['images']['name'][0]);
    //загружаем файлы
    if (isset($_FILES['images']['name'][0]) && $_FILES['images']['name'][0]) {
      $upload = multiple_upload_file($_FILES['images'],false);
      if (!$upload) {
        send_answer(array('errors' => array('Ошибка при загрузке изображений')));
      }
      $images = array();
      foreach ($upload['files_path'] as $key => $image) {
        if ($this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
          if (!resize_image($image, 180, 135)) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
          } else {
            $images_thumbs = array(
              array(
                'thumb'   => $this->gallery_model->thumb($image,180,135),
                'width'   => 180,
                'height'  => 135
              )
            );
            $images[] = array(
              'image'         => $image,
              'images_thumbs' => $images_thumbs
            );
          }
        } else {
          @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        }
      }
    }

    $params = array(
      'store_type_id'     => $type_id,
      'date_primary'      => ($this->input->post('date_primary') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_primary'))) : NULL),
      'date_second'       => ($this->input->post('date_second') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_second'))) : NULL),
      'client_id'         => $client_id,
      'store_workshop_id' => ((int)$this->input->post('store_workshop_id') ? (int)$this->input->post('store_workshop_id') : NULL),
      'date_num'          => htmlspecialchars(trim($this->input->post('date_num'))),
      'transport'         => htmlspecialchars(trim($this->input->post('transport'))),
      'comment'           => htmlspecialchars(trim($this->input->post('comment'))),
      'active'            => false
    );

    $errors = $this->_validate_coming_params($type_id, $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->store_model->create_coming($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    // добавляем фото
    if (isset($images) && $images) {
      $gallery_params = array(
        array(
          'system_name' => 'gallery_system',
          'title' => 'Системная',      
          'childrens'   => array(
            array(
              'system_name' => $this->component['name'],
              'title'       => $this->component['title'],
              'childrens'   => array(
                array(
                  'system_name' => 'comings',
                  'title'       => 'Приходы',
                  'childrens'   => array(
                    array(
                      'system_name' => $id,
                      'title'       => $id,
                      'images'      => $images
                    )
                  )
                )
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        $this->delete_coming($id, true);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }

    //добавляем вторсырье
    $params_products = array(
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
      'weight_ttn'    => $this->input->post('weight_ttn'),
      'weight_pack'   => $this->input->post('weight_pack'),
      'weight_defect' => $this->input->post('weight_defect'),
      'cnt_places'    => $this->input->post('cnt_places'),
    );
    // перед добавлением проверяем указан ли вес вторсырья
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        if($params['store_type_id'] == 1){
          $coming = (float)str_replace(' ', '', $params_products['gross'][$key]);
        } else {
          $coming = (float)str_replace(' ', '', $params_products['net'][$key]);
        }
        if($coming <= 0){
          $this->delete_coming($id, true);
          send_answer(array('errors' => array('Укажите вес для вторсырья '.$params_products['product_id'][$key])));
        }
      }
    }
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        //по ключу собираем все параметры вторсырья
        $params = array(
          'parent_id'         => $id,
          'client_id'         => $params['client_id'],
          'store_type_id'     => $params['store_type_id'],
          'store_workshop_id' => $params['store_workshop_id'],
          'product_id'        => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'             => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'               => (float)str_replace(' ', '', $params_products['net'][$key]),
          'weight_ttn'        => (float)str_replace(' ', '', $params_products['weight_ttn'][$key]),
          'weight_pack'       => (float)str_replace(' ', '', $params_products['weight_pack'][$key]),
          'weight_defect'     => (float)str_replace(' ', '', $params_products['weight_defect'][$key]),
          'cnt_places'        => (float)str_replace(' ', '', $params_products['cnt_places'][$key]),
          'order'             => $key
        );
        // если первичная продукция нетто считаем, т.к. ведем автом-ий подсчет остатков
        if($params['store_type_id'] == 1){
          $params['net'] = round($params['gross'] - $params['weight_pack'] - $params['gross']*$params['weight_defect']/100);
        }
        if (!$this->store_model->create_coming($params)) {
          $this->delete_coming($id, true);
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья')));
        }
      }
    }

    // Создаем акт приемки по приходу первичного вторсырья
    if($params['store_type_id'] == 1){
      $this->load->component(array('name' => 'acceptances'));
      if (!$this->acceptances->_create_acceptance_process(TRUE, $id)) {
        $this->delete_coming($id, true);
        send_answer(array('errors' => array('Не удалось Создать акт приемки')));
      }
    }

    send_answer(array('redirect' => '/admin'.$this->params['path'].'edit_coming/'.$type_id.'/'.$id.'/'));
  }
  
  function _validate_coming_params($type_id, $params) {
    $errors = array();
    if(!$params['active']){
      if ($type_id == 1 && !$params['date_primary']) { $errors['date_primary'] = 'Не указана дата прибытия машины'; }
      if ($type_id == 1 && !$params['client_id']) { $errors['client_id'] = 'Не указан поставщик'; }
      if (!$params['date_second']) { $errors['date_second'] = 'Не указана дата прихода на склад'; }
      if($type_id == 1 && $params['date_second'] && $params['date_second'] < $params['date_primary']){
        $errors['date_second'] = 'Дата прихода не может быть меньше даты прибытия машины';
      }
    }

    return $errors;
  }

  /**
  *  Редактирование прихода.
  */
  function edit_coming($type_id, $id){
    $item = $this->store_model->get_coming(array('store_comings.id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }
    $type = $this->store_model->get_store_type(array('id'=>(int)$item['store_type_id']));
    if(!$type){
      show_error('Не найден тип склада');
    }

    // последняя строка движения, для datepicker
    $rest = $this->store_model->get_rest(array('store_type_id' => $type['id']));
    $blocks = array(array(
      'title'   => 'Основные параметры',
      'fields'   => array(
        array(
          'view'      => 'fields/'.($type['id'] == 1 ? 'select' : 'hidden'),
          'title'     => 'Клиент:',
          'name'      => 'client_id',
          'text_field'=> 'title_full',
          'options'   => $this->clients_model->get_clients(),
          'value'     => $item['client_id'],
          'disabled'  => ($item && $item['active'] ? true : false),
          'empty'     => true,
          'onchange'  => 'updateRestProduct(this)',
        ),
        array(
          'view'  => 'fields/'.($type['id'] == 1 ? 'text' : 'hidden'),
          'title' => 'ТТН и пункт загрузки:',
          'name'  => 'date_num',
          'value' => $item['date_num'],
        ),
        array(
          'view'  => 'fields/'.($type['id'] == 1 ? 'text' : 'hidden'),
          'title' => 'Транспорт:',
          'name'  => 'transport',
          'value' => $item['transport'],
        ),
        array(
          'view'    => 'fields/'.($type['id'] == 1 ? 'datetime' : 'hidden'),
          'title'   => 'Дата прибытия машины:',
          'name'    => 'date_primary',
          'value'   => ($item['date_primary'] ? date('d.m.Y H:i', strtotime($item['date_primary'])) : ''),
          'disabled'  => ($item && $item['active'] ? true : false),
        ),
        array(
          'view'    => 'fields/datetime',
          'title'   => 'Дата прихода на склад:',
          'name'    => 'date_second',
          'value'   => ($item['date_second'] ? date('d.m.Y H:i', strtotime($item['date_second'])) : ''),
          'disabled'  => ($item && $item['active'] ? true : false),
        ),
        array(
          'view'    => 'fields/'.($type['id'] == 1 ? 'hidden' : 'select'),
          'title'   => 'Цех:',
          'name'    => 'store_workshop_id',
          'options' => $this->workshops_model->get_workshops(),
          'disabled'=> ($item && $item['active'] ? true : false),
          'value'   => $item['store_workshop_id']
        ),
        array(
          'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'textarea'),
          'title'   => 'Примечания',
          'name'    => 'comment',
          'value'   => $item['comment']
        ),
        array(
          'view'      => 'fields/'.($type_id == 1 ? 'image' : 'hidden'),
          'title'     => 'Фото (jpg, gif, png):',
          'name'      => 'images[]',
          'multiple'  => true,
          'value'     => @$item['images'][0]['gallery_id']
        ),
        array(
          'view'  => 'fields/hidden',
          'title' => 'Тип склада:',
          'name'  => 'store_type_id',
          'value' => $type['id']
        ),
        array(
          'view'  => 'fields/hidden',
          'title' => 'Тип формы (приход или расход):',
          'name'  => 'section',
          'value' => 'coming'
        ),
        array(
          'view'  => 'fields/hidden',
          'title' => 'Наличие объекта в остатках:',
          'name'  => 'active',
          'value' => $item['active']
        )
      )
    ));

    $productsFields = $this->renderProductFields('array', $item['childs'], 'coming', $type['id'], $item['acceptance']);
    foreach ($productsFields as $key => $productField) {
      $blocks[] = $productField;
    }
    $blocks['submits'] = array(
      'title'    => '&nbsp;',
      'collapse' => false,
      'fields'   => array()
    );
    // Если active==1 редактирование прихода невозможно, т.к. оно отправлено в движение товара, для учета остатка
    if (!$item['active']){
      $blocks['submits']['fields'][] = array(
          'view'     => 'fields/submit',
          'title'    => 'Отправить на склад',
          'type'     => 'ajax',
          'class'    => 'btn-default',
          'onclick'  => 'sendMovement("",this);'
        );
    }
    $blocks['submits']['fields'][] =array(
        'view'     => 'fields/submit',
        'title'    => 'Сохранить',
        'type'     => 'ajax',
        'reaction' => 'reload'
      );
    // акт приемки по приходу
    if($item['acceptance']){
      $blocks['submits']['fields'][] = array(
        'view'    => 'fields/submit',
        'title'   => 'Акт приемки',
        'type'    => '',
        'icon'    => 'glyphicon-new-window',
        'class'   => 'btn-default pull-left m-l-0',
        'onclick' => 'window.open("/admin/acceptances/edit_acceptance/'.$item['acceptance']['id'].'/","_acceptance_'.$item['acceptance']['id'].'")'
      );
    }
    
    // доп. кнопки в шапке
    $block_title_btns = array();
    if($item['client_id']){      
      $block_title_btns = array_merge($block_title_btns, array(
        $this->load->view('fields/submit', 
          array('vars' => array(
            'title'   => 'Карточка клиента',
            'class'   => 'pull-left btn-primary m-r',
            'icon'    => 'glyphicon-list-alt',
            'href'    =>  '/admin/clients/edit_client/'.$item['client_id'].'/'
          )), true)
      ));
    }
    return $this->render_template('admin/inner', array(
      'title'           => 'Склад: '.$type['title'].'. Редактирование прихода',
      'block_title_btn' => $block_title_btns,
      'html'  => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix.'/admin'. $this->params['path'] .'_edit_coming_process/'.$id.'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix.'/admin'. $this->params['path'] . 'comings/'.$type['id'].'/'
    ), TRUE);
  }
  
  function _edit_coming_process($id,$sendMovement = false) {
    $item = $this->store_model->get_coming(array('store_comings.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }
    // проверяем акт приемки если первичная продукция
    // если статус Оплачено, редактировать нельзя
    if($item['store_type_id'] == 1){
      $acceptance = $this->acceptances_model->get_acceptance(array('client_acceptances.store_coming_id'=>$id));
      if($acceptance && $acceptance['status_id'] >= 10){
        send_answer(array('errors' => array('Невозможно изменить приход. Статус акта приемки - "Оплачено"')));
      }
    }

    $main_params = array(
      'active'    => $item['active'],
      'date_num'  => htmlspecialchars(trim($this->input->post('date_num'))),
      'transport' => htmlspecialchars(trim($this->input->post('transport'))),
      'comment'   => htmlspecialchars(trim($this->input->post('comment'))),
    );
    if($this->input->post('date_primary') && !$item['active']){
      $main_params['date_primary'] = date('Y-m-d H:i:s', strtotime($this->input->post('date_primary')));
    }
    if($this->input->post('date_second') && !$item['active']){
      $main_params['date_second'] = date('Y-m-d H:i:s', strtotime($this->input->post('date_second')));
    }
    if((int)$this->input->post('client_id') && !$item['active']){
      $main_params['client_id'] = (int)$this->input->post('client_id');
    }
    if((int)$this->input->post('store_workshop_id') && !$item['active']){
      $main_params['store_workshop_id'] = (int)$this->input->post('store_workshop_id');
    }
    
    //загружаем файлы
    if (isset($_FILES['images']['name'][0]) && $_FILES['images']['name'][0]) {
      $upload = multiple_upload_file($_FILES['images'],false);
      if (!$upload) {
        send_answer(array('errors' => array('Ошибка при загрузке изображений')));
      }
      $images = array();
      foreach ($upload['files_path'] as $key => $image) {
        if ($this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
          if (!resize_image($image, 180, 135)) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
          } else {
            $images_thumbs = array(
              array(
                'thumb'   => $this->gallery_model->thumb($image,180,135),
                'width'   => 180,
                'height'  => 135
              )
            );
            $images[] = array(
              'image'         => $image,
              'images_thumbs' => $images_thumbs
            );
          }
        } else {
          @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        }
      }
    }

    $errors = $this->_validate_coming_params($item['store_type_id'], $main_params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->store_model->update_coming($id, $main_params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    //редактируем/добавляем вторсырье
    $params_products = array(
      'item_id'       => $this->input->post('item_id'),
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
      'weight_ttn'    => $this->input->post('weight_ttn'),
      'weight_pack'   => $this->input->post('weight_pack'),
      'weight_defect' => $this->input->post('weight_defect'),
      'cnt_places'    => $this->input->post('cnt_places'),
    );
    // для готовой продукции вторсырье обязательно для заполнения всегда
    if($item['store_type_id'] == 2 && (!is_array($params_products['product_id']) || !@$params_products['product_id'][0])){
      send_answer(array('errors' => array('Не указаны параметры вторсырья')));
    }
    if(is_array($params_products['product_id']) && !$item['active']){
      // перед добавлением проверяем указан ли вес вторсырья
      foreach ($params_products['product_id'] as $key => $product_id) {
        if($product_id){
          if($item['store_type_id'] == 1){
            $coming = (float)str_replace(' ', '', $params_products['gross'][$key]);
          } else {
            $coming = (float)str_replace(' ', '', $params_products['net'][$key]);
          }
          if($coming <= 0){
            send_answer(array('errors' => array('Укажите вес для вторсырья '.$params_products['product_id'][$key])));
          }
        }
      }
    }
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        //по ключу собираем все параметры вторсырья
        $params = array(
          'parent_id'         => $id,
          'product_id'        => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'cnt_places'        => (float)str_replace(' ', '', $params_products['cnt_places'][$key]),
          'order'             => $key
        );
        if(isset($main_params['client_id'])){
          $params['client_id'] = $main_params['client_id'];
        }
        if(isset($main_params['store_workshop_id'])){
          $params['store_workshop_id'] = $main_params['store_workshop_id'];
        }
        if(isset($params_products['gross'][$key]) && $params_products['gross'][$key]){
          $params['gross'] = (float)str_replace(' ', '', $params_products['gross'][$key]);
        }
        if(isset($params_products['weight_ttn'][$key])){
          $params['weight_ttn'] = (float)str_replace(' ', '', $params_products['weight_ttn'][$key]);
        }
        if(isset($params_products['weight_pack'][$key])){
          $params['weight_pack'] = (float)str_replace(' ', '', $params_products['weight_pack'][$key]);
        }
        if(isset($params_products['weight_defect'][$key])){
          $params['weight_defect'] = (float)str_replace(' ', '', $params_products['weight_defect'][$key]);
        }
        if($item['store_type_id'] == 2 && isset($params_products['net'][$key]) && $params_products['net'][$key]){
          $params['net'] = (float)str_replace(' ', '', $params_products['net'][$key]);
        }        
        // если первичная продукция нетто считаем, т.к. ведем автом-ий подсчет остатков
        if($item['store_type_id'] == 1 && isset($params['gross']) && isset($params['weight_pack'])){
          $params['net'] = round($params['gross'] - $params['weight_pack'] - $params['gross']*$params['weight_defect']/100);
        }
        // если id указано, обновляем данные
        if((int)$params_products['item_id'][$key]){
          if (!$this->store_model->update_coming((int)$params_products['item_id'][$key],$params)) {
            send_answer(array('errors' => array('Ошибка при редактировании вторсырья')));
          }
        } else {
          if (!$this->store_model->create_coming($params)) {
            send_answer(array('errors' => array('Ошибка при добавлении вторсырья')));
          }
        }
      }
    }

    // Редактируем акт приемки по приходу, если первичная продукция
    if($item['store_type_id'] == 1){
      $this->load->component(array('name' => 'acceptances'));
      $acceptance = $this->acceptances_model->get_acceptance(array('client_acceptances.store_coming_id'=>$id));
      if($acceptance){
        if (!$this->acceptances->_edit_acceptance_process($acceptance['id'], true)){
          send_answer(array('errors' => array('Не удалось Изменить акт приемки')));
        }
      } else {
        if (!$this->acceptances->_create_acceptance_process(TRUE, $id)) {
          send_answer(array('errors' => array('Не удалось Создать акт приемки')));
        }
      }
    }

    // Отравляем сырье на склад
    if($sendMovement){
      //проверяем на доступ к методу
      if(!$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'send_coming_movement')){
        send_answer(array('errors' => array('У вас нет прав на отправление прихода на склад')));
      }
      $this->send_coming_movement($id);
    }

    // добавляем фото
    if (isset($images) && $images) {
      $gallery_params = array(
        array(
          'system_name' => 'gallery_system',
          'title' => 'Системная',      
          'childrens'   => array(
            array(
              'system_name' => $this->component['name'],
              'title'       => $this->component['title'],
              'childrens'   => array(
                array(
                  'system_name' => 'comings',
                  'title'       => 'Приходы',
                  'childrens'   => array(
                    array(
                      'system_name' => $id,
                      'title'       => $id,
                      'images'      => $images
                    )
                  )
                )
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        $this->delete_coming($id, true);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }

  /**
   * ПРоверка возможности удаления прихода
   */
  function _check_delete_coming($id) {
    $item = $this->store_model->get_coming(array('store_comings.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }
    foreach ($item['childs'] as $key => $child) {
      // если в движении расход по сырью был, и остаток меньше прихода, то удалить приход нельзя
      $movement = $this->store_model->get_rest(array(
        'store_type_id' => $item['store_type_id'],
        'coming_id'     => null,
        'date >='       => date('Y-m-d H:i:s',strtotime($item['date_second'])),
        'product_id'    => $child['product_id']
      ));
      if($item['store_type_id'] == 1 && $movement && $movement['rest'] < $child['gross']){
        return false;
      }
      if($item['store_type_id'] == 2 && $movement && $movement['rest'] < $child['net']){
        return false;
      }
    }

    return true;
  }

  /**
   * Удаление прихода
   */
  function delete_coming($id, $return = false) {
    $item = $this->store_model->get_coming(array('store_comings.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }

    // перед удалением ищем приход в движении
    if($item['parent_id']){
      $movement = $this->store_model->get_movement_products(array('coming_child_id'=>$item['id']));
      if($movement && !$this->_validate_coming_movement($item)){
        send_answer(array('errors' => array('Невозможно удалить приход, т.к. существует расход по вторсырью "'.$item['product']['title_full'].'"')));
      }
    } else {
      $movement = $this->store_model->get_movement_products(array('coming_id'=>$item['id']));
      // проверяем расход по каждому вторсырью прихода
      if($movement){
        foreach ($item['childs'] as $key => $child) {
          $movement_child = $this->store_model->get_movement_products(array('coming_child_id'=>$child['id']));
          if($movement_child && !$this->_validate_coming_movement($child)){
            send_answer(array('errors' => array('Невозможно удалить приход, т.к. существует расход по вторсырью "'.$child['product']['title_full'].'"')));
          }
        }
      }
    }

    if (!$this->store_model->delete_coming((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }

    // пересчитываем остатки, если в движении была найдена строка
    if ($movement){
      if (!$this->store_model->set_rests(array('order >=' => $movement['order']))) {
        send_answer(array('errors' => array('Не удалось обновить движение товара')));
      }
    }

    if($return){
      return true;
    }

    send_answer();
  }

  /*
  * Проверка на наличие расхода по приходу вторсырья
  */
  function _validate_coming_movement($item){
    // вычисляет остаток по сырью 
    $rest = $this->store_model->get_rest(array(
      'store_type_id' => $item['store_type_id'],
      'product_id'    => $item['product_id'],
      'client_id'     => $item['client_id'],
    ));

    // send_answer(array('errors' => array(var_dump($item),var_dump($rest))));

    // если остаток меньше прихода, значит расход по приходу был
    if($item['store_type_id'] == 1 && $rest['rest'] < $item['gross']){
      return false;
    }
    if($item['store_type_id'] == 2 && $rest['rest'] < $item['net']){
      return false;
    }

    return true;
  }

  /**
   * Отправление прихода на склад
  **/
  function send_coming_movement($id) {
    $item = $this->store_model->get_coming(array('store_comings.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }
    
    if(!$item['date_second']){
      send_answer(array('errors' => array('date_second'=>'Не указана дата прихода')));
    }
    if(!$item['childs']){
      send_answer(array('errors' => array('Приход должен содержать вторсырье')));
    }

    // Отправляем приход по каждому вторсырью
    // в таблицу движения продукции
    foreach ($item['childs'] as $key => $child) {
      $params = array(
        'store_type_id'         => $item['store_type_id'],
        'store_workshop_id'     => $item['store_workshop_id'],
        'coming_id'             => $item['id'],
        'coming_child_id'       => $child['id'],
        'client_id'             => $item['client_id'],
        'product_id'            => $child['product_id'],
        'date'                  => $item['date_second'],
        // если первичая продукция берем брутто, иначе нетто
        'coming'                => ($item['store_type_id'] == 1 ? $child['gross'] : $child['net']),
        'coming_weight_defect'  => $child['weight_defect'],
        'coming_weight_pack'    => $child['weight_pack'],
        'coming_net'            => ($item['store_type_id'] == 1 ? $child['net'] : 0),
      );

      // записываем приход
      $id = $this->store_model->create_movement_products($params);
      if(!$id){
        $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка добавления вторсырья на склад')));
      }

    }

    // пересчитываем order для строк с более поздней датой
    if(!$this->store_model->set_order_movement(array('store_type_id' => $item['store_type_id'], 'date >= ' => $item['date_second']))){
      $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
      send_answer(array('errors' => array('Ошибка перезаписи order в движении сырья')));
    }

    // пересчитываем остатки для строк с более поздней датой
    if(!$this->store_model->set_rests(array('store_type_id' => $item['store_type_id'], 'date >= ' => $item['date_second']))){
      $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
      send_answer(array('errors' => array('Ошибка перезаписи остатков в движении сырья')));
    }

    // пересчитываем остатки нетто для $item['store_type_id'] == 1 для строк с более поздней датой
    if($item['store_type_id'] == 1){
      if(!$this->store_model->set_rests_net(array('store_type_id' => $item['store_type_id'], 'date >= ' => $item['date_second']))){
        $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка перезаписи остатков в движении сырья')));
      }
    }

    // приходу и всем товарам прихода ставим статус "Отправлено на склад"
    if (!$this->store_model->update_coming($item['id'], array('active' => 1))) {
      $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }
    foreach ($item['childs'] as $key => $child) {
      if (!$this->store_model->update_coming($child['id'], array('active' => 1))) {
        $this->store_model->update_coming($item['id'], array('active' => 0));
        $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка при сохранении изменений')));
      }
    }

    send_answer();
  }

  /**
  * Просмотр остатков на складе в карточке прихода / расхода
  */
  function get_rest_product(){
    $rest = array('rest'=>0.00,'rest_product'=>0.00);
    $params = array(
      'store_type_id'     => (int)$this->input->post('store_type_id'),
      'client_id'         => ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : null),
      'product_id'        => (int)$this->input->post('product_id')
    );
    if($this->input->post('date')){
      $params['date <='] = date('Y-m-d H:i:s',strtotime($this->input->post('date')));
    }
    // остатки по клиенту для склада первичной продукции
    if($params['store_type_id'] == 1 && $params['client_id'] && $params['product_id']){
      $result = $this->store_model->get_rest($params);
      if($result){
        $rest['rest'] = $result['rest'];
      }
    }
    // общий остаток на складе
    if($params['store_type_id'] && $params['product_id']){
      $params = array(
        'store_type_id' => (int)$this->input->post('store_type_id'),
        'product_id'    => (int)$this->input->post('product_id')
      );
      if($this->input->post('date')){
        $params['date <='] = date('Y-m-d H:i:s',strtotime($this->input->post('date')));
      }
      $result = $this->store_model->get_rest($params);
      if($result){
        $rest['rest_product'] = $result['rest_product'];
      }
    }
    echo json_encode($rest);
  }

  /**
  * Просмотр таблицы расходов продукции
  */
  function expenditures($type_id, $render_table = false) {
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    $product_id = $this->uri->getParam('product_id');
    $get_params = array(
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : date('Y-m-1')),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : ''),
      'client_id'   => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'product_id'  => ($product_id && @$product_id[0] ? $product_id : array()),
      'customer'    => htmlspecialchars(trim($this->uri->getParam('customer'))),
    );

    $data = array(
      'title'      => 'Склад: '.$type['title'].'. Расход',
      'section'    => 'expenditure',
      'type_id'    => $type_id,
      'get_params' => $get_params,
      //формируем ссылку на создание объекта
      'link_create' => array(
          'title' => 'Создать расход',
          'path' => $this->lang_prefix.'/admin'.$this->component['path'].'create_expenditure/'.$type_id.'/',
        ),
      'form' => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'expenditures/'.$type_id.'/?ajax=1',
        'enctype' => '',
        'blocks' => array(
          array(
            'title'         => 'Параметры поиска',
            'fields'   => array(
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата расхода (от):',
                'name'        => 'date_start',
                'value'       => ($get_params['date_start']? date('d.m.Y',strtotime($get_params['date_start'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата расхода (до):',
                'name'        => 'date_end',
                'value'       => ($get_params['date_end']? date('d.m.Y',strtotime($get_params['date_end'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'       => 'fields/'.($type_id == 1 ? 'select' : 'hidden'),
                'title'      => 'Поставщик:',
                'name'       => 'client_id',
                'text_field' => 'title_full',
                'value'      => $get_params['client_id'],
                'options'    => $this->clients_model->get_clients(),
                'empty'      => true,
                'onchange' => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'   => 'fields/'.($type_id == 1 ? 'hidden' : 'text'),
                'title'  => 'Покупатель:',
                'name'   => 'customer',
                'value'  => $get_params['customer'],
                'onchange' => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'     => 'fields/select',
                'title'    => 'Вид вторсырья:',
                'name'     => 'product_id[]',
                'multiple' => true,
                'empty'    => true,
                'optgroup' => false,
                'options'  => $this->products_model->get_products(array('parent_id' => null)),
                'value'    => $get_params['product_id'],
                'onchange' => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'          => 'fields/submit',
                'title'         => 'Сформировать',
                'type'          => 'ajax',
                'id'            => 'btn-form',
                'reaction_func' => true,
                'reaction'      => 'handle_ajaxResultAllData',
                'data_type'     => 'json'
              )
            )
          )
        )
      )),
    );

    // если запрос на формирование данных, иначе возвращаем шаблон - обертку
    if($render_table || $this->uri->getParam('ajax') == 1){
      $where = array('store_expenditures.parent_id'=>null);
      //условие по типу склада
      $where['store_expenditures.store_type_id'] = $type['id'];
      $error = '';
      if($get_params['date_start']){
        $where['store_expenditures.date >='] = $get_params['date_start'];
      }
      if($get_params['date_end']){
        // к дате окончания добавляем 1 день, т.к. показания за этот день должны быть включены в отчет
        $date_end = date_format(date_modify(date_create($get_params['date_end']), '+1 day'), 'Y-m-d H:i:s');
        $where['store_expenditures.date <='] = $date_end;
      }
      if($get_params['client_id']){
        $where['store_expenditures.client_id'] = $get_params['client_id'];
      }
      if($get_params['customer']){
        $where["store_expenditures.customer LIKE '%".$get_params['customer']."%'"] = null;
      }

      $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
      $limit = 20;
      $offset = $limit * ($page - 1);
      $cnt = $this->store_model->get_expenditures_cnt($where, $get_params['product_id']);
      $pages = get_pages($page, $cnt, $limit);
      $postfix = '';
      foreach ($get_params as $key => $get_param) {
        if(is_array($get_param)){
          $postfix .= $key.'[]='.implode('&'.$key.'[]=', $get_param).'&';
        } else {
          $postfix .= $key.'='.$get_param.'&';
        }
      }
      $pagination_data = array(
        'ajax'    => true,
        'pages'   => $pages,
        'page'    => $page,
        'prefix'  => '/admin'. $this->params['path'].'expenditures/'.$type_id.'/',
        'postfix' => $postfix
      );

      $data = array_merge($data, array(
        'pagination' => $this->load->view('templates/pagination', $pagination_data, true),
        'error' => $error,
        'items' => $this->store_model->get_expenditures($limit, $offset, $where, false, $get_params['product_id']),
         //общая сумма брутто 
        'all_gross' => ($type['id'] == 1 ? $this->store_model->get_expenditure_sum_field('gross', $where, $get_params['product_id']) : 0),
        //общая сумма нетто 
        'all_net'   => ($type['id'] == 2 ? $this->store_model->get_expenditure_sum_field('net', $where, $get_params['product_id']) : 0),
      ));

      if($render_table){
        return $this->load->view('../../application/components/store/templates/admin_expenditures_table',$data,true);
      } elseif($this->uri->getParam('ajax') == 1){
        send_answer(array(
          'page'  => (isset($page) ? $page : 1),
          'pages' => (isset($pages) ? count($pages) : 0),
          'html'  => $this->load->view('../../application/components/store/templates/admin_expenditures_table',$data,true),
        ));
      }
    }

    return $this->render_template('templates/admin_items', array('data'=>$data));
  }

  /**
  *  Создание расхода.
  */
  function create_expenditure($type_id){
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }

    $blocks = array(
      'main_params' => array(
        'title'   => 'Основные параметры',
        'fields'   => array(
          array(
            'view'      => 'fields/'.($type_id == 1 ? 'select' : 'hidden'),
            'title'     => 'Клиент:',
            'name'      => 'client_id',
            'text_field'=> 'title_full',
            // список клиентов с остатками
            'options'   => $this->store_model->get_clients_movements(array('store_movement_products.store_type_id' => $type_id)),
            'value'     => ($this->uri->getParam('client_id') ? mysql_prepare($this->uri->getParam('client_id')) : 0),
            'empty'     => true,
            'onchange'  => 'updateRestProduct(this)',
          ),
          array(
            'view'  => 'fields/hidden',
            'title' => 'Тип склада:',
            'name'  => 'store_type_id',
            'value' => $type_id
          ),
          array(
            'view'  => 'fields/hidden',
            'title' => 'Тип формы (приход или расход):',
            'name'  => 'section',
            'value' => 'expenditure'
          ),
          array(
            'view'  => 'fields/hidden',
            'title' => 'Наличие объекта в остатках:',
            'name'  => 'active',
            'value' => false
          )
        )
      )
    );

    // последняя строка движения, для datepicker
    $rest = $this->store_model->get_rest(array('store_type_id' => $type['id']));
    array_push($blocks['main_params']['fields'], 
      array(
        'view'      => 'fields/datetime',
        'title'     => 'Дата расхода:',
        'name'      => 'date',
        'onchange'  => 'updateClientsRests(this);updateRestProduct(this);',
      ),
      array(
        'view'    => 'fields/'.($type_id == 1 ? 'select' : 'hidden'),
        'title'   => 'Цех:',
        'name'    => 'store_workshop_id',
        'options' => $this->workshops_model->get_workshops(),
      ),
      array(
        'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'text'),
        'title' => 'Покупатель:',
        'name'  => 'customer'
      ),
      array(
        'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'textarea'),
        'title'   => 'Примечания',
        'name'    => 'comment',
      )
    );

    // Блоки с вторсырьем
    $productsFields = $this->renderProductFields('array', array(), 'expenditure', $type_id);
    foreach ($productsFields as $key => $productField) {
      $blocks[] = $productField;
    } 

    $blocks[] = array(
      'title'   => '&nbsp;',
      'collapse'=> false,
      'fields'  => array(
        array(
          'view'     => 'fields/submit',
          'title'    => 'Создать',
          'type'     => 'ajax',
          'reaction' => $this->lang_prefix.'/admin'. $this->params['path'] . 'expenditures/'. $type['id'] .'/'
        )
      )
    );

    return $this->render_template('admin/inner', array(
      'title' => 'Склад: '.$type['title'].'. Добавление расхода',
      'html' => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix.'/admin'. $this->params['path'] .'_create_expenditure_process/'.$type['id'].'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix.'/admin'. $this->params['path'] . 'expenditures/'.$type['id'].'/'
    ), TRUE);
  }
  
  function _create_expenditure_process($type_id){
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      send_answer(array('errors' => array('Не найден тип склада')));
    }

    $params = array(
      'store_type_id'     => $type_id,
      'client_id'         => ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : NULL),
      'store_workshop_id' => ((int)$this->input->post('store_workshop_id') ? (int)$this->input->post('store_workshop_id') : NULL),
      'date'              => ($this->input->post('date') ? date('Y-m-d H:i:s', strtotime($this->input->post('date'))) : NULL),
      'customer'          => htmlspecialchars(trim($this->input->post('customer'))),
      'comment'           => htmlspecialchars(trim($this->input->post('comment'))),
    );

    $errors = $this->_validate_expenditure_params($type_id, $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->store_model->create_expenditure($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    //добавляем вторсырье
    $params_products = array(
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
      'cnt_places'    => $this->input->post('cnt_places'),
    );
    if(!is_array($params_products['product_id']) || !@$params_products['product_id'][0]){
      $this->delete_expenditure($id, true);
      send_answer(array('errors' => array('Не указаны параметры вторсырья')));
    }
    
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        // перед добавлением проверяем указаны ли все необходимые параметры
        if($params['store_type_id'] == 1){
          $expenditure = (float)str_replace(' ', '', $params_products['gross'][$key]);
        } else {
          $expenditure = (float)str_replace(' ', '', $params_products['net'][$key]);
        }
        if($expenditure <= 0){
          $this->delete_expenditure($id, true);
          send_answer(array('errors' => array('Укажите вес для вторсырья')));
        }

        // проверяем чтобы остаток не был < 0
        $rest = $this->store_model->get_rest(array('store_type_id'=>$params['store_type_id'],'client_id'=>$params['client_id'],'product_id'=>$product_id));
        if(!$rest || ($rest['rest'] - $expenditure) < 0){
          $this->delete_expenditure($id, true);
          send_answer(array('errors' => array('Остаток на складе не может быть меньше 0. Возможно расход по данному вторсырью уже заведен в базу более поздней датой. Проверьте остатки в движении вторсырья.')));
        }


        // по ключу собираем все параметры вторсырья
        $child_params = array(
          'parent_id'         => $id,
          'client_id'         => $params['client_id'],
          'store_type_id'     => $params['store_type_id'],
          'store_workshop_id' => $params['store_workshop_id'],
          'product_id'    => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'         => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'           => (float)str_replace(' ', '', $params_products['net'][$key]),
          'cnt_places'    => (float)str_replace(' ', '', $params_products['cnt_places'][$key])
        );
        $child_id = $this->store_model->create_expenditure($child_params);
        if (!$child_id) {
          $this->delete_expenditure($id, true);
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья')));
        }

        // если первичная продукция нетто считаем, т.к. ведем автом-ий подсчет остатков
        if ($child_params['store_type_id'] == 1) {
          // параметры для подсчета
          $movement_params = array(
            'id' => $child_id,
            'client_id'     => $child_params['client_id'],
            'store_type_id' => $child_params['store_type_id'],
            'product_id'    => $child_params['product_id'],
            'date'          => $params['date'],
            'expenditure'   => $child_params['gross'],
            'rest'          => $rest
          );
          $movement_params['order'] = $this->store_model->get_movement_max_order(array('date <= ' => $params['date']));
          
          $result = $this->store_model->calculate_expenditure_net($movement_params);
          if(!$result || !isset($result['expenditure_net']) || !isset($result['expenditure_weight_defect'])){
            $this->delete_expenditure($id, true);
            send_answer(array('errors' => array('Ошибка при подсчете нетто')));
          }
          if (!$this->store_model->update_expenditure($child_id, array(
              'net' => $result['expenditure_net'], 
              'weight_defect' => serialize($result['expenditure_weight_defect'])
            ))) {
            $this->delete_expenditure($id, true);
            send_answer(array('errors' => array('Ошибка при сохранении нетто')));
          }
        }
      }
    }

    send_answer(array('redirect' => '/admin'.$this->params['path'].'edit_expenditure/'.$type_id.'/'.$id.'/'));
  }
  
  function _validate_expenditure_params($type_id, $params) {
    $errors = array();
    if (!$params['date']) { $errors['date'] = 'Не указана дата'; }
    if ($type_id == 1 && !$params['client_id']) { $errors['client_id'] = 'Не указан поставщик'; }
    if ($type_id == 1 && !$params['store_workshop_id']) { $errors['store_workshop_id'] = 'Не указан цех'; }

    return $errors;
  }

  /**
   *  Редактирование расхода.
  **/
  function edit_expenditure($type_id, $id){
    $item = $this->store_model->get_expenditure(array('store_expenditures.id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }
    $type = $this->store_model->get_store_type(array('id'=>(int)$item['store_type_id']));
    if(!$type){
      show_error('Не найден тип склада');
    }

    // список клиентов с остатками
    // $where = array(
    //   'store_movement_products.store_type_id' => $item['store_type_id'],
    //   'store_movement_products.date <=' => $item['date']
    // );
    // $clients = $this->store_model->get_clients_movements($where,array_simple($item['childs'],'product_id'));
    
    $blocks = array(
      'main_params' => array(
        'title'   => 'Основные параметры',
        'fields'   => array(
          array(
            'view'      => 'fields/'.($type['id'] == 1 ? 'select' : 'hidden'),
            'title'     => 'Клиент:',
            'name'      => 'client_id',
            'text_field'=> 'title_full',
            'options'   => $this->clients_model->get_clients(),
            'value'     => $item['client_id'],
            'empty'     => true,
            'onchange'  => 'updateRestProduct(this)',
          ),
          array(
            'view'  => 'fields/hidden',
            'title' => 'Тип склада:',
            'name'  => 'store_type_id',
            'value' => $item['store_type_id']
          ),
          array(
            'view'  => 'fields/hidden',
            'title' => 'Тип формы (приход или расход):',
            'name'  => 'section',
            'value' => 'coming'
          ),
          array(
            'view'  => 'fields/hidden',
            'title' => 'Наличие объекта в остатках:',
            'name'  => 'active',
            'value' => $item['active']
          )
        )
      )
    );

    // последняя строка движения, для datepicker
    $rest = $this->store_model->get_rest(array('store_type_id' => $type['id']));
    array_push($blocks['main_params']['fields'], 
      array(
        'view'      => 'fields/datetime',
        'title'     => 'Дата расхода:',
        'name'      => 'date',
        'onchange'  => 'updateClientsRests(this);updateRestProduct(this);',
        'disabled'  => ($item && $item['active'] ? true : false),
        'value'     => ($item['date'] ? date('d.m.Y H:i', strtotime($item['date'])) : '')
      ),
      array(
        'view'     => 'fields/'.($type['id'] == 1 ? 'select' : 'hidden'),
        'title'    => 'Цех:',
        'name'     => 'store_workshop_id',
        'value'    => $item['store_workshop_id'],
        'options'  => $this->workshops_model->get_workshops(),
        'disabled' => ($item && $item['active'] ? true : false),
        'empty'    => true,
      ),
      array(
        'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'text'),
        'title'   => 'Покупатель:',
        'name'    => 'customer',
        'value'   => $item['customer']
      ),
      array(
        'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'textarea'),
        'title'   => 'Примечания',
        'name'    => 'comment',
        'value'    => $item['comment'],
      )
    );

    // Вторсырье
    $productsFields = $this->renderProductFields('array', $item['childs'], 'expenditure', $type['id']);
    foreach ($productsFields as $key => $productField) {
      $blocks[] = $productField;
    }   

    // Если active==1 редактирование невозможно, т.к. оно отправлено в движение товара, для учета остатка
    if (!$item['active']){
      $blocks[] = array(
        'title'   => '&nbsp;',
        'collapse'=> false,
        'fields'   => array(
          array(
            'view'     => 'fields/submit',
            'title'    => 'Сохранить',
            'type'     => 'ajax',
            'reaction' => 'reload'
          ),
          array(
            'view'     => 'fields/submit',
            'title'    => 'Отправить на склад',
            'type'     => 'ajax',
            'onclick'  => 'sendMovement("",this);'
          ),
        )
      );      
    }
    
    // доп. кнопки в шапке
    $block_title_btns = array();
    if($item['client_id']){      
      $block_title_btns = array_merge($block_title_btns, array(
        $this->load->view('fields/submit', 
          array('vars' => array(
            'title'   => 'Карточка клиента',
            'class'   => 'pull-left btn-primary m-r',
            'icon'    => 'glyphicon-list-alt',
            'href'    =>  '/admin/clients/edit_client/'.$item['client_id'].'/'
          )), true)
      ));
    }

    return $this->render_template('admin/inner', array(
      'title'           => 'Склад: '.$type['title'].'. Редактирование расхода',
      'block_title_btn' => $block_title_btns,
      'html' => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix.'/admin'. $this->params['path'] .'_edit_expenditure_process/'.$id.'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix.'/admin'. $this->params['path'] . 'expenditures/'.$type['id'].'/'
    ), TRUE);
  }
  
  function _edit_expenditure_process($id,$sendMovement = false) {
    $item = $this->store_model->get_expenditure(array('store_expenditures.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }

    $params = array(
      'date'              => ($this->input->post('date') ? date('Y-m-d H:i:s', strtotime($this->input->post('date'))) : NULL),
      'client_id'         => ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : NULL),
      'store_workshop_id' => ((int)$this->input->post('store_workshop_id') ? (int)$this->input->post('store_workshop_id') : NULL),
      'customer'          => htmlspecialchars(trim($this->input->post('customer'))),
      'comment'           => htmlspecialchars(trim($this->input->post('comment')))
    );

    $errors = $this->_validate_expenditure_params($item['store_type_id'], $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->store_model->update_expenditure($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    //редактируем/добавляем вторсырье
    $params_products = array(
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
      'cnt_places'    => $this->input->post('cnt_places'),
    );
    if(!is_array($params_products['product_id']) || !@$params_products['product_id'][0]){
      send_answer(array('errors' => array('Не указаны параметры вторсырья')));
    }

    //удаляем все параметры вторсырья по приходу и добавляем заново указанные
    if (!$this->store_model->delete_expenditure(array('parent_id'=>$item['id']))) {
      send_answer(array('errors' => array('Ошибка при удалении вторсырья')));
    }
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        // проверяем указаны ли все необходимые параметры
        if($item['store_type_id'] == 1){
          $expenditure = (float)str_replace(' ', '', $params_products['gross'][$key]);
        } else {
          $expenditure = (float)str_replace(' ', '', $params_products['net'][$key]);
        }
        if($expenditure <= 0){
          send_answer(array('errors' => array('Укажите вес для вторсырья')));
        }

        // проверяем чтобы остаток не был < 0
        $rest = $this->store_model->get_rest(array('store_type_id'=>$item['store_type_id'],'client_id'=>$params['client_id'],'product_id'=>$product_id));
        if(!$rest || ($rest['rest'] - $expenditure) < 0){
          send_answer(array('errors' => array('Остаток на складе не может быть меньше 0. Возможно расход по данному вторсырью уже заведен в базу более поздней датой. Проверьте остатки в движении вторсырья.')));
        }

        //по ключу собираем все параметры вторсырья
        $child_params = array(
          'parent_id'         => $id,
          'client_id'         => $params['client_id'],
          'store_type_id'     => $item['store_type_id'],
          'store_workshop_id' => $params['store_workshop_id'],
          'product_id'        => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'             => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'               => (float)str_replace(' ', '', $params_products['net'][$key]),
          'cnt_places'        => (float)str_replace(' ', '', $params_products['cnt_places'][$key])
        );

        $child_id = $this->store_model->create_expenditure($child_params);
        if (!$child_id) {
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья')));
        }

        // если первичная продукция нетто считаем, т.к. ведем автом-ий подсчет остатков
        if ($child_params['store_type_id'] == 1) {
          // параметры для подсчета
          $movement_params = array(
            'id' => $child_id,
            'client_id'     => $child_params['client_id'],
            'store_type_id' => $child_params['store_type_id'],
            'product_id'    => $child_params['product_id'],
            'date'          => $params['date'],
            'expenditure'   => $child_params['gross'],
            'rest'          => $rest
          );
          $movement_params['order'] = $this->store_model->get_movement_max_order(array('date <= ' => $params['date']));
          
          $result = $this->store_model->calculate_expenditure_net($movement_params);
          if(!$result || !isset($result['expenditure_net']) || !isset($result['expenditure_weight_defect'])){
            $this->delete_expenditure($id, true);
            send_answer(array('errors' => array('Ошибка при подсчете нетто')));
          }
          if (!$this->store_model->update_expenditure($child_id, array(
              'net' => $result['expenditure_net'], 
              'weight_defect' => serialize($result['expenditure_weight_defect'])
            ))) {
            $this->delete_expenditure($id, true);
            send_answer(array('errors' => array('Ошибка при сохранении нетто')));
          }
        }
      }
    }

    // Отравляем сырье на склад
    if($sendMovement){
      //проверяем на доступ к методу
      if(!$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'send_expenditure_movement')){
        send_answer(array('errors' => array('У вас нет прав на отправление расхода на склад')));
      }
      $this->send_expenditure_movement($id);
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }

  /**
  * Отправление расхода на склад
  */
  function send_expenditure_movement($id) {
    $item = $this->store_model->get_expenditure(array('store_expenditures.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }
    if(!$item['childs']){
      send_answer(array('errors' => array('Расход должен содержать вторсырье')));
    }

    // Отправляем расход по каждому вторсырью в таблицу движения продукции
    foreach ($item['childs'] as $key => $child) {
      $params = array(
        'store_type_id'         => $item['store_type_id'],
        'store_workshop_id'     => $item['store_workshop_id'],
        'expenditure_id'        => $item['id'],
        'expenditure_child_id'  => $child['id'],
        'client_id'             => $item['client_id'],
        'product_id'            => $child['product_id'],
        'date'                  => $item['date'],
        // если первичая продукция берем брутто, иначе нетто
        'expenditure'           => ($item['store_type_id'] == 1 ? $child['gross'] : $child['net']),
        'expenditure_weight_defect' => $child['weight_defect'],
        'expenditure_net'           => $child['net']
      );
      // ошибка, если текущий остаток < 0
      $rest = $this->store_model->get_rest(array(
        'store_type_id' => $item['store_type_id'],
        'client_id'     => $item['client_id'],
        'product_id'    => $child['product_id']
      ));
      if(!$rest || ($rest['rest'] - $params['expenditure']) < 0){
        $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
        send_answer(array('errors' => array('Остаток на складе не может быть меньше 0. Проверьте расход вторсырья "'.$child['product']['title_full'].'"')));
      }
      // записываем расход
      $id = $this->store_model->create_movement_products($params);
      if(!$id){
        $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка добавления вторсырья на склад')));
      }
    }

    // пересчитываем order для строк с более поздней датой
    if(!$this->store_model->set_order_movement(array('store_type_id' => $item['store_type_id'], 'date >= ' => $item['date']))){
      $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
      send_answer(array('errors' => array('Ошибка перезаписи order в движении сырья')));
    }

    // пересчитываем остатки для строк с более поздней датой
    if(!$this->store_model->set_rests(array('store_type_id' => $item['store_type_id'], 'date >= ' => $item['date']))){
      $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
      send_answer(array('errors' => array('Ошибка перезаписи остатков в движении сырья')));
    }

    // пересчитываем остатки нетто для $item['store_type_id'] == 1 для строк с более поздней датой
    if($item['store_type_id'] == 1){
      if(!$this->store_model->set_rests_net(array('store_type_id' => $item['store_type_id'], 'date >= ' => $item['date']))){
        $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка перезаписи остатков в движении сырья')));
      }
    }

    // расходу и всем товарам расхода ставим статус "Отправлено на склад"
    if (!$this->store_model->update_expenditure($item['id'], array('active' => 1))) {
      $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }
    foreach ($item['childs'] as $key => $child) {
      if (!$this->store_model->update_expenditure($child['id'], array('active' => 1))) {
        $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка при сохранении изменений')));
      }
    }
    send_answer(array('success' => array('Изменения успешно сохранены')));
  }

  /**
   * Удаление расхода
  **/
  function delete_expenditure($id, $return = false) {
    $item = $this->store_model->get_expenditure(array('store_expenditures.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }

    // если есть в движении, пересчитываем остатки
    if($item['parent_id']){
      $movement = $this->store_model->get_movement_products(array('expenditure_child_id'=>$item['id']));
    } else {
      $movement = $this->store_model->get_movement_products(array('expenditure_id'=>$item['id']));
    }

    if (!$this->store_model->delete_expenditure((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }

    if($movement){
      if (!$this->store_model->set_rests(array('order >=' => $movement['order']))) {
        send_answer(array('errors' => array('Не удалось обновить движение товара')));
      }
    }

    if($return){
      return true;
    }
    
    send_answer();
  }  

  /**
  * Просмотр отчета остатков на складе
  */
  function rests($type_id, $render_table = false){
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    $product_id = $this->uri->getParam('product_id');
    $get_params = array(
      'type'              => ($type['id'] == 1 && $this->uri->getParam('type') == 'net' ? 'net' : 'gross'),
      'date_start'        => ($this->uri->getParam('date_start') ? date('Y-m-d 00:00:00',strtotime($this->uri->getParam('date_start'))) : date('Y-m-1 00:00:00')),
      'date_end'          => ($this->uri->getParam('date_end') ? date('Y-m-d 00:00:00',strtotime($this->uri->getParam('date_end'))) : date('Y-m-d 00:00:00')),
      'client_id'         => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'store_workshop_id' => ((int)$this->uri->getParam('store_workshop_id') ? (int)$this->uri->getParam('store_workshop_id') : ''),
      'product_id'        => ($product_id && @$product_id[0] ? $product_id : array()),
      'movement'          => ($this->uri->getParam('movement') ? true : false),
      'zero'              => ($this->uri->getParam('zero') ? true : false)
    );

    $data = array(
      'title'           => 'Склад: '.$type['title'].'. Остаток',
      'section'         => 'rest',
      'type_id'         => $type_id,
      'type'            => $type,
      'client'          => ($get_params['client_id'] ? $this->clients_model->get_client(array('id'=>$get_params['client_id'])) : false),
      'products'        => ($get_params['product_id'] ? $this->products_model->get_products('id IN ('.implode(',', $get_params['product_id']).')') : false),
      'get_params'      => $get_params,
      'form' => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'rests/'.$type_id.'/?ajax=1',        
        'enctype' => '',
        'blocks' => array(
          array(
            'title'  => 'Расширенный поиск',
            'fields' => array(
              array(
                'view'    => 'fields/'.($type['id'] == 1 ? 'select' : 'hidden'),
                'title'   => 'Тип:',
                'name'    => 'type',
                'value'   => $get_params['type'],
                'options' => array(
                  array(
                    'id'    => 'gross',
                    'title' => 'Брутто'
                  ),
                  array(
                    'id'    => 'net',
                    'title' => 'Нетто'
                  )
                ),
                'empty'   => true,
                'onchange'=> "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата от:',
                'name'        => 'date_start',
                'value'       => ($get_params['date_start']? date('d.m.Y',strtotime($get_params['date_start'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата по:',
                'name'        => 'date_end',
                'value'       => ($get_params['date_end']? date('d.m.Y',strtotime($get_params['date_end'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'       => 'fields/'.($type['id'] == 1 ? 'select' : 'hidden'),
                'title'      => 'Поставщик:',
                'name'       => 'client_id',
                'text_field' => 'title_full',
                'value'      => $get_params['client_id'],
                'options'    => $this->clients_model->get_clients(),
                'empty'      => true,
                'onchange'   => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Цех:',
                'name'    => 'store_workshop_id',
                'value'   => $get_params['store_workshop_id'],
                'options' => $this->workshops_model->get_workshops(),
                'empty'   => true,
                'onchange'=> "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'     => 'fields/select',
                'title'    => 'Вид вторсырья:',
                'name'     => 'product_id[]',
                'multiple' => true,
                'empty'    => true,
                'optgroup' => false,
                'options'  => $this->products_model->get_products(array('parent_id' => null)),
                'value'    => $get_params['product_id'],
                'onchange' => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'     => 'fields/checkbox',
                'title'    => 'Показать движение вторсырья:',
                'name'     => 'movement',
                'checked'  => $get_params['movement'],
                'onchange' => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'     => 'fields/checkbox',
                'title'    => 'Показать в движении остатки = 0',
                'name'     => 'zero',
                'checked'  => $get_params['zero'],
                'onchange' => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'          => 'fields/submit',
                'title'         => 'Сформировать',
                'type'          => 'ajax',
                'reaction_func' => true,
                'reaction'      => 'handle_ajaxResultAllData',
                'data_type'     => 'json',
                'id'            => 'btn-form'
              )
            )
          )
        )
      )),
    );

    // если запрос на формирование данных, иначе возвращаем шаблон - обертку
    if($render_table || $this->uri->getParam('ajax') == 1){
      $error = '';

      // условия для движения товара и подсчета общего прихода расхода
      $where = 'pr_store_movement_products.store_type_id = '. $type_id;
      if($get_params['date_start']){
        $where .= ($where ? ' AND ' : '').'pr_store_movement_products.date >= "'. $get_params['date_start'].'"';
      }
      if($get_params['date_end']){
        // к дате окончания добавляем 1 день, т.к. показания за этот день должны быть включены в отчет
        $date_end = date_format(date_modify(date_create($get_params['date_end']), '+1 day'), 'Y-m-d H:i:s');
        $where .= ($where ? ' AND ' : '').'pr_store_movement_products.date <= "'. $date_end.'"';
      }
      if($get_params['client_id']){
        $where .= ($where ? ' AND ' : '').'pr_store_movement_products.client_id = '. $get_params['client_id'];
      }
      if($get_params['store_workshop_id']){
        $where .= ($where ? ' AND ' : '').'pr_store_movement_products.store_workshop_id = '. $get_params['store_workshop_id'];
      }

      // Остатки
      // условия для расчета входящего остатка и исходящего остатка
      // если не указаны поставщик и виды вторсырья выводим общий остаток на начальную дату и на конечную дату
      $where_start = 'pr_store_movement_products.store_type_id = '. $type_id. ' AND pr_store_movement_products.date < "'. $get_params['date_start'].'"';
      $where_end = 'pr_store_movement_products.store_type_id = '. $type_id. ' AND pr_store_movement_products.date <= "'. $date_end.'"';
      if($get_params['client_id']){
        $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.client_id = '. $get_params['client_id'];
        $where_end .= ($where_end ? ' AND ' : '').'pr_store_movement_products.client_id = '. $get_params['client_id'];
      }
      if($get_params['store_workshop_id']){
        $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.store_workshop_id = '. $get_params['store_workshop_id'];
        $where_end .= ($where_end ? ' AND ' : '').'pr_store_movement_products.store_workshop_id = '. $get_params['store_workshop_id'];
      }
      // входящий остаток
      $rest_start = $this->store_model->calculate_rest($where_start, $get_params['product_id'], false, ($get_params['type'] == 'net' ? true : false));
      
      // если первичная продукция остатки по клиентам показываем
      if($type['id'] == 1){
        // исхдящий остаток по клиентам
        $rest_end_clients = $this->store_model->calculate_rest($where_end, $get_params['product_id'], true, ($get_params['type'] == 'net' ? true : false));
        // считаем общий остаток
        $rest_end = 0;
        foreach ($rest_end_clients as $key => $value) {
          $rest_end += $value['sum'];
        }
      } else {
        $rest_end = $this->store_model->calculate_rest($where_end, $get_params['product_id'], false, ($get_params['type'] == 'net' ? true : false));
      }

      $rest = array(
        'start'       => $rest_start,
        'end'         => $rest_end,
        'end_clients' => (isset($rest_end_clients) ? $rest_end_clients : array()),
        'coming'      => $this->store_model->calculate_coming($where, $get_params['product_id'], ($get_params['type'] == 'net' ? true : false)),
        'expenditure' => $this->store_model->calculate_expenditure($where, $get_params['product_id'], ($get_params['type'] == 'net' ? true : false)),
      );

      // Если нужно отобразить движение товара
      if($get_params['movement']){
        $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
        $limit = 50;
        $offset = $limit * ($page - 1);
        $cnt = $this->store_model->get_rests_cnt($where, $get_params['product_id']);
        // в движении не отображаем 0-ые остатки по клиенту и продукту
        if(!$get_params['zero']){
          $where .= ($where ? ' AND ' : '').'pr_store_movement_products.rest > 0';
        }
        $items = $this->store_model->get_rests($limit, $offset, $where, false, $get_params['product_id']);
        
        $pages = get_pages($page, $cnt, $limit);
        $postfix = '&';
        foreach ($get_params as $key => $get_param_value) {
          if(is_array($get_param_value)){
            foreach ($get_param_value as $value) {
              $postfix .= $key.'[]='.$value.'&';
            }
          } else {
            $postfix .= $key.'='.$get_param_value.'&';
          }
        }
        $pagination_data = array(
          'ajax'    => true,
          'pages'   => $pages,
          'page'    => $page,
          'prefix'  => '/admin'.$this->params['path'].'rests/'.$type_id.'/',
          'postfix' => $postfix
        );
      }
      
      $data = array_merge($data, array(
        'error'           => $error,
        'rest'            => $rest,
        'items'           => (isset($items) ? $items : array()),
        'pagination'      => (isset($pagination_data) ? $this->load->view('templates/pagination', $pagination_data, true) : '')
      ));

      if($render_table){
        return $this->load->view('../../application/components/store/templates/admin_rests_table',$data,true);
      } else if($this->uri->getParam('ajax') == 1){
        send_answer(array(
          'page'  => (isset($page) ? $page : 1),
          'pages' => (isset($pages) ? count($pages) : 0),
          'html'  => $this->load->view('../../application/components/store/templates/admin_rests_table',$data,true),
        ));
      }
    }
    
    return $this->render_template('templates/admin_items', array('data'=>$data));
  }

}