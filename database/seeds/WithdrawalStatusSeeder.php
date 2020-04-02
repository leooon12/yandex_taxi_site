<?php

use Illuminate\Database\Seeder;

class WithdrawalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses_names = [['name'=>'ожидает подтверждения'], ['name'=>'выполнен'], ['name'=>'отклонен'], ['name'=>'в обработке']];
        \App\WithdrawalStatus::insert($statuses_names);
    }
}
