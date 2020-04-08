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

		.topUp {
			background: #16CEDB !important;
		}
	</style>

    <div>

        <div id="my_loader" class="my_loader"></div>

        <div class="tabs">
            <input id="in_work" type="button" value="Необработанные заявки" onclick="getWithdrawals(IN_WORK_WITHDRAWAL);" class="waiting" />
            <input id="all" type="button" value="Все заявки" onclick="getWithdrawals(ALL_WITHDRAWALS);" class="all" />
        </div>

        <div id="withdrawals"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <script>
		$('#my_loader').hide();

		const IN_WORK_WITHDRAWAL = "in_work";
		const ALL_WITHDRAWALS = "all";

		var last_state = "";
		var withdrawals_html = document.getElementById('withdrawals');
		var withdrawals_count = 0;
		var loader = true;

		var requests_count = 0;

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


				$.get("/api/edit_request/in_work" + type, function (requests) {

					if (requests_count < requests.length) {
						var audio = new Audio('/new_withdrawal_sound.mp3');
						audio.play();

						if (Notification.permission !== 'granted')
							Notification.requestPermission();

						var notification = new Notification('Новая заявка на изменение данных', {
							icon: 'https://cdn1.iconfinder.com/data/icons/hawcons/32/698873-icon-136-document-edit-512.png',
							body: 'Создана новая заявка на изменение данных Сервис Таксометр',
							requireInteraction: true,
							silent: false
						});

						notification.onclick = function () {
							window.open('https://taxiyour.ru/admin/edit_request');
						};

						requests_count = requests.length;
					}
				});

				$.get("/api/withdrawal/" + type, function (withdrawals) {

					//Чтобы не было коллизии при переключении между типами заявок
					if (last_state !== type)
						withdrawals_count = withdrawals.length;

					last_state = type;

					withdrawals_html.innerHTML = "";

					if (withdrawals_count < withdrawals.length) {
						var audio = new Audio('/new_withdrawal_sound.mp3');
						audio.play();

						if (Notification.permission !== 'granted')
							Notification.requestPermission();

						var notification = new Notification('Новая заявка на выплату', {
							icon: 'https://taxiyour.ru/storage/settings/June2019/tLGF2nmNYP1Kd130Ta3V.png',
							body: 'Создана новая заявка на выплату средств Сервис Таксометр',
							requireInteraction: true,
							silent: false
						});

						notification.onclick = function () {
							window.open('https://taxiyour.ru/admin/withdrawal');
						};

						withdrawals_count = withdrawals.length;
					}

					if (withdrawals_count < 1) {
						withdrawals_html.innerHTML = "<br>На текущий момент заявок нет";
						return;
					}

					if (type == ALL_WITHDRAWALS)
						withdrawals.reverse();

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
								}, {
									name: 		"ФИО",
									valueText: 	withdrawal.surname + " " + withdrawal.name + " " + withdrawal.patronymic
								}, {
									name: 		"БИК",
									valueText: 	withdrawal.bik
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
									valueText:  withdrawal.yandex_number
								}];

								var user = {
									surname: 	withdrawal.user.surname,
									name: 		withdrawal.user.name,
									patronymic: withdrawal.user.patronymic,
									phone: 		withdrawal.user.phone_number
								};

								withdrawals_html.innerHTML += generateCard(paymentInfo, requisites, user);

								break;

							case "WithdrawalQiwi":
								var paymentInfo = {
									type:		withdrawal.type,
									typeRU: 	"Киви",
									status: 	withdrawal.status.name,
									date: 		withdrawal.created_at,
									id:			withdrawal.id,
									sum: 		withdrawal.sum
								};

								var requisites = [{
									name: 		"Номер кошелька",
									valueText:  withdrawal.qiwi_number
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
					var statusClass = paymentInfo.status == "ожидает подтверждения" ? "waiting" : paymentInfo.status == "выполнен" ? "success" : "error";

					var html = '<div class="withdrawal-info">' +
									'<p class="title">Выплата на ' + paymentInfo.typeRU + '</p>' +
									'Статус: <b class="'+statusClass+'">' + paymentInfo.status + '</b><br>' +
									'Дата создания: <b>' + paymentInfo.date + '</b><br>' +
									'<br>' +
									'Реквизиты и суммы' + '<br>';

					requisites.forEach(function (item) {
						html += item.name + ': <b>' + item.valueText + '</b><br>';
					});

					html += 'Сумма: <b>' + paymentInfo.sum + " руб</b><br>" +
                            (paymentInfo.typeRU == "Банковская карта" ? 'Комиссия: <b>35 руб</b><br>Сумма к выплате: ' + (paymentInfo.sum - 35) + " руб<br>" : "") +
							'<br>' +
							'Пользователь<br>' +
							'Номер телефона: <b>' + user.phone + '</b><br>' +
                            '<br>';

					if (paymentInfo.type === "WithdrawalBankCard" && paymentInfo.status === "ожидает подтверждения")
						html += '<input class="topUp" value="Автовыплата" type="button" onclick="topUpWithdrawal('+paymentInfo.id+', \''+paymentInfo.type+'\');" />';

					if (paymentInfo.type === "WithdrawalQiwi" && paymentInfo.status === "ожидает подтверждения")
						html += '<input class="topUp" value="Автовыплата" type="button" onclick="topUpWithdrawal('+paymentInfo.id+', \''+paymentInfo.type+'\');" disabled/>';

					statuses.forEach(function (status) {
						if (status.id == 4)
							return;

						var className = status.id == 1 ? "waiting" : status.id == 2 ? "success" : "error";

						html += '<input class="' + className + '" value="'+status.name+'" type="button" onclick="changeStatus(\''+paymentInfo.type+'\', '+paymentInfo.id+', '+status.id+');" />';
					});

					html += "</div>";

					return html;
				}
			});
		}

		//Изменение статуса заявки
		function changeStatus(model_name, withdrawal_id, status_id) {

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

		function topUpWithdrawal(withdrawal_id, type) {

			switch (type) {
				case "WithdrawalBankCard":
					url = "/admin/withdrawal/topUp/bankCard";
					break;
				case "WithdrawalQiwi":
					url = "/admin/withdrawal/topUp/qiwi";
					break;
			}

			$.ajax({
				type: "POST",
				url: url,
				data: {
					withdrawal_id: withdrawal_id
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
		}, 1000 * 5);

    </script>
@stop
