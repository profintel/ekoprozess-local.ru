<!-- Слайдер -->
{{cmp:gallery->render<-slider}}

<div class="container five-cols clearfix">
    <!-- Пять колонок -->
    <div class="clearfix">
        <a href="/katalog" title="Купить автомобиль">
            <img src="/images/col-1.png">
            <div>КУПИТЬ<br>АВТОМОБИЛЬ</div>
        </a>
    </div>
    <div class="clearfix">
        <a href="/prodat_avtomobil" title="Продать автомобиль">
            <img src="/images/col-2.png">
            <div>ПРОДАТЬ<br>АВТОМОБИЛЬ</div>
        </a>
    </div>
    <div class="clearfix">
        <a href="/zalog_nedvizhimosti" title="Залог недвижимости">
            <img src="/images/col-3.png">
            <div>ЗАЛОГ<br>НЕДВИЖИМОСТИ</div>
        </a>
    </div>
    <div class="clearfix">
        <a href="/katalog" title="Каталог">
            <img src="/images/col-4.png">
            <div>КАТАЛОГ</div>
        </a>
    </div>
    <div class="clearfix">
        <a href="#" title="Калькулятор">
            <img src="/images/col-5.png">
            <div>КАЛЬКУЛЯТОР</div>
        </a>
    </div>
</div>

<!-- Панель с тремя колонками -->
<div class="container panel-blue clearfix">
    {{tpl:preim}}
</div>

<div class="container padding-0">
    <h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>
    <?=$_page['params']['content_'.$_language];?>
</div>

<div class="container padding-0">
    <h1 class="title">Горячие предложения</h1>
    {{cmp:catalog->last_catalog<-katalog<-5<-true}}
</div>