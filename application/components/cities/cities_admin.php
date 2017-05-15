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
          'title' => 'Федеральные округа России',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'regions_federal/',
          'class' => ''
        ),
        array(
          'title' => 'Регионы',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'regions/',
          'class' => ''
        ),
        array(
          'title' => 'Города',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'cities/',
          'class' => ''
        )
      ),
      'back' => $this->lang_prefix .'/admin/libs/'
    ));
  }

  /**
  *  Просмотр списка федеральных округов
  */
  function regions_federal($page = 1) {
    $where = array();
    $title = ($this->uri->getParam('title') ? mysql_prepare($this->uri->getParam('title')) : '');
    if($title){
      $where['title LIKE'] = $title.'%';
    }
    $limit = 50;
    $offset = $limit * ($page - 1);
    $cnt = $this->cities_model->get_regions_federal_cnt($where);
    $pages = get_pages($page, $cnt, $limit);
    $pagination_data = array(
      'pages' => $pages,
      'page' => $page,
      'prefix' => '/admin'.$this->params['path'].'regions_federal/',
      'postfix' => ($title ? '?title='.$title : '')
    );
    $data = array(
      'title' => 'Федеральные округа России',
      'search_path'     => '/admin'.$this->params['path'].'regions_federal/',
      'search_title'    => $title,
      'component_item'  => array('name' => 'region_federal', 'title' => 'федеральный округ'),
      'items'           => $this->cities_model->get_regions_federal($limit, $offset, $where),
      'pagination'      => $this->load->view('admin/pagination', $pagination_data, true),
    );

    return $this->render_template('admin/items', $data);
  }
  
  /**
   *  Создание федерального округа
  **/  
  function create_region_federal() {
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление федерального округа',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_region_federal_process/',
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
                'view'      => 'fields/select',
                'title'     => 'Регионы:',
                'name'      => 'regions[]',
                'options'   => $this->cities_model->get_regions(),
                'multiple'  => true
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
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'regions_federal/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'regions_federal/'
    ), TRUE);
  }
  
  function _create_region_federal_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'country_id'  => 3159,
      'active'      => ($this->input->post('active') ? 1 : 0)
    );

    $errors = $this->_validate_region_federal($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->cities_model->create_region_federal($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    $regions = $this->input->post('regions');
    if ($regions) {
      if (!$this->cities_model->set_region_federal_regions($id, $regions)) {
        $thid->delete_region_federal($id);
        exit('Не удалось сохранить регионы');
      }
    }

    send_answer();
  }
  
  /**
   *  Редактирование федерального округа
  **/  
  function edit_region_federal($id) {
    $item = $this->cities_model->get_region_federal(array('id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование федерального округа',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_region_federal_process/'.$id.'/',
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
                'view'      => 'fields/select',
                'title'     => 'Регионы:',
                'name'      => 'regions[]',
                'options'   => $this->cities_model->get_regions(),
                'value'     => $item['regions'],
                'multiple'  => true
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
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'regions_federal/'
    ), TRUE);
  }
  
  function _edit_region_federal_process($id) {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'active'      => ($this->input->post('active') ? 1 : 0)
    );

    $errors = $this->_validate_region_federal($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if (!$this->cities_model->update_region_federal($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    $regions = $this->input->post('regions');
    if ($regions) {
      if (!$this->cities_model->set_region_federal_regions($id, $regions)) {
        exit('Не удалось сохранить регионы');
      }
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }
  
  function _validate_region_federal($params) {
    $errors = array();
    if (!$params['title']) { $errors['title'] = 'Не указано название'; }
    return $errors;
  }

  /**
   *  Включение федерального округа
   * @param $id - id вложения
   */   
  function enable_region_federal($id) {
    $this->cities_model->update_region_federal((int)$id, array('active' => 1));
    header('Location: '.$_SERVER['HTTP_REFERER']);
  }

  /**
   *  Выключение федерального округа
   * @param $id - id вложения
   */     
  function disable_region_federal($id) {
    $this->cities_model->update_region_federal((int)$id, array('active' => 0));
    header('Location: '.$_SERVER['HTTP_REFERER']);
  }

  /**
   * Удаление федерального округа
  **/
  function delete_region_federal($id) {
    if (!$this->cities_model->delete_region_federal((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }

  /**
  *  Просмотр списка Регионов
  */
  function regions($page = 1) {
    $where = array();
    $title = ($this->uri->getParam('title') ? mysql_prepare($this->uri->getParam('title')) : '');
    if($title){
      $where['title LIKE'] = $title.'%';
    }
    $limit = 50;
    $offset = $limit * ($page - 1);
    $cnt = $this->cities_model->get_regions_cnt($where );
    $pages = get_pages($page, $cnt, $limit);
    $pagination_data = array(
      'pages'   => $pages,
      'page'    => $page,
      'prefix'  => '/admin'.$this->params['path'].'regions/',
      'postfix' => ($title ? '?title='.$title : '')
    );
    $data = array(
      'title'           => 'Регионы',
      'search_path'     => '/admin'.$this->params['path'].'regions/',
      'search_title'    => $title,
      'component_item'  => array('name' => 'region', 'title' => 'регион'),
      'items'           => $this->cities_model->get_regions($limit, $offset, $where),
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
  function cities($render_table = false) {
    $get_params = array(
      'title'             => ($this->uri->getParam('title') ? mysql_prepare($this->uri->getParam('title')) : ''),
      'region_federal_id' => ($this->uri->getParam('region_federal_id') ? (int)$this->uri->getParam('region_federal_id') : ''),
      'region_id'         => ($this->uri->getParam('region_id') ? (int)$this->uri->getParam('region_id') : ''),
      'country_id'        => ($this->uri->getParam('country_id') !== FALSE ? (int)$this->uri->getParam('country_id') : 3159),
    );

    $data = array(
      'title'           => 'Города',
      'component_item'  => array('name' => 'city', 'title' => 'город'),
      'get_params'      => $get_params,
      //формируем ссылку на создание объекта
      'link_create'     => array(
          'title' => 'Создать',
          'path'  => $this->lang_prefix.'/admin'.$this->component['path'].'create_city/',
        ),
      'quick_form' => $this->view->render_form(array(
        'method'  => 'GET',
        'action'  => $this->lang_prefix .'/admin'. $this->params['path'] .'cities_search/',
        'view'    => 'forms/form_inline',
        'blocks' => array(
          array(
            'title'    => '',
            'fields'   => array(
              array(
                'view'      => 'fields/autocomplete_input',
                'class'     => 'col-xs-11 quick_form_input',
                'title'     => 'Название:',
                'name'      => 'title',
                'value'     => $get_params['title'],
                'component' => $this->params['name'],
                'method'    => 'cities_search',
                'maxlength' => 256
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'col-xs-1 quick_form_btn',
                'icon'     => 'glyphicon-search',
                'title'    => '',
                'type'     => '',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          )
        )
      )),
      'form'            => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'cities/?ajax=1',
        'enctype' => '',
        'blocks' => array(
          array(
            'title'    => 'Параметры поиска',
            'fields'   => array(
              array(
                'view'     => 'fields/select',
                'title'    => 'Страна:',
                'name'     => 'country_id',
                'multiple' => false,
                'empty'    => true,
                'optgroup' => false,
                'options'  => $this->cities_model->get_countries(),
                'value'    => $get_params['country_id'],
                'onchange' => "changeRegion(this, 'country'); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Федеральный округ:',
                'id'      => 'region_federal_id',
                'name'    => 'region_federal_id',
                'value'   => $get_params['region_federal_id'],
                'options' => $this->cities_model->get_regions_federal(0,0,array('country_id'=>$get_params['country_id'])),
                'onchange'=> "changeRegion(this, 'federal'); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
                'multiple' => false,
                'empty'   => true
              ),
              array(
                'view'     => 'fields/select',
                'title'    => 'Регион:',
                'name'     => 'region_id',
                'multiple' => false,
                'empty'    => true,
                'optgroup' => false,
                'options'  => $this->cities_model->get_regions(),
                'value'    => $get_params['region_id'],
                'onchange' => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
              ),
              array(
                'view'          => 'fields/submit',
                'title'         => 'Сформировать',
                'type'          => 'ajax',
                'id'            => 'btn-form',
                'reaction_func' => true,
                'reaction'      => 'handle_ajaxResultHTML',
                'data_type'     => 'html'
              )
            )
          )
        )
      )),
    );
    
    // если запрос на формирование данных, иначе возвращаем шаблон - обертку
    if($render_table || $this->uri->getParam('ajax') == 1){
      $where = array();
      $error = '';
      if($get_params['title']){
        $where['pr_city.title LIKE '] = $get_params['title'].'%';
      }
      if($get_params['region_id']){
        $where['pr_city.region_id'] = $get_params['region_id'];
      }

      $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
      $limit = 50;
      $offset = $limit * ($page - 1);
      $cnt = $this->cities_model->get_cities_cnt($where, $get_params['region_federal_id'], $get_params['country_id']);
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
        'prefix' => '/admin'.$this->params['path'].'cities/',
        'postfix' => $postfix
      );

      $data = array_merge($data, array(
        'pagination'  => $this->load->view('templates/pagination', $pagination_data, true),
        'error'       => $error,
        'items'       => $this->cities_model->get_cities($limit, $offset, $where, false, $get_params['region_federal_id'], $get_params['country_id'])
      ));
      
      if($render_table){
        return $this->load->view('../../application/components/cities/templates/admin_table',$data,true);
      } else if($this->uri->getParam('ajax') == 1){
        echo $this->load->view('../../application/components/cities/templates/admin_table',$data,true);
        return;
      }
    }

    return $this->render_template('templates/admin_items', array('data'=>$data));
  }

  /**
  *  Быстрый поиск города по названию
  */
  function cities_search() {
    $where = array();
    $title = ($this->input->post('search_string') ? mysql_prepare($this->input->post('search_string')) : '');
    if($title){
      $where['title LIKE'] = $title.'%';
    }
    // var_dump($where);
    $limit = 20;
    $offset = 0;
    $items = $this->cities_model->get_cities($limit, $offset, $where);
    $result = array('items'=>array());
    foreach ($items as $key => $item) {
      $result['items'][] = array(
        'id'        => $item['id'],
        'title'     => $item['title_full'],
        'location'  => '/admin'.$this->params['path'].'edit_city/'.$item['id'].'/',
      );
    }

    echo json_encode($result);
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
                'view'      => 'fields/text',
                'title'     => 'Численность населения (т. чел.):',
                'name'      => 'number',
                'maxlength' => 10
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Расстояние до Екатеринбурга (км):',
                'name'      => 'dist_ekb',
                'maxlength' => 10
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
      'number'      => (int)$this->input->post('number'),
      'dist_ekb'    => (float)$this->input->post('dist_ekb'),
      'active'      => ($this->input->post('active') ? 1 : 0)
    );
    //формируем полное название с регионом
    $region = $this->cities_model->get_region(array('id' => $params['region_id']));
    $params['title_full'] = $params['title'].', '.$region['title'];

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
  */  
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
                'view'      => 'fields/text',
                'title'     => 'Численность населения (т. чел.):',
                'name'      => 'number',
                'maxlength' => 10,
                'value'     => $item['number']
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Расстояние до Екатеринбурга (км):',
                'name'      => 'dist_ekb',
                'maxlength' => 10,
                'value'     => $item['dist_ekb']
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
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'region_id'   => (int)$this->input->post('region_id'),
      'number'      => (int)$this->input->post('number'),
      'dist_ekb'    => (float)$this->input->post('dist_ekb'),
      'active'      => ($this->input->post('active') ? 1 : 0)
    );
    //формируем полное название с регионом
    $region = $this->cities_model->get_region(array('id' => $params['region_id']));
    $params['title_full'] = $params['title'].', '.$region['title'];

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
  * Перезаписывает полное название с регионом для всех городов
  */
  function _updateCitiesTitleFull() {    
    $items = $this->cities_model->get_cities();
    foreach ($items as $key => $item) {
      //формируем полное название с регионом
      $region = $this->cities_model->get_region(array('id' => $item['region_id']));
      $params['title_full'] = $item['title'].', '.$region['title'];
      if ($this->cities_model->update_city($item['id'], $params)) {
        echo $params['title_full'].'<br/>';
      }
    }
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