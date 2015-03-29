<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Drive_admin extends CI_Component {
  protected $client, $service;

  function __construct() {
    parent::__construct();
    $admin = $this->admin_model->get_admin($this->admin_id);
    require_once FCPATH .'/google-api-php-client/src/Google_Client.php';
    require_once FCPATH .'/google-api-php-client/src/contrib/Google_DriveService.php';

    $this->client = new Google_Client();
    $this->client->setClientId('73563138901-a7lq5o3afm5oekd200192ifogl8o1hj9.apps.googleusercontent.com');
    $this->client->setClientSecret('nxO4zmvOHOSjzikmCUzKKZq0');
    $this->client->setRedirectUri('http://www.ekoprozess-local.ru/admin/drive/');
    $this->client->setScopes(array('https://www.googleapis.com/auth/drive'));
    $this->client->setAccessType('offline');

    $authCode = $this->uri->getParam('code');
    if ($authCode) {
      $access_token = $this->client->authenticate($authCode);
      $this->admin_model->update_admin($admin['id'],array('access_token' => $access_token));
      header('Location: '. $this->lang_prefix .'/admin/drive/');
    }
    if ($admin['access_token']) {
      $this->client->setAccessToken($admin['access_token']);
      if($this->client->isAccessTokenExpired()) {
        //echo 'Access Token Expired'; // Debug
        $access_token =get_object_vars(json_decode($admin['access_token']));
        $this->client->refreshToken($access_token['refresh_token']);
      }
    } else {
      $this->client->authenticate();
    }
    $this->service = new Google_DriveService($this->client);
  }
  
  /**
  * Вывод списка администраторов
  */
  function index() {
    if($this->client->getAccessToken()) $list = $this->service->files->listFiles(array("q"=>"trashed = false"));
    $data = array(
      'items' => (isset($list['items']) ? $list['items'] : array()),
      'form' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'uploadFile/',
        'blocks' => array(
          array(
            'title' => 'Загрузить файл',
            'fields' => array(
              array(
                'view'         => 'fields/file',
                'title'        => 'Выберите файл:',
                'name'         => 'file'
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Загрузить',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
        )
      )),
      'error' => ($this->client->getAccessToken() ? '' : 'Ошибка сквозной авторизации')
    );
    // print_r($data['items']);
    return $this->render_template('templates/index', $data);
  }
  
  /**
  * Загрузка файла на гугл-диск
  */
  function uploadFile() {
    if(!$_FILES['file']['name']){
      send_answer(array('errors'=>array('file'=>'Выберите файл.')));
    }
    if($_FILES['file']['error']){
      send_answer(array('errors'=>array('При загрузке файла произошла ошибка. Возможно файл превышает максимально допустимый размер.')));
    }
    if(!$_FILES['file']['name']){send_answer(array('errors'=>array('Не загружен файл')));}
    $this->insertFile($_FILES['file']['name'],'',null,$_FILES['file']['type'],$_FILES['file']['tmp_name']);
  }

  /**
   * Insert new file.
   *
   * @param Google_Service_Drive $service Drive API service instance.
   * @param string $title Title of the file to insert, including the extension.
   * @param string $description Description of the file to insert.
   * @param string $parentId Parent folder's ID.
   * @param string $mimeType MIME type of the file to insert.
   * @param string $filename Filename of the file to insert.
   * @return Google_Service_Drive_DriveFile The file that was inserted. NULL is
   *     returned if an API error occurred.
   */
  function insertFile($title, $description, $parentId, $mimeType = '', $filename) {
    $file = new Google_DriveFile();
    $file->setTitle($title);
    $file->setDescription($description);
    $file->setMimeType($mimeType);

    // Set the parent folder.
    if ($parentId != null) {
      $parent = new Google_Service_Drive_ParentReference();
      $parent->setId($parentId);
      $file->setParents(array($parent));
    }

    try {
      $data = file_get_contents($filename);

      $createdFile = $this->service->files->insert($file, array(
        'data' => $data,
        'mimeType' => $mimeType,
      ));

      // Uncomment the following line to print the File ID
      // print 'File ID: %s' % $createdFile->getId();

      send_answer();
    } catch (Exception $e) {
      send_answer(array('errors'=>array("An error occurred: " . $e->getMessage())));
    }
  }

  function delete_file($fileId) {
    if($this->service->files->trash($fileId)){
      send_answer();
    }
  }

  function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // Модификатор 'G' доступен, начиная с PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
  }
    
}