<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    <?=$title;?>
  </h1>
</div>

<div class="container-fluid">
  <?=$html;?>

  <a class="btn btn-default btn-xs " href="<?=(isset($back) ? $back : $_lang_prefix .'/admin'. $_component['path']);?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>