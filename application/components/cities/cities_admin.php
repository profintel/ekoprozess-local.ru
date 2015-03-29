<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cities_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();

    $this->load->model('cities/models/cities_model');
  }
  
  /**
  * Просмотр меню компонента Регионы / Города
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'Регионы / Города',
      'items' => array(
        array(
          'title' => 'Регионы',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'regions/',
          'class' => 'permits-superusers-icon'
        ),
        array(
          'title' => 'Города',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'cities/',
          'class' => 'components-installed-icon'
        )
      )
    ));
  }
  
  /**
  *  Просмотр списка Регионов
  */
  function regions($page = 1) {
    $limit = 50;
    $offset = $limit * ($page - 1);
    $cnt = $this->cities_model->get_regions_cnt();
    $pages = get_pages($page, $cnt, $limit);
    $pagination_data = array(
      'pages' => $pages,
      'page' => $page,
      'prefix' => '/admin'.$this->params['path'].'regions/'
    );
    $data = array(
      'title'           => 'Регионы',
      'component_item'  => array('name' => 'region', 'title' => 'регион'),
      'items'           => $this->cities_model->get_regions($limit, $offset),
      'pagination'      => $this->load->view('admin/pagination', $pagination_data, true),
    );

    return $this->render_template('admin/items', $data);
  }
  
  /**
   *  Создание региона
  **/  
  function create_region() {
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление региона',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_region_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'maxlength' => 256
              ),
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Вкл./Выкл.',
                'name'  => 'active'
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'regions/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'regions/'
    ), TRUE);
  }
  
  function _create_region_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'country_id'  => 3159,
      'active'      => ($this->input->post('active') ? 1 : 0)
    );

    $errors = $this->_validate_region($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->cities_model->create_region($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    send_answer();
  }
  
  /**
   *  Редактирование региона
  **/  
  function edit_region($id) {
    $item = $this->cities_model->get_region(array('id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование региона',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_region_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'value'     => $item['title'],
                'maxlength' => 256
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Вкл./Выкл.',
                'checked' => $item['active'],
                'name'    => 'active'
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
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'regions/'
    ), TRUE);
  }
  
  function _edit_region_process($id) {    
    $params = array(
      'title'   => htmlspecialchars(trim($this->input->post('title'))),
      'active'  => ($this->input->post('active') ? 1 : 0)
    );

    $errors = $this->_validate_region($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if (!$this->cities_model->update_region($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }
  
  function _validate_region($params) {
    $errors = array();
    if (!$params['title']) { $errors['title'] = 'Не указано название'; }
    return $errors;
  }

  /**
   *  Включение региона
   * @param $id - id вложения
   */   
  function enable_region($id) {
    $this->cities_model->update_region((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'].'regions/');
  }

  /**
   *  Выключение региона
   * @param $id - id вложения
   */     
  function disable_region($id) {
    $this->cities_model->update_region((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'].'regions/');
  }

  /**
   * Удаление региона
  **/
  function delete_region($id) {
    if (!$this->cities_model->delete_region((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }

  /**
  *  Просмотр списка Городов
  */
  function cities($page = 1) {
    $limit = 50;
    $offset = $limit * ($page - 1);
    $cnt = $this->cities_model->get_cities_cnt();
    $pages = get_pages($page, $cnt, $limit);
    $pagination_data = array(
      'pages' => $pages,
      'page' => $page,
      'prefix' => '/admin'.$this->params['path'].'cities/'
    );
    $data = array(
      'title' => 'Города',
      'component_item'  => array('name' => 'city', 'title' => 'город'),
      'items'           => $this->cities_model->get_cities($limit, $offset),
      'pagination'      => $this->load->view('admin/pagination', $pagination_data, true),
    );

    return $this->render_template('admin/items', $data);
  }
  
  /**
   *  Создание города
  **/  
  function create_city() {
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление города',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_city_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'maxlength' => 256
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Регион:',
                'name'    => 'region_id',
                'options' => $this->cities_model->get_regions()
              ),
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Вкл./Выкл.',
                'name'  => 'active'
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'cities/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'cities/'
    ), TRUE);
  }
  
  function _create_city_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'region_id'   => (int)$this->input->post('region_id'),
      'active'      => ($this->input->post('active') ? 1 : 0)
    );

    $errors = $this->_validate_city($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->cities_model->create_city($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    send_answer();
  }
  
  /**
   *  Редактирование города
  **/  
  function edit_city($id) {
    $item = $this->cities_model->get_city(array('id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование города',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_city_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'value'     => $item['title'],
                'maxlength' => 256
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Регион:',
                'name'    => 'region_id',
                'options' => $this->cities_model->get_regions(),
                'value'   => $item['region_id']
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Вкл./Выкл.',
                'checked' => $item['active'],
                'name'    => 'active'
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
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'cities/'
    ), TRUE);
  }
  
  function _edit_city_process($id) {    
    $params = array(
      'title'   => htmlspecialchars(trim($this->input->post('title'))),
      'region_id'   => (int)$this->input->post('region_id'),
      'active'  => ($this->input->post('active') ? 1 : 0)
    );

    $errors = $this->_validate_city($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if (!$this->cities_model->update_city($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }
  
  function _validate_city($params) {
    $errors = array();
    if (!$params['title']) { $errors['title'] = 'Не указано название'; }
    if (!$params['region_id']) { $errors['region_id'] = 'Не указан регион'; }
    return $errors;
  }

  /**
   *  Включение города
   * @param $id - id вложения
   */   
  function enable_city($id) {
    $this->cities_model->update_city((int)$id, array('active' => 1));
    header('Location: '.$_SERVER['HTTP_REFERER']);
  }

  /**
   *  Выключение города
   * @param $id - id вложения
   */     
  function disable_city($id) {
    $this->cities_model->update_city((int)$id, array('active' => 0));
    header('Location: '.$_SERVER['HTTP_REFERER']);
  }

  /**
   * Удаление города
  **/
  function delete_city($id) {
    if (!$this->cities_model->delete_city((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }
  
}