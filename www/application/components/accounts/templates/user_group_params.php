<h1 class="icon_big <?=$_component['name'];?>-title">Распределение параметров пользователей по группам</h1>

<form onsubmit="return false;" enctype="multipart/form-data" target="_self" method="POST" action="/admin/accounts/_edit_user_group_params/">
  <div class="panel selection">
    <div class="name">Параметры</div>
    <? foreach ($groups as $group) { ?>
      <div class="param"><?=$group['title'];?></div>
    <? } ?>
    <div class="clear"></div>
  </div>   
  <? foreach ($items as $item) { ?>
    <div class="panel selection">
      <div class="name"><?=$item['title'];?></div>
      <? foreach ($groups as $group) { ?>
        <div class="param"><input type="checkbox" name="param_<?=$item['id'];?>_group_<?=$group['id'];?>" <?=(isset($user_group_param['param_'.$item['id'].'_group_'.$group['id']]) ? 'checked' : '');?>/></div>
      <? } ?>
      <div class="clear"></div>
    </div>
  <? } ?>
  <a class="button default-generated icon_small accept_i_s" onclick="return submit_form(this, '1', null);" href="#">Сохранить изменения</a>
</form>

<div class="clear"></div>

<br /><br /><a href="/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>