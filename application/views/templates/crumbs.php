<? if (isset($_page['crumbs']) && count($_page['crumbs']) > 1) { ?>
  <ul class="breadcrumb">
    <? foreach ($_page['crumbs'] as $num => $crumb) { ?>
      <li>
        <?=($num!=0 ? '<span class="divider">/</span>' : '');?>
        <a href="<?=$crumb['path'];?>"><?=$crumb['title'];?></a>
      </li>
    <? } ?>
  </ul>
<? } ?>