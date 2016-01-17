<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('clients/models/clients_model');
    $this->load->model('store/models/store_model');
  }
  
  /**
  * Просмотр меню компонента
  */
  function index() {
    // определяем типы склада
    $types = $this->store_model->get_store_types(array('active'=>1));
    if(!$types){
      show_error('Не найдены типы склада');
    }
    $items = array();
    foreach ($types as $key => $type) {
      $items[] = array(
        'title' => $type['title'],
        'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'products/'.$type['id']
      );
    }
    return $this->render_template('admin/menu', array(
      'title' => 'Склад',
      'items' => $items
    ));
  }
  
  /**
  * Просмотр меню по типу склада
  */
  function products($type_id) {
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    return $this->render_template('admin/menu', array(
      'title' => 'Склад: Первичная продукция',
      'items' => array(
        array(
          'title' => 'Приход',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'comings/'.$type_id.'/'
        ),
        array(
          'title' => 'Расход',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'expenditures/'.$type_id.'/'
        ),
        array(
          'title' => 'Остаток',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'remains/'.$type_id.'/'
        ),
      )
    ));
  }
  
  /**
  * Приход продукции по типу склада
  */
  function comings($type_id) {
    $type = $this->store_model->get_store_type(array('id'=>(int)$type_id));
    if(!$type){
      show_error('Не найден тип склада');
    }
    return $this->render_template('admin/menu', array(
      'title' => 'Склад: Первичная продукция. Приход',
      'items' => array(
        
      )
    ));
  }

}