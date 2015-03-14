<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Вспомогательный класс установщика
* Создает структуру БД и вставляет данные
*/
class Install_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
  * Создает суперпользователя
  * @param $username - логин
  * @param $password - пароль
  * @return boolean
  */
  function create_superuser($username, $password) {
    return (bool)$this->db->insert('admins', array(
      'username' => $username,
      'password' => $password
    ));
  }
  
  /**
  * Создает дефолтный проект
  * @param $domain - доменное имя
  * @param $title - внутреннее имя
  * @param $active - активность
  * @return boolean
  */
  function create_first_project($domain, $title, $active = 1) {
    return (bool)$this->db->insert('projects', array(
      'domain' => $domain,
      'title'  => $title,
      'active' => $active
    ));
  }
  
}