<?php

namespace App\Http\Middleware;

use App\Models\EstateModFeature;
use App\Models\ModFeature;
use App\Services\StandardResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $featureSlug = $request->route()->getAction('defaults')['feature'] ?? null;

        if (!$featureSlug) {
            return $next($request); // no feature restriction
        }

        $feature = ModFeature::where('slug', $featureSlug)->first();
        $estateFeature = EstateModFeature::where('mod_feature_id', $feature?->id)->first();

        if (!$feature || !$feature->isAvailable()) {
            return StandardResponse::error(403, 'Feature not available', []);
        }

        if (!$estateFeature || !$estateFeature?->isAvailable()) {

            return StandardResponse::error(403, 'Your Estate is not Subscribed To This Feature', []);
        }

        return $next($request);
    }
}
