
<html lang="ru" class="desktop   landscape">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>Подключение к Яндекс-Такси Владивосток: Официальный партнер</title>

    <link rel="icon" href="/Home_files/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="/Home_files/grid.css">
    <link rel="stylesheet" href="/Home_files/style.css">
    <link rel="stylesheet" href="/Home_files/booking.css">

    <script src="/Home_files/jquery.js"></script>
    <script src="/Home_files/jquery.maskedinput.min.js"></script>
    <!-- BEGIN JIVOSITE CODE {literal} -->
    <script type='text/javascript'>
        (function(){ var widget_id = 'vaxM51Gk77';var d=document;var w=window;function l(){
            var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
            s.src = '//code.jivosite.com/script/widget/'+widget_id
            ; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}
            if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}
            else{w.addEventListener('load',l,false);}}})();
    </script>
    <!-- {/literal} END JIVOSITE CODE -->
</head>

<body class="">

<script>
$(document).ready(function () {
    $("input[name=phone_number]").mask("8 (999) 999-9999");

    var isAvailable = true;

    $("#form-button").click(function (e) {
        if (!isAvailable) {
            e.preventDefault();
            return;
        }

        isAvailable = false;
        $("#form-button").css("background-color", "#cccccc");

        $("#form-button").html("Отправка...");

        var phone = $("input[name=phone_number]").val().replace(/\D/g, '');
        var name = $("input[name=full_name]").val();

        $.post(
            "http://taxiyour.ru/api/driver",
            {
                full_name: name,
                phone_number: phone
            },
            function (data) {
                ShowResultMessage(data);

                $("#form-button").css("background-color", "#ffffff");
                $("#form-button").html("Отправить!");
                isAvailable = true;
            }
        );

        e.preventDefault();
    });

    function ShowResultMessage(data) {
        if (data.status == 400) {
            $(".result-modal .status-icon.success-status").hide();
            $(".result-modal .status-icon.error-status").show();

            $(".result-modal .title").html("Ошибка");
            $(".result-modal .description").html(data.message);

            $(".result-modal .errors").html(
                (data.object.full_name ? data.object.full_name : "") +
                "<br />" +
                (data.object.phone_number ? data.object.phone_number : "")
            );
        } else {
            $(".result-modal .status-icon.success-status").show();
            $(".result-modal .status-icon.error-status").hide();

            $(".result-modal .title").html("Заявка отправлена");
            $(".result-modal .description").html(data.message);

            $(".result-modal .errors").html("");

            $("input[name=phone_number]").val("");
            $("input[name=full_name]").val("");
        }

        $(".result-modal").fadeIn();
    }
});

</script>


    <div class="result-modal">
        <img class="status-icon success-status" src="/images/svg/success.svg">
        <img class="status-icon error-status" src="/images/svg/error.svg">
        <p class="title"></p>
        <p class="description"></p>
        <small><p class="errors"></p></small>
        <div class="button" onclick="$('.result-modal').fadeOut();">Закрыть</div>
    </div>


<div class="page">
    <!--========================================================
                              HEADER
    =========================================================-->
    <header>
        <div class="brand">
            <img src="/Home_files/yandex-taxi-logo.png" style="
                    max-width: 180px;
                    margin-top: 15px;
                    margin-bottom: 15px;
            ">
            <h1 class="brand_name" style="padding-left: 10px; display: none">
                <em>Yandex</em> Taxi - подключение Владивосток </a>
            </h1>
        </div>
    </header>
    <!--========================================================
                              CONTENT
    =========================================================-->
    <main>
        <section class="parallax parallax01" style="min-height: 80vh; background-position: 50% -16px;">
            <div class="container">
                <div class="row">
                    <div class="grid_8">
                        <div class="promo-box">
                            <p>
                                Подключайтесь к Яндекс.Такси
                                <em>
                                    у официального партнера
                                </em>
                            </p>
                        </div>
                    </div>
                    <div class="grid_4" style="margin-top: 10vh;">
                        <div class="booking-box">
                            <h2>Заявка на подключение</h2>
                            <form id="bookingForm" class="booking-form" method="POST" action="{{ url('/driver') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="controlHolder">
                                    <div class="tmInput">
                                        <input name="full_name" placeholder="Ваше Имя и Фамилия" type="text">
                                    </div>
                                </div>
                                <div class="controlHolder">
                                    <div class="tmInput">
                                        <input name="phone_number" placeholder="Ваш номер телефона" type="tel">
                                    </div>
                                </div>

                                <button type="submit" class="btn" id="form-button">Отправить!</button>
                                <!--
                                <a href="#" class="btn"
                                   data-type="submit" onclick="sendForm()">Отправить!</a>
                                   -->
                            </form>
                            <p>Подключение к Яндекс.Такси
                                за 15 минут
                                заполняйте заявку и ожидайте звонка </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="well center border">
            <div class="container">
                <h2>Присоединяйтесь к Яндекс.Такси</h2>
                <h3>С нами сделать это просто</h3>
                <p style="font-size: 20px; color: #737373; display: inline-block;">
                    Вы хотите работать водителем такси на своем или арендованном автомобиле и при этом получать достойный доход?
                    <br />
                    Или вы уже работаете в такси, но заработок и условия труда вас не устраивают?
                    <br />
                    А может, вы работаете в другом направлении, но вас интересует подработка в такси на своем автомобиле?
                    <br />
                    <br />
                    Начните зарабатывать достойно вместе с нами в Яндекс.Такси!
                </p>
            </div>
        </section>

        <section class="parallax parallax02 center" style="background-position: 50% -146px;">
            <div class="container">
                <h2>Преимущества</h2>
                <div class="custom-box02 left">
                    <div class="row">
                        <div class="grid_4">
                            <div class="iconed-box">
                                <img src="./Home_files/clock.png" class="advantages-img">
                                <h3>Вы получаете деньги сразу</h3>
                                <p>Получайте оплату за совершенные поездки в первый же день работы</p>
                            </div>
                        </div>
                        <div class="grid_4">
                            <div class="iconed-box">
                                <img src="./Home_files/car.png" class="advantages-img">
                                <h3>Без холостого пробега</h3>
                                <p>Не тратьте время на поиски клиентов – заказы приходят сами</p>
                            </div>
                        </div>
                        <div class="grid_4">
                            <div class="iconed-box">
                                <img src="./Home_files/money.png" class="advantages-img">
                                <h3>Зарабатывайте от 70.000 рублей </h3>
                                <p>Решайте сами, когда и сколько работать</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!--========================================================
                              FOOTER
    =========================================================-->
    <footer>
        <div class="container">
            <div class="footer-box center">

                <div class="phone">
                    <div><a href="tel:+74232800855">+7 (423) 2800-855</a></div>
                    <div><a href="tel:+79841880588">+7 (984) 188-05-88</a></div>
                </div>
                <div class="socials">
                    <ul>
                        <li>
                            <a href="https://api.whatsapp.com/send?phone=89679580855">
                                <img src="/Home_files/wa.png">
                            </a>
                        </li>
                        <li>
                            <a href="viber://chat?number=89679580855">
                                <img src="/Home_files/viber.png">
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="copyright">
                    Разработано компанией <a href="http://awake.su">Awake!</a> © <span id="copyright-year">2019</span>
                    <!-- {%FOOTER_LINK} -->
                </div>
            </div>
        </div>
    </footer>
</div>

<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0">
<script src="/Home_files/jquery.rd-parallax.js"></script>
<script type="application/javascript">
    function sendForm(e) {
        alert($('#bookingForm').serialize());
        $.get('server.php', $('#bookingForm').serialize());
    };
</script>

</body>

</html>
