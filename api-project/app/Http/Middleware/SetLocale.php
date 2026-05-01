<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'sv'];

    /**
     * Resolves locale from (in order): ?locale= query → Accept-Language → user's country default → 'en'.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);
        App::setLocale($locale);

        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }

    private function resolve(Request $request): string
    {
        $explicit = $request->query('locale');
        if (is_string($explicit) && in_array($explicit, self::SUPPORTED, true)) {
            return $explicit;
        }

        $header = $request->header('Accept-Language');
        if (is_string($header)) {
            foreach (explode(',', $header) as $part) {
                $code = strtolower(trim(explode(';', $part)[0]));
                $code = substr($code, 0, 2);
                if (in_array($code, self::SUPPORTED, true)) {
                    return $code;
                }
            }
        }

        $user = $request->user();
        if ($user && $user->country) {
            $countryLocale = $user->country->default_locale;
            if (in_array($countryLocale, self::SUPPORTED, true)) {
                return $countryLocale;
            }
        }

        return 'en';
    }
}
