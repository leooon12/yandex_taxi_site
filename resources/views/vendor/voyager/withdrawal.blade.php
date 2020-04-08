@extends('voyager::master')
@section('content')
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


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="table-responsive">
                        @switch($type)
                            @case(\App\TopUpWithdrawal::BANK_CARD_WITHDRAWAL_TYPE)
                                @php
                                    $type_name       = "Банковская карта";
                                    $model_name      = "WithdrawalBankCard";
                                    $requisites_name = "Номер карты";
                                @endphp
                            @break
                            @case(\App\TopUpWithdrawal::QIWI_WITHDRAWAL_TYPE)
                                @php
                                    $type_name       = "Киви";
                                    $model_name      = "WithdrawalQiwi";
                                    $requisites_name = "Номер кошелька";
                                @endphp
                            @break
                        @endswitch
                                <div class="withdrawal-info">
                                    <p class="title">Выплата на {{ $type_name }}</p>
                                    Статус:
                                    @switch($withdrawal->status_id)
                                        @case(\App\WithdrawalStatus::WAITING_FOR_CONFIRMATION)
                                        <b class="waiting">ожидает подтверждения</b><br>
                                        @break
                                        @case(\App\WithdrawalStatus::COMPLETED)
                                        <b class="success">выполнен</b><br>
                                        @break
                                        @case(\App\WithdrawalStatus::CANCELED)
                                        <b class="error">отклонен</b><br>
                                        @break
                                        @case(\App\WithdrawalStatus::IN_WORK)
                                        <b class="waiting">в обработке</b><br>
                                        @break
                                    @endswitch
                                    Дата создания: <b>{{ $withdrawal->created_at }}</b><br><br>
                                    Реквизиты и суммы<br>{{ $requisites_name }}: <b>{{ $withdrawal->card_number }}</b><br>
                                    Сумма: <b>{{ $withdrawal->sum }} руб</b><br>Комиссия: <b>{{ \App\WithdrawalBankCard::COMMISSION}} руб</b><br>
                                    Сумма к выплате: {{ $withdrawal->sum - \App\WithdrawalBankCard::COMMISSION}} руб<br><br>
                                    Пользователь<br>
                                    Номер телефона: <b>{{ $withdrawal->user->phone_number }}</b><br><br>
                                    <input class="waiting" value="ожидает подтверждения" type="button" onclick="changeStatus('{{ $model_name }}', '{{ $withdrawal->id }}', '{{\App\WithdrawalStatus::WAITING_FOR_CONFIRMATION}}');">
                                    <input class="success" value="выполнен" type="button" onclick="changeStatus('{{ $model_name }}', '{{ $withdrawal->id }}', '{{\App\WithdrawalStatus::COMPLETED}}');">
                                    <input class="error" value="отклонен" type="button" onclick="changeStatus('{{ $model_name }}', '{{ $withdrawal->id }}', '{{\App\WithdrawalStatus::CANCELED}}');">
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

<script>
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
                location.reload();
            }
        });
    }
</script>
