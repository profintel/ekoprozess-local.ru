<? if ($pages) { ?>
  <ul class="pagination">
		<li class="pages green">Страницы: </li>
		<? if ($page == 1) { ?>
      <li class="pagination_left_off"></li>
    <? } else { ?>
      <li class="pagination_left"><a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . ($page - 1) . (isset($postfix) ? $postfix : '');?>"></a></li>
    <? } ?>
    <? foreach ($pages as $page_num => $page_type) { ?>
      <li class="pagination_item">
      <?
      switch ($page_type) {
        case 0:
      ?>
          <?=$page_num;?>
      <?
        break;
        case 1:
      ?>
        <? if (isset($postfix) && $postfix) { ?>
          <a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . $page_num . (isset($postfix) ? $postfix : '');?>"><?=$page_num;?></a>
        <? } else { ?>
          <a href="<?=(isset($prefix) ? $prefix : '/') . ($page_num > 1 ? '?page='.$page_num : '') ;?>"><?=$page_num;?></a>
        <? } ?>
      <?
        break;
        case 2:
      ?>
          <a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . $page_num . (isset($postfix) ? $postfix : '');?>">...</a>
      <?
        break;
      }
      ?>
      </li>
    <? } ?>
    <? end($pages); ?>
    <? if ($page == key($pages)) { ?>
      <li class="pagination_right_off"></li>
    <? } else { ?>
      <li class="pagination_right"><a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . ($page + 1) . (isset($postfix) ? $postfix : '');?>"></a></li>
    <? } ?>
  </ul>
<? } ?>