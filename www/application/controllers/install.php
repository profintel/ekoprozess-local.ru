<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Класс установщика
*/
class Install extends CI_Controller {
  
  function __construct() {
    parent::__construct();
    
    //Проверка существования конфиг-файла
    if (@file_exists(APPPATH .'config/pr_config.php')) {
      $this->load->config('pr_config');
    }
    
    //Если установка уже завершена, редиректим на админку
    if ($this->config->item('pr_installed')) {
      location('/admin/', FALSE);
    }
  }
  
  /**
  * Форма с параметрами установки
  */
	function index() {
    $this->load->view('install', array(
      'html'       => $this->_make_form(),
      'pr_version' => $this->config->item('version')
    ));
	}
  
  function test() {
    explode(',', array());
  }
  
  /**
  * Форма с параметрами установки
  */
  function _make_form() {
    return $this->view->render_form(array(
      'action' => '/install/process/',
      'blocks' => array(
        array(
          'title'  => 'Параметры подключения к базе данных',
          'fields' => array(
            array(
              'view'  => 'fields/text',
              'title' => 'Имя хоста:',
              'name'  => 'hostname',
              'value' => 'localhost',
              'req'   => TRUE
            ),
            array(
              'view'  => 'fields/text',
              'title' => 'Номер порта:',
              'name'  => 'hostport'
            ),
            array(
              'view'  => 'fields/text',
              'title' => 'Имя пользователя:',
              'name'  => 'username',
              'req'   => TRUE
            ),
            array(
              'view'  => 'fields/password',
              'title' => 'Пароль:',
              'name'  => 'password',
              'req'   => TRUE
            ),
            array(
              'view'  => 'fields/text',
              'title' => 'База данных:',
              'name'  => 'database',
              'req'   => TRUE
            ),
            array(
              'view'    => 'fields/select',
              'title'   => 'Тип базы:',
              'name'    => 'dbdriver',
              'options' => array(
                array('id' => 'mysql', 'title' => 'mysql')
              )
            ),
            array(
              'view'    => 'fields/checkbox',
              'title'   => 'Вывод ошибок',
              'name'    => 'db_debug'
            )
          )
        ),
        array(
          'title'  => 'Реквизиты суперпользователя',
          'fields' => array(
            array(
              'view'  => 'fields/text',
              'title' => 'Имя пользователя:',
              'name'  => 'su_username',
              'req'   => TRUE,
              'value' => 'master'
            ),
            array(
              'view'  => 'fields/password',
              'title' => 'Пароль:',
              'name'  => 'su_password',
              'req'   => TRUE,
              'value' => 'fgtkmcbyjdsqujhj['
            ),
            array(
              'view'  => 'fields/password',
              'title' => 'Повтор пароля:',
              'name'  => 're_password',
              'req'   => TRUE,
              'value' => 'fgtkmcbyjdsqujhj['
            ),
            array(
              'view'     => 'fields/submit',
              'class'    => 'icon_small accept_i_s',
              'title'    => 'Начать установку',
              'type'     => 'ajax',
              'reaction' => '/admin/'
            )
          )
        )
      )
    ));
  }
  
  /**
  * Запуск установки
  */
  function process() {
    $params = array(
      'hostname'    => trim($this->input->post('hostname')),
      'hostport'    => trim($this->input->post('hostport')),
      'username'    => trim($this->input->post('username')),
      'password'    => $this->input->post('password'),
      'database'    => trim($this->input->post('database')),
      'dbdriver'    => $this->input->post('dbdriver'),
      'su_username' => trim($this->input->post('su_username')),
      'su_password' => $this->input->post('su_password'),
      're_password' => $this->input->post('re_password'),
      'db_debug'    => TRUE,
      'dbprefix'    => 'pr_'
    );
    $_SESSION['install_params'] = $params;
    
    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $this->_create_db($params);
    
    $params['db_debug'] = ($this->input->post('db_debug') ? 'TRUE' : 'FALSE');
    $this->_create_db_config($params);    
    
    $this->_install_components();
    
    $this->_create_cms_config();
    
    $this->session->destroy();
    
    send_answer();
  }
  
