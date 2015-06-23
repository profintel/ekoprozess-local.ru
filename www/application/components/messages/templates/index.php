<h1 class="icon_big <?=$_component['name'];?>-title"><?=$title;?></h1>

<? if (!$component_name) { ?>
	<? foreach ($items as $item) { ?>
		<div class="panel selection">
			<div class="left">
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['name'];?>/"><?=$item['title'];?></a>
			</div>
			<div class="right">	
				<div class="buttons">
					<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['name'];?>/" class="door_in_i_s" title="Перейти"></a>
					<a href="#"
						onClick="return send_confirm(
							'Вы уверены, что хотите удалить все сообщения данной категории?',
							'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_category/<?=$item['name'];?>/',
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
<? } elseif ($categorie_childs && !$owner_id) { ?>
  <? foreach ($items as $item) { ?>
		<div class="panel selection">
			<div class="left">
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$component_name;?>/<?=$item['id'];?>/"><?=$item['title'];?></a>
			</div>
			<div class="right">	
				<div class="buttons">
					<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$component_name;?>/<?=$item['id'];?>/" class="door_in_i_s" title="Перейти"></a>
					<a href="#"
						onClick="return send_confirm(
							'Вы уверены, что хотите удалить все сообщения данной категории?',
							'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_category/<?=$component_name;?>/<?=$item['id'];?>/',
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
	<br /><a href="/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>
<? } else { ?>
	<? foreach ($items as $item) { ?>
		<div class="panel selection">
			<div class="left">
				<span class="date"><?=date('d.m.Y H:i', strtotime($item['tm']));?></span>
				<span class="user"><?=($item['user_id'] ? '<span class="green">USER: </span>'.$item['user']['username'] : '<span class="green">IP: </span>'.$item['user_ip']);?></span>
				<span class="date">№<?=$item['id'];?></span>
				<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_message/<?=$item['id'];?>/"><?=$item['title'];?></a>
        <br/><?=(isset($item['params']['comment']) ? $item['params']['comment'] : '');?>        
			</div>
			<div class="right">	
				<div class="buttons">
          <? if ($item['active']) { ?>
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_message/<?=$component_name;?>/<?=$item['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
          <? } else { ?>
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_message/<?=$component_name;?>/<?=$item['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
          <? } ?>	
					<a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_message/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
					<a href="#"
						onClick="return send_confirm(
							'Вы уверены, что хотите удалить сообщение?',
							'<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_message/<?=$component_name;?>/<?=$item['id'];?>/',
							{},
							'<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$component_name;?>'
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
	<br /><a href="/admin<?=$_component['path'];?><?=($owner_id ? $component_name.'/' : '');?>" class="icon_small arrow_left_i_s">Назад</a>
<? } ?>