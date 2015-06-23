<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Ошибка PHP</title>
  <link rel="stylesheet" type="text/css" href="/components/tp_admin.css?<?=time();?>" />
</head>
<body class="gray-bg">
  <div id="wrapper">
    <div class="well-lg">
      <h1>Обнаружена ошибка PHP</h1>
      <p>Важность: <?php echo $severity; ?></p>
      <p>Текст ошибки:  <?php echo $message; ?></p>
      <p>Файл: <?php echo $filepath; ?></p>
      <p>Номер строки: <?php echo $line; ?></p>
    </div>
  </div>
</body>
</html>