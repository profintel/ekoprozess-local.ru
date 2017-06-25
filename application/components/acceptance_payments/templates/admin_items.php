<div class="container-fluid padding_0">
  <div class="block-title row hidden-print">
    <div class="col-sm-5">
      <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$title ? $title : $_component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <div class="col-sm-3 col-sm-offset-4 text-right">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_cashbox/" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-rub"></span> Касса
      </a>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="hidden-print">
    <div class="clearfix">
      <a class="btn btn-default btn-xs pull-left" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
      <a  class="btn btn-default btn-xs pull-right" href="/admin<?=$this->component['path'];?>">Очистить параметры</a>   
    </div><br/>
    <?=$data['form'];?>
  </div>
  <div id="ajax_result">
    {{cmp:acceptance_payments->index<-1}}
  </div>
  <a class="btn btn-default btn-xs hidden-print" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>