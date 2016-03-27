<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acceptances_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('acceptances/models/acceptances_model');
    $this->load->model('clients/models/clients_model');
  }
  
  /**
  *  Просмотр списка актов приемки по своим клиентам
  *
  */
  function index() {
    $where = array('client_acceptances.parent_id'=>null);
    $error = '';
    $product_id = $this->uri->getParam('product_id');
    $get_params = array(
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : date('Y-m-1')),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : ''),
      'client_id'   => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'type_report' => ($this->uri->getParam('type_report') == 'short' ? 'short' : 'long'),
      'product_id'  => ($product_id && @$product_id[0] ? $product_id : array()),
    );
    if($get_params['date_start']){
      $where['client_acceptances.date >='] = $get_params['date_start'];
    }
    if($get_params['date_end']){
      $where['client_acceptances.date <='] = $get_params['date_end'];
    }
    if($get_params['client_id']){
      $where['client_acceptances.client_id'] = $get_params['client_id'];
    }

    //если нет доступа к работе по всем клиентам добавляем условие
    if(!$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_allClients')){
      $where['clients.admin_id'] = $this->admin_id;
      // проверка свой ли клиент указан
      if($get_params['client_id']){
        $client = $this->clients_model->get_client(array('id'=>$get_params['client_id']));
        if(!$client){
          $error = 'Клиент не найден';
        }
        if($client['admin_id'] != $this->admin_id){
          $error = 'У вас нет прав на просмотр актов приемки для клиентов других менеджеров';
        }
      }
    }

    $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
    $limit = 100;
    $offset = $limit * ($page - 1);
    $cnt = $this->acceptances_model->get_acceptances_cnt($where, $get_params['product_id']);
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
      'prefix' => '/admin'.$this->params['path'],
      'postfix' => $postfix
    );
    $items = $this->acceptances_model->get_acceptances($limit, $offset, $where, false, $get_params['product_id']);
    $data = array(
      'title' => 'Акты приемки',
      'component_item'  => array('name' => 'acceptance', 'title' => 'акт приемки'),
      'items'           => $items,
      'get_params'      => $get_params,
      'error'           => $error,
      'pagination'      => $this->load->view('templates/pagination', $pagination_data, true),
      'form' => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] ,        
        'enctype' => '',
        'blocks' => array(
          array(
            'title'         => 'Параметры отчета',
            'fields'   => array(
              array(
                'view'    => 'fields/select',
                'title'   => 'Вид отчета:',
                'name'    => 'type_report',
                'value'   => $get_params['type_report'],
                'options' => array(
                  array(
                    'id'    => 'short',
                    'title' => 'Свернутый',
                  ),
                  array(
                    'id'    => 'long',
                    'title' => 'Расширенный',
                  )
                ),
                'onchange' => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'        => 'fields/datetime',
                'title'       => 'Дата приемки (от):',
                'name'        => 'date_start',
                'value'       => ($get_params['date_start']? date('d.m.Y',strtotime($get_params['date_start'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'        => 'fields/datetime',
                'title'       => 'Дата приемки (до):',
                'name'        => 'date_end',
                'value'       => ($get_params['date_end']? date('d.m.Y',strtotime($get_params['date_end'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'       => 'fields/select',
                'title'      => 'Поставщик:',
                'name'       => 'client_id',
                'text_field' => 'title_full',
                'value'      => $get_params['client_id'],
                'options'    => $this->clients_model->get_clients(),
                'empty'      => true,
                'onchange'   => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
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
      echo $this->load->view('../../application/components/acceptances/templates/admin_client_acceptances_tbl_'.$get_params['type_report'],$data,true);
    } else {
      return $this->render_template('templates/admin_client_acceptances', $data);
    }
  }

  /**
  * Доступ к работе с актами приемки по всем клиентам (просмотр, радактирование, удаление)
  */
  function permit_acceptance_allClients(){}

  function _render_client_acceptances_table($data){
    $data = unserialize(base64_decode($data));
    $type_report = ($data['get_params']['type_report'] == 'short' ? 'short' : 'long');
    return $this->load->view('../../application/components/acceptances/templates/admin_client_acceptances_tbl_'.$type_report,$data,true);
  }

  function _render_client_acceptance_table($data){
    $data = unserialize(base64_decode($data));
    return $this->load->view('../../application/components/acceptances/templates/admin_client_acceptance_tbl',$data,true);
  }

  /**
  *  Просмотр акта приемки по своим клиентам
  */
  function acceptance($id) {
    $item = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>(int)$id));
    if(!$item){
      show_error('Объект не найден');
    }

    //если клиент не текущего менеджера и нет доступа к работе по всем клиентам
    if($item['client_id'] && $item['client']['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_allClients')){
      show_error('У вас нет прав на просмотр актов приемки для клиентов других менеджеров');
    }

    $data = array(
      'title' => 'Акт приемки',
      'html'  => $this->load->view('../../application/components/acceptances/templates/admin_client_acceptance',array('item' => $item),TRUE),
      'back'  => $this->lang_prefix .'/admin'. $this->params['path']
    );
    return $this->render_template('admin/inner', $data);
  }
   
  /**
  * Добавление нескольких видов вторсырья в акт приемки
  */ 
  function renderProductsFields($return_type = 'array',$items = array()) {
    $result = array();
    if ($items) {
      foreach ($items as $key => $item) {
        $result[] = $this->_renderProductsField(($key==0?true:false), $item);
      }
    } else {
      $result[] = $this->_renderProductsField(($return_type=='array'?true:false));
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
          'onclick'  => 'renderFieldsProducts("/admin/acceptances/renderProductsFields/html/", this);',
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
  function _renderProductsField($label = true, $item = array()) {
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
          'form_group_class' => 'form_group_product_field form_group_w20',
        ),
        array(
          'view'  => 'fields/text',
          'title' => ($label ? 'Вес в ТТН Поставщика,&nbsp;(кг)' : ''),
          'name'  => 'weight_ttn[]',
          'value' => ($item ? $item['weight_ttn'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'  => 'fields/text',
          'title' => ($label ? 'Брутто, (кг)' : ''),
          'name'  => 'gross[]',
          'value' => ($item ? $item['gross'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'  => 'fields/text',
          'title' => ($label ? 'Упаковка, (кг)' : ''),
          'name'  => 'weight_pack[]',
          'value' => ($item ? $item['weight_pack'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'  => 'fields/text',
          'title' => ($label ? 'Засор, (%)' : ''),
          'name'  => 'weight_defect[]',
          'value' => ($item ? $item['weight_defect'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'  => 'fields/text',
          'title' => ($label ? 'Кол-во мест' : ''),
          'name'  => 'cnt_places[]',
          'value' => ($item ? $item['cnt_places'] : ''),
          'class' => 'number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'      => 'fields/text',
          'title'     => ($label ? 'Нетто, (кг)' : ''),
          'name'      => 'net[]',
          'value'     => ($item ? $item['net'] : ''),
          'onkeyup'   => 'updateAcceptanceSumProduct()',
          'class'     => 'product_field_count number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'      => 'fields/text',
          'title'     => ($label ? 'Цена, (руб.)' : ''),
          'name'      => 'price[]',
          'value'     => ($item ? $item['price'] : ''),
          'onkeyup'   => 'updateAcceptanceSumProduct()',
          'class'     => 'product_field_price number',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'  => 'fields/readonly',
          'title' => ($label ? 'Стоимость, (руб.)' : ''),
          'value' => '<div class="sum_product">'.($item ? number_format(($item['price']*$item['net']),2,'.',' ') : '0.00').'</div>',
          'num'   => ($item ? ($item['price']*$item['net']) : ''),
          'class' => 'sum_product',
          'form_group_class' => 'form_group_product_field',
        ),
        array(
          'view'    => 'fields/submit',
          'title'   => '',
          'class'   => 'btn-default '.($label ? 'form_group_product_field_btn' : 'form_group_product_field_btn_m5'),
          'icon'    => 'glyphicon-remove',
          'onclick' =>  'removeFormBlock(this,"'.($item ? '/admin/acceptances/delete_acceptance/'.$item['id'] : '').'");',
        ),
        array(
          'view'     => 'fields/submit',
          'title'    => '',
          'type'     => 'ajax',
          'class'    => 'btn-primary '.($label ? 'form_group_product_field_btn' : 'form_group_product_field_btn_m5'),
          'icon'     => 'glyphicon-plus',
          'onclick'  => 'renderFieldsProducts("/admin/acceptances/renderProductsFields/html/", this);',
          'reaction' => ''
        )
      )
    );
  }

  /**
   *  Создание акта приемки по своим клиентам
  **/  
  function create_acceptance(){
    $client_id = ($this->uri->getParam('client_id') ? mysql_prepare($this->uri->getParam('client_id')) : 0);
    $productsFields = $this->renderProductsFields();
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
          'view'  => 'fields/datetime',
          'title' => 'Дата приемки:',
          'name'  => 'date',
          'value' => date('d.m.Y'),
        ),
        array(
          'view'  => 'fields/text',
          'title' => 'Дата и номер ТН:',
          'name'  => 'date_num',
        ),
        array(
          'view'  => 'fields/text',
          'title' => 'Транспорт:',
          'name'  => 'transport',
        ),
        array(
          'view'  => 'fields/datetime',
          'title' => 'Дата и время прибытия:',
          'name'  => 'date_time',
        ),
      )
    ));
    foreach ($productsFields as $key => $productField) {
      $blocks[] = $productField;
    }
    $blocks[] = array(
      'title'   => '&nbsp;',
      'collapse'=> false,
      'fields'   => array(
        array(
          'view'     => 'fields/text',
          'title'    => 'Дополнительные расходы:',
          'name'     => 'add_expenses',
          'class'    => 'add_expenses number',
          'onkeyup'  => 'updateAcceptanceSumProduct()',
        )
      )
    );
    $blocks[] = array(
      'title'   => '',
      'collapse'=> false,
      'fields'   => array(
        array(
          'view'  => 'fields/readonly',
          'title' => 'ИТОГО:',
          'value' => '<div class="all_sum">0.00</div>',
        )
      )
    );
    $blocks[] = array(
      'title'   => '&nbsp;',
      'collapse'=> false,
      'fields'   => array(
        array(
          'view'     => 'fields/submit',
          'title'    => 'Создать акт',
          'type'     => 'ajax',
          'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
        )
      )
    );
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление акта приемки',
      'html' => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_acceptance_process/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path']
    ), TRUE);
  }
  
  function _create_acceptance_process() {
    $params = array(
      'date'          => ($this->input->post('date') ? date('Y-m-d', strtotime($this->input->post('date'))) : NULL),
      'date_num'      => htmlspecialchars(trim($this->input->post('date_num'))),
      'transport'     => htmlspecialchars(trim($this->input->post('transport'))),
      'client_id'     => ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : NULL),
      'company'       => htmlspecialchars(trim($this->input->post('company'))),
      'date_time'     => ($this->input->post('date_time') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_time'))) : NULL),
      'add_expenses'  => (float)str_replace(' ', '', $this->input->post('add_expenses')),
    );

    $errors = $this->_validate_acceptance($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->acceptances_model->create_acceptance($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    //добавляем к акту вторсырье
    $params_products = array(
      'product_id'    => $this->input->post('product_id'),
      'weight_ttn'    => $this->input->post('weight_ttn'),
      'gross'         => $this->input->post('gross'),
      'weight_pack'   => $this->input->post('weight_pack'),
      'weight_defect' => $this->input->post('weight_defect'),
      'cnt_places'    => $this->input->post('cnt_places'),
      'net'           => $this->input->post('net'),
      'price'         => $this->input->post('price'),
    );
    if(!is_array($params_products['product_id']) || !@$params_products['product_id'][0]){
      $this->acceptances_model->delete_acceptance($id);
      send_answer(array('errors' => array('Не указаны параметры вторсырья')));
    }
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        //по ключу собираем все параметры вторсырья
        $params = array(
          'parent_id'     => $id,
          'client_id'     => $params['client_id'],
          'product_id'    => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'weight_ttn'    => (float)str_replace(' ', '', $params_products['weight_ttn'][$key]),
          'gross'         => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'weight_pack'   => (float)str_replace(' ', '', $params_products['weight_pack'][$key]),
          'weight_defect' => (float)str_replace(' ', '', $params_products['weight_defect'][$key]),
          'cnt_places'    => (float)str_replace(' ', '', $params_products['cnt_places'][$key]),
          'net'           => (float)str_replace(' ', '', $params_products['net'][$key]),
          'price'         => (float)str_replace(' ', '', $params_products['price'][$key]),
        );
        if (!$this->acceptances_model->create_acceptance($params)) {
          $this->acceptances_model->delete_acceptance($id);
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья в акт')));
        }
      }
    }

    send_answer(array('redirect' => '/admin'.$this->params['path'].'acceptance/'.$id.'/'));
  }
  
  /**
  *  Редактирование акта приемки по своим клиентам
  */  
  function edit_acceptance($id) {
    $item = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }
    $blocks = array(array(
      'title'   => 'Основные параметры',
      'fields'   => array(
        array(
          'view'       => 'fields/select',
          'title'      => 'Клиент:',
          'name'       => 'client_id',
          'text_field' => 'title_full',
          'options'    => $this->clients_model->get_clients(),
          'value'      => $item['client_id'],
          'empty'      => true,
        ),
        array(
          'view'  => 'fields/datetime',
          'title' => 'Дата приемки:',
          'name'  => 'date',
          'value' => date('d.m.Y'),
          'value' => ($item['date'] ? date('d.m.Y', strtotime($item['date'])) : '')
        ),
        array(
          'view'  => 'fields/text',
          'title' => 'Дата и номер ТН:',
          'name'  => 'date_num',
          'value' => $item['date_num'],
        ),
        array(
          'view'  => 'fields/text',
          'title' => 'Транспорт:',
          'name'  => 'transport',
          'value' => $item['transport'],
        ),
        array(
          'view'  => 'fields/datetime',
          'title' => 'Дата и время прибытия:',
          'name'  => 'date_time',
          'value' => ($item['date_time'] ? date('d.m.Y H:i:s', strtotime($item['date_time'])) : '')
        ),
        // array(
        //   'view'     => 'fields/submit',
        //   'title'    => 'Сохранить',
        //   'type'     => 'ajax',
        //   'reaction' => 'reload'
        // )
      )
    ));
    $all_sum = 0;
    $productsFields = $this->renderProductsFields('array',$item['childs']);
    foreach ($productsFields as $key => $productField) {
      $blocks[] = $productField;
      foreach($productField['fields'] as $product_field){        
        if(isset($product_field['class']) && $product_field['class'] == 'sum_product' && isset($product_field['num'])){
          $all_sum += (float)$product_field['num'];
        }
      }
    }
    $all_sum -= $item['add_expenses'];
    $blocks[] = array(
      'title'   => '&nbsp;',
      'collapse'=> false,
      'fields'   => array(
        array(
          'view'     => 'fields/text',
          'title'    => 'Дополнительные расходы:',
          'name'     => 'add_expenses',
          'value'    => $item['add_expenses'],
          'class'    => 'add_expenses number',
          'onkeyup'  => 'updateAcceptanceSumProduct()',
        )
      )
    );
    $blocks[] = array(
      'title'   => '',
      'collapse'=> false,
      'fields'   => array(
        array(
          'view'  => 'fields/readonly',
          'title' => 'ИТОГО:',
          'value' => '<div class="all_sum">'.number_format($all_sum,2,'.',' ').'</div>',
        )
      )
    );
    $blocks[] = array(
      'title'    => '&nbsp;',
      'collapse' => false,
      'fields'   => array(
        array(
          'view'     => 'fields/submit',
          'title'    => 'Сохранить',
          'type'     => 'ajax',
          'reaction' => ''
        ),
        array(
          'view'     => 'fields/submit',
          'title'    => 'Сохранить и просмотреть',
          'type'     => 'ajax',
          'reaction' => '/admin'.$this->params['path'].'acceptance/'.$id.'/'
        )
      )
    );
    return $this->render_template('admin/inner', array(
      'title' => 'Карточка акта приемки <small>(ID '.$item['id'].')</small>',
      'html' => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_acceptance_process/'.$id.'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path']
    ), TRUE);
  }
  
  function _edit_acceptance_process($id) {
    $params = array(
      'date'          => ($this->input->post('date') ? date('Y-m-d', strtotime($this->input->post('date'))) : NULL),
      'date_num'      => htmlspecialchars(trim($this->input->post('date_num'))),
      'transport'     => htmlspecialchars(trim($this->input->post('transport'))),
      'client_id'     => ((int)$this->input->post('client_id') ? (int)$this->input->post('client_id') : NULL),
      'company'       => htmlspecialchars(trim($this->input->post('company'))),
      'date_time'     => ($this->input->post('date_time') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_time'))) : NULL),
      'add_expenses'  => (float)str_replace(' ', '', $this->input->post('add_expenses')),
    );

    $errors = $this->_validate_acceptance($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->acceptances_model->update_acceptance($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    //редактируем/добавляем к акту вторсырье
    $params_products = array(
      'item_id'       => $this->input->post('item_id'),
      'product_id'    => $this->input->post('product_id'),
      'weight_ttn'    => $this->input->post('weight_ttn'),
      'gross'         => $this->input->post('gross'),
      'weight_pack'   => $this->input->post('weight_pack'),
      'weight_defect' => $this->input->post('weight_defect'),
      'cnt_places'    => $this->input->post('cnt_places'),
      'net'           => $this->input->post('net'),
      'price'         => $this->input->post('price'),
    );
    if(!is_array($params_products['product_id']) || !@$params_products['product_id'][0]){
      send_answer(array('errors' => array('Не указаны параметры вторсырья')));
    }
    foreach ($params_products['product_id'] as $key => $product_id) {
      if($product_id){
        //по ключу собираем все параметры вторсырья
        $params = array(
          'parent_id'     => $id,
          'client_id'     => $params['client_id'],
          'product_id'    => (float)str_replace(' ', '', $params_products['product_id'][$key]),
          'weight_ttn'    => (float)str_replace(' ', '', $params_products['weight_ttn'][$key]),
          'gross'         => (float)str_replace(' ', '', $params_products['gross'][$key]),
          'weight_pack'   => (float)str_replace(' ', '', $params_products['weight_pack'][$key]),
          'weight_defect' => (float)str_replace(' ', '', $params_products['weight_defect'][$key]),
          'cnt_places'    => (float)str_replace(' ', '', $params_products['cnt_places'][$key]),
          'net'           => (float)str_replace(' ', '', $params_products['net'][$key]),
          'price'         => (float)str_replace(' ', '', $params_products['price'][$key]),
        );
        if ($params_products['item_id'][$key] && 
          !$this->acceptances_model->update_acceptance($params_products['item_id'][$key], $params)) {
          send_answer(array('errors' => array('Ошибка при сохранении вторсырья в акте')));
        }
        if (!$params_products['item_id'][$key] && !$this->acceptances_model->create_acceptance($params)) {
          send_answer(array('errors' => array('Ошибка при добавлении вторсырья в акт')));
        }
      }
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }
  
  function _validate_acceptance($params) {
    $errors = array();
    if (!$params['client_id'] && !$params['company']) { 
      $errors['client_id'] = 'Не указан поставщик';
      $errors['company'] = 'Не указана поставщик'; 
    }

    $client = $this->clients_model->get_client(array('id' => (int)$params['client_id']));
    if($params['client_id'] && !$client){
      $errors['client_id'] = 'Клиент не найден';
    }
    //если клиент не текущего менеджера и нет доступа к работе по всем клиентам
    if(!$params['client_id'] && $params['company'] && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_allClients')){
      $errors['company'] = 'У вас нет прав на добавление/редактирование актов приемки для клиентов других менеджеров';
    }
    if($client && $client['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_allClients')){
      $errors['client_id'] = 'У вас нет прав на добавление/редактирование актов приемки для клиентов других менеджеров';
    }

    return $errors;
  }

  /**
  *  Отправление email с актом приемки по своим клиентам
  */
  function client_acceptance_email($id) {
    $item = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    //если клиент не текущего менеджера и нет доступа к работе по всем клиентам
    if($item['client_id'] && $item['client']['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_allClients')){
      show_error('У вас нет прав на работу с актами приемки для клиентов других менеджеров');
    }

    if($item['client_id']){
      $item['email'] = $item['client']['email'];
    }

    return $this->render_template('templates/admin_client_acceptance_email', array(
      'title' => 'Акт приемки',
      'html'  => $this->view->render_fields(array(
        array(
          'view'  => 'fields/text',
          'title' => 'Тема письма:',
          'name'  => 'subject',
          'value' => 'Акт приемки. '.rus_date($item['date'],'d m Y г.'),
        ),
        array(
          'view'    => 'fields/readonly',
          'title'   => 'Текст письма:',
          'name'    => 'text',
          'value'   => $this->load->view('../../application/components/acceptances/templates/admin_client_acceptance_tbl',array('item'  => $item),TRUE),
          'toolbar' => ''
        )
      )
      ),
      'item'  => $item,
      'emails'=> $this->acceptances_model->get_acceptance_emails(array('acceptance_id'=>$item['id']))
    ));
  }

  /**
  *  Отправление email с актом приемки
  *  @params $id - id акта приемки
  */
  function _client_acceptance_email($id) {
    $item = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }

    //если клиент не текущего менеджера и нет доступа к работе по всем клиентам
    if($item['client_id'] && $item['client']['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_allClients')){
      send_answer(array('errors' => array('У вас нет прав на работу с актами приемки для клиентов других менеджеров')));
    }

    $from = $this->input->post('from');
    if (!preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $from)) { 
      send_answer(array('errors' => array('Некорректный еmail отправителя')));
    }
    $to = explode(',', $this->input->post('to'));
    foreach ($to as $key => $email) {
      $email = trim($email);
      if (!preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $email)) { 
        send_answer(array('errors' => array('Некорректный еmail получателя - "'.$email.'"')));
      }
    }
    $subject = htmlspecialchars(trim($this->input->post('subject')));
    $message = $this->load->view('../../application/components/acceptances/templates/admin_client_acceptance_tbl',array('item'  => $item),TRUE);
    foreach ($to as $key => $email) {
      $email = trim($email);
      if(!send_mail($from, $email, $subject, $message)){
        send_answer(array('errors' => array('Не удалось отправить сообщение на email - "'.$email.'"')));
      }
    }
    $params = array(
      'admin_id'     => $this->admin_id,
      'acceptance_id'=> $item['id'],
      'from'         => $from,
      'to'           => implode(',', $to),
      'subject'      => $subject,
      'message'      => $message 
    );
    if(!$this->acceptances_model->create_acceptance_email($params)){
      send_answer(array('errors' => array('Сообщение успешно отправлено. Не удалось сохранить письмо в истории')));
    }

    send_answer(array('messages' => array('Сообщение успешно отправлено')));
  }

  /**
   * Удаление акта приемки по своим клиентам
  **/
  function delete_acceptance($id) {
    $item = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>(int)$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }

    //если клиент не текущего менеджера и нет доступа к работе по всем клиентам
    if($item['client_id'] && $item['client']['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_allClients')){
      send_answer(array('errors' => array('У вас нет прав на работу с актами приемки для клиентов других менеджеров')));
    }

    if (!$this->acceptances_model->delete_acceptance((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }
  
}