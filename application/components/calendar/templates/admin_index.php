<link rel="stylesheet" href="/bootstrap-calendar/css/calendar.css">
<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Календарь событий
  </h1>
</div>

<div class="container-fluid">
  <div class="col-xs-11 pull-right"><div id="calendar"></div></div>
</div>

<script type="text/javascript" src="/bootstrap-calendar/components/underscore/underscore-min.js"></script>
<script type="text/javascript" src="/bootstrap-calendar/js/language/ru-RU.js"></script>
<script type="text/javascript" src="/bootstrap-calendar/js/calendar.js"></script>
<script type="text/javascript">
  var calendar = $("#calendar").calendar(
  {
    language: 'ru-RU',
    tmpl_path: "/bootstrap-calendar/tmpls/",
    events_source: function () { return []; }
  });
</script>