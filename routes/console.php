<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sitemap:generate', function () {
    $count = app(\App\Services\V1\Core\SitemapService::class)->generate();
    $this->info("Đã ghi public/sitemap.xml: {$count} URL");
})->purpose('Sinh lại sitemap.xml từ sản phẩm, danh mục, bài viết đang hiển thị');
