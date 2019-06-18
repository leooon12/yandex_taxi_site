@extends('voyager::master')
@section('content')


    <link rel="stylesheet" href="/css/my_loader.css">

    <style>
        .tabs {
            text-align: center;
            color: #333333;
            font-weight: bold;
        }

        .withdrawal-info {
            display: inline-block;
            padding: 10px;
            margin: 10px;
            background: #eeeeee;
            border-radius: 10px;
            font-weight: bold;
            color: #333333;
        }

        .withdrawal-info .title {
            text-align: center;
        }

        input[type="button"] {
            background: #dddddd;
            border: 0;
            border-radius: 5px;
            margin: 5px;
            padding: 10px;
            color: #333333;
            opacity: 0.9;
        }

        input[type="button"]:hover {
            opacity: 1;
        }

        .all {
            background: #53aae8 !important;;
        }

        .waiting {
            background: #e8c153 !important;;
        }

        .success {
            background: #53e888 !important;;
        }

        .error {
            background: #e85353 !important;
        }
    </style>

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
                        requests_html.innerHTML = "<br> На текущий момент заявок нет";
                        return;
                    }

                    requests.forEach(function (request) {
	                    var data = JSON.parse(request.content);

	                    var requestInfo = {
		                    type:		data.type,
		                    status: 	request.status.name,
		                    date: 		request.created_at,
		                    id:			request.id
	                    };

	                    var content = data.data;

	                    var user = {
		                    surname: 	request.user.surname,
		                    name: 		request.user.name,
		                    patronymic: request.user.patronymic,
		                    phone: 		request.user.phone_number
	                    };

	                    requests_html.innerHTML += generateCard(requestInfo, content, user);
                    });

                });

            });
        }

        function generateCard(requestInfo, content, user) {
	        var statusClass = requestInfo.status == "в обработке" ? "waiting" : requestInfo.status == "выполнен" ? "success" : "error";

	        var html = '<div class="withdrawal-info">' +
		        '<p class="title">' + requestInfo.typeRU + '</p>' +
		        'Статус: <b class="'+statusClass+'">' + requestInfo.status + '</b><br>' +
		        'Дата создания: <b>' + requestInfo.date + '</b><br>' +
		        '<br>' +
		        'Реквизиты и суммы' + '<br>';

	        Object.keys(content).forEach(function (item) {
		        html += item + ': <b>' + content[item] + '</b><br>';
	        });

	        html += 'Пользователь' +
		        '<br>' +
		        'Номер телефона: <b>' + user.phone + '</b><br><br>';

	        statuses.forEach(function (status) {
		        var className = status.id == 1 ? "waiting" : status.id == 2 ? "success" : "error";

		        html += '<input class="' + className + '" value="'+status.name+'" type="button" onclick="changeStatus(\''+requestInfo.type+'\', '+requestInfo.id+', '+status.id+');" />';
	        });

	        html += "</div>";

	        return html;
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