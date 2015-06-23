<div class="comments">
  <div class="title">Комментарии</div> 
  <div class="message">
    Оставить комментарий может любой <a href="/registration/">зарегистрированный</a> участник, все комментарии проходят проверку модератором согласно правилам.
  </div>
  <? if ($comments) { ?>
    <? foreach ($comments as $comment) { ?>
      <div class="comment">
        <p><?=$comment['params']['comment'];?></p>
        <div class="comment-arrow-bottom-border"></div>
        <div class="comment-arrow-bottom"></div>
      </div>
      <div class="user row">
        <? if ($comment['user']) { ?>
          <div class="span1 thumbnail thumbnail_light"><img class="avatar" src="<?=(isset($comment['user']['params']['file_avatar_40_40']) ? $comment['user']['params']['file_avatar_40_40'] : 'images/img/users/default_avatar.png' );?>" alt="" /></div>
          <div class="span4">
            <abbr title="<?=(isset($comment['user']['params']['age']) && $comment['user']['params']['age'] ? 'Возраст: '.$comment['user']['params']['city'] : '');?><?=(isset($comment['user']['params']['city']) && $comment['user']['params']['city'] ? ' Город: '.$comment['user']['params']['city'] : '');?>">
            <?=(isset($comment['user']['params']['name']) && $comment['user']['params']['name'] ? $comment['user']['params']['name'] : 'Аноним');?>
            </abbr>, 
            <?=rus_date($comment['tm'],'d m Y');?> в <?=rus_date($comment['tm'],'H:i');?> | 
            <a class="<?=($_user ? '' : 'disabled');?> pointer" OnClick="$('#form_coment_<?=$comment['id'];?>').show();">Ответить</a></div>
        <? } ?>
        <div class="clear"></div>
        <? if ($_user) { ?>
          <div class="form_comment well well-small pull-left" id="form_coment_<?=$comment['id'];?>">
            <button class="close pull-right">&times;</button>
            <form onsubmit="return false;" enctype="multipart/form-data" target="_self" method="POST" action="/component/messages/processing_comment/<?=$component_name;?>/">             
              <abbr class="span1 thumbnail thumbnail_light" title="имя: <?=$_user['params']['name'];?><?=(isset($_user['params']['age']) && $_user['params']['age'] ? ', возраст: '.$_user['params']['age'].'лет' : '');?><?=(isset($_user['params']['city']) && $_user['params']['city'] ? ', город: '.$_user['params']['city'] : '');?>">
                <img class="avatar" src="<?=(isset($_user['params']['file_avatar_40_40']) && $_user['params']['file_avatar_40_40'] ? $_user['params']['file_avatar_40_40'] : '/images/no_image.png');?>" alt="" />
              </abbr>
              <div class="span3">
                <input type="hidden" name="parent_id" value="<?=$comment['id'];?>" />
                <input type="hidden" name="owner_id" value="<?=$comment['owner_id'];?>" />
                <textarea id="comment" name="comment" rows="1" class="default-generated"></textarea>
                <div class="clear"></div><br/>
                <a onclick="return submit_form(this, 'reload', 'alert');" class="btn btn-primary btn-mini pull-right" href="#">Отправить</a>
                <div id="to_user"></div>
              </div>     
              <div class="clear"></div>
            </form> 
          </div>
        <? } ?>
      </div>
      {{cmp:messages->render_comments_childs<-<?=$component_name;?><-<?=$owner_id;?><-<?=$comment['id'];?>}}
    <? } ?>
  <? } ?>
  <? if ($_user) { ?>
    <div class="title">Добавить комментарий</div> 
    <div class="form_comment well">
      <form onsubmit="return false;" enctype="multipart/form-data" target="_self" method="POST" action="/component/messages/processing_comment/<?=$component_name;?>/">             
        <abbr class="span1 thumbnail" title="имя: <?=$_user['params']['name'];?><?=(isset($_user['params']['age']) && $_user['params']['age'] ? ', возраст: '.$_user['params']['age'].'лет' : '');?><?=(isset($_user['params']['city']) && $_user['params']['city'] ? ', город: '.$_user['params']['city'] : '');?>">
          <img class="avatar" src="<?=(isset($_user['params']['file_avatar']) && $_user['params']['file_avatar'] ? $_user['params']['file_avatar'] : '/images/no_image.png');?>" alt="" />
        </abbr>
        <div class="span4">
          <input type="hidden" name="parent_id" value="0" />
          <input type="hidden" name="owner_id" value="<?=$owner_id;?>" />
          <textarea id="comment" name="comment" rows="2" class="default-generated"></textarea>
          <div class="clear"></div><br/>
          <a onclick="return submit_form(this, 'reload', 'alert');" class="btn btn-primary" href="#">Отправить</a>
          <div id="to_user"></div>
        </div>     
        <div class="clear"></div>
      </form>
    </div>
  <? } else { ?>
    <br/>
    <div class="alert alert-info">Только зарегистрированные пользователи могут оставлять комментарии. Войдите, пожалуйста.<br/><br/></div>
    <div class="well">{{cmp:forms->render<-autorization_reload}}</div>
  <? } ?>
</div> 