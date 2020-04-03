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
                        <table id="dataTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Дата выплаты</th>
                                <th>Номер транзакции</th>
                                <th>Номер карты</th>
                                <th>Статус</th>
                                <th>Выплаченная сумма</th>
                            </tr>
                            </thead>

                            @foreach ($topUps as $topUp)
                                <tr>
                                    <td>{{ $loop->index }}</td>
                                    <td>{{ $topUp->created_at }}</td>
                                    <td>{{ $topUp->transaction_number }}</td>
                                    <td>{{ $topUp->card_number }}</td>
                                    @switch($topUp->status)
                                        @case(50)
                                        <td>Платеж принят в обработку</td>
                                        @break
                                        @case(52)
                                        <td>Средства зачисляются на счет Клиента</td>
                                        @break
                                        @case(60)
                                        <td>Платеж проведен</td>
                                        @break
                                        @case(150)
                                        <td>Платеж не принят</td>
                                        @break
                                        @case(160)
                                        <td>Платеж не проведен или отменен</td>
                                        @break
                                        @default
                                        <td>Платеж принят в обработку</td>
                                        @break
                                    @endswitch

                                    <td>{{ $topUp->sum }} руб</td>
                                </tr>
                            @endforeach

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $topUps->links() }}
@stop
