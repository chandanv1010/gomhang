<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

     public $bindings = [
        'App\Repositories\Interfaces\CustomerRepositoryInterface' => 'App\Repositories\CustomerRepository',
        'App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface' => 'App\Repositories\CustomerCatalogueRepository',
        'App\Repositories\Interfaces\UserRepositoryInterface' => 'App\Repositories\UserRepository',
        'App\Repositories\Interfaces\UserCatalogueRepositoryInterface' => 'App\Repositories\UserCatalogueRepository',
        'App\Repositories\Interfaces\LanguageRepositoryInterface' => 'App\Repositories\LanguageRepository',
        'App\Repositories\Interfaces\PostCatalogueRepositoryInterface' => 'App\Repositories\PostCatalogueRepository',
        'App\Repositories\Interfaces\GenerateRepositoryInterface' => 'App\Repositories\GenerateRepository',
        'App\Repositories\Interfaces\PermissionRepositoryInterface' => 'App\Repositories\PermissionRepository',
        'App\Repositories\Interfaces\PostRepositoryInterface' => 'App\Repositories\PostRepository',
        'App\Repositories\Interfaces\ProvinceRepositoryInterface' => 'App\Repositories\ProvinceRepository',
        'App\Repositories\Interfaces\DistrictRepositoryInterface' => 'App\Repositories\DistrictRepository',
        'App\Repositories\Interfaces\RouterRepositoryInterface' => 'App\Repositories\RouterRepository',
        'App\Repositories\Interfaces\ProductCatalogueRepositoryInterface' => 'App\Repositories\ProductCatalogueRepository',
        'App\Repositories\Interfaces\ProductRepositoryInterface' => 'App\Repositories\ProductRepository',
        'App\Repositories\Interfaces\ProductCatalogueRepositoryInterface' => 'App\Repositories\ProductCatalogueRepository',
        'App\Repositories\Interfaces\ProductRepositoryInterface' => 'App\Repositories\ProductRepository',
        'App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface' => 'App\Repositories\AttributeCatalogueRepository',
        'App\Repositories\Interfaces\AttributeRepositoryInterface' => 'App\Repositories\AttributeRepository',
        'App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface' => 'App\Repositories\ProductVariantLanguageRepository',
        'App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface' => 'App\Repositories\ProductVariantAttributeRepository',
        'App\Repositories\Interfaces\SystemRepositoryInterface' => 'App\Repositories\SystemRepository',
        'App\Repositories\Interfaces\IntroduceRepositoryInterface' => 'App\Repositories\IntroduceRepository',
        'App\Repositories\Interfaces\MenuCatalogueRepositoryInterface' => 'App\Repositories\MenuCatalogueRepository',
        'App\Repositories\Interfaces\MenuRepositoryInterface' => 'App\Repositories\MenuRepository',
        'App\Repositories\Interfaces\SlideRepositoryInterface' => 'App\Repositories\SlideRepository',
        'App\Repositories\Interfaces\WidgetRepositoryInterface' => 'App\Repositories\WidgetRepository',
        'App\Repositories\Interfaces\PromotionRepositoryInterface' => 'App\Repositories\PromotionRepository',
        'App\Repositories\Interfaces\SourceRepositoryInterface' => 'App\Repositories\SourceRepository',
        'App\Repositories\Interfaces\ProductVariantRepositoryInterface' => 'App\Repositories\ProductVariantRepository',
        'App\Repositories\Interfaces\OrderRepositoryInterface' => 'App\Repositories\OrderRepository',
        'App\Repositories\Interfaces\ReviewRepositoryInterface' => 'App\Repositories\ReviewRepository',
        'App\Repositories\Interfaces\DistributionRepositoryInterface' => 'App\Repositories\Core\DistributionRepository',
        'App\Repositories\Interfaces\DistributionAreaRepositoryInterface' => 'App\Repositories\Core\DistributionAreaRepository',
        'App\Repositories\Interfaces\AgencyRepositoryInterface' => 'App\Repositories\AgencyRepository',
        'App\Repositories\Interfaces\ConstructRepositoryInterface' => 'App\Repositories\ConstructRepository',
        'App\Repositories\Interfaces\VoucherRepositoryInterface' => 'App\Repositories\VoucherRepository',
        'App\Repositories\Interfaces\ContactRepositoryInterface' => 'App\Repositories\ContactRepository',
        'App\Repositories\Interfaces\LecturerRepositoryInterface' => 'App\Repositories\LecturerRepository',
        'App\Repositories\Interfaces\SchoolRepositoryInterface' => 'App\Repositories\SchoolRepository',
        'App\Repositories\Interfaces\SchoolCatalogueRepositoryInterface' => 'App\Repositories\SchoolCatalogueRepository',
        'App\Repositories\Interfaces\ScholarshipRepositoryInterface' => 'App\Repositories\ScholarshipRepository',
        'App\Repositories\Interfaces\ScholarshipCatalogueRepositoryInterface' => 'App\Repositories\ScholarshipCatalogueRepository',
        'App\Repositories\Interfaces\TrainRepositoryInterface' => 'App\Repositories\TrainRepository',
        'App\Repositories\Interfaces\PolicyRepositoryInterface' => 'App\Repositories\PolicyRepository',
        'App\Repositories\Interfaces\AreaRepositoryInterface' => 'App\Repositories\AreaRepository',
        'App\Repositories\Interfaces\CityRepositoryInterface' => 'App\Repositories\CityRepository',
        'App\Repositories\Interfaces\ProjectRepositoryInterface' => 'App\Repositories\ProjectRepository',
    ];

    public function register(): void
    {
        foreach($this->bindings as $key => $val)
        {
            $this->app->bind($key, $val);
        }
    }

    
    public function boot(): void
    {
        
    }
}
