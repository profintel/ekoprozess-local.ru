<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title><?=$title;?></title>
</head>
<body style="padding:0; margin:0;">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:40px;">
    <tr>
      <td align="center" valign="top">
        <table width="714" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>        
              <?=stripslashes(str_replace('\n', "\n", $content));?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
</body>
</html>
