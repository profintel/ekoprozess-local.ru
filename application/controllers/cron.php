<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Класс для кроновских скриптов
*/
class Cron extends PR_Controller {
  
  function __construct() {
    parent::__construct();
    
    if (!$this->input->is_cli_request()) {
      show_404();
    }
  }
  
}
?>