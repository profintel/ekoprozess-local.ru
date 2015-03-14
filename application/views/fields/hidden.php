<input type="hidden"
  class="default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
  <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
  <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
  <?=(isset($vars['value']) ? "value='". $vars['value'] ."'" : '');?>
/>