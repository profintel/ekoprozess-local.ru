<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Переадресация
* @param $url - адрес назначения
* @param $permanently - постоянная или временная переадресация
*/
function location($url, $permanently = TRUE) {
  header('HTTP/1.1 '. ($permanently ? '301 Moved Permanently' : '302 Moved Temporarily'));
  header('Location: '. $url);
  exit();
}

function get_main_host($per_dot = FALSE) {
	if (preg_match('/^[1-2]?[0-5]?[0-5]\.[1-2]?[0-5]?[0-5]\.[1-2]?[0-5­]?[0-5]\.[1-2]?[0-5]?[0-5]$/', $_SERVER['HTTP_HOST'])) {
		$result = $_SERVER['HTTP_HOST'];
		$per_dot = FALSE;
	} elseif (strpos($_SERVER['HTTP_HOST'], '.') !== FALSE) {
		$parts = explode('.', $_SERVER['HTTP_HOST']);
		$result = array_pop($parts);
		$result = array_pop($parts) .'.'. $result;
	} else {
		$result = $_SERVER['HTTP_HOST'];
	}
  return ($per_dot ? '.' : '') . $result;
}

function send_answer($data = array()) {
  echo json_encode(array_merge(array(
    'errors' => array(),
    'messages' => array(),
    'url' => ''
  ), $data));
  exit();
}

function array_simple($array, $value = 'id', $key = FALSE) {
  $result = array();
  foreach ($array as $num => $row) {
    $result[($key ? $row[$key] : $num)] = ($value ? $row[$value] : $row);
  }
  return $result;
}

