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
    <link rel="stylesheet" href="/lightbox/css/jquery.lightbox-0.5.css" type="text/css" />
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
  </head>
  <body>

      <!-- Шапка сайта -->
      <div class="masthead">
          <div class="container">
              <div class="header">
                  <div class="logo col-xs-7 padding-0">
                      <a href="/" title="<?=(@$_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?><?=(@$_project['params']['site_title_'. $_language] && $_page['params']['title_'. $_language] ? ' - ' : '');?><?=(@$_page['params']['title_'. $_language] ? $_page['params']['title_'. $_language] : '');?>">
                          <img  src="/images/logo.png"
                                alt="<?=(@$_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?>"
                                title="<?=(@$_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?>">
                          <span><?=(@$_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?></span>
                      </a>
                  </div>
                  <div class="contacts col-xs-5">
                      {{tpl:header_contacts}}
                  </div>
                  <div class="main_menu col-xs-10">
                      <!-- Главное меню -->
                      {{cmp:menus->render<-main_menu}}

                  </div>
                  <div class="col-xs-2 padding-0">
                      <a class="btn btn-feedback pull-right" href="#" data-toggle="modal" data-target="#feedbacks">обратная связь</a>
                      <!-- Modal -->
                      <div class="modal fade" id="feedbacks" tabindex="-1" role="dialog" aria-labelledby="feedback" aria-hidden="true">
                          <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                  <div class="modal-header blue_back">
                                      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">закрыть</span></button>
                                      <h4 class="modal-title" id="myModalLabel">Обратная связь</h4>
                                  </div>
                                  <div class="modal-body gray_back">
                                      {{cmp:forms->render<-feedback}}
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <?=$_content;?>
    
    <!-- Подвал сайта -->
    <div id="footer">
      <div class="container padding-0">
        <div class="col-xs-3"><img class="footer_logo" src="/images/logo_footer.png" alt="<?=(@$_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?>"></div>
        <div class="col-xs-4">
            <!-- Нижнее меню -->
            <div class="clearfix bottom_menu">
                {{cmp:menus->render<-bottom_menu}}
            </div>
            <form class="form_search" action="/search/" METHOD="GET" enctype="multipart/form-data" target="_self" name="searching">
                <input class="search" type="text" placeholder="Поиск..." name="query_string" onclick="$(this).submit();"/>
            </form>
        </div>
        
        <!-- Контакты в подвале -->
        <div class="col-xs-4 text-right footer_contacts">
          {{tpl:footer_contacts}}
        </div>
        <div class="col-xs-1 footer_social text-center">
            <a href=""><i class="fa fa-facebook"></i></a><br>
            <a href=""><i class="fa fa-twitter"></i></a><br>
            <a href=""><i class="fa fa-vk"></i></a>
        </div>

      </div>
    </div>
    
  </body>
  <script type="text/javascript" src="/components/tp_site.js"></script>  
  <script>$('.carousel').carousel()</script>
  <script type="text/javascript" src="/lightbox/js/jquery.lightbox-0.5.js"></script>
</html>