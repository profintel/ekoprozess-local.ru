<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permits_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('permits/models/permits_model');
  }
  
  /**
  * Меню компонента
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'Разграничение доступа',
      'items' => array(
        array(
          'title' => 'Суперпользователи',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'superusers/',
          'class' => 'permits-superusers-icon'
        ),
        array(
          'title' => 'Компоненты и функции',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'components/',
          'class' => 'components-installed-icon'
        ),
        array(
          'title' => 'Группы прав администраторов',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'groups/',
          'class' => ''
        )
      )
    ));
  }
  
  /**
  * Вывод списка установленных компонентов
  */
  function components() {
    return $this->render_template('templates/components', array(
      'components' => $this->components_model->get_components()
    ));
  }
  
  /**
  * Вывод списка методов компонента
  * @param $component_id - идентификатор компонента
  */
  function methods($component_id) {
    $component = $this->components_model->get_component((int)$component_id);
    if (!$component) {
      show_error('Компонент не найден');
    }
    
    return $this->render_template('templates/methods', array(
      'component' => $component,
      'methods'   => $this->_parse_component($component['name'])
    ));
  }
  
  /**
  * Парсит контроллер компонента и возвращает массив вида имя_метода => описание_метода
  * @param $name - имя компонента
  * @return array
  */
  function _parse_component($name) {
    $methods = array();
    
    $code = @file_get_contents(APPPATH .'components/'. $name .'/'. $name .'_admin.php');
    if ($code) {
      preg_match_all('/(?:\/\*\*?\s*\*\s*(.*?)\s*\*(?>\s*@.*?\/|\/)?\s*)?function\s+(.*?)\s*\(/s', $code, $matches, PREG_SET_ORDER);
      
      foreach ($matches as $match) {
        if (!preg_match('/^_/', $match[2])) {
          $methods[$match[2]] = preg_replace('/\s*\*\s*/', ' ', $match[1]);
        }
      }
    }
    
    return $methods;
  }
  
  /**
  * Форма для работы с правами администраторов на полный доступ к компоненту
  * @param $id - идентификатор компонента
  */
  function component($id) {
    $component = $this->components_model->get_component((int)$id);
    if (!$component) {
      show_error('Компонент не найден');
    }
    
    $blocks = $fields = $group_fields = array();
    $groups = $this->permits_model->get_groups();
    foreach ($groups as $group) {
      $group_fields[] = array(
        'view'        => 'fields/checkbox',
        'id'          => 'group_'. $group['id'],
        'name'        => 'groups[]',
        'value'       => $group['id'],
        'title'       => $group['title'],
        'checked'     => $this->_check_group_access($group['id'], $component['name']),
      );
    }

    $admins = $this->admin_model->get_admins();
    foreach ($admins as $admin) {
      $fields[] = array(
        'view'        => 'fields/checkbox',
        'id'          => 'admin_'. $admin['id'],
        'name'        => 'admins[]',
        'value'       => $admin['id'],
        'title'       => $admin['username'],
        'checked'     => $this->_check_access($admin['id'], $component['name']),
        'readonly'    => $admin['superuser'],
        'description' => ($admin['superuser'] ? 'Права суперпользователя' : '')
      );
    }
    
    $fields[] = array(
      'view'     => 'fields/submit',
      'class'    => '',
      'title'    => 'Сохранить',
      'type'     => 'ajax',
      'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'component/'. $component['id'] .'/'
    );
    
    if ($group_fields) {
      $blocks[] = array(
        'title'  => 'Группы администраторов',
        'fields' => $group_fields
      );
    }
    $blocks[] = array(
      'title'  => 'Администраторы',
      'fields' => $fields
    );
    $data =  array(
      'title' => 'Полный доступ к компоненту "'. $component['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'set_component_permits/'. $component['id'] .'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'components/'
    );
    return $this->render_template('admin/inner', $data, TRUE);
  }
  
  /**
  * Установка прав администраторов на полный доступ к компоненту
  * @param $id - идентификатор компонента
  */
  function set_component_permits($id) {
    $component = $this->components_model->get_component((int)$id);
    if (!$component) {
      send_answer(array('errors' => array('Компонент не найден')));
    }
    
    $admins = $this->input->post('admins');
    if (!is_array($admins)) {
      $admins = array();
    }
    if (!$this->permits_model->set_permits($admins, $component['name'])) {
      send_answer(array('errors' => array('Не удалось установить права')));
    }
    
    $groups = $this->input->post('groups');
    if (!is_array($groups)) {
      $groups = array();
    }
    if (!$this->permits_model->set_group_permits($groups, $component['name'])) {
      send_answer(array('errors' => array('Не удалось установить права группе')));
    }
    
    send_answer();
  }
  
  /**
  * Форма для работы с правами администраторов на доступ к методам компонента
  * @param $component_id - идентификатор компонента
  * @param $method - имя метода
  */
  function method($component_id, $method) {
    $component = $this->components_model->get_component((int)$component_id);
    if (!$component) {
      show_error('Компонент не найден');
    }
    
    $blocks = $fields = $group_fields = array();
    $groups = $this->permits_model->get_groups();
    foreach ($groups as $group) {
      $group_fields[] = array(
        'view'        => 'fields/checkbox',
        'id'          => 'group_'. $group['id'],
        'name'        => 'groups[]',
        'value'       => $group['id'],
        'title'       => $group['title'],
        'checked'     => $this->_check_group_access($group['id'], $component['name'], $method),
      );
    }

    $admins = $this->admin_model->get_admins();
    foreach ($admins as $admin) {
      $fields[] = array(
        'view'        => 'fields/checkbox',
        'id'          => 'admin_'. $admin['id'],
        'name'        => 'admins[]',
        'value'       => $admin['id'],
        'title'       => $admin['username'],
        'checked'     => $this->_check_access($admin['id'], $component['name'], $method),
        'readonly'    => $admin['superuser'],
        'description' => ($admin['superuser'] ? 'Права суперпользователя' : '')
      );
    }
    
    $fields[] = array(
      'view'     => 'fields/submit',
      'class'    => '',
      'title'    => 'Сохранить',
      'type'     => 'ajax',
      'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'method/'. $component['id'] .'/'. $method .'/'
    );
    
    if ($group_fields) {
      $blocks[] = array(
        'title'  => 'Группы администраторов',
        'fields' => $group_fields
      );
    }
    $blocks[] = array(
      'title'  => 'Администраторы',
      'fields' => $fields
    );
    $data =  array(
      'title' => 'Доступ к методу "'. $method .'" компонента "'. $component['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'set_method_permits/'. $component['id'] .'/'. $method .'/',
        'blocks' => $blocks
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'methods/'. $component['id'] .'/'
    );
    return $this->render_template('admin/inner', $data, TRUE);
  }
  
  /**
  * Установка прав администраторов на доступ к методу компонента
  * @param $id - идентификатор компонента
  * @param $method - имя метода
  */
  function set_method_permits($id, $method) {
    $component = $this->components_model->get_component((int)$id);
    if (!$component) {
      send_answer(array('errors' => array('Компонент не найден')));
    }
    
    $admins = $this->input->post('admins');
    if (!is_array($admins)) {
      $admins = array();
    }    
    if (!$this->permits_model->set_permits($admins, $component['name'], $method)) {
      send_answer(array('errors' => array('Не удалось установить права')));
    }
    
    $groups = $this->input->post('groups');
    if (!is_array($groups)) {
      $groups = array();
    }
    if (!$this->permits_model->set_group_permits($groups, $component['name'], $method)) {
      send_answer(array('errors' => array('Не удалось установить права группе')));
    }
    
    send_answer();
  }
  
  /**
  * Проверка прав доступа администратора к компоненту/методу компонента
  * @param $admin_id - идентификатор администратора
  * @param $component - имя компонента
  * @param $method - имя метода
  * @return boolean
  */
  function _check_access($admin_id, $component, $method = '') {
    return $this->permits_model->check_access($admin_id, $component, $method);
  }
  
  /**
  * Проверка прав доступа группы администраторов к компоненту/методу компонента
  * @param $group_id - идентификатор группы
  * @param $component - имя компонента
  * @param $method - имя метода
  * @return boolean
  */
  function _check_group_access($group_id, $component, $method = '') {
    return $this->permits_model->check_group_access($group_id, $component, $method);
  }
  
  /**
  * Форма для работы с правами суперпользователя
  */
  function superusers() {
    $fields = array();
    $admins = $this->admin_model->get_admins();
    foreach ($admins as $admin) {
      $fields[] = array(
        'view'    => 'fields/checkbox',
        'id'      => 'admin_'. $admin['id'],
        'name'    => 'admins[]',
        'value'   => $admin['id'],
        'title'   => $admin['username'],
        'checked' => $admin['superuser']
      );
    }
    
    $fields[] = array(
      'view'     => 'fields/submit',
      'class'    => '',
      'title'    => 'Сохранить',
      'type'     => 'ajax',
      'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'superusers/'
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Суперпользователи',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'set_superusers/',
        'blocks' => array(
          array(
            'title'  => 'Администраторы',
            'fields' => $fields
          )
        )
      ))
    ), TRUE);
  }
  
  /**
  * Установка прав суперпользователя
  */
  function set_superusers() {
    $admins = $this->input->post('admins');
    if (!is_array($admins)) {
      $admins = array();
    }
    
    if (!$this->permits_model->set_superusers($admins)) {
      send_answer(array('errors' => array('Не удалось установить права')));
    }
    
    send_answer();
  }

  /**
  * Просмотр групп прав администраторов 
  **/
  function groups() { 
    return $this->render_template('admin/items', array(
      'items'           => $this->permits_model->get_groups(),
      'component_item'  => array('name' => 'group', 'title' => 'группу')
    ));
  }
  
  /**
  * Создание группы
  */  
  function create_group() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание группы',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_group_process/',
        'blocks' => array(
          array(
            'title'   => 'Параметры',
            'fields'   => array(
              array(
                'view'       => 'fields/text',
                'title'     => 'Название группы:',
                'name'       => 'title',
                'maxlength' => 256,
                'description' => '',
                'req'       => true
              ),
              array(
                'view' => 'fields/select',
                'title' => 'Администраторы группы:',
                'name' => 'admins[]',
                'multiple' => true,
                'options' => $this->admin_model->get_admins(),
                'value_field' => 'id',
                'text_field' => 'username'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => '',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. 'groups/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'groups/'
    ), TRUE);
  }
  
  function _create_group_process() {
    $params = array(
      'title' => htmlspecialchars(trim($this->input->post('title'))),
    );
    
    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->permits_model->create_group($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать группу')));
    }

    $admins = $this->input->post('admins');
    if ($admins) {
      if (!$this->permits_model->set_group_admins($id, $admins)) {
        $this->permits_model->delete_group($id);
        exit('Не удалось сохранить администраторов группы');
      }
    }
    
    send_answer();
  }  
  
  function _validate_params($params) {
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано название'; }
    return $errors;
  }  
  
  /**
   * Редактирование группы
   */  
  function edit_group($id) {
    $item = $this->permits_model->get_group($id);
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование группы',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_group_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Параметры',
            'fields'   => array(
              array(
                'view'         => 'fields/text',
                'title'       => 'Название группы:',
                'name'         => 'title',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['title'],
                'req'       => true
              ),
              array(
                'view' => 'fields/select',
                'title' => 'Администраторы группы:',
                'name' => 'admins[]',
                'multiple' => true,
                'options' => $this->admin_model->get_admins(),
                'value_field' => 'id',
                'text_field' => 'username',
                'value' => $item['admins']['ids']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => '',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'groups/'
    ), TRUE);
  }

  function _edit_group_process($id) {
    $params = array(
      'title' => htmlspecialchars(trim($this->input->post('title'))),
    );
    
    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->permits_model->edit_group($id, $params)) {
      send_answer(array('errors' => array('Не удалось отредактировать группу')));
    }

    $admins = $this->input->post('admins');
    if ($admins) {
      if (!$this->permits_model->set_group_admins($id, $admins)) {
        exit('Не удалось сохранить администраторов группы');
      }
    }
    
    send_answer();
  }
  
  /**
   * Удаление группы
   */ 
  function delete_group($id) {
    $this->permits_model->delete_group($id);

    send_answer();
  }
  
}