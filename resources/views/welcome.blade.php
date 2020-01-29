<html lang="ru" class="desktop   landscape">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>Подключение к Яндекс Такси</title>

    <link rel="icon" href="/Home_files/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="/Home_files/grid.css">
    <link rel="stylesheet" href="Home_files/style.css?v=12">
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

    <meta name="keywords" content="подключиться,Яндекс,такси,работа,свой,автомобиль,личный,арендованный,машина,таксист,деньги,бизнес" />
    <meta name="description" content="Вы хотите работать водителем такси на своем, личном или арендованном автомобиле и при этом получать достойный доход? Начните зарабатывать достойно вместе с нами в Яндекс.Такси!" />
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

        ShowResultMessage("pending");

        $("#form-button").css("background-color", "#cccccc");

        $("#form-button").html("Отправка...");

        var phone = $("input[name=phone_number]").val().replace(/\D/g, '');
        var name = $("input[name=full_name]").val();

        $.post(
            "/api/driver",
            {
                full_name: name,
                phone_number: phone
            },
            function (data) {
                ShowResultMessage(data);
                MakeFormAvailable();
            }
        ).error(function(){
            ShowResultMessage({
                status: 400,
                message: "При отправке запроса произошла ошибка на сервере",
                object: {
                    full_name: "Ошибка будет исправлена в кратчайшие сроки",
                    phone_number: "Пока Вы можете связаться с нами по номеру телефона"
                }
            });
            MakeFormAvailable();
        });

        e.preventDefault();
    });

    function MakeFormAvailable() {
        $("#form-button").css("background-color", "#ffffff");
        $("#form-button").html("Отправить!");
        isAvailable = true;
    }

    function ShowResultMessage(data) {
        if (data === "pending") {
            $(".result-modal .info").hide();
            $(".result-modal .preloader").show();
            $(".result-modal").fadeIn();
            return;
        }

        $(".result-modal .preloader").slideUp(function(){
            $(".result-modal .info").slideDown();
        });

        if (data.status == 400) {
            $(".result-modal .info .status-icon.success-status").hide();
            $(".result-modal .info .status-icon.error-status").show();

            $(".result-modal .info .title").html("Ошибка");
            $(".result-modal .info .description").html(data.message);

            $(".result-modal .info .errors").html(
                (data.object.full_name ? data.object.full_name : "") +
                "<br />" +
                (data.object.phone_number ? data.object.phone_number : "")
            );
        } else {
            $(".result-modal .info .status-icon.success-status").show();
            $(".result-modal .info .status-icon.error-status").hide();

            $(".result-modal .info .title").html("Заявка отправлена");
            $(".result-modal .info .description").html(data.message);

            $(".result-modal .info .errors").html("");

            $("input[name=phone_number]").val("");
            $("input[name=full_name]").val("");
        }

        $(".result-modal").fadeIn();
    }
});

</script>


    <div class="result-modal">
        <div class="preloader">
            <img class="status-icon" src="/images/svg/preloader.svg">
            <p class="title">Отправка...</p>
            <p class="description">Подождите, производится отправка формы</p>
        </div>
        <div class="info">
            <img class="status-icon success-status" src="/images/svg/success.svg">
            <img class="status-icon error-status" src="/images/svg/error.svg">
            <p class="title"></p>
            <p class="description"></p>
            <small><p class="errors"></p></small>
            <div class="button" onclick="$('.result-modal').fadeOut();">Закрыть</div>
        </div>
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
                        <div class="promo-box" style="text-align: center; font-size: 2.5rem;  text-shadow: 0 0 15px black;">
                            <p>
                                Подключайтесь к Яндекс.Такси<br/><span style="font-size: 2.7rem">На этой работе Вы начальник</span>
                            </p>
                        </div>
                    </div>
                    <div class="grid_4" style="margin-top: 10vh;">
                        <div class="booking-box">

                            <p style="color: black; padding: 10px 0; border-radius: 5px;">
                                <strong style="font-size: 200%; display: block; margin-bottom: 1rem; font-weight: bold; line-height: 120%;">
                                    Заявка на подключение
                                </strong>
                                Подключение к Яндекс.Такси за 15 минут: заполняйте заявку и ожидайте звонка
                            </p>
                            
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
                            <p style="background: rgba(255, 255, 255, 0.4); color: black; padding: 10px; text-align: center; border-radius: 5px;">

                                <small style="font-size: 10px; line-height: 10px; color: rgba(0,0,0,0.7);">
                                    Нажимая кнопку "Отправить!" вы <br>
                                    соглашаетесь с условиями <a href="/oferta.docx" style="color: blue">публичной оферты</a>
                                </small>

                            </p>
                        </div>
                        <br/>
                        <p style="text-align: center; color: white; font-size: 1.2rem; text-shadow: 0 0 5px black; opacity: 0.8">Официальный партнер Яндекс.Такси</p>
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

        <section class="well border" style="background-color: #f7cf29; color: black">
            <div class="container">
                <h2 class="center">Что для этого необходимо</h2>

                <br /><br />

                <div class="item-box">
                    <div class="number">1</div>
                    <div class="text">
                        Если у Вас уже есть <b>личный автомобиль</b>, который вы бы хотели использовать для работы в такси, Вы можете сразу переходить к следующему пункту.
                        <br />
                        Если же у Вас пока нет своего автомобиля, но Вы бы хотели получать дополнительный заработок, работая в такси, Вы всегда можете подключиться к нашей программе на <b>арендованной машине</b>.</div>
                </div>

                <hr />

                <div class="item-box">
                    <div class="number">2</div>
                    <div class="text">
                        <b>Заполните форму</b>, приведенную выше на сайте <b>для подключения к Яндекс Такси</b>. В ближайшее время после отправки Ваших данных, <b>с Вами свяжется наш менеджер</b> и уточнит подробности по подключению к программе
                        <br />
                        Также Вы можете воспользоваться для подключения нашим <b>мобильным приложением</b>: <a href="https://play.google.com/store/apps/details?id=su.awake.taxiyour" style="text-decoration: underline">нажмите сюда для перехода в Google Play (Android)</a>
                    </div>
                </div>

                <hr />

                <div class="item-box">
                    <div class="number">3</div>
                    <div class="text">
                        После оформления необходимых документов <b>Вы сможете сразу начинать свою работу в такси</b>.
                        <br />
                        Выбирайте время, когда Вам удобно работать, и зарабатывайте с Яндекс Такси!
                        <br />
                        Теперь у Вас есть замечательная возможность <b>самостоятельно выбирать график работы</b> и количество времени, которое Вы готовы на нее потратить.
                    </div>
                </div>

                <hr />

                <div class="item-box">
                    <div class="number">4</div>
                    <div class="text">
                        <b>Выводите деньги сразу на первый же день работы!</b>
                        <br />
                        Работая в Яндекс.Такси на своём или арендованном автомобиле, вы можете выводить заработанные средства в первый же день работы! Больше нет необходимости ждать дня зарплаты, ведь выводить заработанные в такси средства можно <b>каждый день</b>.
                    </div>
                </div>

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
                    {{--<div><a href="tel:+74232800855">+7 (423) 2800-855</a></div>--}}
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
                <div>
                    
                </div>
                <div class="copyright">
                    Разработано в <a href="http://awake.su">Awake!</a> © <span id="copyright-year">2019</span>
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
