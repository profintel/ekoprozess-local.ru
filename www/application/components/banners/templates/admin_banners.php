<h1 class="icon_big <?=$_component['name'];?>-title">Баннеры</h1>
	
<div class="links">
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>/create_banner/" class="icon_small add_i_s">Создать баннер</a>
	
	<div class="clear"></div>
</div>
<div class="clear"></div>

<? foreach ($items as $item) { ?>
	<div class="panel selection">
		<div class="left">
			<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_banner/<?=$item['id'];?>/"><?=$item['title'];?></a>
		</div>
		<div class="right">
			<div class="buttons">
        <? if ($item['active']) { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_banner/<?=$item['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
        <? } else { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_banner/<?=$item['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
        <? } ?>
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_banner/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
				<a href="#"
					onClick="return send_confirm(
						'Вы уверены, что хотите удалить баннер?',
						'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_banner/<?=$item['id'];?>/',
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
	<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_banner/" class="icon_small add_i_s">Создать баннер</a>
	
	<div class="clear"></div>
</div>

<br /><br /><a href="/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>