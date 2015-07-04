<div class="container-fluid padding_0">
  <div class="block-title row">
    <div class="col-sm-3">
      <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$title ? $title : $_component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <div class="col-sm-6 quick_form"><?=$quick_form;?></div>
    <div class="col-sm-3 text-right">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_acceptance/" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-plus"></span> Создать акт приемки
      </a>
    </div>
  </div>
</div>
<div class="container-fluid">
  <? if ($items) { ?>
    <table class="table table-report table-hover table-bordered">
      <tr>
        <th>Дата</th>
        <th>Компания</th>
        <th>Брутто, кг</th>
        <th>Упаковка + засор, %</th>
        <th>Нетто, кг</th>
        <th>Цвет</th>
        <th>Цена</th>
        <th>Стоимость</th>
        <th></th>
      </tr>
      <? foreach ($items as $item) { ?>
        <tr onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <td><?=date('d.m.Y',strtotime($item['date']));?></td>
          <td><?=$item['client'];?></td>
          <td><?=$item['gross'];?></td>
          <td><?=$item['result'];?></td>
          <td><?=$item['net'];?></td>
          <td><?=$item['color'];?></td>
          <td><?=$item['price'];?></td>
          <td><?=$item['price']*$item['net'];?></td>
          <td><a href="#">Отправить акт по email</a></td>
        </tr>
      <? } ?>
    </table>
    <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <? } else { ?>
    <div class="alert text-warning">Не найдено ни одного акта приемки</div>
  <? } ?>
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>