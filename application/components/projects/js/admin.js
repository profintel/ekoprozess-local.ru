var projects_page_slugging = true;

$(function() {
  if ($('#projects-page-alias').val() != slug($('#projects-page-title').val())) {
    projects_page_slugging = false;
  }
  
  $('#projects-page-title').keyup(function(event) {
    if (
      projects_page_slugging
      && [9, 13, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 144].indexOf(event.keyCode) == -1
    ) {
      $('#projects-page-alias').val(slug($(this).val()));
    }
  });
  
  $('#projects-page-alias').keyup(function() {
    if ($(this).val() != '') {
      projects_page_slugging = false;
    } else {
      projects_page_slugging = true;
    }
  });
  
  $('div.projects-page-container a.toggle').on('click', function() {
    if ($(this).hasClass('toggle_plus_i_s')) {
      var container = $(this).parent().next();
      if (container.hasClass('projects-page-container')) {
        $.get('/admin/projects/expand/'+ $(this).data('page_id') +'/');
        container.slideDown();
      } else {
        send_request('/admin/projects/expand/'+ $(this).data('page_id') +'/', {}, projects_pages_make, this)
      }
      $(this).removeClass('toggle_plus_i_s').addClass('toggle_minus_i_s');
    } else if ($(this).hasClass('toggle_minus_i_s')) {
      $.get('/admin/projects/collapse/'+ $(this).data('page_id') +'/');
      $(this).parent().next().slideUp();
      $(this).removeClass('toggle_minus_i_s').addClass('toggle_plus_i_s');
    }
    
    return false;
  });
  
  make_draggable('#projects-pages-structure div.page-draggable');
  make_droppable('#projects-pages-structure div.page-droppable');
});

function make_draggable(objs) {
  $(objs).draggable({
    addClasses: false,
    handle: 'a.move_i_s',
    cursor: 'move',
    axis: 'y',
    containment: '#projects-pages-structure',
    opacity: 0.5,
    revertDuration: 300,
    revert: 'invalid',
    zIndex: 2
  });
}

function make_droppable(objs) {
  $(objs).droppable({
    addClasses: false,
    hoverClass: 'for-drop',
    tolerance: 'pointer',
    drop: function(event, ui) {
      var page = $(ui.draggable);
      var dest = $(this);
      if (dest.hasClass('page-draggable')) {
        modal(
          'information',
          '',
          'Вставить страницу после целевой или сделать дочерней?',
          [
            {text: 'Сделать дочерней', handler: function() { move_page(page, dest, 'inner'); }, icon: 'accept'},
            {text: 'Вставить после',   handler: function() { move_page(page, dest, 'after'); }, icon: 'accept'}
          ]
        );
      } else {
        move_page(page, dest, 'after');
      }
    }
  });
}

function move_page(page, dest, placement) {
  modal('hide');
  send_request('/admin/projects/move_page/', {
    page:      page.attr('id'),
    dest:      dest.attr('id'),
    placement: placement
  }, 'reload');
}

function slug(str) {
  var result = '';
  
  var ru = [
    'а', 'б', 'в', 'г', 'д', 'е', 'ё',  'ж',  'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х',
    'ц', 'ч',  'ш',  'щ',   'ы', 'э', 'ю',  'я',  '-', '_', ' ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
    'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm'
  ];
  var en = [
    'a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh',
    'c', 'ch', 'sh', 'shh', 'y', 'e', 'ju', 'ja', '-', '_', '_', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
    'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm'
  ];
  
  for (var i in str) {
    var index = ru.indexOf(str[i].toLowerCase());
    if (index != -1) {
      result += en[index];
    }
  }
  
  return result;
}

function projects_pages_make(page, returning) {
  var html = '<div id="project-page-'+ page.id +'-childs" class="projects-page-container">';
  
  $(page.pages).each(function() {
    html += '<div id="project-page-'+ this.id +'" class="panel-2 selection page-draggable page-droppable" data-childs="'+ this.pages.length +'">' +
      '<a href="#" class="toggle '+ 
      (this.state == 1 ?
        (this.childs > 0 ? 'toggle_minus_i_s' : 'toggle_none_i_s')
      :
        (this.childs > 0 ? 'toggle_plus_i_s' : 'toggle_none_i_s')
      ) +
      '" data-project_id="'+ this.project_id +'" data-page_id="'+ this.id +'"></a>' +
      
      '<a href="/admin/projects/edit_page/'+ this.id +'/" title="'+ this.path +'" class="title">'+ this.title +'</a>' +
      
      '<div class="buttons">' +
        '<a href="#" class="move_i_s" title="Переместить"></a>' +
        
        '<a href="'+ this.path +'" target="_blank" class="magnifier_i_s" title="Посмотреть"></a>' +
        
        '<a href="/admin/projects/page_history/'+ this.id +'/" class="compress_i_s" title="Резервирование и восстановление"></a>' +
        
        (this.active == 1 ?
          '<a href="/admin/projects/disable_page/'+ this.id +'/" class="lightbulb_i_s" title="Отключить"></a>'
        :
          '<a href="/admin/projects/enable_page/'+ this.id +'/" class="lightbulb_off_i_s" title="Включить"></a>'
        ) +
        
        '<a href="/admin/projects/clone_page/'+ this.id +'/" class="copy_i_s" title="Клонировать"></a>' +
        
        '<a href="/admin/projects/create_page/'+ this.project_id +'/'+ this.id +'/" class="add_i_s" title="Создать страницу"></a>' +
        
        '<a href="/admin/projects/edit_page/'+ this.id +'/" class="pencil_i_s" title="Изменить"></a>' +
        
        '<a href="#" onClick="return send_confirm(' +
          '\'Вы уверены, что хотите удалить страницу?\', \'/admin/projects/delete_page/'+ this.id +'/\', \'{}, /admin/projects/\'' +
        ');" class="cross_i_s" title="Удалить" ></a>' +
        
        '<div class="clear"></div>' +
      '</div>' +
      
      '<div class="clear"></div>' +
    '</div>';
    
    if (this.pages.length) {
      html += projects_pages_make(this, true);
    }
  });
  
  html += '</div>';
  
  if (returning) {
    return html;
  }
  
  $(this).parent().after(html);
  
  make_draggable('#project-page-'+ page.id +'-childs div.page-draggable');
  make_droppable('#project-page-'+ page.id +'-childs div.page-droppable');
  
  sheet('hide');
}