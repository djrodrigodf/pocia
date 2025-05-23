<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GptService
{
    public static function gerarAnamnese(string $transcription): ?string
    {
        $prompt = <<<EOT
Você é um fisioterapeuta. Abaixo está a transcrição de uma conversa entre profissional e paciente.
Gere uma anamnese clínica estruturada com:
- Nome e idade do paciente (se mencionado)
- Queixa principal
- História da dor
- Fatores que pioram ou melhoram
- Histórico de tratamentos anteriores
- Uso de medicamentos
- Atividade física atual

Transcrição:
$transcription
EOT;

        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um fisioterapeuta especialista.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 800,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content');
        }

        \Log::error('GPT geração de anamnese falhou', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }
}
