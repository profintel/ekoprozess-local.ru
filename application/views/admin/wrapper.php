<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex,nofollow" />
  
  <link rel="stylesheet" type="text/css" href="/adm/jquery-ui/css/jquery-ui-1.9.2.custom.css" />
  <link rel="stylesheet" type="text/css" href="/adm/lightbox/css/jquery.lightbox-0.5.css" />
  <link rel="stylesheet" type="text/css" href="/adm/css/main.css" />
  <link rel="stylesheet" type="text/css" href="/adm/css/icons.css" />
  <link rel="stylesheet" type="text/css" href="/adm/chosen/chosen.css" />
  <link rel="stylesheet" type="text/css" href="/components/tp_admin.css?<?=time();?>" />
  
  <script type="text/javascript" src="/components/tp_admin.js?<?=time();?>"></script>
  <script type="text/javascript" src="/adm/ckeditor/ckeditor.js"></script>
  
	<title>Profectum - Панель администратора</title>
</head>
<body>
  
  <div id="wrapper">
    
    <div id="header">
      <div class="logo">
        <a href="<?=$_lang_prefix;?>/admin/"><img src="/adm/images/logo.png" border="0" alt="" title="" /></a>
      </div>
      
      <div class="menu">
        <? foreach ($_menu_primary as $item) { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>"<?=($item['name'] == $_component['name'] ? ' class="active"' : '');?>>
            <?=$item['title'];?>
          </a>
        <? } ?>
      </div>
      
      <div class="user">
        <a href="" class="name"><?=$_admin['username'];?></a>
        | <a href="/autorization/close/" class="logout">Выйти</a>
      </div>
      
      <div id="home">
        <a href="/" title="Перейти на сайт" target="_blank" class="icon_small projects-icon-small">
          Перейти на сайт
        </a>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div id="body">
      
      <div id="content">
        <div class="inner">
          
          <?=$_html;?>
          
        </div>
      </div>
      
      <div id="right_panel">
        <div class="inner">
          
          <div class="rounded bg_dark white menu">
            <h1>Инструменты</h1>
            <? foreach ($_menu_secondary as $item) { ?>
              <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>"<?=($item['name'] == $_component['name'] ? ' class="active"' : '');?>>
                <img src="/admin/components/icon/<?=$item['name'];?>/" /><?=$item['title'];?>
              </a>
            <? } ?>
          </div>
          
        </div>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div class="rasporka"></div>
    
  </div>
  
  <div id="footer" class="green">
    <div class="version">Версия <?=$pr_version;?></div>
    
    <div class="etime">Страница сгенерирована за {elapsed_time} сек.</div>
    
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