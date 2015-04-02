<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('calendar/models/calendar_model');
  }

  function index($page = 1) {

    return $this->render_template('templates/admin_index', array());
  }
    
}