<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArticleCategory;
use App\Models\Article;
use App\Models\Service;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Banner;
use App\Models\TeamMember;
use App\Models\JobOffer;
use App\Models\JobApplication;
use App\Models\Testimonial;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\CustomPage;
use App\Models\Statistique;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ArticleCategory::factory()->count(5)->create();

        Article::factory()->count(10)->create([
            'category_id' => $categories->random()->id,
        ]);

        Service::factory()->count(6)->create();
        Faq::factory()->count(10)->create();
        Partner::factory()->count(8)->create();
        Banner::factory()->count(3)->create();
        TeamMember::factory()->count(5)->create();

        $offers = JobOffer::factory()->count(3)->create();
        foreach ($offers as $offer) {
            JobApplication::factory()->count(3)->create([
                'job_offer_id' => $offer->id,
            ]);
        }

        Testimonial::factory()->count(10)->create();
        ContactMessage::factory()->count(10)->create();
        NewsletterSubscriber::factory()->count(10)->create();
        CustomPage::factory()->count(3)->create();
        Statistique::factory()->count(5)->create();
    }
}
