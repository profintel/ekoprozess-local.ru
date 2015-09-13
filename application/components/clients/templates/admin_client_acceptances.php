<div class="container-fluid padding_0">
  <div class="block-title row">
    <div class="col-sm-3">
      <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$title ? $title : $_component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <div class="col-sm-6 quick_form"><?=(isset($quick_form)?$quick_form:'');?></div>
    <div class="col-sm-3 text-right">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_acceptance/" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-plus"></span> Создать акт приемки
      </a>
    </div>
  </div>
</div>
<div class="container-fluid">
  <?=$html?>
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>