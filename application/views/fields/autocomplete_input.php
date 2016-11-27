<script>
  function autocomplete_search(request, response, input_obj) {  
    if (request.term.length) {
      $('#item_id').val(request.term);
      input_obj.addClass('loading');
      $.post('/admin/'+ input_obj.data('component') +'/'+ input_obj.data('method') +'/', {search_string: request.term}, function(result) {     
          input_obj.removeClass('loading');
          if (typeof(result.items) == 'object' && !$.isEmptyObject(result.items)) {
            response($.map(result.items, function(item) {
              return {label: item.title, value: item.id, 'location': item.location};
            }));
          } else {
            response([]);
          }
        }, 'json');
    }
  }
</script>
<div class="form-group <?=(isset($vars['class']) && $vars['class'] ? $vars['class'] : '');?>">
  <div class="col-sm-2">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <label class="control-label">
        <? if (isset($vars['icon'])) { ?>
          <img src="<?=$vars['icon'];?>" class="icon" />
        <? } ?>
        
        <?=$vars['title'];?>
        
        <? if (isset($vars['req']) && $vars['req']) { ?>
          <span class="red"> *</span>
        <? } ?>
      </label>
    <? } ?>
    
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <div class="help-block"><?=$vars['description'];?></div>
    <? } ?>
  </div>
  
  <div class="col-sm-10">
    <input type="text" id="<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string" 
    class="form-control input-sm autocomplete_search_string dark" 
    <?=(isset($vars['value']) ? "value='". $vars['value'] ."'" : '');?>
    data-name ="<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>"
    data-component ="<?=(isset($vars['component']) && $vars['component'] ? $vars['component'] : '');?>"
    data-method ="<?=(isset($vars['method']) && $vars['method'] ? $vars['method'] : '');?>" />
    <script>
      $(document).ready(function() {
        $("#<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string").autocomplete({
          delay: 500,
          source: function(request, response) {
            var result = autocomplete_search(request, response, $("#<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string"));
            return result;
          },
          select: function(event, ui) { 
            if (ui.item) {
              if (typeof(ui.item.location) != 'undefined') {
                document.location = ui.item.location;
              }
              $('#item_id').val(ui.item.value);
              $('#<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>_search_string').val(ui.item.label);
              return false;
            }
          },
          focus: function() { return false; }
          });
      });
    </script>
    <input type="hidden" 
      name="<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : '');?>" 
      value="<?=(isset($vars['value']) ? $vars['value'] : '');?>" id="item_id">
  </div>
  
  <div class="clear"></div>
</div>