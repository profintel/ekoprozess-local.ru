<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrators_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('models/admin_model');
  }

  function render_admin_panel(){
    //если авторизован админ, отображаем админ. панель
    if($this->admin_id){
      $data = array(
        'admin'     => $this->admin_model->get_admin($this->admin_id),
      );
      return $this->render_template('templates/admin_panel',$data);
    }
  }
  

}