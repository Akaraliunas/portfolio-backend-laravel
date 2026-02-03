<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $key = 'contact-form:' . $request->ip();

        if (!empty($request->website)) {
            return response()->json(['message' => 'Message sent!'], 201); // Fake success
        }

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many contact form submissions. Please try again later.',
            ], 429);
        }

        // Merge default subject if not provided by the frontend
        $data = $request->all();
        if (empty($data['subject'])) {
            $data['subject'] = 'Portfolio Inquiry from ' . ($data['name'] ?? 'Guest');
        }

        // Validate the merged data
        $validator = Validator::make($data, [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            // 1. Save to Database
            ContactMessage::create($validated);

            // Mark hit only on successful validation to prevent lockouts from typos
            RateLimiter::hit($key, 3600);

            // 2. TODO: Queue an Email
            // Mail::to('aivaras@karaliunas.dev')->queue(new ContactFormMailable($validated));

            return response()->json([
                'message' => 'Message sent successfully!',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save message.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
