<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('contact');
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email',
            'subject'         => 'required|string|max:255',
            'message'         => 'required|string',
            'website'         => 'prohibited', // honeypot
            'recaptcha_token' => 'required|string',
        ]);

        // Verifikasi reCAPTCHA
        $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => env('RECAPTCHA_SECRET_KEY'),
            'response' => $validated['recaptcha_token'],
            'remoteip' => $request->ip(),
        ])->json();

        if (!($verify['success'] ?? false) || ($verify['score'] ?? 0) < 0.5) {
            return response()->json(['message' => 'reCAPTCHA verification failed'], 422);
        }

        // Batasi pengiriman (maks 5x/jam)
        $key = 'contact:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['message' => 'Too many requests, try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        // Kirim email
        Mail::to(env('MAIL_FROM_ADDRESS'))->send(new ContactMail($validated));

        return response()->json(['message' => 'Message sent successfully!']);
    }
}
