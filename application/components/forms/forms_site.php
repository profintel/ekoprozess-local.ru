<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('forms/models/forms_model');
    $this->load->model('messages/models/messages_model');
  }
  
  function render($form) {
    $complete = ($this->uri->getParam('complete') ? $this->uri->getParam('complete') : '');
    switch ($complete) {
      case '1':
        return $this->render_template('templates/complete', array());
      break;
      default:
        $form = $this->forms_model->get_form($form);
        if (!$form) {
          return FALSE;
        }
        
        $type = $this->forms_model->get_forms_type($form['type_id']);
        if (!$type) {
          return FALSE;
        }
        
        $render_fields = array();
        $fields = $this->forms_model->get_forms_fields($form['id']);
        foreach ($fields as $field) {
          if ($field['active']) {
            $render_fields[] = array_merge(
              array(
                'view'     => ($field['template_id'] ? $field['template_path'] : 'fields/'. $field['type']),
                'title'    => (isset($field['params']['title_'. $this->language]) && $field['params']['title_'. $this->language] ? $field['params']['title_'. $this->language] : NULL),
                'id'       => ($field['attr_id'] ? $field['attr_id'] : NULL),
                'name'     => ($field['attr_name'] ? $field['attr_name'] : NULL),
                'class'    => ($field['attr_class'] ? $field['attr_class'] : NULL),
                'placeholder'    => ($field['attr_placeholder'] ? $field['attr_placeholder'] : NULL),
                'tabindex' => ($field['attr_tabindex'] ? $field['attr_tabindex'] : NULL),
                'disabled' => ($field['attr_disabled'] ? (bool)$field['attr_disabled'] : NULL),
                'req'      => (bool)$field['required']
              ),
              $this->_get_field_struct($field, 'vars')
            );
          }
        }
        
        return $this->view->render_form(array(
          'view'     => ($form['template_id'] ? $form['template_path'] : 'forms/default'),
          'action'   => $this->lang_prefix . $type['action'],
          'method'   => $type['method'],
          'target'   => ($type['target'] ? $type['target'] : '_self'),
          'onsubmit' => ($type['onsubmit'] ? $type['onsubmit'] : 'return false;'),
          'enctype'  => $type['enctype'],
          'blocks'   => array(array('fields' => $render_fields))
        ));
      break;
    }
  }
  
  /** Обработка формы, отправленной с сайта
  *   @param $name - системное имя формы
  **/
  function processing_form($name) {
    $form_type = $this->db->get_where('forms', array('name' => $name))->row_array();
    if ($form_type) {
      $form_fields = $this->forms_model->get_forms_fields($form_type['id']);
      $add_params = array();
      
      foreach ($form_fields as $form_field) { 
        if ($form_field['type'] == 'submit' || $form_field['active'] == 0) { continue; }
        if ($form_field['type'] != 'captcha') {
          $form_params[$form_field['attr_name']] = array (
            'name'  => $form_field['attr_name'],
            'title' => $form_field['params']['title_'.$this->language],
            'value' => htmlspecialchars(trim($this->input->post($form_field['attr_name']))),
            'field' => $form_field['type'],
            'req' => $form_field['required']
          );
        } else {
          $add_params['captcha'] = $this->input->post($form_field['attr_name']);
        }
      }
      $params = array(
        array(
          'block_params' 		=> array('title' => 'Параметры сообщения', 'system_name' => 'main_params'),
          'message_params'	=> $form_params
        )
      );
      
      $errors = $this->_validate_params_message($form_fields, array_merge($form_params,$add_params));
      if ($errors) {
        send_answer(array('errors' => $errors));
      }
      
      $message_id = $this->messages_model->send_to_messages("messages", 0, false, 0, 'Новое сообщение', ($this->user_id ? $this->user_id : 0), $params);
      if (!$message_id) {
        send_answer(array('errors' => array(('Не удалось сохранить сообщение'))));
      }
      
      if (!$this->messages_model->_send_email($message_id,$params,'Ваше сообщение успешно доставлено.','Новое сообщение')) {
        send_answer(array('errors' => array(('Не удалось отправить сообщение'))));
      } 
      
      send_answer();
    }
  }  
  
  function _validate_params_message($form_fields, $params) {
    $errors = array();
    foreach ($form_fields as $form_field) {
      if ($form_field['required'] == 1 && $form_field['type'] != 'submit' && $form_field['type'] != 'captcha') {                
        if (!$params[$form_field['attr_name']]['value']) { $errors[] = 'Не указан параметр "'.$form_field['title'].'"'; }                           
      }
    }
    if (isset($params['email']) && $params['email']['value'] && !preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $params['email']['value'])) {
      $errors[] = 'Некорректный Email';
    }
    if (isset($params['captcha']) && !$this->session->userdata('captcha')) { $errors[] = 'Неверное проверочное число'; }
    if (isset($params['captcha']) && $params['captcha'] != $this->session->userdata('captcha')) { $errors[] = 'Неверное проверочное число'; }
    return $errors;
  }
  
  function _get_field_struct($field, $struct_name) {
    $structs = array(
      'text'     => '_get_textField_',
      'password' => '_get_textField_',
      'hidden'   => '_get_textField_',
      'textarea' => '_get_textareaField_',
      'captcha'  => '_get_captchaField_',
      'checkbox' => '_get_checkboxField_',
      'radio'    => '_get_checkboxField_',
      'select'   => '_get_selectField_',
      'submit'   => '_get_submitField_'
    );
    
    if (!isset($structs[$field['type']]) || !method_exists($this, $structs[$field['type']] . $struct_name)) {
      return array();
    }
    
    return $this->{$structs[$field['type']] . $struct_name}($field);
  }
  
  function _get_textField_vars($field) {
    return array(
      'value'     => (isset($field['params']['value_'. $this->language]) ? $field['params']['value_'. $this->language] : NULL),
      'maxlength' => (isset($field['params']['maxlength']) && $field['params']['maxlength'] ? $field['params']['maxlength'] : NULL),
      'autofocus' => (isset($field['params']['autofocus']) ? (bool)$field['params']['autofocus'] : FALSE)
    );
  }
  
  function _get_selectField_vars($field) {
    return array(
      'value'          => (isset($field['params']['value_'. $this->language]) ? $field['params']['value_'. $this->language] : NULL),
      'onchange'       => (isset($field['params']['onchange']) && $field['params']['onchange'] ? $field['params']['onchange'] : NULL),
      'options'        => (isset($field['params']['table']) && $field['params']['table'] ?
        $this->forms_model->get_options($field['params']['table'])
      :
        $this->_make_options(isset($field['params']['strings_'. $this->language]) ? $field['params']['strings_'. $this->language] : array())
      ),
      'value_field'    => (isset($field['params']['table']) && $field['params']['table'] && isset($field['params']['value_field_'. $this->language]) && $field['params']['value_field_'. $this->language] ? $field['params']['value_field_'. $this->language] : NULL),
      'text_field'     => (isset($field['params']['table']) && $field['params']['table'] && isset($field['params']['text_field_'. $this->language]) && $field['params']['text_field_'. $this->language] ? $field['params']['text_field_'. $this->language] : NULL),
      'multiple'       => (isset($field['params']['multiple']) && $field['params']['multiple'] ? $field['params']['multiple'] : NULL),
      'empty'          => (isset($field['params']['empty']) && $field['params']['empty'] ? $field['params']['empty'] : NULL),
      'chosen_disable' => TRUE
    );
  }
  
  function _get_checkboxField_vars($field) {
    return array(
      'value'       => (isset($field['params']['value_'. $this->language]) ? $field['params']['value_'. $this->language] : NULL),
      'options'     => (isset($field['params']['table']) && $field['params']['table'] ?
        $this->forms_model->get_options($field['params']['table'])
      :
        $this->_make_options(isset($field['params']['strings_'. $this->language]) ? $field['params']['strings_'. $this->language] : NULL)
      ),
      'value_field' => (isset($field['params']['table']) && $field['params']['table'] && isset($field['params']['value_field_'. $this->language]) && $field['params']['value_field_'. $this->language] ? $field['params']['value_field_'. $this->language] : NULL),
      'text_field'  => (isset($field['params']['table']) && $field['params']['table'] && isset($field['params']['text_field_'. $this->language]) && $field['params']['text_field_'. $this->language] ? $field['params']['text_field_'. $this->language] : NULL)
    );
  }
  
  function _get_textareaField_vars($field) {
    return array(
      'value'     => (isset($field['params']['value_'. $this->language]) ? $field['params']['value_'. $this->language] : NULL),
      'cols'      => (isset($field['params']['cols']) && $field['params']['cols'] ? $field['params']['cols'] : NULL),
      'rows'      => (isset($field['params']['rows']) && $field['params']['rows'] ? $field['params']['rows'] : NULL),
      'autofocus' => (isset($field['params']['autofocus']) ? (bool)$field['params']['autofocus'] : FALSE)
    );
  }
  
  function _get_captchaField_vars($field) {
    return array(
      'autofocus' => (isset($field['params']['autofocus']) ? (bool)$field['params']['autofocus'] : FALSE),
      'bgcolor'   => (isset($field['params']['bgcolor']) && $field['params']['bgcolor'] ? $field['params']['bgcolor'] : NULL),
      'textcolor' => (isset($field['params']['textcolor']) && $field['params']['textcolor'] ? $field['params']['textcolor'] : NULL),
      'symbols'   => (isset($field['params']['symbols']) && $field['params']['symbols'] ? $field['params']['symbols'] : NULL),
      'width'     => (isset($field['params']['width']) && $field['params']['width'] ? $field['params']['width'] : NULL),
      'height'    => (isset($field['params']['height']) && $field['params']['height'] ? $field['params']['height'] : NULL)
    );
  }
  
  function _get_submitField_vars($field) {
    return array(
      'type'     => (isset($field['params']['type']) ? $field['params']['type'] : 'ajax'),
      'reaction' => (isset($field['params']['success_handler_type']) && $field['params']['success_handler_type'] ?
        ($field['params']['success_handler_type'] == 'reload' ? 'reload' : $field['params']['success_handler_value'])
      :
        NULL
      ),
      'failure'  => (isset($field['params']['failure_handler_type']) && $field['params']['failure_handler_type'] ?
        ($field['params']['failure_handler_type'] == 'alert' ? 'alert' : $field['params']['failure_handler_value'])
      :
        NULL
      )
    );
  }
  
  function _make_options($options) {
    $result = array();
    
    foreach (explode("\n", $options) as $option) {
      $option = trim($option);
      if ($option) {
        $result[] = array(
          'id'    => $option,
          'title' => $option
        );
      }
    }
    
    return ($result ? $result : NULL);
  }
  
  function captcha($bg = '', $txt = '', $n = 0, $w = 0, $h = 0) {
    $bg  = ($bg ? hex2rgb($bg) : hex2rgb('ffffff'));
    $txt = ($txt ? hex2rgb($txt) : hex2rgb('000000'));
    $n = ($n ? $n : 6);
    
    $w = round($w ? $w : $h * 0.56 * $n);
    $h = round($h ? $h : $w / $n * 1.8);
    if (!$w || !$h) {
      $w = $n * 14;
      $h = round($w / $n * 1.8);
    }
    $font_size = round($h * 0.6);
    $lines     = round($h / 3);
    
    $text = '';
    $symbols = '0123456789';
    $sym_a = strlen($symbols);
    for ($i = 0; $i < $n; $i++) {
      $text .= $symbols[rand(1, $sym_a) - 1];
    }
 		$this->session->set_userdata(array('captcha' => $text));
    $image = imagecreate($w, $h);
		$font = FCPATH .'fonts/komika.ttf';
		$background = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
		$textcolor  = imagecolorallocate($image, $txt['r'], $txt['g'], $txt['b']);
		imagettftext($image, $font_size, 0, 3, $h - 3, $textcolor, $font, $text);
		for($i = 0; $i < $lines; $i++){
			imageline($image, rand(0, $w), rand(0, $h), rand(0, $w), rand(0, $h), $background);
		}
		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
  }
  
}