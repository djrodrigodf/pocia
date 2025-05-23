<?php

    namespace App\Services;



    class WhisperService
    {
    public static function transcribe(string $audioPath): ?string
    {
    $file = storage_path("app/public/{$audioPath}");

    $response = \Http::withToken(config('services.openai.key'))
    ->attach('file', file_get_contents($file), 'audio.webm')
    ->post('https://api.openai.com/v1/audio/transcriptions', [
    'model' => 'whisper-1',
    'language' => 'pt',
    ]);

    if ($response->successful()) {
    return $response->json('text');
    }

    \Log::error('Whisper transcription failed', [
    'status' => $response->status(),
    'body' => $response->body(),
    ]);

    return null;
    }
    }