/**
* Возвращает отформатированную русскую дату
* @param $sql_date - дата в sql-формате
* @param $mask - формат
* @return string
* @author Fenriz
*/
function rus_date($sql_date = FALSE, $mask = 'd m Y') {
  $days = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
  $months = array(1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');
  $months2 = array(1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель', 5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь');
  $tm = ($sql_date ? strtotime($sql_date) : time());
  $day = $days[(int)date('w', $tm)];
  $month = $months[(int)date('m', $tm)];
  $month2 = $months2[(int)date('m', $tm)];
  return date(str_replace(array('w', 'm', 'F'), array($day, $month, $month2), $mask), $tm);
}

function get_ext($name) {
  return strtolower(array_pop(explode('.', $name)));
}

function upload_file($file, $rename = TRUE, $destination = '') {
  if ($destination) {
    $files_dir = $destination;
  } else {
    $files_dir = FCPATH .'uploads';
    $dirs = explode('/', date('Y/m/d'));
    foreach ($dirs as $dir) {
      $files_dir .= '/'. $dir;
      if (!file_exists($files_dir)) {
        mkdir($files_dir);
        chmod($files_dir, 0777);
      }
    }
  }
  
  if ($rename) {
    $file_path = $files_dir .'/'. str_replace('.', '', microtime(TRUE)) . rand(100, 999) .'.'. get_ext($file['name']);
  } else {
    $file_path = $files_dir .'/'. $file['name'];
  }
  
  if (@move_uploaded_file($file['tmp_name'], $file_path)) {
    chmod($file_path, 0777);
    return str_replace(FCPATH, '/', $file_path);
  }
  
  return FALSE;
}

function multiple_upload_file($files, $rename = TRUE, $destination = '') {
  $result['errors'] = array();
  if ($destination) {
    $files_dir = FCPATH . $destination;
  } else {
    $files_dir = FCPATH .'uploads';
    $dirs = explode('/', date('Y/m/d'));
    foreach ($dirs as $dir) {
      $files_dir .= '/'. $dir;
      if (!file_exists($files_dir)) {
        mkdir($files_dir);
        chmod($files_dir, 0777);
      }
    }
  }
  
	if (!$files['name'][0]) {
    $result['errors'][] = 'Не загружены изображения.';
  }
  
	$result['files_path'] = array();
	foreach ($files['name'] as $num=> $file_name) {
		if ($file_name) {
      if ($rename) {
        $file_path = $files_dir .'/'. str_replace('.', '', microtime(TRUE)) . rand(100, 999) .'.'. get_ext($file_name);
      } else {
        $file_path = $files_dir .'/'. $file_name;
      }
          
      if (@move_uploaded_file($files['tmp_name'][$num], $file_path)) {
        chmod($file_path, 0777);
        $result['files_path'][] = str_replace(FCPATH, '/', $file_path);
      } else {
        $result['errors'][] = 'Ошибка при загрузке изображения "'.$file_name.'"';
      }
		}
	}
  
	return $result;
}

function unlinks($files = array()) {
  if (!is_array($files)) {
    $files = array($files);
  }
  
  foreach ($files as $file) {
    @unlink(FCPATH . ltrim($file, '/'));
  }
}

function transliterate($str) {
  $from = array("А", "Б", "В", "Г", "Д", "Е", "Ё",  "Ж",  "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц",  "Ч",  "Ш",  "Щ",   "Ь", "Ы", "Ъ", "Э", "Ю",  "Я",  "а", "б", "в", "г", "д", "е", "ё",  "ж",  "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц",  "ч",  "ш",  "щ",   "ь", "ы", "ъ", "э", "ю",  "я",  " ");
  $to  =  array("A", "B", "V", "G", "D", "E", "Yo", "Zh", "Z", "I", "Y", "K", "L", "M", "N", "O", "P", "R", "S", "T", "U", "F", "H", "Ts", "Ch", "Sh", "Sch", "",  "Y", "",  "E", "Yu", "Ya", "a", "b", "v", "g", "d", "e", "yo", "zh", "z", "i", "y", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "ts", "ch", "sh", "sch", "",  "y", "",  "e", "yu", "ya", "_");
  for ($i = 0; $i < count($from); $i++) {
    $str = str_replace($from[$i], $to[$i], $str);
  }
  $str = preg_replace("/[^A-Za-z0-9_—–\-\.\+\,\=\[\]\(\)\&\@\#\!\{\}]+/", "", $str);
  return $str;
}

function resize_image($image, $width, $height) {
  if (!$image || (!$width && !$height)) { return false; }
  $image = $_SERVER["DOCUMENT_ROOT"] . $image;
  if (!file_exists($image)) { return false; }
  $image_info = getimagesize($image);
  if (!$image_info[1] && !$image_info[0]) { return false; }
  
  switch ($image_info[2]) {
    case 1: $source = imagecreatefromgif($image); break;
    case 2: $source = imagecreatefromjpeg($image); break;
    case 3: $source = imagecreatefrompng($image); break;
  }
  
  $parts = explode('.', $image);
  $ext = strtolower(array_pop($parts));
  $name = implode('.', $parts);
  $destination = $name .'_'. $width .'_'. $height .'.'. $ext;
  
  if ($width && $height) {
    if ($image_info[0] >= $image_info[1]) {
      $resource_width = $width;
      $resource_height = floor($width * $image_info[1] / $image_info[0]);
    } else {
      $resource_height = $height;
      $resource_width = floor($height * $image_info[0] / $image_info[1]);
    }
  } else {
    if ($width) {
      $resource_width = $width;
      $resource_height = floor($width * $image_info[1] / $image_info[0]);
    } else {
      $resource_height = $height;
      $resource_width = floor($height * $image_info[0] / $image_info[1]);
    }
  }
  
  $resource = imagecreatetruecolor($resource_width, $resource_height);
	/* Check if this image is PNG or GIF, then set if Transparent*/  
	if(($image_info[2] == 1) OR ($image_info[2] == 3)){
		imagealphablending($resource, false);
		imagesavealpha($resource,true);
		$transparent = imagecolorallocatealpha($resource, 255, 255, 255, 127);
		imagefilledrectangle($resource, 0, 0, $resource_width, $resource_height, $transparent);
	}		
  imagecopyresampled($resource, $source, 0, 0, 0, 0, $resource_width, $resource_height, $image_info[0], $image_info[1]);
  
  switch ($image_info[2]) {
    case 1: imagegif($resource, $destination); break;
    case 2: imagejpeg($resource, $destination); break;
    case 3: imagepng($resource, $destination); break;
  }
  
  if (!file_exists($destination)) { return false; }
  
  return true;
}

function thumb($image, $width, $height) {
  if ($image && ($width || $height)) {
    $parts = explode('.', $image);
    $ext = strtolower(array_pop($parts));
    $name = implode('.', $parts);
    return $name .'_'. $width .'_'. $height .'.'. $ext;
  } else {
    return $image;
  }
} 

function add_logo_to_image($path_result_img, $path_dest_img,$path_logo,$dst_x = 0,$dst_y = 0,$src_x = 0,$src_y = 0,$dst_w = 0,$dst_h = 0,$src_w = 0,$src_h = 0) {
  $logo = imagecreatefrompng('.'.($path_logo ? $path_logo : '/uploads/logo.png'));
  $size_dest_img = getimagesize($_SERVER['DOCUMENT_ROOT'] . $path_dest_img);
  $size_logo = getimagesize($_SERVER['DOCUMENT_ROOT'] . $path_logo);
  $src_w = ($src_w ? $src_w : $size_logo[0]);
  $src_h = ($src_h ? $src_h : $size_logo[1]);

  $ext = get_ext($path_dest_img);
  switch($ext) {
    case 'jpg':
    case 'jpeg':
      $dest_img = imagecreatefromjpeg('.'.$path_dest_img);
      if(imagecopyresampled($dest_img,$logo,$dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h)) {
        imagejpeg($dest_img,'.'.$path_result_img,100);
        imagedestroy($dest_img);
        imagedestroy($logo);
        return true;
      }
    break;
    case 'png':
      $dest_img = imagecreatefrompng('.'.$path_dest_img);
      $resource = imagecreatetruecolor($size_dest_img[0], $size_dest_img[1]);
      if(imagecopyresampled($resource, $dest_img, 0, 0, 0, 0, $size_dest_img[0], $size_dest_img[1], $size_dest_img[0], $size_dest_img[1])) {        
        if(imagecopyresampled($resource,$logo,$dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h)) {
          imagepng($resource,'.'.$path_result_img);
          imagedestroy($logo);
        }
        imagedestroy($resource);
        imagedestroy($dest_img);
        return true;
      }                
    break;
    case 'gif':
      $dest_img = imagecreatefromgif('.'.$path_dest_img);
      $resource = imagecreatetruecolor($size_dest_img[0], $size_dest_img[1]);                
      if(imagecopyresampled($resource, $dest_img, 0, 0, 0, 0, $size_dest_img[0], $size_dest_img[1], $size_dest_img[0], $size_dest_img[1])) {               
        if(imagecopyresampled($resource,$logo,$dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h)) {
          imagegif($resource,'.'.$path_result_img);
          imagedestroy($logo);
        }
        imagedestroy($resource);
        imagedestroy($dest_img); 
        return true;
      } 
    break;
  }  
  return false;
}

function send_mail($from, $email, $subject, $message) {
  $from = $from . ' <'. $from .'>';
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
  $headers .= 'From: '. $from . "\r\n";
  @mail($email, code_mail_subject(pre_text($subject)), pre_text($message), $headers);
}

function code_mail_subject($subject) {
  return '=?UTF-8?B?'. base64_encode($subject) .'?=';
}

function get_player($id, $autoplay = true) {
  $player = "<script>
    $(document).ready(function() {
      flowplayer('". $id ."', '/adm/flowplayer/flowplayer-3.2.5.swf', {
        plugins:  {
          controls: {
            backgroundGradient: 'none',
            backgroundColor: '#3f3f3f',
            bufferColor: '#999999',
            progressColor: '#9eb535',
            buttonColor: '#9eb535',
            buttonOverColor: '#bed846',
            timeColor: '#bed846',
            autoHide: 'always',
            tooltips: {
              buttons: true,
              play: 'Старт',
              pause: 'Пауза',
              mute: 'Выключить звук',
              unmute: 'Включить звук',
              fullscreenExit: 'Выход из полноэкранного режима',
              fullscreen: 'На полный экран'
            },
            tooltipColor: '#3f3f3f',
            tooltipTextColor: '#ffffff'
          }
        },
        clip: {
          autoPlay: ". ($autoplay ? 'true' : 'false') .",
          autoBuffering: true,
          scaling: 'fit'
        }
      });
    });</script>";
  return $player;
}

function get_music_player($id, $time = false) {
  return '<script>
    $(document).ready(function() {
      flowplayer("'. $id .'", "/flowplayer/flowplayer-3.2.5.swf", {
        plugins:  {
          controls: {
            backgroundGradient: "none",
            backgroundColor: "#3f3f3f",
            bufferColor: "#999999",
            progressColor: "#9eb535",
            buttonColor: "#9eb535",
            buttonOverColor: "#bed846",
            timeColor: "#bed846",
            autoHide: "never",
            all: false,
            play: true,
            scrubber: true,
            volume: true,
            mute: true,
            time: '. ($time ? 'true' : 'false') .',
            tooltips: {
              buttons: true,
              play: "Играть",
              pause: "Приостановить",
              stop: "Стоп",
              mute: "Выключить звук",
              unmute: "Включить звук"
            },
            tooltipColor: "#3f3f3f",
            tooltipTextColor: "#ffffff"
          }
        },
        clip: {
          autoPlay: false
        }
      });
    });</script>';
}

function parse_link($link, $var = false) {
  $result = array();
  $url = parse_url($link);
  $params = explode('&', $url['query']);
  foreach ($params as $param) {
    list($name, $value) = explode('=', $param);
    $result[$name] = $value;
  }
  if ($var) {
    return (isset($result[$var]) ? $result[$var] : false);
  } else {
    return $result;
  }
}

function pre_price($price, $decimals = 2, $separator = '', $prefix = '', $postfix = '') {
  return pre_string($prefix) . number_format($price, (int)$decimals, '.', pre_string($separator)) . pre_string($postfix);
}

function autocomplete_input($name, $controller, $method, $function) {
  return '
    <div class="search">
      <input type="text" id="'. $name .'_search_string" class="autocomplete_search_string def" value="Поиск" data-name="'. $name .'" data-controller="'. $controller .'" data-method="'. $method .'" />
    </div>
    <script>
      $(document).ready(function() {
        $("#'. $name .'_search_string").autocomplete({
          delay: 500,
          source: function(request, response) { return autocomplete_search(request, response, $("#'. $name .'_search_string")) },
          select: function(event, ui) { if (ui.item) { product_link_add(ui.item, "'. $name .'"); return false; } },
          focus: function() { return false; }
        });
      });
    </script>
  ';
}

function make_link($text, $link) {
  return '<a href="'. (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $link .'">'. $text .'</a>';
}

function make_week($days) {
  $result = array();
  $curr_day = date('w');
  foreach ($days as $num => $day) {
    $result[mktime(0, 0, 0, date('m'), date('d') - $curr_day + $num + 1, date('Y'))] = $day;
  }
  return $result;
}

function rnd() {
  return ++$_SESSION['time'];
}

function hex2rgb($hex) {
  $hex = str_replace('#', '', $hex);

  if(strlen($hex) == 3) {
    $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
    $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
    $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
  } else {
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
  }
  
  return array('r' => $r, 'g' => $g, 'b' => $b);
}

/**@about Постраничный вывод
* @param $current - номер текущей страницы
* @param $all - общее кол-во
* @param $in_page - к-во на странице
*/
function get_pages($current, $all, $in_page) {
	$result = array();
	$pages = ceil($all / $in_page);
	if ($pages > 1) {
		if ($pages <= 10) {
			for ($i = 1; $i <= $pages; $i++) {
				$result[$i] = ($i == $current ? 0: 1);
			}
		} else {
			if ($current <= 4) {
				for ($i = 1; $i <= 4; $i++) {
					$result[$i] = ($i == $current ? 0: 1);
				}
				if ($current == 4) {
					$result[5] = 1;
					$result[6] = 2;
				} else {
					$result[5] = 2;
				}
				$result[$pages] = 1;
			} elseif ($current >= $pages - 2) {
				$result[1] = 1;
				if ($current == $pages - 2) {
					$result[$pages - 4] = 2;
					$result[$pages - 3] = 1;
				} else {
					$result[$pages - 3] = 2;
				}
				for ($i = $pages - 2; $i <= $pages; $i++) {
					$result[$i] = ($i == $current ? 0: 1);
				}
			} else {
				$result[1] = 1;
				$result[$current - 2] = 2;
				for ($i = $current - 1; $i <= $current + 1; $i++) {
					$result[$i] = ($i == $current ? 0: 1);
				}
				$result[$current + 2] = 2;
				$result[$pages] = 1;
			}
		}
	}
	return $result;
}

function search_input($name, $component, $method) {
  return '
  <div class="input_block">
    <div class="input" id="search">
      <input type="text" class="dark" id="search_string" value="Поиск" data-name ="'. $name .'" data-component = "'. $component .'" data-method = "'. $method .'" />
      <input type="hidden" name="'. $name .'" value="" id="item_id">
    </div>
    <div class="clear"></div>
    <div id="search_items">
      <div id="search_info">
        <div id="search_info_all" class="float_l">Найдено: <span></span></div>
        <div id="search_info_view" class="float_l">Показано: <span></span></div>
        <a href="#" onClick="return search_hide();" id="search_info_hide" class="float_l">Вернуться</a>
        <br class="clear" />
      </div>
      <br class="clear" />
      <div id="search_items_result"></div>
    </div>
  </div>
  ';
}

/**
* Экранирование строки перед добавлением в базу
**/
function mysql_prepare($str) {
  $CI =& get_instance();
  $CI->load->database();
  return str_replace("'", '', $CI->db->escape($str));
}