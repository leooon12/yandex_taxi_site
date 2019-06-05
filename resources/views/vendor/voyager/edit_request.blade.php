@extends('voyager::master')
@section('content')


    <link rel="stylesheet" href="/css/my_loader.css">

    <div>

        <div id="my_loader" class="my_loader"></div>

        <div>
            <input id="in_work" type="button" value="Заявки в обработкe" onclick="getRequests(IN_WORK_REQUESTS);" />
            <input id="all" type="button" value="Все заявки" onclick="getRequests(ALL_REQUESTS);" />

        </div>

        <div id="requests"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <script>
        $('#my_loader').hide();

        const IN_WORK_REQUESTS = "in_work";
        const ALL_REQUESTS = "all";

        getRequests(IN_WORK_REQUESTS);

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

                $.get("/api/edit_request/" + type, function(requests){

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

                    requests.forEach(function (request) {
                        data = JSON.parse(request.content);

                        //Здесь свитч по типу зачвки, по каждому типу Jsona свой парсинг
                        switch(data.type) {
                            case "Изменение ВУ":
                                inner_request_info(data.type,  request.status.name, request.created_at);
                                inner_request_data(data);
                                inner_user_info(request.user.surname, request.user.name, request.user.patronymic, request.user.phone_number);
                                inner_statuses(statuses, request.id);
                                break;
                            default:
                                inner_request_info(data.type,  request.status.name, request.created_at);
                                inner_request_data(data);
                                inner_user_info(request.user.surname, request.user.name, request.user.patronymic, request.user.phone_number);
                                inner_statuses(statuses, request.id);
                                break;
                        }
                    });

                });

            });
        }

        //Заполенение данных о типе заявки
        function inner_request_info(type, status, created_at) {
            requests_html.innerHTML = requests_html.innerHTML + '<br><div id="request_info"><b>Заявка</b>'+
                '<br>Тип: ' + type + '<br>Статус: ' + status + '<br>Дата создания: ' + created_at
                +'</div>';
        }

        //Заполенение данных о типе заявки
        function inner_request_data(data) {
            //Код вывода пропасенного JSON
        }

        //Заполенение данных о пользователе
        function inner_user_info(surname, name, patronymic, phone_number) {
            requests_html.innerHTML =
                requests_html.innerHTML + '<br><div id="user_info"> <b>Пользователь</b> ' +
                //'<br>Фамилия: ' + surname + '<br>Имя: ' + name + '<br>Отчество: ' + patronymic +
                '<br>Номер телефона: ' + phone_number
                + '</div>';
        }

        //Заполенение данных о статусах заявки
        function inner_statuses(statuses, request_id) {
            requests_html.innerHTML = requests_html.innerHTML + '<br><div id="statuses_info_'+ request_id +'"></div><hr>';
            var statuses_html = document.getElementById('statuses_info_'+ request_id);

            statuses.forEach(function (status) {
                statuses_html.innerHTML = statuses_html.innerHTML +
                    ' <input id="'+ status.id +'" type="button" value="' + status.name + '" onclick="changeStatus(this.parentNode.id, this.id);" />';
            });
        }

        //Изменение статуса заявки
        function changeStatus(parent_node_name, status_id) {
            var request_id = parent_node_name.split("_")[2];

            $.ajax({
                type: "POST",
                url: "/api/edit_request/status",
                data: {
                    request_id: request_id,
                    status_id: status_id,
                },
                success: function(){
                    getRequests(last_state);
                }
            });
        }

        //Автоообновление данных каждую минуту
        setInterval(function() {
            //Костыль, чтобы лоадер не показывался во время автообновления
            loader = false;
            getRequests(last_state);
            loader = true;
        }, 1000 * 30);
    </script>
@stop