<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Components_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('components/models/components_model');
  }
  
  /**
  * Просмотр меню компонентов
  **/
  function index() {
    $installed = $this->components_model->get_components();
    
    return $this->render_template('templates/index', array(
      'installed'   => $installed,
      'uninstalled' => $this->_get_components($installed)
    ));
  }
  
  /**
  * Просмотр установленного компонента
  **/
  function installed($component) {
    $item = $this->components_model->get_component($component);
    
    return $this->render_template('templates/installed', array(
      'item'   => $item,
      'childs' => $this->components_model->get_components($item['name'])
    ));
  }
  
  /**
  * Просмотр доступного компонента
  **/
  function uninstalled($component) {
    if (!file_exists(APPPATH .'components/'. $component .'/manifest.xml')) {
      show_error('Компонент не найден');
    }
    
    return $this->render_template('templates/uninstalled', array(
      'item' => $this->_parse_component($component)
    ));
  }
  
  /**
  * Установка главного компонента
  **/ 
  function set_main($component, $returning = FALSE) {
    $item = $this->components_model->get_component($component);
    if (!$item) {
      if (!$returning) {
        show_error('Компонент не найден');
      } else {
        return FALSE;
      }
    }
    
    $this->components_model->set_main($item['id']);
    
    if (!$returning) {
      header('Location: '. $this->lang_prefix .'/admin'. $this->component['path'] .'installed/'. $item['id'] .'/');
    } else {
      return TRUE;
    }
  }
  
  function _get_components($installed) {
    $components = array();
    $installed = array_simple($installed, 'name');
    $files = scandir(APPPATH .'components');
    
    foreach ($files as $file) {
      if (
        is_dir(APPPATH .'components/'. $file)
        && file_exists(APPPATH .'components/'. $file .'/manifest.xml')
        && !in_array($file, $installed)
      ) {
        $components[] = $this->_parse_component($file);
      }
    }
    
    return $components;
  }
  
  function _parse_component($file) {
    $component = array(
      'name'   => mb_strtolower($file),
      'errors' => array()
    );
    
    if (!$this->form_validation->alpha_dash($component['name'])) {
      $component['errors'][] = 'Некорректное имя компонента';
    }
    
    $manifest = @simplexml_load_file(APPPATH .'components/'. $file .'/manifest.xml');
    if (!$manifest) {
      $component['errors'][] = 'Не удалось прочитать файл манифеста';
      return $component;
    }
    
    if (isset($manifest->information->title) && $manifest->information->title) {
      $component['title'] = (string)$manifest->information->title;
    } else {
      $component['errors'][] = 'Отсутствует название компонента';
    }
    if (isset($manifest->information->author)) {
      $component['author'] = (string)$manifest->information->author;
    }
    if (isset($manifest->information->version)) {
      $component['version'] = (float)$manifest->information->version;
    }
    if (isset($manifest->information->description)) {
      $component['description'] = (string)$manifest->information->description;
    }
    
    if (isset($manifest->install->parent)) {
      $component['parent'] = (string)$manifest->install->parent;
    }
    if (isset($manifest->install->menu)) {
      $component['menu'] = (string)$manifest->install->menu;
    }
    if (isset($manifest->install->requirement)) {
      $component['requirement'] = array();
      foreach ($manifest->install->requirement as $requirement) {
        $component['requirement'][] = (string)$requirement;
        if (!$this->components_model->get_component((string)$requirement)) {
          $component['errors'][] = 'Отсутствует требующийся компонент "'. $requirement .'"';
        }
      }
    }
    if (isset($manifest->template)) {
      $component['templates'] = $manifest->template;
    }
    
    return $component;
  }
  
  /**
  * Просмотр иконки компонента
  **/ 
  function icon($component = FALSE) {
    $this->load->helper('download');
    
    if (!$component) {
      $icon = FCPATH .'adm/images/icons/def_component_icon.png';
    } elseif (file_exists(FCPATH .'components/'. $component .'/icon.png')) {
      $icon = FCPATH .'components/'. $component .'/icon.png';
    } elseif (file_exists(APPPATH .'components/'. $component .'/icon.png')) {
      $icon = APPPATH .'components/'. $component .'/icon.png';
    } else {
      $icon = FCPATH .'adm/images/icons/def_component_icon.png';
    }
    
    force_download($component .'_icon.png', file_get_contents($icon));
  }
  
  /**
  * Установка компонента
  **/  
  function install($component, $returning = FALSE) {
    $errors = array();
    
    $component = $this->_parse_component($component);
    if ($component['errors']) {
      if (!$returning) {
        send_answer(array('errors' => $component['errors']));
      }
      return $component['errors'];
    }
    
    $parent = array();
    if (isset($component['parent'])) {
      $parent = $this->components_model->get_component($component['parent']);
      if (!$parent) {
        $errors = array('Не найден компонент '. $component['parent']);
        if (!$returning) {
          send_answer(array('errors' => $errors));
        }
        return $errors;
      } 
    }
    
    if (file_exists(APPPATH .'components/'. $component['name'] .'/database/'. $this->db->dbdriver .'_install.sql')) {
      $sql = file_get_contents(APPPATH .'components/'. $component['name'] .'/database/'. $this->db->dbdriver .'_install.sql');
      if ($sql) {
        if (!$this->main_model->execute_sql($sql)) {
          $this->components_model->delete_component($component['name']);
          $errors = array('Не удалось создать структуру таблиц БД');
          if (!$returning) {
            send_answer(array('errors' => $errors));
          }
          return $errors;
        }
      }
    }
    
    $component_params = array(
      'parent'      => ($parent ? $parent['name'] : NULL),
      'name'        => $component['name'],
      'path'        => ($parent ? $parent['path'] : '/') . $component['name'] .'/',
      'menu'        => (isset($component['menu']) ? $component['menu'] : NULL),
      'title'       => $component['title'],
      'author'      => (isset($component['author']) ? $component['author'] : NULL),
      'version'     => (isset($component['version']) ? $component['version'] : NULL),
      'description' => (isset($component['description']) ? $component['description'] : NULL)
    );
    
    if (!$this->components_model->add_component($component_params)) {
      $errors = array('Не удалось добавить компонент в БД');
      if (!$returning) {
        send_answer(array('errors' => $errors));
      }
      return $errors;
    }
    
    $error = $this->refresh($component['name'], TRUE);
    if ($error) {
      $this->components_model->delete_component($component['name']);
      $errors = array($error);
      if (!$returning) {
        send_answer(array('errors' => $errors));
      }
      return $errors;
    }
    
    if (isset($component['templates']) && $component['templates']) {
      $this->load->component(array('name' => 'templates'));
      if (!$this->templates->install(TRUE)) {
        $this->components_model->delete_component($component['name']);
        $errors = array('Не удалось установить шаблоны');
        if (!$returning) {
          send_answer(array('errors' => $errors));
        } else {
          return $errors;
        }
      }
    }
    
    if (!$returning) {
      send_answer(array('messages' => array('Компонент успешно установлен')));
    }
    
    return $errors;
  }
  
  /**
  * Удаление компонента
  **/
  function delete($component) {
    $component = $this->components_model->get_component($component);
    if (!$component) {
      send_answer(array('errors' => array('Компонент не найден')));
    }
        
    if (file_exists(APPPATH .'components/'. $component['name'] .'/database/'. $this->db->dbdriver .'_uninstall.sql')) {
      $sql = file_get_contents(APPPATH .'components/'. $component['name'] .'/database/'. $this->db->dbdriver .'_uninstall.sql');
      if ($sql) {        
        if (!$this->main_model->execute_sql($sql)) {          
          $errors = array('Не удалось удалить таблицы из БД. SQL: '.$sql);
          send_answer(array('errors' => $errors));          
        }
      }
    }
    
    if (!$this->components_model->delete_component($component['name'])) {
      send_answer(array('errors' => array('Не удалось удалить компонент')));
    }

    if (file_exists(FCPATH .'components/'. $component['name'])) {
      $this->_unlink_recursive(FCPATH .'components/'. $component['name'], TRUE);
    }
    
    $this->_update_css(TRUE);
    $this->_update_javascript(TRUE);
    
    send_answer(array('messages' => array('Компонент успешно удален')));
  }
    
  /**
  * Обновление кеша
  **/
  function refresh($component = FALSE, $returning = FALSE) {
    if ($component !== FALSE) {
      $components = array($this->components_model->get_component($component));
    } else {
      $components = $this->components_model->get_components();
    }
    
    $error = $this->_update_css($returning);
    if ($returning && $error) {
      return $error;
    }
    
    $error = $this->_update_javascript($returning);
    if ($returning && $error) {
      return $error;
    }
    
    foreach ($components as $component) {
      $error = $this->_cache_media($component['name'], $returning);
      if ($returning && $error) {
        return $error;
      }
    }
    
    if (!$returning) {
      send_answer();
    }
    return $error;
  }
  
  /**
  * Обновление стилей
  **/
  function update_css() {
    $this->_update_css();
    send_answer(array('messages' => array('Стили успешно обновлены')));
  }
  
  function _update_css($returning = FALSE) {
    $error = '';
    $types = array('admin', 'site');
    $components = $this->components_model->get_components();
    
    foreach ($types as $type) {
      $data = '/* Generated '. date('d.m.Y H:i:s') .' */';
      foreach ($components as $component) {
        if (file_exists(APPPATH .'components/'. $component['name'] .'/css/'. $type .'.css')) {
          $css = @file_get_contents(APPPATH .'components/'. $component['name'] .'/css/'. $type .'.css');
          if ($css === FALSE) {
            $error = 'Не удалось прочитать файл '. $type .'.css компонента "'. $component['name'] .'"';
            if (!$returning) {
              send_answer(array('errors' => array($error)));
            }
            return $error;
          }
          $data .= "\n\n/* ". $component['name'] ." */\n\n". $css;
        }
      }
      
      if ($type == 'admin') {
        $css = $this->_read_recursive(FCPATH .'adm/css', 'css');
      }
      if ($type == 'site') {
        $css = $this->_read_recursive(FCPATH .'css', 'css');
      }
      
      if ($css === FALSE) {
        $error = 'Не удалось обработать директорию стилей';
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
      $data .= $css;
      
      if (!@file_put_contents(FCPATH .'components/tp_'. $type .'.css', $data)) {
        $error = 'Не удалось записать файл tp_'. $type .'.css';
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
    }
    
    return $error;
  }
  
  /**
  * Обновление скриптов
  **/
  function update_javascript() {
    $this->_update_javascript();
    send_answer(array('messages' => array('Скрипты успешно обновлены')));
  }
  
  function _update_javascript($returning = FALSE) {
    $error = '';
    $types = array('admin', 'site');
    $components = $this->components_model->get_components();
    
    foreach ($types as $type) {
      $data = '/*** Generated '. date('d.m.Y H:i:s') .' ***/';
      
      if ($type == 'site') {
        $js = $this->_read_recursive(FCPATH .'js', 'js');
      }
      if ($type == 'admin') {
        $js = $this->_read_recursive(FCPATH .'adm/js', 'js');
      }
      if ($js === FALSE) {
        $error = 'Не удалось обработать директорию скриптов';
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
      $data .= $js;
      
      foreach ($components as $component) {
        if (file_exists(APPPATH .'components/'. $component['name'] .'/js/'. $type .'.js')) {
          $js = @file_get_contents(APPPATH .'components/'. $component['name'] .'/js/'. $type .'.js');
          if ($js === FALSE) {
            $error = 'Не удалось прочитать файл '. $type .'.js компонента "'. $component['name'] .'"';
            if (!$returning) {
              send_answer(array('errors' => array($error)));
            }
            return $error;
          }
          $data .= "\n\n/*** ". $component['name'] ." ***/\n\n". $js;
        }
      }
      
      if (!@file_put_contents(FCPATH .'components/tp_'. $type .'.js', $data)) {
        $error = 'Не удалось записать файл tp_'. $type .'.js';
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
    }
    
    return $error;
  }
  
  function _cache_media($component, $returning = FALSE) {
    $error = '';
    
    if (!file_exists(APPPATH .'components/'. $component)) {
      $error = 'Компонент '. $component .' не найден';
      if (!$returning) {
        send_answer(array('errors' => array($error)));
      }
      return $error;
    }
    
    if (!file_exists(FCPATH .'components/'. $component)) {
      if (!@mkdir(FCPATH .'components/'. $component)) {
        $error = 'Не удалось создать директорию /www/components/'. $component;
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
    }
    
    if (file_exists(APPPATH .'components/'. $component .'/media')) {
      $error = $this->_copy_recursive(APPPATH .'components/'. $component .'/media', FCPATH .'components/'. $component .'/media', $returning);
      if ($returning && $error) {
        return $error;
      }
    }
    
    if (file_exists(APPPATH .'components/'. $component .'/icon.png')) {
      if (!@copy(APPPATH .'components/'. $component .'/icon.png', FCPATH .'components/'. $component .'/icon.png')) {
        $error = 'Не удалось записать файл /www/components/'. $component .'/icon.png';
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
    }
    
    return $error;
  }
  
  function _copy_recursive($source, $target, $returning = FALSE) {
    $error = '';
    
    if (is_dir($source))  {
      if (!file_exists($target)) {
        if (!@mkdir($target)) {
          $error = 'Не удалось создать директорию '. $target;
          if (!$returning) {
            send_answer(array('errors' => array($error)));
          }
          return $error;
        }
      }
      
      $files = scandir($source);
      foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
          $error = $this->_copy_recursive($source .'/'. $file, $target .'/'. $file, $returning);
          if ($returning && $error) {
            return $error;
          }
        }
      }
    } else {
      if (!@copy($source, $target)) {
        $error = 'Не удалось записать файл '. $target;
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
    }
    
    return $error;
  }
  
  function _unlink_recursive($path, $returning = FALSE) {
    $error = '';
    
    if (is_dir($path))  {
      $files = scandir($path);
      foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
          $error = $this->_unlink_recursive($path .'/'. $file, $returning);
          if ($returning && $error) {
            return $error;
          }
        }
      }
      
      if (!@rmdir($path)) {
        $error = 'Не удалось удалить директорию '. $path;
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
    } else {
      if (!@unlink($path)) {
        $error = 'Не удалось удалить файл '. $path;
        if (!$returning) {
          send_answer(array('errors' => array($error)));
        }
        return $error;
      }
    }
    
    return $error;
  }
  
  function _read_recursive($path, $extensions = array()) {
    $result = '';
    
    if (!is_array($extensions)) {
      $extensions = array($extensions);
    }
    
    if (is_dir($path))  {
      $files = scandir($path);
      foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
          $data = $this->_read_recursive($path .'/'. $file, $extensions);
          if ($data === FALSE) {
            return FALSE;
          }
          $result .= $data;
        }
      }
    } elseif (in_array(get_ext($path), $extensions)) {
      $result = @file_get_contents($path);
      if ($result) {
        $result = "\n\n/*** FILE /". str_replace(FCPATH, '', $path) ." ***/\n\n". $result;
      }
    }
    
    return $result;
  }
  
}
