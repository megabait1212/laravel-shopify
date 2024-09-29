<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Osiset\ShopifyApp\Util;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessScopes
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->ajax()) {
            return $next($request);
        }

        $shop = $request->user();

        if ($shop->force_scope_update) {
            $scopesResponse = $shop->api()->rest('GET', '/admin/oauth/access_scopes.json');
            if ($scopesResponse && $scopesResponse['errors']) {
                return $next($request);
            }
            $scopes = json_decode(json_encode($scopesResponse['body']['access_scopes']), false);
            $scopes = array_map(static function ($scope) {
                return $scope->handle;
            }, $scopes);

            $requiredScopes = explode(',', Util::getShopifyConfig('api_scopes'));
            $missingScopes = array_diff($requiredScopes, $scopes);
            if (count($missingScopes) === 0) {
                return $next($request);
            }
//            dd($missingScopes);
            return response()->json(
                [
                    'forceRedirectUrl' => route(
                        Util::getShopifyConfig('route_names.authenticate'),
                        [
                            'shop' => $shop->name,
                            'host' => $request->get('host')
                        ]
                    )
                ],
                403
            );
        }
        return $next($request);
    }
}
