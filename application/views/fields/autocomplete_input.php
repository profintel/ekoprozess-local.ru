<script>  
  $(function() {
    $('#search_string,.autocomplete_search_string').focus(function() {
      if ($(this).val() == 'Поиск') {
        $(this).removeClass('def');
        $(this).val('');
      }
    });
    $('#search_string,.autocomplete_search_string').blur(function() {
      if ($(this).val() == '') {
        $(this).addClass('def');
        $(this).val('Поиск');
      }
    });
  });


  function autocomplete_search(request, response, input_obj) {  
    if (request.term.length) {
      input_obj.addClass('loading');
      $.post('/admin/'+ input_obj.data('component') +'/'+ input_obj.data('method') +'/', {search_string: request.term}, function(result) {     
        input_obj.removeClass('loading');
        if (typeof(result.items) != 'undefined' && result.items.length) {
          response($.map(result.items, function(item) {
            return {label: item.title, value: item.id};
          }));
        }
      }, 'json');
    }
  }
</script>
<div class="input_block">
  <div class="name">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <div class="title">
        <? if (isset($vars['icon'])) { ?>
          <img src="<?=$vars['icon'];?>" class="icon" />
        <? } ?>
        
        <?=$vars['title'];?>
        
        <? if (isset($vars['req']) && $vars['req']) { ?>
          <span class="red"> *</span>
        <? } ?>
      </div>
    <? } ?>
    
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <div class="description"><?=$vars['description'];?></div>
    <? } ?>
  </div>
  
  <div class="input">
    <input type="text" id="<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string" class="autocomplete_search_string dark" value="Поиск" 
    data-name ="<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>"
    data-component ="<?=(isset($vars['component']) && $vars['component'] ? $vars['component'] : '');?>"
    data-method ="<?=(isset($vars['method']) && $vars['method'] ? $vars['method'] : '');?>" />
    <script>
      $(document).ready(function() {
        $("#<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string").autocomplete({
          delay: 500,
          source: function(request, response) {
            return autocomplete_search(request, response, $("#<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string"));
          },
          select: function(event, ui) { 
            if (ui.item) { 
              $('#item_id').val(ui.item.value);
              $('#<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string').val(ui.item.label);
              return false; 
            } 
          },
          focus: function() { return false; }
          });
      });
    </script>
    <input type="hidden" name="<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>" value="" id="item_id">
  </div>
  
  <div class="clear"></div>
</div>