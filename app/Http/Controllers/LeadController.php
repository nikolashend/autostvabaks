<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Mail\NewLeadMail;
use App\Models\Lead;
use App\Support\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LeadController extends Controller
{
    /**
     * Final step of the two-step form ("Saada päring"):
     * validate → save → store photos → notify by e-mail → confirm to the user.
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $success = [
            'ok' => true,
            'title' => __('site.modal.success_title'),
            'message' => __('site.modal.success_text'),
        ];

        // Honeypot: real visitors never see or fill this field.
        if (filled($request->input('website'))) {
            return response()->json($success);
        }

        $data = $request->validated();

        $lead = Lead::create([
            'reg_number' => $data['reg_number'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'desired_price' => $data['desired_price'] ?? null,
            'language' => $data['language'],
            'mileage' => $data['mileage'] ?? null,
            'info' => $data['info'] ?? null,
            'locale' => app()->getLocale(),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'referrer' => Str::limit((string) $request->input('referrer'), 500, '') ?: null,
        ]);

        $paths = [];
        foreach ($request->file('photos', []) as $i => $photo) {
            $name = sprintf('%02d-%s.%s', $i + 1, Str::random(12), $photo->extension() ?: 'jpg');
            $paths[] = $photo->storeAs($lead->photoDirectory(), $name, 'local');
        }

        if ($paths) {
            $lead->update(['photos' => $paths]);
        }

        // The lead is already saved; a mail failure must not lose it or break the form.
        try {
            Mail::to(Site::setting('leads_email', config('autostvabaks.leads.mail_to')))
                ->send(new NewLeadMail($lead));

            $lead->update(['mail_sent_at' => now()]);
        } catch (Throwable $e) {
            Log::error('Lead notification e-mail failed', ['lead' => $lead->id, 'error' => $e->getMessage()]);
            $lead->update(['mail_error' => Str::limit($e->getMessage(), 500, '')]);
        }

        return response()->json($success);
    }

    /** Admin-only: serves a lead photo from the private disk. */
    public function photo(Request $request, Lead $lead, int $index): StreamedResponse
    {
        $path = ($lead->photos ?? [])[$index] ?? null;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'private, max-age=3600',
        ], $request->boolean('download') ? 'attachment' : 'inline');
    }
}
