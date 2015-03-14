<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery_site extends CI_Component {
  
  function __construct() {
    parent::__construct();

		$this->load->model('gallery/models/gallery_model');
		$this->load->model('templates/models/templates_model');
  }

  function render($param) {
    if ((int)$param) {
      $link = $this->gallery_model->get_link(array('item_id' => (int)$param, 'type' => 'page_id'));
      if (!$link) {
        return '';
      }
      $album = $this->gallery_model->get_gallery_one(array('id' => $link['gallery_id']), true);
    } else {
      $album = $this->gallery_model->get_gallery_one(array('system_name' => $param), true);
    }
    
    if (!$album) {
      return FALSE;
    }    
    if (!$album['template_id']) {
      return FALSE;
    }
    if (count($this->arguments)) {
      //show_404();
    }
    
    $data = array(
      'images' => $album['images']
    );

    $template = $this->templates_model->get_template((int)$album['template_id']);
    return $this->render_template($template['path'],$data);
  }

  function render_file($id) {
    $data = array(
      'item' => $this->gallery_model->get_gallery_image(array('id' => $id))
    );
    return $this->render_template('templates/item',$data);
  }

  function render_templates() {
    $album_id = ($this->uri->getParam('album_id') ? $this->uri->getParam('album_id') : 0);
    
    if ($album_id) {
      $album = $this->gallery_model->get_gallery_one(array('id' => $album_id), true);
      if (!$album) {
        return FALSE;
      }
    
      if (!$album['template_id']) {
        return FALSE;
      }
      if (count($this->arguments)) {
        show_404();
      }
      
      $page = $this->page;
      $crumb = array(array(
        'title' => $album['params']['name_'.$this->language],
        'path'  => $this->params['path']
      ));
      $page['crumbs'] = array_merge($this->page['crumbs'],$crumb);
      $this->page = $page;
      
      $data = array(
        'images' => $album['images']
      );

      $template = $this->templates_model->get_template((int)$album['template_id']);
      return $this->render_template($template['path'],$data);
    } else {
      $links = $this->gallery_model->get_links('page_id',$this->page['id']);
      if (!$links) {
        return FALSE;
      }
      $albums = array();
      foreach ($links as $link) {        
        $albums[] = $this->gallery_model->get_gallery_one(array('id' => $link['gallery_id']), true);
      }
      if (!$albums) {
        return FALSE;
      }
      
      if (count($albums) == 1) {
        $album = $albums[0];
        if (!$album['template_id']) {
          return FALSE;
        }
        if (count($this->arguments)) {
          show_404();
        }
        
        $data = array(
          'images' => $album['images']
        );

        $template = $this->templates_model->get_template((int)$album['template_id']);
        return $this->render_template($template['path'],$data);
      } else {
        $data = array(
          'albums' => $albums
        );

        return $this->render_template('templates/albums',$data);
      }
    }
  }
}