<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $lang = session()->get('lang', 'fr');  // اللغة الافتراضية هي الفرنسية
        App::setLocale($lang);

        return $next($request);
    }
}