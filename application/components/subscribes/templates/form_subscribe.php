<div class="subscribe">
  <span>Подписка на рассылки</span>
  <form action="/component/subscribes/email_sign/" method="post" enctype="multipart/form-data" onSubmit="return false;">	
    <input class="subscribe_email"  type="text" name="email" maxlength="256" value="" />	
    <input TYPE="submit" class="" onClick="return submit_form(this, 'reload');" VALUE="Подписаться">
  </form>		
  <div class="additional_form_replace_text">
    <br/>Email успешно добавлен в список подписчиков.
  </div>  
</div>
<div id="sheet_loading" style="display: none;"></div>
<div id="sheet" style="display: none;"></div>
<div id="modal">
  <div class="title"></div>
  <div class="inner"></div>
  <div class="buttons"></div>
</div>