<!doctype html>
<html>
  <head>
    <title>
      <?=(@$_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?>
      <?=(@$_project['params']['site_title_'. $_language] && $_page['params']['title_'. $_language] ? '-' : '');?>
      <?=(@$_page['params']['title_'. $_language] ? $_page['params']['title_'. $_language] : '');?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <? if (@$_page['params']['keywords_'. $_language]) { ?>
      <meta name="keywords" content="<?=$_page['params']['keywords_'. $_language];?>" />
    <? } elseif (@$_project['params']['keywords_'. $_language]) { ?>
      <meta name="keywords" content="<?=$_project['params']['keywords_'. $_language];?>" />
    <? } ?>
    <? if (@$_page['params']['description_'. $_language]) { ?>
      <meta name="description" content="<?=$_page['params']['description_'. $_language];?>" />
    <? } elseif(@$_project['params']['description_'. $_language]) {?>
      <meta name="description" content="<?=$_project['params']['description_'. $_language];?>" />
    <? } ?>	  
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/components/tp_site.css" type="text/css" />    
  </head>
  <body>
    <?=$_content;?>   
  </body>
  
  <script type="text/javascript" src="/components/tp_site.js"></script>
</html>