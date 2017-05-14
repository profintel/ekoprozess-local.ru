<div class="container-fluid padding_0 hidden-print">
  <div class="block-title row">
    <div class="col-sm-3">
      <h1><span class="glyphicon <?=($this->component['icon']?$this->component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$data['title'] ? $data['title'] : $this->component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <div class="col-sm-6 quick_form"><?=$data['quick_form'];?></div>
    <div class="col-sm-3 pull-right text-right">
      <? if(isset($data['link_create']) && $data['link_create']) { ?>
        <a href="<?=$data['link_create']['path'];?>" class="btn btn-primary btn-xs pull-right">
          <span class="glyphicon glyphicon-plus"></span> <?=$data['link_create']['title'];?>
        </a>
      <? } ?>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="clearfix hidden-print">
    <a class="btn btn-default btn-xs pull-left" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
    <? if (isset($data['form']) && $data['form']) { ?>
      <a  class="btn btn-default btn-xs pull-right" href="/admin/cities/">Очистить параметры</a>
      <div class="clearfix"></div><br>
      <?=$data['form'];?>
    <? } ?>
    <a href="javascript:void(0)" onclick="window.print();" class="btn btn-primary btn-xs pull-right">
      <span class="glyphicon glyphicon-print"></span> 
      Печать
    </a>
  </div>
  <br/>
  <div id="ajax_result">
    {{cmp:cities->cities<-1}}
  </div>
  <a class="btn btn-default btn-xs" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>