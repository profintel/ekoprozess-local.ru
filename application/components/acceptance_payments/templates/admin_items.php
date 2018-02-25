<div class="container-fluid padding_0">
  <div class="block-title hidden-print">
    <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
      <?=(@$title ? $title : $_component['title']);?>
    </h1>
    <p class="visible-xs-block">&nbsp;</p>
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

<div class="modal fade" id="acceptancePaymentEditModal" tabindex="-1" role="dialog" aria-labelledby="acceptancePaymentEditModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        Загрузка ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>