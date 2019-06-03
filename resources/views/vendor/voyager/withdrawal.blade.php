@extends('voyager::master')
@section('content')

   <div>

       <p>Документация: <a href="https://yandextaxi.docs.apiary.io/#reference/-/0/get">все необходимые роуты на получение данных</a></p>

       <div>
           <input id="in_work" type="button" value="Заявки в обработкe" onclick="getWithdrawals(IN_WORK_WITHDRAWAL);" />
           <input id="all" type="button" value="Все заявки" onclick="getWithdrawals(ALL_WITHDRAWALS);" />

       </div>
       <div id="withdrawals"></div>
   </div>

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

   <script>

       const IN_WORK_WITHDRAWAL = "in_work";
       const ALL_WITHDRAWALS = "all";

       var audio = new Audio('/new_withdrawal_sound.mp3');
       var last_state = "";
       var withdrawals_html = document.getElementById('withdrawals');
       var withdrawals_count = 0;

       //Инициирование страницы
       getWithdrawals(IN_WORK_WITHDRAWAL);

       //Основной метод на получение данных с сервера
       function getWithdrawals(type) {

           $.ajax("/api/withdrawal_statuses").done(function (statuses) {

               $.get("/api/withdrawal/" + type, function(withdrawals){

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
                       withdrawals_html.innerHTML = "<br> На текущий момент заявок нет.";
                       return;
                   }

                   withdrawals.forEach(function (withdrawal) {
                       switch(withdrawal.type) {
                           case "WithdrawalBankAccount":
                               inner_withdrawal_info("Банковский счет", withdrawal.status.name,  withdrawal.created_at);
                               inner_withdrawal_details("Номер счета: ", withdrawal.account_number, withdrawal.sum);
                               inner_user_info(withdrawal.user.surname, withdrawal.user.name, withdrawal.user.patronymic, withdrawal.user.phone_number);
                               inner_statuses(statuses, withdrawal.id, "WithdrawalBankAccount");
                               break;
                           case "WithdrawalBankCard":
                               inner_withdrawal_info("Банковская карта", withdrawal.status.name,  withdrawal.created_at);
                               inner_withdrawal_details("Номер карты: ", withdrawal.card_number, withdrawal.sum);
                               inner_user_info(withdrawal.user.surname, withdrawal.user.name, withdrawal.user.patronymic, withdrawal.user.phone_number);
                               inner_statuses(statuses, withdrawal.id, "WithdrawalBankCard");
                               break;
                           case "WithdrawalYandex":
                               inner_withdrawal_info("Яндекс-деньги", withdrawal.status.name,  withdrawal.created_at);
                               inner_withdrawal_details("Номер кошелька: ", withdrawal.yandex_number, withdrawal.sum);
                               inner_user_info(withdrawal.user.surname, withdrawal.user.name, withdrawal.user.patronymic, withdrawal.user.phone_number);
                               inner_statuses(statuses, withdrawal.id, "WithdrawalYandex");
                               break;
                       }
                   });
               });

           });
       }

       //Заполенение данных о типе заявки
       function inner_withdrawal_info(type, status, created_at) {
           withdrawals_html.innerHTML = withdrawals_html.innerHTML + '<br><div id="withdrawal_info"><b>Заявка на выплату</b>'+
               '<br>Тип: ' + type + '<br>Статус: ' + status + '<br>Дата создания: ' + created_at
               +'</div>';
       }

       //Заполенение данных о реквизитах
       function inner_withdrawal_details(type, requisites, sum) {
           withdrawals_html.innerHTML =
               withdrawals_html.innerHTML + '<br><div id="withdrawal_details"> <b>Реквизиты и суммы</b> ' +
               '<br>' + type + requisites + '<br>Сумма: ' + sum
               + '</div>';
       }

       //Заполенение данных о пользователе
       function inner_user_info(surname, name, patronymic, phone_number) {
           withdrawals_html.innerHTML =
               withdrawals_html.innerHTML + '<br><div id="user_info"> <b>Пользователь</b> ' +
               '<br>Фамилия: ' + surname + '<br>Имя: ' + name + '<br>Отчество: ' + patronymic + '<br>Номер телефона: ' + phone_number
               + '</div>';
       }

       //Заполенение данных о статусах заявки
       function inner_statuses(statuses, withdrawal_id, withdrawal_type) {
           withdrawals_html.innerHTML = withdrawals_html.innerHTML + '<br><div id="statuses_info_'+ withdrawal_type + '_' + withdrawal_id +'"></div><hr>';
           var statuses_html = document.getElementById('statuses_info_'+ withdrawal_type + '_' + withdrawal_id);

           statuses.forEach(function (status) {
               statuses_html.innerHTML = statuses_html.innerHTML +
                       ' <input id="'+ status.id +'" type="button" value="' + status.name + '" onclick="changeStatus(this.parentNode.id, this.id);" />';
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
               success: function(){
                   getWithdrawals(last_state);
               }
           });
       }

       //Автоообновление данных каждую минуту
       setInterval(function() {
           getWithdrawals(last_state);
           console.log('Вызов автообновления!');
       }, 1000 * 60);

   </script>
@stop