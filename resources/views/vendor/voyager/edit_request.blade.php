@extends('voyager::master')
@section('content')


    <link rel="stylesheet" href="/css/my_loader.css">

    <div>

        <div id="my_loader" class="my_loader"></div>

        <div>
            <input id="in_work" type="button" value="Заявки в обработкe" onclick="getWithdrawals(IN_WORK_REQUESTS);" />
            <input id="all" type="button" value="Все заявки" onclick="getWithdrawals(ALL_REQUESTS);" />

        </div>

        <div id="requests"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <script>
        $('#my_loader').hide();

        const IN_WORK_REQUESTS = "in_work";
        const ALL_REQUESTS = "all";

        var audio = new Audio('/new_withdrawal_sound.mp3');
        var last_state = "";
        var requests_html = document.getElementById('requests');
        var requests_count = 0;
        var loader = true;

        //Основной метод на получение данных с сервера
        function getRequests(type) {

            if (loader)
                $.ajax({
                    beforeSend: function() {
                        $('#my_loader').show();
                    },
                    complete: function() {
                        $('#my_loader').hide();
                    }
                });

            $.ajax("/api/withdrawal_statuses").done(function (statuses) {

                $.get("/api/edit_requests/" + type, function(requests){

                    //Чтобы не было коллизии при переключении между типами заявок
                    if (last_state !== type)
                        requests_count = requests.length;

                    last_state = type;

                    requests_html.innerHTML = "";


                    if (requests_count < requests.length) {
                        audio.play();
                        requests_count = requests.length;
                    }

                    if (requests_count < 1) {
                        requests_html.innerHTML = "<br> На текущий момент заявок нет.";
                        return;
                    }

                });

            });
        }
    </script>
@stop