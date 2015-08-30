<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery_model extends CI_Model {
  
  function __construct() {
    parent::__construct();			
  }
  
  function get_gallery_albums($parent_id = 0, $where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    
    return $this->db->get_where('pr_gallery_hierarchy',array('parent_id' => $parent_id))->result_array();
  }
  
  function get_gallery_one($params = array(), $images = false) {
    $item = $this->db->get_where('pr_gallery_hierarchy',$params)->row_array();
		if ($item) {
      $item['params'] = $this->main_model->get_params('gallery', $item['id']);

      $links = $this->db->get_where('pr_gallery_links', array('gallery_id' => $item['id']))->result_array();
      foreach ($links as $link) {
        $item[$link['type']][] = $link['item_id'];
      }
      $item['image'] = $this->get_gallery_image(array('gallery_images.gallery_id' => $item['id'], 'gallery_images.main' => 1));
      
      if ($images) {
        $item['images'] = $this->db->get_where('pr_gallery_images',array('gallery_id' => $item['id']))->result_array();
        foreach ($item['images'] as &$image) {
          $image['ext'] = get_ext($image['image']);
          $image['thumbs'] = $this->db->get_where('pr_gallery_thumbs',array('image_id' => $image['id']))->result_array();
          foreach ($image['thumbs'] as $thumb) {
            if ($thumb['width'] == 185 && $thumb['height'] == 135) {
              $image['standard_thumb'] = $thumb['thumb'];
            } else {
              $image['thumb'] = $thumb['thumb'];
              $image['thumb_width'] = $thumb['width'];
              $image['thumb_height'] = $thumb['height'];
            }        
          }
          $image['params'] = $this->main_model->get_params('gallery_image', $image['id']);
          //ссылка на страницу
          if($image['page_id']){
            $image['path'] = $this->db->get_where('pages', array('id' => $image['page_id']))->row()->path;
          }
        }
        unset($image);
      }
    }
    return $item;
  }
  
  function get_links($type, $item_id) {
    return $this->db->get_where('pr_gallery_links', array('item_id' => $item_id, 'type' => $type))->result_array();
  }
  
  function get_link($params) {
    return $this->db->get_where('pr_gallery_links', $params)->row_array();
  }
  
  function create_album($params) {
    if ($this->db->insert('pr_gallery_hierarchy', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }
  
  function set_gallery_links($id, $links) {
    if (!$links) {
      $this->db->delete('pr_gallery_links', array('gallery_id' => $id));
      return true;
    }
    
    foreach ($links as $type => $items) {
      $now = array();
      if (!$this->db->query("DELETE FROM pr_gallery_links WHERE gallery_id = ". $id ." AND type = '". $type ."' AND item_id NOT IN (". ($items ? (is_array($items) ? implode(',', $items) : $items) : 0) .")")) {
        return false;
      }
      if ($items) {        
        if (!is_array($items)) {
          $items = array($items);
        }
        $now_items = $this->db->get_where('pr_gallery_links', array('gallery_id' => $id, 'type' => $type))->result_array();
        foreach ($now_items as $now_item) {
          $now[] = $now_item['item_id'];
        }
        foreach ($items as $item_id) {
          if (!in_array($item_id, $now)) {
            $this->db->trans_begin();
            $this->db->insert('pr_gallery_links', array('gallery_id' => $id, 'type' => $type, 'item_id' => (int)$item_id));
            $id = $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
              $this->db->trans_rollback();
              return FALSE;
            }
            $this->db->trans_commit();
          }
        }
      }
    }
    return true;
  }
  
  /**
  * Вывод изображний галереи
  * @param $where_params - параметры для вывода альбома
  *        $limit = 0, $offset = 0 - параметры для вывода изображений
  **/
  function get_gallery_images($where_params, $limit = 0, $offset = 0, $order_by = 'main DESC, order ASC') {
    if (!$where_params) {
      return false;
    }
    $album = $this->db->get_where('pr_gallery_hierarchy', $where_params)->row_array();
    $images = array();
    if ($album) {
      if ($limit) {
        $this->db->limit($limit, $offset);
      }
      $this->db->order_by($order_by);
      $images = $this->db->get_where('pr_gallery_images',array('gallery_id' => $album['id']))->result_array();
      foreach ($images as &$image) {
        $image['ext'] = get_ext($image['image']);
        $image['thumbs'] = $this->db->get_where('pr_gallery_thumbs',array('image_id' => $image['id']))->result_array();
        foreach ($image['thumbs'] as $thumb) {
          $image['thumbs'][$thumb['width'].'_'.$thumb['height']] = $thumb['thumb'];
        }
      }
      unset($image);
    }
    return $images;
  }
  
  /**
  * Вывод изображения
  **/
  function get_gallery_image($params) {
    $this->db->where($params);
    $this->db->select('gallery_images.*');
    $this->db->join('gallery_hierarchy','gallery_hierarchy.id = gallery_images.gallery_id');
    $this->db->order_by('main DESC, order ASC');
    $image = $this->db->get('gallery_images')->row_array();
    if ($image) {
      $image['params'] = $this->main_model->get_params('gallery_image', $image['id']);
      
      $image['ext'] = get_ext($image['image']);
      $image['thumbs'] = $this->db->get_where('pr_gallery_thumbs',array('image_id' => $image['id']))->result_array();
      foreach ($image['thumbs'] as $thumb) {
        $image['thumbs'][$thumb['width'].'_'.$thumb['height']] = $thumb['thumb'];
      }
    }
    return $image;
  }

  /**
  * Перемещение объектов галереи
  **/
  function move_gallery_image($id, $dest_id) {
    $this->db->trans_begin();
    
    $item = $this->get_gallery_image(array('gallery_images.id' => $id));
    
    if ($dest_id) {
      $dest = $this->get_gallery_image(array('gallery_images.id' => $dest_id));      
      $params = array(
        'order' => $dest['order'] + 1
      );
    } else {
      $params = array(
        'order' => (int)$this->db->select_min('order', 'min_order')->get('gallery_images')->row()->min_order
      );
    }
    
    $this->edit_gallery_image($item['id'], $params);
    
    $this->db->set('order', '`order` + 1', FALSE)->where(array(
      'order >='   => $params['order'],
      'id !='      => $item['id']
    ))->update('gallery_images');
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  /**
  * Добавление изображений в системную галерею  
  *  @param 
  *    $gallery_hierarchy_params = array(
  *      array(
  *        'system_name' => ,
  *        'title'       => ,
  *        'images'      => array(
  *          array (
  *            'image'         => ,
  *            'images_thumbs' => array(
  *              array(
  *                'thumb' => ,
  *                'width' => ,
  *                'height' => 
  *              )
  *            )
  *         )
  *        ),            
  *        'childrens'   => array(
  *          array(
  *            'system_name' => ,
  *            'title'       => ,
  *            'images'      => array(
  *              array (
  *                'image'         => ,
  *                'images_thumbs' => array(
  *                  array(
  *                    'thumb' => ,
  *                    'width' => ,
  *                    'height' => 
  *                  )
  *                )
  *             )
  *            ),            
  *            'childrens'   => array(
  *              ...
  *            ),
  *          )
  *        ),
  *      )
  *    )
  **/
  function add_gallery_images($gallery_hierarchy_params,$parent_id = 0,$path = '/') {
    if (!$gallery_hierarchy_params) {
      return false;
    }
    foreach($gallery_hierarchy_params as $gallery) {
      if (!isset($gallery['system_name']) || !$gallery['system_name']) {
        continue;
      }
      $album = $this->db->get_where('pr_gallery_hierarchy',array('system_name' => $gallery['system_name'], 'parent_id' => $parent_id))->row_array();
      if (!$album) {
        $album_path = $path . $gallery['system_name'].'/';
        $this->db->insert('pr_gallery_hierarchy', array('parent_id' => $parent_id, 'title' => $gallery['title'], 'path' => $album_path, 'system_name' => $gallery['system_name']));
        $album_id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
      } else {
        $album_id   = $album['id'];
        $album_path = $album['path'];
      }
      if (@$gallery['images']){
        foreach($gallery['images'] as $image) {
          if (!$image) {
            continue;
          }
          $image_params = array(
            'gallery_id' => $album_id,
            'image' => $image['image'],
            'type' => (isset($image['type']) && $image['type'] ? $image['type'] : 'image'),
            'order' => $this->get_images_order()
          );
          $this->db->insert('pr_gallery_images', $image_params);
          $image_id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
          if (!isset($image['images_thumbs'])) {
            continue;
          }
          $multiparams = array();
          foreach($image['images_thumbs'] as $thumb) {
            $this->db->insert('pr_gallery_thumbs', array('image_id' => $image_id, 'thumb' => $thumb['thumb'], 'width' => $thumb['width'], 'height' => $thumb['height']));
            $multiparams['thumb_width'] = $thumb['width'];
            $multiparams['thumb_height'] = $thumb['height'];
          }
          $this->main_model->set_params('gallery_image', $image_id, $multiparams);
        }
      }
      if (@$gallery['childrens']) {
        $this->add_gallery_images($gallery['childrens'], $album_id, $album_path);
      }
    }
    return true;
  }

  function get_images_order() {
    return (int)$this->db->select_max('order', 'max_order')->get('pr_gallery_images')->row()->max_order + 1;
  }
  
  function set_gallery_hierarchy($gallery_hierarchy_params, $parent_id = 0, $path = '/') {
    foreach($gallery_hierarchy_params as $gallery) {
      $album = $this->db->get_where('pr_gallery_hierarchy',array('system_name' => $gallery['system_name'], 'parent_id' => $parent_id))->row_array();
      if (!$album) {
        $album['path'] = $path . $gallery['system_name'].'/';
        $this->db->insert('pr_gallery_hierarchy', array('parent_id' => $parent_id, 'title' => $gallery['title'], 'path' => $album['path'], 'system_name' => $gallery['system_name']));
        $album['id'] = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
      }
      if (isset($gallery['childrens']) && $gallery['childrens']) {
        $album = $this->set_gallery_hierarchy($gallery['childrens'], $album['id'], $album['path']);
      }
    }
    return $album;
  }
  
  function create_gallery_image($params) {    
    $this->db->insert('pr_gallery_images', $params);
    
    return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
  }
  
  function edit_gallery_image($id, $params) {    
    if ($this->db->update('pr_gallery_images',$params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function create_thumb_gallery_image($params) {
    $this->db->insert('pr_gallery_thumbs', $params);
    
    return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
  }
  
  /**
  * Удаление галереи изображений
  * @param
  **/  
  function delete_gallery_images($where_params = array()) {
    if (!$where_params) {
      return false;
    }
    $album = @$this->db->get_where('pr_gallery_hierarchy',$where_params)->row_array();

    if ($album) {
      $images = $this->db->get_where('pr_gallery_images',array('gallery_id' => $album['id']))->result_array();
      foreach ($images as $image) {
        $thumbs = $this->db->get_where('pr_gallery_thumbs',array('image_id' => $image['id']))->result_array();
        foreach($thumbs as $thumb) {
          @unlink($_SERVER['DOCUMENT_ROOT'] . $thumb['thumb']);
        }
        $this->db->delete('pr_gallery_thumbs',array('image_id' => $image['id']));
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image['image']);
      }
      $this->db->delete('pr_gallery_images',array('gallery_id' => $album['id']));
      
      $albums_child = $this->db->get_where('pr_gallery_hierarchy',array('parent_id' => $album['id']))->result_array();
      if ($albums_child) {
        foreach ($albums_child as $album_child) {
          $this->delete_gallery_images(array('path' => $album_child['path']));
        }
      }
      $this->db->delete('pr_gallery_hierarchy', array('id' => $album['id']));
    }    
  }
  
  /**
  * Удаление изображения и его превью
  **/
  function delete_image($where_params, $unlink = true) {
    if (!$where_params) {
      return false;
    }    
    $image = $this->db->get_where('pr_gallery_images',$where_params)->row_array();
    if ($image) {
      $thumbs = $this->db->get_where('pr_gallery_thumbs',array('image_id' => $image['id']))->result_array();
      foreach($thumbs as $thumb) {
        if ($unlink) {
          @unlink($_SERVER['DOCUMENT_ROOT'] . $thumb['thumb']);
        }
      }
      $this->db->delete('pr_gallery_thumbs',array('image_id' => $image['id']));
      if ($unlink) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image['image']);
      }
      $this->db->delete('pr_gallery_images',array('id' => $image['id']));
    }
  }
  
  /**
  * Удаление файлов и его превью, без удаления из базы
  **/
  function delete_image_files($where_params) {
    if (!$where_params) {
      return false;
    }    
    $image = $this->db->get_where('pr_gallery_images',$where_params)->row_array();
    if ($image) {
      $thumbs = $this->db->get_where('pr_gallery_thumbs',array('image_id' => $image['id']))->result_array();
      foreach($thumbs as $thumb) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $thumb['thumb']);
      }
      $this->db->delete('pr_gallery_thumbs',array('image_id' => $image['id']));
      @unlink($_SERVER['DOCUMENT_ROOT'] . $image['image']);
    }
  }

  function edit_gallery($id, $params) {
    if ($this->db->update('pr_gallery_hierarchy',$params, array('id' => $id))) {
      return true;
    }
    return false;
  }

  function update_gallery_hierarchy($params, $where) {
    if (!$this->db->update('gallery_hierarchy', $params, $where)) {
      return false;
    }
    return true;
  }
  
  function edit_gallery_images($gallery_id, $params) {
    if ($this->db->update('pr_gallery_images',$params, array('gallery_id' => $gallery_id))) {
      return true;
    }
    return false;
  }
  
  function _validate_gallery_system_name($system_name, $id = 0) {
    if ($id) {
      $this->db->where(array('id !=' => $id));
    }
    if ($this->db->get_where('pr_gallery_hierarchy', array('system_name' => $system_name))->num_rows()) {
      return false;
    }
    return true;
  }  

  function validate_file($file, $permission_exts) {
    if (!$file || !$permission_exts) {
      return false;
    }
    if (in_array(get_ext($file), $permission_exts)) { 
      return true;
    }
    return false;
  }
  
  function thumb($image, $width, $height) {
    if ($image && ($width || $height)) {
      $parts = explode('.', $image);
      $ext = strtolower(array_pop($parts));
      $name = implode('.', $parts);
      return $name .'_'. $width .'_'. $height .'.'. $ext;
    } else {
      return $image;
    }
  } 
}