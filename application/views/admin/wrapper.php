<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex,nofollow" />
  
  <!-- <link rel="stylesheet" type="text/css" href="/adm/chosen/chosen.css" /> -->
  <link rel="stylesheet" type="text/css" href="/components/tp_admin.css?<?=time();?>" />
  
  <title>ЭКО-процессинг - Панель администратора</title>
</head>
<body class="gray-bg">
  <div id="wrapper" class="">
    <nav role="navigation" class="navbar-default navbar-static-side">
      <div class="sidebar-collapse">
        <ul id="side-menu" class="nav">
          <li class="nav-header">
            <div class="visible-lg-block">
              <a href="/admin/" class="logo-element">
                <? if (isset($_admin['image']) && $_admin['image']) { ?>
                  <img src="/adm/images/logo.png" class="img-circle" alt="<?=$_admin['username'];?>">
                <? } else { ?>
                  <img src="/components/accounts/media/user.png" class="img-circle" alt="<?=$_admin['username'];?>">
                <? } ?>
              </a>              
              <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <? if (isset($_admin['params']['name']) && $_admin['params']['name']) { ?>
                    <div class="font-bold"><?=$_admin['params']['name'];?></div>
                  <? } ?>
                  <div class="font-bold">
                    <? if (isset($_admin['params']['post']) && $_admin['params']['post']) { ?>
                      <span><?=$_admin['params']['post'];?></span>
                    <? } else { ?>
                      <span><?=$_admin['username'];?></span>
                    <? } ?>
                    <b class="caret"></b>
                  </div>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="/admin/administrators/profile/">Сменить пароль</a></li>
                  <li class="divider"></li>
                  <li><a href="/autorization/close/"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
                </ul>
              </div>
            </div>
            <div class="menu-primary-element hidden-lg">
              <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-success" data-toggle="dropdown">
                  <span class="glyphicon glyphicon-align-justify"></span>
                </a>
                <ul class="dropdown-menu">
                  <? foreach ($_menu_primary as $item) { ?>
                    <li class="<?=($item['name'] == $_component['name'] ? 'active' : '');?>">
                      <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>">
                        <?=$item['title'];?>
                      </a>
                    </li>
                  <? } ?>
                  <li class="divider"></li>
                  <li><a href="/autorization/close/"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
                </ul>
              </div>
            </div>
          </li>
          <? foreach ($_menu_secondary as $item) { ?>
            <li class="visible-lg-block <?=($item['name'] == $_component['name'] ? 'active' : '');?>">
              <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>">
                <span class="glyphicon <?=($item['icon']?$item['icon']:'glyphicon-chevron-right');?>"></span><?=$item['title'];?>
              </a>
            </li>
            <li class="hidden-lg el-tooltip <?=($item['name'] == $_component['name'] ? 'active' : '');?>" data-toggle="tooltip" data-placement="right" title="<?=$item['title'];?>">
              <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>">
                <span class="glyphicon <?=($item['icon']?$item['icon']:'glyphicon-chevron-right');?>"></span>
              </a>
            </li>
          <? } ?>
        </ul>
      </div>
    </nav>
    <div id="page-wrapper">
      <div id="header">
      <nav class="navbar-default">
        <div class="collapse navbar-collapse">
          <ul class="nav nav-pills navbar-left">
            <? foreach ($_menu_primary as $item) { ?>
              <li class="<?=($item['name'] == $_component['name'] ? 'active' : '');?>">
                <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>">
                  <?=$item['title'];?>
                </a>
              </li>
            <? } ?>
          </ul>
          <ul class="nav navbar-right">
            <li>
              <a href="/autorization/close/"><span class="glyphicon glyphicon-log-out"></span> Выйти</a>
            </li>
          </ul>
        </div>
      </nav>
      </div>
      <div id="content" class="container-fluid">
        <?=$_html;?>
      </div>
    </div>
  </div>
  <div id="modal">
    <div class="title"></div>
    <div class="inner"></div>
    <div class="buttons"></div>
  </div>
  <script type="text/javascript" src="/components/tp_admin.js?<?=time();?>"></script>
  <script type="text/javascript" src="/adm/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="/adm/flowplayer/flowplayer-3.2.4.min.js"></script>
  <script type="text/javascript" src="/adm/chosen/chosen.jquery.js"></script>  
</body>
</html>