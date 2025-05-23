<?php

    namespace App\Services;



    use Illuminate\Support\Facades\Http;

    class WhisperService
    {
        public static function transcribe(string $audioPath): ?string
        {
            $file = storage_path("app/public/{$audioPath}");

            \Log::info('➡️ WhisperService: tentando transcrever', [
                'caminho' => $file,
                'existe' => file_exists($file),
                'tamanho' => file_exists($file) ? filesize($file) : 'arquivo não encontrado',
            ]);

            $response = Http::withToken(config('services.openai.key'))
                ->attach('file', file_get_contents($file), 'audio.webm')
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => 'pt',
                ]);

            if ($response->successful()) {
                \Log::info('✅ WhisperService: transcrição obtida com sucesso');
                return $response->json('text');
            }

            \Log::error('❌ WhisperService: falha na transcrição', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

    }
