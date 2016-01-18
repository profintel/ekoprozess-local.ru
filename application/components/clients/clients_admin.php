<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clients_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('clients/models/clients_model');
    $this->load->model('cities/models/cities_model');
    $this->load->model('administrators/models/administrators_model');
    $this->load->model('calendar/models/calendar_model');
    $this->load->model('products/models/products_model');
    $this->load->model('acceptances/models/acceptances_model');
  }
  
  /**
  * Просмотр меню компонента
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'База клиентов',
      'items' => array(
        array(
          'title' => 'Список клиентов',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'clients_report/'
        ),
        array(
          'title' => 'Импорт списка клиентов',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'import/'
        ),
      )
    ));
  }

  /**
  * Просмотр списка клиентов всех менеджеров
  */
  function clients_report_allClients(){}

  /**
  * Просмотр списка своих клиентов
  */
  function clients_report() {
    $where = '';
    $error = '';
    $get_params = array(
      'title'     => ($this->uri->getParam('title') ? mysql_prepare($this->uri->getParam('title')) : ''),
      'country_id' => ($this->uri->getParam('country_id') ? mysql_prepare($this->uri->getParam('country_id')) : 3159),
      'region_federal_id' => ($this->uri->getParam('region_federal_id') ? mysql_prepare($this->uri->getParam('region_federal_id')) : ''),
      'region_id' => ($this->uri->getParam('region_id') ? mysql_prepare($this->uri->getParam('region_id')) : ''),
      'city_id'   => ($this->uri->getParam('city_id') ? mysql_prepare($this->uri->getParam('city_id')) : ''),
      'admin_id'  => ($this->uri->getParam('admin_id') ? mysql_prepare($this->uri->getParam('admin_id')) : ''),
      'client_id'  => ($this->uri->getParam('client_id') ? mysql_prepare($this->uri->getParam('client_id')) : ''),
    );
    //если указан не текущий менеджер,
    //то проверяем на доступ к просмотру клиентов других менеджеров
    if($get_params['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'clients_report_allClients')){
      if(!$get_params['admin_id']){
        $get_params['admin_id'] = $this->admin_id;
      } else {
        $error = 'У вас нет прав на просмотр клиентов других менеджеров';
      }
    }
    if($get_params['client_id']){
      $where .= ($where ? ' AND ' : '').'pr_clients.id = '.$get_params['client_id'];
    } else {
      if($get_params['title']){
        $where .= ($where ? ' AND ' : '').'pr_clients.title LIKE "'.$get_params['title'].'%"';
      }
      if($get_params['country_id'] && !$get_params['region_federal_id'] && !$get_params['region_id'] && !$get_params['city_id']){
        //выборка городов по федеральному округу
        $cities = $this->cities_model->get_cities(0,0,false,false,false,$get_params['country_id']);
        if($cities){
          $cities = array_simple($cities,'id');
        }
        $where .= ($where ? ' AND ' : '').'pr_clients.city_id IN ('.($cities ? implode(',', $cities) : 0).')';
      }
      if($get_params['region_federal_id'] && !$get_params['region_id'] && !$get_params['city_id']){
        //выборка городов по федеральному округу
        $cities = $this->cities_model->get_cities(0,0,false,false,$get_params['region_federal_id']);
        if($cities){
          $cities = array_simple($cities,'id');
        }
        $where .= ($where ? ' AND ' : '').'pr_clients.city_id IN ('.($cities ? implode(',', $cities) : 0).')';
      }
      if($get_params['region_id'] && !$get_params['city_id']){
        //выборка городов по региону
        $cities = $this->cities_model->get_cities(0,0,array('region_id' => $get_params['region_id']));
        if($cities){
          $cities = array_simple($cities,'id');
        }
        $where .= ($where ? ' AND ' : '').'pr_clients.city_id IN ('.($cities ? implode(',', $cities) : 0).')';
      }
      if($get_params['city_id']){
        $where .= ($where ? ' AND ' : '').'pr_clients.city_id = '.$get_params['city_id'];
      }
      if($get_params['admin_id']){
        $where .= ($where ? ' AND ' : '').'pr_clients.admin_id = '.$get_params['admin_id'];
      }
    }
    $page = ($this->uri->getParam('page') ? $this->uri->getParam('page') : 1);
    $limit = 50;
    $offset = $limit * ($page - 1);
    $cnt = $this->clients_model->get_clients_cnt($where );
    $pages = get_pages($page, $cnt, $limit);
    $postfix = '&';
    foreach ($get_params as $key => $value) {
      $postfix .= $key.'='.$value.'&';
    }
    $pagination_data = array(
      'ajax'    => true,
      'pages'   => $pages,
      'page'    => $page,
      'prefix'  => '/admin'.$this->params['path'].'clients_report/',
      'postfix' => $postfix
    );
    $items = $this->clients_model->get_clients_report($limit, $offset, $where, array('city.title_full'=>'asc'));
    $data = array(
      'title'           => 'Клиенты',
      'client_params'   => $this->clients_model->get_client_params(0,0,array('active' => 1)),
      'error'           => $error,
      'items'           => $items,
      'pagination'      => $this->load->view('templates/pagination', $pagination_data, true),
      'quick_form' => $this->view->render_form(array(
        'method'  => 'GET',
        'action'  => $this->lang_prefix .'/admin'. $this->params['path'] .'clients_report/',
        'view'    => 'forms/form_inline',
        'blocks' => array(
          array(
            'title'    => '',
            'fields'   => array(
              array(
                'view'      => 'fields/autocomplete_input',
                'class'     => 'col-xs-11 quick_form_input',
                'title'     => 'Название:',
                'name'      => 'title',
                'value'     => $get_params['title'],
                'component' => $this->params['name'],
                'method'    => '_client_search',
                'maxlength' => 256
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'col-xs-1 quick_form_btn',
                'icon'     => 'glyphicon-search',
                'title'    => '',
                'type'     => '',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          )
        )
      )),
      'form' => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'clients_report/',        
        'enctype' => '',
        'blocks' => array(
          array(
            'title'         => 'Расширенный поиск',
            'fields'   => array(
              array(
                'view'    => 'fields/select',
                'title'   => 'Страна:',
                'name'    => 'country_id',
                'value'   => $get_params['country_id'],
                'options' => $this->cities_model->get_countries(),
                'onchange'=> "changeRegion(this, 'country'); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
                'empty'   => false
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Федеральный округ:',
                'id'      => 'region_federal_id',
                'name'    => 'region_federal_id',
                'value'   => $get_params['region_federal_id'],
                'options' => $this->cities_model->get_regions_federal(0,0,array('country_id'=>$get_params['country_id'])),
                'onchange'=> "changeRegion(this, 'federal'); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
                'empty'   => true
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Регион:',
                'name'    => 'region_id',
                'value'   => $get_params['region_id'],
                'options' => $this->cities_model->get_regions(0,0,array('country_id'=>$get_params['country_id']),false,$get_params['region_federal_id']),
                'onchange'=> "changeRegion(this); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
                'empty'   => true
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Город:',
                'name'    => 'city_id',
                'value'   => $get_params['city_id'],
                'options' => $this->cities_model->get_cities(0,0,($get_params['region_id'] ? array('city.region_id'=>$get_params['region_id']) : false),false,$get_params['region_federal_id'],$get_params['country_id']),
                'onchange'=> "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
                'empty'   => true
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Менеджер:',
                'name'        => 'admin_id',
                'value'       => $get_params['admin_id'],
                'text_field'  => 'name_ru',
                'options'     => $this->administrators_model->get_admins(),
                'onchange'    => "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
                'empty'       => true
              ),
              array(
                'view'    => 'fields/hidden',
                'title'   => 'ID:',
                'name'    => 'client_id',
                'value'   => $get_params['client_id'],
                'options' => $this->clients_model->get_clients(),
                'empty'   => true
              ),
              array(
                'view'          => 'fields/submit',
                'title'         => 'Поиск',
                'type'          => 'ajax',
                'failure'       => '?ajax=1',
                'reaction_func' => true,
                'reaction'      => 'handle_ajaxResultHTML',
                'data_type'     => 'html'
              )
            )
          )
        )
      )),
    );
    
    if($this->uri->getParam('ajax') == 1){
      echo $this->load->view('../../application/components/clients/templates/admin_report_table',$data,true);
    } else {
      return $this->render_template('templates/admin_report', array('data'=>$data));
    }

  }

  function _render_clients_report_table($data){
    $data = unserialize(base64_decode($data));
    return $this->load->view('../../application/components/clients/templates/admin_report_table',$data,true);
  }

  /**
  * Формирует html select-ов для отчета
  * return json
  */
  function _renderSelectsReport() {
    $result = array();
    $type = $this->input->post('type');
    $id = ((int)$this->input->post('id') ? (int)$this->input->post('id') : 0);
    if($type == 'country'){
      //выборка федеральных округов по стране
      $vars =array(
        'view'    => 'fields/select',
        'title'   => 'Федеральный округ:',
        'id'      => 'region_federal_id',
        'name'    => 'region_federal_id',
        'options' => $this->cities_model->get_regions_federal(0,0,array('country_id'=>$id)),
        'onchange'=> "changeRegion(this, 'federal'); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
        'empty'   => true
      );
      $result['federal_regions'] = $this->load->view('fields/select', array('vars' => $vars), true); 
      //выборка регионов по стране
      $regions = $this->cities_model->get_regions(0,0,array('country_id'=>$id),false,false);
      $vars = array(
        'view'    => 'fields/select',
        'title'   => 'Регион:',
        'name'    => 'region_id',
        'options' => $regions,
        'onchange'=> "changeRegion(this); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
        'empty'   => true
      );
      $result['regions'] = $this->load->view('fields/select', array('vars' => $vars), true); 
      //выборка городов по федеральному округу
      $cities = $this->cities_model->get_cities(0,0,false,false,false,$id);
    } elseif ($type == 'federal'){
      //выборка регионов по федеральному округу
      $regions = $this->cities_model->get_regions(0,0,false,false,$id);
      $vars = array(
        'view'    => 'fields/select',
        'title'   => 'Регион:',
        'name'    => 'region_id',
        'options' => $regions,
        'onchange'=> "changeRegion(this); submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
        'empty'   => true
      );
      $result['regions'] = $this->load->view('fields/select', array('vars' => $vars), true); 
      //выборка городов по федеральному округу
      $cities = $this->cities_model->get_cities(0,0,false,false,$id);
    } else {
      $cities = $this->cities_model->get_cities(0,0,($id ? array('region_id' => $id) : false));
    }
    $vars = array(
      'view'    => 'fields/select',
      'title'   => 'Город:',
      'name'    => 'city_id',
      'options' => $cities,
      'onchange'=> "submit_form(this, handle_ajaxResultHTML, '?ajax=1', 'html');",
      'empty'   => true
    );
    $result['city'] = $this->load->view('fields/select', array('vars' => $vars), true);
    echo json_encode($result);
  }

  /**
  *  Просмотр списка клиентов
  */
  function _clients_list($page = 1) {
    $where = array();
    $title = ($this->uri->getParam('title') ? mysql_prepare($this->uri->getParam('title')) : '');
    if($title){
      $where['title LIKE'] = $title.'%';
    }
    $limit = 50;
    $offset = $limit * ($page - 1);
    $cnt = $this->clients_model->get_clients_cnt($where );
    $pages = get_pages($page, $cnt, $limit);
    $pagination_data = array(
      'pages'   => $pages,
      'page'    => $page,
      'prefix'  => '/admin'.$this->params['path'].'clients_list/',
      'postfix' => ($title ? '?title='.$title : '')
    );
    $items = $this->clients_model->get_clients($limit, $offset, $where);
    foreach ($items as $key => &$item) {
      $city = $this->cities_model->get_city(array('id'=>$item['city_id']));
      if($city){
        $region_federal = $this->cities_model->get_region_federal_region($city['region_id']);
        $item['title'] = $city['title_full'] .' ('.@$region_federal['title'] .') '. $item['title'];
      }
    }
    unset($item);
    $data = array(
      'title'           => 'Клиенты',
      'search_path'     => '/admin'.$this->params['path'].'clients_list/',
      'search_title'    => $title,
      'component_item'  => array('name' => 'client', 'title' => 'клиента'),
      'items'           => $items,
      'pagination'      => $this->load->view('admin/pagination', $pagination_data, true),
    );

    return $this->render_template('admin/items', $data);
  }

  /**
  *  Поиск клиентов
  */
  function _client_search() {
    $where = array();
    $title = $this->input->post('search_string');
    if($title){
      $where['title LIKE'] = $title.'%';
    }
    $limit = 20;
    $offset = 0;
    $items = $this->clients_model->get_clients($limit, $offset, $where);
    $result = array('items'=>array());
    foreach ($items as $key => $item) {
      $result['items'][] = array(
        'id'        => $item['id'],
        'title'     => $item['title_full'],
        'location'  => '/admin'.$this->params['path'].'edit_client/'.$item['id'].'/',
      );
    }

    echo json_encode($result);
  }
  
  /**
  *  Создание карточки своего клиента
  */  
  function create_client() {
    $languages = $this->languages_model->get_languages(1, 0);
    //Дополнительные параметры
    $client_params = $this->clients_model->get_client_params();
    $fields_params = array();
    foreach ($client_params as $key => $param) {
      $fields_params[] = array(
        'view'      => 'fields/'.($key == 0 ? 'editor' : 'text'),
        'rows'      => 2,
        'title'     => $param['title'],
        'name'      => 'param_'.$param['id'],
        'languages' => $languages,
        'height'    => 60
      );
    }
    $fields_params[] = array(
      'view'     => 'fields/submit',
      'title'    => 'Создать',
      'type'     => 'ajax',
      'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'clients_list/'
    );
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление карточки клиента',
      'html' => $this->view->render_form(array(
        'view'   => 'forms/form_blocks',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_client_process/',
        'blocks' => array(
          array(
            'title'    => 'Основные параметры',
            'col'      => 1,
            'small'    => false,
            'fields'   => array(
              array(
                'view'        => 'fields/autocomplete_input',
                'title'       => 'Название:',
                'name'        => 'title',
                'component'   => $this->params['name'],
                'method'      => '_client_search',
                'placeholder' => ' ',
                'maxlength'   => 256
              ),
              array(
                'view'  => 'fields/text',
                'title' => 'Email:',
                'name'  => 'email',
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Город:',
                'name'    => 'city_id',
                'options' => $this->cities_model->get_cities(),
                'empty'   => true
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Менеджер:',
                'name'        => 'admin_id',
                'value'       => $this->admin_id,
                'text_field'  => 'name_ru',
                'options'     => $this->administrators_model->get_admins(),
                'value'       => $this->admin_id,
                'empty'       => true
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'clients_list/'
              )
            )
          ),
          array(
            'title'    => 'Дополнительные параметры',
            'col'      => 1,
            'small'    => false,
            'fields'   => $fields_params
          ),
          array(
            'title'    => 'Реквизиты',
            'col'      => 2,
            'small'    => false,
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Наименование банка',
                'name'      => 'bank',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'ИНН',
                'name'      => 'inn',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'КПП',
                'name'      => 'kpp',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'ОГРН',
                'name'      => 'ogrn',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Расчетный счёт',
                'name'      => 'bank_account',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Кор. счет',
                'name'      => 'kor_account',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'БИК',
                'name'      => 'bik',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Юр. Адрес',
                'name'      => 'legal_address',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Почтовый адрес',
                'name'      => 'post_address',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'ФИО директора',
                'name'      => 'director_name',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Телефон директора',
                'name'      => 'director_phone',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Email директора',
                'name'      => 'director_email',
                'languages' => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'clients_list/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'clients_report/'
    ), TRUE);
  }
  
  /**
  *  Создание карточки клиента для других менеджеров
  */  
  function checkCreateCardOtherClients(){} //используется для проверки прав
  
  function _create_client_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'email'       => htmlspecialchars(trim($this->input->post('email'))),
      'city_id'     => (int)$this->input->post('city_id'),
      'admin_id'    => ((int)$this->input->post('admin_id') ? (int)$this->input->post('admin_id') : null),
      'active'      => 1,
      'order'       => $this->clients_model->get_client_order()
    );
    $city = $this->cities_model->get_city(array('id' => $params['city_id']));
    if(!$city){
      send_answer(array('errors' => array('Не найден город')));
    }
    //если указан не текущий менеджер,
    //то проверяем на доступ к прикреплению других менеджеров
    if($params['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'checkCreateCardOtherClients')){
      send_answer(array('errors' => array('У вас нет прав на прикрепление клиентам других менеджеров')));
    }
    $params['title_full'] = $params['title'].', '.$city['title_full'];

    $languages = $this->languages_model->get_languages(1, 0);  
    //если указан не текущий менеджер,
    //то проверяем на доступ к прикреплению других менеджеров
    if($params['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'checkCreateCardOtherClients')){
      send_answer(array('errors' => array('У вас нет прав на прикрепление клиентам других менеджеров')));
    }

    $client_params = $this->clients_model->get_client_params();
    //значения по дополнительным параметрам клиента
    $client_multiparams = array();
    foreach ($languages as $language) {
      $client_multiparams[$language['name']] = array();
      foreach ($client_params as $key => $param) {
        $client_multiparams[$language['name']]['param_'.$param['id']] = htmlspecialchars(trim($this->input->post('param_'.$param['id'].'_'. $language['name'])));
      }
    }

    //значения по реквизитам
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']]['bank'] = htmlspecialchars(trim($this->input->post('bank_'. $language['name'])));
      $multiparams[$language['name']]['inn'] = htmlspecialchars(trim($this->input->post('inn_'. $language['name'])));
      $multiparams[$language['name']]['kpp'] = htmlspecialchars(trim($this->input->post('kpp_'. $language['name'])));
      $multiparams[$language['name']]['ogrn'] = htmlspecialchars(trim($this->input->post('ogrn_'. $language['name'])));
      $multiparams[$language['name']]['bank_account'] = htmlspecialchars(trim($this->input->post('bank_account_'. $language['name'])));
      $multiparams[$language['name']]['kor_account'] = htmlspecialchars(trim($this->input->post('kor_account_'. $language['name'])));
      $multiparams[$language['name']]['bik'] = htmlspecialchars(trim($this->input->post('bik_'. $language['name'])));
      $multiparams[$language['name']]['legal_address'] = htmlspecialchars(trim($this->input->post('legal_address_'. $language['name'])));
      $multiparams[$language['name']]['post_address'] = htmlspecialchars(trim($this->input->post('post_address_'. $language['name'])));
      $multiparams[$language['name']]['director_name'] = htmlspecialchars(trim($this->input->post('director_name_'. $language['name'])));
      $multiparams[$language['name']]['director_phone'] = htmlspecialchars(trim($this->input->post('director_phone_'. $language['name'])));
      $multiparams[$language['name']]['director_email'] = htmlspecialchars(trim($this->input->post('director_email_'. $language['name'])));
    }

    $errors = $this->_validate_client_title($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    $errors = $this->_validate_client($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->clients_model->create_client($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }
    
    if (!$this->main_model->set_params('client_params', $id, $client_multiparams)) {
      $this->clients_model->delete_client($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (!$this->main_model->set_params('clients', $id, $multiparams)) {
      $this->clients_model->delete_client($id);
      send_answer(array('errors' => array('Не удалось сохранить реквизиты')));
    }

    send_answer(array('redirect' => '/admin'.$this->params['path'].'edit_client/'.$id.'/'));
  }
  
  /**
   *  Редактирование карточки своего клиента
  **/  
  function edit_client($id) {
    $item = $this->clients_model->get_client(array('id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    //если клиент не текущего менеджера,
    //то проверяем на доступ
    if($item['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'checkEditCardOtherClients')){
      show_error('У вас нет прав на просмотр карточки клиента других менеджеров');
    }

    $languages = $this->languages_model->get_languages(1, 0);
    //Дополнительные параметры
    $client_params = $this->clients_model->get_client_params();
    $fields_params = array();
    foreach ($client_params as $key => $param) {
      $fields_params[] = array(
        'view'      => 'fields/'.($key == 0 ? 'editor' : 'text'),
        'rows'      => 2,
        'title'     => $param['title'],
        'name'      => 'param_'.$param['id'],
        'value'     => $item['params'],
        'languages' => $languages,
        'height'    => 60
      );
      //1 параметр - описание с телефонами, добавляем в событие по умолчанию
      if($key == 0){
        $event_desc = $item['params']['param_'.$param['id'].'_'.$this->language];
      }
    }
    $fields_params[] = array(
      'view'     => 'fields/submit',
      'title'    => 'Сохранить',
      'type'     => 'ajax',
      'reaction' => ''
    );
    //параметры для добавления события
    $event_params = json_encode(array(
      'start'       => date("Y-m-d H:i:s", mktime(0,0,0,date("m"),date("d")+1,date("Y"))),
      'client_id'   => $item['id'],
      'title'       => $item['title_full'],
      'description' => @$event_desc,
      'allDay'      => true,
    ));
    $event_btn = array(
      'title'    => 'Добавить событие',
      'icon'     => 'glyphicon-plus',
      'onclick'  => 'createLocalEvent('.$event_params.', "reload")',
    );
    //список событий клиента
    $events = $this->calendar_model->get_events(5,0,array('client_id' => $item['id']),array('start'=>'desc'));
    //поля для формы
    $fields_events = array();
    foreach ($events as $key => $value) {
      $fields_events[] = array(
        'view'      => 'fields/readonly',
        'title'     => '<small>'.date('d.m.Y H:i:s',strtotime($value['start'])).'</small> '.
                       "<small><a href='javascript:void(0)' onClick='editEvent(".json_encode($value).")'>Редактировать</a></small>",
        'value'     => '<small>'.$value['admin']['params']['name_'.$this->language].($value['event'] ? '<br/>'.$value['event'] : '').($value['result'] ? '<br/>('.$value['result'].')' : '').'</small>',
      );
    }
    //параметры для добавления акта приемки
    $acceptance_btn = array(
      'title'    => 'Добавить акт приемки',
      'icon'     => 'glyphicon-plus',
      'onclick'  => 'window.open("/admin/clients/create_acceptance/?client_id='.$item['id'].'","_client_acceptance_create_'.$item['id'].'")',
    );
    //список актов приемки
    $acceptances = $this->acceptances_model->get_acceptances(3, 0, array('client_id'=>$id), array('tm'=>'desc'));
    //поля для формы
    $fields_acceptances = array();
    if($acceptances){
      $fields_acceptances[] = array(
        'view'      => 'fields/readonly_value',
        'title'     => '',
        'value'     => $this->load->view('../../application/components/acceptances/templates/admin_client_acceptances_tbl_sm',array('items' => $acceptances,'client_id'=>$id),TRUE),
      );
    }
    return $this->render_template('admin/inner', array(
      'title' => 'Карточка клиента <small>(ID '.$item['id'].')</small>',
      'block_title_btn' => $this->load->view('fields/submit', 
        array('vars' => array(
          'title'   => 'Удалить клиента',
          'class'   => 'btn-default',
          'icon'    => 'glyphicon-remove',
          'onclick' =>  'return send_confirm("Вы уверены, что хотите удалить клиента - ID'.$item['id'].' '.$item['title'].'?","'.$this->lang_prefix .'/admin'. $this->params['path'] .'delete_client/'.$id.'/", {},"'.$this->lang_prefix .'/admin'. $this->params['path'].'clients_report/" );'
        )), true),
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_client_process/'.$id.'/',
        'view'   => 'forms/form_blocks',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'col'     => 1,
            'small'   => false,
            'fields'  => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'value'     => $item['title'],
                'maxlength' => 256
              ),
              array(
                'view'  => 'fields/text',
                'title' => 'Email:',
                'name'  => 'email',
                'value' => $item['email'],
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Город:',
                'name'    => 'city_id',
                'value'   => $item['city_id'],
                'options' => $this->cities_model->get_cities(),
                'empty'   => true
              ),
              array(
                'view'        => 'fields/select',
                'title'       => 'Менеджер:',
                'name'        => 'admin_id',
                'value'       => $item['admin_id'],
                'text_field'  => 'name_ru',
                'options'     => $this->administrators_model->get_admins(),
                'empty'       => true
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => ''
              )
            )
          ),
          array(
            'title'   => 'Дополнительные параметры',
            'col'     => 1,
            'small'   => true,
            'fields'  => $fields_params
          ),
          array(
            'title'         => 'Акты приемки',
            'col'           => 2,
            'small'         => false,
            'title_btn'     => $this->load->view('fields/submit', array('vars' => $acceptance_btn), true),
            'fields'        => $fields_acceptances,
            'aria-expanded' => true
          ),
          array(
            'title'         => 'События',
            'col'           => 2,
            'small'         => true,
            'title_btn'     => $this->load->view('fields/submit', array('vars' => $event_btn), true),
            'fields'        => $fields_events,
            'aria-expanded' => true
          ),
          array(
            'title'   => 'Реквизиты',
            'col'     => 1,
            'small'   => true,
            'fields'  => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Наименование банка',
                'name'      => 'bank',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'ИНН',
                'name'      => 'inn',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'КПП',
                'name'      => 'kpp',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'ОГРН',
                'name'      => 'ogrn',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Расчетный счёт',
                'name'      => 'bank_account',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Кор. счет',
                'name'      => 'kor_account',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'БИК',
                'name'      => 'bik',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Юр. Адрес',
                'name'      => 'legal_address',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Почтовый адрес',
                'name'      => 'post_address',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'ФИО директора',
                'name'      => 'director_name',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Телефон директора',
                'name'      => 'director_phone',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Email директора',
                'name'      => 'director_email',
                'languages' => $languages,
                'value'     => $item['main_params'],
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => ''
              )
            )
          ),
          array(
            'title'         => 'Документы',
            'col'           => 2,
            'small'         => true,
            'fields'        => array(
              array(
                'view'      => 'fields/file',
                'title'     => '',
                'name'      => 'files[]',
                'value'     => @$item['docs'][0]['gallery_id'],
                'multiple'  => true
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Загрузить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            ),
            'aria-expanded' => true
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'clients_report/'
    ), TRUE);
  }

  /**
  *  Редактирование карточки клиента других менеджеров
  */  
  function checkEditCardOtherClients(){} //используется для проверки прав
  
  function _edit_client_process($id) {    
    $params = array(
      'title'     => htmlspecialchars(trim($this->input->post('title'))),
      'email'     => htmlspecialchars(trim($this->input->post('email'))),
      'city_id'   => (int)$this->input->post('city_id'),
      'admin_id'  => ((int)$this->input->post('admin_id') ? (int)$this->input->post('admin_id') : null),
      'active'    => 1
    );
    $city = $this->cities_model->get_city(array('id' => $params['city_id']));
    if(!$city){
      send_answer(array('errors' => array('Не найден город')));
    }
    //если указан не текущий менеджер,
    //то проверяем на доступ к прикреплению других менеджеров
    if($params['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'checkCreateCardOtherClients')){
      send_answer(array('errors' => array('У вас нет прав на прикрепление клиентам других менеджеров')));
    }    
    $params['title_full'] = $params['title'].', '.$city['title_full'];

    $languages = $this->languages_model->get_languages(1, 0);
    $client_params = $this->clients_model->get_client_params();
    //значения по дополнительным параметрам клиента
    $client_multiparams = array();
    foreach ($languages as $language) {
      $client_multiparams[$language['name']] = array();
      foreach ($client_params as $key => $param) {
        $client_multiparams[$language['name']]['param_'.$param['id']] = htmlspecialchars(trim($this->input->post('param_'.$param['id'].'_'. $language['name'])));
      }
    }

    //значения по реквизитам
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']]['bank'] = htmlspecialchars(trim($this->input->post('bank_'. $language['name'])));
      $multiparams[$language['name']]['inn'] = htmlspecialchars(trim($this->input->post('inn_'. $language['name'])));
      $multiparams[$language['name']]['kpp'] = htmlspecialchars(trim($this->input->post('kpp_'. $language['name'])));
      $multiparams[$language['name']]['ogrn'] = htmlspecialchars(trim($this->input->post('ogrn_'. $language['name'])));
      $multiparams[$language['name']]['bank_account'] = htmlspecialchars(trim($this->input->post('bank_account_'. $language['name'])));
      $multiparams[$language['name']]['kor_account'] = htmlspecialchars(trim($this->input->post('kor_account_'. $language['name'])));
      $multiparams[$language['name']]['bik'] = htmlspecialchars(trim($this->input->post('bik_'. $language['name'])));
      $multiparams[$language['name']]['legal_address'] = htmlspecialchars(trim($this->input->post('legal_address_'. $language['name'])));
      $multiparams[$language['name']]['post_address'] = htmlspecialchars(trim($this->input->post('post_address_'. $language['name'])));
      $multiparams[$language['name']]['director_name'] = htmlspecialchars(trim($this->input->post('director_name_'. $language['name'])));
      $multiparams[$language['name']]['director_phone'] = htmlspecialchars(trim($this->input->post('director_phone_'. $language['name'])));
      $multiparams[$language['name']]['director_email'] = htmlspecialchars(trim($this->input->post('director_email_'. $language['name'])));
    }

    $errors = $this->_validate_client_title($params, $id);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }

    $errors = $this->_validate_client($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if (!$this->clients_model->update_client($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }
    
    if (!$this->main_model->set_params('client_params', $id, $client_multiparams)) {
      $this->clients_model->delete_client($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (!$this->main_model->set_params('clients', $id, $multiparams)) {
      $this->clients_model->delete_client($id);
      send_answer(array('errors' => array('Не удалось сохранить реквизиты')));
    }

    //загружаем файлы
    if ($_FILES['files']['name'][0]) {
      $upload = multiple_upload_file($_FILES['files'],false);
      if (!$upload) {
        send_answer(array('errors' => array('Ошибка при загрузке файлов')));
      }
      $files = array();
      foreach ($upload['files_path'] as $key => $file) {
        $images_thumbs = array();
        if ($this->gallery_model->validate_file($file, array('jpeg', 'jpg', 'gif', 'png'))) {
          resize_image($file, 180, 135);
          resize_image($file, 60, 60);
          $images_thumbs = array(
            array(
              'thumb'   => $this->gallery_model->thumb($file,180,135),
              'width'   => 180,
              'height'  => 135
            ),
            array(
              'thumb'   => $this->gallery_model->thumb($file,60,60),
              'width'   => 60,
              'height'  => 60
            )
          );
        }
        $files[] = array(
          'image'         => $file,
          'images_thumbs' => $images_thumbs
        );
      }
      $item = $this->clients_model->get_client(array('id'=>$id));
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
                  'system_name' => $item['id'],
                  'title'       => $item['title_full'],
                  'childrens'   => array(
                    array(
                      'system_name' => 'docs',
                      'title'       => 'Документы',
                      'images'      => $files
                    ),
                  ),
                )
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        send_answer(array('errors' => array('Не удалось сохранить документы')));
      }
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }
  
  function _validate_client($params) {
    $errors = array();
    if (!$params['title']) { $errors['title'] = 'Не указано название'; }
    if (!$params['city_id']) { $errors['city_id'] = 'Не указан город'; }
    // if ($params['email'] && !preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $params['email'])) { 
    //   $errors['email'] = 'Некорректный Email'; 
    // }
    return $errors;
  }
  
  function _validate_client_title($params, $id = 0) {
    $errors = array();
    $where = array('title' => $params['title'],'city_id' => $params['city_id']);
    if($id){
      $where['id !='] = $id;
    }
    $client = $this->clients_model->get_client($where);
    if ($client) { 
      $errors['title'] = 'В базе уже существует клиент с указанным названием и городом. Менеджер: '.
      ($client['admin'] ? $client['admin']['params']['name_'.$this->language] : 'не указан');
    }
    return $errors;
  }

  /**
  *  Удаление карточки клиента, принадлежащих другим менеджерам
  */  
  function checkDeleteCardOtherClients(){} //используется для проверки прав

  /**
   * Удаление карточки своего клиента
  **/
  function delete_client($id) {
    $item = $this->clients_model->get_client(array('id'=>(int)$id));
    if(!$item){
      show_error('Объект не найден');
    }
    //если клиент не текущего менеджера,
    //то проверяем на доступ
    if($item['admin_id'] != $this->admin_id && !$this->permits_model->check_access($this->admin_id, $this->component['name'], $method = 'checkDeleteCardOtherClients')){
      show_error('У вас нет прав на удаление карточки клиента других менеджеров');
    }
    if (!$this->clients_model->delete_client((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    $this->main_model->delete_params('clients',$id);
    $this->main_model->delete_params('client_params',$id);
    
    send_answer();
  }

  /**
  *  Просмотр списка параметров таблицы клиентов
  */
  function client_params($page = 1) {
    $where = array();
    $title = ($this->uri->getParam('title') ? mysql_prepare($this->uri->getParam('title')) : '');
    if($title){
      $where['title LIKE'] = $title.'%';
    }
    $limit = 50;
    $offset = $limit * ($page - 1);
    $cnt = $this->clients_model->get_client_params_cnt($where );
    $pages = get_pages($page, $cnt, $limit);
    $pagination_data = array(
      'pages'   => $pages,
      'page'    => $page,
      'prefix'  => '/admin'.$this->params['path'].'client_params/',
      'postfix' => ($title ? '?title='.$title : '')
    );
    $data = array(
      'title'           => 'Клиенты',
      'search_path'     => '/admin'.$this->params['path'].'client_params/',
      'search_title'    => $title,
      'component_item'  => array('name' => 'client_param', 'title' => 'параметр'),
      'items'           => $this->clients_model->get_client_params($limit, $offset, $where),
      'pagination'      => $this->load->view('admin/pagination', $pagination_data, true),
      'back' => $this->lang_prefix .'/admin/libs/'
    );

    return $this->render_template('admin/items', $data);
  }
  
  /**
   *  Создание параметра таблицы клиента
  **/  
  function create_client_param() {
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление параметра таблицы клиента',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_client_param_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'maxlength' => 256
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'].'client_params/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'client_params/'
    ), TRUE);
  }
  
  function _create_client_param_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'active'      => 1
    );
    $params['order'] = $this->clients_model->get_client_param_order();

    $errors = $this->_validate_client_param($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->clients_model->create_client_param($params);
    if (!$id) {
      send_answer(array('errors' => array('Ошибка при добавлении объекта')));
    }

    send_answer();
  }
  
  /**
   *  Редактирование параметра таблицы клиента
  **/  
  function edit_client_param($id) {
    $item = $this->clients_model->get_client_param(array('id'=>$id));
    if(!$item){
      show_error('Объект не найден');
    }

    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование параметра таблицы клиента',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_client_param_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Название:',
                'name'      => 'title',
                'value'     => $item['title'],
                'maxlength' => 256
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
      'back' => $this->lang_prefix .'/admin'. $this->params['path'].'client_params/'
    ), TRUE);
  }
  
  function _edit_client_param_process($id) {    
    $params = array(
      'title'     => htmlspecialchars(trim($this->input->post('title'))),
      'active'    => 1
    );

    $errors = $this->_validate_client_param($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if (!$this->clients_model->update_client_param($id, $params)) {
      send_answer(array('errors' => array('Ошибка при сохранении изменений')));
    }

    send_answer(array('success' => array('Изменения успешно сохранены')));
  }
  
  function _validate_client_param($params) {
    $errors = array();
    if (!$params['title']) { $errors['title'] = 'Не указано название'; }
    return $errors;
  }

  /**
   * Удаление параметра таблицы клиента
  **/
  function delete_client_param($id) {
    if (!$this->clients_model->delete_client_param((int)$id)) {
      send_answer(array('errors' => array('Не удалось удалить объект')));
    }
    
    send_answer();
  }

  /**
   * Импорт клиентской базы из Excel
  **/
  function import() {
    return $this->render_template('templates/admin_import', array(
      'title'         => 'Импорт клиентской базы из Excel',
      'client_params' => $this->clients_model->get_client_params(0,0,array('active' => 1)),
      'form' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_import_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'      => 'fields/file',
                'title'     => 'Файл (.csv):',
                'name'      => 'file'
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Загрузить',
                'type'     => 'ajax',
                'reaction' => ''
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path']
    ), TRUE);
  }

  /**
  * Обработка загруженного файла excel
  * 1 ячейка - Название фирмы
  * 2 ячейка - Название региона
  * 3 ячейка - Название города
  * Остальные ячейки - активные параметры клиента в порядке установленном в админке
  */
  function _import_process() {
    if (!$_FILES['file']['name']) {
      send_answer(array('errors' => array('file' => 'Загрузите файл!')));
    }
    $file = upload_file($_FILES['file']);
    if (!$file) {
      send_answer(array('errors' => array('file' => 'Ошибка при загрузке файла!')));
    }
    //загружаем библиотеку PHPExcel для обработки файла
    $this->load->library('PHPExcel');
    // Load $inputFileName to a PHPExcel Object
    $objPHPExcel = PHPExcel_IOFactory::load('.'.$file);

    //данные активного листа файла
    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    foreach ($sheetData as $key => $row) {
      //параметры для таблицы pr_clients
      $params = array(
        'title'       => htmlspecialchars(trim($row['A'])),
        'city_id'     => NULL,
        'admin_id'    => NULL,
        'active'      => 1,
        'order'       => $this->clients_model->get_client_order(),
      );

      if(!$params['title']){
        send_answer(array('errors' => array('file' => 'Ошибка в строке '.$key.': Не указано название клиента')));
      }

      /*Поиск города с учетом региона*/
      // разбиваем строку с регионом на массив через пробелы
      $row['B'] = explode(' ', $row['B']);
      //названием региона является 1 слово в строке
      $row['B'] = $row['B'][0];
      //поиск региона в базе
      $region = $this->cities_model->get_region('title LIKE "%'.str_replace(' ', '%', $row['B']).'%"');
      if(!$region){
        send_answer(array('errors' => array('file' => 'Ошибка в строке '.$key.': Не найден регион в базе')));
      }
      
      // разбиваем строку с городом на массив через пробелы
      $row['C'] = explode(' ', $row['C']);
      //названием города является 1 слово в строке
      $row['C'] = $row['C'][0];
      //поиск города с учетом региона в базе
      $city = $this->cities_model->get_city('region_id = '.$region['id'].' AND title LIKE "%'.str_replace(' ', '%', $row['C']).'%"');

      if(!$city){
        send_answer(array('errors' => array('file' => 'Ошибка в строке '.$key.': Не найден город в базе')));
      }

      $params['city_id'] = $city['id'];
      
      //активные параметры клиента в заданной сортировке
      $client_params = $this->clients_model->get_client_params(0,0,array('active' => 1));
      $multiparams = array();
      //начинаем просмотр строки файла с 4-ой колонки, откуда начинаются параметры
      $columnNumIndex = 3;
      foreach ($client_params as $key => $client_param) {
        //строковый индекс колонки в соответствии с заданным числовым индексом
        $columnStringIndex = PHPExcel_Cell::stringFromColumnIndex($columnNumIndex);
        //сопоставляем параметр с ячейкой в файле с вычисленным индексом
        $multiparams[$this->language]['param_'.$client_param['id']] = htmlspecialchars(trim(@$row[$columnStringIndex]));
        //увеличиваем индекс ячейки
        $columnNumIndex++;
      }
      //поиск клиента в базе по title
      $client = $this->clients_model->get_client(array('title'=>$params['title'], 'city_id'=>$city['id']));
      if ($client){
        $id = $client['id'];
        if (!$this->clients_model->update_client($id, $params)) {
          send_answer(array('errors' => array('Ошибка при сохранении изменений')));
        }
      } else {        
        $id = $this->clients_model->create_client($params);
        if (!$id) {
          send_answer(array('errors' => array('Ошибка при добавлении объекта')));
        }
      }
      
      if (!$this->main_model->set_params('client_params', $id, $multiparams)) {
        $this->clients_model->delete_client($id);
        send_answer(array('errors' => array('Не удалось сохранить параметры')));
      }

    }

    send_answer(array('messages' => array('Файл успешно обработан')));
  }

}