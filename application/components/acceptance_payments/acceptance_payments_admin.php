<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acceptance_payments_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('acceptances/models/acceptances_model');
    $this->load->model('acceptance_payments/models/acceptance_payments_model');
  }
   
  /** 
  * Просмотр списка актов приемки по своим клиентам
  */
  function index($render_table = false, $render_table_email = false) {
    $get_params = array(
      'tm_start'  => ($this->uri->getParam('tm_start') ? date('Y-m-d',strtotime($this->uri->getParam('tm_start'))) : date('Y-m-1')),
      'tm_end'    => ($this->uri->getParam('tm_end') ? date('Y-m-d',strtotime($this->uri->getParam('tm_end'))) : ''),
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : ''),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : ''),
      'client_id'   => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'client_child_id'   => ((int)$this->uri->getParam('client_child_id') ? (int)$this->uri->getParam('client_child_id') : '')
    );

    $data = array(
      'title'               => 'Акты приемки. Бухгалтерия.',
      'component_item'      => array('name' => 'acceptance_payment', 'title' => 'Бухгалтерия'),
      'cashbox'             => $this->main_model->get_param('cashbox', 1, 'cashbox_0'),
      'render_table_email'  => $render_table_email,
      'form' => $this->view->render_form(array(
        'method' => 'GET',
        'id'     => 'acceptance_payments_report',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'?ajax=1' ,
        'enctype' => '',
        'blocks' => array(
          array(
            'title'         => 'Параметры отчета',
            'fields'   => array(
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата добавления в бухгалтерию (от):',
                'name'        => 'tm_start',
                'value'       => ($get_params['tm_start']? date('d.m.Y',strtotime($get_params['tm_start'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата добавления в бухгалтерию (до):',
                'name'        => 'tm_end',
                'value'       => ($get_params['tm_end']? date('d.m.Y',strtotime($get_params['tm_end'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата приемки (от):',
                'name'        => 'date_start',
                'value'       => ($get_params['date_start']? date('d.m.Y',strtotime($get_params['date_start'])) : ''),
                'onchange1'    => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'        => 'fields/date',
                'title'       => 'Дата приемки (до):',
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
                'options'    => $this->clients_model->get_clients(0,0,array('parent_id' => null)),
                'empty'      => true,
                'onchange'   => "changeClientChilds('submit_form(this, handle_ajaxResultAllData)'); submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'       => 'fields/select',
                'title'      => 'Компания:',
                'id'         => 'client_child_id',
                'name'       => 'client_child_id',
                'text_field' => 'title_full',
                'value'      => $get_params['client_child_id'],
                'options'    => $this->clients_model->get_clients(0,0,($get_params['client_id'] ? 'parent_id = ' . $get_params['client_id'] : 'parent_id IS NOT NULL')),
                'empty'      => true,
                'onchange'   => "submit_form(this, handle_ajaxResultAllData);",
              ),
              array(
                'view'          => 'fields/submit',
                'id'            => 'btnFormAcceptance_payments_report',
                'title'         => 'Сформировать',
                'type'          => 'ajax',
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
      $error = '';

      $where = 'pr_client_acceptance_payments.acceptance_parent_id IS NULL';
      if($get_params['tm_start']){
        $where .= ' AND pr_client_acceptance_payments.tm >= "' . $get_params['tm_start'].'"';
      }
      if($get_params['tm_end']){
        $where .= ' AND pr_client_acceptance_payments.tm <= "' . $get_params['tm_end'].'"';
      }
      if($get_params['date_start']){
        $where .= ' AND pr_client_acceptance_payments.date >= "' . $get_params['date_start'].'"';
      }
      if($get_params['date_end']){
        $where .= ' AND pr_client_acceptance_payments.date <= "' . $get_params['date_end'].'"';
      }
      if($get_params['client_id']){
        $where .= ' AND pr_client_acceptance_payments.client_id = ' . $get_params['client_id'];
      }
      if($get_params['client_child_id']){
        $where .= ' AND (pr_client_acceptance_payments.client_child_id = ' . $get_params['client_child_id'] . ' OR pr_client_acceptance_payments.client_id = ' . $get_params['client_child_id'] . ')';
      }

      //если нет доступа к работе по всем клиентам добавляем условие
      if(!$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_payments_allClients')){
        $where .= ' AND pr_clients.admin_id =' . $this->admin_id;
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
      $cnt = $this->acceptance_payments_model->get_acceptance_payments_cnt($where);
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
      $items = $this->acceptance_payments_model->get_acceptance_payments(($render_table_email ? 0 : $limit), ($render_table_email ? 0 : $offset), $where, false);
      // группируем строки по parent_id
      $new_items = array();
      foreach ($items as $key => $item) {
        if(!isset($new_items[$item['parent_id']])){
          $new_items[$item['parent_id']] = array(
            'comment' => $item['comment'],
            'cash' => array(),
            'card' => array()
          );
        }
        $new_items[$item['parent_id']][$item['method']][] = $item;
      }
      // var_dump($new_items);exit;

      $data = array_merge($data, array(
        'items'           => $new_items,
        'postfix'         => $postfix,
        'error'           => $error,
        'pagination'      => $this->load->view('templates/pagination', $pagination_data, true),
      ));
     
      if($render_table){
        return $this->load->view('../../application/components/acceptance_payments/templates/admin_items_tbl',$data,true);
      } else if($this->uri->getParam('ajax') == 1){
        send_answer(array(
          'page'  => (isset($page) ? $page : 1),
          'pages' => (isset($pages) ? count($pages) : 0),
          'html' => $this->load->view('../../application/components/acceptance_payments/templates/admin_items_tbl',$data,true)
          ));
      }
    }

    return $this->render_template('templates/admin_items', $data);
  }

  /**
  * Доступ к работе с оплатой по актам приемки по всем клиентам (просмотр, радактирование, удаление)
  */
  function permit_acceptance_payments_allClients(){}

  /**
  *  Редактирование суммы в кассе
  */  
  function edit_cashbox() {
    $item = $this->main_model->get_param('cashbox', 1, 'cashbox_0');

    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование региона',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_cashbox/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Сумма:',
                'name'      => 'cashbox',
                'value'     => (float)@$item['value']
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      ))
    ), TRUE);    
    
  } 

  function _edit_cashbox() {
    $params = array(array(
      'cashbox' => (float)@str_replace(',', '.', $this->input->post('cashbox'))
    ));

    if (!$this->main_model->set_params('cashbox', 1, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить')));
    }

    send_answer();
  }

  /**
  *  Редактирование оплаты акта приемки по своим клиентам для модального окна
  */
  function edit_acceptance_paymentModal($id){
    $acceptance_payment = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.id'=>(int)$id));
    if(!$acceptance_payment){
      show_error('Объект не найден');
    }

    echo $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_acceptance_payment_process/'.$acceptance_payment['id'].'/',
        'blocks' => array(
          array(
            'title'   => 'Акт приемки',
            'fields'  => array(array(
              'view'      => 'fields/readonly_value',
              'title'     => '',
              'value'     => $this->load->view('../../application/components/acceptance_payments/templates/admin_client_acceptance_tbl_short',array('item' => $acceptance_payment),TRUE),
            )),
            'aria-expanded' => true
          ),
          array(
            'title'   => 'Параметры оплаты',
            'fields'   => array(
              array(
                'view'   => 'fields/hidden',
                'name'   => 'modal',
                'value'  => 1
              ),
              array(
                'view'     => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'hidden' : 'readonly'),
                'title'    => 'Статус',
                'value'    => 'Оплачено'
              ),
              array(
                'view'     => 'fields/datetime',
                'title'    => 'Дата оплаты:',
                'name'     => 'date_payment',
                'id'       => 'date_payment'.$acceptance_payment['id'],
                'disabled' => ($acceptance_payment['status_id'] == 10 ? true : false),
                'value'    => ($acceptance_payment['date_payment'] ? date('d.m.Y H:i:s', strtotime($acceptance_payment['date_payment'])) : '')
              ),
              array(
                'view'          => 'fields/select',
                'title'         => 'Способ оплаты:',
                'name'          => 'method',
                'id'            => 'method'.$acceptance_payment['id'],
                'text_field'    => 'title',
                'value_field'   => 'value',
                'chosen_disable'=> true,
                'options'       => array(array('title'=>'Наличный расчет','value'=>'cash'),array('title'=>'Безналичный расчет','value'=>'card')),
                'disabled'      => ($acceptance_payment['status_id'] == 10 ? true : false),
                'value'         => $acceptance_payment['method'],
              ),
              array(
                'view'     => 'fields/text',
                'title'    => '% скидки:',
                'name'     => 'sale_percent',
                'disabled' => ($acceptance_payment['status_id'] == 10 ? true : false),
                'value'    => $acceptance_payment['sale_percent'],
              ),
              array(
                'view'     => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'checkbox' : 'hidden'),
                'title'    => 'Оплачено:',
                'id'       => 'pay'.$acceptance_payment['id'],
                'name'     => 'pay',
                'onchange' => 'setElMethodPayCash(this)'
              ),
              array(
                'view'              => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'select' : 'hidden'),
                'title'             => 'Метод оплаты:',
                'name'              => 'method_pay_cash',
                'id'                => 'method_pay_cash',
                'form_group_class'  => 'form_group_method_pay_cash',
                'text_field'        => 'title',
                'value_field'       => 'value',
                'options'           => array(
                  array('title'=>'Прибавить в кассу','value'=>'plus'),
                  array('title'=>'Вычесть из кассы','value'=>'minus')),
                'value'          => 'plus',
                'chosen_disable' => true,
                'description'    => 'Используется при наличном расчете в момент когда активна галочка "Оплачено"',
              ),
              array(
                'view'     => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'submit' : 'hidden'),
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => ''
              )
            )
          )
        )
      ));
  }

  /**
  *  Редактирование оплаты акта приемки по своим клиентам
  */
  function edit_acceptance_payment($id) {
    $item = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.id'=>(int)$id), false);
    if(!$item){
      show_error('Объект не найден');
    }
    // массив с актами прикрепленными к данной оплате
    $item['acceptances'] = $this->acceptance_payments_model->get_acceptance_payments(0,0,array('client_acceptance_payments.parent_id'=>(int)$item['id']),false,true);
    // var_dump($item['acceptances']);exit();
    $html = '';
    $blocks = array();
    // примечание общее на строку оплаты
    $html .= $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_acceptance_payment_process/'.$item['id'].'/',
        'blocks' => array(
          array(
            'title'   => '',
            'fields'   => array(
              array(
                'view'     => 'fields/textarea',
                'title'    => 'Примечания',
                'name'     => 'comment',
                'value'    => $item['comment'],
              ),
              array(
                'view'     => 'fields/submit',
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => ''
              )
            )
          )
        )
      ));
    // на каждый акт своя форма
    foreach ($item['acceptances'] as $key => $acceptance_payment) {
      $html .= $this->view->render_form(array(
        'view'   => 'forms/default',
        'id'     => 'formAcceptancePayment'.$acceptance_payment['id'],
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_acceptance_payment_process/'.$acceptance_payment['id'].'/',
        'blocks' => array(
          array(
            'title'   => 'Акт приемки',
            'fields'  => array(array(
              'view'      => 'fields/readonly_value',
              'title'     => '',
              'value'     => $this->load->view('../../application/components/acceptance_payments/templates/admin_client_acceptance_tbl_short',array('item' => $acceptance_payment),TRUE),
            )),
            'aria-expanded' => true
          ),
          array(
            'title'   => 'Параметры оплаты',
            'fields'   => array(
              array(
                'view'     => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'hidden' : 'readonly'),
                'title'    => 'Статус',
                'value'    => 'Оплачено'
              ),
              array(
                'view'     => 'fields/datetime',
                'title'    => 'Дата оплаты:',
                'name'     => 'date_payment',
                'id'       => 'date_payment'.$acceptance_payment['id'],
                'disabled' => ($acceptance_payment['status_id'] > 4 ? true : false),
                'value'    => ($acceptance_payment['date_payment'] ? date('d.m.Y H:i:s', strtotime($acceptance_payment['date_payment'])) : '')
              ),
              array(
                'view'       => 'fields/select',
                'title'      => 'Способ оплаты:',
                'name'       => 'method',
                'id'         => 'method'.$acceptance_payment['id'],
                'text_field' => 'title',
                'value_field'=> 'value',
                'options'    => array(
                  array('title'=>'Наличный расчет','value'=>'cash'),
                  array('title'=>'Безналичный расчет','value'=>'card')),
                'value'      => $acceptance_payment['method'],
              ),
              array(
                'view'     => 'fields/text',
                'title'    => '% скидки:',
                'name'     => 'sale_percent',
                'onkeyup'  => "submit_form(this);",
                'value'    => $acceptance_payment['sale_percent'],
              ),
              array(
                'view'     => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'checkbox' : 'hidden'),
                'title'    => 'Оплачено:',
                'id'       => 'pay'.$acceptance_payment['id'],
                'name'     => 'pay',
                'onchange' => 'setElMethodPayCash(this)'
              ),
              array(
                'view'              => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'select' : 'hidden'),
                'title'             => 'Метод оплаты:',
                'name'              => 'method_pay_cash',
                'id'                => 'method_pay_cash',
                'form_group_class'  => 'form_group_method_pay_cash',
                'text_field'        => 'title',
                'value_field'       => 'value',
                'options'           => array(
                  array('title'=>'Прибавить в кассу','value'=>'plus'),
                  array('title'=>'Вычесть из кассы','value'=>'minus')),
                'value'          => 'plus',
                'chosen_disable' => true,
                'description'    => 'Используется при наличном расчете в момент когда активна галочка "Оплачено"',
              ),
              array(
                'view'     => 'fields/'.($acceptance_payment['status_id'] < 10 ? 'submit' : 'hidden'),
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      ));
    }
    return $this->render_template('admin/inner', array(
      'title' => 'Настройки оплаты акта приемки <small>(ID '.$item['id'].')</small>',
      'block_title_btn' => $this->load->view('fields/submit', 
        array('vars' => array(
          'title'   => 'Удалить карточку оплаты',
          'class'   => 'btn-default',
          'icon'    => 'glyphicon-remove',
          'onclick' =>  'return send_confirm("Вы уверены, что хотите удалить карточку оплаты - ID'.$item['id'].'?","'.$this->lang_prefix .'/admin'. $this->params['path'] .'delete_acceptance_payment/'.$id.'/", {},"/admin/acceptance_payments/" );'
        )), true),
      'html' => $html
    ), TRUE);
  }
  
  function edit_acceptancePaymentParent(){
    $id = (int)$this->input->post('id');
    $parent_id = (int)$this->input->post('parent_id');

    $item = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.id'=>$id));
    if(!$item){
      send_answer(array('errors' => array('Акт не найден.')));
    }
    $parent = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.id'=>$parent_id));
    if(!$parent){
      send_answer(array('errors' => array('Родительский акт не найден.')));
    }

    if (!$this->acceptance_payments_model->update_acceptance_payment($id, array('parent_id'=>$parent_id,'tm'=>$parent['tm']))) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    send_answer();
  }

  function _edit_acceptance_payment_process($id) {
    $item = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    if($item['status_id'] >= 10){
      send_answer(array('errors' => array('Акт оплачен. Редактирование невозможно.')));
    }
    // проверяем права доступа к акту приемки
    if($item['client_admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_payments_allClients')){
      send_answer(array('errors' => array('У вас нет прав на редактирование оплаты актов приемки для клиентов других менеджеров')));
    }

    $params = array(
      'date_payment'  => ($this->input->post('date_payment') ? date('Y-m-d H:i:s', strtotime($this->input->post('date_payment'))) : null),
      'method'       => htmlspecialchars(trim($this->input->post('method'))),
      'sale_percent' => (int)$this->input->post('sale_percent'),
      'comment'      => htmlspecialchars(trim($this->input->post('comment'))),
    );
    if($params['method'] != 'cash' && $params['sale_percent']){
      send_answer(array('errors' => array('При безналичном расчете скидка не предоставляется')));
    }
    
    if (!$this->acceptance_payments_model->update_acceptance_payment($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    // учитываем скидку
    if($params['method'] == 'cash' && $params['sale_percent']){
      $item['sum'] = $item['sumAcceptance'] - $item['sumAcceptance']*($params['sale_percent']/100);
    } 

    // если оплачено и наличные, прибавляем в кассу сумму
    if($this->input->post('pay') && $params['method'] == 'cash'){   
      $cashbox = $this->main_model->get_param('cashbox', 1, 'cashbox_0');
      if($this->input->post('method_pay_cash') == 'minus'){
        $cashbox = array(array(
          'cashbox' => (float)@$cashbox['value'] - $item['sum']
        ));
      } else {
        $cashbox = array(array(
          'cashbox' => (float)@$cashbox['value'] + $item['sum']
        ));        
      }
      if (!$this->main_model->set_params('cashbox', 1, $cashbox)) {
        send_answer(array('errors' => array('Не удалось сохранить изменения в кассе')));
      }
    }

    // меняем статус у строки оплаты если оплачено
    if($this->input->post('pay') && !$this->acceptance_payments_model->update_acceptance_payment($id, array('status_id' => 10))){
      send_answer(array('errors' => array('Ошибка при изменении статуса')));
    }

    // меняем статус у строки оплаты если не оплачено и указана дата оплаты
    if(!$this->input->post('pay') && $params['date_payment'] && !$this->acceptance_payments_model->update_acceptance_payment($id, array('status_id' => 5))){
      send_answer(array('errors' => array('Ошибка при изменении статуса')));
    }

    // акт приемки
    $acceptance = $this->acceptances_model->get_acceptance(array('pr_client_acceptances.id'=>$item['acceptance_id'],'pr_client_acceptances.client_id'=>$item['client_id'],'pr_client_acceptances.client_child_id'=>$item['client_child_id']));
    if($acceptance){
      // меняем параметр auto у акта приемки, необходимо в случае изменения прихода, чтобы изменился цвет строки
      if(!$this->acceptances_model->update_acceptance($acceptance['id'], array('auto' => 0))){
        send_answer(array('errors' => array('Ошибка при изменении статуса акта приемки')));
      }

      // меняем статус у акта приемки если оплачено
      if($this->input->post('pay') && !$this->acceptances_model->update_acceptance($acceptance['id'], array('status_id' => 10))){
        send_answer(array('errors' => array('Ошибка при изменении статуса')));
      }

      // меняем статус у акта приемки если не оплачено и указана дата оплаты
      if(!$this->input->post('pay') && $params['date_payment'] && !$this->acceptances_model->update_acceptance($acceptance['id'], array('status_id' => 5))){
        send_answer(array('errors' => array('Ошибка при изменении статуса')));
      }
    }
    
    if($this->input->post('modal')){
      send_answer(array('success' => array('function'=>'setAcceptancePaymentModal','item'=>$item)));
    }
    if($params['method'] == 'cash' && $params['sale_percent']){
      send_answer(array('success' => array('function'=>'setAcceptancePaymentSum','item'=>$item)));
    }
    send_answer(array('success' => array('Изменения успешно сохранены')));
  }
  
  /**
  * Смена статуса оплаты и акта приемки
  */
  function _set_status_acceptance_payment($id, $status_id){
    $item = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    if($item['status_id'] >= 10){
      send_answer(array('errors' => array('Акт оплачен. Редактирование невозможно.')));
    }
    // проверяем права доступа к акту приемки
    if($item['client_admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_payments_allClients')){
      send_answer(array('errors' => array('У вас нет прав на редактирование оплаты актов приемки для клиентов других менеджеров')));
    }

    // меняем статус у строки оплаты
    if(!$this->acceptance_payments_model->update_acceptance_payment($id, array('status_id' => $status_id))){
      send_answer(array('errors' => array('Ошибка при изменении статуса')));
    }

    // если оплачено и наличные, прибавляем в кассу сумму
    if($status_id == 10 && $item['method'] == 'cash'){
      // учитываем скидку
      if($item['method'] == 'cash' && $item['sale_percent']){
        $item['sum'] = $item['sum'] - $item['sum']*($item['sale_percent']/100);
      }    
      $cashbox = $this->main_model->get_param('cashbox', 1, 'cashbox_0');
      if($this->input->post('method_pay_cash') == 'minus'){
        $cashbox = array(array(
          'cashbox' => (float)@$cashbox['value'] - $item['sum']
        ));
      } else {
        $cashbox = array(array(
          'cashbox' => (float)@$cashbox['value'] + $item['sum']
        ));        
      }
      if (!$this->main_model->set_params('cashbox', 1, $cashbox)) {
        send_answer(array('errors' => array('Не удалось сохранить изменения в кассе')));
      }
    }

    // акт приемки
    $acceptance = $this->acceptances_model->get_acceptance(array('pr_client_acceptances.id'=>$item['acceptance_id'],'pr_client_acceptances.client_id'=>$item['client_id'],'pr_client_acceptances.client_child_id'=>$item['client_child_id']));
    if($acceptance){
      // меняем статус у акта приемки
      if(!$this->acceptances_model->update_acceptance($acceptance['id'], array('status_id' => $status_id))){
        send_answer(array('errors' => array('Ошибка при изменении статуса')));
      }
    }

    send_answer(array('success' => array('function'=>'setAcceptancePaymentModal','item'=>$item)));
  }

  /**
   * Удаление оплаты акта приемки по своим клиентам
  **/
  function delete_acceptance_payment($id) {
    $item = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.id'=>(int)$id));
    if(!$item){
      send_answer(array('errors' => array('Объект не найден')));
    }

    //если клиент не текущего менеджера и нет доступа к работе по всем клиентам
    if($item['client_admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'permit_acceptance_payments_allClients')){
      send_answer(array('errors' => array('У вас нет прав на редактирование оплаты актов приемки для клиентов других менеджеров')));
    }
    // если статус у акта "Отправлено в бухгалтерию" меняем на статус в обработке
    $acceptance = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>(int)$item['acceptance_id']));
    if($acceptance && $acceptance['status_id'] == 4){
      // если акт отправлен по email, то статус 3
      $acceptance_emails = $this->acceptances_model->get_acceptance_emails(array('acceptance_id'=>(int)$item['acceptance_id']));
      if($acceptance_emails){
        $this->acceptances_model->update_acceptance($acceptance['id'], array('status_id' => 3));
      } else{
        // статус в обработке
        $this->acceptances_model->update_acceptance($acceptance['id'], array('status_id' => 2));
      }
    }

    if (!$this->acceptance_payments_model->delete_acceptance_payment((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    // удаляем все родительские строки, у которых нет дочерних
    $this->acceptance_payments_model->delete_acceptances_empty();

    send_answer();
  }

  function send_acceptances_payment_email(){
    $message = $this->index(true,true);

    return $this->render_template('templates/admin_items_email', 
      array(
        'title' => 'Отчет по бухгалтерии',
        'emails'=> $this->acceptance_payments_model->get_acceptance_payments_emails(),
        'html'  => $this->view->render_fields(array(
          array(
            'view'        => 'fields/readonly',
            'title'       => 'От кого:',
            'value'       => '<h6>info@ekoprozess.isnet.ru</h6>',
          ),
          array(
            'view'        => 'fields/hidden',
            'name'        => 'from',
            'title'       => 'От кого:',
            'value'       => 'info@ekoprozess.isnet.ru',
          ),
          array(
            'view'        => 'fields/text',
            'title'       => 'Кому:',
            'name'        => 'to',
          ),
          array(
            'view'  => 'fields/text',
            'title' => 'Тема письма:',
            'name'  => 'subject',
            'value' => 'Отчет. ',
          ),
          array(
            'view'    => 'fields/editor',
            'title'   => 'Текст письма:',
            'id'      => 'message_acceptances_payment_email',
            'name'    => 'message',
            'value'   => $message,
            // 'toolbar' => 'Full',
            'height' => '500',
          )
        )),
      )
    );
  }

  function _send_acceptances_payment_email(){
    $from = $this->input->post('from');
    if (!preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $from)) { 
      send_answer(array('errors' => array('Некорректный еmail отправителя '.$from)));
    }
    $to = explode(',', $this->input->post('to'));
    foreach ($to as $key => $email) {
      $email = htmlspecialchars(trim($email));
      if (!preg_match('/@{1}/', $email)) { 
        send_answer(array('errors' => array('Некорректный еmail получателя - "'.$email.'"')));
      }
    }
    $subject = htmlspecialchars(trim($this->input->post('subject')));
    $message = $this->input->post('message');

    foreach ($to as $key => $email) {
      $email = trim($email);
      if(!send_mail($from, $email, $subject, $message)){
        send_answer(array('errors' => array('Не удалось отправить сообщение на email - "'.$email.'"')));
      }
    }
    $params = array(
      'admin_id'     => $this->admin_id,
      'from'         => $from,
      'to'           => implode(',', $to),
      'subject'      => $subject,
      'message'      => $message 
    );
    if(!$this->acceptance_payments_model->create_acceptance_payments_email($params)){
      send_answer(array('errors' => array('Сообщение успешно отправлено. Не удалось сохранить письмо в истории')));
    }

    send_answer(array('messages' => array('Сообщение успешно отправлено')));
  }

  /*
  * Удаление всей истории писем из раздела Бухгалтерия
  **/
  function delete_acceptances_payments_emails(){
    if(!$this->acceptance_payments_model->delete_acceptances_payments_emails('id IS NOT NULL')){
      send_answer(array('errors' => array('Не удалось удалить историю')));
    };
    send_answer();
  }
}