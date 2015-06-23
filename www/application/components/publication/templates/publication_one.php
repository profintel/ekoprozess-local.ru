      <!-- Панель с заголовком -->
      <div class="panel panel-orange">
        <div class="panel-heading">
          <h3 class="panel-title"><?=($_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?></h3>
        </div>
        <div class="panel-body clearfix">
          <span class="title"><?=date('d.m.Y', strtotime($item['tm_start']));?></span>
          <h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>          
          <? if (isset($item['image']) && $item['image']) { ?>
              <a class="zoom img" href="<?=$item['image']['image']?>" title="<?=$item['params']['name_'. $_language];?>"><img class="img-rounded public" src="<?=$item['image']['thumbs']['180_135']?>" /></a>
          <? } ?>	
          <div class="full_text"><?=$item['params']['text_full_'. $_language];?></div>          
          <!-- Поделиться в соц.сетях -->
          <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
          <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,gplus" data-yashareTheme="counter"></div>
          <hr>
          <a href="<?=$_page['path'];?>" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Назад</a>
        </div>
      </div>