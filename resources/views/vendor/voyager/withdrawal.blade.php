@extends('voyager::master')
@section('content')


    <link rel="stylesheet" href="/css/my_loader.css">

    <div>

        <div id="my_loader" class="my_loader"></div>

        <div>
            <input id="in_work" type="button" value="Заявки в обработкe" onclick="getWithdrawals(IN_WORK_WITHDRAWAL);"/>
            <input id="all" type="button" value="Все заявки" onclick="getWithdrawals(ALL_WITHDRAWALS);"/>

        </div>
        <div id="withdrawals"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <script>
		$('#my_loader').hide();

		const IN_WORK_WITHDRAWAL = "in_work";
		const ALL_WITHDRAWALS = "all";

		var audio = new Audio('/new_withdrawal_sound.mp3');
		var last_state = "";
		var withdrawals_html = document.getElementById('withdrawals');
		var withdrawals_count = 0;
		var loader = true;

		//Инициирование страницы
		getWithdrawals(IN_WORK_WITHDRAWAL);

		//Основной метод на получение данных с сервера
		function getWithdrawals(type) {

			if (loader)
				$.ajax({
					beforeSend: function () {
						$('#my_loader').show();
					},
					complete: function () {
						$('#my_loader').hide();
					}
				});

			$.ajax("/api/withdrawal_statuses").done(function (statuses) {

				$.get("/api/withdrawal/" + type, function (withdrawals) {

					//Чтобы не было коллизии при переключении между типами заявок
					if (last_state !== type)
						withdrawals_count = withdrawals.length;

					last_state = type;

					withdrawals_html.innerHTML = "";


					if (withdrawals_count < withdrawals.length) {
						audio.play();
						withdrawals_count = withdrawals.length;
					}

					if (withdrawals_count < 1) {
						withdrawals_html.innerHTML = "<br>На текущий момент заявок нет";
						return;
					}

					withdrawals.forEach(function (withdrawal) {
						switch (withdrawal.type) {
							case "WithdrawalBankAccount":
								var paymentInfo = {
									type:		withdrawal.type,
									typeRU: 	"Банковский счет",
									status: 	withdrawal.status.name,
									date: 		withdrawal.created_at,
									id:			withdrawal.id,
									sum: 		withdrawal.sum
								};

								var requisites = [{
									name: 		"Номер счета",
									valueText: 	withdrawal.account_number
								}];

								var user = {
									surname: 	withdrawal.user.surname,
									name: 		withdrawal.user.name,
									patronymic: withdrawal.user.patronymic,
									phone: 		withdrawal.user.phone_number
								};

								withdrawals_html.innerHTML += generateCard(paymentInfo, requisites, user);

								break;

							case "WithdrawalBankCard":
								var paymentInfo = {
									type:		withdrawal.type,
									typeRU: 	"Банковская карта",
									status: 	withdrawal.status.name,
									date: 		withdrawal.created_at,
									id:			withdrawal.id,
									sum: 		withdrawal.sum
								};

								var requisites = [{
									name: 		"Номер карты",
									valueText: 	withdrawal.card_number
								}];

								var user = {
									surname: 	withdrawal.user.surname,
									name: 		withdrawal.user.name,
									patronymic: withdrawal.user.patronymic,
									phone: 		withdrawal.user.phone_number
								};

								withdrawals_html.innerHTML += generateCard(paymentInfo, requisites, user);

								break;

							case "WithdrawalYandex":
								var paymentInfo = {
									type:		withdrawal.type,
									typeRU: 	"Яндекс-деньги",
									status: 	withdrawal.status.name,
									date: 		withdrawal.created_at,
									id:			withdrawal.id,
									sum: 		withdrawal.sum
								};

								var requisites = [{
									name: 		"Номер кошелька",
									valueText:  withdrawal.card_number
								}];

								var user = {
									surname: 	withdrawal.user.surname,
									name: 		withdrawal.user.name,
									patronymic: withdrawal.user.patronymic,
									phone: 		withdrawal.user.phone_number
								};

								withdrawals_html.innerHTML += generateCard(paymentInfo, requisites, user);

								break;
						}
					});
				});

				function generateCard(paymentInfo, requisites, user) {
					var html = '<div id="withdrawal_info">' +
							'<b>Заявка на выплату: ' + paymentInfo.typeRU + '</b>' +
							'<br>' +
							'Статус: ' + paymentInfo.status +
							'<br>' +
							'Дата создания: ' + paymentInfo.date +
							'<br>' +
							'<br>' +
							'<b>Реквизиты и суммы</b> ' +
							'<br>';

					requisites.forEach(function (item) {
						html += item.name + ': ' + item.valueText + '<br>';
					});

					html += 'Сумма: ' + paymentInfo.sum + "<br>";

					html += '<b>Пользователь</b>' +
							'<br>' +
							'Номер телефона: ' + user.phone +
							'<br>';

					statuses.forEach(function (status) {
						html += '<input id="' + status.id + '" type="button" value="' + status.name + '" onclick="changeStatus('+paymentInfo.type+', '+paymentInfo.id+');" />';
					});

					html += "</div>";

					return html;
				}
			});
		}

		//Изменение статуса заявки
		function changeStatus(parent_node_name, status_id) {
			var model_name = parent_node_name.split("_")[2];
			var withdrawal_id = parent_node_name.split("_")[3];

			$.ajax({
				type: "POST",
				url: "/api/withdrawal/status",
				data: {
					withdrawal_id: withdrawal_id,
					status_id: status_id,
					model_name: model_name
				},
				success: function () {
					getWithdrawals(last_state);
				}
			});
		}

		//Автоообновление данных каждую минуту
		setInterval(function () {
			//Костыль, чтобы лоадер не показывался во время автообновления
			loader = false;
			getWithdrawals(last_state);
			loader = true;
		}, 1000 * 30);

    </script>
@stop