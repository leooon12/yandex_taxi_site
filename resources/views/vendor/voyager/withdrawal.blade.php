@extends('voyager::master')
@section('content')
   Документация: <a href="https://yandextaxi.docs.apiary.io/#reference/-/0/get">все необходимые роуты на получение данных</a>

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

   <script>
       $.get("/api/api/withdrawal/all", function(data, status){
           alert("Data: " + data + "\nStatus: " + status);
       });
   </script>

@stop