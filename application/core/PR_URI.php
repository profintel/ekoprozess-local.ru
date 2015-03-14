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
    return $this->_get_params;
  }
  
  function getParam($key) {
    if (!isset($this->_get_params[$key])) {
      return FALSE;
    }
    
    return $this->_get_params[$key];
  }

}