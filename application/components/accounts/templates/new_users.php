<div class="new_users">
  <div class="title">New users</div>
  <div class="">
  <? foreach ($users as $user) { ?>
    <? if (isset($user['params']['file_photo']) && $user['params']['file_photo']) { ?>
      <a href="/designer?id=<?=$user['id'];?>" ><img src="<?=thumb($user['params']['file_photo'],180,135);?>" /></a>
    <? } ?>
  <? } ?>
  </div>
</div>