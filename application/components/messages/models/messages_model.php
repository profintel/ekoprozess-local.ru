<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
		
		$this->load->model('components/models/components_model');	
		$this->load->model('accounts/models/accounts_model');	
		$this->load->model('models/main_model');	
  }

  function get_categories() {
    $result = array();
		$items = $this->db->query('SELECT DISTINCT(`component_name`) FROM `pr_messages`')->result_array();
		foreach ($items as $item) {
			$result[] = $this->components_model->get_component($item['component_name']);
		}
		return $result;
  }

  function get_categorie_childs($component_name) {
    $result = array();
		$items = $this->db->query('SELECT DISTINCT(`owner_id`) FROM `pr_messages` WHERE `component_name` = "'.$component_name.'"')->result_array();

		return $items;
  }
	
  function get_messages($component_name, $where = array()) {
		if ($where) {
      $this->db->where($where);
    }
		$this->db->order_by('tm','DESC');
		$items = $this->db->get_where('pr_messages', array('component_name' => $component_name))->result_array();
		foreach ($items as &$item) {
			$item['params'] = array();
      $params = $this->db->get_where('pr_messages_params', array('message_id' => $item['id']))->result_array();
			foreach ($params as $param) {
        $item['params'][$param['name']] = $param['value'];
      }
      if ($item['user_id']) {
        $item['user'] = $this->accounts_model->get_user(array('id' => $item['user_id']));
      }
		}
		unset($item);
		
		return $items;
  }
	
  function get_message($id, $where = array()) {
		if ($where) {
      $this->db->where($where);
    }	
		$item = $this->db->get_where('pr_messages', array('id' => $id))->row_array();
		if ($item) {
			$item['params'] = array();
			$params_blocks = $this->db->get_where('pr_messages_params', array('message_id' => $id, 'parent_id' => NULL))->result_array();
			foreach ($params_blocks as $param) {
				$item['params'][$param['system_name']] = array(
					'title' 					=> $param['title'],
					'message_params' 	=> $this->db->get_where('pr_messages_params', array('message_id' => $id, 'parent_id' => $param['id']))->result_array()
				);
			}
			unset($param);
      if ($item['user_id']) {
        $item['user'] = $this->accounts_model->get_user(array('id' => $item['user_id']));
      }
		}
		return $item;
  }
	
  function set_message_params($id, $params) {
    foreach ($params as $param) {
      if ($this->db->get_where('pr_messages_params', array('message_id' => $id, 'name' => $param['name']))->num_rows()) {
        $query = $this->db->update('pr_messages_params', array('value' => $param['value']), array('message_id' => $id, 'name' => $param['name']));
      } else {
        $param['message_id'] = $id;
        $query = $this->db->insert('pr_messages_params', $param);
      }
      if (!$query) { return false; }
    }
    return true;
  }
  
  function send_to_messages($component_name, $owner_id = 0, $active = false, $parent_id = 0, $title, $user_id, $params) {
    $main_params = array(
      'component_name' => $component_name,
      'title'     => $title,
      'active'    => ($active ? 1 : 0),
      'parent_id' => $parent_id,
      'user_ip'   => $_SERVER['REMOTE_ADDR']
    );
    if ($user_id) {
      $main_params['user_id'] = $user_id;
    }
    if ($owner_id) {
      $main_params['owner_id'] = $owner_id;
    }
    
    $this->db->trans_begin();
    
    $this->db->insert('pr_messages', $main_params);
    $message_id = $this->db->query('SELECT LAST_INSERT_ID() as id')->row()->id;
		foreach ($params as $param) {
			$param['block_params']['message_id'] = $message_id;
      $this->db->insert('pr_messages_params', $param['block_params']);
			$parent_id = $this->db->query('SELECT LAST_INSERT_ID() as id')->row()->id;
			foreach($param['message_params'] as $message_param) {
				$message_param['message_id'] = $message_id;
				$message_param['parent_id'] = $parent_id;
				$this->db->insert('pr_messages_params', $message_param);
			}
    }
    
    if (!$this->db->trans_status()) {
      $this->db->trans_rollback();
      return false;
    }
    $this->db->trans_commit();
    
    return $message_id;
  }
	
  function edit_message($id, $params) {
    if ($this->db->update('pr_messages', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
	function delete_message($id = NULL) {
    $this->db->query('DELETE FROM pr_messages_params WHERE message_id = '. $id);
    $this->db->query('DELETE FROM pr_messages WHERE id = '. $id);
	}
  
	function delete_messages($params) {
    $items =  $this->messages_model->get_messages($params);
    foreach ($items as $item) {
      $this->db->query('DELETE FROM pr_messages_params WHERE message_id = '. $item['id']);
      $this->db->query('DELETE FROM pr_messages WHERE id = '. $item['id']);
    }
	}
  
  function _send_email($message_id,$params,$subject_user,$subject_admin) {
		if ($this->project['project_email']) {
			$this->load->library('email');
			$this->email->from($this->project['project_email']);
			$this->email->to($this->project['project_email']); 
			$this->email->subject($subject_admin.' - "'.$this->project['domain'].'"');
	
			$message =  "<html><body>";
			foreach($params as $param){
				$message .= "<br/>"."<br/>".$param['block_params']['title']."<br/>"."<br/>";
				foreach($param['message_params'] as $message_param){
					if ($message_param['name'] == 'email') {
						$email_user = $message_param['value'];
					}
					if ($message_param['value']) {
						$message .= $message_param['title'].': '.htmlspecialchars($message_param['value'])."<br/>";
					}
				}
			}
			$message .= "<br/>".$this->make_link('Перейти к сообщению>>','/admin/messages/edit_message/'. $message_id .'/');
			$message .= "</body></html>";

			$this->email->message($message);
			
			if (isset($email_user) && $email_user) {
				if ($this->email->send()) {
          $this->email->from($this->project['project_email']);
          $this->email->to($email_user);
          $this->email->subject($subject_user.' - "'.$this->project['domain'].'"');
          
          $message =  "Выражаем благодарность за использование нашего ресурса. Ваши данные находятся на стадии обработки.";
          foreach($params as $param){
            $message .= "<br/>"."<br/>".$param['block_params']['title']."<br/>"."<br/>";
            foreach($param['message_params'] as $message_param){
              if ($message_param['value']) {
                $message .= $message_param['title'].': '.htmlspecialchars_decode($message_param['value'])."<br/>";
              }
            }
          }	
          
          $this->email->message($message);		
          if ($this->email->send()) {	
            return true;
          }
				}  
			} else {
        if ($this->email->send()) {	
          return true;
        }    
      }
    }	
    return false;
  }	
  
  function make_link($text, $link) {
    return '<a href="'. (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $link .'">'. $text .'</a>';
  }  
}