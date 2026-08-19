<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = Subscriber::query()->firstOrNew(['email' => $data['email']]);

        if ($subscriber->exists && $subscriber->is_active) {
            return response()->json(['message' => __('website.newsletter_already_subscribed')]);
        }

        $subscriber->is_active = true;
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return response()->json(['message' => __('website.newsletter_success')]);
    }
}
