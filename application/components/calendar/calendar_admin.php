<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('calendar/models/calendar_model');
  }

  function index($page = 1) {

    return $this->render_template('templates/admin_index', array());
  }
     
  /**
   *  Добавление события
  **/  
  function create_event() {
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление события',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_event_process/',
        'blocks' => array(
          array(
            'title'   => '',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'value'     => ($this->uri->getParam('title') ? $this->uri->getParam('title') : ''),
                'maxlength' => 256
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Контакты:',
                'name'      => 'description',
                'value'     => ($this->uri->getParam('description') ? $this->uri->getParam('description') : ''),
                'maxlength' => 1000,
                'rows'      => 2
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Описание события:',
                'name'      => 'event',
                'value'     => ($this->uri->getParam('event') ? $this->uri->getParam('event') : ''),
                'maxlength' => 1000,
                'rows'      => 2
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Результат:',
                'name'      => 'result',
                'value'     => ($this->uri->getParam('result') ? $this->uri->getParam('result') : ''),
                'maxlength' => 1000,
                'rows'      => 2
              ),
              array(
                'view'      => 'fields/hidden',
                'title'     => 'Клиент:',
                'name'      => 'client_id',
                'value'     => ($this->uri->getParam('client_id') ? $this->uri->getParam('client_id') : ''),
              ),
              array(
                'view'  => 'fields/datetime',
                'title' => 'Начало:',
                'name'  => 'start',
                'value' => ($this->uri->getParam('start') ? date('d.m.Y H:i', strtotime($this->uri->getParam('start'))) : '')
              ),
              array(
                'view'  => 'fields/hidden',
                'title' => 'Окончание:',
                'name'  => 'end',
                'value' => ($this->uri->getParam('end') ? date('d.m.Y H:i', strtotime($this->uri->getParam('end'))) : '')
              ),
              array(
                'view'    => 'fields/hidden',
                'title'   => 'Весь день',
                'name'    => 'allDay',
                'value'   => ($this->uri->getParam('allDay') ? 1 : 0),
                'checked' => ($this->uri->getParam('allDay') ? 1 : 0)
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Выполнено',
                'name'    => 'check',
                'checked' => ($this->uri->getParam('check') ? 1 : 0)
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'class'    => 'hidden',
                'type'     => 'ajax',
                'reaction' => ''
              )
            )
          )
        )
      )),
      'back' => ''
    ), FALSE);
  }
  
  function _create_event_process() {
    $params = array(
      'admin_id'    => $this->admin_id,
      'client_id'   => ($this->input->post('client_id') ? $this->input->post('client_id') : null),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'description' => htmlspecialchars(trim($this->input->post('description'))),
      'event'       => htmlspecialchars(trim($this->input->post('event'))),
      'result'      => htmlspecialchars(trim($this->input->post('result'))),
      'start'       => ($this->input->post('start') ? date('Y-m-d H:i:s', strtotime($this->input->post('start'))) : NULL),
      'end'         => ($this->input->post('end') ? date('Y-m-d H:i:s', strtotime($this->input->post('end'))) : NULL),
      'active'      => 1,
      'allDay'      => ($this->input->post('allDay') ? 1 : 0),
      'check'       => ($this->input->post('check') ? 1 : 0),
    );

    $errors = $this->_validate_event($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }

    if ($params['allDay'] && !$params['end']) {
      $params['end'] = date('Y-m-d H:i:s', mktime(0,0,0,
        date("m",strtotime($params['start'])),
        date("d",strtotime($params['start']))+1,
        date("Y",strtotime($params['start']))
      ));
    }
    
    $id = $this->calendar_model->create_event($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    send_answer();
  }
  
  function _validate_event($params) {
    $errors = array();
    if (!$params['title']) { $errors['title'] = 'Не указано название'; }
    if (!$params['start']) { $errors['start'] = 'Не указано начало'; }
    if (!$params['allDay'] && !$params['end']) { $errors['end'] = 'Не указано окончание'; }
    return $errors;
  }
     
  /**
   *  Редактирование события
  **/  
  function edit_event($id) {
    $item = $this->calendar_model->get_event(array('id' => $id));
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование события',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_event_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => '',
            'fields'   => array(
              array(
                'view'     => 'fields/' . ($item['client_id'] ? 'readonly' : 'hidden'),
                'value'    => '<a href="/admin/clients/edit_client/'. $item['client_id'] .'/" target="_blank" class="btn btn-xs btn-info m-a-0"><span class="glyphicon glyphicon-list-alt"></span> Карточка клиента</a>',
                'type'     => '',
                'reaction' => ''
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'maxlength' => 256,
                'value'     => $item['title'],
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Контакты:',
                'name'      => 'description',
                'value'     => $item['description'],
                'maxlength' => 1000,
                'rows'      => 2
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Описание события:',
                'name'      => 'event',
                'value'     => $item['event'],
                'maxlength' => 1000,
                'rows'      => 2
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Результат:',
                'value'     => $item['result'],
                'name'      => 'result',
                'maxlength' => 1000,
                'rows'      => 2
              ),
              array(
                'view'      => 'fields/hidden',
                'title'     => 'Клиент:',
                'name'      => 'client_id',
                'value'     => $item['client_id'],
              ),
              array(
                'view'  => 'fields/datetime',
                'title' => 'Начало:',
                'name'  => 'start',
                'value' => ($item['start'] ? date('d.m.Y H:i', strtotime($item['start'])) : '')
              ),
              array(
                'view'  => 'fields/hidden',
                'title' => 'Окончание:',
                'name'  => 'end',
                'value' => ($item['end'] ? date('d.m.Y H:i', strtotime($item['end'])) : '')
              ),
              array(
                'view'    => 'fields/hidden',
                'title'   => 'Весь день',
                'name'    => 'allDay',
                'value'   => $item['allDay'],
                'checked' => $item['allDay']
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Выполнено',
                'name'    => 'check',
                'checked' => $item['check']
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'class'    => 'hidden',
                'type'     => 'ajax',
                'reaction' => ''
              )
            )
          )
        )
      )),
      'back' => ''
    ), FALSE);
  }
  
  function _edit_event_process($id) {
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'description' => htmlspecialchars(trim($this->input->post('description'))),
      'event'       => htmlspecialchars(trim($this->input->post('event'))),
      'result'      => htmlspecialchars(trim($this->input->post('result'))),
      'start'       => ($this->input->post('start') ? date('Y-m-d H:i:s', strtotime($this->input->post('start'))) : NULL),
      'end'         => ($this->input->post('end') ? date('Y-m-d H:i:s', strtotime($this->input->post('end'))) : NULL),
      'check'       => ($this->input->post('check') ? 1 : 0),
      'allDay'      => ($this->input->post('allDay') ? 1 : 0),
      'color'       => ($this->input->post('check') ? '#1ABC9C' : '')
    );

    $errors = $this->_validate_event($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->calendar_model->update_event($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    send_answer();
  }

  /**
   * Удаление события
  **/
  function delete_event($id) {
    if (!$this->calendar_model->delete_event((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }
  
  function get_events() {
    $where = array('admin_id'=>$this->admin_id);
    if($this->uri->getParam('start')){
      $where['start >=']=date('Y-m-d H:i:s', strtotime($this->uri->getParam('start')));
    }
    if($this->uri->getParam('end')){
      $where['end <=']=date('Y-m-d H:i:s', strtotime($this->uri->getParam('end')));
    }

    $events = $this->calendar_model->get_events(0,0,$where);
    echo json_encode($events);
  }

  function get_event($id) {
    $event = $this->calendar_model->get_event(array('id'=>$id));
    echo json_encode($event);
  }

  function get_lastEvent() {
    $event = $this->calendar_model->get_event(array('admin_id'=>$this->admin_id),array('id'=>'desc'));
    echo json_encode($event);
  }

  /*
  * Список событий по типу и клиенту
  * return json
  */
  function getClientEvents() {
    $where = array();
    //тип событий
    if($this->input->post('type')){
      //прошедшие события не отмеченные "выполнено"
      if($this->input->post('type') == 'red'){
        $where['check']=0;
        $where['start <']=date('Y-m-d H:i:s');
      }
      //запланированные события не отмеченные "выполнено"
      if($this->input->post('type') == 'blue'){
        $where['check']=0;
        $where['start >']=date('Y-m-d H:i:s');
      }
    }
    if((int)$this->input->post('client_id')){
      $where['client_id']=(int)$this->input->post('client_id');
    }
    if((int)$this->input->post('admin_id')){
      $where['admin_id']=(int)$this->input->post('admin_id');
    }

    $events = $this->calendar_model->get_events(0,0,$where,array('start'=>'desc'));
    foreach ($events as $key => &$event) {
      $event['start'] = date('d.m.Y',strtotime($event['start']));
    }
    unset($event);
    echo json_encode($events);
  }

  function eventDrop() {
    $result = array(
      'errors' => array()
    );
    $id = (int)$this->input->post('id');
    $params = array(
      'start'  => ($this->input->post('start') ? date('Y-m-d H:i:s', strtotime($this->input->post('start'))) : NULL),
      'allDay' => ($this->input->post('allDay') == "true" ? 1 : 0),
    );
    if($this->input->post('end') != null){
      $params['end'] = ($this->input->post('end') ? date('Y-m-d H:i:s', strtotime($this->input->post('end'))) : NULL);
    }
    if(!$id){
      $result['errors'][] = 'Событие не найдено';
    } else {
      if(!$this->calendar_model->update_event($id,$params)){
        $result['errors'][] = 'Ошибка при сохранении изменений';
      }      
    }
    echo json_encode($result);
  }
   
}