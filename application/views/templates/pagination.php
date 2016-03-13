<? if ($pages) { ?>
<nav class="hidden-print">
  <ul class="pagination" id="<?=(isset($ajax) && $ajax ? 'pagination_ajax' : '');?>">
    <? if ($page == 1) { ?>
      <li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
    <? } else { ?>
      <li class=""><a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . ($page - 1) . (isset($postfix) ? '&'.$postfix : '');?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a></li>
    <? } ?>
    <? foreach ($pages as $page_num => $page_type) { ?>      
      <? switch ($page_type) {
        case 0:
      ?>
          <li class="active"><a href="#"><?=$page_num;?> <span class="sr-only">(current)</span></a></li>
      <?
        break;
        case 1:
      ?>
        <? if (isset($postfix) && $postfix) { ?>
          <li><a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . $page_num . (isset($postfix) ? '&'.$postfix : '');?>"><?=$page_num;?></a></li>
        <? } else { ?>
          <li><a href="<?=(isset($prefix) ? $prefix : '/') . ($page_num > 1 ? '?page='.$page_num : '') ;?>"><?=$page_num;?></a></li>
        <? } ?>
      <?
        break;
        case 2:
      ?>
          <li><a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . $page_num . (isset($postfix) ? '&'.$postfix : '');?>">...</a></li>
      <?
        break;
      } ?>
    <? } ?>
    <? end($pages); ?>
    <? if ($page == key($pages)) { ?>
      <li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
    <? } else { ?>
      <li class=""><a href="<?=(isset($prefix) ? $prefix : '/') . '?page=' . ($page + 1) . (isset($postfix) ? '&'.$postfix : '');?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a></li>
    <? } ?>    
  </ul>
</nav>
<div class="visible-print-block">
  <? foreach ($pages as $page_num => $page_type) { ?>      
    <? if ($page_type != 0) continue; ?>
    Страница <?=$page_num;?>
  <? } ?>
</div>
<? } ?>