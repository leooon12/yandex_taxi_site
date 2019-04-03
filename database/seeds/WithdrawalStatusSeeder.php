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
        $statuses_names = [['name'=>'в обработке'], ['name'=>'выполнен'], ['name'=>'отклонен']];
        \App\WithdrawalStatus::insert($statuses_names);
    }
}
