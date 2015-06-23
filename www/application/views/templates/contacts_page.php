      <div class="container">
        <script type="text/javascript" charset="utf-8" src="//api-maps.yandex.ru/services/constructor/1.0/js/?sid=up_9BivQf8fN-T9bQDtpz259NcKL2J25&width=100%&height=450"></script>
        <h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>
        <?=$_page['params']['content_'.$_language];?>
          <hr>
          <h2 class="feedback_title">Задать вопрос</h2>
          <form action="/component/forms/processing_form/feedback/" method="POST" target="_self" enctype="multipart/form-data" onsubmit="return false;">
              <div class="clearfix" id="question">
                  <div class="col-xs-6 padding-0">
                     <input type="text" class="form-control default-generated" name="name" placeholder="Имя">
                     <input type="text" class="form-control default-generated" name="phone" placeholder="Иелефон">
                     <input type="text" class="form-control default-generated" name="email" placeholder="Email">
                  </div>
                  <div class="col-xs-6">
                     <textarea class="form-control default-generated" rows="3" placeholder="Ввести текст..." name="comment"></textarea>
                     <a href="#" id="submit" class="pull-right button btn default-generated" onclick="return submit_form(this, 'reload', 'alert');">ОТПРАВИТЬ</a>
                  </div>
              </div>
          </form>
      </div>