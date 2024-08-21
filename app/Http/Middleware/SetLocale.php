<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
  public function handle($request, Closure $next)
{
    $locale = substr($request->header('Accept-Language'), 0, 2);
    if (!in_array($locale, ['ar', 'en'])) {
        $locale = 'ar'; 
    }
    App::setLocale($locale);

    return $next($request);
}

}
