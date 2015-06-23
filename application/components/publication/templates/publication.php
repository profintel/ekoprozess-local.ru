      <!-- Панель с заголовком -->
      <div class="panel panel-blue">
        <div class="panel-heading">
          <h3 class="panel-title"><?=($_project['params']['site_title_'. $_language] ? $_project['params']['site_title_'. $_language] : '');?></h3>
        </div>
        <div class="panel-body">
          <h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>
          <? foreach ($items as $item) { ?>
            <? if (isset($item['image']) && $item['image']) { ?>
              <div class="span2">        
                <a href="<?=$_page['path'];?><?=$item['system_name'];?>/" title="<?=$item['params']['name_'. $_language];?>"><img src="<?=$item['image']['thumbs']['180_135']?>" /></a>
              </div>
              <div class="span4 publications">
                <p class="title"><a href="<?=$_page['path'];?><?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></p>
                <p class="text">                  
                  <?=$item['params']['text_small_'. $_language];?>
                </p>
                <small class="date"><?=rus_date($item['tm_start'], 'd m, Y');?></small>
              </div> 
            <? } else { ?>
              <div class="span6 publications">
                <p class="title"><a href="<?=$_page['path'];?><?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></p>
                <p class="text">                  
                  <?=$item['params']['text_small_'. $_language];?>
                </p>
                <small class="date"><?=rus_date($item['tm_start'], 'd m, Y');?></small>
              </div>
            <? } ?>
            <br/>
          <? } ?>
          <?=($pagination ? $pagination : '');?>
        </div>
      </div>
