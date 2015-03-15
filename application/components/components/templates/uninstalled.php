<h1 class="h3"><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
  Просмотр доступного компонента
</h1>
<div class="panel">
  <div class="panel-heading">
    <? if (isset($item['title']) && $item['title']) { ?>
      <div class="panel-heading-title"><span class="glyphicon <?=(@$item['icon']?$item['icon']:'glyphicon-ok');?>"></span> <?=$item['title'];?></div>
    <? } ?>
  </div>
  
  <div class="panel-body">
    <? if (isset($item['description']) && $item['description']) { ?>
      <p><?=$item['description'];?></p>
    <? } ?>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6 col-xs-6 col-sm-6">
        <p>Системное имя: <b><?=$item['name'];?></b></p>
        <? if (isset($item['parent']) && $item['parent']) { ?>
          <p>
            Родительский компонент: <b><?=$item['parent'];?></b>
          </p>
        <? } ?>
        
        <? if (isset($item['requirement']) && $item['requirement']) { ?>
          <p>
            Необходимые компоненты: <b><?=implode(', ', $item['requirement']);?></b>
          </p>
        <? } ?>          
      </div>
      <div class="col-md-6 col-xs-6 col-sm-6">
        <? if (!$item['errors']) { ?>
          <p>
            <a href="#"
              onClick="return send_request(
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>install/<?=$item['name'];?>/',
                {},
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
              );"
              class=""
              title="Установить компонент"
            >
              <span class="glyphicon glyphicon-plus"></span> Установить компонент
            </a>
          </p>
        <? } else { ?>
          <p class="alert alert-danger"><b>Установка невозможна</b></p>
        <? } ?>
      </div>
    </div>
  </div>
  
</div>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class=""><span class="glyphicon glyphicon-backward"></span> Назад</a>