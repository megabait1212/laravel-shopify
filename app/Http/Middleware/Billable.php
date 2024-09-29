<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;
use Osiset\ShopifyApp\Util;
use Symfony\Component\HttpFoundation\Response;

class Billable
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Util::getShopifyConfig('billing_enabled')) {
            return $next($request);
        }

        if (!Util::useNativeAppBridge() && !$request->ajax()) {
            return $next($request);
        }

        /** @var $shop IShopModel */
        $shop = auth()->user();
        if (!$shop->plan && !$shop->isFreemium() && !$shop->isGrandfathered() && $request->ajax()) {
            // They're not grandfathered in, and there is no charge or charge was declined... redirect to billing
            $redirectUrl = route(
                Util::getShopifyConfig('route_names.billing'),
                array_merge($request->input(), [
                    'shop' => $shop->getDomain()->toNative(),
                    'host' => $request->get('host'),
                ])
            );
            return response()->json(['forceRedirectUrl' => $redirectUrl], 403);
        }

        return $next($request);
    }
}
