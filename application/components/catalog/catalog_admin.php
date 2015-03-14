<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalog_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
				
    $this->load->model('templates/models/templates_model');
		$this->load->model('catalog/models/catalog_model');
		$this->load->model('gallery/models/gallery_model');
  }

  function index($parent_id = 0, $page = 1) {
    if (!$parent_id) {
      $items = $this->catalog_model->get_catalog();
    } else {
      $category = $this->catalog_model->get_catalog_one(array('id' => $parent_id));
      $in_page = 50;
      $all_count = $this->catalog_model->get_catalog_count($parent_id);
      $pages = get_pages($page, $all_count, $in_page);
      $pagination_data = array(
        'pages' => $pages,
        'page' => $page,
        'prefix' => '/admin/catalog/'. $parent_id .'/'
      );
			$items = $this->catalog_model->get_catalog($parent_id,$in_page, $in_page * ($page - 1));
    }

		return $this->render_template('templates/index', array(
      'parent_id' => $parent_id,
      'items' => $items,
      'pagination' => ($parent_id ? $this->load->view('admin/pagination', $pagination_data, true) : false),
    ));
  }
	
	/**
	 *  Создание категории каталога
	 */	
  function create_category() {
    $pages = $this->db->get('pr_pages')->result_array();
		if ($pages) {
			foreach ($pages as &$page) {
				$project = $this->db->get('pr_projects',array('id', $page['project_id']))->row_array();
				$page['title'] = $project['title'].': '.$page['title'];
			}
			unset($page);
		}
    $languages = $this->languages_model->get_languages(1, 0);
		return $this->render_template('admin/inner', array(
      'title' => 'Добавление категории каталога',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_category_process/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'id' 			  => 'catalog-title',
								'maxlength'       => 256,
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 			  => 'catalog-alias',
                'description' => 'Используется для отображения вложений данной категории в теле страницы',
								'maxlength'   => 256,
								'req' 			  => true
							),
							array(
								'view' 			=> 'fields/text',
								'title' 		=> 'Название:',
								'name' 			=> 'name',
								'languages'     => $languages,
								'maxlength'     => 256
							),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $this->templates_model->get_template("catalog"),
                'req'     => TRUE
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
						)
					),
					array(
						'title' => 'Дополнительные параметры',
						'fields' => array(
							array(
								'view'  => 'fields/file',
								'title' => 'Изображение (gif, png, jpg):',
								'name'  => 'image'
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Связанные страницы:',
                'description' => 'Укажите страницу, на которой будут отображаться вложения данной категории',
								'name' 			  => 'page',
								'options'  	  => $pages,
								'multiple' 	  => false
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Вложений на странице:',
                'description' => 'Количество вложений, отображаемых на одной странице',
								'name' 			  => 'in_page',
								'maxlength'   => 3
							),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
						)
					)
				)
			)),
    ), TRUE);
  }
	
  function _create_category_process() {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'template_id' => (int)$this->input->post('template_id'),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'in_page'     => (int)$this->input->post('in_page'),
      'active'      => 1
    );
    
    if ($_FILES['image']['name']) {
      $image = upload_file($_FILES['image']);      
      if (!$image) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if (!resize_image($image, 180, 135)) {
        send_answer(array('errors' => array('Не удалось создать миниатюру')));
      };
    }
 
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }

    if (!$this->catalog_model->_validate_catalog_system_name($params['system_name'])) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
		
    $id = $this->catalog_model->create_catalog($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать категорию каталога')));
    }
    
    $links = array(
      'page_id' => $this->input->post('page')
    );
    if (!$this->catalog_model->set_catalog_links($id, $links)) {
      send_answer(array('errors' => array('Не удалось сохранить связи')));
    }
    
    if (!$this->main_model->set_params('catalog', $id, $multiparams)) {
      $this->catalog_model->delete_catalog($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (isset($image) && $image) {
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
                  'system_name' => $params['system_name'],
                  'title'       => $params['title'],
                  'images'      => array(
                    array (
                      'image'         => $image,
                      'images_thumbs' => array(
                        array(
                          'thumb' => $this->gallery_model->thumb($image,180,135),
                          'width' => 180,
                          'height' => 135
                        )
                      )
                    )
                  )
                )
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        $this->catalog_model->delete_catalog($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }

    send_answer();
	}
  
  function _validate_params($params) {
    $errors = array();
    if (!$params['template_id']) { $errors[] = 'Не указан шаблон'; }
    if (!$params['title']) { $errors[] = 'Не указано внутреннее имя'; }
    if (!$params['system_name']) { $errors[] = 'Не указано системное имя'; }
    if (!preg_match('/^[A-Za-z0-9_-]{1,}$/', $params['system_name'])) { $errors[] = 'Некорректное значение для системного имени'; }
    if (isset($params['in_page']) && !$params['in_page']) { $errors[] = 'Не указано количество публикаций на странице'; }
    return $errors;
  }

	/**
	 *  Редактирование категории каталога
	 *  @param $id - id категории
	 */		
  function edit_category($id) {
    $pages = $this->db->get('pr_pages')->result_array();
		if ($pages) {
			foreach ($pages as &$page) {
				$project = $this->db->get('pr_projects',array('id', $page['project_id']))->row_array();
				$page['title'] = $project['title'].': '.$page['title'];
			}
			unset($page);
		}    
    $languages = $this->languages_model->get_languages(1, 0);
		$item =  $this->catalog_model->get_catalog_one(array('id' => $id));
		$item['image'] = "";
    $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$item['system_name'].'/'),1,0);
    foreach ($item['images'] as $image) {
      $item['image'] = $image['image'];
    }
    
		return $this->render_template('admin/inner', array(
      'title' => 'Редактирование категории каталога',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_category_process/'.$id.'/',
				'blocks' => array(
					array(
						'title' => 'Основные параметры',
						'fields' => array(
							array(
								'view' 			  => 'fields/text',
								'title'			  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'id' 				  => 'catalog-title',
								'maxlength'   => 256,
								'value' 		  => $item['title'],
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'catalog-alias',
								'maxlength'   => 256,
								'value' 		  => $item['system_name'],
                'description' => 'Используется для отображения вложений данной категории в теле страницы',
								'req' 			  => true
							),
							array(
								'view' 			=> 'fields/text',
								'title' 		=> 'Название:',
								'name' 			=> 'name',
								'languages' => $languages,
								'value' 		=> $item['params'],
								'maxlength' => 256
							),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $item['template_id'],
                'req'     => TRUE
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
						)
					),
					array(
						'title' => 'Дополнительные параметры',
						'fields' => array(
							array(
								'view' 	=> 'fields/file',
								'title' => 'Изображение (gif, png, jpg):',
								'value' => $item['image'],
								'name'	=> 'image'
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Связанные страницы:',
                'description' => 'Укажите страницу, на которой будут отображаться вложения данной категории',
								'name' 			  => 'page',
								'options'  	  => $pages,
								'value'			  => (isset($item['page_id']) ? $item['page_id'] : 0),
								'multiple' 	  => false
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Вложений на странице:',
                'description' => 'Количество вложений, отображаемых на одной странице',
								'name' 			  => 'in_page',
                'value'       => $item['in_page'],
								'maxlength'   => 3
							),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
						)
					)
				)
			)),
    ), TRUE);
  }

  function _edit_category_process($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $item = $this->catalog_model->get_catalog_one(array('id' => $id));
		$item['image'] = "";
    $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$item['system_name'].'/'),1,0);
    foreach ($item['images'] as $image) {
      $item['image'] = $image['image'];
    }
		unset($image);
    
    $params = array(
      'template_id' => (int)$this->input->post('template_id'),
      'title' => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'in_page'     => (int)$this->input->post('in_page'),
      'active' => 1
    );
    
    if ($_FILES['image']['name']) {
      if ($item['image']) {
        $this->gallery_model->delete_image(array('image' => $item['image']));
      }
      $image = upload_file($_FILES['image']);
      if (!$image) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      resize_image($image, 180, 135);
    } elseif ($this->input->post('image_delete')) {
      $this->gallery_model->delete_image(array('image' => $item['image']));
    }
 
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }
		
    if (!$this->catalog_model->_validate_catalog_system_name($params['system_name'],$id)) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if (!$this->catalog_model->edit_catalog($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    $links = array(
      'page_id' => $this->input->post('page')
    );		
    if (!$this->catalog_model->set_catalog_links($id, $links)) {
      send_answer(array('errors' => array('Не удалось сохранить связи')));
    }
    
    if (!$this->main_model->set_params('catalog', $id, $multiparams)) {
      $this->catalog_model->delete_catalog($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    $gallery = $this->gallery_model->get_gallery_one(array('system_name' => $item['system_name']));
    if ($gallery) {
      $this->gallery_model->edit_gallery($gallery['id'], array('system_name' => $params['system_name'], 'path' => '/gallery_system/'.$this->component['name'].'/'.$params['system_name'].'/'));
    }
    
    if (isset($image) && $image) {
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
                  'system_name' => $params['system_name'],
                  'title'       => $params['title'],
                  'images'      => array(
                    array (
                      'image'         => $image,
                      'images_thumbs' => array(
                        array(
                          'thumb' => $this->gallery_model->thumb($image,180,135),
                          'width' => 180,
                          'height' => 135
                        )
                      )
                    )
                  )
                )
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        $this->catalog_model->delete_catalog($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }
    
    send_answer();
	}
		
	/**
	 *  Удаление категории каталога
	 * @param $id - id категории каталога
	 */			
  function delete_category($id) {
    $this->catalog_model->delete_category((int)$id);
    send_answer();
  }

    /**
     *  Создание параметров категории каталога
     */
    function edit_category_params($id,$page = 1) {

        $category = $this->catalog_model->get_catalog_one(array('id' => $id));
        $in_page = 50;
        $all_count = $this->catalog_model->get_catalog_params_count($id);
        $pages = get_pages($page, $all_count, $in_page);
        $pagination_data = array(
            'pages' => $pages,
            'page' => $page,
            'prefix' => '/admin/catalog/'. $id .'/'
        );
        $items = $this->catalog_model->get_catalog_params($id,$in_page, $in_page * ($page - 1));

        return $this->render_template('templates/params', array(
            'id' => $id,
            'items' => $items,
            'pagination' => ($id ? $this->load->view('admin/pagination', $pagination_data, true) : false),
        ));
    }

    function create_category_param($id) {
        $types = array(
            '0'=> array('id'=>'1', 'title'=>'текст'),
            '1'=> array('id'=>'2','title'=>'список'),
            '2'=> array('id'=>'3','title'=>'ползунок'),
            '3'=> array('id'=>'4','title'=>'переключатель'),
            '4'=> array('id'=>'5','title'=>'флажок')
        );
        $languages = $this->languages_model->get_languages(1, 0);
        return $this->render_template('admin/inner', array(
            'title' => 'Добавление параметров категории каталога',
            'html' => $this->view->render_form(array(
                    'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_category_param_process/'.$id,
                    'blocks' => array(
                        array(
                            'title' 	=> 'Основные параметры',
                            'fields' 	=> array(
                                array(
                                    'view' 			=> 'fields/text',
                                    'title' 		=> 'Название:',
                                    'name' 			=> 'name',
                                    'maxlength'     => 256
                                ),
                                array(
                                    'view'    => 'fields/select',
                                    'title'   => 'Тип:',
                                    'name'    => 'type',
                                    'options' => $types,
                                    'req'     => TRUE
                                ),
                                array(
                                    'view'    => 'fields/textarea',
                                    'title'   => 'Значения:',
                                    'description' => 'Список - каждое на новой строке<br>Ползунок - мин. и макс. на разных строках',
                                    'name'    => 'values',
                                    'req'     => FALSE
                                ),
                                array(
                                    'view' 		=> 'fields/checkbox',
                                    'title' 	=> 'Использовать в фильтре',
                                    'name'		=> 'in_filter',
                                ),
                                array(
                                    'view'     => 'fields/submit',
                                    'class'    => 'icon_small accept_i_s',
                                    'title'    => 'Создать',
                                    'type'     => 'ajax',
                                    'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'edit_category_params/'.$id
                                )
                            )
                        )
                    )
                )),
            'back' => $this->lang_prefix .'/admin'. $this->params['path'].'edit_category_params/'.$id.'/'
        ), TRUE);
    }

    function _create_category_param_process($id) {
        $languages = $this->languages_model->get_languages(1, 0);

        $params = array(
            'catalog_id' => $id,
            'name'       => htmlspecialchars(trim($this->input->post('name'))),
            'type'       => (int)$this->input->post('type'),
            'values'     => serialize($this->input->post('values')),
            'in_filter' => ($this->input->post('in_filter') ? 1 : 0)
        );

        $id = $this->catalog_model->create_catalog_params($params);
        if (!$id) {
            send_answer(array('errors' => array('Не удалось создать параметр категории каталога')));
        }

        send_answer();
    }

    function edit_category_param($id) {
        $params = $this->catalog_model->get_catalog_param($id);
        $types = array(
            '0'=> array('id'=>'1', 'title'=>'текст'),
            '1'=> array('id'=>'2','title'=>'список'),
            '2'=> array('id'=>'3','title'=>'ползунок'),
            '3'=> array('id'=>'4','title'=>'переключатель'),
            '4'=> array('id'=>'5','title'=>'флажок')
        );
        $languages = $this->languages_model->get_languages(1, 0);
        return $this->render_template('admin/inner', array(
            'title' => 'Редактирование параметров категории каталога',
            'html' => $this->view->render_form(array(
                    'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_category_param_process/'.$id,
                    'blocks' => array(
                        array(
                            'title' 	=> 'Основные параметры',
                            'fields' 	=> array(
                                array(
                                    'view' 			=> 'fields/text',
                                    'title' 		=> 'Название:',
                                    'name' 			=> 'name',
                                    'value'         => (isset($params['name']) ? $params['name'] : ''),
                                    'maxlength'     => 256
                                ),
                                array(
                                    'view'    => 'fields/select',
                                    'title'   => 'Тип:',
                                    'name'    => 'type',
                                    'options' => $types,
                                    'value'   => (isset($params['type']) ? $params['type'] : 0),
                                    'req'     => TRUE
                                ),
                                array(
                                    'view'    => 'fields/textarea',
                                    'title'   => 'Значения:',
                                    'name'    => 'values',
                                    'description' => 'Список - каждое на новой строке<br>Ползунок - мин. и макс. на разных строках',
                                    'value'   => (isset($params['values']) ? unserialize($params['values']) : ''),
                                    'req'     => FALSE
                                ),
                                array(
                                    'view' 		=> 'fields/checkbox',
                                    'title' 	=> 'Использовать в фильтре',
                                    'name'		=> 'in_filter',
                                    'checked'   => $params['in_filter'],
                                ),
                                array(
                                    'view'     => 'fields/submit',
                                    'class'    => 'icon_small accept_i_s',
                                    'title'    => 'Редактировать',
                                    'type'     => 'ajax',
                                    'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'edit_category_params/'.$params['catalog_id']
                                )
                            )
                        )
                    )
                )),
            'back' => $this->lang_prefix .'/admin'. $this->params['path'].'edit_category_params/'.$params['catalog_id'].'/'
        ), TRUE);
    }

    function _edit_category_param_process($id) {
        $params = array(
            'name'       => htmlspecialchars(trim($this->input->post('name'))),
            'type'       => (int)$this->input->post('type'),
            'values'     => serialize($this->input->post('values')),
            'in_filter' => ($this->input->post('in_filter') ? 1 : 0),
        );

        $ids = $this->catalog_model->edit_catalog_params($id,$params);
        if (!$ids) {
            send_answer(array('errors' => array('Не удалось изменить параметр категории каталога')));
        }

        send_answer();
    }

    function delete_category_param($id) {
        $this->catalog_model->delete_catalog_params((int)$id);
        send_answer();
    }

    function in_filter_on_param($id) {
        $this->catalog_model->update_catalog_param((int)$id, array('in_filter' => 1));
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    function in_filter_off_param($id) {
        $this->catalog_model->update_catalog_param((int)$id, array('in_filter' => 0));
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }


	/**
	 *  Добавление вложений
	 * @param $parent_id - id категории каталога
	 */		
  function create_catalog($parent_id) {
      $parent = $this->catalog_model->get_catalog_one(array('id' => $parent_id));
      $languages = $this->languages_model->get_languages(1, 0);
      $params = $this->catalog_model->get_catalog_params($parent['id']);
      $params_fields = array();
      foreach ($params as $param) {
          $options_value = explode("\n", unserialize($param['values']));
          $options = array();
          $options[] = array('id' => 0, 'title' => 'Выберите из списка');
          foreach ($options_value as $key=>$value) {
              $options[] = array('id' => $key+1, 'title' => $value);
          }
          $params_fields[] = array(
              'view' 	=> 'fields/'.(($param['type'] == 2 || $param['type'] == 4 || $param['type'] == 5) ? 'select' : 'text'),
              'title' => $param['name'],
              'name' 	=> 'params['.$param['id'].']',
              'options' => $options
          );
      }
		return $this->render_template('admin/inner', array(
      'title' => 'Добавление вложений в категории "'.htmlspecialchars($parent['title']).'"',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_catalog_process/'. $parent_id .'/',
				'blocks' => array(
					array(
						'title' => 'Основные параметры',
						'fields' => array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'id' 				  => 'catalog-title',
								'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'catalog-alias',
								'maxlength'   => 256,
                'description' => 'Используется для отображения вложения в теле страницы',
								'req' 			  => true
							),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $this->templates_model->get_template("catalog_one"),
                'req'     => TRUE
              ),
							array(
								'view' 			=> 'fields/textarea',
								'title' 		=> 'Анонс:',
								'name' 			=> 'text_small',
								'languages' => $languages
							),
							array(
								'view' 			=> 'fields/editor',
								'title' 		=> 'Полный текст:',
								'name' 			=> 'text_full',
								'languages' => $languages,
								'toolbar' 	=> 'Full'
							),
                            array(
                                'view'	=> 'fields/file',
                                'title' => 'Основное изображение (gif, png, jpg):',
                                'name' 	=> 'image'
                            ),
                            array(
                                'view'	=> 'fields/file',
                                'title' => 'Дополнительные изображения (gif, png, jpg):',
                                'name' 	=> 'images[]',
                                'multiple'    => TRUE
                            ),
							array(
								'view' 	=> 'fields/checkbox',
								'title' => 'Вложение активно',
								'name' 	=> 'active'
							),
                            array(
                                'view' 	=> 'fields/checkbox',
                                'title' => 'Показывать на Главной',
                                'name' 	=> 'on_main'
                            ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/'
              )
						)
					),
                    array(
                        'title' => 'Параметры',
                        'fields' => $params_fields
                    ),
					array(
						'title' => 'Заголовки и SEO',
						'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок вложения:',
                'name'        => 'name',
                'description' => 'Используется при отображении вложения на странице',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок страницы:',
                'description' => 'Отображается в заголовке окна браузера (мета-тег TITLE)',
                'name'        => 'title',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок в теле страницы:',
                'description' => 'Подставляется в тег H1',
                'name'        => 'h1',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Ключевые слова:',
                'description' => 'Подставляются в мета-тег KEYWORDS',
                'name'        => 'keywords',
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание:',
                'description' => 'Подставляется в мета-тег DESCRIPTION',
                'name'        => 'description',
                'languages'   => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/'
              )
						)
					),
				)
			)),
    ), TRUE);		
	}
	
  function _create_catalog_process($parent_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'parent_id' => $parent_id,
      'title' => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'template_id' => (int)$this->input->post('template_id'),
      'active' => ($this->input->post('active') ? 1 : 0),
      'on_main' => ($this->input->post('on_main') ? 1 : 0),
      'tm_start' => ($this->input->post('tm_start') ? date('Y-m-d H:i:s', strtotime($this->input->post('tm_start'))) : date('Y-m-d H:i:s')),
      'tm_end' => ($this->input->post('tm_end') ? date('Y-m-d H:i:s', strtotime($this->input->post('tm_end'))) : NULL)
    );
    
    if ($_FILES['image']['name']) {
      $image = upload_file($_FILES['image']);      
      if (!$image) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if (!resize_image($image, 180, 135)) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Не удалось создать миниатюру')));
      };
    }
 
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'text_small' => htmlspecialchars(trim($this->input->post('text_small_'. $language['name']))),
        'text_full' => htmlspecialchars(trim($this->input->post('text_full_'. $language['name']))),
        'title' => htmlspecialchars(trim($this->input->post('title_'. $language['name']))),
        'h1' => htmlspecialchars(trim($this->input->post('h1_'. $language['name']))),
        'keywords' => htmlspecialchars(trim($this->input->post('keywords_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))      
			);
    }
		
    if (!$this->catalog_model->_validate_catalog_system_name($params['system_name'])) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->catalog_model->create_catalog($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать вложение')));
    }

    $category_params = $this->input->post('params');
      if ($category_params) {
          $this->catalog_model->set_category_params($id, $category_params);
      }

    if (!$this->main_model->set_params('catalog', $id, $multiparams)) {
      $this->catalog_model->delete_catalog($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (@$image) {
      $category = $this->catalog_model->get_catalog_one(array('id' => $parent_id));
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
                  'system_name' => $category['system_name'],
                  'title'       => $category['title'],
                  'childrens'   => array(
                    array(
                      'system_name' => $params['system_name'],
                      'title'       => $params['title'],
                      'images'      => array(
                        array (
                          'image'         => $image,
                          'images_thumbs' => array(
                            array(
                              'thumb' => $this->gallery_model->thumb($image,180,135),
                              'width' => 180,
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
        $this->catalog_model->delete_catalog($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }

      unset($image);

      if ($_FILES['images']['name']) {
          foreach ($_FILES['images']['name'] as $key=>$im) {
              $file['name'] = $_FILES['images']['name'][$key];
              $file['type'] = $_FILES['images']['type'][$key];
              $file['tmp_name'] = $_FILES['images']['tmp_name'][$key];
              $file['error'] = $_FILES['images']['error'][$key];
              $file['size'] = $_FILES['images']['size'][$key];
              $image = upload_file($file);
              if (!$image) {
                  send_answer(array('errors' => array('Ошибка при загрузке изображения')));
              }
              if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
                  @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                  send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
              }
              if (!resize_image($image, 180, 135)) {
                  @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                  send_answer(array('errors' => array('Не удалось создать миниатюру')));
              }
              if (@$image) {
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
                                          'system_name' => $category['system_name'],
                                          'title'       => $category['title'],
                                          'childrens'   => array(
                                              array(
                                                  'system_name' => $params['system_name'],
                                                  'title'       => $params['title'],
                                                  'images'      => array(
                                                      array (
                                                          'image'         => $image,
                                                          'images_thumbs' => array(
                                                              array(
                                                                  'thumb' => $this->gallery_model->thumb($image,180,135),
                                                                  'width' => 180,
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
                      $this->catalog_model->delete_catalog($id);
                      send_answer(array('errors' => array('Не удалось сохранить изображения')));
                  }
              }

              unset($image);
          }
      }
    
    send_answer();
	}
	
	/**
	 *  Редактирование вложений
	 * @param $parent_id - id вложения
	 */		
  function edit_catalog($id) {
	$item = $this->catalog_model->get_catalog_one(array('id' => $id));
    $category = $this->catalog_model->get_catalog_one(array('id' => $item['parent_id']));
	$item['image'] = "";
    $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
    foreach ($item['images'] as $image) {
      $item['image'] = $image['image'];
    }
    $params = $this->catalog_model->get_catalog_params($category['id']);
    $params_fields = array();
      foreach ($params as $param) {
          $options_value = explode("\n", unserialize($param['values']));
          $options = array();
          $options[] = array('id' => 0, 'title' => 'Выберите из списка');
          foreach ($options_value as $key=>$value) {
              $options[] = array('id' => $key+1, 'title' => $value);
          }
          $params_fields[] = array(
              'view' 	=> 'fields/'.(($param['type'] == 2 || $param['type'] == 4 || $param['type'] == 5) ? 'select' : 'text'),
              'title' => $param['name'],
              'name' 	=> 'params['.$param['id'].']',
              'options' => $options,
              'value'   => (@$item['values'][$param['id']] ? $item['values'][$param['id']] : '')
          );
      }
    unset($image);
    $languages = $this->languages_model->get_languages(1, 0);    
    
		return $this->render_template('admin/inner', array(
      'title' => 'Редактирование вложения "'.htmlspecialchars($item['title']).'"',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_catalog_process/'. $id .'/',
				'blocks' => array(
					array(
						'title' => 'Основные параметры',
						'fields' => array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'id' 				  => 'catalog-title',
								'maxlength'   => 256,
								'value' 		  => $item['title'],
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'catalog-alias',
								'maxlength'   => 256,
								'value' 		  => $item['system_name'],
                'description' => 'Используется для отображения вложения в теле страницы',
								'req' 			  => true
							),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $item['template_id'],
                'req'     => TRUE
              ),
							array(
								'view'			=> 'fields/textarea',
								'title' 		=> 'Анонс:',
								'name' 			=> 'text_small',
								'value' 		=> $item['params'],
								'languages' => $languages
							),
							array(
								'view' 			=> 'fields/editor',
								'title' 		=> 'Полный текст:',
								'name' 			=> 'text_full',
								'value' 		=> $item['params'],
								'languages' => $languages,
								'toolbar' 	=> 'Full'
							),
							array(
								'view'	=> 'fields/file',
								'title' => 'Основное изображение (gif, png, jpg):',
								'name' 	=> 'image',
								'value' => $item['image'],
							),
                            array(
                                'view'	=> 'fields/file',
                                'title' => 'Дополнительные изображения (gif, png, jpg):',
                                'name' 	=> 'images[]',
                                'value' => $item['images'][0]['gallery_id'],
                                'multiple'    => TRUE
                            ),
							array(
								'view' 		=> 'fields/checkbox',
								'title' 	=> 'Вложение активно',
								'name'		=> 'active',
								'checked' => $item['active'],
							),
                            array(
                                'view' 	=> 'fields/checkbox',
                                'title' => 'Показывать на Главной',
                                'name' 	=> 'on_main',
                                'checked' => $item['on_main'],
                            ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
						)
					),
					array(
						'title' => 'Параметры',
						'fields' => $params_fields
					),
					array(
						'title' => 'Заголовки и SEO',
						'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок вложения:',
                'name'        => 'name',
                'description' => 'Используется при отображении вложения на странице',
								'value' 			=> $item['params'],
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок страницы:',
                'description' => 'Отображается в заголовке окна браузера (мета-тег TITLE)',
                'name'        => 'title',
								'value' 			=> $item['params'],
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок в теле страницы:',
                'description' => 'Подставляется в тег H1',
                'name'        => 'h1',
								'value' 			=> $item['params'],
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Ключевые слова:',
                'description' => 'Подставляются в мета-тег KEYWORDS',
                'name'        => 'keywords',
								'value' 			=> $item['params'],
                'languages'   => $languages
              ),
              array(
                'view'        => 'fields/textarea',
                'title'       => 'Описание:',
                'description' => 'Подставляется в мета-тег DESCRIPTION',
                'name'        => 'description',
								'value' 			=> $item['params'],
                'languages'   => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
						)
					),
				)
			)),
			'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $item['parent_id'] .'/'
    ), TRUE);		
	}
	
  function _edit_catalog_process($id) {
    $item = $this->catalog_model->get_catalog_one(array('id' => $id));
    $category = @$item['parent_id'] ? $this->catalog_model->get_catalog_one(array('id' => $item['parent_id'])) : '';
	$item['image'] = "";
    $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.($category ? $category['system_name'].'/' : '').$item['system_name'].'/'),1,0);
    foreach ($item['images'] as $image) {
      $item['image'] = $image['image'];
    }
    unset($image);		
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'title' => htmlspecialchars(trim($this->input->post('title'))),
      'template_id' => htmlspecialchars(trim($this->input->post('template_id'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'active' => ($this->input->post('active') ? 1 : 0),
      'on_main' => ($this->input->post('on_main') ? 1 : 0),
      'tm_start' => ($this->input->post('tm_start') ? date('Y-m-d H:i:s', strtotime($this->input->post('tm_start'))) : date('Y-m-d H:i:s')),
      'tm_end' => ($this->input->post('tm_end') ? date('Y-m-d H:i:s', strtotime($this->input->post('tm_end'))) : NULL)
    );

    if ($_FILES['image']['name']) {
      if ($item['image']) {
        $this->gallery_model->delete_image(array('image' => $item['image']));
      }
      $image = upload_file($_FILES['image']);
      if (!$image) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }      
      if (!resize_image($image, 180, 135)) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Не удалось создать миниатюру')));
      }
    } elseif ($this->input->post('image_delete')) {
      $this->gallery_model->delete_image(array('image' => $item['image']));
    }
 
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'text_small' => htmlspecialchars(trim($this->input->post('text_small_'. $language['name']))),
        'text_full' => htmlspecialchars(trim($this->input->post('text_full_'. $language['name']))),
        'title' => htmlspecialchars(trim($this->input->post('title_'. $language['name']))),
        'h1' => htmlspecialchars(trim($this->input->post('h1_'. $language['name']))),
        'keywords' => htmlspecialchars(trim($this->input->post('keywords_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name'])))        
			);
    }
		
    if (!$this->catalog_model->_validate_catalog_system_name($params['system_name'],$id)) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
		
    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if (!$this->catalog_model->edit_catalog($id, $params)) {
      send_answer(array('errors' => array('Не удалось отредактировать вложение')));
    }
    
    if (!$this->main_model->set_params('catalog', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }

      $category_params = $this->input->post('params');
      if ($category_params) {
          $this->catalog_model->edit_category_params($id, $category_params);
      }
        
    $gallery = $this->gallery_model->get_gallery_one(array('system_name' => $item['system_name']));
    if ($gallery) {
      $this->gallery_model->edit_gallery($gallery['id'], array('system_name' => $params['system_name'], 'path' => '/gallery_system/'.$this->component['name'].'/'.$category['system_name'].'/'.$params['system_name'].'/'));
    }
    
    if (@$image) {
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
                  'system_name' => $category['system_name'],
                  'title'       => $category['title'],
                  'childrens'   => array(
                    array(
                      'system_name' => $params['system_name'],
                      'title'       => $params['title'],
                      'images'      => array(
                        array (
                          'image'         => $image,
                          'images_thumbs' => array(
                            array(
                              'thumb' => $this->gallery_model->thumb($image,180,135),
                              'width' => 180,
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
        $this->catalog_model->delete_catalog($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }

    }

      unset($image);

      if ($_FILES['images']['name']) {
          foreach ($_FILES['images']['name'] as $key=>$im) {
              $file['name'] = $_FILES['images']['name'][$key];
              $file['type'] = $_FILES['images']['type'][$key];
              $file['tmp_name'] = $_FILES['images']['tmp_name'][$key];
              $file['error'] = $_FILES['images']['error'][$key];
              $file['size'] = $_FILES['images']['size'][$key];
              $image = upload_file($file);
              if (!$image) {
                  send_answer(array('errors' => array('Ошибка при загрузке изображения')));
              }
              if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
                  @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                  send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
              }
              if (!resize_image($image, 180, 135)) {
                  @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                  send_answer(array('errors' => array('Не удалось создать миниатюру')));
              }
              if (@$image) {
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
                                          'system_name' => $category['system_name'],
                                          'title'       => $category['title'],
                                          'childrens'   => array(
                                              array(
                                                  'system_name' => $params['system_name'],
                                                  'title'       => $params['title'],
                                                  'images'      => array(
                                                      array (
                                                          'image'         => $image,
                                                          'images_thumbs' => array(
                                                              array(
                                                                  'thumb' => $this->gallery_model->thumb($image,180,135),
                                                                  'width' => 180,
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
                      $this->catalog_model->delete_catalog($id);
                      send_answer(array('errors' => array('Не удалось сохранить изображения')));
                  }
              }

              unset($image);
          }
      }
    
    send_answer();
	}

	/**
	 *  Удаление вложений
	 * @param $id - id вложения
	 */			
  function delete_catalog($parent_id, $id) {
    $this->catalog_model->delete_catalog((int)$id);
		send_answer();
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/');
  }	

	/**
	 *  Включение вложений
	 * @param $id - id вложения
	 */	  
  function enable_catalog($parent_id, $id) {
    $this->catalog_model->edit_catalog((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/');
  }

	/**
	 *  Выключение вложений
	 * @param $id - id вложения
	 */	    
  function disable_catalog($parent_id, $id) {
    $this->catalog_model->edit_catalog((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/');
  }		

}