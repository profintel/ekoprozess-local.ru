<h1 class="icon_big catalog-title">Управление параметрами категории</h1>

	<div class="links">
		<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_category_param/<?=$id;?>/" class="icon_small add_i_s">Добавить параметр</a>
		
		<div class="clear"></div>
	</div>	
	
	<? if ($items) { ?>
		<? foreach ($items as $item) { ?>
			<div class="panel selection">
				<div class="left">
					<a href="/admin<?=$_component['path'];?>edit_category_param/<?=$item['id'];?>/"><?=$item['name'];?></a>
					<div class="clear"></div>
				</div>
				<div class="right">
					<div class="buttons">
                        <? if ($item['in_filter']) { ?>
                            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>in_filter_off_param/<?=$item['id'];?>/" class="in_filter_i_s" title="Не использовать в фильтре"></a>
                        <? } else { ?>
                            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>in_filter_on_param/<?=$item['id'];?>/" class="in_filter_off_i_s" title="Использовать в фильтре"></a>
                        <? } ?>
                        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_category_param/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
                        <? if ($item['id'] !=1) { ?>
                            <a href="#"
                                onClick="return send_confirm(
                                    'Вы уверены, что хотите удалить параметр?',
                                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_category_param/<?=$item['id'];?>/',
                                    {},
                                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_category_params/<?=$id;?>/'
                                );"
                                class="cross_i_s"
                                title="Удалить"
                            ></a>
                        <? } ?>
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