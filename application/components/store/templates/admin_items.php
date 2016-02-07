<div class="container-fluid padding_0 hidden-print">
  <div class="block-title row">
    <div class="col-sm-9">
      <h1><span class="glyphicon <?=($this->component['icon']?$this->component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$data['title'] ? $data['title'] : $this->component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <div class="col-sm-3 pull-right text-right">
      <a href="<?=$data['link_create']['path'];?>" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-plus"></span> <?=$data['link_create']['title'];?>
      </a>
    </div>
  </div>
</div>
<div class="container-fluid">
  <? if (isset($data['form']) && $data['form']) { ?>
    <div class="clearfix">
      <a class="btn btn-default btn-xs pull-left" href="/admin<?=$this->component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
      <a  class="btn btn-default btn-xs pull-right" href="/admin<?=$this->component['path'];?>clients_report/">Очистить параметры</a>   
    </div><br/>
    <?=$data['form'];?>
  <? } ?>  
  <div id="ajax_result">
    {{cmp:store->_render_items_report_table<-<?=base64_encode(serialize($data));?>}}
  </div>
  <a class="btn btn-default btn-xs" href="/admin<?=$this->component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>