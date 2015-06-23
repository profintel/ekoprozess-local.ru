<h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>

<?=$_page['params']['content_'.$_language];?>
<div id="form_site">
  <form action="/component/accounts/activation_account_phone/" method="POST" enctype="multipart/form-data" onSubmit="return false;">
    <div class="form_block">
      <div class="name">
        <div class="title"><br/>Номер телефона</div>
      </div>
      <div class="input">
        <input class="" type="hidden" value="<?=$user['id'];?>" name="user_id">
        <input class="default-generated" type="text" value="+7" name="user_phone">
      </div>
      <br/>
      <div class="button_yellow" ><div class="right" ><div class="left">
        <a id="submit" onclick="return submit_form(this, '<?=$_page['path'];?>?type=<?=md5('confirmation_code');?>&id=<?=md5($user['id']);?>', 'alert');" href="#"> Получить код </a>
      </div></div></div>

      <div class="clear"></div>       
    </div>
  </form>
  
  <div class="clear"></div>
</div>