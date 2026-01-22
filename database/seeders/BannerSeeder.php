<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $bannerKeys = [
            "About Us",
            "CZM Governance",
            "Accountability",
            "Zakat in Quran",
            "Zakat in Hadith",
            "Personal Zakat",
            "Business Zakat",
            "Ushar/ Zakat on Agriculture",
            "Sadaqah",
            "Waqf/ Cash Waqf",
            "Video & Lectures",
            "Programs",
            "Campaigns",
            "News",
            "Success Stories",
            "Blogs & Articles",
            "Career",
            "Contact Us",
            "Photo Gallery",
            "Video Gallery",
            "Announcement",
            "Publication",
            "Audit Report",
            "Book",
            "Report",
            "Newsletter",
            "Zakat Calculator",
            "Sadaqah",
            "Waqf/ Cash Waqf",
            "Videos and Lectures",
            "Qard Al Hasan"
        ];

        DB::table('banners')->truncate();

        foreach ($bannerKeys as $key) {
            Banner::firstOrCreate(['key' => $key]);
        }
    }
}
