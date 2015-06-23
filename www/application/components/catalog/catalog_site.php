<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalog_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
		
    $this->load->model('catalog/models/catalog_model');
  }
  
  function render_templates() {
    $catalog_links = $this->db->get_where('pr_catalog_links',array('item_id' => $this->page['id'], 'type' => 'page_id'))->row_array();
    if (!$catalog_links) {
      return FALSE;
    } 
    $catalog_category = $this->db->get_where('pr_catalog', array('id' => $catalog_links['catalog_id']))->row_array();
    if (!$catalog_category) {
      return FALSE;
    }    
    if (!$catalog_category['template_id']) {
      return FALSE;
    }
    if (count($this->arguments) > 1) {
      show_404();
    }

	if (!$this->arguments) {
      $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
        $in_page = $catalog_category['in_page'];
        $all_count = $this->catalog_model->get_catalog_count($catalog_category['id']);
        $pages = get_pages($page, $all_count, $in_page);
        $pagination_data = array(
            'pages' => $pages,
            'page' => $page,
            'prefix' => $this->page['path']
        );
        $items = $this->catalog_model->get_catalog($catalog_category['id'],$in_page, $in_page * ($page - 1), array('active' => 1), true);
        foreach ($items as &$item) {
            $category = $this->db->get_where('pr_catalog', array('id' => $item['parent_id']))->row_array();
            $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
            if ($images) {
              $item['image'] = $images[0];
            }
            $item['params_values'] = $this->catalog_model->get_catalog_values($item['id']);
        }
        unset($item);

      $data = array(
				'items' 			=> $items,
                'catalog_category'  => $catalog_category,
				'page' 				=> $page,
				'pagination' 	=> $this->load->view('templates/pagination', $pagination_data, true)
			);

      $template = $this->templates_model->get_template((int)$catalog_category['template_id']);
      return $this->render_template($template['path'],$data);
		}	else {
            $item = $this->catalog_model->get_catalog_one(array('system_name' => $this->arguments[0]));
            $item['params_fields'] = $this->catalog_model->get_catalog_params($item['parent_id']);
            foreach ($item['params_fields'] as &$field) {
                $field['values'] = explode("\n", unserialize($field['values']));
            }
            $data = array( 'item' => $item );
      
      if (!$data['item']) {
        show_404();
      }
      
      $category = $this->db->get_where('pr_catalog', array('id' => $data['item']['parent_id']))->row_array();
      $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$data['item']['system_name'].'/'),1,0);
      if ($images) {
        $data['item']['image'] = $images[0];
      }
        $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$data['item']['system_name'].'/'),10,1);
        if ($images) {
            $data['item']['images'] = $images;
        }
      $data['catalog_category'] = $catalog_category;
      
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
  
	function last_catalog ($name, $count, $on_main = FALSE) {
		$category = $this->catalog_model->get_catalog_one(array('system_name' => $name,'parent_id' => 0));
        if (!$category) {
          return FALSE;
        }

            $page_link = $this->db->get_where('pr_pages',array('id' => $category['page_id'][0]))->row_array();
            if (!$page_link) {
          return FALSE;
        }
        if ($on_main) {
            $data = array(
                'page_path'  => $page_link['path'],
                'title' => $category['params']['name_'.$this->language],
                'items' => $this->catalog_model->get_catalog($category['id'], $count, 0, array('active' => 1, 'on_main' => 1), true)
            );
        } else {
            $data = array(
                'page_path'  => $page_link['path'],
                'title' => $category['params']['name_'.$this->language],
                'items' => $this->catalog_model->get_catalog($category['id'], $count, 0, array('active' => 1), true)
            );
        }

        foreach ($data['items'] as &$item) {
          $item['image'] = "";
          $item['params_values'] = $this->catalog_model->get_catalog_values($item['id']);
          $category = $this->db->get_where('pr_catalog', array('id' => $item['parent_id']))->row_array();
          $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
          if ($images) {
            $item['image'] = $images[0];
          }
        }
        unset($item);

        return $this->render_template('templates/last_catalog',$data);
	}

    function catalog_categories () {
        $categories = $this->catalog_model->get_catalog();
        foreach ($categories as &$category) {
            $page_link = $this->db->get_where('pr_pages',array('id' => $category['page_id'][0]))->row_array();
            $category['path'] = $page_link['path'];
        }

        $data = array(
            'page_path'  => $page_link['path'],
            'categories' => $categories,
        );

        return $this->render_template('templates/catalog_categories',$data);
    }

    function catalog_filter ($parent_id) {
        $fields = $this->catalog_model->get_filter_params($parent_id);
        foreach ($fields as &$field) {
            $field['values'] = explode("\n", unserialize($field['values']));
        }

        $data = array(
            'fields'    => $fields
        );

        return $this->render_template('templates/catalog_filter',$data);
    }

    function filter() {
        $id = $this->input->get('c');
        $filter_params = $this->input->get();
        unset ($filter_params['c']);

        $category_links = $this->db->get_where('pr_catalog_links', array('item_id' => $id))->row_array();

        $category = $this->catalog_model->get_catalog_one(array('id' => $category_links['catalog_id']));

        if (!$category) {
            return FALSE;
        }

        $page_link = $this->db->get_where('pr_pages',array('id' => $category['page_id'][0]))->row_array();
        if (!$page_link) {
            return FALSE;
        }
        $data = array(
            'catalog_category'  => $category,
            'page_path' => $page_link['path'],
            'title'     => $category['params']['name_'.$this->language],
            'items'     => $this->catalog_model->get_catalog_join($category['id'], 20, 0, array('active' => 1), true, $filter_params)
        );

        foreach ($data['items'] as &$item) {
            $item['image'] = "";
            $category = $this->db->get_where('pr_catalog', array('id' => $item['parent_id']))->row_array();
            $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
            if ($images) {
                $item['image'] = $images[0];
            }
        }
        unset($item);

        return $this->render_template('templates/filter',$data);
    }
}