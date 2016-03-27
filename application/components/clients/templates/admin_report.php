<div class="container-fluid padding_0">
  <div class="block-title row">
    <div class="col-sm-3">
      <h1><span class="glyphicon <?=($this->component['icon']?$this->component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$data['title'] ? $data['title'] : $this->component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <div class="col-sm-6 quick_form"><?=$data['quick_form'];?></div>
    <div class="col-sm-3 text-right">
      <a href="<?=$this->lang_prefix;?>/admin<?=$this->component['path'];?>create_client/" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-plus"></span> Создать клиента
      </a>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="clearfix">
    <a class="btn btn-default btn-xs pull-left" href="/admin<?=$this->component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
    <? if(isset($data['form'])) {?>
      <a  class="btn btn-default btn-xs pull-right" href="/admin<?=$this->component['path'];?>clients_report/">Очистить параметры</a>
    <? } ?>
  </div><br/>
  <?=(isset($data['form']) ? $data['form'] : '');?>
  <div id="ajax_result">
    {{cmp:clients->_render_clients_report_table<-<?=base64_encode(serialize($data));?>}}
  </div>
  <a class="btn btn-default btn-xs" href="/admin<?=$this->component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>