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

    @foreach ($topUps as $topUp)
        <div class="withdrawal-info">
            <p class="title">Информация</p>
            Дата выплаты: <b>{{ $topUp->created_at }}</b></br>
            Номер транзакции: <b>{{ $topUp->transaction_number }}</b><br>
            Номер карты: <b>{{ $topUp->card_number }}</b><br>
            Сумма к выплате: {{ $topUp->sum }} руб<br><br>
            <input class="topUp" value="Проверить статус" type="button" onclick="">
        </div>
    @endforeach

    <br>
    {{ $topUps->links() }}
@stop