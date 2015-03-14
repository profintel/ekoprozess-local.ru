<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banners_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
			
  }
  
  ### Баннерные зоны ###
  function get_banner_zones() {
    return $this->db->get('banner_zones')->result_array();
  }
  
  function get_banner_zone($where = array()) {
    return $this->db->get_where('banner_zones', $where)->row_array();
  }
  
  function create_banner_zone($params) {
    if ($this->db->insert('banner_zones', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }
  
  function edit_banner_zone($id, $params) {
    if ($this->db->update('banner_zones', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_banner_zone($id) {
    $this->db->get_where('banner_zones', array('id' => $id))->row_array();
    
    //Удаление изображений
    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->component['name'].'/'.$item['system_name']));
    
    $this->db->delete('banner_zones', array('id' => $id));
  }
  ######  
  
  ### Баннеры ###
  function get_banners($limit = 0, $offset = 0, $params = array()) {
    $this->db->select('banners.*, banner_zones.title as zone');
    if ($params) {
      $this->db->where($params);
    }
    $this->db->order_by('id', 'desc');
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    $this->db->join('banner_zones', 'banner_zones.id = banners.zone_id');
    return $this->db->get('banners')->result_array();
  }
  
  function get_banner($id) {
    $item = $this->db->get_where('banners', array('id' => $id))->row_array();
    $item['banner_zone'] = $this->banners_model->get_banner_zone(array('id' => $item['zone_id']));

    $item['projects'] = array();
    $projects = $this->db->get_where('banner_projects', array('banner_id' => $id))->result_array();
    foreach ($projects as $project) {
      $item['projects'][] = $project['project_id'];
    } 

    return $item;
  }
  
  function create_banner($params) {
    if ($this->db->insert('banners', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }  
  
  function edit_banner($id, $params) {
    if ($this->db->update('banners', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function banner_active($id, $active) {
    if (!$this->db->simple_query('UPDATE banners SET active = '. $active .' WHERE id = '. $id)) {
      return false;
    }
    return true;
  }
  
  function set_banner_projects($id, $projects) {
    $this->db->query('DELETE FROM pr_banner_projects WHERE banner_id = '. $id .' AND project_id NOT IN ('. ($projects ? implode(',', $projects) : 0) .')');
    $now = array();
    $now_projects = $this->db->get_where('banner_projects', array('banner_id' => $id))->result_array();
    foreach ($now_projects as $project) {
      $now[] = $project['project_id'];
    }
    foreach ($projects as $project_id) {
      if (!in_array($project_id, $now)) {
        if (!$this->db->insert('banner_projects', array('banner_id' => $id, 'project_id' => $project_id))) {
          return false;
        }
      }
    }
    return true;
  }
  
  function delete_banner($id) {
    $item = $this->db->get_where('banners', array('id' => $id))->row_array();
    
    //Удаление изображения
    $banner_zone = $this->banners_model->get_banner_zone(array('id' => $item['zone_id']));
    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->component['name'].'/'.$banner_zone['system_name'].'/'.$item['system_name']));
    
    $this->db->delete('banners', array('id' => $id));
  }
  
  function set_banner($project_id, $system_name) {
    $zone = $this->db->get_where('banner_zones', array('system_name' => $system_name))->row_array();
    if (!$zone) {
      return false;
    }
    $this->db->join('banner_projects', 'banner_projects.banner_id = banners.id');
    $this->db->order_by('possibility DESC');
    $banners = $this->db->get_where('banners', array(
      'banners.zone_id' => $zone['id'],
      'banners.active' => 1,
      'banner_projects.project_id' => $project_id
    ))->result_array();    
    
    if ($banners) {
      $rand_max = 0;
      foreach ($banners as $key=> $banner) {
        $rand_max += $banner['possibility'];
        $results[$key] = $rand_max;        
      }
      $rand = rand(0, $rand_max);
      $prev = 1;
      foreach ($results as $key=> $result) {
        if ($key != 0) {
          $prev = $results[$key - 1];
        }
        if ($rand >= $prev && $rand <= $result) {
          $this->db->query('UPDATE pr_banners SET views = views + 1 WHERE id ='.$banners[$key]['id']);
          return $banners[$key];
        } 
      }
    }
    return false;
  }  
  ######
}