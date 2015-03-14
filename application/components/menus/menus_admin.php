<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menus_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('projects/models/projects_model');
    $this->load->model('templates/models/templates_model');
    $this->load->model('menus/models/menus_model');
  }
  
  /**
   * Просмотр списка меню
  **/  
  function index() {
    return $this->render_template('templates/index', array(
      'menus' => $this->menus_model->get_menus()
    ));
  }
  
  /**
   * Создание меню
  **/  
  function create() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание меню',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_process/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'    => 'fields/select',
                'title'   => 'Проект:',
                'name'    => 'project_id',
                'options' => $this->projects_model->get_projects()
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Системное имя:',
                'description' => 'Уникальное имя меню',
                'name'        => 'name',
                'req'         => TRUE
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон меню:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'req'     => TRUE
              ),
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'req'   => TRUE
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Описание:',
                'name'  => 'description'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Глубина вложенности:',
                'description' => 'Количество уровней вложенности меню (0 - без ограничений, 1 - одноуровневое меню, 2 - двухуровневое и т. д.)',
                'name'        => 'depth',
                'value'       => 1
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small add_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          )
        )
      ))
    ), TRUE);
  }
  
  function _create_process() {
    $params = array(
      'project_id'  => (int)$this->input->post('project_id'),
      'name'        => trim($this->input->post('name')),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'description' => htmlspecialchars($this->input->post('description')),
      'depth'       => trim($this->input->post('depth')),
      'template_id' => (int)$this->input->post('template_id')
    );
    
    $errors = $this->_validate($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->menus_model->create_menu($params)) {
      send_answer(array('errors' => array('Не удалось создать меню')));
    }
    
    send_answer();
  }
  
  function _validate($params, $id = 0) {
    $errors = array();
    if (!$this->form_validation->alpha_dash($params['name'])) {
      $errors[] = 'Некорректное системное имя';
    }
    if (!$this->main_model->is_available('menus', $params['name'], $id)) {
      $errors[] = 'Указанное системное имя занято';
    }
    if (!$params['template_id']) {
      $errors[] = 'Не выбран шаблон';
    }
    if (!$params['title']) {
      $errors[] = 'Не указано название';
    }
    if (!$this->form_validation->numeric($params['depth']) || $params['depth'] < 0) {
      $errors[] = 'Некорректная глубина вложенности';
    }
    return $errors;
  }
  
  /**
   * Редактирование меню
  **/ 
  function edit($id) {
    $menu = $this->menus_model->get_menu((int)$id);
    if (!$menu) {
      show_error('Меню не найдено');
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование меню "'. $menu['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_process/'. $menu['id'] .'/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'    => 'fields/select',
                'title'   => 'Проект:',
                'name'    => 'project_id',
                'options' => $this->projects_model->get_projects(),
                'value'   => $menu['project_id']
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Системное имя:',
                'description' => 'Уникальное имя меню',
                'name'        => 'name',
                'value'       => $menu['name'],
                'req'         => TRUE
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон меню:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $menu['template_id'],
                'req'     => TRUE
              ),
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'value' => $menu['title'],
                'req'   => TRUE
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Описание:',
                'name'  => 'description',
                'value' => $menu['description']
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Глубина вложенности:',
                'description' => 'Количество уровней вложенности меню (0 - без ограничений, 1 - одноуровневое меню, 2 - двухуровневое и т. д.)',
                'name'        => 'depth',
                'value'       => $menu['depth']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      ))
    ), TRUE);
  }
  
  function _edit_process($id) {
    $menu = $this->menus_model->get_menu((int)$id);
    if (!$menu) {
      send_answer(array('errors' => array('Меню не найдено')));
    }
    
    $params = array(
      'project_id'  => (int)$this->input->post('project_id'),
      'name'        => trim($this->input->post('name')),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'description' => htmlspecialchars($this->input->post('description')),
      'depth'       => trim($this->input->post('depth')),
      'template_id' => (int)$this->input->post('template_id')
    );
    
    $errors = $this->_validate($params, $menu['id']);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->menus_model->update_menu($menu['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    send_answer();
  }
  
  /**
   * Удаление меню
  **/ 
  function delete($id) {
    $menu = $this->menus_model->get_menu((int)$id);
    if (!$menu) {
      send_answer(array('errors' => array('Меню не найдено')));
    }
    
    if (!$this->menus_model->delete_menu($menu['id'])) {
      send_answer(array('errors' => array('Не удалось удалить меню')));
    }
    
    send_answer();
  }
  
}