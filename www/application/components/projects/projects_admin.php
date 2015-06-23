<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('templates/models/templates_model');
  }
    
  /**
   * Просмотр структуры страниц
  **/  
  function index() {
    $projects = $this->projects_model->get_projects();
    foreach ($projects as &$project) {
      $project['pages'] = $this->_make_pages($this->projects_model->get_pages($project['id']));
    }
    unset($project);
    
    return $this->render_template('templates/index', array(
      'projects' => $projects
    ));
  }
  
  function _make_pages($pages) {
    $renders = array();
    foreach ($pages as $page) {
      $renders[] = $this->render_template('templates/page', array(
        'page'  => $page,
        'pages' => $this->_make_pages($page['pages'])
      ));
    }
    return implode("\n\n", $renders);
  }

  /**
   * Активация проекта
  **/  
  function enable_project($id) {
    $this->projects_model->update_project((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path']);
  }
  
  /**
   * Деактивация проекта
  **/ 
  function disable_project($id) {
    $this->projects_model->update_project((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path']);
  }
  
  /**
   * Создание проекта
  **/ 
  function create_project() {
    $languages = $this->languages_model->get_languages(1, 0);
    
    return $this->render_template('admin/inner', array(
      'title' => 'Создание проекта',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_project_process/',
        'blocks' => array(
          array(
            'title'  => 'Основные параметры',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Внутреннее имя:',
                'name'        => 'title',
                'description' => 'Используется только внутри панели администрирования',
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Основной домен:',
                'description' => 'Основное доменное имя проекта',
                'name'        => 'domain',
                'req'         => TRUE
              ),
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Включен',
                'name'  => 'active'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'        => 'fields/select',
                'title'       => 'Основной шаблон по умолчанию:',
                'name'        => 'main_template_id',
                'description' => 'Подставляется в качестве основного шаблона при создании страниц',
                'options'     => $this->templates_model->get_templates(),
                'empty'       => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Шаблон страниц по умолчанию:',
                'name'        => 'template_id',
                'description' => 'Подставляется в качестве шаблона страницы при ее создании',
                'options'     => $this->templates_model->get_templates(),
                'empty'       => TRUE
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Заголовки и SEO',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Название проекта:',
                'name'        => 'project_title',
                'description' => 'Используется при отображении списка проектов на сайте',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок сайта:',
                'description' => 'Предваряет заголовок страницы в окне браузера (мета-тег TITLE)',
                'name'        => 'site_title',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок страниц по умолчанию:',
                'description' => 'Подставляется в заголовок страницы при ее создании',
                'name'        => 'page_title',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Ключевые слова по умолчанию:',
                'description' => 'Подставляются в ключевые слова страницы при ее создании',
                'name'        => 'keywords',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание по умолчанию:',
                'description' => 'Подставляется в описание страницы при ее создании',
                'name'        => 'description',
                'languages'   => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Контакты',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Email проекта:',
                'name'        => 'project_email',
                'description' => 'Email, с которого будут отправляться сообщения пользователям'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Email администратора:',
                'description' => 'Email для получения технических сообщений о работе проекта',
                'name'        => 'admin_email'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
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
  
  function _create_project_process() {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'title'            => htmlspecialchars(trim($this->input->post('title'))),
      'domain'           => mb_strtolower(trim($this->input->post('domain'))),
      'active'           => ($this->input->post('active') ? 1 : 0),
      'project_email'    => trim($this->input->post('project_email')),
      'admin_email'      => trim($this->input->post('admin_email')),
      'main_template_id' => ($this->input->post('main_template_id') ? (int)$this->input->post('main_template_id') : NULL),
      'template_id'      => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'project_title' => htmlspecialchars(trim($this->input->post('project_title_'. $language['name']))),
        'site_title'    => htmlspecialchars(trim($this->input->post('site_title_'. $language['name']))),
        'page_title'    => htmlspecialchars(trim($this->input->post('page_title_'. $language['name']))),
        'keywords'      => htmlspecialchars(trim($this->input->post('keywords_'. $language['name']))),
        'description'   => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_project($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->projects_model->create_project($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать проект')));
    }
    
    if (!$this->main_model->set_params('projects', $id, $multiparams)) {
      $this->projects_model->delete_project($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
  
  function _validate_project($params, $id = 0) {
    $errors = array();
    if (!$params['title']) {
      $errors[] = 'Не указано внутреннее имя';
    }
    if (!preg_match('/[a-zа-яё\d\.\-_]+/u', $params['domain'])) {
      $errors[] = 'Недопустимое доменное имя';
    }
    if (!$this->main_model->is_available('projects', $params['domain'], $id, 'domain')) {
      $errors[] = 'Указанный домен соответствует другому проекту';
    } elseif (!$this->main_model->is_available('projects_aliases', $params['domain'])) {
      $errors[] = 'Указанный домен соответствует другому проекту';
    }
    if ($params['project_email'] && !$this->form_validation->valid_email($params['project_email'])) {
      $errors[] = 'Некорректный email проекта';
    }
    if ($params['admin_email'] && !$this->form_validation->valid_email($params['admin_email'])) {
      $errors[] = 'Некорректный email администратора';
    }
    return $errors;
  }
  
  /**
   * Редактирование проекта
  **/  
  function edit_project($id) {
    $project = $this->projects_model->get_project((int)$id);
    if (!$project) {
      show_error('Проект не найден');
    }    
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование проекта',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_project_process/'. $project['id'] .'/',
        'blocks' => array(
          array(
            'title'  => 'Основные параметры',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Внутреннее имя:',
                'name'        => 'title',
                'description' => 'Используется только внутри панели администрирования',
                'value'       => $project['title'],
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Основной домен:',
                'description' => 'Основное доменное имя проекта',
                'name'        => 'domain',
                'value'       => $project['domain'],
                'req'         => TRUE
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Включен',
                'name'    => 'active',
                'checked' => $project['active']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          ),
          array(
            'title'  => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'        => 'fields/select',
                'title'       => 'Основной шаблон по умолчанию:',
                'name'        => 'main_template_id',
                'description' => 'Подставляется в качестве основного шаблона при создании страниц',
                'options'     => $this->templates_model->get_templates(),
                'value'       => $project['main_template_id'],
                'empty'       => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Шаблон страниц по умолчанию:',
                'name'        => 'template_id',
                'description' => 'Подставляется в качестве шаблона страницы при ее создании',
                'options'     => $this->templates_model->get_templates(),
                'value'       => $project['template_id'],
                'empty'       => TRUE
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          ),
          array(
            'title'  => 'Заголовки и SEO',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Название проекта:',
                'name'        => 'project_title',
                'description' => 'Используется при отображении списка проектов на сайте',
                'languages'   => $languages,
                'value'       => $project['params']
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок сайта:',
                'description' => 'Предваряет заголовок страницы в окне браузера (тег TITLE)',
                'name'        => 'site_title',
                'languages'   => $languages,
                'value'       => $project['params']
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок страниц по умолчанию:',
                'description' => 'Подставляется в заголовок страницы при ее создании',
                'name'        => 'page_title',
                'languages'   => $languages,
                'value'       => $project['params']
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Ключевые слова по умолчанию:',
                'description' => 'Подставляются в ключевые слова страницы при ее создании',
                'name'        => 'keywords',
                'languages'   => $languages,
                'value'       => $project['params']
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание по умолчанию:',
                'description' => 'Подставляется в описание страницы при ее создании',
                'name'        => 'description',
                'languages'   => $languages,
                'value'       => $project['params']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          ),
          array(
            'title'  => 'Контакты',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Email проекта:',
                'name'        => 'project_email',
                'value'       => $project['project_email'],
                'description' => 'Email, с которого будут отправляться сообщения пользователям'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Email администратора:',
                'name'        => 'admin_email',
                'value'       => $project['admin_email'],
                'description' => 'Email для получения технических сообщений о работе проекта'
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
  
  function _edit_project_process($id) {
    $project = $this->projects_model->get_project((int)$id);
    if (!$project) {
      send_answer(array('errors' => array('Проект не найден')));
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'title'            => htmlspecialchars(trim($this->input->post('title'))),
      'domain'           => mb_strtolower(trim($this->input->post('domain'))),
      'active'           => ($this->input->post('active') ? 1 : 0),
      'project_email'    => trim($this->input->post('project_email')),
      'admin_email'      => trim($this->input->post('admin_email')),
      'main_template_id' => ($this->input->post('main_template_id') ? (int)$this->input->post('main_template_id') : NULL),
      'template_id'      => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'project_title' => htmlspecialchars(trim($this->input->post('project_title_'. $language['name']))),
        'site_title'    => htmlspecialchars(trim($this->input->post('site_title_'. $language['name']))),
        'page_title'    => htmlspecialchars(trim($this->input->post('page_title_'. $language['name']))),
        'keywords'      => htmlspecialchars(trim($this->input->post('keywords_'. $language['name']))),
        'description'   => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_project($params, $project['id']);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->projects_model->update_project($project['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if (!$this->main_model->set_params('projects', $project['id'], $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
  
  /**
   * Удаление проекта
  **/
  function delete_project($id) {
    if (!$this->projects_model->delete_project((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить проект')));
    }
    
    send_answer();
  }
  
  /**
   * Просмотр алиасов
  **/
  function project_aliases($id) {
    $project = $this->projects_model->get_project((int)$id);
    if (!$project) {
      show_error('Проект не найден');
    }
    
    return $this->render_template('templates/project_aliases', array(
      'project' => $project,
      'aliases' => $this->projects_model->get_project_aliases($project['id'])
    ));
  }
  
  /**
   * Создание алиаса
  **/
  function create_project_alias($project_id) {
    $project = $this->projects_model->get_project((int)$project_id);
    if (!$project) {
      show_error('Проект не найден');
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление алиаса для проекта "'. $project['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_project_alias_process/'. $project['id'] .'/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Доменное имя:',
                'name'  => 'name',
                'req'   => TRUE
              ),
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Перенаправление на основной домен',
                'name'  => 'redirect'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Добавить',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'project_aliases/'. $project['id'] .'/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'project_aliases/'. $project['id'] .'/'
    ), TRUE);
  }
  
  function _create_project_alias_process($project_id) {
    $project = $this->projects_model->get_project((int)$project_id);
    if (!$project) {
      send_answer(array('errors' => array('Проект не найден')));
    }
    
    $params = array(
      'project_id' => $project['id'],
      'name'       => mb_strtolower(trim($this->input->post('name'))),
      'redirect'   => ($this->input->post('redirect') ? 1 : 0)
    );
    
    $errors = $this->_validate_project_alias($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->projects_model->create_project_alais($params)) {
      send_answer(array('errors' => array('Не удалось создать алиас')));
    }
    
    send_answer();
  }
  
  function _validate_project_alias($params, $id = 0) {
    $errors = array();
    if (!preg_match('/[a-zа-яё\d\.\-_]+/u', $params['name'])) {
      $errors[] = 'Недопустимое доменное имя';
    }
    if (!$this->main_model->is_available('projects', $params['name'], 0, 'domain')) {
      $errors[] = 'Указанный домен уже существует';
    } elseif (!$this->main_model->is_available('projects_aliases', $params['name'], $id)) {
      $errors[] = 'Указанный домен уже существует';
    }
    return $errors;
  }
 
  /**
   * Редактирование алиаса
  **/ 
  function edit_project_alias($id) {
    $alias = $this->projects_model->get_project_alias((int)$id);
    if (!$alias) {
      show_error('Алиас не найден');
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование алиаса "'. $alias['name'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_project_alias_process/'. $alias['id'] .'/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Доменное имя:',
                'name'  => 'name',
                'value' => $alias['name'],
                'req'   => TRUE
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Перенаправление на основной домен',
                'name'    => 'redirect',
                'checked' => $alias['redirect']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'project_aliases/'. $alias['project_id'] .'/'
    ), TRUE);
  }
  
  function _edit_project_alias_process($id) {
    $alais = $this->projects_model->get_project_alias((int)$id);
    if (!$alais) {
      send_answer(array('errors' => array('Алиас не найден')));
    }
    
    $params = array(
      'name'     => mb_strtolower(trim($this->input->post('name'))),
      'redirect' => ($this->input->post('redirect') ? 1 : 0)
    );
    
    $errors = $this->_validate_project_alias($params, $alais['id']);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->projects_model->update_project_alias($alais['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    send_answer();
  }
  
  /**
   * Удаление алиаса
  **/ 
  function delete_project_alias($id) {
    if (!$this->projects_model->delete_project_alias((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить алиас')));
    }
    
    send_answer();
  }
  
  /**
   * Активация алиаса
  **/ 
  function enable_page($id) {
    $this->projects_model->update_page((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path']);
  }
  
  /**
   * Деактивация алиаса
  **/ 
  function disable_page($id) {
    $this->projects_model->update_page((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path']);
  }
  
  /**
   * Создание страницы
  **/ 
  function create_page($project_id, $parent_id = 0) {
    $project = $this->projects_model->get_project((int)$project_id);
    if (!$project) {
      show_error('Проект не найден');
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    $additional_fields = array();
    
    if ($this->main_model->exists_component('menus')) {
      $this->load->model('menus/models/menus_model');
      
      $additional_fields[] = array(
        'view'        => 'fields/select',
        'title'       => 'Принадлежность к меню:',
        'description' => 'Отображение страницы в качестве корневого элемента выбранных меню (дочерние страницы прикрепляются автоматически) при включеной опции "Отображать в меню"',
        'name'        => 'menus[]',
        'options'     => $this->menus_model->get_menus($project['id']),
        'multiple'    => TRUE
      );
    }
    
    $additional_fields[] = array(
      'view'    => 'fields/select',
      'title'   => 'Доступ к странице:',
      'name'    => 'access_type_id',
      'options' => $this->projects_model->get_access_types()
    );
    $additional_fields[] = array(
      'view'  => 'fields/checkbox',
      'title' => 'Главная страница',
      'name'  => 'is_main'
    );
    $additional_fields[] = array(
      'view'    => 'fields/checkbox',
      'title'   => 'Доступна для поиска',
      'name'    => 'is_searchable',
      'checked' => TRUE
    );
    $additional_fields[] = array(
      'view'  => 'fields/checkbox',
      'title' => 'Отображать в меню',
      'name'  => 'in_menu'
    );
    $additional_fields[] = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small accept_i_s',
      'title'    => 'Создать',
      'type'     => 'ajax',
      'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Создание страницы',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_page_process/'. $project['id'] .'/'. (int)$parent_id .'/',
        'blocks' => array(
          array(
            'title'  => 'Основные параметры',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Внутреннее имя:',
                'name'        => 'title',
                'id'          => 'projects-page-title',
                'description' => 'Используется только внутри панели администрирования',
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Алиас:',
                'id'          => 'projects-page-alias',
                'description' => 'Уникальное в рамках раздела имя страницы, являющееся составной частью ее полного адреса',
                'name'        => 'alias',
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Основной шаблон:',
                'name'        => 'main_template_id',
                'description' => 'Основной шаблон, "обертка" для шаблона страницы',
                'options'     => $this->templates_model->get_templates(),
                'value'       => $project['main_template_id'],
                'empty'       => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Шаблон страницы:',
                'name'        => 'template_id',
                'description' => 'Управляет отображением содержимого страницы',
                'options'     => $this->templates_model->get_templates(),
                'value'       => $project['template_id'],
                'empty'       => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Переадресация:',
                'description' => 'Адрес для перенаправления при входе на страницу',
                'name'        => 'redirect'
              ),
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Страница доступна',
                'name'  => 'active'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Наполнение',
            'fields' => array(
              array(
                'view'      => 'fields/editor',
                'title'     => 'Содержимое страницы:',
                'name'      => 'content',
                'toolbar'   => 'Full',
                'languages' => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Заголовки и SEO',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Название страницы:',
                'name'        => 'name',
                'description' => 'Используется в пунктах меню, "хлебных крошках" и т. п.',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок страницы:',
                'description' => 'Отображается в заголовке окна браузера (мета-тег TITLE)',
                'name'        => 'title',
                'value'       => @$project['params']['page_title_'. $this->language],
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок в теле страницы:',
                'description' => 'Подставляется в тег H1',
                'name'        => 'h1',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Ключевые слова:',
                'description' => 'Подставляются в мета-тег KEYWORDS',
                'name'        => 'keywords',
                'value'       => @$project['params']['keywords_'. $this->language],
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание:',
                'description' => 'Подставляется в мета-тег DESCRIPTION',
                'name'        => 'description',
                'value'       => @$project['params']['description_'. $this->language],
                'languages'   => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Дополнительные параметры',
            'fields' => $additional_fields
          )
        )
      ))
    ), TRUE);
  }
  
  function _create_page_process($project_id, $parent_id = 0) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    if ($parent_id) {
      $parent = $this->projects_model->get_page($parent_id);
      if (!$parent) {
        send_answer(array('errors' => array('Не найдена родительская страница')));
      }
    }
    
    $params = array(
      'project_id'       => (int)$project_id,
      'parent_id'        => ($parent_id ? $parent['id'] : NULL),
      'title'            => htmlspecialchars(trim($this->input->post('title'))),
      'alias'            => trim($this->input->post('alias')),
      'main_template_id' => ($this->input->post('main_template_id') ? (int)$this->input->post('main_template_id') : NULL),
      'template_id'      => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL),
      'redirect'         => trim($this->input->post('redirect')),
      'active'           => ($this->input->post('active') ? 1 : 0),
      'is_main'          => ($this->input->post('is_main') ? 1 : 0),
      'is_searchable'    => ($this->input->post('is_searchable') ? 1 : 0),
      'in_menu'          => ($this->input->post('in_menu') ? 1 : 0),
      'access_type_id'   => (int)$this->input->post('access_type_id'),
      'tm'               => date('Y-m-d H:i:s')
    );
    $params['path']   = ($parent_id ? $parent['path'] : '/') . $params['alias'] .'/';
    $params['order']  = $this->projects_model->get_page_order($params['project_id'], $params['parent_id']);
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'content'     => trim($this->input->post('content_'. $language['name'])),
        'name'        => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'title'       => htmlspecialchars(trim($this->input->post('title_'. $language['name']))),
        'h1'          => htmlspecialchars(trim($this->input->post('h1_'. $language['name']))),
        'keywords'    => htmlspecialchars(trim($this->input->post('keywords_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_page($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->projects_model->create_page($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать страницу')));
    }
    
    if (!$this->main_model->set_params('pages', $id, $multiparams)) {
      $this->projects_model->delete_page($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if ($this->main_model->exists_component('menus')) {
      $this->load->model('menus/models/menus_model');
      
      if (!$this->menus_model->set_page_menus($id, $this->input->post('menus'))) {
        $this->projects_model->delete_page($id);
        send_answer(array('errors' => array('Не удалось сохранить принадлежность к меню')));
      }
    }
    
    if ($params['is_main']) {
      $this->projects_model->reset_main($params['project_id'], $id);
    }
    
    send_answer();
  }
  
  function _validate_page($params, $id = 0) {
    $errors = array();
    if (!$params['title']) {
      $errors[] = 'Не указано внутреннее имя';
    }
    if (!$this->form_validation->alpha_dash($params['alias'])) {
      $errors[] = 'Некорректный алиас';
    }
    if (!$this->projects_model->is_available_alias($params['project_id'], $params['parent_id'], $params['alias'], $id)) {
      $errors[] = 'Указанный алиас занят';
    }
    return $errors;
  }
  
  /**
   * Редактирование страницы
  **/ 
  function edit_page($id) {
    $page = $this->projects_model->get_page((int)$id);
    if (!$page) {
      show_error('Страница не найдена');
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    $additional_fields = array();
    
    if ($this->main_model->exists_component('menus')) {
      $this->load->model('menus/models/menus_model');
      
      $additional_fields[] = array(
        'view'        => 'fields/select',
        'title'       => 'Принадлежность к меню:',
        'description' => 'Отображение страницы в качестве корневого элемента выбранных меню (дочерние страницы прикрепляются автоматически) при включеной опции "Отображать в меню"',
        'name'        => 'menus[]',
        'options'     => $this->menus_model->get_menus($page['project_id']),
        'value'       => array_simple($this->menus_model->get_page_menus($page['id']), 'menu_id'),
        'multiple'    => TRUE
      );
    }
    
    $additional_fields[] = array(
      'view'    => 'fields/select',
      'title'   => 'Доступ к странице:',
      'name'    => 'access_type_id',
      'options' => $this->projects_model->get_access_types(),
      'value'   => $page['access_type_id']
    );
    $additional_fields[] = array(
      'view'    => 'fields/checkbox',
      'title'   => 'Главная страница',
      'name'    => 'is_main',
      'checked' => $page['is_main']
    );
    $additional_fields[] = array(
      'view'    => 'fields/checkbox',
      'title'   => 'Доступна для поиска',
      'name'    => 'is_searchable',
      'checked' => $page['is_searchable']
    );
    $additional_fields[] = array(
      'view'  => 'fields/checkbox',
      'title' => 'Отображать в меню',
      'name'  => 'in_menu',
      'checked' => $page['in_menu']
    );
    $additional_fields[] = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small accept_i_s',
      'title'    => 'Сохранить',
      'type'     => 'ajax',
      'reaction' => 'reload'
    );
    $additional_fields[] = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small magnifier_i_s',
      'title'    => 'Сохранить и посмотреть',
      'type'     => 'ajax',
      'reaction' => $page['path']
    );
    $additional_fields[] = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small arrow_left_i_s',
      'title'    => 'Сохранить и вернуться',
      'type'     => 'ajax',
      'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование страницы',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_page_process/'. $page['id'] .'/',
        'blocks' => array(
          array(
            'title'  => 'Основные параметры',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Внутреннее имя:',
                'name'        => 'title',
                'id'          => 'projects-page-title',
                'description' => 'Используется только внутри панели администрирования',
                'value'       => $page['title'],
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Алиас:',
                'id'          => 'projects-page-alias',
                'description' => 'Уникальное в рамках раздела имя страницы, являющееся составной частью ее полного адреса',
                'name'        => 'alias',
                'value'       => $page['alias'],
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Основной шаблон:',
                'name'        => 'main_template_id',
                'description' => 'Основной шаблон, "обертка" для шаблона страницы',
                'options'     => $this->templates_model->get_templates(),
                'value'       => $page['main_template_id'],
                'empty'       => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Шаблон страницы:',
                'name'        => 'template_id',
                'description' => 'Управляет отображением содержимого страницы',
                'options'     => $this->templates_model->get_templates(),
                'value'       => $page['template_id'],
                'empty'       => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Переадресация:',
                'description' => 'Адрес для перенаправления при входе на страницу',
                'name'        => 'redirect',
                'value'       => $page['redirect']
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Страница доступна',
                'name'    => 'active',
                'checked' => $page['active']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small magnifier_i_s',
                'title'    => 'Сохранить и посмотреть',
                'type'     => 'ajax',
                'reaction' => $page['path']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small arrow_left_i_s',
                'title'    => 'Сохранить и вернуться',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Наполнение',
            'fields' => array(
              array(
                'view'      => 'fields/editor',
                'title'     => 'Содержимое страницы:',
                'name'      => 'content',
                'toolbar'   => 'Full',
                'languages' => $languages,
                'value'     => $page['params']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small magnifier_i_s',
                'title'    => 'Сохранить и посмотреть',
                'type'     => 'ajax',
                'reaction' => $page['path']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small arrow_left_i_s',
                'title'    => 'Сохранить и вернуться',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Заголовки и SEO',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Название страницы:',
                'name'        => 'name',
                'description' => 'Используется в пунктах меню, "хлебных крошках" и т. п.',
                'languages'   => $languages,
                'value'       => $page['params']
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок страницы:',
                'description' => 'Отображается в заголовке окна браузера (мета-тег TITLE)',
                'name'        => 'title',
                'languages'   => $languages,
                'value'       => $page['params']
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок в теле страницы:',
                'description' => 'Подставляется в тег H1',
                'name'        => 'h1',
                'languages'   => $languages,
                'value'       => $page['params']
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Ключевые слова:',
                'description' => 'Подставляются в мета-тег KEYWORDS',
                'name'        => 'keywords',
                'languages'   => $languages,
                'value'       => $page['params']
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание:',
                'description' => 'Подставляется в мета-тег DESCRIPTION',
                'name'        => 'description',
                'languages'   => $languages,
                'value'       => $page['params']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small magnifier_i_s',
                'title'    => 'Сохранить и посмотреть',
                'type'     => 'ajax',
                'reaction' => $page['path']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small arrow_left_i_s',
                'title'    => 'Сохранить и вернуться',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title'  => 'Дополнительные параметры',
            'fields' => $additional_fields
          )
        )
      ))
    ), TRUE);
  }
  
  function _edit_page_process($id) {
    $page = $this->projects_model->get_page((int)$id);
    if (!$page) {
      send_answer(array('errors' => array('Страница не найдена')));
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    if ($page['parent_id']) {
      $parent = $this->projects_model->get_page($page['parent_id']);
      if (!$parent) {
        send_answer(array('errors' => array('Не найдена родительская страница')));
      }
    }
    
    $params = array(
      'project_id'       => $page['project_id'],
      'parent_id'        => $page['parent_id'],
      'title'            => htmlspecialchars(trim($this->input->post('title'))),
      'alias'            => trim($this->input->post('alias')),
      'main_template_id' => ($this->input->post('main_template_id') ? (int)$this->input->post('main_template_id') : NULL),
      'template_id'      => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL),
      'redirect'         => trim($this->input->post('redirect')),
      'active'           => ($this->input->post('active') ? 1 : 0),
      'is_main'          => ($this->input->post('is_main') ? 1 : 0),
      'is_searchable'    => ($this->input->post('is_searchable') ? 1 : 0),
      'in_menu'          => ($this->input->post('in_menu') ? 1 : 0),
      'access_type_id'   => (int)$this->input->post('access_type_id'),
      'tm'               => date('Y-m-d H:i:s')
    );
    $params['path']   = ($page['parent_id'] ? $parent['path'] : '/') . $params['alias'] .'/';
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'content'     => trim($this->input->post('content_'. $language['name'])),
        'name'        => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'title'       => htmlspecialchars(trim($this->input->post('title_'. $language['name']))),
        'h1'          => htmlspecialchars(trim($this->input->post('h1_'. $language['name']))),
        'keywords'    => htmlspecialchars(trim($this->input->post('keywords_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_page($params, $page['id']);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->projects_model->update_page($page['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if ($params['path'] != $page['path']) {
      if (!$this->projects_model->update_paths_recursively($page['id'])) {
        send_answer(array('errors' => array('Не удалось изменить адреса дочерних страниц')));
      }
    }
    
    if (!$this->main_model->set_params('pages', $page['id'], $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if ($this->main_model->exists_component('menus')) {
      $this->load->model('menus/models/menus_model');
      
      if (!$this->menus_model->set_page_menus($page['id'], $this->input->post('menus'))) {
        send_answer(array('errors' => array('Не удалось сохранить принадлежность к меню')));
      }
    }
    
    if ($params['is_main']) {
      $this->projects_model->reset_main($page['project_id'], $page['id']);
    }
    
    $this->projects_model->create_backup($page, $this->admin_id);
    
    send_answer();
  }
  
  /**
   * Удаление страницы
  **/ 
  function delete_page($id) {
    if (!$this->projects_model->delete_page((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить страницу')));
    }
    
    send_answer();
  }
  
  /**
   * Перемещение страницы
  **/ 
  function move_page() {
    $page_id = (int)str_replace('project-page-', '', $this->input->post('page'));
    $page = $this->projects_model->get_page($page_id);
    if (!$page) {
      send_answer(array('messages' => array('Перемещаемая страница не найдена')));
    }
    
    $dest_id = $this->input->post('dest');
    if (preg_match('/^project-\d+$/', $dest_id)) {
      $project_id = (int)str_replace('project-', '', $dest_id);
      $project = $this->projects_model->get_project($project_id);
      if (!$project) {
        send_answer(array('messages' => array('Целевой проект не найден')));
      }
      $dest_id = NULL;
    } else {
      $dest_id = (int)str_replace('project-page-', '', $dest_id);
      $dest = $this->projects_model->get_page($dest_id);
      if (!$dest) {
        send_answer(array('messages' => array('Целевая страница не найдена')));
      }
      $project_id = NULL;
    }
    
    $placement = trim($this->input->post('placement'));
    
    if (!$this->projects_model->move_page($page_id, $dest_id, $project_id, $placement)) {
      send_answer(array('messages' => array('Не удалось переместить страницу')));
    }
    
    send_answer();
  }
  
  /**
   * Отображение дочерних страниц
  **/ 
  function expand($page_id) {
    $page = $this->projects_model->get_page((int)$page_id);
    if (!$page) {
      send_answer(array('errors' => array('Страница не найдена')));
    }
    
    $this->projects_model->set_page_state($page['id'], $this->admin_id, 1);
    
    $page['pages'] = $this->projects_model->get_pages(FALSE, $page['id']);
    
    send_answer($page);
  }
  
  /**
   * Скрытие дочерних страниц
  **/ 
  function collapse($page_id) {
    $this->projects_model->set_page_state($page_id, $this->admin_id, 0);
    send_answer();
  }
  
  /**
   * Клонирование страниц
  **/
  function clone_page($id) {
    $page = $this->projects_model->get_page((int)$id);
    if (!$page) {
      show_error('Страница не найдена');
    }
    
    $this->projects_model->clone_page($page['id']);
    
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path']);
  }

  /**
   * Просмотр списка резервных копий страниц
  **/
  function page_history($id) {
    $page = $this->projects_model->get_page((int)$id);
    if (!$page) {
      show_error('Страница не найдена');
    }
    
    return $this->render_template('templates/page_history', array(
      'page'    => $page,
      'backups' => $this->projects_model->get_backups($page['id'])
    ));
  }
  
  /**
   * Создание резервной копии страниц
  **/
  function create_backup($page_id) {
    $page = $this->projects_model->get_page((int)$page_id);
    if (!$page) {
      send_answer(array('errors' => array('Страница не найдена')));
    }
    
    if (!$this->projects_model->create_backup($page, $this->admin_id)) {
      send_answer(array('errors' => array('Не удалось создать резервную копию')));
    }
    
    $this->projects_model->update_page($page['id'], array('tm' => date('Y-m-d H:i:s')));
    
    send_answer(array('messages' => array('Резервная копия успешно создана')));
  }
  
  /**
   * Восстановление из резервной копии страниц
  **/
  function restore_backup($id) {
    $backup = $this->projects_model->get_backup((int)$id);
    if (!$backup) {
      send_answer(array('errors' => array('Резервная копия не найдена')));
    }
    
    if (!$this->projects_model->restore_backup($backup['id'])) {
      send_answer(array('errors' => array('Не удалось восстановить резервную копию')));
    }
    
    send_answer(array('messages' => array('Резервная копия успешно восстановлена')));
  }
  
  /**
   * Удаление резервной копии страниц
  **/
  function delete_backup($id) {
    $backup = $this->projects_model->get_backup((int)$id);
    if (!$backup) {
      send_answer(array('errors' => array('Резервная копия не найдена')));
    }
    
    if (!$this->projects_model->delete_backup($backup['id'])) {
      send_answer(array('errors' => array('Не удалось удалить резервную копию')));
    }
    
    send_answer();
  }
  
}