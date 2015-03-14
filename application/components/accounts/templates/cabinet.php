<a name="label"></a>
<div class="content_inner cabinet">
  <div class="cabinet_menu">
    <div class="right">
      <div class="bg">
        <a href="<?=$_page['path'];?>#label" class="float_l"> Мой профиль </a>
        <a href="<?=$_page['path'];?>?type=big_game#label" class="float_l"> Большая игра </a>
        <a href="<?=$_page['path'];?>?type=instant_play#label" class="float_l"> Мгновенная игра </a>
        <a href="<?=$_page['path'];?>?type=quiz#label"> Викторина </a>
        <a href="/component/accounts/logout/" class="float_r"> Выход </a> 
      </div>
    </div>
  </div>
  <div class="balance float_r">
    <h2>Мой баланс</h2>
    <h2><?=($_user['balance']);?> руб.</h2>
    <a href="<?=$_page['path'];?>?type=fill_balance#label" class=""> Пополнить </a>    
  </div>
  <div class="my_main_data float_l">
    <h2><?=(isset($_user['params']['name']) && $_user['params']['name'] ? $_user['params']['name'] : 'Профиль аккаунта');?></h2>
    email:&nbsp;<?=($_user['username']);?>, тел.&nbsp;<?=($_user['phone']);?>
  </div>
  <div class="clear"></div>

  <br/>

  <div class="block">
    <div class="top"><div class="right"><div class="left"></div></div></div>
    <div class="middle">     
        <? switch ($type) {
          case 'edit_profile':
        ?>              
          <h1>Редактирование личных данных</h1>
          <form class="form" action="/component/accounts/edit_profile/" method="POST" enctype="multipart/form-data" onSubmit="return false;">
            <? foreach ($user_params as $user_param) { ?>
              <? switch(substr($user_param['system_name'],0,4)) { 
                  case 'desc':
              ?>
                  <div class="title"><?=$user_param['params']['name_'.$_language];?></div>
                  <div class="input"><textarea name="<?=$user_param['system_name'];?>"><?=$_user['params'][$user_param['system_name']];?></textarea></div>
              <?
                  break;
                  case 'file':
              ?>
                  <div class="title"><?=$user_param['params']['name_'.$_language];?></div>
                  <? if (isset($_user['params'][$user_param['system_name']]) && $_user['params'][$user_param['system_name']]) { ?>
                    <div class="input_file_value">
                      <a href="<?=$_user['params'][$user_param['system_name']];?>" target="_blank"><?=$_user['params'][$user_param['system_name']];?></a>
                      <input type="checkbox" id="<?=$user_param['system_name'];?>_delete" name="<?=$user_param['system_name'];?>_delete" />
                      <label for="<?=$user_param['system_name'];?>_delete" class="red">удалить</label>
                    </div>
                  <? } ?>                      
                  <div class="input"><input name="<?=$user_param['system_name'];?>" type="file" ></div>
              <?
                  break;
                  default:
              ?>
                  <div class="title"><?=$user_param['params']['name_'.$_language];?></div>
                  <div class="input"><input name="<?=$user_param['system_name'];?>" type="text" value="<?=$_user['params'][$user_param['system_name']];?>"></div>
              <?
                  break;
                } 
              ?>
            <? } ?>
            <div class="clear"></div> 
            <div class="submit"><br/><input type="submit" value="Сохранить изменения" onClick="return submit_form(this, 'reload', 'alert');"></div>
          </form>
        <? 
          break; 
          case 'fill_balance': 
        ?>
            <h1>Пополнение баланса</h1>
            <div class="error"><?=($error ? $error : '');?></div>
            <form class="form" action="/component/accounts/fill_balance/" method="POST" enctype="multipart/form-data">
              <div class="title">Сумма пополнения</div>
              <div class="input"><input name="sum" type="text" value="" maxlength="10"></div>
              <div class="clear"></div> 
              <div class="submit"><br/><input type="submit" value="Продолжить" ></div>
            </form>
        <? 
          break; 
          case 'account':           
            // регистрационная информация (логин, пароль #1)
            $mrh_login = "15378.mesto.biz";
            $mrh_pass1 = "fgtkmcbyjdsqujhj[1";
            // номер заказа
            $inv_id = (int)$account['id'];
            // описание заказа
            $inv_desc = "Пополнение счета";
            // сумма заказа
            $out_summ = $account['sum'];
            // тип товара
            //$shp_item = 1;
            // предлагаемая валюта платежа
            $in_curr = "AlfaBankR";
            // язык
            $culture = "ru";
            // кодировка
            $encoding = "utf-8";
            // формирование подписи
            $crc  = md5($mrh_login.":".$out_summ.":".$inv_id.":".$mrh_pass1);		
        ?>
            <table class="table_cabinet" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr class="tr_title">
                <th class="name">Плательщик</th>
                <th class="num_order">№ Заказа</th>
                <th class="sum">Сумма (руб.)</th>
                <th class="comment">Описание</th>
              </tr>
              <tr class="">
                <td class="name"><?=$_user['params']['name'];?>, <?=$_user['username'];?></td>
                <td class="num_order"><?=$account['id'];?></td>
                <td class="sum"><?=$account['sum'];?></td>
                <td class="comment">Пополнение счета пользователя.</td>
              </tr>
            </table>			
            <br/><br/>
            <!--<script language='javascript' type='text/javascript' src='https://merchant.roboxchange.com/Handler/MrchSumPreview.ashx?MrchLogin=<?=$mrh_login;?>&OutSum=<?=$out_summ;?>&InvId=<?=$inv_id;?>&SignatureValue=<?=$crc;?>&Culture=<?=$culture;?>&IncCurrLabel=<?=$in_curr;?>&Encoding=<?=$encoding;?>'></script>			-->		
            
            <form class="" method="get" action="http://test.robokassa.ru/Index.aspx">
              <input type="hidden" name="MrchLogin" value="<?=$mrh_login;?>" />
              <input type="hidden" name="OutSum" value="<?=$out_summ;?>" />
              <input type="hidden" name="InvId" value="<?=$inv_id;?>" />
              <input type="hidden" name="SignatureValue" value="<?=$crc;?>" />
              <div class="float_r"><br/><input type="submit" value="Оплатить" ></div>
            </form>	  
        <? 
          break; 
          case 'quiz':           
        ?>
            <? if ($game_contest_id) { ?>   
              <div class="cabinet_quiz">
                <? if ($pr_game_contest_users) { ?>
                  <div class="content_quiz">
                    <div class="ticket quiz main">
                      <div class="top"><div class="right"><div class="left"></div></div></div>
                      <div class="middle">            
                        <div class="points"><div class="num"><?=($all_points_current_game ? $all_points_current_game : 0);?></div>баллов</div>
                        <div class="logo">
                          <img class="" src="/images/tickets/quiz/logo.png" />
                          <div><?=(date('d.m.Yг.',strtotime($game_contest['tm_start'])));?> c <?=(date('H:i:s', strtotime($game_contest['tm_start'])));?> по <?=(date('H:i:s',strtotime($game_contest['tm_end'])));?></div>
                        </div>
                        <div class="place">Место <div class="place_bg"><?=(isset($place) ? $place : '')?></div> среди играющих</div>
                        <div class="clear"></div>
                      </div>
                      <div class="bottom"><div class="right"><div class="left"></div></div></div>
                    </div>
                  </div>  
                  <br/>
                  <? if (date('Y-m-d H:i:s', strtotime($game_contest['tm_end'])) < date('Y-m-d H:i:s')) { ?>                
                    <? if ($quiz_status && $quiz_status['execution'] == 1) { ?>
                      <h2>Викторина завершена. Выигрыши распределены по пользователям, занявшим призовые места.</h2>                    
                      <? if ($pr_game_contest_users['sum_prize'] && $pr_game_contest_users['active_prize'] == 1) { ?>
                        Ваш выигрыш составляет <span class="prize"><?=$pr_game_contest_users['sum_prize'];?> руб.</span> (средства переведены на Ваш личный счет)                    
                      <? } elseif($all_points_current_game) { ?>
                        К сожалению в этой игре Вы не заняли призовое место.
                      <? } else { ?>
                        К сожалению в этой игре Вы не набрали ни одного балла и не можете претендовать на призовые места.
                      <? } ?>
                    <? } else { ?>
                      <h2>Викторина завершена. Розыгрыш призов еще не проведен.</h2>
                    <? } ?>
                  <? } else { ?>
                    Викторина еще продолжается. <a href="/viktorina/">Перейти к игре</a>
                  <? } ?>
                <? } else { ?>
                  <h2>Вы не учавствовали в данной викторине.</h2>
                <? } ?>
                <a class="back" href="<?=$_page['path'];?>?type=quiz#label"> Назад к списку </a>
              </div>
            <? } else { ?>
              <h2>Список викторин, в которых учавствовал аккаунт:</h2>
              <table class="table_cabinet" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr class="tr_title">
                  <th class="">№</th>
                  <th class="">Дата</th>
                  <th class="">Время начала</th>
                  <th class="">Время окончания</th>
                  <th class="">К-во баллов</th>
                  <th class="">Выигрыш</th>
                  <th class=""></th>
                </tr>
                <? foreach ($game_contests_user as $game_contest) { ?>
                  <tr class="">
                    <td class="" align="center"><a href="<?=$_page['path'];?>?type=quiz&id=<?=$game_contest['id'];?>#label"><?=$game_contest['id'];?></td>
                    <td class="" align="center"><?=rus_date($game_contest['tm_start'],'d m Y');?></td>
                    <td class="" align="center"><?=date('H:i:s',strtotime($game_contest['tm_start']));?></td>
                    <td class="" align="center"><?=date('H:i:s',strtotime($game_contest['tm_end']));?></td>
                    <td class="" align="center"><?=$game_contest['points'];?></td>
                    <td class="" align="center"><?=$game_contest['sum_prize'];?></td>
                    <td class="" align="center"><a href="<?=$_page['path'];?>?type=quiz&id=<?=$game_contest['id'];?>#label"><img src="/adm/images/icons/door_in_s.png" /></a></td>
                  </tr>
                <? } ?>
              </table>	
            <? } ?>
        <? 
          break; 
          case 'big_game': 
        ?>
          <div class="ticket big_game main">
            <div class="top"><div class="right"><div class="left"></div></div></div>
            <div class="middle">
              <div class="ticket_left">
                <div class="clover logo">
                  <img class="logo" src="/images/tickets/big_game/logo.png" />
                </div>
                <div class="clear"></div>
                <div class="button_yellow"><div class="right"><div class="left">
                  Купить билеты
                </div></div></div>
              </div>
              <div class="ticket_right">
                <table class="ticket" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td>Количество купленных билетов</td>
                    <td class="num">2</td>
                  </tr>
                  <tr>
                    <td>Джекпот (руб.)</td>
                    <td class="num">3' 000' 122</td>
                  </tr>
                  <tr>
                    <td>Сумма выигрыша (руб.)</td>
                    <td class="num">10' 254</td>
                  </tr>
                </table>
                <br/><br/>
                <div>
                  <a href="">Правила выигрыша</a>
                  <a href="">Как выигрывать?</a>
                  <a href="">Выплаты</a>
                </div>
              </div>
              <div class="clear"></div>
            </div>
            <div class="bottom"><div class="right"><div class="left"></div></div></div>
          </div>
          <div>Список купленных билетов (2) на сумму 2000 рублей</div><br/>
          <div class="ticket big_game">
            <div class="top"><div class="right"><div class="left"></div></div></div>
            <div class="middle">
                <div class="article">ПМ 123847</div>
                <div class="">
                  <div class="raffle_tickets">
                    <div class="num">999</div>
                    <div class="">розыгрыш</div>
                    <div class="date">04.05.2012г.</div>
                  </div>
                  <div class="clover logo">
                    <img class="logo" src="/images/tickets/big_game/logo.png" />
                  </div>
                  <div class="clear"></div>
                </div>
                <div class="price">Стоимость билета 1000 руб.</div>
              <div class="clear"></div>
            </div>
            <div class="bottom"><div class="right"><div class="left"></div></div></div>
          </div>
          <div class="ticket big_game ">
            <div class="top"><div class="right"><div class="left"></div></div></div>
            <div class="middle">
              <div class="win">
                <div class="article">АМ 05487454</div>
                <div class="">
                  <div class="raffle_tickets">
                    <div class="num">998</div>
                    <div class="">розыгрыш</div>
                    <div class="date">03.05.2012г.</div>
                  </div>
                  <div class="clover logo">
                    <img class="logo" src="/images/tickets/big_game/logo.png" />
                  </div>
                  <div class="sum_win">
                    <div class="compl">ПОЗДРАВЛЯЕМ !</div>
                    <div class="sum">10'256</div>
                    <div class="txt">ваш выигрыш</div>
                  </div>
                  <div class="clear"></div>
                </div>
                <div class="price">Стоимость билета 1000 руб.</div>
              <div class="clear"></div>
              </div>
            </div>
            <div class="bottom"><div class="right"><div class="left"></div></div></div>
          </div>
        <? 
          break; 
          default: 
        ?>  
            <div class="profile">
              <h2>Личные данные</h2>
              <? foreach ($user_params as $user_param) { ?>             
                <? if ($user_param['system_name'] == 'company_name' || substr($user_param['system_name'],0,4) == 'file')  { continue; }?> 
                <div class="title float_l"><?=$user_param['params']['name_'.$_language];?></div>
                <div class=""><?=$_user['params'][$user_param['system_name']];?></div>
                <div class="clear"></div>
              <? } ?>
              <br/><br/>
              <h2>Игры, в которых принимал участие аккаунт</h2>  
              <div class="account_games">  
                <a href="<?=$_page['path'];?>?type=big_game#label"> Большая игра </a><br/>
                <a href="<?=$_page['path'];?>?type=instant_play#label"> Мгновенная игра </a><br/>
                <a href="<?=$_page['path'];?>?type=quiz#label"> Викторина </a><br/>
              </div>  
            </div>  
          <? 
          break;  
        } 
        ?>  
      <div class="clear"></div>   
    </div>
    <div class="bottom"><div class="right"><div class="left"></div></div></div>
  </div>

  <br/>
  
  <div class="button_yellow float_r" ><div class="right" ><div class="left">
    <a href="<?=$_page['path'];?>?type=edit_profile#label"> Редактировать профиль </a>
  </div></div></div>
  <div class="button_yellow float_r" ><div class="right" ><div class="left">    
    <a href="<?=$_page['path'];?>?type=fill_balance#label" class="float_l"> Пополнить баланс </a>
  </div></div></div>
 
  <div class="clear"></div> 
</div>