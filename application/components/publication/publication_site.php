<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Publication_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
		
    $this->load->model('publication/models/publication_model');
  }
  
  function render_templates() {
    $publication_links = $this->db->get_where('pr_publication_links',array('item_id' => $this->page['id'], 'type' => 'page_id'))->row_array();    
    if (!$publication_links) {
      return FALSE;
    } 
    $publication_category = $this->db->get_where('pr_publication', array('id' => $publication_links['publication_id']))->row_array();
    if (!$publication_category) {
      return FALSE;
    }    
    if (!$publication_category['template_id']) {
      return FALSE;
    }
    if (count($this->arguments) > 1) {
      show_404();
    }

		if (!$this->arguments) {
      $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
			$in_page = $publication_category['in_page'];
			$all_count = $this->publication_model->get_publication_count($publication_category['id']);
			$pages = get_pages($page, $all_count, $in_page);
			$pagination_data = array(
				'pages' => $pages,
				'page' => $page,
				'prefix' => $this->page['path']
			);
			$items = $this->publication_model->get_publication($publication_category['id'],$in_page, $in_page * ($page - 1), array('active' => 1), true);
			foreach ($items as &$item) {
        $category = $this->db->get_where('pr_publication', array('id' => $item['parent_id']))->row_array();
        $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
        if ($images) {
          $item['image'] = $images[0];
        }
      }
      unset($item);
      
      $data = array(
				'items' 			=> $items,
				'page' 				=> $page,
				'pagination' 	=> $this->load->view('templates/pagination', $pagination_data, true)
			);

      $template = $this->templates_model->get_template((int)$publication_category['template_id']);
      return $this->render_template($template['path'],$data);
		}	else {
			$data = array(
        'item' => $this->publication_model->get_publication_one(array('system_name' => $this->arguments[0]))
      );
      
      if (!$data['item']) {
        show_404();
      }
      
      $category = $this->db->get_where('pr_publication', array('id' => $data['item']['parent_id']))->row_array();
      $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$data['item']['system_name'].'/'),1,0);
      if ($images) {
        $data['item']['image'] = $images[0];
      }
      
      $page = $this->page;
			if ($data['item']['params']['h1_'.$this->language]) {
        $page['params']['h1_'.$this->language] = $data['item']['params']['h1_'.$this->language];
			}
			if ($data['item']['params']['title_'.$this->language]) {
        $page['params']['title_'.$this->language] = $data['item']['params']['title_'.$this->language];
			}
			if ($data['item']['params']['description_'.$this->language]) {
				$page['params']['description_'.$this->language] = $data['item']['params']['description_'.$this->language];
			}
			if ($data['item']['params']['keywords_'.$this->language]) {
				$page['params']['keywords_'.$this->language] = $data['item']['params']['keywords_'.$this->language];
			}
      $this->page = $page;
      
      $template = $this->templates_model->get_template((int)$data['item']['template_id']);
      return $this->render_template($template['path'],$data);
		}    
	}  
  
	function last_publication ($name, $count) {
		$category = $this->publication_model->get_publication_one(array('system_name' => $name,'parent_id' => 0));		
    if (!$category) {
      return FALSE;
    }
    
		$page_link = $this->db->get_where('pr_pages',array('id' => $category['page_id'][0]))->row_array();
		if (!$page_link) {
      return FALSE;
    }
    $data = array(
			'page_path'  => $page_link['path'],
			'title' => $category['params']['name_'.$this->language],
			'items' => $this->publication_model->get_publication($category['id'], $count, 0, array('active' => 1), true)
		);
    
    foreach ($data['items'] as &$item) {
      $item['image'] = "";
      $category = $this->db->get_where('pr_publication', array('id' => $item['parent_id']))->row_array();
      $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
      if ($images) {
        $item['image'] = $images[0];
      }
    }
    unset($item);
    
    return $this->render_template('templates/last_publication',$data);				
	}  
}