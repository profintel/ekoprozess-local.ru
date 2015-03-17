<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Управление группами
  </h1>
</div>
  

<div class="container-fluid wrapper-list">
  <div class="clearfix well-sm">
    <div class="pull-right">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_group/" class="icon_small add_i_s">Создать группу</a>
    </div>
  </div>

  <? foreach ($items as $item) { ?>
    <div class="clearfix item-list">
      <div class="col-md-9 col-sm-8 col-xs-8">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_group/<?=$item['id'];?>/"><?=$item['title'];?></a>
      </div>
      <div class="col-md-3 col-sm-4 col-xs-4">  
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
  <br /><br /><a href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
