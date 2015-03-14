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
  
	<title>Profectum - Авторизация</title>
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
      
      <div id="autorization">
        <div class="inner">
          
          <?=$form;?>
          
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