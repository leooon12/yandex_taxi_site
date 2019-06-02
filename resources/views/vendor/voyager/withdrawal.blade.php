@extends('voyager::master')
@section('content')

   <div>

       <p>Документация: <a href="https://yandextaxi.docs.apiary.io/#reference/-/0/get">все необходимые роуты на получение данных</a></p>

       <div>
           <input id="clickMe" type="button" value="Заявки в обработкe" onclick="getWithdrawals('in_work');" />
           <input id="clickMe" type="button" value="Все заявки" onclick="getWithdrawals('all');" />

       </div>
       <div id="withdrawals"></div>
   </div>

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

   <script>
       getWithdrawals("in_work");

       var withdrawals_html = document.getElementById('withdrawals');

       function getWithdrawals(type) {
           $.get("/api/withdrawal/" + type, function(withdrawals){
               withdrawals_html.innerHTML = "";

               withdrawals.forEach(function (withdrawal) {
                   switch(withdrawal.type) {
                       case "WithdrawalBankAccount":
                           inner_withdrawal_info("Банковский счет", withdrawal.status.name,  withdrawal.created_at);
                           inner_withdrawal_details("Номер счета: ", withdrawal.account_number, withdrawal.sum);
                           inner_user_info(withdrawal.user.surname, withdrawal.user.name, withdrawal.user.patronymic, withdrawal.user.phone_number);
                           break;
                       case "WithdrawalBankCard":
                           inner_withdrawal_info("Банковская карта", withdrawal.status.name,  withdrawal.created_at);
                           inner_withdrawal_details("Номер карты: ", withdrawal.card_number, withdrawal.sum);
                           inner_user_info(withdrawal.user.surname, withdrawal.user.name, withdrawal.user.patronymic, withdrawal.user.phone_number);
                           break;
                       case "WithdrawalYandex":
                           inner_withdrawal_info("Яндекс-деньги", withdrawal.status.name,  withdrawal.created_at);
                           inner_withdrawal_details("Номер кошелька: ", withdrawal.yandex_number, withdrawal.sum);
                           inner_user_info(withdrawal.user.surname, withdrawal.user.name, withdrawal.user.patronymic, withdrawal.user.phone_number);
                           break;
                   }
               });
           });
       }

       function inner_withdrawal_info(type, status, created_at) {
           withdrawals_html.innerHTML = withdrawals_html.innerHTML + '<br><div id="withdrawal_info"><b>Заявка на выплату</b>'+
               '<br>Тип: ' + type + '<br>Статус: ' + status + '<br>Дата создания: ' + created_at
               +'</div>';
       }

       function inner_withdrawal_details(type, requisites, sum) {
           withdrawals_html.innerHTML =
               withdrawals_html.innerHTML + '<br><div id="withdrawal_details"> <b>Реквизиты и суммы</b> ' +
               '<br>' + type + requisites + '<br>Сумма: ' + sum
               + '</div>';
       }

       function inner_user_info(surname, name, patronymic, phone_number) {
           withdrawals_html.innerHTML =
               withdrawals_html.innerHTML + '<br><div id="user_info"> <b>Пользователь</b> ' +
               '<br>Фамилия: ' + surname + '<br>Имя: ' + name + '<br>Отчество: ' + patronymic + '<br>Номер телефона: ' + phone_number
               + '</div><hr>';
       }
   </script>

@stop