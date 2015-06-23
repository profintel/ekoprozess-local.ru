<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PR_URI extends CI_URI {
  
  var $_get_params = array();
  
  function _fetch_uri_string() {
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '?') !== FALSE) {
      parse_str(array_pop(explode('?', $_SERVER['REQUEST_URI'], 2)), $this->_get_params);
    }
    
    parent::_fetch_uri_string();
  }
  
  function getParams() {
    $params = array();
    foreach ($this->_get_params as $key => $value) {
      if(!is_array($value)){
        $params[$key] = strip_tags($value);
      }
    }
    return $params;
  }
  
  function getParam($key) {
    if (!isset($this->_get_params[$key])) {
      return FALSE;
    }
    $param = $this->_get_params[$key];
    if(!is_array($param)){
      $param = strip_tags($param);
    }
    return $param;
  }

}