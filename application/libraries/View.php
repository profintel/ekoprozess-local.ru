<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Класс для подготовки и рендеринга различных отображений
*/
class View {

  private $CI;
  
  function __construct() {
    $this->CI =& get_instance();
  }
  
  /**
  * Рендеринг полей форм
  * @param $fields - массив с параметрами полей в следующем формате:
  *   $fields = array(
  *     array(
  *       'имя_параметра' => 'значение'[,
  *       ...]
  *     )[,
  *     ...]
  *   );
  * @return string
  */
  function render_fields($fields) {
    $content = '';
    foreach ($fields as $vars) {
      if (!isset($vars['ignore']) || !$vars['ignore']) {
        if (!file_exists(APPPATH .'views/'. $vars['view'] .'.php')) {
          $vars['view'] = 'templates/'. $vars['view'];
        }
        if (!file_exists(APPPATH .'views/'. $vars['view'] .'.php')) {
          $vars['view'] = 'fields/text';
        }
        $content .= $this->CI->load->view($vars['view'], array('vars' => $vars), true);
      }
    }
    return $content;
  }
  
  
  /**
  * Рендеринг формы
  * @param $vars - массив с параметрами формы в следующем формате:
  *   $vars = array(
  *     'view'     => 'шаблон_формы',
  *     'action'   => 'адрес_обработчика',
  *     'method'   => 'метод_передачи',
  *     'enctype'  => 'способ_кодирования',
  *     'onsubmit' => 'обработка_события_отправки',
  *     'blocks'   => array( - массив с наборами полей формы
  *       'class'  => 'класс_контейнера',
  *       'title'  => 'заголовок_набора',
  *       'fields' => 'массив_полей||html_код'
  *     )
  *   );
  * @return string
  */
  function render_form($vars = array()) {
    $vars = array_merge(array(
      'view'     => 'forms/default',
      'action'   => '',
      'method'   => 'POST',
      'target'   => '_self',
      'enctype'  => 'multipart/form-data',
      'onsubmit' => 'return false;',
      'blocks'   => array(),
    ), $vars);
    
    foreach ($vars['blocks'] as &$block) {
      if (is_array($block['fields'])) {
        $block['fields'] = $this->render_fields($block['fields']);
      }
    }
    unset($block);
    
    if (!file_exists(APPPATH .'views/'. $vars['view'] .'.php')) {
      $vars['view'] = 'templates/'. $vars['view'];
    }
    if (!file_exists(APPPATH .'views/'. $vars['view'] .'.php')) {
      $vars['view'] = 'fields/text';
    }
    
    return $this->CI->load->view($vars['view'], array('vars' => $vars), true);
  }
  
}
?>