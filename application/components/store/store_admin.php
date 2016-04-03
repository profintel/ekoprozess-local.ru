<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('clients/models/clients_model');
    $this->load->model('store/models/store_model');
    $this->load->model('workshops/models/workshops_model');
    $this->load->model('acceptances/models/acceptances_model');
  }
  
  /**
  * Просмотр меню компонента
  */
  function index() {
    // определяем типы склада
    $types = $this->store_model->get_store_types(array('active'=>1));
    if(!$types){
      show_error('Не найдены типы склада');
    }
    $items = array();
    foreach ($types as $key => $type) {
      $items[] = array(
        'title' => $type['title'],
        'link'  => $this->lang_prefix.'/admin'. $this->params['path'] .'store_types/'.$type['id'] . '/'
      );
    }
    return $this->render_template('admin/menu', array(
      'title' => 'Склад',
      'items' => $items
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
      'items' => array(
        array(
          'title' => 'Приход',
          'link'  => $this->lang_prefix.'/admin'. $this->params['path'] .'comings/'.$type_id.'/'
        ),
        array(
          'title' => 'Расход',
          'link'  => $this->lang_prefix.'/admin'. $this->params['path'] .'expenditures/'.$type_id.'/'
        ),
        array(
          'title' => 'Остаток',
          'link'  => $this->lang_prefix.'/admin'. $this->params['path'] .'rests/'.$type_id.'/'
        ),
      ),
      'back' => $this->lang_prefix.'/admin'. $this->params['path']
    ));
  }
  
  /**
  * Просмотр таблицы приходов продукции
  */
  function comings($type_id) {
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }

    $where = array('store_comings.parent_id'=>null);
    //условие по типу склада
    $where['store_comings.store_type_id'] = $type['id'];
    $error = '';
    $product_id = $this->uri->getParam('product_id');
    $get_params = array(
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : ''),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : ''),
      'client_id'   => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'product_id'  => ($product_id && @$product_id[0] ? $product_id : array()),
    );
    if($get_params['date_start']){
      $where['store_comings.date_second >='] = $get_params['date_start'];
    }
    if($get_params['date_end']){
      $where['store_comings.date_second <='] = $get_params['date_end'];
    }
    if($get_params['client_id']){
      $where['store_comings.client_id'] = $get_params['client_id'];
    }

    $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
    $limit = 100;
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
    $items = $this->store_model->get_comings($limit, $offset, $where, false, $get_params['product_id']);

    $data = array(
      'title'       => 'Склад: '.$type['title'].'. Приход',
      'section'     => 'coming',
      'type_id'     => $type_id,
      'pagination'  => $this->load->view('templates/pagination', $pagination_data, true),
      //формируем ссылку на создание объекта
      'link_create' => array(
          'title' => 'Создать приход',
          'path'  => $this->lang_prefix.'/admin'.$this->component['path'].'create_coming/'.$type_id.'/',
        ),
      'error' => $error,
      'items' => $items
    );

    if($this->uri->getParam('ajax') == 1){
      echo $this->load->view('../../application/components/store/templates/admin_comins_table',$data,true);
    } else {
      return $this->render_template('templates/admin_items', array('data'=>$data));
    }
  }

  function _render_items_report_table($data){
    $data = unserialize(base64_decode($data));
    if($data['section'] == 'coming'){
      $template = '../../application/components/store/templates/admin_comins_table';
    }
    if($data['section'] == 'expenditure'){
      $template = '../../application/components/store/templates/admin_expenditures_table';
    }
    if($data['section'] == 'rest'){
      $template = '../../application/components/store/templates/admin_rests_table';
    }
    return $this->load->view($template,$data,true);
  }
   
  /**
  * Добавление нескольких видов вторсырья
  */ 
  function renderProductFields($return_type = 'array', $items = array(), $section = '', $type_id = 1) {
    $result = array();
    if ($items) {
      foreach ($items as $key => $item) {
        $result[] = $this->_renderProductFields(($key==0?true:false), $item, $section, $type_id);
      }
    } else {
      $result[] = $this->_renderProductFields(($return_type=='array'?true:false),false,$section,$type_id);
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
  function _renderProductFields($label = true, $item = array(), $section = '', $type_id = 1) {
    return array(
      'title'    => ($label ? 'Вторсырье' : ''),
      'collapse' => false,
      'class'    => 'clearfix '.($label ? 'form_block_label' : ''),
      'fields'   => array(
        array(
          'view'    => 'fields/hidden',
          'title'   => 'item_id:',
          'name'    => 'item_id[]',
          'value'   => ($item ? $item['id'] : 0)
        ),
        array(
          'view'    => 'fields/select',
          'title'   => ($label ? 'Вид вторсырья' : ''),
          'name'    => 'product_id[]',
          'empty'   => true,
          'optgroup'=> true,
          'options' => $this->products_model->get_products(array('parent_id' => null)),
          'value'   => ($item ? $item['product_id'] : ''),
          'onchange'=> 'updateRestProduct(this)',
          'form_group_class' => 'form_group_product_field form_group_w30',
        ),
        array(
          'view'    => 'fields/'.($type_id == 1 ? 'text' : 'hidden'),
          'title'   => ($label ? 'Брутто, (кг)' : ''),
          'name'    => 'gross[]',
          'value'   => ($item ? $item['gross'] : ''),
          'class'   => 'number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'    => 'fields/'.($type_id == 2 ? 'text' : 'hidden'),
          'title'   => ($label ? 'Нетто, (кг)' : ''),
          'name'    => 'net[]',
          'value'   => ($item ? $item['net'] : ''),
          'class'   => 'number',
          'form_group_class' => 'form_group_product_field'.($type_id == 2 ? ' form_group_w20' : ''),
        ),
        array(
          'view'  => 'fields/'.($type_id == 1 && $section == 'coming' ? 'text' : 'hidden'),
          'title' => ($label ? 'Упаковка, (кг)' : ''),
          'name'  => 'weight_pack[]',
          'value' => ($item && $section == 'coming' ? $item['weight_pack'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field'
        ),
        array(
          'view'  => 'fields/'.($type_id == 1 && $section == 'coming' ? 'text' : 'hidden'),
          'title' => ($label ? 'Засор, (%)' : ''),
          'name'  => 'weight_defect[]',
          'value' => ($item && $section == 'coming' ? $item['weight_defect'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'  => 'fields/text',
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
          'class'   => 'btn-default '.($item && $item['active'] ? ' disabled ' : '').($label ? 'form_group_product_field_btn' : 'form_group_product_field_btn_m5'),
          'icon'    => 'glyphicon-remove',
          'onclick' =>  ($item && $item['active'] ? 'return false;' : 'removeFormBlock(this,"'.($item ? '/admin/store/delete_'.$section.'/'.$item['id'] : '').'");'),
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
      )
    );
  }

  /**
   *  Создание прихода.
  **/
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
          'view'    => 'fields/'.($type_id == 1 ? 'datetime' : 'hidden'),
          'title'   => 'Дата прибытия машины:',
          'maxDate' => date('d.m.Y'),
          'name'    => 'date_primary',
        ),
        array(
          'view'    => 'fields/datetime',
          'title'   => 'Дата прихода на склад:',
          'minDate' => ($rest ? date('d.m.Y',strtotime($rest['date'])) : null),
          'maxDate' => date('d.m.Y'),
          'name'    => 'date_second',
        ),
        array(
          'view'    => 'fields/'.($type_id == 1 ? 'hidden' : 'select'),
          'title'   => 'Цех:',
          'name'    => 'store_workshop_id',
          'options' => $this->workshops_model->get_workshops(),
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

    $params = array(
      'store_type_id'     => $type_id,
      'date_primary'      => ($this->input->post('date_primary') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_primary'))) : NULL),
      'date_second'       => ($this->input->post('date_second') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_second'))) : NULL),
      'client_id'         => $client_id,
      'store_workshop_id' => ((int)$this->input->post('store_workshop_id') ? (int)$this->input->post('store_workshop_id') : NULL),
    );

    $errors = $this->_validate_coming_params($type_id, $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->store_model->create_coming($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    //добавляем вторсырье
    $params_products = array(
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
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
          'weight_pack'       => (float)str_replace(' ', '', $params_products['weight_pack'][$key]),
          'weight_defect'     => (float)str_replace(' ', '', $params_products['weight_defect'][$key]),
          'cnt_places'        => (float)str_replace(' ', '', $params_products['cnt_places'][$key]),
          'order'             => $key
        );
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

    send_answer(array('redirect' => '/admin'.$this->params['path'].'edit_coming/'.$id.'/'));
  }
  
  function _validate_coming_params($type_id, $params) {
    $errors = array();
    if ($type_id == 1 && !$params['date_primary']) { $errors['date_primary'] = 'Не указана дата прибытия машины'; }
    if ($type_id == 1 && !$params['client_id']) { $errors['client_id'] = 'Не указан поставщик'; }
    if($type_id == 1 && $params['date_second'] && $params['date_second'] < $params['date_primary']){
      $errors['date_second'] = 'Дата прихода не может быть меньше даты прибытия машины';
    }
    if ($type_id == 2 && !$params['date_second']) { $errors['client_id'] = 'Не указана дата прихода на склад'; }

    return $errors;
  }

  /**
   *  Редактирование прихода.
  **/
  function edit_coming($id){
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
          'empty'     => true,
          'onchange'  => 'updateRestProduct(this)',
        ),
        array(
          'view'    => 'fields/'.($type['id'] == 1 ? 'datetime' : 'hidden'),
          'title'   => 'Дата прибытия машины:',
          'maxDate' => date('d.m.Y'),
          'name'    => 'date_primary',
          'value'   => ($item['date_primary'] ? date('d.m.Y H:i', strtotime($item['date_primary'])) : '')
        ),
        array(
          'view'    => 'fields/datetime',
          'title'   => 'Дата прихода на склад:',
          'minDate' => ($rest ? date('d.m.Y',strtotime($rest['date'])) : null),
          'maxDate' => date('d.m.Y'),
          'name'    => 'date_second',
          'value'   => ($item['date_second'] ? date('d.m.Y H:i', strtotime($item['date_second'])) : '')
        ),
        array(
          'view'    => 'fields/'.($type['id'] == 1 ? 'hidden' : 'select'),
          'title'   => 'Цех:',
          'name'    => 'store_workshop_id',
          'options' => $this->workshops_model->get_workshops(),
          'value'   => $item['store_workshop_id']
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

    $productsFields = $this->renderProductFields('array', $item['childs'], 'coming', $type['id']);
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
        array(
          'view'     => 'fields/submit',
          'title'    => 'Отправить на склад',
          'type'     => 'ajax',
          'class'    => 'btn-default',
          'onclick'  => 'sendMovement("",this);'
        ),
        array(
          'view'     => 'fields/submit',
          'title'    => 'Сохранить',
          'type'     => 'ajax',
          'reaction' => 'reload'
        ),
      );
    }
    // акт приемки по приходу
    $acceptance = $this->acceptances_model->get_acceptance(array('store_coming_id'=>$item['id']));
    if($acceptance){
      $blocks['submits']['fields'][] = array(
        'view'    => 'fields/submit',
        'title'   => 'Акт приемки',
        'type'    => '',
        'class'   => 'btn-default pull-left m-l-0',
        'onclick' => 'document.location = "/admin/acceptances/edit_acceptance/'.$acceptance['id'].'/"'
      );
    }

    return $this->render_template('admin/inner', array(
      'title' => 'Склад: '.$type['title'].'. Редактирование прихода',
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

    $params = array(
      'date_primary'      => ($this->input->post('date_primary') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_primary'))) : NULL),
      'date_second'       => ($this->input->post('date_second') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_second'))) : NULL),
      'client_id'         => ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : NULL),
      'store_workshop_id' => ((int)$this->input->post('store_workshop_id') ? (int)$this->input->post('store_workshop_id') : NULL),
    );

    $errors = $this->_validate_coming_params($item['store_type_id'], $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->store_model->update_coming($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    //редактируем/добавляем вторсырье
    $params_products = array(
      'item_id'       => $this->input->post('item_id'),
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
      'weight_pack'   => $this->input->post('weight_pack'),
      'weight_defect' => $this->input->post('weight_defect'),
      'cnt_places'    => $this->input->post('cnt_places'),
    );
    // для готовой продукции вторсырье обязательно для заполнения всегда
    if($item['store_type_id'] == 2 && (!is_array($params_products['product_id']) || !@$params_products['product_id'][0])){
      send_answer(array('errors' => array('Не указаны параметры вторсырья')));
    }
    if(is_array($params_products['product_id'])){
      // перед удалением проверяем указан ли вес вторсырья
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
          'client_id'         => $params['client_id'],
          'store_type_id'     => $item['store_type_id'],          
          'store_workshop_id' => $params['store_workshop_id'],
          'product_id'        => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'             => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'               => (float)str_replace(' ', '', $params_products['net'][$key]),
          'weight_pack'       => (float)str_replace(' ', '', $params_products['weight_pack'][$key]),
          'weight_defect'     => (float)str_replace(' ', '', $params_products['weight_defect'][$key]),
          'cnt_places'        => (float)str_replace(' ', '', $params_products['cnt_places'][$key]),
          'order'             => $key
        );
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

    // Редактируем акт приемки по приходу первичного вторсырья
    if($item['store_type_id'] == 1){
      $this->load->component(array('name' => 'acceptances'));
      $acceptance = $this->acceptances_model->get_acceptance(array('store_coming_id'=>$id));
      if($acceptance){
        if (!$this->acceptances->_edit_acceptance_process($acceptance['id'], true)){
          $this->delete_coming($id, true);
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

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }

  /**
   * ПРоверка возможности удаления прихода
  **/
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
  **/
  function delete_coming($id, $return = false) {
    $item = $this->store_model->get_coming(array('store_comings.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }
    if($item['active']){
      send_answer(array('errors' => array('Невозможно удалить приход, т.к. он отправлен на склад')));
    }
    if (!$this->store_model->delete_coming((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    if($return){
      return true;
    }

    send_answer();
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

    // ошибка, если есть приход не отправленный на склад с более ранней датой
    if($this->store_model->get_comings_cnt(array(
      'store_comings.store_type_id' => $item['store_type_id'],
      'store_comings.id !='         => $id,
      'store_comings.active'        => 0,
      'store_comings.date_second <' => $item['date_second']))){
      send_answer(array('errors' => array('Существует более ранний приход не отправленный на склад, для корректного подсчета остатков необходимо отправлять на склад приходы по дате прихода на склад')));
    }

    // ошибка, если есть расход не отправленный на склад с более ранней датой
    if($this->store_model->get_expenditures_cnt(array(
      'store_expenditures.store_type_id'  => $item['store_type_id'],
      'store_expenditures.active'         => 0,
      'store_expenditures.date <='        => $item['date_second']))){
      send_answer(array('errors' => array('Существует более ранний расход не отправленный на склад, для корректного подсчета остатков отправьте все расходы на склад')));
    }

    // ошибка, если есть строки в движении с поздней датой
    $rest = $this->store_model->get_rest(array('store_type_id' => $item['store_type_id']));
    if($rest && date('Y-m-d', strtotime($rest['date'])) > date('Y-m-d', strtotime($item['date_second']))){
      send_answer(array('errors' => array('В движении вторсырья на складе существует приход или расход с более поздней датой, для корректного подсчета остатков приходы и расходы должны быть заполненны по мере прибывания на склад')));
    }

    // Отправляем приход по каждому вторсырью
    // в таблицу движения продукции
    foreach ($item['childs'] as $key => $child) {
      $params = array(
        'store_type_id'     => $item['store_type_id'],
        'store_workshop_id' => $item['store_workshop_id'],
        'coming_id'         => $item['id'],
        'client_id'         => $item['client_id'],
        'product_id'        => $child['product_id'],
        'date'              => $item['date_second'],
        // если первичая продукция берем брутто, иначе нетто
        'coming'            => ($item['store_type_id'] == 1 ? $child['gross'] : $child['net'])
      );
      // записываем приход
      $id = $this->store_model->create_movement_products($params);
      if(!$id){
        $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка добавления вторсырья на склад')));
      }
      
      // записываем остаток в текущую строку движения
      if(!$this->store_model->update_movement_products($id, array(
          // считаем остатки по клиенту и вторсырью с учетом добавленной строки
          'rest'          => $this->store_model->calculate_rest(array(
              'store_type_id' => $item['store_type_id'],
              'client_id'     => $item['client_id'],
              'product_id'    => $child['product_id']
            )),
          // общие остатки по сырью
          'rest_product'  => $this->store_model->calculate_rest(array(
              'store_type_id' => $item['store_type_id'],
              'product_id'    => $child['product_id']
            )), 
          // общие остатки всего сырья
          'rest_all' => $this->store_model->calculate_rest(array('store_type_id'=>$item['store_type_id']))
        ))){
        $this->store_model->delete_movement_products(array('coming_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка подсчета остатков по вторсырью')));
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
  function expenditures($type_id) {
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }

    $where = array('store_expenditures.parent_id'=>null);
    //условие по типу склада
    $where['store_expenditures.store_type_id'] = $type['id'];
    $error = '';
    $product_id = $this->uri->getParam('product_id');
    $get_params = array(
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : ''),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : ''),
      'client_id'   => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'product_id'  => ($product_id && @$product_id[0] ? $product_id : array()),
    );
    if($get_params['date_start']){
      $where['store_expenditures.date >='] = $get_params['date_start'];
    }
    if($get_params['date_end']){
      $where['store_expenditures.date <='] = $get_params['date_end'];
    }
    if($get_params['client_id']){
      $where['store_expenditures.client_id'] = $get_params['client_id'];
    }

    $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
    $limit = 100;
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
    $items = $this->store_model->get_expenditures($limit, $offset, $where, false, $get_params['product_id']);

    $data = array(
      'title'      => 'Склад: '.$type['title'].'. Расход',
      'section'    => 'expenditure',
      'type_id'    => $type_id,
      'pagination' => $this->load->view('templates/pagination', $pagination_data, true),
      //формируем ссылку на создание объекта
      'link_create' => array(
          'title' => 'Создать расход',
          'path' => $this->lang_prefix.'/admin'.$this->component['path'].'create_expenditure/'.$type_id.'/',
        ),
      'error' => $error,
      'items' => $items
    );

    if($this->uri->getParam('ajax') == 1){
      echo $this->load->view('../../application/components/store/templates/admin_expenditures_table',$data,true);
    } else {
      return $this->render_template('templates/admin_items', array('data'=>$data));
    }
  }

  /**
   *  Создание расхода.
  **/
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
            'options'   => $this->clients_model->get_clients(),
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
        'minDate'   => ($rest ? date('d.m.Y',strtotime($rest['date'])) : null),
        'maxDate'   => date('d.m.Y'),
        'name'      => 'date',
        'onchange'  => 'updateRestProduct(this)',
      ),
      array(
        'view'    => 'fields/'.($type_id == 1 ? 'select' : 'hidden'),
        'title'   => 'Цех:',
        'name'    => 'store_workshop_id',
        'options' => $this->workshops_model->get_workshops(),
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
      'date'              => ($this->input->post('date') ? date('Y-m-d H:i:s', strtotime($this->input->post('date'))) : NULL)
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
    // перед добавлением проверяем указаны ли все необходимые параметры
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
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
        $rest = $this->store_model->get_rest(array('store_type_id'=>$params['store_type_id'],'client_id'=>$params['client_id'],'product_id'=>$product_id,'date <='=>$params['date']));
        if(!$rest || ($rest['rest'] - $expenditure) < 0){
          $this->delete_expenditure($id, true);
          send_answer(array('errors' => array('Остаток на складе не может быть меньше 0. Проверьте расход вторсырья')));
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
          'product_id'    => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'         => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'           => (float)str_replace(' ', '', $params_products['net'][$key]),
          'cnt_places'    => (float)str_replace(' ', '', $params_products['cnt_places'][$key])
        );
        if (!$this->store_model->create_expenditure($params)) {
          $this->delete_expenditure($id, true);
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья')));
        }
      }
    }

    send_answer(array('redirect' => '/admin'.$this->params['path'].'edit_expenditure/'.$id.'/'));
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
  function edit_expenditure($id){
    $item = $this->store_model->get_expenditure(array('store_expenditures.id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }
    $type = $this->store_model->get_store_type(array('id'=>(int)$item['store_type_id']));
    if(!$type){
      show_error('Не найден тип склада');
    }

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
        'minDate'   => ($rest ? date('d.m.Y',strtotime($rest['date'])) : null),
        'maxDate'   => date('d.m.Y'),
        'name'      => 'date',
        'onchange'  => 'updateRestProduct(this)',
        'value'     => ($item['date'] ? date('d.m.Y H:i', strtotime($item['date'])) : '')
      ),
      array(
        'view'    => 'fields/'.($type['id'] == 1 ? 'select' : 'hidden'),
        'title'   => 'Цех:',
        'name'    => 'store_workshop_id',
        'value'   => $item['store_workshop_id'],
        'options' => $this->workshops_model->get_workshops(),
        'empty'   => true,
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
            'reaction' => ''
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

    return $this->render_template('admin/inner', array(
      'title' => 'Склад: '.$type['title'].'. Редактирование расхода',
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

    );

    $errors = $this->_validate_expenditure_params($item['store_type_id'], $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
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
    // перед удалением проверяем указан ли все необходимые параметры
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        if($item['store_type_id'] == 1){
          $expenditure = (float)str_replace(' ', '', $params_products['gross'][$key]);
        } else {
          $expenditure = (float)str_replace(' ', '', $params_products['net'][$key]);
        }
        if($expenditure <= 0){
          send_answer(array('errors' => array('Укажите вес для вторсырья')));
        }

        // проверяем чтобы остаток не был < 0
        $rest = $this->store_model->get_rest(array('store_type_id'=>$item['store_type_id'],'client_id'=>$params['client_id'],'product_id'=>$product_id,'date <='=>$params['date']));
        if(!$rest || ($rest['rest'] - $expenditure) < 0){
          send_answer(array('errors' => array('Остаток на складе не может быть меньше 0. Проверьте расход вторсырья')));
        }
      }
    }
    
    if (!$this->store_model->update_expenditure($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    //удаляем все параметры вторсырья по приходу и добавляем заново указанные
    if (!$this->store_model->delete_expenditure(array('parent_id'=>$item['id']))) {
      send_answer(array('errors' => array('Ошибка при удалении вторсырья')));
    }
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        //по ключу собираем все параметры вторсырья
        $params = array(
          'parent_id'         => $id,
          'client_id'         => $params['client_id'],
          'store_type_id'     => $item['store_type_id'],
          'store_workshop_id' => $params['store_workshop_id'],
          'product_id'        => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'             => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'               => (float)str_replace(' ', '', $params_products['net'][$key]),
          'cnt_places'        => (float)str_replace(' ', '', $params_products['cnt_places'][$key])
        );

        if (!$this->store_model->create_expenditure($params)) {
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья')));
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
  **/
  function send_expenditure_movement($id) {
    $item = $this->store_model->get_expenditure(array('store_expenditures.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }
    if(!$item['childs']){
      send_answer(array('errors' => array('Расход должен содержать вторсырье')));
    }

    // ошибка, если есть расход не отправленный на склад с более ранней датой
    if($this->store_model->get_expenditures_cnt(array(
      'store_expenditures.store_type_id'  => $item['store_type_id'],
      'store_expenditures.id !='          => $id,
      'store_expenditures.active'         => 0,
      'store_expenditures.date <'         => $item['date']))){
      send_answer(array('errors' => array('Существует более ранний расход не отправленный на склад, для корректного подсчета остатков необходимо отправлять на склад расходы по возрастанию даты')));
    }

    // ошибка, если есть приход не отправленный на склад с более ранней датой
    if($this->store_model->get_comings_cnt(array(
      'store_comings.store_type_id' => $item['store_type_id'],
      'store_comings.active'        => 0,
      'store_comings.date_second <' => $item['date']))){
      send_answer(array('errors' => array('Существует более ранний приход не отправленный на склад, для корректного подсчета остатков необходимо отправлять на склад приходы по дате прихода на склад')));
    }

    // ошибка, если есть строки в движении с поздней датой
    $rest = $this->store_model->get_rest(array('store_type_id' => $item['store_type_id']));
    if($rest && date('Y-m-d', strtotime($rest['date'])) > date('Y-m-d', strtotime($item['date']))){
      send_answer(array('errors' => array('В движении вторсырья на складе существует приход или расход с более поздней датой, для корректного подсчета остатков приходы и расходы должны быть заполненны по мере прибывания на склад')));
    }

    // Отправляем расход по каждому вторсырью в таблицу движения продукции
    foreach ($item['childs'] as $key => $child) {
      $params = array(
        'store_type_id'     => $item['store_type_id'],
        'store_workshop_id' => $item['store_workshop_id'],
        'expenditure_id'    => $item['id'],
        'client_id'         => $item['client_id'],
        'product_id'        => $child['product_id'],
        'date'              => $item['date'],
        // если первичая продукция берем брутто, иначе нетто
        'expenditure'   => ($item['store_type_id'] == 1 ? $child['gross'] : $child['net'])
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
      
      // записываем остаток в текущую строку движения
      if(!$this->store_model->update_movement_products($id, array(
          // считаем остатки по клиенту и вторсырью с учетом добавленной строки
          'rest'  => $this->store_model->calculate_rest(array(
              'store_type_id' => $item['store_type_id'],
              'client_id'     => $item['client_id'],
              'product_id'    => $child['product_id']
            )),
          // общие остатки по сырью
          'rest_product'  => $this->store_model->calculate_rest(array(
              'store_type_id' => $item['store_type_id'],
              'product_id'    => $child['product_id']
            )), 
          // общие остатки всего сырья
          'rest_all' => $this->store_model->calculate_rest(array('store_type_id'=>$item['store_type_id']))
        ))){
        $this->store_model->delete_movement_products(array('expenditure_id' => $item['id']));
        send_answer(array('errors' => array('Ошибка подсчета остатков по вторсырью')));
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
    if($item['active']){
      send_answer(array('errors' => array('Невозможно удалить расход, т.к. он отправлен на склад')));
    }
    if (!$this->store_model->delete_expenditure((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    if($return){
      return true;
    }
    
    send_answer();
  }  

  /**
   * Просмотр отчета остатков на складе
  **/
  function rests($type_id){
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    $error = '';
    $product_id = $this->uri->getParam('product_id');
    $get_params = array(
      'date_start'        => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : date('Y-m-1')),
      'date_end'          => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : date('Y-m-d')),
      'client_id'         => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'store_workshop_id' => ((int)$this->uri->getParam('store_workshop_id') ? (int)$this->uri->getParam('store_workshop_id') : ''),
      'product_id'        => ($product_id && @$product_id[0] ? $product_id : array()),
      'movement'          => ($this->uri->getParam('movement') ? true : false),
    );

    // условия для движения товара и подсчета общего прихода расхода
    $where = 'pr_store_movement_products.store_type_id = '. $type_id;
    if($get_params['date_start']){
      $where .= ($where ? ' AND ' : '').'pr_store_movement_products.date >= "'. $get_params['date_start'].'"';
    }
    if($get_params['date_end']){
      $where .= ($where ? ' AND ' : '').'pr_store_movement_products.date <= "'. $get_params['date_end'].'"';
    }
    if($get_params['client_id']){
      $where .= ($where ? ' AND ' : '').'pr_store_movement_products.client_id = '. $get_params['client_id'];
    }
    if($get_params['store_workshop_id']){
      $where .= ($where ? ' AND ' : '').'pr_store_movement_products.store_workshop_id = '. $get_params['store_workshop_id'];
    }
    if($get_params['product_id']){
      $where .= ($where ? ' AND ' : '').'pr_store_movement_products.product_id IN ('.implode(',', $get_params['product_id']).')';
    }

    // Остатки
    // условия для расчета входящего остатка и исходящего остатка
    // если не указаны поставщик и виды вторсырья выводим общий остаток на начальную дату и на конечную дату
    if(!$get_params['client_id'] && !$get_params['store_workshop_id'] && !$get_params['product_id']){
      $rest_start = $this->store_model->get_rest('pr_store_movement_products.store_type_id = '. $type_id. ' AND pr_store_movement_products.date < "'. $get_params['date_start'].'"');
      $rest_start = ($rest_start ? $rest_start['rest_all'] : 0);
      $rest_end = $this->store_model->get_rest('pr_store_movement_products.store_type_id = '. $type_id. ' AND pr_store_movement_products.date <= "'. $get_params['date_end'].'"');
      $rest_end = ($rest_end ? $rest_end['rest_all'] : 0);
    } else {
      $where_start = 'pr_store_movement_products.store_type_id = '. $type_id. ' AND pr_store_movement_products.date < "'. $get_params['date_start'].'"';
      $where_end = 'pr_store_movement_products.store_type_id = '. $type_id. ' AND pr_store_movement_products.date <= "'. $get_params['date_end'].'"';
      if($get_params['client_id']){
        $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.client_id = '. $get_params['client_id'];
        $where_end .= ($where_end ? ' AND ' : '').'pr_store_movement_products.client_id = '. $get_params['client_id'];
      }
      if($get_params['store_workshop_id']){
        $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.store_workshop_id = '. $get_params['store_workshop_id'];
        $where_end .= ($where_end ? ' AND ' : '').'pr_store_movement_products.store_workshop_id = '. $get_params['store_workshop_id'];
      }
      if($get_params['product_id']){
        $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.product_id IN ('.implode(',', $get_params['product_id']).')';
        $where_end .= ($where_end ? ' AND ' : '').'pr_store_movement_products.product_id IN ('.implode(',', $get_params['product_id']).')';
      }
      $rest_start = $this->store_model->calculate_rest($where_start);
      $rest_end = $this->store_model->calculate_rest($where_end);
    }

    $rest = array(
        'start' => $rest_start,
        'end'   => $rest_end,
        'coming' => $this->store_model->calculate_coming($where),
        'expenditure' => $this->store_model->calculate_expenditure($where),
      );

    // Если нужно отобразить движение товара
    if($get_params['movement']){
      $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
      $limit = 200;
      $offset = $limit * ($page - 1);
      $cnt = $this->store_model->get_rests_cnt($where);
      $items = $this->store_model->get_rests($limit, $offset, $where);
      
      /*Подсчет общего остатка на складе: 03.04.2016 сказали его отображать не надо
      // если не указаны поставщик и виды вторсырья в движении показываем остаток общий из базы
      // иначе считаем остаток по каждой строке движения
      if($items && ($get_params['client_id'] || $get_params['store_workshop_id'] || $get_params['product_id'])){
        // для первой страницы входящий остаток посчитан с учетом даты из формы
        if($page > 1){
          //для остальных страниц считаем входящий остаток с учетом id первого элемента items
          $where_start = 'pr_store_movement_products.store_type_id = '. $type_id. ' AND pr_store_movement_products.id < "'. $items[0]['id'].'"';
          if($get_params['client_id']){
            $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.client_id = '. $get_params['client_id'];
          }
          if($get_params['store_workshop_id']){
            $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.store_workshop_id = '. $get_params['store_workshop_id'];
          }
          if($get_params['product_id']){
            $where_start .= ($where_start ? ' AND ' : '').'pr_store_movement_products.product_id IN ('.implode(',', $get_params['product_id']).')';
          }
          // начальный остаток для текущей страницы движения вторсырья
          $rest_start = $this->store_model->calculate_rest($where_start);          
        }
        // перезаписываем остаток по движению - rest_all
        foreach ($items as $key => &$item) {
          $rest_start += $item['coming'] - $item['expenditure'];         
          $item['rest_all'] = $rest_start;
        }
      }
      */
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
    
    $data = array(
      'title'           => 'Склад: '.$type['title'].'. Остаток',
      'section'         => 'rest',
      'type_id'         => $type_id,
      'type'            => $type,
      'error'           => $error,
      'client'          => ($get_params['client_id'] ? $this->clients_model->get_client(array('id'=>$get_params['client_id'])) : false),
      'products'        => ($get_params['product_id'] ? $this->products_model->get_products('id IN ('.implode(',', $get_params['product_id']).')') : false),
      'rest'            => $rest,
      'items'           => (isset($items) ? $items : array()),
      'get_params'      => $get_params,
      'pagination'      => (isset($pagination_data) ? $this->load->view('templates/pagination', $pagination_data, true) : ''),
      'form' => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'rests/'.$type_id.'/',        
        'enctype' => '',
        'blocks' => array(
          array(
            'title'         => 'Расширенный поиск',
            'fields'   => array(
              array(
                'view'        => 'fields/datetime',
                'title'       => 'Дата от:',
                'name'        => 'date_start',
                'value'       => ($get_params['date_start']? date('d.m.Y',strtotime($get_params['date_start'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'        => 'fields/datetime',
                'title'       => 'Дата до:',
                'name'        => 'date_end',
                'value'       => ($get_params['date_end']? date('d.m.Y',strtotime($get_params['date_end'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'       => 'fields/'.($type['id'] == 1 ? 'select' : 'hidden'),
                'title'      => 'Поставщик:',
                'name'       => 'client_id',
                'text_field' => 'title_full',
                'value'      => $get_params['client_id'],
                'options'    => $this->clients_model->get_clients(),
                'empty'      => true,
                'onchange'   => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Цех:',
                'name'    => 'store_workshop_id',
                'value'   => $get_params['store_workshop_id'],
                'options' => $this->workshops_model->get_workshops(),
                'empty'   => true,
                'onchange'=> "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'     => 'fields/select',
                'title'    => 'Вид вторсырья:',
                'name'     => 'product_id[]',
                'multiple' => true,
                'empty'    => true,
                'optgroup' => true,
                'options'  => $this->products_model->get_products(array('parent_id' => null)),
                'value'    => $get_params['product_id'],
                'onchange' => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'     => 'fields/checkbox',
                'title'    => 'Показать движение вторсырья:',
                'name'     => 'movement',
                'checked'  => $get_params['movement'],
                'onchange' => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'          => 'fields/submit',
                'title'         => 'Сформировать',
                'type'          => 'ajax',
                'failure'       => '?ajax=1',
                'reaction_func' => true,
                'reaction'      => 'handle_ajaxResultHTML',
                'data_type'     => 'html'
              )
            )
          )
        )
      )),
    );
    
    if($this->uri->getParam('ajax') == 1){
      echo $this->load->view('../../application/components/store/templates/admin_rests_table',$data,true);
    } else {
      return $this->render_template('templates/admin_items', array('data'=>$data));
    }
  }
}