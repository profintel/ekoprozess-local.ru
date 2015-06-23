<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Session {

	function Session() {
		$this->object =& get_instance();
    
    if ($this->object->input->is_cli_request()) {
      return;
    }
    
    //$this->object->load->model('config_model');
    //session_name($this->object->config_model->get_param('session_cookie_name'));
		$this->_sess_run();
	}
  
	function _sess_run() {
    $time = time();
    $host = get_main_host(TRUE);
    $ttl  = $this->object->config->item('sess_expiration');
    
    ini_set('session.save_path', BASEPATH .'../sessions');
    ini_set('session.gc_maxlifetime', $ttl);
		session_set_cookie_params($ttl, '/', $host, FALSE);
		session_start();
		setcookie(session_name(), session_id(), $time + $ttl, '/', $host);
    
    $this->set_userdata('time', $time);
	}
  
	function destroy() {
		unset($_SESSION);
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 3600, '/', get_main_host(TRUE));
		}
		session_destroy();
	}
  
	function userdata($item) {
		if($item == 'session_id') {
			return session_id();
		} else {
			return (!isset($_SESSION[$item]) ? FALSE : $_SESSION[$item]);
		}
	}
  
	function set_userdata($newdata = array(), $newval = '') {
		if (is_string($newdata)) {
			$newdata = array($newdata => $newval);
		}
    
		if (is_array($newdata)) {
			foreach ($newdata as $key => $val) {
				$_SESSION[$key] = $val;
			}
		}
	}
  
	function unset_userdata($newdata = array()) {
		if (is_string($newdata)) {
			$newdata = array($newdata);
		}

		if (is_array($newdata)) {
			foreach ($newdata as $key) {
				unset($_SESSION[$key]);
			}
		}
	}
  
}
?>
