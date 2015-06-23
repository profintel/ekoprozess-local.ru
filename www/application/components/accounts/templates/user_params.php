<h1 class="icon_big <?=$_component['name'];?>-title">Управление параметрами пользователей</h1>
	
<div class="links">
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>/create_user_param/" class="icon_small add_i_s">Создать параметр пользователей</a>
	
	<div class="clear"></div>
</div>
<div class="clear"></div>

<? foreach ($items as $item) { ?>
	<div class="panel selection">
		<div class="left">
			<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_user_param/<?=$item['id'];?>/"><?=$item['title'];?></a>
		</div>
		<div class="right">	
			<div class="buttons">
        <a href="/admin<?=$_component['path'];?>user_param_move/<?=$item['id'];?>/1/" class="arrow_down"></a>
        <a href="/admin<?=$_component['path'];?>user_param_move/<?=$item['id'];?>/-1/" class="arrow_up"></a>
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_user_param/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
				<a href="#"
					onClick="return send_confirm(
						'Вы уверены, что хотите удалить учетную запись?',
						'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_user_param/<?=$item['id'];?>/',
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
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_user_param/" class="icon_small add_i_s">Создать параметр пользователей</a>
	
	<div class="clear"></div>
</div>

<br /><br /><a href="/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>