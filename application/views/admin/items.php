<div class="container-fluid padding_0">
  <div class="block-title row">
    <div class="<?=(@$search_path ?'col-sm-7':'col-sm-12');?>">
      <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$title ? $title : $_component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <? if (@$search_path) { ?>
      <div class="col-sm-5 text-right">
        <form action="<?=$search_path;?>" method="GET" class="form-inline">
          <div class="form-group">
            <input type="text" name="title" value="<?=$search_title;?>" <?=($search_title ? 'autofocus="true"' : '');?> class="form-control input-sm" id="searchTitle" placeholder="Введите название">
          </div>
          <div class="form-group">
          <button type="submit" class="btn btn-default btn-sm">Поиск</button>
          </div>
        </form>
      </div>
    <? } ?>
  </div>
</div>
<div class="container-fluid">
  <div class="clearfix block_mb20">
    <? if (count($items) > 20) { ?>
      <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
    <? } ?>
    <? if (empty($btn_create_disabled)) { ?>
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_<?=$component_item['name'];?>/<?=(isset($parent_id) ? $parent_id.'/' : '');?>" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-plus"></span> Создать <?=@$component_item['title'];?>
      </a>    
    <? } ?>
  </div>
  <? if (@$search_title && !$items) { ?>
    <div class="alert alert-warning clearfix">
      <h5 class="pull-left">По запросу "<?=$search_title;?>" ничего не найдено</h5>
      <a class="btn btn-default btn-xs pull-right" href="<?=$search_path;?>">Очистить поиск</a>
    </div>
  <? } ?>
  <ul class="list-group">
    <? foreach ($items as $item) { ?>
      <li class="clearfix list-group-item" <?=(isset($item['color']) ? 'style="background-color:'.$item['color'].'"' : '')?>>
        <a class="pull-left icon" data-toggle="dropdown">
          <span class="glyphicon glyphicon-cog"></span>
        </a>
        <a class="col-md-11 col-sm-11 col-xs-11 dropdown-toggle" data-toggle="dropdown">
          <?/*?> <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_<?=$component_item['name'];?>/<?=$item['id'];?>/"><?=$item['title'];?></a><?*/?>
          <?=$item['title'];?>
        </a>
        <ul class="dropdown-menu">
          <?/* if (isset($item['active']) && $item['active']) { ?>
            <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_<?=$component_item['name'];?>/<?=$item['id'];?>/" title="Отключить"><span class="glyphicon glyphicon-check"></span> Отключить</a></li>
          <? } ?>
          <? if (isset($item['active']) && !$item['active']) { ?>
            <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_<?=$component_item['name'];?>/<?=$item['id'];?>/" title="Включить"><span class="glyphicon glyphicon-unchecked"></span> Включить</a></li>
          <? } */?>
          <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_<?=$component_item['name'];?>/<?=$item['id'];?>/" title="Редактировать"><span class="glyphicon glyphicon-edit"></span> Редактировать</a></li>
          <? if (array_key_exists('parent_id',$item) && is_null($item['parent_id'])) { ?>
            <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_<?=$component_item['name'];?>/<?=$item['id'];?>/" title="Создать подраздел"><span class="glyphicon glyphicon-plus"></span> Создать подраздел</a></li>
          <? } ?>
          <li class="divider"></li>
          <li>
            <a href="#"
              onClick="return send_confirm(
                'Вы уверены, что хотите удалить объект - <?=$item['title'];?>?',
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_<?=$component_item['name'];?>/<?=$item['id'];?>/',
                {},
                'reload'
              );"                    
              title="Удалить"
            ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
          </li>
        </ul>
      </li>
      <? if(isset($item['childs'])) {?>
        <? foreach ($item['childs'] as $child) {?>
          <li class="clearfix list-group-item">
            <a class="pull-left icon" data-toggle="dropdown">
              <span class="glyphicon glyphicon-cog"></span>
            </a>
            <a class="col-md-11 col-sm-11 col-xs-11 dropdown-toggle" data-toggle="dropdown">
              &emsp;-&nbsp;<?=$child['title'];?>
            </a>
            <ul class="dropdown-menu">
              <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_<?=$component_item['name'];?>/<?=$child['id'];?>/" title="Редактировать"><span class="glyphicon glyphicon-edit"></span> Редактировать</a></li>
              <? if (array_key_exists('parent_id',$child) && is_null($child['parent_id'])) { ?>
                <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_<?=$component_item['name'];?>/<?=$child['id'];?>/" title="Создать подраздел"><span class="glyphicon glyphicon-plus"></span> Создать подраздел</a></li>
              <? } ?>
              <li class="divider"></li>
              <li>
                <a href="#"
                  onClick="return send_confirm(
                    'Вы уверены, что хотите удалить объект - <?=$child['title'];?>?',
                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_<?=$component_item['name'];?>/<?=$child['id'];?>/',
                    {},
                    'reload'
                  );"                    
                  title="Удалить"
                ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
              </li>
            </ul>
          </li>
        <? } ?>
      <? } ?>
    <? } ?>
  </ul>
  <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <? if(isset($back)) {?>
    <a class="btn btn-default btn-xs" href="<?=$back;?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
  <? } else {?>
    <a class="btn btn-default btn-xs hidden-print" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
  <? } ?>
</div>
<br/>