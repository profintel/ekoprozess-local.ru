<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Ошибка</title>
  <link rel="stylesheet" type="text/css" href="/components/tp_admin.css?<?=time();?>" />
</head>
<body class="gray-bg">
  <div id="wrapper">
    <nav class="navbar-default border-bottom">
      <div class="collapse navbar-collapse">
        <ul class="nav navbar-right">
          <li>
            <a href="/autorization/close/" class="text-white"><span class="glyphicon glyphicon-log-out"></span> Сменить аккаунт</a>
          </li>
        </ul>
      </div>
    </nav>
    <div class="well-lg">
      <h1>Обнаружена ошибка</h1>
      <?php echo $message; ?>
    </div>
  </div>
</body>
</html>