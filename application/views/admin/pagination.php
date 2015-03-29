<nav>
  <? if ($pages) { ?>
    <ul class="pagination">
      <li class="<?=($page != 1 ?: 'disabled');?>"><a href="<?=(isset($prefix) ? $prefix : '/') . ($page - 1) . (isset($postfix) ? $postfix : '/');?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
      <? foreach ($pages as $page_num => $page_type) { ?>       
        <?
        switch ($page_type) {
          case 0:
        ?>
            <li class="active"><a href="#"><?=$page_num;?></a></li>
        <?
          break;
          case 1:
        ?>
            <li><a href="<?=(isset($prefix) ? $prefix : '/') . $page_num . (isset($postfix) ? $postfix : '/');?>"><?=$page_num;?></a></li>
        <?
          break;
          case 2:
        ?>
            <li><a href="<?=(isset($prefix) ? $prefix : '/') . $page_num . (isset($postfix) ? $postfix : '/');?>">...</a></li>
        <?
          break;
        }
        ?>        
      <? } ?>
      <? end($pages); ?>
      <li class="<?=($page != key($pages) ?: 'disabled');?>"><a href="<?=(isset($prefix) ? $prefix : '/') . ($page + 1) . (isset($postfix) ? $postfix : '/')?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
    </ul>
  <? } ?>
</nav>