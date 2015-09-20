<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex,nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>ЭКО-процессинг - Панель администратора</title>
  
  <link rel="stylesheet" type="text/css" href="/adm/chosen/chosen.min.css" />
  <link rel="stylesheet" type="text/css" href="/components/tp_admin.css?<?=time();?>" />
  
  <script type="text/javascript" src="/components/tp_admin.js?<?=time();?>"></script>
  <script type="text/javascript" src="/adm/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="/adm/chosen/chosen.jquery.min.js"></script> 

  <!--[if gte IE 9]>
    <style type="text/css">
      .gradient {
         filter: none;
      }
    </style>
  <![endif]--> 
  
</head>
<body class="">
  <div id="wrapper" class="gray-bg">
    <nav role="navigation" class="navbar-default navbar-static-side hidden-print">
      <div class="sidebar-collapse">
        <ul id="side-menu" class="nav">
          <li class="nav-header">
            <div class="visible-lg-block">
              <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="logo-element">
                    <? if (isset($_admin['image']) && $_admin['image']) { ?>
                      <img src="<?=$_admin['images'][0]['thumbs']['32_32'];?>" class="img-circle" alt="<?=$_admin['username'];?>">
                    <? } else { ?>
                      <img src="/components/accounts/media/user.png" class="img-circle" alt="<?=$_admin['username'];?>">
                    <? } ?>
                  </span>
                  <? if (isset($_admin['params']['name_'.$_language]) && $_admin['params']['name_'.$_language]) { ?>
                    <div class=""><?=$_admin['params']['name_'.$_language];?></div>
                  <? } ?>
                  <div class="">
                    <? if (isset($_admin['params']['post_'.$_language]) && $_admin['params']['post_'.$_language]) { ?>
                      <span><?=$_admin['params']['post_'.$_language];?></span>
                    <? } else { ?>
                      <span><?=$_admin['username'];?></span>
                    <? } ?>
                    <b class="caret"></b>
                  </div>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="/admin/administrators/edit_admin/<?=$_admin['id'];?>/">Редактировать профиль</a></li>
                  <li class="divider"></li>
                  <li><a href="/autorization/close/"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
                </ul>
              </div>
            </div>
            <div class="menu-primary-element hidden-lg">
              <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-primary btn-xs" data-toggle="dropdown">
                  <span class="glyphicon glyphicon-align-justify"></span>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="/admin/administrators/edit_admin/<?=$_admin['id'];?>/">Редактировать профиль</a></li>
                  <li class="divider"></li>
                  <li><a href="/autorization/close/"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
                </ul>
              </div>
            </div>
          </li>
          <? foreach ($_menu_secondary as $item) { ?>
            <li class="visible-lg-block <?=($item['name'] == $_component['name'] ? 'active' : '');?>">
              <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>">
                <span class="glyphicon <?=($item['icon']?$item['icon']:'glyphicon-ok');?>"></span><?=$item['title'];?>
                <? if ($item['name']=='calendar') {?>
                  <div class="text-right">
                    <? if ($_admin['red_events']) {?>
                      <span class="label label-danger"><?=count($_admin['red_events']);?></span>
                    <?}?>
                    <? if ($_admin['events']) {?>
                      <span class="label label-info"><?=count($_admin['events']);?></span>
                    <?}?>
                  </div>
                <?}?>
              </a>
            </li>
            <li class="hidden-lg el-tooltip <?=($item['name'] == $_component['name'] ? 'active' : '');?>" data-toggle="tooltip" data-placement="right" title="<?=$item['title'];?>">
              <a href="<?=$_lang_prefix;?>/admin<?=$item['path'];?>">
                <span class="glyphicon <?=($item['icon']?$item['icon']:'glyphicon-ok');?>"></span>
              </a>
            </li>
          <? } ?>
        </ul>
      </div>
      
      <?/*if ($_admin['red_events']) {?>
        <div class="alert alert-danger" id="events-danger">
          <?foreach ($_admin['red_events'] as $key => $value) {?>
            <div><?=$value['title'];?></div>          
          <?}?>
        </div>
      <?}*/?>
    </nav>
    <div id="page-wrapper">
      <div id="header">
        <nav class="navbar-default border-bottom hidden-print">
          <div class="navbar-collapse">
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
      <div id="content" class="">
        <?=$_html;?>
      </div>
      <div id="progress-main" class="well hidden-print">
        <div class="progress">
          <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
        </div>
      </div>
      <div class="alert" id="alert_msg"></div>
    </div>
  </div>
  <div id="modal" class="modal fade hidden-print">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>
</body>
</html>