<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('clients/models/clients_model');
    $this->load->model('store/models/store_model');
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
      'title' => 'Склад: Первичная продукция',
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
          'link'  => $this->lang_prefix.'/admin'. $this->params['path'] .'remains/'.$type_id.'/'
        ),
      )
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
      'title'   => 'Склад: '.$type['title'].'. Приход',
      'section' => 'coming',
      //формируем ссылку на создание объекта
      'link_create' => array(
          'title' => 'Создать приход',
          'path' => $this->lang_prefix.'/admin'.$this->component['path'].'create_coming/'.$type_id.'/',
        ),
      'error' => $error,
      'items' => $items
    );

    if($this->uri->getParam('ajax') == 1){
      echo $this->load->view('../../application/components/store/templates/admin_items_table',$data,true);
    } else {
      return $this->render_template('templates/admin_items', array('data'=>$data));
    }
  }

  function _render_items_report_table($data){
    $data = unserialize(base64_decode($data));
    return $this->load->view('../../application/components/store/templates/admin_items_table',$data,true);
  }
   
  /**
  * Добавление нескольких видов вторсырья
  */ 
  function renderProductFields($return_type = 'array', $items = array(), $section = '') {
    $result = array();
    if ($items) {
      foreach ($items as $key => $item) {
        $result[] = $this->_renderProductFields(($key==0?true:false), $item, $section);
      }
    } else {
      $result[] = $this->_renderProductFields(($return_type=='array'?true:false));
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
          'onclick'  => 'renderFieldsProducts("/admin/store/renderProductFields/html/", this);',
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
  * Формирует поля блока с вторсырьем
  * для формы акта приемки
  * $label - указывает нади ли формировать заголовик
  * $item - массив с данными по вторсырью
  */ 
  function _renderProductFields($label = true, $item = array(), $section = '') {
    return array(
      'title'    => ($label ? 'Вторсырье' : ''),
      'collapse' => false,
      'class'    => 'clearfix '.($label ? 'form_block_label' : ''),
      'fields'   => array(
        array(
          'view'    => 'fields/hidden',
          'title'   => 'item_id:',
          'name'    => 'item_id[]',
          'value'   => ($item ? $item['id'] : '')
        ),
        array(
          'view'    => 'fields/select',
          'title'   => ($label ? 'Вид вторсырья' : ''),
          'name'    => 'product_id[]',
          'empty'   => true,
          'optgroup'=> true,
          'options' => $this->products_model->get_products(array('parent_id' => null)),
          'value'   => ($item ? $item['product_id'] : ''),
          'form_group_class' => 'form_group_product_field form_group_w50',
        ),
        array(
          'view'  => 'fields/text',
          'title' => ($label ? 'Брутто, (кг)' : ''),
          'name'  => 'gross[]',
          'value' => ($item ? $item['gross'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field form_group_w20',
        ),
        array(
          'view'  => 'fields/text',
          'title' => ($label ? 'Кол-во мест' : ''),
          'name'  => 'cnt_places[]',
          'value' => ($item ? $item['cnt_places'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field form_group_w20',
        ),
        array(
          'view'    => 'fields/submit',
          'title'   => '',
          'class'   => 'btn-default '.($label ? 'form_group_product_field_btn' : 'form_group_product_field_btn_m5'),
          'icon'    => 'glyphicon-remove',
          'onclick' =>  'removeFormBlock(this,"'.($item ? '/admin/store/delete_'.$section.'/'.$item['id'] : '').'");',
        ),
        array(
          'view'     => 'fields/submit',
          'title'    => '',
          'type'     => 'ajax',
          'class'    => 'btn-primary '.($label ? 'form_group_product_field_btn' : 'form_group_product_field_btn_m5'),
          'icon'     => 'glyphicon-plus',
          'onclick'  => 'renderFieldsProducts("/admin/store/renderProductFields/html/", this);',
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

    if($type_id == 1){
      $client_id = ($this->uri->getParam('client_id') ? mysql_prepare($this->uri->getParam('client_id')) : 0);
      $blocks = array(array(
        'title'   => 'Основные параметры',
        'fields'   => array(
          array(
            'view'      => 'fields/select',
            'title'     => 'Клиент:',
            'name'      => 'client_id',
            'text_field'=> 'title_full',
            'options'   => $this->clients_model->get_clients(),
            'value'     => $client_id,
            'empty'     => true,
          ),
          array(
            'view'        => 'fields/text',
            'title'       => 'Поставщик:',
            'description' => 'Укажите в случае, если поставщика нет в базе клиентов',
            'name'        => 'company',
          ),
          array(
            'view'  => 'fields/datetime',
            'title' => 'Дата прибытия машины:',
            'name'  => 'date_primary'
          ),
          array(
            'view'  => 'fields/datetime',
            'title' => 'Дата прихода на склад:',
            'name'  => 'date_second'
          )
        )
      ));
      $productsFields = $this->renderProductFields();
      foreach ($productsFields as $key => $productField) {
        $blocks[] = $productField;
      }     
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
    if(!$client_id && !$this->input->post('company')){
      send_answer(array('errors' => array('Не указан поставщик')));
    }
    if(!$client_id){
      $client_id = $this->clients_model->create_client(array(
        'title'       => htmlspecialchars($this->input->post('company')),
        'title_full'  => htmlspecialchars($this->input->post('company')),
        'one_time'    => true
      ));
    }

    $params = array(
      'store_type_id' => $type_id,
      'date_primary'  => ($this->input->post('date_primary') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_primary'))) : NULL),
      'date_second'   => ($this->input->post('date_second') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_second'))) : NULL),
      'client_id'     => $client_id
    );

    $errors = $this->_validate_params($type_id, $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->store_model->create_coming($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    //добавляем к акту вторсырье
    $params_products = array(
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
      'cnt_places'    => $this->input->post('cnt_places'),
    );
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        //по ключу собираем все параметры вторсырья
        $params = array(
          'parent_id'     => $id,
          'client_id'     => $params['client_id'],
          'product_id'    => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'         => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'           => (float)str_replace(' ', '', $params_products['net'][$key]),
          'cnt_places'    => (float)str_replace(' ', '', $params_products['cnt_places'][$key])
        );
        if (!$this->store_model->create_coming($params)) {
          $this->delete_coming($id);
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья в акт')));
        }
      }
    }

    send_answer();
  }
  
  function _validate_params($type_id, $params) {
    $errors = array();
    if (!$params['date_primary']) { $errors['date_primary'] = 'Не указана дата прибытия машины'; }
    if (!$params['client_id']) { $errors['client_id'] = 'Не указан поставщик'; }

    return $errors;
  }

  /**
   *  Создание прихода.
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

    if($type['id'] == 1){
      $blocks = array(array(
        'title'   => 'Основные параметры',
        'fields'   => array(
          array(
            'view'      => 'fields/select',
            'title'     => 'Клиент:',
            'name'      => 'client_id',
            'text_field'=> 'title_full',
            'options'   => $this->clients_model->get_clients(),
            'value'     => $item['client_id'],
            'empty'     => true,
          ),
          array(
            'view'  => 'fields/datetime',
            'title' => 'Дата прибытия машины:',
            'name'  => 'date_primary',
            'value' => ($item['date_primary'] ? date('d.m.Y H:i:s', strtotime($item['date_primary'])) : '')
          ),
          array(
            'view'  => 'fields/datetime',
            'title' => 'Дата прихода на склад:',
            'name'  => 'date_second',
            'value' => ($item['date_second'] ? date('d.m.Y H:i:s', strtotime($item['date_second'])) : '')
          )
        )
      ));
      $productsFields = $this->renderProductFields('array', $item['childs'], 'coming');
      foreach ($productsFields as $key => $productField) {
        $blocks[] = $productField;
      }      
    }

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
      )
    );

    return $this->render_template('admin/inner', array(
      'title' => 'Склад: '.$type['title'].'. Добавление прихода',
      'html' => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix.'/admin'. $this->params['path'] .'_edit_coming_process/'.$id.'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix.'/admin'. $this->params['path'] . 'comings/'.$type['id'].'/'
    ), TRUE);
  }
  
  function _edit_coming_process($id) {
    $item = $this->store_model->get_coming(array('store_comings.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }

    // Если поставщик не указан, создаем нового поставщика в базе с отметкой "Разовый поставщик"
    $client_id = ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : NULL);
    if(!$client_id && !$this->input->post('company')){
      send_answer(array('errors' => array('Не указан поставщик')));
    }
    if(!$client_id){
      $client_id = $this->clients_model->create_client(array(
        'title'       => htmlspecialchars($this->input->post('company')),
        'title_full'  => htmlspecialchars($this->input->post('company')),
        'one_time'    => true
      ));
    }

    $params = array(
      'date_primary'  => ($this->input->post('date_primary') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_primary'))) : NULL),
      'date_second'   => ($this->input->post('date_second') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_second'))) : NULL),
      'client_id'     => $client_id
    );

    $errors = $this->_validate_params($item['store_type_id'], $params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->store_model->update_coming($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    //редактируем/добавляем к акту вторсырье
    $params_products = array(
      'item_id'       => $this->input->post('item_id'),
      'product_id'    => $this->input->post('product_id'),
      'gross'         => $this->input->post('gross'),
      'net'           => $this->input->post('net'),
      'cnt_places'    => $this->input->post('cnt_places'),
    );
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        //по ключу собираем все параметры вторсырья
        $params = array(
          'parent_id'     => $id,
          'client_id'     => $params['client_id'],
          'product_id'    => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'gross'         => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'net'           => (float)str_replace(' ', '', $params_products['net'][$key]),
          'cnt_places'    => (float)str_replace(' ', '', $params_products['cnt_places'][$key])
        );
        if ($params_products['item_id'][$key] && 
          !$this->store_model->update_coming($params_products['item_id'][$key], $params)) {
          send_answer(array('errors' => array('Ошибка при сохранении вторсырья в акте')));
        }
        if (!$params_products['item_id'][$key] && !$this->store_model->create_coming($params)) {
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья в акт')));
        }
      }
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }

  /**
   * Удаление прихода
  **/
  function delete_coming($id) {
    if (!$this->store_model->delete_coming((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }

}