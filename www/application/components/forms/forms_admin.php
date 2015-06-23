<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('templates/models/templates_model');
    $this->load->model('forms/models/forms_model');
  }
  
  /**
   * Просмотр меню компонента
  **/ 
  function index() {
    return $this->render_template('templates/index', array(
      'types' => $this->forms_model->get_forms_types(),
      'forms' => $this->forms_model->get_forms()
    ));
  }
  
  /**
   * Создание типа форм
  **/ 
  function create_type() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание типа форм',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_type_process/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'req'   => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Способ кодирования:',
                'name'        => 'enctype',
                'description' => 'enctype',
                'value'       => 'multipart/form-data',
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Метод отправки:',
                'options'     => array(
                  array('id' => 'POST', 'title' => 'POST'),
                  array('id' => 'GET',  'title' => 'GET')
                ),
                'description' => 'method',
                'name'        => 'method'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Адрес обработки:',
                'description' => 'action',
                'name'        => 'action'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Целевое окно/фрейм:',
                'description' => 'target',
                'name'        => 'target'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Действие при отправке:',
                'description' => 'onSubmit',
                'name'        => 'onsubmit'
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Описание:',
                'name'  => 'description'
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
  
  function _create_type_process() {
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'action'      => htmlspecialchars(trim($this->input->post('action'))),
      'method'      => $this->input->post('method'),
      'enctype'     => htmlspecialchars(trim($this->input->post('enctype'))),
      'target'      => htmlspecialchars(trim($this->input->post('target'))),
      'onsubmit'    => htmlspecialchars($this->input->post('onsubmit')),
      'description' => htmlspecialchars($this->input->post('description'))
    );
    
    $errors = $this->_validate_type($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->forms_model->create_forms_type($params)) {
      send_answer(array('errors' => array('Не удалось создать тип форм')));
    }
    
    send_answer();
  }
  
  function _validate_type($params) {
    $errors = array();
    if (!$params['title']) {
      $errors[] = 'Не указано название';
    }
    if (!$params['enctype']) {
      $errors[] = 'Не указан способ кодирования данных';
    }
    if (!preg_match('/^(POST|GET)$/', $params['method'])) {
      $errors[] = 'Некорректный метод отправки';
    }
    return $errors;
  }
  
  /**
   * Редактирование типа форм
  **/ 
  function edit_type($id) {
    $type = $this->forms_model->get_forms_type((int)$id);
    if (!$type) {
      show_error('Тип не найден');
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование типа форм "'. $type['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_type_process/'. $type['id'] .'/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'value' => $type['title'],
                'req'   => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Способ кодирования:',
                'name'        => 'enctype',
                'description' => 'enctype',
                'value'       => $type['enctype'],
                'req'         => TRUE
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Метод отправки:',
                'options'     => array(
                  array('id' => 'POST', 'title' => 'POST'),
                  array('id' => 'GET',  'title' => 'GET')
                ),
                'value'       => $type['method'],
                'description' => 'method',
                'name'        => 'method'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Адрес обработки:',
                'description' => 'action',
                'value'       => $type['action'],
                'name'        => 'action'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Целевое окно/фрейм:',
                'description' => 'target',
                'value'       => $type['target'],
                'name'        => 'target'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Действие при отправке:',
                'description' => 'onSubmit',
                'value'       => $type['onsubmit'],
                'name'        => 'onsubmit'
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Описание:',
                'value' => $type['description'],
                'name'  => 'description'
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
  
  function _edit_type_process($id) {
    $type = $this->forms_model->get_forms_type((int)$id);
    if (!$type) {
      send_answer(array('errors' => array('Тип не найден')));
    }
    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'action'      => htmlspecialchars(trim($this->input->post('action'))),
      'method'      => $this->input->post('method'),
      'enctype'     => htmlspecialchars(trim($this->input->post('enctype'))),
      'target'      => htmlspecialchars(trim($this->input->post('target'))),
      'onsubmit'    => htmlspecialchars($this->input->post('onsubmit')),
      'description' => htmlspecialchars($this->input->post('description'))
    );
    
    $errors = $this->_validate_type($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->forms_model->update_forms_type($type['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    send_answer();
  }
  
  /**
   * Удаление типа форм
  **/ 
  function delete_type($id) {
    $type = $this->forms_model->get_forms_type((int)$id);
    if (!$type) {
      send_answer(array('errors' => array('Тип не найден')));
    }
    
    if (!$this->forms_model->delete_forms_type($type['id'])) {
      send_answer(array('errors' => array('Не удалось удалить тип')));
    }
    
    send_answer();
  }
  
  /**
   * Создание формы
  **/ 
  function create() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание формы',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_process/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'    => 'fields/select',
                'title'   => 'Тип:',
                'name'    => 'type_id',
                'options' => $this->forms_model->get_forms_types(),
                'req'     => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Системное имя:',
                'description' => 'Уникальное имя формы',
                'name'        => 'name',
                'req'         => TRUE
              ),
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'req'   => TRUE
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон формы:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'empty'   => TRUE
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Описание:',
                'name'  => 'description'
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
      'type_id'     => (int)$this->input->post('type_id'),
      'name'        => trim($this->input->post('name')),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'description' => htmlspecialchars($this->input->post('description')),
      'template_id' => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL)
    );
    
    $errors = $this->_validate($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->forms_model->create_form($params)) {
      send_answer(array('errors' => array('Не удалось создать форму')));
    }
    
    send_answer();
  }
  
  function _validate($params, $id = 0) {
    $errors = array();
    if (!$params['type_id']) {
      $errors[] = 'Не выбран тип';
    }
    if (!$this->form_validation->alpha_dash($params['name'])) {
      $errors[] = 'Некорректное системное имя';
    }
    if (!$this->main_model->is_available('forms', $params['name'], $id)) {
      $errors[] = 'Указанное системное имя занято';
    }
    if (!$params['title']) {
      $errors[] = 'Не указано название';
    }
    return $errors;
  }
  
  /**
   * Редактирование формы
  **/
  function edit($id) {
    $form = $this->forms_model->get_form((int)$id);
    if (!$form) {
      show_error('Форма не найдена');
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование формы "'. $form['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_process/'. $form['id'] .'/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'    => 'fields/select',
                'title'   => 'Тип:',
                'name'    => 'type_id',
                'options' => $this->forms_model->get_forms_types(),
                'value'   => $form['type_id'],
                'req'     => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Системное имя:',
                'description' => 'Уникальное имя формы',
                'name'        => 'name',
                'value'       => $form['name'],
                'req'         => TRUE
              ),
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'value' => $form['title'],
                'req'   => TRUE
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон формы:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $form['template_id'],
                'empty'   => TRUE
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Описание:',
                'name'  => 'description',
                'value' => $form['description']
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
    $form = $this->forms_model->get_form((int)$id);
    if (!$form) {
      send_answer(array('errors' => array('Форма не найдена')));
    }
    
    $params = array(
      'type_id'     => (int)$this->input->post('type_id'),
      'name'        => trim($this->input->post('name')),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'description' => htmlspecialchars($this->input->post('description')),
      'template_id' => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL)
    );
    
    $errors = $this->_validate($params, $form['id']);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->forms_model->update_form($form['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    send_answer();
  }
  
  /**
   * Удаление формы
  **/
  function delete($id) {
    $form = $this->forms_model->get_form((int)$id);
    if (!$form) {
      send_answer(array('errors' => array('Форма не найдена')));
    }
    
    if (!$this->forms_model->delete_form($form['id'])) {
      send_answer(array('errors' => array('Не удалось удалить форму')));
    }
    
    send_answer();
  }
  
  /**
   * Просмотр полей формы
  **/
  function fields($form_id) {
    $form = $this->forms_model->get_form((int)$form_id);
    if (!$form) {
      show_404();
    }
    
    return $this->render_template('templates/fields', array(
      'form'   => $form,
      'fields' => $this->forms_model->get_forms_fields($form['id'])
    ));
  }
  
  function _get_field_struct($field_type, $struct_name, $languages, $field = FALSE) {
    $structs = array(
      'text'     => '_get_textField_',
      'password' => '_get_textField_',
      'hidden'   => '_get_textField_',
      'textarea' => '_get_textareaField_',
      'captcha'  => '_get_captchaField_',
      'checkbox' => '_get_checkboxField_',
      'radio'    => '_get_checkboxField_',
      'select'   => '_get_selectField_',
      'submit'   => '_get_submitField_'
    );
    
    if (!isset($structs[$field_type]) || !method_exists($this, $structs[$field_type] . $struct_name)) {
      return array();
    }
    
    return $this->{$structs[$field_type] . $struct_name}($languages, $field);
  }
  
  /**
   * Создание поля формы
  **/
  function create_field($form_id, $type = 'text') {
    $form = $this->forms_model->get_form((int)$form_id);
    if (!$form) {
      show_404();
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    $add_fields = $this->_get_field_struct($type, 'fields', $languages);
    
    $add_block = ($add_fields ?
      array(
        'title'  => 'Частные параметры',
        'fields' => array_merge(
          $add_fields,
          array(
            array(
              'view'     => 'fields/submit',
              'class'    => 'icon_small add_i_s',
              'title'    => 'Создать',
              'type'     => 'ajax',
              'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $form['id'] .'/'
            )
          )
        )
      )
      :
      FALSE
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление поля к форме "'. $form['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_field_process/'. $form['id'] .'/'. $type .'/',
        'blocks' => array(
          array(
            'title'  => 'Общие параметры',
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Внутреннее имя:',
                'name'  => 'title',
                'req'   => TRUE
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Внутреннее описание:',
                'name'  => 'description'
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон поля:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'empty'   => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок поля:',
                'name'        => 'title',
                'description' => 'Отображается пользователю',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание поля:',
                'name'        => 'description',
                'description' => 'Отображается пользователю',
                'languages'   => $languages
              ),
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Обязательно для заполнения:',
                'name'  => 'required'
              ),
              array(
                'view'        => 'fields/checkbox',
                'title'       => 'Используется:',
                'description' => 'Снимите флажок, чтобы иключить поле из формы',
                'name'        => 'active',
                'checked'     => TRUE
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small add_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $form['id'] .'/'
              )
            )
          ),
          array(
            'title'  => 'Общие атрибуты',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Идентификатор:',
                'description' => 'id',
                'name'        => 'attr_id'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Имя:',
                'description' => 'name',
                'name'        => 'attr_name'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Класс:',
                'description' => 'class',
                'name'        => 'attr_class'
              ),
                array(
                    'view'        => 'fields/text',
                    'title'       => 'Placeholder:',
                    'description' => 'placeholder',
                    'name'        => 'attr_placeholder'
                ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Индекс:',
                'description' => 'tabindex',
                'name'        => 'attr_tabindex'
              ),
              array(
                'view'        => 'fields/checkbox',
                'title'       => 'Недоступно для ввода:',
                'description' => 'disabled',
                'name'        => 'attr_disabled'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small add_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $form['id'] .'/'
              )
            )
          ),
          $add_block
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $form['id'] .'/'
    ), TRUE);
  }
  
  function _get_textField_fields($languages, $field) {
    return array(
      $this->_get_textfield_value($languages, $field),
      $this->_get_textfield_maxlength($languages, $field),
      $this->_get_textfield_autofocus($languages, $field)
    );
  }
  
  function _get_selectField_fields($languages, $field) {
    return array(
      $this->_get_textfield_value($languages, $field),
      array(
        'view'        => 'fields/text',
        'title'       => 'Функция при смене значения:',
        'name'        => 'onchange',
        'value'       => (isset($field['params']['onchange']) ? $field['params']['onchange'] : NULL),
        'description' => 'onChange'
      ),
      $this->_get_select_tablename($languages, $field),
      $this->_get_select_value_field($languages, $field),
      $this->_get_select_text_field($languages, $field),
      $this->_get_select_options($languages, $field),
      array(
        'view'        => 'fields/checkbox',
        'title'       => 'Множественный выбор:',
        'name'        => 'multiple',
        'checked'     => (isset($field['params']['multiple']) ? $field['params']['multiple'] : NULL),
        'description' => 'multiple'
      ),
      array(
        'view'    => 'fields/checkbox',
        'title'   => 'Наличие пустой опции:',
        'checked' => (isset($field['params']['empty']) ? $field['params']['empty'] : NULL),
        'name'    => 'empty'
      )
    );
  }
  
  function _get_checkboxField_fields($languages, $field) {
    return array(
      $this->_get_textfield_value($languages, $field),
      $this->_get_select_tablename($languages, $field),
      $this->_get_select_value_field($languages, $field),
      $this->_get_select_text_field($languages, $field),
      $this->_get_select_options($languages, $field)
    );
  }
  
  function _get_textareaField_fields($languages, $field) {
    return array(
      $this->_get_textfield_value($languages, $field),
      array(
        'view'        => 'fields/text',
        'title'       => 'Количество столбцов:',
        'name'        => 'cols',
        'value'       => (isset($field['params']['cols']) ? $field['params']['cols'] : NULL),
        'description' => 'cols'
      ),
      array(
        'view'        => 'fields/text',
        'title'       => 'Количество строк:',
        'name'        => 'rows',
        'value'       => (isset($field['params']['rows']) ? $field['params']['rows'] : NULL),
        'description' => 'rows'
      ),
      $this->_get_textfield_autofocus($languages, $field)
    );
  }
  
  function _get_captchaField_fields($languages, $field) {
    return array(
      $this->_get_textfield_autofocus($languages, $field),
      array(
        'view'        => 'fields/text',
        'title'       => 'Цвет фона:',
        'name'        => 'bgcolor',
        'value'       => (isset($field['params']['bgcolor']) ? $field['params']['bgcolor'] : NULL),
        'description' => 'В шестнадцатиричном формате (по умолчанию: белый)'
      ),
      array(
        'view'        => 'fields/text',
        'title'       => 'Цвет символов:',
        'name'        => 'textcolor',
        'value'       => (isset($field['params']['textcolor']) ? $field['params']['textcolor'] : NULL),
        'description' => 'В шестнадцатиричном формате (по умолчанию: черный)'
      ),
      array(
        'view'        => 'fields/text',
        'title'       => 'Количество символов:',
        'name'        => 'symbols',
        'value'       => (isset($field['params']['symbols']) ? $field['params']['symbols'] : NULL),
        'description' => 'По умолчанию: 6'
      ),
      array(
        'view'        => 'fields/text',
        'title'       => 'Ширина изображения:',
        'name'        => 'width',
        'value'       => (isset($field['params']['width']) ? $field['params']['width'] : NULL),
        'description' => 'По умолчанию: количество символов * 14'
      ),
      array(
        'view'        => 'fields/text',
        'title'       => 'Высота изображения:',
        'name'        => 'height',
        'value'       => (isset($field['params']['height']) ? $field['params']['height'] : NULL),
        'description' => 'По умолчанию: определяется автоматически'
      )
    );
  }
  
  function _get_submitField_fields($languages, $field) {
    return array(
      array(
        'view'    => 'fields/select',
        'title'   => 'Способ отправки формы:',
        'name'    => 'type',
        'value'   => (isset($field['params']['type']) ? $field['params']['type'] : NULL),
        'options' => array(
          array('id' => 'ajax', 'title' => 'Асинхронный (без перезагрузки страницы)'),
          array('id' => 'sync', 'title' => 'Синхронный (с перезагрузкой страницы)')
        )
      ),
      array(
        'view'        => 'fields/select',
        'title'       => 'Действие в случае успеха:',
        'name'        => 'success_handler_type',
        'value'       => (isset($field['params']['success_handler_type']) ? $field['params']['success_handler_type'] : NULL),
        'description' => 'Работает только для асинхроного способа отправки',
        'empty'       => TRUE,
        'options'     => array(
          array('id' => 'reload',   'title' => 'Перезагрузка страницы'),
          array('id' => 'url',      'title' => 'Переход по адресу'),
          array('id' => 'function', 'title' => 'Javascipt-функция')
        )
      ),
      array(
        'view'        => 'fields/text',
        'title'       => 'Адрес/Имя функции:',
        'name'        => 'success_handler_value',
        'value'       => (isset($field['params']['success_handler_value']) ? $field['params']['success_handler_value'] : NULL),
        'description' => 'Для действий "Переход по адресу" и "Javascipt-функция" в случае успеха'
      ),
      array(
        'view'        => 'fields/select',
        'title'       => 'Действие в случае ошибки:',
        'name'        => 'failure_handler_type',
        'value'       => (isset($field['params']['failure_handler_type']) ? $field['params']['failure_handler_type'] : NULL),
        'description' => 'Работает только для асинхроного способа отправки',
        'empty'       => TRUE,
        'options'     => array(
          array('id' => 'alert',    'title' => 'Модальное окно'),
          array('id' => 'function', 'title' => 'Javascipt-функция')
        )
      ),
      array(
        'view'        => 'fields/text',
        'title'       => 'Имя функции:',
        'name'        => 'failure_handler_value',
        'value'       => (isset($field['params']['failure_handler_value']) ? $field['params']['failure_handler_value'] : NULL),
        'description' => 'Для действия "Javascipt-функция" в случае ошибки'
      )
    );
  }
  
  function _get_textfield_value($languages, $field) {
    return array(
      'view'        => 'fields/text',
      'title'       => 'Значение по умолчанию:',
      'name'        => 'value',
      'description' => 'value',
      'value'       => $field['params'],
      'languages'   => $languages
    );
  }
  
  function _get_textfield_maxlength($languages, $field) {
    return array(
      'view'        => 'fields/text',
      'title'       => 'Максимальное количество символов:',
      'name'        => 'maxlength',
      'value'       => (isset($field['params']['maxlength']) ? $field['params']['maxlength'] : NULL),
      'description' => 'maxlength'
    );
  }
  
  function _get_textfield_autofocus($languages, $field) {
    return array(
      'view'        => 'fields/checkbox',
      'title'       => 'Автофокус:',
      'name'        => 'autofocus',
      'checked'     => (isset($field['params']['autofocus']) ? $field['params']['autofocus'] : NULL),
      'description' => 'autofocus'
    );
  }
  
  function _get_select_tablename($languages, $field) {
    return array(
      'view'        => 'fields/text',
      'title'       => 'Имя таблицы данных:',
      'name'        => 'table',
      'value'       => (isset($field['params']['table']) ? $field['params']['table'] : NULL),
      'description' => 'Список значений будет автоматически сформирован на основании данных указанной таблицы'
    );
  }
  
  function _get_select_value_field($languages, $field) {
    return array(
      'view'        => 'fields/text',
      'title'       => 'Поле значения:',
      'name'        => 'value_field',
      'value'       => $field['params'],
      'description' => 'Используется только для табличных данных',
      'languages'   => $languages
    );
  }
  
  function _get_select_text_field($languages, $field) {
    return array(
      'view'        => 'fields/text',
      'title'       => 'Поле названия:',
      'name'        => 'text_field',
      'value'       => $field['params'],
      'description' => 'Используется только для табличных данных',
      'languages'   => $languages
    );
  }
  
  function _get_select_options($languages, $field) {
    return array(
      'view'        => 'fields/textarea',
      'title'       => 'Строки данных:',
      'name'        => 'strings',
      'value'       => $field['params'],
      'rows'        => 5,
      'description' => 'Если нет возможности использовать табличные данные, составьте список вручную (каждый элемент списка на отдельной строке)',
      'languages'   => $languages
    );
  }
  
  function _create_field_process($form_id, $type) {
    $form = $this->forms_model->get_form((int)$form_id);
    if (!$form) {
      show_error('Форма не найдена');
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'form_id'       => $form['id'],
      'type'          => $type,
      'title'         => htmlspecialchars(trim($this->input->post('title'))),
      'attr_id'       => trim($this->input->post('attr_id')),
      'attr_name'     => trim($this->input->post('attr_name')),
      'attr_class'    => trim($this->input->post('attr_class')),
      'attr_placeholder'    => trim($this->input->post('attr_placeholder')),
      'attr_disabled' => ($this->input->post('attr_disabled') ? 1 : 0),
      'attr_tabindex' => ($this->input->post('attr_tabindex') ? (int)$this->input->post('attr_tabindex') : NULL),
      'description'   => htmlspecialchars($this->input->post('description')),
      'template_id'   => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL),
      'required'      => ($this->input->post('required') ? 1 : 0),
      'active'        => ($this->input->post('active') ? 1 : 0)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'title'       => htmlspecialchars(trim($this->input->post('title_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_field($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $field_params = $this->_get_field_struct($type, 'params', $languages);
    
    $id = $this->forms_model->create_forms_field($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать поле')));
    }
    
    if (!$this->main_model->set_params('forms_fields', $id, $multiparams)) {
      $this->forms_model->delete_forms_field($id);
      send_answer(array('errors' => array('Не удалось сохранить общие параметры')));
    }
    
    if (!$this->main_model->set_params('forms_fields', $id, $field_params)) {
      $this->forms_model->delete_forms_field($id);
      send_answer(array('errors' => array('Не удалось сохранить частные параметры')));
    }
    
    send_answer();
  }
  
  function _validate_field($params) {
    $errors = array();
    if (!$params['title']) {
      $errors[] = 'Не указано внутреннее имя';
    }
    if ($params['attr_id'] && !$this->form_validation->alpha_dash($params['attr_id'])) {
      $errors[] = 'Некорректный атрибут "Идентификатор"';
    }
    if ($params['attr_name'] && !$this->form_validation->alpha_dash($params['attr_name'])) {
      $errors[] = 'Некорректный атрибут "Имя"';
    }
    if ($params['attr_class'] && !$this->form_validation->alpha_dash($params['attr_class'])) {
      $errors[] = 'Некорректный атрибут "Класс"';
    }
    return $errors;
  }
  
  function _get_textField_params($languages, $field) {
    $params = array(
      'maxlength' => ($this->input->post('maxlength') ? abs((int)$this->input->post('maxlength')) : NULL),
      'autofocus' => ($this->input->post('autofocus') ? 1 : 0)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'value' => htmlspecialchars(trim($this->input->post('value_'. $language['name'])))
      );
    }
    
    return array_merge($params, $multiparams);
  }
  
  function _get_textareaField_params($languages, $field) {
    $params = array(
      'cols'      => ($this->input->post('cols') ? abs((int)$this->input->post('cols')) : NULL),
      'rows'      => ($this->input->post('rows') ? abs((int)$this->input->post('rows')) : NULL),
      'autofocus' => ($this->input->post('autofocus') ? 1 : 0)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'value' => htmlspecialchars(trim($this->input->post('value_'. $language['name'])))
      );
    }
    
    return array_merge($params, $multiparams);
  }
  
  function _get_captchaField_params($languages, $field) {
    $params = array(
      'bgcolor'   => trim($this->input->post('bgcolor')),
      'textcolor' => trim($this->input->post('textcolor')),
      'symbols'   => ($this->input->post('symbols') ? abs((int)$this->input->post('symbols')) : NULL),
      'width'     => ($this->input->post('width') ? abs((int)$this->input->post('width')) : NULL),
      'height'    => ($this->input->post('height') ? abs((int)$this->input->post('height')) : NULL),
      'autofocus' => ($this->input->post('autofocus') ? 1 : 0)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'value' => htmlspecialchars(trim($this->input->post('value_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_captchaField($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    return array_merge($params, $multiparams);
  }
  
  function _validate_captchaField($params) {
    $errors = array();
    if ($params['bgcolor'] && !$this->form_validation->hex_color($params['bgcolor'])) {
      $errors[] = 'Некорректный цвет фона';
    }
    if ($params['textcolor'] && !$this->form_validation->hex_color($params['textcolor'])) {
      $errors[] = 'Некорректный цвет текста';
    }
    return $errors;
  }
  
  function _get_checkboxField_params($languages, $field) {
    $params = array(
      'table' => trim($this->input->post('table'))
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'value'       => htmlspecialchars(trim($this->input->post('value_'. $language['name']))),
        'value_field' => htmlspecialchars(trim($this->input->post('value_field_'. $language['name']))),
        'text_field'  => htmlspecialchars(trim($this->input->post('text_field_'. $language['name']))),
        'strings'     => htmlspecialchars(trim($this->input->post('strings_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_checkboxField($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    return array_merge($params, $multiparams);
  }
  
  function _validate_checkboxField($params) {
    $errors = array();
    if ($params['table'] && !$this->main_model->table_exists($params['table'])) {
      $errors[] = 'Таблицы с указанным именем не существует';
    }
    return $errors;
  }
  
  function _get_selectField_params($languages, $field) {
    $params = array(
      'table'    => trim($this->input->post('table')),
      'onchange' => trim($this->input->post('onchange')),
      'multiple' => ($this->input->post('multiple') ? 1 : 0),
      'empty'    => ($this->input->post('empty') ? 1 : 0)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'value'       => htmlspecialchars(trim($this->input->post('value_'. $language['name']))),
        'value_field' => htmlspecialchars(trim($this->input->post('value_field_'. $language['name']))),
        'text_field'  => htmlspecialchars(trim($this->input->post('text_field_'. $language['name']))),
        'strings'     => htmlspecialchars(trim($this->input->post('strings_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_selectField($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    return array_merge($params, $multiparams);
  }
  
  function _validate_selectField($params) {
    $errors = array();
    if ($params['table'] && !$this->main_model->table_exists($params['table'])) {
      $errors[] = 'Таблицы с указанным именем не существует';
    }
    return $errors;
  }
  
  function _get_submitField_params($languages, $field) {
    $params = array(
      'type'                  => trim($this->input->post('type')),
      'success_handler_type'  => trim($this->input->post('success_handler_type')),
      'success_handler_value' => htmlspecialchars(trim($this->input->post('success_handler_value'))),
      'failure_handler_type'  => trim($this->input->post('failure_handler_type')),
      'failure_handler_value' => htmlspecialchars(trim($this->input->post('failure_handler_value'))),
    );
    
    return $params;
  }
  
  /**
   * Редактирование поля формы
  **/
  function edit_field($id) {
    $field = $this->forms_model->get_forms_field((int)$id);
    if (!$field) {
      show_error('Поле не найдено');
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    $add_fields = $this->_get_field_struct($field['type'], 'fields', $languages, $field);
    
    $add_block = ($add_fields ?
      array(
        'title'  => 'Частные параметры',
        'fields' => array_merge(
          $add_fields,
          array(
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
      :
      FALSE
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование поля "'. $field['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_field_process/'. $field['id'] .'/',
        'blocks' => array(
          array(
            'title'  => 'Общие параметры',
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Внутреннее имя:',
                'name'  => 'title',
                'value' => $field['title'],
                'req'   => TRUE
              ),
              array(
                'view'  => 'fields/textarea',
                'title' => 'Внутреннее описание:',
                'value' => $field['description'],
                'name'  => 'description'
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон поля:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $field['template_id'],
                'empty'   => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок поля:',
                'name'        => 'title',
                'description' => 'Отображается пользователю',
                'value'       => $field['params'],
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание поля:',
                'name'        => 'description',
                'description' => 'Отображается пользователю',
                'value'       => $field['params'],
                'languages'   => $languages
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Обязательно для заполнения:',
                'checked' => $field['required'],
                'name'    => 'required'
              ),
              array(
                'view'        => 'fields/checkbox',
                'title'       => 'Используется:',
                'description' => 'Снимите флажок, чтобы иключить поле из формы',
                'name'        => 'active',
                'checked'     => $field['active']
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
            'title'  => 'Общие атрибуты',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Идентификатор:',
                'description' => 'id',
                'value'       => $field['attr_id'],
                'name'        => 'attr_id'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Имя:',
                'description' => 'name',
                'value'       => $field['attr_name'],
                'name'        => 'attr_name'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Класс:',
                'description' => 'class',
                'value'       => $field['attr_class'],
                'name'        => 'attr_class'
              ),
                array(
                    'view'        => 'fields/text',
                    'title'       => 'Placeholder:',
                    'description' => 'placeholder',
                    'value'       => $field['attr_placeholder'],
                    'name'        => 'attr_placeholder'
                ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Индекс:',
                'description' => 'tabindex',
                'value'       => $field['attr_tabindex'],
                'name'        => 'attr_tabindex'
              ),
              array(
                'view'        => 'fields/checkbox',
                'title'       => 'Недоступно для ввода:',
                'description' => 'disabled',
                'checked'     => $field['attr_disabled'],
                'name'        => 'attr_disabled'
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
          $add_block
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $field['form_id'] .'/'
    ), TRUE);
  }
  
  function _edit_field_process($id) {
    $field = $this->forms_model->get_forms_field((int)$id);
    if (!$field) {
      send_answer(array('errors' => array('Поле не найдено')));
    }
    
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'title'         => htmlspecialchars(trim($this->input->post('title'))),
      'attr_id'       => trim($this->input->post('attr_id')),
      'attr_name'     => trim($this->input->post('attr_name')),
      'attr_class'    => trim($this->input->post('attr_class')),
      'attr_placeholder'    => trim($this->input->post('attr_placeholder')),
      'attr_disabled' => ($this->input->post('attr_disabled') ? 1 : 0),
      'attr_tabindex' => ($this->input->post('attr_tabindex') ? (int)$this->input->post('attr_tabindex') : NULL),
      'description'   => htmlspecialchars($this->input->post('description')),
      'template_id'   => ($this->input->post('template_id') ? (int)$this->input->post('template_id') : NULL),
      'required'      => ($this->input->post('required') ? 1 : 0),
      'active'        => ($this->input->post('active') ? 1 : 0)
    );
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'title'       => htmlspecialchars(trim($this->input->post('title_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))
      );
    }
    
    $errors = $this->_validate_field($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $field_params = $this->_get_field_struct($field['type'], 'params', $languages);
    
    if (!$this->forms_model->update_forms_field($field['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if (!$this->main_model->set_params('forms_fields', $field['id'], $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить общие параметры')));
    }
    
    if (!$this->main_model->set_params('forms_fields', $field['id'], $field_params)) {
      send_answer(array('errors' => array('Не удалось сохранить частные параметры')));
    }
    
    send_answer();
  }
  
  /**
   * Активация поля формы
  **/
  function enable_field($id) {
    $field = $this->forms_model->get_forms_field((int)$id);
    if (!$field) {
      show_error('Поле не найдено');
    }
    
    $this->forms_model->update_forms_field($field['id'], array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $field['form_id'] .'/');
  }
  
  /**
   * Деактивация поля формы
  **/
  function disable_field($id) {
    $field = $this->forms_model->get_forms_field((int)$id);
    if (!$field) {
      show_error('Поле не найдено');
    }
    
    $this->forms_model->update_forms_field($field['id'], array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $field['form_id'] .'/');
  }
  
  /**
   * Сдвиг поля формы вверх
  **/
  function up_field($id) {
    $field = $this->forms_model->get_forms_field((int)$id);
    if (!$field) {
      show_error('Поле не найдено');
    }
    
    $this->forms_model->up_forms_field($field['id']);
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $field['form_id'] .'/');
  }
  
  /**
   * Сдвиг поля формы вниз
  **/
  function down_field($id) {
    $field = $this->forms_model->get_forms_field((int)$id);
    if (!$field) {
      show_error('Поле не найдено');
    }
    
    $this->forms_model->down_forms_field($field['id']);
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] .'fields/'. $field['form_id'] .'/');
  }
  
  /**
   * Удаление поля формы
  **/
  function delete_field($id) {
    $field = $this->forms_model->get_forms_field((int)$id);
    if (!$field) {
      send_answer(array('errors' => array('Поле не найдено')));
    }
    
    if (!$this->forms_model->delete_forms_field($field['id'])) {
      send_answer(array('errors' => array('Не удалось удалить поле')));
    }
    
    send_answer();
  }
  
}