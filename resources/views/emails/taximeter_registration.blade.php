<!DOCTYPE html>
<html>
<head>
    <title>Новая регистрация водителя</title>
</head>

<body>
<h2>Новая регистрация водителя</h2>

ФИО: {{ $driver_info->name }} {{ $driver_info->patronymic }} {{ $driver_info->surname }} <br>
Серия прав: {{ $driver_info->document_serial_number }} <br>
Номер прав: {{ $driver_info->document_uniq_number }} <br>
Дата выдачи прав: {{ $driver_info->document_issue_date }} <br>
Дата окончания действия прав: {{ $driver_info->document_end_date }} <br>
Страна, выдавшая права: {{ $driver_info->document_country }} <br>
Марка автомобиля: {{ $driver_info->car_brand }} <br>
Модель автомобиля: {{ $driver_info->car_model }} <br>
Год выпуска автомобиля: {{ $driver_info->car_creation_year }} <br>
Цвет автомобиля: {{ $driver_info->car_color }} <br>
Гос.номер автомобиля: {{ $driver_info->car_gov_number }} <br>
Серия/номер СТС: {{ $driver_info->car_reg_sertificate }} <br>

</body>

</html>