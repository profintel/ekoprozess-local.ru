<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Libs_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
  * Просмотр меню компонента
  */
  function index() {
    $items = array();
    if(exists_component('products')) {
      $items[] = array(
        'title' => 'Виды вторсырья',
        'link'  => $this->lang_prefix .'/admin/products/'
      );
    }
    if(exists_component('clients')) {
      $items[] = array(
        'title' => 'Параметры таблицы клиентов',
        'link'  => $this->lang_prefix .'/admin/clients/client_params/'
      );
    }
    if(exists_component('cities')) {
      $items[] = array(
        'title' => 'Регионы / Города',
        'link'  => $this->lang_prefix .'/admin/cities/'
      );
    }
    return $this->render_template('admin/menu', array(
      'title' => 'Справочники',
      'items' => $items
    ));
  }
  
  
}