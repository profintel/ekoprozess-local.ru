function togglePanel(action){
  if(action == 'open'){
    $('#admin_panel').removeClass('closed');
    $.cookie('admin_panel', 'open', { path: '/' });
  } else {
    $('#admin_panel').addClass('closed');
    $.cookie('admin_panel', 'closed', { path: '/' });
  }
}