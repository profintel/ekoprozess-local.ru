<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Модель класса авторизации
*/
class Autorization_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function check_admin($username, $password) {
    return $this->db->get_where('admins', array('username' => $username, 'password' => md5($password)))->row_array();
  }
  
}