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
  function index($render_table = false) {
    $get_params = array(
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d',strtotime($this->uri->getParam('date_start'))) : date('Y-m-1')),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d',strtotime($this->uri->getParam('date_end'))) : ''),
      'client_id'   => ((int)$this->uri->getParam('client_id') ? (int)$this->uri->getParam('client_id') : ''),
      'client_child_id'   => ((int)$this->uri->getParam('client_child_id') ? (int)$this->uri->getParam('client_child_id') : '')
    );

    $data = array(
      'title'           => 'Акты приемки. Бухгалтерия.',
      'component_item'  => array('name' => 'acceptance_payment', 'title' => 'Бухгалтерия'),
      'cashbox'         => $this->main_model->get_param('cashbox', 1, 'cashbox_0'),
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
                'id'            => 'btn-form',
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

      $where = 'pr_client_acceptances.parent_id IS NULL';
      if($get_params['date_start']){
        $where .= ' AND pr_client_acceptances.date >= "' . $get_params['date_start'].'"';
      }
      if($get_params['date_end']){
        $where .= ' AND pr_client_acceptances.date <= "' . $get_params['date_end'].'"';
      }
      if($get_params['client_id']){
        $where .= ' AND pr_client_acceptances.client_id = ' . $get_params['client_id'];
      }
      if($get_params['client_child_id']){
        $where .= ' AND (pr_client_acceptances.client_child_id = ' . $get_params['client_child_id'] . ' OR pr_client_acceptances.client_id = ' . $get_params['client_child_id'] . ')';
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
      $items = $this->acceptance_payments_model->get_acceptance_payments($limit, $offset, $where, false);
      $data = array_merge($data, array(
        'items'           => $items,
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
  *  Редактирование оплаты акта приемки по своим клиентам
  */  
  function edit_acceptance_payment($acceptance_id) {
    $item = $this->acceptance_payments_model->get_acceptance_payment(array('client_acceptance_payments.acceptance_id'=>(int)$acceptance_id));
    if(!$item){
      show_error('Объект не найден');
    }
    $acceptance = $this->acceptances_model->get_acceptance(array('pr_client_acceptances.id'=>(int)$acceptance_id));
    if(!$item){
      show_error('Акт не найден');
    }

    $blocks = array(
      array(
        'title'         => 'Акт приемки',
        'fields'        => array(array(
          'view'      => 'fields/readonly_value',
          'title'     => '',
          'value'     => $this->load->view('../../application/components/acceptances/templates/admin_client_acceptance_tbl_short',array('item' => $acceptance),TRUE),
        )),
        'aria-expanded' => true
      ),
      array(
      'title'   => 'Параметры оплаты',
      'fields'   => array(
        array(
          'view'     => 'fields/'.($acceptance['status_id'] < 10 ? 'hidden' : 'readonly'),
          'title'    => 'Статус',
          'value'    => 'Оплачено'
        ),
        array(
          'view'     => 'fields/datetime',
          'title'    => 'Дата оплаты:',
          'name'     => 'date',
          'disabled' => ($acceptance['status_id'] > 4 ? true : false),
          'value'    => (strtotime($item['date']) ? date('d.m.Y H:i:s', strtotime($item['date'])) : '')
        ),
        array(
          'view'       => 'fields/select',
          'title'      => 'Способ оплаты:',
          'name'       => 'method',
          'text_field' => 'title',
          'value_field'=> 'value',
          'options'    => array(array('title'=>'Наличный расчет','value'=>'cash'),array('title'=>'Безналичный расчет','value'=>'card')),
          'value'      => $item['method'],
        ),
        array(
          'view'     => 'fields/text',
          'title'    => '% скидки:',
          'name'     => 'sale_percent',
          'value'    => $item['sale_percent'],
        ),
        array(
          'view'     => 'fields/textarea',
          'title'    => 'Примечания',
          'name'     => 'comment',
          'value'    => $item['comment'],
        ),
        array(
          'view'     => 'fields/'.($acceptance['status_id'] < 10 ? 'checkbox' : 'hidden'),
          'title'    => 'Оплачено:',
          'name'     => 'pay'
        ),
        array(
          'view'     => 'fields/'.($acceptance['status_id'] < 10 ? 'submit' : 'hidden'),
          'title'    => 'Сохранить',
          'type'     => 'ajax',
          'reaction' => ''
        )
      )
    ));

    return $this->render_template('admin/inner', array(
      'title' => 'Настройки оплаты акта приемки <small>(ID '.$item['id'].')</small>',
      'block_title_btn' => array(),
      'html' => $this->view->render_form(array(
        'view'   => 'forms/default',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_acceptance_payment_process/'.$item['id'].'/',
        'blocks' => $blocks
      ))
    ), TRUE);
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
      'date'         => ($this->input->post('date') ? date('Y-m-d H:i:s', strtotime($this->input->post('date'))) : ''),
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

    // если оплачено и наличные, прибавляем в кассу сумму
    if($this->input->post('pay') && $params['method'] == 'cash'){
      // сумма с вычетом доп.стоимости акта
      $item['sum'] = $item['sum'] - $item['add_expenses'];
      // учитываем скидку
      if($params['method'] == 'cash' && $params['sale_percent']){
        $item['sum'] = $item['sum'] - $item['sum']*($params['sale_percent']/100);
      }    
      $cashbox = $this->main_model->get_param('cashbox', 1, 'cashbox_0');
      $cashbox = array(array(
        'cashbox' => (float)@$cashbox['value'] + $item['sum']
      ));
      if (!$this->main_model->set_params('cashbox', 1, $cashbox)) {
        send_answer(array('errors' => array('Не удалось сохранить изменения в кассе')));
      }
    }

    // меняем статус если оплачено
    if($this->input->post('pay') && !$this->acceptances_model->update_acceptance($item['acceptance_id'], array('status_id' => 10))){
      send_answer(array('errors' => array('Ошибка при изменении статуса')));
    }

    // меняем статус если не оплачено и указана дата оплаты
    if($params['date'] && !$this->acceptances_model->update_acceptance($item['acceptance_id'], array('status_id' => 5))){
      send_answer(array('errors' => array('Ошибка при изменении статуса')));
    }
    
    send_answer(array('success' => array('Изменения успешно сохранены')));
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

    if (!$this->acceptance_payments_model->delete_acceptance_payment((int)$id)){
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }
}