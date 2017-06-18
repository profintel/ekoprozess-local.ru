<div class="block-title row hidden-print">
  <div class="<?=(isset($block_title_btn) && $block_title_btn ? 'col-sm-4' : 'col-xs-12');?>">
    <h1><span class="glyphicon <?=($this->component['icon']?$this->component['icon']:'glyphicon-ok');?>"></span>
      <?=(@$data['title'] ? $data['title'] : $this->component['title']);?>
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

<div class="container-fluid">
  <a class="btn btn-default btn-xs hidden-print" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a><br/><br/>

  <?=$html;?>

  <a class="btn btn-default btn-xs hidden-print" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>