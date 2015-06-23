<h1 class="icon_big publication-title">Управление публикациями</h1>

<? if (!$parent_id) { ?>
	<div class="links">
		<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_category/" class="icon_small add_i_s">Создать категорию</a>
		
		<div class="clear"></div>
	</div>
		
	<? foreach ($items as $item) { ?>
		<div class="panel selection">
			<div class="left">
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['id'];?>/"><?=$item['title'];?></a>
			</div>
			<div class="right">	
				<div class="buttons">					
					<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['id'];?>/" class="door_in_i_s" title="Перейти"></a>
					<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_category/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
					<a href="#"
						onClick="return send_confirm(
							'Вы уверены, что хотите удалить категорию публикаций и все публикации, относящиеся к ней?',
							'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_category/<?=$item['id'];?>/',
							{},
							'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
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
<? } else { ?>
	<div class="links">
		<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_publication/<?=$parent_id;?>/" class="icon_small add_i_s">Добавить публикацию</a>
		
		<div class="clear"></div>
	</div>	
	
	<? if ($items) { ?>
		<? foreach ($items as $item) { ?>
			<div class="panel selection">
				<div class="left">
					<div class="date"><?=date('d.m.Y', strtotime($item['tm']));?>&nbsp;</div>
					<a href="/admin<?=$_component['path'];?>edit_publication/<?=$item['id'];?>/"><?=$item['title'];?></a>
					<div class="clear"></div>
				</div>
				<div class="right">
					<div class="buttons">
						<? if ($item['active']) { ?>
							<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_publication/<?=$parent_id;?>/<?=$item['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
						<? } else { ?>
							<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_publication/<?=$parent_id;?>/<?=$item['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
						<? } ?>						
						<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_publication/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
						<a href="#"
							onClick="return send_confirm(
								'Вы уверены, что хотите удалить публикацию?',
								'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_publication/<?=$parent_id;?>/<?=$item['id'];?>/',
								{},
								'<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$parent_id;?>/'
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
		<?=(isset($pagination) && $pagination ? $pagination : '');?>
	<? } ?>
	<div class="clear"></div>
	<br /><a href="/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>
<? } ?>