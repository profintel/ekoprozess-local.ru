<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Libs_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();

    $this->load->model('acceptances/models/acceptances_model');
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
    if(exists_component('workshops')) {
      $items[] = array(
        'title' => 'Цеха',
        'link'  => $this->lang_prefix .'/admin/workshops/'
      );
    }
    if(exists_component('acceptances')) {
      $items[] = array(
        'title' => 'Статусы актов приемки',
        'link'  => $this->lang_prefix .'/admin/acceptances/statuses_acceptances'
      );
    }
    return $this->render_template('admin/menu', array(
      'title' => 'Справочники',
      'items' => $items
    ));
  }
  
  
}