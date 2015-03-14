<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Publication_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
				
    $this->load->model('templates/models/templates_model');
		$this->load->model('publication/models/publication_model');
		$this->load->model('gallery/models/gallery_model');
  }

  function index($parent_id = 0, $page = 1) {
    if (!$parent_id) {
      $items = $this->publication_model->get_publication();
    } else {
      $category = $this->publication_model->get_publication_one(array('id' => $parent_id));
      $in_page = 50;
      $all_count = $this->publication_model->get_publication_count($parent_id);
      $pages = get_pages($page, $all_count, $in_page);
      $pagination_data = array(
        'pages' => $pages,
        'page' => $page,
        'prefix' => '/admin/publication/'. $parent_id .'/'
      );
			$items = $this->publication_model->get_publication($parent_id,$in_page, $in_page * ($page - 1));
    }

		return $this->render_template('templates/index', array(
      'parent_id' => $parent_id,
      'items' => $items,
      'pagination' => ($parent_id ? $this->load->view('admin/pagination', $pagination_data, true) : false),
    ));
  }
	
	/**
	 *  Создание категории публикаций
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
      'title' => 'Добавление категории публикаций',
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
								'id' 				  => 'publication-title',
								'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'publication-alias',
                'description' => 'Используется для отображения публикаций данной категории в теле страницы',
								'maxlength'   => 256,
								'req' 			  => true
							),
							array(
								'view' 			=> 'fields/text',
								'title' 		=> 'Название:',
								'name' 			=> 'name',
								'languages' => $languages,
								'maxlength' => 256
							),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $this->templates_model->get_template("publication"),
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
                'description' => 'Укажите страницу, на которой будут отображаться публикации данной категории',
								'name' 			  => 'page',
								'options'  	  => $pages,
								'multiple' 	  => false
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Публикаций на странице:',
                'description' => 'Количество публикаций, отображаемых на одной странице',
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

    if (!$this->publication_model->_validate_publication_system_name($params['system_name'])) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
		
    $id = $this->publication_model->create_publication($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать категорию публикаций')));
    }
    
    $links = array(
      'page_id' => $this->input->post('page')
    );
    if (!$this->publication_model->set_publication_links($id, $links)) {
      send_answer(array('errors' => array('Не удалось сохранить связи')));
    }
    
    if (!$this->main_model->set_params('publication', $id, $multiparams)) {
      $this->publication_model->delete_publication($id);
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
        $this->publication_model->delete_publication($id);
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
	 *  Редактирование категории публикаций
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
		$item =  $this->publication_model->get_publication_one(array('id' => $id));
		$item['image'] = "";
    $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$item['system_name'].'/'),1,0);
    foreach ($item['images'] as $image) {
      $item['image'] = $image['image'];
    }
    
		return $this->render_template('admin/inner', array(
      'title' => 'Редактирование категории публикаций',
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
								'id' 				  => 'publication-title',
								'maxlength'   => 256,
								'value' 		  => $item['title'],
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'publication-alias',
								'maxlength'   => 256,
								'value' 		  => $item['system_name'],
                'description' => 'Используется для отображения публикаций данной категории в теле страницы',
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
                'description' => 'Укажите страницу, на которой будут отображаться публикации данной категории',
								'name' 			  => 'page',
								'options'  	  => $pages,
								'value'			  => (isset($item['page_id']) ? $item['page_id'] : 0),
								'multiple' 	  => false
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Публикаций на странице:',
                'description' => 'Количество публикаций, отображаемых на одной странице',
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
    $item = $this->publication_model->get_publication_one(array('id' => $id));
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
		
    if (!$this->publication_model->_validate_publication_system_name($params['system_name'],$id)) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if (!$this->publication_model->edit_publication($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    $links = array(
      'page_id' => $this->input->post('page')
    );		
    if (!$this->publication_model->set_publication_links($id, $links)) {
      send_answer(array('errors' => array('Не удалось сохранить связи')));
    }
    
    if (!$this->main_model->set_params('publication', $id, $multiparams)) {
      $this->publication_model->delete_publication($id);
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
        $this->publication_model->delete_publication($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }
    
    send_answer();
	}
		
	/**
	 *  Удаление категории публикаций
	 * @param $id - id категории публикации
	 */			
  function delete_category($id) {
    $this->publication_model->delete_category((int)$id);
    send_answer();
  }		
	
	/**
	 *  Добавление публикации
	 * @param $parent_id - id категории публикаций
	 */		
  function create_publication($parent_id) {
		$parent = $this->publication_model->get_publication_one(array('id' => $parent_id));
    $languages = $this->languages_model->get_languages(1, 0);
		return $this->render_template('admin/inner', array(
      'title' => 'Добавление публикации в категории "'.htmlspecialchars($parent['title']).'"',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_publication_process/'. $parent_id .'/',
				'blocks' => array(
					array(
						'title' => 'Основные параметры',
						'fields' => array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'id' 				  => 'publication-title',
								'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'publication-alias',
								'maxlength'   => 256,
                'description' => 'Используется для отображения публикации в теле страницы',
								'req' 			  => true
							),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $this->templates_model->get_template("publication_one"),
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
								'view' 	=> 'fields/file',
								'title' => 'Изображение (gif, png, jpg):',
								'name' 	=> 'image'
							),
							array(
								'view' 	=> 'fields/checkbox',
								'title' => 'Публикация активна',
								'name' 	=> 'active'
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
						'title' => 'Дополнительные параметры',
						'fields' => array(
							array(
								'view' 	=> 'fields/datetime',
								'title' => 'Опубликовать с:',
								'name'	=> 'tm_start'
							),
							array(
								'view' 	=> 'fields/datetime',
								'title' => 'Опубликовать до:',
								'name' 	=> 'tm_end'
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
						'title' => 'Заголовки и SEO',
						'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок публикации:',
                'name'        => 'name',
                'description' => 'Используется при отображении публикации на странице',
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
	
  function _create_publication_process($parent_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'parent_id' => $parent_id,
      'title' => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'template_id' => (int)$this->input->post('template_id'),
      'active' => ($this->input->post('active') ? 1 : 0),
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
		
    if (!$this->publication_model->_validate_publication_system_name($params['system_name'])) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->publication_model->create_publication($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать публикацию')));
    }
    
    if (!$this->main_model->set_params('publication', $id, $multiparams)) {
      $this->publication_model->delete_publication($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (isset($image) && $image) {
      $category = $this->publication_model->get_publication_one(array('id' => $parent_id));
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
        $this->publication_model->delete_publication($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }
    
    send_answer();
	}
	
	/**
	 *  Редактирование публикации
	 * @param $parent_id - id публикации
	 */		
  function edit_publication($id) {
		$item = $this->publication_model->get_publication_one(array('id' => $id));
    $category = $this->publication_model->get_publication_one(array('id' => $item['parent_id']));
		$item['image'] = "";
    $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
    foreach ($item['images'] as $image) {
      $item['image'] = $image['image'];
    }
    unset($image);
    $languages = $this->languages_model->get_languages(1, 0);    
    
		return $this->render_template('admin/inner', array(
      'title' => 'Редактирование публикации "'.htmlspecialchars($item['title']).'"',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_publication_process/'. $id .'/',
				'blocks' => array(
					array(
						'title' => 'Основные параметры',
						'fields' => array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'id' 				  => 'publication-title',
								'maxlength'   => 256,
								'value' 		  => $item['title'],
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Системное имя:',
								'name' 			  => 'system_name',
								'id' 				  => 'publication-alias',
								'maxlength'   => 256,
								'value' 		  => $item['system_name'],
                'description' => 'Используется для отображения публикации в теле страницы',
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
								'title' => 'Изображение (gif, png, jpg):',
								'name' 	=> 'image',
								'value' => $item['image'],
							),
							array(
								'view' 		=> 'fields/checkbox',
								'title' 	=> 'Публикация активна',
								'name'		=> 'active',
								'checked' => $item['active'],
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
								'view' 	=> 'fields/datetime',
								'title' => 'Опубликовать с:',
								'name' 	=> 'tm_start',
								'value' => ($item['tm_start'] ? date('d.m.Y H:i', strtotime($item['tm_start'])) : '')
							),
							array(
								'view' 	=> 'fields/datetime',
								'title' => 'Опубликовать до:',
								'name'	=> 'tm_end',
								'value' => ($item['tm_end'] ? date('d.m.Y H:i', strtotime($item['tm_end'])) : '')
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
						'title' => 'Заголовки и SEO',
						'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Заголовок публикации:',
                'name'        => 'name',
                'description' => 'Используется при отображении публикации на странице',
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
	
  function _edit_publication_process($id) {
    $item = $this->publication_model->get_publication_one(array('id' => $id));
    $category = $this->publication_model->get_publication_one(array('id' => $item['parent_id']));
		$item['image'] = "";
    $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/'.$this->params['name'].'/'.$category['system_name'].'/'.$item['system_name'].'/'),1,0);
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
		
    if (!$this->publication_model->_validate_publication_system_name($params['system_name'],$id)) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
		
    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if (!$this->publication_model->edit_publication($id, $params)) {
      send_answer(array('errors' => array('Не удалось отредактировать публикацию')));
    }
    
    if (!$this->main_model->set_params('publication', $id, $multiparams)) {
      $this->publication_model->delete_publication($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
        
    $gallery = $this->gallery_model->get_gallery_one(array('system_name' => $item['system_name']));
    if ($gallery) {
      $this->gallery_model->edit_gallery($gallery['id'], array('system_name' => $params['system_name'], 'path' => '/gallery_system/'.$this->component['name'].'/'.$category['system_name'].'/'.$params['system_name'].'/'));
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
        $this->publication_model->delete_publication($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }
    
    send_answer();
	}

	/**
	 *  Удаление публикаций
	 * @param $id - id публикации
	 */			
  function delete_publication($parent_id, $id) {
    $this->publication_model->delete_publication((int)$id);
		send_answer();
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/');
  }	

	/**
	 *  Включение публикаций
	 * @param $id - id публикации
	 */	  
  function enable_publication($parent_id, $id) {
    $this->publication_model->edit_publication((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/');
  }

	/**
	 *  Выключение публикаций
	 * @param $id - id публикации
	 */	    
  function disable_publication($parent_id, $id) {
    $this->publication_model->edit_publication((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $parent_id .'/');
  }		

}