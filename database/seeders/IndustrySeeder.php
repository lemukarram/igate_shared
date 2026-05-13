<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Industry;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            ['name' => 'Technology', 'name_ar' => 'التقنية'],
            ['name' => 'Retail', 'name_ar' => 'التجزئة'],
            ['name' => 'Manufacturing', 'name_ar' => 'التصنيع'],
            ['name' => 'Construction', 'name_ar' => 'المقاولات والإنشاءات'],
            ['name' => 'Healthcare', 'name_ar' => 'الرعاية الصحية'],
            ['name' => 'Finance', 'name_ar' => 'المالية'],
            ['name' => 'Education', 'name_ar' => 'التعليم'],
            ['name' => 'Hospitality', 'name_ar' => 'الضيافة'],
            ['name' => 'Logistics', 'name_ar' => 'الخدمات اللوجستية'],
            ['name' => 'Consulting', 'name_ar' => 'الاستشارات'],
            ['name' => 'Real Estate', 'name_ar' => 'العقارات'],
            ['name' => 'Other', 'name_ar' => 'أخرى'],
        ];

        foreach ($industries as $industry) {
            Industry::updateOrCreate(['name' => $industry['name']], $industry);
        }
    }
}
