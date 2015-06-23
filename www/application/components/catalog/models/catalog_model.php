<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalog_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
		
    $this->load->model('gallery/models/gallery_model'); 
  }
  
  function get_catalog_count($parent_id = 0, $where = '') {
    $this->db->where(array('parent_id' => $parent_id));
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('pr_catalog');
  }
  
  function get_catalog($parent_id = 0, $limit = 0, $offset = 0, $params = array(), $full = false) {
    $this->db->where(array('parent_id' => $parent_id));
    $this->db->order_by('tm_start DESC');
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($params) {
      $this->db->where($params);
    }
    $items = $this->db->get('pr_catalog')->result_array();
    foreach ($items as &$item) {
      $links = $this->db->get_where('pr_catalog_links', array('catalog_id' => $item['id']))->result_array();
      foreach ($links as $link) {
        $item[$link['type']][] = $link['item_id'];
      }
      //$item['image_thumb'] = thumb($item['image'],180,135);
		}	
		unset($item); 
    
    if ($full) {
      foreach ($items as &$item) {
        $item['params'] = $this->main_model->get_params('catalog', $item['id']);
      }
      unset($item);
    }
    
    return $items;
  }

    function get_catalog_join($parent_id = 0, $limit = 0, $offset = 0, $params = array(), $full = false, $filter_params) {
        foreach ($filter_params as $key=>$value) {
            if ($value) {
                if ($this->db->get_where('pr_catalog_params', array('id' => $key))->row('type') == 3) {
                    $filter_params[$key] = explode(",", $value);
                }
                if ($this->db->get_where('pr_catalog_params', array('id' => $key))->row('type') == 5) {
                    $filter_params[$key] = array($value);
                }
            }
        }

        $this->db->select('*');
        $this->db->from('pr_catalog');
        $this->db->join('pr_catalog_values', 'pr_catalog_values.catalog_id = pr_catalog.id');
        $this->db->group_by('pr_catalog.id');
        $this->db->where(array('parent_id' => $parent_id));
        $cnt=0;
        foreach ($filter_params as $key=>$value) {
            if ($value) {
                $cnt++;
                if (is_array($value)) {
                    if (is_array($value[0])) {
                        $where = "(param_id=".$key." AND value IN (".implode(",", $value[0])."))";
                    } else {
                        $where = "(param_id=".$key." AND value BETWEEN ".(int)$value[0]." and ".(int)$value[1].")";
                    }
                } else {
                    $where = "(param_id=".$key." AND value=".(int)$value.")";
                }
                if ($cnt==1) { $this->db->where($where); } else { $this->db->or_where($where); }
            }
        }

        if ($cnt>0) $this->db->having('count(pr_catalog.id) = '.$cnt);
        $this->db->order_by('tm_start DESC');
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        if ($params) {
            $this->db->where($params);
        }
        $items = $this->db->get()->result_array();

        //var_dump($this->db->last_query());

        foreach ($items as &$item) {
            $links = $this->db->get_where('pr_catalog_links', array('catalog_id' => $item['catalog_id']))->result_array();
            foreach ($links as $link) {
                $item[$link['type']][] = $link['item_id'];
            }
            //$item['image_thumb'] = thumb($item['image'],180,135);
        }
        unset($item);

        if ($full) {
            foreach ($items as &$item) {
                $item['params'] = $this->main_model->get_params('catalog', $item['catalog_id']);
            }
            unset($item);
        }

        return $items;
    }
  
  function _validate_catalog_system_name($system_name, $catalog_id = 0) {
    if ($catalog_id) {
      $this->db->where(array('id !=' => $catalog_id));
    }
    if ($this->db->get_where('pr_catalog', array('system_name' => $system_name))->num_rows()) {
      return false;
    }
    return true;
  }

  function get_catalog_one($where_params = array()) {
    $item = $this->db->get_where('pr_catalog', $where_params)->row_array();
    if ($item) {
        $item['params'] = $this->main_model->get_params('catalog', $item['id']);

        $links = $this->db->get_where('pr_catalog_links', array('catalog_id' => $item['id']))->result_array();
        foreach ($links as $link) {
            $item[$link['type']][] = $link['item_id'];
        }
        $values = $this->db->get_where('pr_catalog_values', array('catalog_id' => $item['id']))->result_array();
        foreach($values as $value){
            $item['values'][$value['param_id']] = $value['value'];
        }
    }
    return $item;
  }

  function create_catalog($params) {
    if ($this->db->insert('pr_catalog', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function edit_catalog($id, $params) {
    if ($this->db->update('pr_catalog', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
	
  function set_catalog_links($id, $links) {
    if (!$links) {
      $this->db->delete('pr_catalog_links', array('catalog_id' => $id));
      return true;
    }
    
    foreach ($links as $type => $items) {
      $now = array();
      if (is_array($items)) {
        if (!$this->db->query("DELETE FROM pr_catalog_links WHERE catalog_id = ". $id ." AND type = '". $type ."' AND item_id NOT IN (". ($items ? implode(',', $items) : 0) .")")) {
          return false;
        }
        if ($items) {
          $now_items = $this->db->get_where('pr_catalog_links', array('catalog_id' => $id, 'type' => $type))->result_array();
          foreach ($now_items as $now_item) {
            $now[] = $now_item['item_id'];
          }
          foreach ($items as $item_id) {
            if (!in_array($item_id, $now)) {
              if (!$this->db->insert('pr_catalog_links', array('catalog_id' => $id, 'type' => $type, 'item_id' => $item_id))) {
                return false;
              }
            }
          }
        }
      } else {
        $item_id = $items;
        if (!$this->db->query("DELETE FROM pr_catalog_links WHERE catalog_id = ". $id ." AND type = '". $type ."' AND item_id != ".$item_id )) {
          return false;
        }
        if ($item_id) {
          $now_items = $this->db->get_where('pr_catalog_links', array('catalog_id' => $id, 'type' => $type))->result_array();
          foreach ($now_items as $now_item) {
            $now[] = $now_item['item_id'];
          }
          if (!in_array($item_id, $now)) {
            if (!$this->db->insert('pr_catalog_links', array('catalog_id' => $id, 'type' => $type, 'item_id' => $item_id))) {
              return false;
            }
          }
        }      
      }
    }
    return true;
  }
  
  function catalog_active($id, $active) {
    if (!$this->db->query('UPDATE pr_catalog SET active = '. $active .' WHERE id = '. $id)) {
      return false;
    }
    return true;
  }
  
  function delete_catalog($id) {
    $item = $this->get_catalog_one(array('id' => $id));

    $category = $this->get_catalog_one(array('id' => $item['parent_id']));
    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->component['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'));
    
    $this->db->delete('pr_catalog_links', array('catalog_id' => $id));
    $this->db->delete('pr_catalog', array('id' => $id));
  }
  
  function delete_category($id) {
    $item = $this->get_catalog_one(array('id' => $id));

    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->component['name'].'/'.$item['system_name'].'/'));
    
    $this->db->delete('pr_catalog_links', array('catalog_id' => $id));
    $this->db->delete('pr_catalog', array('parent_id' => $id));
    $this->db->delete('pr_catalog', array('id' => $id));
  }

    function get_catalog_params_count($id = 0, $where = '') {
        $this->db->where(array('catalog_id' => $id));
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results('pr_catalog_params');
    }

    function get_catalog_params($id = 0, $limit = 0, $offset = 0) {
        $this->db->where(array('catalog_id' => $id));
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $items = $this->db->get('pr_catalog_params')->result_array();

        return $items;
    }
    function get_filter_params($id = 0) {
        $this->db->where(array('catalog_id' => $id, 'in_filter' => '1'));
        $items = $this->db->get('pr_catalog_params')->result_array();

        return $items;
    }
    function get_catalog_param($id) {
        $this->db->where(array('id' => $id));
        $items = $this->db->get('pr_catalog_params')->row_array();

        return $items;
    }

    function update_catalog_param($id, $params) {
        if ($this->db->update('pr_catalog_params', $params, array('id' => $id))) {
            return true;
        }
        return false;
    }

    function create_catalog_params($params) {
        if ($this->db->insert('pr_catalog_params', $params)) {
            return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
        }
        return false;
    }

    function edit_catalog_params($id, $params) {
        if ($this->db->update('pr_catalog_params', $params, array('id' => $id))) {
            return true;
        }
        return false;
    }

    function delete_catalog_params($id) {
        $this->db->delete('pr_catalog_params', array('id' => $id));
        $this->db->delete('pr_catalog_values', array('param_id' => $id));
    }

    function set_category_params($id, $params) {
        foreach ($params as $key=>$param) {
            $catalog_param['catalog_id'] = $id;
            $catalog_param['param_id'] = $key;
            $catalog_param['value'] = $param;
            $this->db->insert('pr_catalog_values', $catalog_param);
        }
        return true;
    }

    function edit_category_params($id, $params) {
        foreach ($params as $key=>$param) {
            $catalog_param['catalog_id'] = $id;
            $catalog_param['param_id'] = $key;
            $catalog_param['value'] = $param;

            if ($this->db->get_where('pr_catalog_values', array('catalog_id' => $id, 'param_id' => $key))->row()) {
                $this->db->update('pr_catalog_values', $catalog_param, array('catalog_id' => $id, 'param_id' => $key));
            } else {
                $this->db->insert('pr_catalog_values', $catalog_param);
            }
        }
        return true;
    }

    function get_catalog_values($id) {
        $values = $this->db->get_where('pr_catalog_values', array('catalog_id' => $id))->result_array();
        $item = array();
        foreach($values as $value){
            $item[$value['param_id']] = $value['value'];
        }
        return $item;
    }

}