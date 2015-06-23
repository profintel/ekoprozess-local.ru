<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Templates_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('templates/models/templates_model');
    $this->load->model('components/models/components_model');
  }
    
  /**
   * Просмотр списка шаблонов
  **/ 
  function index() {
    return $this->render_template('templates/index', array(
      'templates' => $this->templates_model->get_templates()
    ));
  }
  
  /**
   * Обновление списка шаблонов
  **/ 
  function install($returning = FALSE) {
    $templates = $this->_get_templates($returning);
    if ($templates === FALSE) {
      if (!$returning) {
        send_answer(array('errors' => array('Ошибка разбора манифеста')));
      }
      return FALSE;
    }
    
    $result = $this->templates_model->install($templates);
    
    if (!$returning) {
      if (!$result) {
        send_answer(array('errors' => array('Не удалось обновить шаблоны')));
      }
      
      send_answer(array('messages' => array('Добавлено: '. $result['added'], 'Удалено: '. $result['deleted'])));
    } else {
      return $result;
    }
  }
  
  function _get_templates($returning = FALSE) {
    $templates = array();
    
    if (file_exists(APPPATH .'views/templates/manifest.xml')) {
      if (!$this->_parse_manifest(APPPATH .'views/templates/manifest.xml', $templates, $returning)) {
        if (!$returning) {
          send_answer(array('errors' => array('Ошибка разбора манифеста')));
        }
        return FALSE;
      }
    }
    
    $files = scandir(APPPATH .'components');
    foreach ($files as $file) {
      if (is_dir(APPPATH .'components/'. $file) && file_exists(APPPATH .'components/'. $file .'/manifest.xml')) {
        $component = $this->components_model->get_component($file);
        if ($component) {
          if (!$this->_parse_manifest(APPPATH .'components/'. $file .'/manifest.xml', $templates, $returning, $component)) {
            if (!$returning) {
              send_answer(array('errors' => array('Ошибка разбора манифеста')));
            }
            return FALSE;
          }
        }
      }
    }
    
    return $templates;
  }
  
  function _parse_manifest($file, &$templates, $returning = FALSE, $component = FALSE) {
    $manifest = @simplexml_load_file($file);
    if (!$manifest) {
      if (!$returning) {
        send_answer(array('errors' => array('Не удалось прочитать файл '. $file)));
      }
      return FALSE;
    }
    
    if (isset($manifest->template)) {
      foreach ($manifest->template as $template) {
        if (!isset($template->path) || !$template->path) {
          if (!$returning) {
            send_answer(array('errors' => array('Отсутствует путь к шаблону в файле '. $file)));
          }
          return FALSE;
        }
        
        if (!$component) {
          if (!file_exists(APPPATH .'views/templates/'. $template->path .'.php')) {
            if (!$returning) {
              send_answer(array('errors' => array('Не найден шаблон '. $template->path)));
            }
            return FALSE;
          }
        } else {
          if (!file_exists(APPPATH .'components/'. $component['name'] .'/'. $template->path .'.php')) {
            if (!$returning) {
              send_answer(array('errors' => array('Не найден шаблон '. $template->path)));
            }
            return FALSE;
          }
        }
        
        $name = end(explode('/', $template->path));
        if (!$this->form_validation->alpha_dash($name)) {
          if (!$returning) {
            send_answer(array('errors' => array('Некорректное имя шаблона '. $name)));
          }
          return FALSE;
        }
        
        if (!isset($template->title) || !$template->title) {
          if (!$returning) {
            send_answer(array('errors' => array('Отсутствует название шаблона '. $name)));
          }
          return FALSE;
        }
        
        $templates[] = array(
          'path'         => (string)$template->path,
          'name'         => $name,
          'title'        => (string)$template->title,
          'description'  => (isset($template->description) ? (string)$template->description : NULL),
          'component_id' => ($component ? $component['id'] : NULL)
        );
      }
    }
    
    return TRUE;
  }  
  
  /**
   * Создание шаблона
  **/ 
  function create() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание шаблона',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_process/',
        'blocks' => array(
          array(
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Системное имя:',
                'description' => 'Уникальное имя шаблона',
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
                'view'  => 'fields/textarea',
                'title' => 'Описание:',
                'name'  => 'description'
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Содержимое шаблона:',
                'description' => 'Шаблон может включать в себя разметку страницы, описание стилей, JavaScript, PHP, а также спецтеги CMS',
                'name'        => 'tpl_content',
                'rows'        => 20
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
      'name'        => trim($this->input->post('name')),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'description' => htmlspecialchars($this->input->post('description')),
      'custom'      => 1
    );
    $params['path'] = 'custom/'. $params['name'];
    
    $errors = $this->_validate($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->templates_model->create_template($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать шаблон')));
    }
    
    $content = $this->input->post('tpl_content');
    if (@file_put_contents(APPPATH .'views/templates/'. $params['path'] .'.php', $content) === FALSE) {
      $this->templates_model->delete_template($id);
      send_answer(array('errors' => array('Не удалось записать файл шаблона')));
    }
    
    send_answer();
  }
  
  function _validate($params, $id = 0) {
    $errors = array();
    if (isset($params['name']) && !$this->form_validation->alpha_dash($params['name'])) {
      $errors[] = 'Некорректное системное имя';
    }
    if (isset($params['name']) && !$this->main_model->is_available('templates', $params['name'], $id)) {
      $errors[] = 'Указанное системное имя занято';
    }
    if (isset($params['title']) && !$params['title']) {
      $errors[] = 'Не указано название';
    }
    return $errors;
  }
  
  /**
   * Редактирование шаблона
  **/ 
  function edit($id) {
    $template = $this->templates_model->get_template((int)$id);
    if (!$template) {
      show_error('Шаблон не найден');
    }
    
    if (!$template['component_id']) {
      $content = @file_get_contents(APPPATH .'views/templates/'. $template['path'] .'.php');
    } else {
      $component = $this->components_model->get_component((int)$template['component_id']);
      $content = @file_get_contents(APPPATH .'components/'. $component['name'] .'/'. $template['path'] .'.php');
    }
    if ($content === FALSE) {
      show_error('Не удалось прочитать файл шаблона');
    }
    
    $fields = array();
    if ($template['custom']) {
      $fields[] = array(
        'view'  => 'fields/text',
        'title' => 'Название:',
        'name'  => 'title',
        'value' => $template['title'],
        'req'   => TRUE
      );
      $fields[] = array(
        'view'  => 'fields/textarea',
        'title' => 'Описание:',
        'name'  => 'description',
        'value' => $template['description']
      );
    }
    $fields[] = array(
      'view'        => 'fields/textarea',
      'title'       => 'Содержимое шаблона:',
      'description' => 'Шаблон может включать в себя разметку страницы, описание стилей, JavaScript, PHP, а также спецтеги CMS',
      'name'        => 'tpl_content',
      'value'       => htmlspecialchars($content),
      'rows'        => 20
    );
    $fields[] = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small accept_i_s',
      'title'    => 'Сохранить изменения',
      'type'     => 'ajax',
      'reaction' => 'reload'
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование шаблона "'. $template['title'] .'"',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_process/'. $template['id'] .'/',
        'blocks' => array(
          array(
            'fields' => $fields
          )
        )
      ))
    ), TRUE);
  }
  
  function _edit_process($id) {
    $template = $this->templates_model->get_template((int)$id);
    if (!$template) {
      send_answer(array('errors' => array('Шаблон не найден')));
    }
    
    $params = array();
    if ($template['custom']) {
      $params['title']       = htmlspecialchars(trim($this->input->post('title')));
      $params['description'] = htmlspecialchars($this->input->post('description'));
    }
    
    $errors = $this->_validate($params, $template['id']);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if ($params && !$this->templates_model->update_template($template['id'], $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    $content = $this->input->post('tpl_content');
    
    if (!$template['component_id']) {
      $result = @file_put_contents(APPPATH .'views/templates/'. $template['path'] .'.php', $content);
    } else {
      $component = $this->components_model->get_component((int)$template['component_id']);
      $result = @file_put_contents(APPPATH .'components/'. $component['name'] .'/'. $template['path'] .'.php', $content);
    }
    if ($result === FALSE) {
      send_answer(array('errors' => array('Не удалось записать файл шаблона')));
    }
    
    send_answer();
  }
  
  /**
   * Удаление шаблона
  **/ 
  function delete($id) {
    $template = $this->templates_model->get_template((int)$id);
    if (!$template) {
      send_answer(array('errors' => array('Шаблон не найден')));
    }
    if (!$template['custom']) {
      send_answer(array('errors' => array('Невозможно удалить встроенный шаблон')));
    }
    
    if (!$this->templates_model->delete_template($template['id'])) {
      send_answer(array('errors' => array('Не удалось удалить шаблон')));
    }
    
    @unlink(APPPATH .'views/templates/'. $template['path'] .'.php');
    
    send_answer();
  }
  
}