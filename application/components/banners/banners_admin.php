<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banners_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('banners/models/banners_model');
    $this->load->model('gallery/models/gallery_model');
  }
  
  /**
  * Меню компонента
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'Управление баннерами',
      'items' => array(
        array(
          'title' => 'Баннерные зоны',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'banner_zones/',
          'class' => 'banners-zones-icon'
        ),
        array(
          'title' => 'Баннеры',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'banners_list/',
          'class' => 'banners-title'
        ),
      )
    ));
  }
  
  /**
  * Баннерные зоны
  */
  function banner_zones() { 
    return $this->render_template('templates/admin_banner_zones', array(
			'items' => $this->banners_model->get_banner_zones(),
    ));
  }
  
	/**
	 *  Создание баннерной зоны
	 */	
  function create_banner_zone() {
		return $this->render_template('admin/inner', array(
      'title' => 'Добавление баннерной зоны',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_banner_zone_process/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Название:',
								'name' 			  => 'title',
								'id' 				  => 'banner_zone-title',
								'maxlength'   => 256,
                'description' => '',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'banner_zone-alias',
                'description' => 'Используется для прикрепления баннеров к данной зоне',
								'maxlength'   => 256,
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Ширина:',
								'name' 			  => 'width',
								'maxlength'   => 256,
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Высота:',
								'name' 			  => 'height',
								'maxlength'   => 256,
								'req' 			  => true
							),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'banner_zones/'
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'banner_zones/'
    ), TRUE);
  }
	
  function _create_banner_zone_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'width'       => (int)$this->input->post('width'),
      'height'      => (int)$this->input->post('height')
    );

    if ($this->db->get_where('banner_zones', array('system_name' => $params['system_name']))->row_array()) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
    
    $errors = $this->_validate_banner_zone_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
		
    $id = $this->banners_model->create_banner_zone($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать баннерную зону')));
    }
    
    send_answer();
	}
  
  function _validate_banner_zone_params($params) {
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано название'; }
    if (!$params['system_name']) { $errors[] = 'Не указано системное имя'; }
    if (!$params['width']) { $errors[] = 'Не указана ширина'; }
    if (!$params['height']) { $errors[] = 'Не указана высота'; }
    if (!preg_match('/^[A-Za-z0-9_-]{1,}$/', $params['system_name'])) { $errors[] = 'Некорректное значение для системного имени'; }
    return $errors;
  }
  
	/**
	 *  Редактирование баннерной зоны
	 *  @param $id - id баннерной зоны
	 */		
  function edit_banner_zone($id) {
		$item =  $this->banners_model->get_banner_zone(array('id' => $id));
    
		return $this->render_template('admin/inner', array(
      'title' => 'Редактирование баннерной зоны',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_banner_zone_process/'.$id.'/',
				'blocks' => array(
					array(
						'title' => 'Основные параметры',
						'fields' => array(
              array(
                'view' 			  => 'fields/text',
                'title' 		  => 'Название:',
                'name' 			  => 'title',
                'id' 				  => 'banner_zone-title',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['title'],
                'req' 			  => true
              ),
              array(
                'view' 			  => 'fields/text',
                'title' 		  => 'Системное имя:',
                'name' 			  => 'system_name',
                'id' 				  => 'banner_zone-alias',
                'description' => 'Используется для прикрепления баннеров к данной зоне',
                'maxlength'   => 256,
                'value'       => $item['system_name'],
                'req' 			  => true
              ),
              array(
                'view' 			  => 'fields/text',
                'title' 		  => 'Ширина:',
                'name' 			  => 'width',
                'maxlength'   => 256,
                'value'       => $item['width'],
                'req' 			  => true
              ),
              array(
                'view' 			  => 'fields/text',
                'title' 		  => 'Высота:',
                'name' 			  => 'height',
                'maxlength'   => 256,
                'value'       => $item['height'],
                'req' 			  => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 1
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'banner_zones/'
    ), TRUE);
  }

  function _edit_banner_zone_process($id) {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'width'       => (int)$this->input->post('width'),
      'height'     => (int)$this->input->post('height')
    );

    if ($this->db->get_where('banner_zones', array('system_name' => $params['system_name'], 'id !=' => $id))->row_array()) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_banner_zone_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if (!$this->banners_model->edit_banner_zone($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    send_answer();
	}
		
	/**
	 *  Удаление баннерной зоны
	 * @param $id - id баннерной зоны
	 */			
  function delete_banner_zone($id) {
    $this->banners_model->delete_banner_zone((int)$id);
    send_answer();
  }
  
  /**
  * Баннеры
  */
  function banners_list() { 
    return $this->render_template('templates/admin_banners', array(
			'items' => $this->banners_model->get_banners(),
    ));
  }
  
	/**
	 *  Создание баннера
	 */	
  function create_banner() {
		return $this->render_template('admin/inner', array(
      'title' => 'Добавление баннера',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_banner_process/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Название:',
								'name' 			  => 'title',
								'id' 				  => 'item-title',
								'maxlength'   => 256,
                'description' => '',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'item-alias',
                'description' => 'Используется для прикрепления файла к баннеру',
								'maxlength'   => 256,
								'req' 			  => true
							),
              array(
								'view'  => 'fields/file',
								'title' => 'Изображение (gif, png, jpg, swf):',
								'name'  => 'image'
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Ссылка:',
								'name' 			  => 'link',
                'description' => '',
								'maxlength'   => 256,
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Баннерная зона:',
                'description' => 'Зона на сайте, в которой будет отображаться баннер',
								'name' 			  => 'zone_id',
								'options'  	  => $this->banners_model->get_banner_zones(),
								'multiple' 	  => false,
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Веc:',
								'name' 			  => 'possibility',
                'description' => 'Чем больше вес баннера, тем чаще он отображается в выбранной банерной зоне',
								'maxlength'   => 10,
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Проекты:',
                'description' => 'проекты, на которых будет отображаться баннер',
								'name' 			  => 'projects[]',
								'options'  	  => $this->projects_model->get_projects(),
								'multiple' 	  => true
							),
							array(
								'view' 			  => 'fields/checkbox',
								'title' 		  => 'Открывать в новой вкладке:',
                'description' => '',
								'name' 			  => 'target_blank',
							),
							array(
								'view' 			  => 'fields/checkbox',
								'title' 		  => 'Баннер активен:',
                'description' => '',
								'name' 			  => 'active',
							),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'banners_list/'
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'banners_list/'
    ), TRUE);
  }
	
  function _create_banner_process() {
    $params = array(
      'title'        => htmlspecialchars(trim($this->input->post('title'))),
      'system_name'  => htmlspecialchars(trim($this->input->post('system_name'))),
      'link'         => htmlspecialchars(trim($this->input->post('link'))),
      'zone_id'      => (int)$this->input->post('zone_id'),
      'possibility'  => (int)$this->input->post('possibility'),
      'target_blank' => ($this->input->post('target_blank') ? 1 : 0),
      'active'       => ($this->input->post('active') ? 1 : 0),
    );
    
    $projects = $this->input->post('projects');
    
    $errors = $this->_validate_banners_params(array_merge($params, array('projects' => $projects)));
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if ($_FILES['image']['name']) {
      $image = upload_file($_FILES['image']);
      if (!$image) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png', 'swf'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if ($this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {               
        if (!resize_image($image, 180, 135)) {
          @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
          send_answer(array('errors' => array('Не удалось создать стандартную миниатюру')));
        };
      }
    } else {
      send_answer(array('errors' => array('Не загружено изображение')));
    }
    
    if ($this->db->get_where('banners', array('system_name' => $params['system_name']))->row()) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
    
    $id = $this->banners_model->create_banner($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать баннер')));
    }    
    
    $banner_zone = $this->banners_model->get_banner_zone(array('id' => $params['zone_id']));
    $gallery_params = array(
      array(
        'system_name' => 'gallery_system',
        'title' => 'Системная',      
        'childrens'   => array(
          array(
            'system_name' => $this->component['name'],
            'title'       => $this->component['title'],
            'childrens'   => array(
              array(
                'system_name' => $banner_zone['system_name'],
                'title'       => $banner_zone['title'],
                'childrens'   => array(
                  array(
                    'system_name' => $params['system_name'],
                    'title'       => $params['title'],
                    'images'      => array (
                      array (
                        'image'         => $image,
                        'images_thumbs' => array(
                          array(
                            'thumb'  => $this->gallery_model->thumb($image,180,135),
                            'width'  => 180,
                            'height' => 135
                          )
                        )
                      )
                    )
                  )
                ),
              ),
            ),
          ),
        ),
      )
    );
    if (!$this->gallery_model->add_gallery_images($gallery_params)) {
      $this->banners_model->delete_banner($id);
      send_answer(array('errors' => array('Не удалось сохранить изображение')));
    }

    if (!$this->banners_model->set_banner_projects($id, $projects)) {
      $this->banners_model->delete_banner($id);
      exit('Не удалось сохранить проекты');
    }
    send_answer();
	}
  
  function _validate_banners_params($params) {
    $errors = array();
    if (!$params['title'])       { $errors[] = 'Не указано название'; }
    if (!$params['system_name']) { $errors[] = 'Не указано системное имя'; }
    if (!$params['link'])        { $errors[] = 'Не указана ссылка'; }
    if (!$params['zone_id'])     { $errors[] = 'Не указана баннерная зона'; }
    if (!$params['projects'])    { $errors[] = 'Не выбран ни один проект'; }
    if ($params['possibility'] <= 1) { $errors[] = 'Минимальный вес баннера - 1'; }
    return $errors;
  }
  
	/**
	 *  Редактирование баннера
	 */	
  function edit_banner($id) {
    $item = $this->banners_model->get_banner($id);
		$item['image'] = "";
    $images = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$item['banner_zone']['system_name'].'/'.$item['system_name'].'/'),1,0);
    if ($images) {
      $item['image'] = $images[0]['image'];
    }
    
		return $this->render_template('admin/inner', array(
      'title' => 'Добавление баннера',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_banner_process/'.$id.'/'.$item['system_name'].'/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Название:',
								'name' 			  => 'title',
								'id' 				  => 'item-title',
								'maxlength'   => 256,
                'description' => '',
                'value'       => $item['title'],
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'item-alias',
                'description' => 'Используется для прикрепления файла к баннеру',
								'maxlength'   => 256,
                'value'       => $item['system_name'],
								'req' 			  => true
							),
              array(
								'view'  => 'fields/file',
								'title' => 'Изображение (gif, png, jpg, swf):',
								'name'  => 'image',
                'value' => $item['image'],
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Ссылка:',
								'name' 			  => 'link',
                'description' => '',
								'maxlength'   => 256,
                'value'       => $item['link'],
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Баннерная зона:',
                'description' => 'Зона на сайте, в которой будет отображаться баннер',
								'name' 			  => 'zone_id',
								'options'  	  => $this->banners_model->get_banner_zones(),
								'multiple' 	  => false,
                'value'       => $item['zone_id'],
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Вес:',
								'name' 			  => 'possibility',
                'description' => 'Чем больше вес баннера, тем чаще он отображается в выбранной банерной зоне',
								'maxlength'   => 10,
                'value'       => $item['possibility'],
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Проекты:',
                'description' => 'проекты, на которых будет отображаться баннер',
								'name' 			  => 'projects[]',
								'options'  	  => $this->projects_model->get_projects(),
                'value'       => $item['projects'],
								'multiple' 	  => true
							),
							array(
								'view' 			  => 'fields/checkbox',
								'title' 		  => 'Открывать в новой вкладке:',
                'description' => '',
								'name' 			  => 'target_blank',
                'checked'       => ($item['target_blank'] == 1 ? true : false),
							),
							array(
								'view' 			  => 'fields/checkbox',
								'title' 		  => 'Баннер активен:',
                'description' => '',
								'name' 			  => 'active',
                'checked'       => ($item['active'] == 1 ? true : false),
							),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 1
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'banners_list/'
    ), TRUE);
  }
	
  function _edit_banner_process($id, $system_name) {
    $banner = $this->banners_model->get_banner($id);
    
    $params = array(
      'title'        => htmlspecialchars(trim($this->input->post('title'))),
      'system_name'  => htmlspecialchars(trim($this->input->post('system_name'))),
      'link'         => htmlspecialchars(trim($this->input->post('link'))),
      'zone_id'      => (int)$this->input->post('zone_id'),
      'possibility'  => (int)$this->input->post('possibility'),
      'target_blank' => ($this->input->post('target_blank') ? 1 : 0),
      'active'       => ($this->input->post('active') ? 1 : 0),
    );
    $projects = $this->input->post('projects');
    
    $errors = $this->_validate_banners_params(array_merge($params, array('projects' => $projects)));
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
		
    if ($this->banners_model->get_banners(0,0, array('banners.system_name' => $params['system_name'], 'banners.id !=' => $id))) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
    
    if ($banner['zone_id'] != $params['zone_id']) {
      $banner_zone = $this->banners_model->get_banner_zone(array('id' => $params['zone_id']));
      $gallery_params = array(
        array(
          'system_name' => 'gallery_system',
          'title' => 'Системная',      
          'childrens'   => array(
            array(
              'system_name' => $this->component['name'],
              'title'       => $this->component['title'],
              'childrens'   => array(
                array(
                  'system_name' => $banner_zone['system_name'],
                  'title'       => $banner_zone['title']
                ),
              ),
            ),
          ),
        )
      );
      $album = $this->gallery_model->set_gallery_hierarchy($gallery_params);
      if ($album) {
        if (!$this->gallery_model->update_gallery_hierarchy(array('path' => $album['path'].$params['system_name'].'/','parent_id' => $album['id']), array('path' => '/gallery_system/banners/'.$banner['banner_zone']['system_name'].'/'.$params['system_name'].'/'))) {
          send_answer(array('errors' => array('Не удалось сменить путь для изображений.')));        
        }
      } else {
        send_answer(array('errors' => array('Не удалось сменить путь для изображений.')));        
      }
    }
    
    if (!$this->banners_model->edit_banner($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }

    if (!$this->banners_model->set_banner_projects($id, $projects)) {
      exit('Не удалось сохранить проекты');
    }
    
    if ($_FILES['image']['name']) {
      $banner_zone = $this->banners_model->get_banner_zone(array('id' => $params['zone_id']));
      
      //Удаление существующих изображений
      $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$banner_zone['system_name'].'/'.$system_name.'/'));     
      
      //Добавление нового изображения
      $image = upload_file($_FILES['image']);
      if (!$image) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png', 'swf'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if ($this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
        if (!resize_image($image, 180, 135)) {
          @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
          send_answer(array('errors' => array('Не удалось создать стандартную миниатюру')));
        };
      }
      
      $gallery_params = array(
        array(
          'system_name' => 'gallery_system',
          'title' => 'Системная',      
          'childrens'   => array(
            array(
              'system_name' => $this->component['name'],
              'title'       => $this->component['title'],
              'childrens'   => array(
                array(
                  'system_name' => $banner_zone['system_name'],
                  'title'       => $banner_zone['title'],
                  'childrens'   => array(
                    array(
                      'system_name' => $params['system_name'],
                      'title'       => $params['title'],
                      'images'      => array (
                        array (
                          'image'         => $image,
                          'images_thumbs' => array(
                            array(
                              'thumb'  => $this->gallery_model->thumb($image,180,135),
                              'width'  => 180,
                              'height' => 135
                            )
                          )
                        )
                      )
                    )
                  ),
                ),
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        send_answer(array('errors' => array('Не удалось сохранить изображение')));
      }    
    } elseif ($this->input->post('image_delete')) {
      send_answer(array('errors' => array('Не выбрано изображение')));
    }
    
    send_answer();
	}

	/**
	 *  Включение баннера
	 */	  
  function enable_banner($id) {
    $this->banners_model->edit_banner((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] .'/banners_list/');
  }

	/**
	 *  Выключение баннера
	 */	    
  function disable_banner($id) {
    $this->banners_model->edit_banner((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] .'/banners_list/');
  }
  
	/**
	 *  Удаление баннера
	 */			
  function delete_banner($id) { 
    $banner = $this->banners_model->get_banner($id);
    //Удаление существующих изображений
    $this->gallery_model->delete_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$banner['banner_zone']['system_name'].'/'.$banner['system_name'].'/')); 
    $this->banners_model->delete_banner((int)$id);     
    send_answer();
  }
    
}