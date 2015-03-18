<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Просмотр установленного компонента
  </h1>
</div>
<div class="container-fluid">
  <div class="panel">
    <div class="panel-heading">
      <div class="panel-heading-title"><span class="glyphicon <?=(@$item['icon']?$item['icon']:'glyphicon-ok');?>"></span> <?=$item['title'];?></div>
    </div>
    <div class="panel-body">
      <? if ($item['description']) { ?>
        <p><?=$item['description'];?></p>
      <? } ?>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6 col-xs-6 col-sm-6">
          <p>Системное имя: <b><?=$item['name'];?></b></p>
          
          <p>Установлен: <b><?=rus_date($item['tm']);?> года</b></p>
          
          <? if ($item['parent']) { ?>
            <p>
              Родительский компонент:
              <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>installed/<?=$item['parent'];?>/" class="blank" target="_blank">
                <?=$item['parent'];?>
              </a>
            </p>
          <? } ?>
          
          <? if ($childs) { ?>
            <p>
              Зависимые компоненты:
              <? foreach ($childs as $child) { ?>
                <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>installed/<?=$child['id'];?>/" class="blank" target="_blank"><?=$child['name'];?></a>
              <? } ?>
            </p>
          <? } ?>
        </div>
        <div class="col-md-6 col-xs-6 col-sm-6">
         <? if ($item['main']) { ?>
            <p><span class="glyphicon glyphicon-ok"></span> <b>Компонент по умолчанию</b></p>
          <? } else { ?>
            <p>
              <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>set_main/<?=$item['id'];?>/" class="">
                <span class="glyphicon glyphicon-ok"></span> Сделать компонентом по умолчанию
              </a>
            </p>
          <? } ?>
          
          <p>
            <a href="#" onClick="return send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/<?=$item['id'];?>/');">
              <span class="glyphicon glyphicon-refresh font-bold"></span> Обновить кэш
            </a>
          </p>
          
          <p>
            <a href="#"
              onClick="return send_confirm(
                'Вы уверены, что хотите удалить компонент?',
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
                {},
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
              );"
            >
              <span class="glyphicon glyphicon-remove text-danger font-bold"></span> Удалить компонент
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
  <br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class=""><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
