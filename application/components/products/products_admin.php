<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('products/models/products_model');
  }
  
  /**
  * Просмотр списка видов вторсырья
  **/
  function index($parent_id = null) {
    return $this->render_template('admin/items', array(
      'title'           => 'Виды вторсырья',
      'search_path'     => '/admin'.$this->params['path'].'product/',
      'search_title'    => '',
      'parent_id'       => $parent_id,
      'component_item'  => array('name' => 'product', 'title' => ''),
      'move_path'       => '/admin/clients/_move_product/',
      'items'           => $this->products_model->get_products(array('parent_id' => $parent_id)),
      'back'            => $this->lang_prefix .'/admin/libs/',
    ));
  }

  /**
   *  Создание вида вторсырья
   */  
  function create_product($parent_id = null) {
    $parent = $this->products_model->get_product(array('id' => $parent_id));
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление вида вторсырья',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_product_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Название:',
                'name'        => 'title',
                'id'          => 'admin-item-title',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'        => 'fields/hidden',
                'title'       => 'Название родителя:',
                'name'        => 'parent_title',
                'value'       => ($parent ? $parent['title'] : ''),
              ),
              array(
                'view'        => 'fields/hidden',
                'title'       => 'Категория:',
                'name'        => 'parent_id',
                'value'       => $parent_id,
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
        )
      )),
      'back'  => $this->lang_prefix .'/admin'. $this->params['path']
    ), true);
  }

  function _create_product_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'parent_id'   => ($this->input->post('parent_id') ? $this->input->post('parent_id') : null),
      'order'       => $this->products_model->get_product_order($this->input->post('parent_id'))
    );
    if($this->input->post('parent_title')){
      $params['title_full'] = htmlspecialchars(trim($this->input->post('parent_title'))).', '.$params['title'];
    }

    $errors = $this->_validate_product_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->products_model->create_product($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать объект')));
    }

    send_answer();
  }
  
  function _validate_product_params($params) {
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано внутреннее имя'; }
    return $errors;
  }

  /*
  *  Редактирование вида вторсырья
  */  
  function edit_product($id) {
    $item = $this->products_model->get_product(array('id' => $id));
    $parent = $this->products_model->get_product(array('id' => $item['parent_id']));
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование вида вторсырья',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_product_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'  => 'fields/text',
                'id'    => 'admin-item-title',
                'title' => 'Название:',
                'name'  => 'title',
                'value' => $item['title'],
                'req'   => true
              ),
              array(
                'view'  => 'fields/hidden',
                'title' => 'Название родителя:',
                'name'  => 'parent_title',
                'value' => ($parent ? $parent['title'] : ''),
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      )),
      'back'  => $this->lang_prefix .'/admin'. $this->params['path']
    ), true);
  }

  function _edit_product_process($id) {    
    $params = array(
      'title'     => htmlspecialchars(trim($this->input->post('title'))),
    );
    if($this->input->post('parent_title')){
      $params['title_full'] = htmlspecialchars(trim($this->input->post('parent_title'))).', '.$params['title'];
    }

    $errors = $this->_validate_product_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if (!$this->products_model->update_product($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }

    send_answer();
  }
  
  /**
   * Перемещение вида вторсырья
  **/
  function _move_product() {
    $item_id = (int)str_replace('item-', '', $this->input->post('page'));
    $item = $this->products_model->get_product(array('id'=>(int)$item_id));
    
    if (!$item) {
      send_answer(array('messages' => array('Перемещаемый объект не найден')));
    }
    
    $dest_id = (int)str_replace('item-', '', $this->input->post('dest'));
    $dest = $this->products_model->get_product(array('id'=>(int)$dest_id));
    if (!$dest) {
      send_answer(array('messages' => array('Целевой объект не найден')));
    }
    
    $placement = trim($this->input->post('placement'));
    
    if (!$this->products_model->move_product($item_id, $dest_id, $placement)) {
      send_answer(array('messages' => array('Не удалось переместить объект')));
    }
    
    send_answer();
  }

  /**
   *  Удаление вида вторсырья
   * @param $id - id вида вторсырья
   */      
  function delete_product($id) {
  	send_answer(array('messages' => array('Удалять вид вторсырья запрещено')));

    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/clients/products/'.$id.'/'));
    $this->main_model->delete_params('products', $id);
    $this->products_model->delete_product((int)$id);
    send_answer();
  }
  
  
}