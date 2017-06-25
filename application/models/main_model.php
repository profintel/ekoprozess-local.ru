<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
  * Создает структуру БД
  * @param $sql - дамп таблиц
  * @return boolean
  */
  function execute_sql($sql) {
    $sql = str_replace("\r", '', $sql);
    $sqls = explode("\n\n", $sql);
    foreach ($sqls as $sql) {
      if (!$this->db->query($sql)) {
        return FALSE;
      }
    }
    return TRUE;
  }
  
  function is_available($table, $value, $id = FALSE, $field = 'name') {
    if ($id !== FALSE) {
      $this->db->where('id !=', $id);
    }
    if ($this->db->where($field, $value)->count_all_results($table)) {
      return FALSE;
    }
    return TRUE;
  }
  
  function set_params($category, $owner_id, $params) {
    $this->db->trans_begin();

    foreach ($params as $name => $value) {
      if (is_array($value)) {
        foreach ($value as $subname => $subvalue) {
          $this->_set_param($category, $owner_id, $subname .'_'. $name, $subvalue);
        }
      } else {
        $this->_set_param($category, $owner_id, $name, $value);
      }
    }

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function _set_param($category, $owner_id, $name, $value) {
    if ($this->db->get_where('params', array('category' => $category, 'owner_id' => $owner_id, 'name' => $name))->num_rows()) {
      return (bool)$this->db->update('params', array('value' => $value), array('category' => $category, 'owner_id' => $owner_id, 'name' => $name));
    } else {
      return (bool)$this->db->insert('params', array('category' => $category, 'owner_id' => $owner_id, 'name' => $name, 'value' => $value));
    }
  }
  
  function get_params($category, $owner_id) {
    $result = array();
    
    $params = $this->db->get_where('params', array('category' => $category, 'owner_id' => $owner_id))->result_array();
    foreach ($params as $param) {
      $result[$param['name']] = htmlspecialchars_decode($param['value']);
    }
    
    return $result;
  }
  
  function get_param($category, $owner_id, $param_name) {
    $result = array();
    
    $params = $this->db->get_where('params', array('category' => $category, 'owner_id' => $owner_id, 'name' => $param_name))->row_array();
    
    return $params;
  }
  
  function delete_params($category, $owner_id) {
    
    return (bool)$this->db->delete('params', array('category' => $category, 'owner_id' => $owner_id));
  }
  
  function exists_component($name) {
    return (bool)$this->db->get_where('components', array('name' => $name))->num_rows();
  }
  
  function table_exists($name) {
    return (bool)$this->db->table_exists($name);
  }
  
  /**
  * Создает sitemap.xml для SEO
  * @return xml
  */
  function sitemap() {
    $project = $this->db->get('projects')->row_array();
    $items = array(array(
      'url'      => 'http://' . $project['domain'],
      'lastmod'  => date('c'),
      'priority' => 1
    ));
    //Проекты
    $pages = $this->db->get_where('pages', array('active' => 1, 'is_searchable' => 1))->result_array();
    foreach ($pages as $page) {
      $items[] = array(
        'url'      => 'http://' . $project['domain']. $page['path'],
        'lastmod'  => date('c', strtotime($page['last_modified'])),
        'priority' => $page['priority']
      );
    }
    //Публикации
    if ($this->db->get_where('components', array('name' => 'publication'))->num_rows()) {
      $this->load->model('publication/models/publication_model');
      $publication_links = $this->db->get_where('pr_publication_links',array('type' => 'page_id'))->result_array();
      foreach ($publication_links as $key => $value) {
        $page_category = $this->db->get_where('pages', array('id' => $value['item_id']))->row_array();
        $publication_category = $this->db->get_where('pr_publication', array('id' => $value['publication_id']))->row_array();
        $publications = $this->publication_model->get_publication($publication_category['id'], 0, 0, array('active' => 1), true);
        foreach ($publications as $publication) {
          $items[] = array(
            'url'      => 'http://' . $project['domain'] . $page_category['path'] . $publication['system_name'],
            'lastmod'  => date('c', strtotime($publication['tm'])),
            'priority' => 0.5
          );
        }
        $count_pagin_pages = round(count($publications)/(int)$publication_category['in_page']);
        if ($count_pagin_pages > 0) {
          for($i=1; $i<=$count_pagin_pages; $i++) {
            if ($i > 1) {
              $items[] = array(
                'url'      => 'http://' . $project['domain'] . $page_category['path'] . '?page=' .$i,
                'lastmod'  => date('c', strtotime($publication['tm'])),
                'priority' => 0.5
              );              
            }
          }
        }
      }
    }
    //Формируем sitemap.xml
    $xml = new SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
    foreach($items as $item) {
      $url_tag = $xml->addChild("url");
      $url_tag->addChild("loc", htmlspecialchars($item['url']));
      $url_tag->addChild("lastmod", $item['lastmod']);
      $url_tag->addChild("priority", $item['priority']);
    }
    return $xml->asXML();
  }
  
}