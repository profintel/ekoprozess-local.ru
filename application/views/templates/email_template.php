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
        <table width="614" border="0" cellspacing="0" cellpadding="2" bgcolor="#197db1">
          <tr>
            <td>        
              <table width="614" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                <tr>
                  <td>
                    <table width="610" border="0" cellspacing="17" cellpadding="0">
                      <tr>
                        <td>
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td style="font-family: Arial; font-size:10px; line-height:12px; text-align:center; color:#888888;">
                              </td>                            
                            </tr>
                            <tr>
                              <td bgcolor="" >
                                <table width="100%" border="0" cellspacing="0" cellpadding="10">
                                  <tr>
                                    <td align="left" width="230" >
                                      <a href="http://<?=$domain;?>/"><img src="http://<?=$domain;?>/images/logo.png" border="0" alt="" /></a>
                                    </td>
                                    <td style="color:#197db1; font-size:11px; font-weight:bold; font-family:Arial; text-transform:uppercase; font-style:italic;   " align="right"><?=date("d.m.Y H:i:s");?></td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td style="font-size:0; line-height:0;"></td>
                            </tr>
                            
                            <tr>
                              <td>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td valign="top">
                                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td style="font-family: Arial; font-size:14px; line-height:19px; text-align:left; color:#666;">
                                            <br /><div style="font-family: Arial; color:#666666; font-weight:normal; text-align:left; ">
                                              <?=stripslashes(str_replace('\n', "\n", $content));?>
                                            </div>
                                            <p style="margin-top:50px">
                                              <hr/>
                                              <small><a href="http://<?=$domain;?>/"><?=$domain;?></a></small>
                                            </p>                                            
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            
                          </table>
                        </td>
                      </tr>
                      
                    </table>
                  </td>
                </tr>              
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center">
        <table width="614" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><br/></td>
          </tr>
          <tr style="font-family: Arial; font-size:11px; color:#888888;">
            <td align="center" valign="middle" width="">
              &nbsp;Данное письмо сформировано автоматически и не предполагает ответа.
            </td>
          </tr>
          <tr>
            <td><br/></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
</body>
</html>
