<?php   
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\V1\LanguageController;
use App\Http\Controllers\Backend\V1\SystemController;
use App\Http\Controllers\Backend\V1\IntroduceController;
use App\Http\Controllers\Backend\V1\ReviewController;
use App\Http\Controllers\Backend\V1\MenuController;
use App\Http\Controllers\Backend\V1\SlideController;
use App\Http\Controllers\Backend\V1\WidgetController;
use App\Http\Controllers\Backend\V1\ReportController;
use App\Http\Controllers\Backend\V1\ContactController;
use App\Http\Controllers\Backend\V1\LecturerController;
use App\Http\Controllers\Backend\V1\DistributionAreaController;
use App\Http\Controllers\Backend\V1\DistributionController;



Route::group(['middleware' => ['admin','locale','backend_default_locale']], function () {

    Route::group(['prefix' => 'language'], function () {
        Route::get('index', [LanguageController::class, 'index'])->name('language.index')->middleware(['admin','locale']);
        Route::get('create', [LanguageController::class, 'create'])->name('language.create');
        Route::post('store', [LanguageController::class, 'store'])->name('language.store');
        Route::get('{id}/edit', [LanguageController::class, 'edit'])->where(['id' => '[0-9]+'])->name('language.edit');
        Route::post('{id}/update', [LanguageController::class, 'update'])->where(['id' => '[0-9]+'])->name('language.update');
        Route::get('{id}/delete', [LanguageController::class, 'delete'])->where(['id' => '[0-9]+'])->name('language.delete');
        Route::delete('{id}/destroy', [LanguageController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('language.destroy');
        Route::get('{id}/switch', [LanguageController::class, 'swicthBackendLanguage'])->where(['id' => '[0-9]+'])->name('language.switch');
        Route::get('{id}/{languageId}/{model}/translate', [LanguageController::class, 'translate'])->where(['id' => '[0-9]+', 'languageId' => '[0-9]+'])->name('language.translate');
        Route::post('storeTranslate', [LanguageController::class, 'storeTranslate'])->name('language.storeTranslate');
    });

    Route::group(['prefix' => 'system'], function () {
        Route::get('index', [SystemController::class, 'index'])->name('system.index');
        Route::post('store', [SystemController::class, 'store'])->name('system.store');
        Route::get('{languageId}/translate', [SystemController::class, 'translate'])->where(['languageId' => '[0-9]+'])->name('system.translate');
        Route::post('{languageId}/saveTranslate', [SystemController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('system.save.translate');
    });


    Route::group(['prefix' => 'introduce'], function () {
        Route::get('index', [IntroduceController::class, 'index'])->name('introduce.index');
        Route::post('store', [IntroduceController::class, 'store'])->name('introduce.store');
        Route::get('{languageId}/translate', [IntroduceController::class, 'translate'])->where(['languageId' => '[0-9]+'])->name('introduce.translate');
        Route::post('{languageId}/saveTranslate', [IntroduceController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('introduce.save.translate');
    });

    Route::group(['prefix' => 'review'], function () {
        Route::get('index', [ReviewController::class, 'index'])->name('review.index');
        Route::get('{id}/delete', [ReviewController::class, 'delete'])->where(['id' => '[0-9]+'])->name('review.delete');
        Route::delete('{id}/destroy', [ReviewController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('review.destroy');
        
    });

    Route::group(['prefix' => 'menu'], function () {
        Route::get('index', [MenuController::class, 'index'])->name('menu.index');
        Route::get('create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('store', [MenuController::class, 'store'])->name('menu.store');
        Route::get('{id}/edit', [MenuController::class, 'edit'])->where(['id' => '[0-9]+'])->name('menu.edit');
        Route::get('{id}/editMenu', [MenuController::class, 'editMenu'])->where(['id' => '[0-9]+'])->name('menu.editMenu');
        Route::post('{id}/update', [MenuController::class, 'update'])->where(['id' => '[0-9]+'])->name('menu.update');
        Route::get('{id}/delete', [MenuController::class, 'delete'])->where(['id' => '[0-9]+'])->name('menu.delete');
        Route::delete('{id}/destroy', [MenuController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('menu.destroy');
        Route::get('{id}/children', [MenuController::class, 'children'])->where(['id' => '[0-9]+'])->name('menu.children');
        Route::post('{id}/saveChildren', [MenuController::class, 'saveChildren'])->where(['id' => '[0-9]+'])->name('menu.save.children');
        Route::get('{languageId}/{id}/translate', [MenuController::class, 'translate'])->where(['languageId' => '[0-9]+', 'id' => '[0-9]+'])->name('menu.translate');
        Route::post('{languageId}/saveTranslate', [MenuController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('menu.translate.save');
    });


    Route::group(['prefix' => 'slide'], function () {
        Route::get('index', [SlideController::class, 'index'])->name('slide.index');
        Route::get('create', [SlideController::class, 'create'])->name('slide.create');
        Route::post('store', [SlideController::class, 'store'])->name('slide.store');
        Route::get('{id}/edit', [SlideController::class, 'edit'])->where(['id' => '[0-9]+'])->name('slide.edit');
        Route::post('{id}/update', [SlideController::class, 'update'])->where(['id' => '[0-9]+'])->name('slide.update');
        Route::get('{id}/delete', [SlideController::class, 'delete'])->where(['id' => '[0-9]+'])->name('slide.delete');
        Route::delete('{id}/destroy', [SlideController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('slide.destroy');
    });

    Route::group(['prefix' => 'widget'], function () {
        Route::get('index', [WidgetController::class, 'index'])->name('widget.index');
        Route::get('create', [WidgetController::class, 'create'])->name('widget.create');
        Route::post('store', [WidgetController::class, 'store'])->name('widget.store');
        Route::get('{id}/edit', [WidgetController::class, 'edit'])->where(['id' => '[0-9]+'])->name('widget.edit');
        Route::post('{id}/update', [WidgetController::class, 'update'])->where(['id' => '[0-9]+'])->name('widget.update');
        Route::get('{id}/delete', [WidgetController::class, 'delete'])->where(['id' => '[0-9]+'])->name('widget.delete');
        Route::delete('{id}/destroy', [WidgetController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('widget.destroy');
        Route::get('{languageId}/{id}/translate', [WidgetController::class, 'translate'])->where(['id' => '[0-9]+', 'languageId' => '[0-9]+'])->name('widget.translate');
        Route::post('saveTranslate', [WidgetController::class, 'saveTranslate'])->name('widget.saveTranslate');
    });

    Route::group(['prefix' => 'report'], function () {
        Route::get('time', [ReportController::class, 'time'])->name('report.time');
        Route::get('product', [ReportController::class, 'product'])->name('report.product');
        Route::get('customer', [ReportController::class, 'customer'])->name('report.customer');
    });

    Route::group(['prefix' => 'contact'], function () {
        Route::get('index', [ContactController::class, 'index'])->name('contact.index');
        Route::get('{id}/delete', [ContactController::class, 'delete'])->where(['id' => '[0-9]+'])->name('contact.delete');
        Route::delete('{id}/destroy', [ContactController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('contact.destroy');
    });
   
    Route::group(['prefix' => 'lecturer'], function () {
        Route::get('index', [LecturerController::class, 'index'])->name('lecturer.index');
        Route::get('create', [LecturerController::class, 'create'])->name('lecturer.create');
        Route::post('store', [LecturerController::class, 'store'])->name('lecturer.store');
        Route::get('{id}/edit', [LecturerController::class, 'edit'])->where(['id' => '[0-9]+'])->name('lecturer.edit');
        Route::post('{id}/update', [LecturerController::class, 'update'])->where(['id' => '[0-9]+'])->name('lecturer.update');
        Route::get('{id}/delete', [LecturerController::class, 'delete'])->where(['id' => '[0-9]+'])->name('lecturer.delete');
        Route::delete('{id}/destroy', [LecturerController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('lecturer.destroy');
    });
    
    Route::group(['prefix' => 'introduce'], function () {
        Route::get('index', [IntroduceController::class, 'index'])->name('introduce.index');
        Route::get('create', [IntroduceController::class, 'create'])->name('introduce.create');
        Route::post('store', [IntroduceController::class, 'store'])->name('introduce.store');
        Route::get('{id}/edit', [IntroduceController::class, 'edit'])->where(['id' => '[0-9]+'])->name('introduce.edit');
        Route::post('{id}/update', [IntroduceController::class, 'update'])->where(['id' => '[0-9]+'])->name('introduce.update');
        Route::get('{id}/delete', [IntroduceController::class, 'delete'])->where(['id' => '[0-9]+'])->name('introduce.delete');
        Route::delete('{id}/destroy', [IntroduceController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('introduce.destroy');
    });

    Route::group(['prefix' => 'distribution/area'], function () {
        Route::get('index', [DistributionAreaController::class, 'index'])->name('distribution.area.index');
        Route::get('create', [DistributionAreaController::class, 'create'])->name('distribution.area.create');
        Route::post('store', [DistributionAreaController::class, 'store'])->name('distribution.area.store');
        Route::get('{id}/edit', [DistributionAreaController::class, 'edit'])->where(['id' => '[0-9]+'])->name('distribution.area.edit');
        Route::post('{id}/update', [DistributionAreaController::class, 'update'])->where(['id' => '[0-9]+'])->name('distribution.area.update');
        Route::get('{id}/delete', [DistributionAreaController::class, 'delete'])->where(['id' => '[0-9]+'])->name('distribution.area.delete');
        Route::delete('{id}/destroy', [DistributionAreaController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('distribution.area.destroy');
        Route::get('getArea', [DistributionAreaController::class, 'getArea'])->name('ajax.distribution.getArea');
    });

    Route::group(['prefix' => 'distribution'], function () {
        Route::get('index', [DistributionController::class, 'index'])->name('distribution.index');
        Route::get('create', [DistributionController::class, 'create'])->name('distribution.create');
        Route::post('store', [DistributionController::class, 'store'])->name('distribution.store');
        Route::get('{id}/edit', [DistributionController::class, 'edit'])->where(['id' => '[0-9]+'])->name('distribution.edit');
        Route::post('{id}/update', [DistributionController::class, 'update'])->where(['id' => '[0-9]+'])->name('distribution.update');
        Route::get('{id}/delete', [DistributionController::class, 'delete'])->where(['id' => '[0-9]+'])->name('distribution.delete');
        Route::delete('{id}/destroy', [DistributionController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('distribution.destroy');
    });

});

