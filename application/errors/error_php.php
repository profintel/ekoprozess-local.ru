<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Ошибка PHP</title>
  <link rel="stylesheet" type="text/css" href="/adm/css/main.css" />
</head>
<body class="error_body">
	<div class="error_container">
    <h1>Обнаружена ошибка PHP</h1>
    <p>Важность: <?php echo $severity; ?></p>
    <p>Текст ошибки:  <?php echo $message; ?></p>
    <p>Файл: <?php echo $filepath; ?></p>
    <p>Номер строки: <?php echo $line; ?></p>
	</div>
</body>
</html>