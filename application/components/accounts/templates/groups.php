<h1 class="icon_big <?=$_component['name'];?>-title">Управление группами</h1>
	
<div class="links">
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_group/" class="icon_small add_i_s">Создать группу</a>
	
	<div class="clear"></div>
</div>
<div class="clear"></div>

<? foreach ($items as $item) { ?>
	<div class="panel selection">
		<div class="left">
			<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_group/<?=$item['id'];?>/"><?=$item['title'];?></a>
		</div>
		<div class="right">	
			<div class="buttons">
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_group/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
				<a href="#"
					onClick="return send_confirm(
						'Вы уверены, что хотите удалить группу?',
						'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_group/<?=$item['id'];?>/',
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
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_group/" class="icon_small add_i_s">Создать группу</a>
	
	<div class="clear"></div>
</div>

<br /><br /><a href="/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>