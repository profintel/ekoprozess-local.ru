<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Publication_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
		
    $this->load->model('gallery/models/gallery_model'); 
  }
  
  function get_publication_count($parent_id = 0, $where = '') {
    $this->db->where(array('parent_id' => $parent_id));
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('pr_publication');
  }
  
  function get_publication($parent_id = 0, $limit = 0, $offset = 0, $params = array(), $full = false) {
    $this->db->where(array('parent_id' => $parent_id));
    $this->db->order_by('tm_start DESC');
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($params) {
      $this->db->where($params);
    }
    $items = $this->db->get('pr_publication')->result_array();
		foreach ($items as &$item) {
      $links = $this->db->get_where('pr_publication_links', array('publication_id' => $item['id']))->result_array();
      foreach ($links as $link) {
        $item[$link['type']][] = $link['item_id'];
      }
      //$item['image_thumb'] = thumb($item['image'],180,135);
		}	
		unset($item); 
    
    if ($full) {
      foreach ($items as &$item) {
        $item['params'] = $this->main_model->get_params('publication', $item['id']);
      }
      unset($item);
    }
    
    return $items;
  }
  
  function _validate_publication_system_name($system_name, $publication_id = 0) {
    if ($publication_id) {
      $this->db->where(array('id !=' => $publication_id));
    }
    if ($this->db->get_where('pr_publication', array('system_name' => $system_name))->num_rows()) {
      return false;
    }
    return true;
  }

  function get_publication_one($where_params = array()) {
    $item = $this->db->get_where('pr_publication', $where_params)->row_array();
		if ($item) {
      $item['params'] = $this->main_model->get_params('publication', $item['id']);

      $links = $this->db->get_where('pr_publication_links', array('publication_id' => $item['id']))->result_array();
      foreach ($links as $link) {
        $item[$link['type']][] = $link['item_id'];
      }
    }
    return $item;
  }

  function create_publication($params) {
    if ($this->db->insert('pr_publication', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function edit_publication($id, $params) {
    if ($this->db->update('pr_publication', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
	
  function set_publication_links($id, $links) {
    if (!$links) {
      $this->db->delete('pr_publication_links', array('publication_id' => $id));
      return true;
    }
    
    foreach ($links as $type => $items) {
      $now = array();
      if (is_array($items)) {
        if (!$this->db->query("DELETE FROM pr_publication_links WHERE publication_id = ". $id ." AND type = '". $type ."' AND item_id NOT IN (". ($items ? implode(',', $items) : 0) .")")) {
          return false;
        }
        if ($items) {
          $now_items = $this->db->get_where('pr_publication_links', array('publication_id' => $id, 'type' => $type))->result_array();
          foreach ($now_items as $now_item) {
            $now[] = $now_item['item_id'];
          }
          foreach ($items as $item_id) {
            if (!in_array($item_id, $now)) {
              if (!$this->db->insert('pr_publication_links', array('publication_id' => $id, 'type' => $type, 'item_id' => $item_id))) {
                return false;
              }
            }
          }
        }
      } else {
        $item_id = $items;
        if (!$this->db->query("DELETE FROM pr_publication_links WHERE publication_id = ". $id ." AND type = '". $type ."' AND item_id != ".$item_id )) {
          return false;
        }
        if ($item_id) {
          $now_items = $this->db->get_where('pr_publication_links', array('publication_id' => $id, 'type' => $type))->result_array();
          foreach ($now_items as $now_item) {
            $now[] = $now_item['item_id'];
          }
          if (!in_array($item_id, $now)) {
            if (!$this->db->insert('pr_publication_links', array('publication_id' => $id, 'type' => $type, 'item_id' => $item_id))) {
              return false;
            }
          }
        }      
      }
    }
    return true;
  }
  
  function publication_active($id, $active) {
    if (!$this->db->query('UPDATE pr_publication SET active = '. $active .' WHERE id = '. $id)) {
      return false;
    }
    return true;
  }
  
  function delete_publication($id) {    
    $item = $this->get_publication_one(array('id' => $id));
    
    //Удаление изображений публикации
    $category = $this->get_publication_one(array('id' => $item['parent_id']));
    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->component['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'));
    
    $this->db->delete('pr_publication_links', array('publication_id' => $id));
    $this->db->delete('pr_publication', array('id' => $id));    
  }
  
  function delete_category($id) {
    $item = $this->get_publication_one(array('id' => $id));

    //Удаление изображений категории
    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->component['name'].'/'.$item['system_name'].'/'));
    
    $this->db->delete('pr_publication_links', array('publication_id' => $id));
    $this->db->delete('pr_publication', array('parent_id' => $id));
    $this->db->delete('pr_publication', array('id' => $id));
  }

}