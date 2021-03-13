<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = new App\Setting();
        $setting->name = 'commission';
        $setting->value = '0.001';
        $setting->save();
    }
}
