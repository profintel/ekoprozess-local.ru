<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar_site extends CI_Component {
  
  function __construct() {
    parent::__construct();

    $this->load->model('calendar/models/calendar_model');
  }
}