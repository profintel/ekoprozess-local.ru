<h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>
<p>
  На указанный номер была отправлена sms с кодом подтверждения. <br/> Введите код и Ваш аккаунт активируется. 
</p>
<div id="form_site">
  <form action="/component/accounts/activation_account/" method="POST" enctype="multipart/form-data" onSubmit="return false;">
    <div class="form_block">
      <div class="name">
        <div class="title"><br/>Код подтверждения</div>
      </div>
      <div class="input">
        <input class="" type="hidden" value="<?=$user['id'];?>" name="user_id">
        <input class="default-generated" type="text" value="" name="confirmation_code">
      </div>
      <br/>
      <div class="button_yellow" ><div class="right" ><div class="left">
        <a id="submit" onclick="return submit_form(this, '/cabinet/', 'alert');" href="#"> Активировать аккаунт </a>
      </div></div></div>

      <div class="clear"></div>       
    </div>
  </form>
  
  <div class="clear"></div>
</div>