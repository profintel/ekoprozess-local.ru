<div class="container-fluid padding_0">
  <div class="block-title row">
    <div class="<?=(isset($block_title_btn) && $block_title_btn ? 'col-sm-4' : 'col-xs-12');?>">
      <h1><span class="glyphicon <?=($this->component['icon']?$this->component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$title ? $title : $this->component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <? if (isset($block_title_btn) && $block_title_btn) { ?>
      <div class="col-sm-8 text-right">
          <? if (!is_array($block_title_btn)) { ?>
            <?$block_title_btn = array($block_title_btn);?>
          <? } ?>
          <div class="btn-group btn-block row">
            <? foreach ($block_title_btn as $key => $btn) { ?>
              <div class="clearfix m-t m-b p-l-0 col-sm-<?=round(12/count($block_title_btn))*2;?> col-md-<?=round(12/count($block_title_btn));?>"><?=$btn;?></div>
            <? } ?>
          </div>
      </div>
    <? } ?>
  </div>
</div>
<div class="container-fluid">
  <a class="btn btn-default btn-xs" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a><br/><br/>
  <? if ($emails) {?>
    <table class="table table-hover panel table-sm">
      <thead>
        <tr>
          <th colspan="3"><h5>История отправленных писем</h5></th>
          <th class="text-right" colspan="2">
            <a href="#" 
              onClick="return send_confirm(
                  'Вы уверены, что хотите удалить историю?',
                  '/admin/acceptance_payments/delete_acceptances_payments_emails/',
                  {},
                  'reload'
                );"
                title="Удалить"
                class="btn btn-default btn-xs" 
              ><span class="glyphicon glyphicon-trash"></span> Удалить историю</a>
           </th>
        </tr>
        <tr>
          <th>Дата</th>
          <th>Администратор</th>
          <th>Email отправителя</th>
          <th>Email получателя</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <? foreach ($emails as $value) { ?>
          <tr class="panel selection">
            <td><?=rus_date($value['tm'],'d m Yг. H:i');?></td>
            <td><?=$value['username'];?></td>
            <td><?=htmlspecialchars($value['from']);?></td>
            <td><?=htmlspecialchars($value['to']);?></td>
            <td>
              <a href="javascript:void(0)" class="" title="Ссылки"
                onClick="return my_modal('modal-w100', 'Текст письма', '<?=htmlspecialchars(str_replace(array("\r\n", "\r", "\n"), "", $value['message']));?>',[{text: 'OK', handler: function() {my_modal('hide');}, icon: 'accept'}]);">
                Текст письма
              </a>
            </td>
          </tr>
        <? } ?>
      </tbody>
    </table>
  <?}?>
  <form class="form-horizontal " action="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>_send_acceptances_payment_email/" method="POST" target="_self" enctype="multipart/form-data" onsubmit="return false;" style="" encoding="multipart/form-data">
    <div class="form-horizontal panel">
      <div class="form_block panel-body">
        <?=$html;?>
        <a href="#" id="submitAcceptanceEmail" class="btn btn-primary btn-xs pull-right" onclick="return submit_form(this, 'reload', null);">
          <span class="glyphicon glyphicon-save"></span>  Отправить
        </a>
      </div>
    </div>
  </form>
  <a class="btn btn-default btn-xs" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>