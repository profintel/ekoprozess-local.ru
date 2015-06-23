<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banners_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
		
    $this->load->model('banners/models/banners_model');
    $this->load->model('gallery/models/gallery_model');
  }
  
  function render($banner_zone) {
    $data['banner_zone'] = $this->banners_model->get_banner_zone(array('system_name' => $banner_zone));
    $data['banner'] = $this->banners_model->set_banner($this->project['id'], $banner_zone);
    $data['banner']['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$data['banner_zone']['system_name'].'/'.$data['banner']['system_name'].'/'),1,0);

    return $this->render_template('templates/site_banner',$data);
	}  
  
  function banner_transition() {
    $banner_id = ($this->uri->getParam('banner_id') ? $this->uri->getParam('banner_id') : 0);
    $banner_zone = ($this->uri->getParam('banner_zone') ? $this->uri->getParam('banner_zone') : 0);
    $banner_link = ($this->uri->getParam('banner_link') ? $this->uri->getParam('banner_link') : 0);
    if ($banner_id && $banner_zone && $banner_link) {
      $this->db->query('INSERT INTO pr_banner_clicks SET banner_id = '.$banner_id.', user_ip ="'.$_SERVER['REMOTE_ADDR'].'"');
      $this->db->query('UPDATE pr_banners SET clicks = clicks + 1 WHERE id ='.$banner_id);
    }
    header('Location: '. $this->lang_prefix . $banner_link);
	}  
}