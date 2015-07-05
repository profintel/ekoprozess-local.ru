<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    <?=$title;?>
  </h1>
</div>

<div class="container-fluid">
  <div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
      <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
      <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a></li>
      <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="home">...</div>
      <div role="tabpanel" class="tab-pane" id="profile">...</div>
      <div role="tabpanel" class="tab-pane" id="messages">...</div>
      <div role="tabpanel" class="tab-pane" id="settings">...</div>
    </div>

  </div>
  
  <a class="btn btn-default btn-xs " href="<?=(isset($back) ? $back : $_lang_prefix .'/admin'. $_component['path']);?>"><span class="glyphicon glyphicon-backward"></span> Назад</a><br/><br/>

  <?=$html;?>

  <a class="btn btn-default btn-xs " href="<?=(isset($back) ? $back : $_lang_prefix .'/admin'. $_component['path']);?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>