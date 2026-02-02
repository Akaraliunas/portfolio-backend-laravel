<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;

class ContactController
{
    public function store(Request $request): JsonResponse
    {
        $key = 'contact-form:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many contact form submissions. Please try again later.',
            ], 429);
        }

        RateLimiter::hit($key, 3600); // 1 hour window

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // TODO: Configure your email address and Mail driver
            // Mail::send('emails.contact', $validated, function ($message) {
            //     $message->to('your-email@example.com')
            //             ->subject($validated['subject']);
            // });

            return response()->json([
                'message' => 'Message sent successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send message. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
