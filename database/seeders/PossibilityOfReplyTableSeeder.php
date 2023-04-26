<?php

namespace Database\Seeders;

use App\Models\PossibilityOfReply;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PossibilityOfReplyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        PossibilityOfReply::truncate();

        PossibilityOfReply::create([
            'id' => 1 ,
            'name_ar' => 'تم الرد',
            'name_en' => 'Answerd'
        ]);

        PossibilityOfReply::create([
            'id' => 2 ,
            'name_ar' => 'لم يرد',
            'name_en' => 'No Answer'
        ]);

        PossibilityOfReply::create([
            'id' => 3 ,
            'name_ar' => 'مشغول (غير متاح الان)',
            'name_en' => 'Busy (Not avilable at the moment)'
        ]);

        PossibilityOfReply::create([
            'id' => 4 ,
            'name_ar' => 'مغلق أو غير متاح',
            'name_en' => 'NOt avilable'
        ]);

        PossibilityOfReply::create([
            'id' => 5 ,
            'name_ar' => 'الرقم خطأ',
            'name_en' => 'Worng number'
        ]);

        PossibilityOfReply::create([
            'id' => 6 ,
            'name_ar' => 'يحتاج إلي متابعة',
            'name_en' => 'Needs follow-up'
        ]);
    }
}
