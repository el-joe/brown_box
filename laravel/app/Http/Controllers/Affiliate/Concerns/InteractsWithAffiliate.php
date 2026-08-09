<?php

namespace App\Http\Controllers\Affiliate\Concerns;

use App\Models\Affiliate;
use Illuminate\Support\Facades\Auth;

trait InteractsWithAffiliate
{
    protected function affiliate(): Affiliate
    {
        return Auth::guard('affiliate')->user()->affiliate;
    }
}
