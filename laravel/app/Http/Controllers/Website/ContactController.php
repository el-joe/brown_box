<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('website.pages.contact', [
            'contactEmail' => Setting::query()->where('key', 'contact_email')->value('value') ?: 'support@brownbox.test',
            'contactPhone' => Setting::query()->where('key', 'contact_phone')->value('value'),
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        // No dedicated ContactMessage model/mailable exists yet in this project.
        // The submission is logged so support staff can review it, and we make
        // a best-effort attempt to email the configured contact address.
        Log::info('Website contact form submission', $data);

        $to = Setting::query()->where('key', 'contact_email')->value('value');

        if ($to) {
            try {
                Mail::raw(
                    "Name: {$data['name']}\nEmail: {$data['email']}\nSubject: {$data['subject']}\n\n{$data['message']}",
                    function ($mail) use ($to, $data): void {
                        $mail->to($to)
                            ->subject('New Contact Message: '.$data['subject'])
                            ->replyTo($data['email'], $data['name']);
                    }
                );
            } catch (\Throwable $e) {
                Log::warning('Failed to send contact form email: '.$e->getMessage());
            }
        }

        return response()->json([
            'message' => __('website.contact_success'),
        ]);
    }
}
