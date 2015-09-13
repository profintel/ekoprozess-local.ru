<div class="block-title row">
  <div class="col-sm-9">
    <h1><span class="glyphicon <?=($this->component['icon']?$this->component['icon']:'glyphicon-ok');?>"></span>
      <?=(@$data['title'] ? $data['title'] : $this->component['title']);?>
    </h1>
    <p class="visible-xs-block">&nbsp;</p>
  </div>
  <div class="col-sm-3 text-right">
    <? if (isset($block_title_btn) && $block_title_btn) { ?>
      <?=$block_title_btn;?>
    <? } ?>
  </div>
</div>

<div class="container-fluid">
  <a class="btn btn-default btn-xs " href="<?=(isset($back) ? $back : $_lang_prefix .'/admin'. $_component['path']);?>"><span class="glyphicon glyphicon-backward"></span> Назад</a><br/><br/>

  <?=$html;?>

  <a class="btn btn-default btn-xs " href="<?=(isset($back) ? $back : $_lang_prefix .'/admin'. $_component['path']);?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>