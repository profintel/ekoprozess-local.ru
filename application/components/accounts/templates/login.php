<? if ($_user) { header('Location: '. $this->lang_prefix . '/cabinet/'); ?>
<? } else { ?>
  <div id="" class="content_inner">
    <h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>
     
    {{cmp:forms->render<-autorization}}
  </div>
<? } ?>