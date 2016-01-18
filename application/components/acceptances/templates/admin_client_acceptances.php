<div class="container-fluid padding_0">
  <div class="block-title row hidden-print">
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
  <div class="hidden-print">
    <div class="clearfix">
      <a class="btn btn-default btn-xs pull-left" href="/admin<?=$this->component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
      <a  class="btn btn-default btn-xs pull-right" href="/admin<?=$this->component['path'];?>acceptances/">Очистить параметры</a>   
    </div><br/>
    <?=$data['form'];?>
  </div>
  <div id="ajax_result">
    {{cmp:acceptances->_render_client_acceptances_table<-<?=base64_encode(serialize($data));?>}}
  </div>
  <a class="btn btn-default btn-xs hidden-print" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>