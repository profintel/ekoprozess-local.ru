<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Drive_admin extends CI_Component {
  protected $client, $service;

  function __construct() {
    parent::__construct();

    require_once '/google-api-php-client/src/Google_Client.php';
    require_once '/google-api-php-client/src/contrib/Google_DriveService.php';
    
    $this->client = new Google_Client();
    
    if($this->client->isAccessTokenExpired() === false){
      //Get your credentials from the console
      $this->client->setClientId('73563138901-a7lq5o3afm5oekd200192ifogl8o1hj9.apps.googleusercontent.com');
      $this->client->setClientSecret('nxO4zmvOHOSjzikmCUzKKZq0');
      $this->client->setRedirectUri('http://www.ekoprozess-local.ru/admin/drive/');
      $this->client->setScopes(array('https://www.googleapis.com/auth/drive'));

      define('STDIN',fopen("php://stdin","r"));
      $authCode = trim(fgets(STDIN));

      // Exchange authorization code for access token
      $accessToken = $this->client->authenticate($authCode);

      file_put_contents(FCPATH .'adm/token.json', $accessToken);
    }

    $this->client->setAccessToken(file_get_contents(FCPATH .'adm/token.json'));
    $this->labels = new Google_DriveFileLabels();
    $this->labels->setTrashed(0);
    $this->service = new Google_DriveService($this->client);

    $this->load->model('drive/models/drive_model');
  }
  
  /**
  * Вывод списка администраторов
  */
  function index() {
    $list = $this->service->files->listFiles();
    $data = array(
      'items' => $list['items']
    );
    // print_r($data['items']);
    return $this->render_template('templates/index', $data);
  }

  function delete_file($fileId) {
    $this->service->files->trash($fileId);
  }
    
}