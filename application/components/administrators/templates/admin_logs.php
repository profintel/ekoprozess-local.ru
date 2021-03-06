<div class="container-fluid padding_0">
  <div class="block-title hidden-print">
    <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
      <?=(@$title ? $title : $_component['title']);?>
    </h1>
  </div>
</div>
<div class="container-fluid">
  <div class="hidden-print">
    <div class="clearfix">
      <a class="btn btn-default btn-xs pull-left" href="/admin<?=$this->component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
      <a  class="btn btn-default btn-xs pull-right" href="/admin<?=$this->component['path'];?>logs/">Очистить параметры</a>   
    </div><br/>
    <?=$data['form'];?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover panel table-sm">
      <thead>
        <tr>
          <th>Дата</th>
          <th>Администратор</th>
          <th>Компонент</th>
          <th>Метод</th>
          <th>Описание метода</th>
          <th>Path</th>
          <th>POST</th>
        </tr>
      </thead>
      <tbody>
        <? foreach ($items as $item) { ?>
          <tr class="panel selection">
            <td><?=rus_date($item['tm'],'d m Yг. H:i');?></td>
            <td><?=$item['username'];?></td>
            <td><?=$item['component'];?></td>
            <td><?=$item['method'];?></td>
            <td><?=$item['title'];?></td>
            <td width="30%" style="word-break: break-all;"><?=$item['path'];?></td>
            <td>
              <? if ($item['post']) {?>
                <a href="javascript:void(0)" class="" title="Ссылки"
                  onClick="return my_modal('information', 'POST данные', '<?=htmlspecialchars(str_replace("\n", "<br/>", $item['post']));?>',[{text: 'OK', handler: function() {my_modal('hide');}, icon: 'accept'}]);">
                  POST
                </a>
              <? } ?>
            </td>
          </tr>
        <? } ?>
      </tbody>
    </table>
  </div>
</div>

<?=(isset($pagination) && $pagination ? $pagination : '');?>

<div class="clear"></div>