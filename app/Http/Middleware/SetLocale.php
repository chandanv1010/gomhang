<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use App\Models\Language;


class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('app_locale', config('app.locale')); 
        App::setLocale($locale);

        $languageId = cache()->rememberForever('language_id_' . $locale, function () use ($locale) {
            return Language::where('canonical', $locale)->value('id') ?? 1;
        });

        Config::set('app.language_id', $languageId);
        
        return $next($request);
    }
}