  /**
  * Проверка параметров формы
  */
  function _validate_params($params) {
    $errors = array();
    if (!$params['hostname']) { $errors[] = 'Не указано имя хоста'; }
    if (!$params['username']) { $errors[] = 'Не указано имя пользователя'; }
    if (!$params['password']) { $errors[] = 'Не указан пароль'; }
    if (!$params['database']) { $errors[] = 'Не указана база данных'; }
    if (!$this->form_validation->alpha_dash($params['su_username'])) { $errors[] = 'Недопустимый логин'; }
    if (!$this->form_validation->min_length($params['su_username'], 2)) { $errors[] = 'Слишком короткий логин'; }
    if (!$this->form_validation->max_length($params['su_username'], 100)) { $errors[] = 'Слишком длинный логин'; }
    if (!$this->form_validation->min_length($params['su_password'], 6)) { $errors[] = 'Слишком короткий пароль суперпользователя'; }
    if ($params['su_password'] != $params['re_password']) { $errors[] = 'Пароль не совпадает с повтором'; }
    return $errors;
  }
  
  /**
  * Проверка подключения к БД и создание структуры
  */
  function _create_db($params) {
    //Попытка подключения к БД с указанными параметрами
    $this->load->database($params);
    if (!$this->db->initialized) {
      send_answer(array('errors' => array('Не удалось подключиться к базе данных с указанными параметрами')));
    }
    
    //Чтение файла импорта структуры и данных БД
    $sql = @file_get_contents(APPPATH .'database/'. $this->input->post('dbdriver') .'.sql');
    if (!$sql) {
      send_answer(array('errors' => array('Не удалось прочитать файл /application/database/'. $this->input->post('dbdriver') .'.sql')));
    }
    
    $this->load->model('install_model');
    
    //Создание структуры БД
    if (!$this->main_model->execute_sql($sql)) {
      send_answer(array('errors' => array('Не удалось создать структуру базы данных')));
    }
    
    //Создание учетной записи суперпользователя
    if (!$this->install_model->create_superuser($params['su_username'], md5($params['su_password']))) {
      send_answer(array('errors' => array('Не удалось создать учетную запись суперпользователя')));
    }
  }
  
  /**
  * Запись конфигурационного файла БД в соответствии с шаблоном
  */
  function _create_db_config($params) {
    //Чтение шаблона конфига БД
    $database_config = @file_get_contents(APPPATH .'config/database.tpl');
    if (!$database_config) {
      send_answer(array('errors' => array('Не удалось прочитать файл /application/config/database.tpl')));
    }
    
    //Установка параметров
    foreach ($params as $key => $value) {
      $database_config = str_replace('#'. $key .'#', $value, $database_config);
    }
    
    //Запись шаблона конфига БД
    if (!@file_put_contents(APPPATH .'config/database.php', $database_config)) {
      send_answer(array('errors' => array('Не удалось записать файл /application/config/database.php')));
    }
  }
  
  function _install_components() {
    $this->placement = 'admin';
    $this->load->model('components/models/components_model');
    $this->load->component(array('name' => 'components'));
    
    foreach ($this->config->item('def_components') as $component) {
      $errors = $this->components->install($component, TRUE);
      if ($errors) {
        send_answer(array('errors' => $errors));
      }
      if ($component == "projects") {    
        $this->_create_first_project( $_SESSION['install_params'] );
      }
    }
    
    if (!$this->components->set_main('projects', TRUE)) {
      send_answer(array('errors' => array('Не удалось задать компонент по умолчанию')));
    }
    
    $this->load->component(array('name' => 'templates'));
    if (!$this->templates->install(TRUE)) {
      send_answer(array('errors' => array('Не удалось установить шаблоны')));
    }
  }
  
  /**
  * Создание дефолтного проекта
  */
  function _create_first_project($params) {
    if (!$this->install_model->create_first_project($_SERVER['HTTP_HOST'], 'Profectum')) {
      send_answer(array('errors' => array('Не удалось создать базовый проект')));
    }
  }
  
  /**
  * Запись конфигурационного файла ЦМС в соответствии с шаблоном
  */
  function _create_cms_config() {
    //Чтение шаблона конфига ЦМС
    $pr_config = @file_get_contents(APPPATH .'config/pr_config.tpl');
    if (!$pr_config) {
      send_answer(array('errors' => array('Не удалось прочитать файл /application/config/pr_config.tpl')));
    }
    
    //Установка параметров
    $pr_config = str_replace(array(
      '#pr_installed#',
      '#pr_version#'
    ), array(
      'TRUE',
      $this->config->item('version')
    ), $pr_config);
    
    //Запись шаблона конфига ЦМС
    if (!@file_put_contents(APPPATH .'config/pr_config.php', $pr_config)) {
      send_answer(array('errors' => array('Не удалось записать файл /application/config/pr_config.php')));
    }
  }
  
}