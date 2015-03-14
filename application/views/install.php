<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex,nofollow" />
  
  <link rel="stylesheet" type="text/css" href="/adm/css/main.css" />
  <link rel="stylesheet" type="text/css" href="/adm/css/icons.css" />
  
  <script type="text/javascript" src="/adm/js/jquery.js"></script>
  <script type="text/javascript" src="/adm/js/jquery.form.js"></script>
  <script type="text/javascript" src="/adm/js/main.js"></script>
  
	<title>Profectum - Установка</title>
</head>
<body>
  
  <div id="wrapper">
    
    <div id="header">
      <div class="logo">
        <a href="/admin/"><img src="/adm/images/logo.gif" border="0" alt="Profectum" title="Profectum" /></a>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div id="body">
      
      <div id="content">
        <div class="inner">
          <h1 class="icon_big switch_i_b">Установка</h1>
          
          <div class="install rounded bg_white">
            
            <?=$html;?>
            
            <div class="clear"></div>
          </div>
          
        </div>
      </div>
      
      <div id="right_panel">
        <div class="inner">
          
          <div class="rounded bg_dark white">
            <h1>Помощь</h1>
            <p>
              Для установки CMS Profectum в соответствующих полях укажите параметры подключения к существующей базе данных.
              В процессе создания структуры БД будут удалены и заново созданы все необходимые таблицы. Таблицы, не относящиеся к
              системе, останутся в прежнем состоянии.
            </p>
            <p>Для получения доступа после установки к административной панели CMS укажите желаемые имя и пароль суперпользователя.</p>
          </div>
          
        </div>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div class="rasporka"></div>
    
  </div>
  
  <div id="footer" class="green">
    <div class="version">Версия <?=$pr_version;?></div>
    
    <div class="clear"></div>
  </div>
  
  <div id="sheet_loading"></div>
  <div id="sheet"></div>
  
  <div id="modal">
    <div class="title"></div>
    <div class="inner"></div>
    <div class="buttons"></div>
  </div>
  
</body>
</html>