<h1 class="icon_big <?=$_component['name'];?>-title">Управление пользователями</h1>

<? if ($groups) { ?>
  <div class="tags">
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>users/">
      Все
    </a>   
    <? foreach ($groups as $item) { ?>
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>users/<?=$item['id'];?>/"<?=($item['id'] == $category_id ? ' class="active"' : '');?>>
        <?=$item['title'];?>&nbsp;(<?=$item['users_cnt'];?>)
      </a>
    <? } ?>
    <div class="clear"></div>
  </div>
<? } ?>
	
<div class="links">
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>/create_user/" class="icon_small add_i_s">Создать пользователя</a>
	
	<div class="clear"></div>
</div>
<div class="clear"></div>

<? foreach ($items as $item) { ?>
	<div class="panel selection">
		<div class="left">
			<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_user/<?=$item['id'];?>/"><?=$item['username'];?></a>
		</div>
		<div class="right">	
			<div class="buttons">						
        <? if ($item['active']) { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_user/<?=$item['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
        <? } else { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_user/<?=$item['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
        <? } ?>	
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_user/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
				<a href="#"
					onClick="return send_confirm(
						'Вы уверены, что хотите удалить учетную запись <?=$item['id'];?>?',
						'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_user/<?=$item['id'];?>/',
						{},
						'reload'
					);"
					class="cross_i_s"
					title="Удалить"
				></a>				
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
<? } ?>

<div class="clear"></div>
<div class="links">
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_user/" class="icon_small add_i_s">Создать пользователя</a>
	
	<div class="clear"></div>
</div>

<br /><br /><a href="/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>