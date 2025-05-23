<?php

namespace App\Livewire;

use App\Services\WhisperService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AnamneseRecorder extends Component
{
    use WithFileUploads;

    public $audio;
    public $gravando = false;
    public $anamnese = null;

    public function startRecording()
    {
        $this->reset(['audio', 'anamnese']);
        $this->gravando = true;
        $this->js(<<<'JS'
            window.mediaRecorder = null;
            let audioChunks = [];

            navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = e => {
                    if (e.data.size > 0) audioChunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    const blob = new Blob(audioChunks, { type: 'audio/webm' });
                    const file = new File([blob], "anamnese.webm", { type: 'audio/webm' });

                    const componentId = document.getElementById('anamnese-root')?.getAttribute('wire:id');

                    if (!componentId) {
                        console.error('wire:id não encontrado no componente.');
                        return;
                    }

                    Livewire.find(componentId).upload('audio', file, () => {
                        console.log('upload ok');
                    }, () => {
                        console.error('erro no upload');
                    });

                    const url = URL.createObjectURL(blob);
                    const audio = document.getElementById('audioPlayback');
                    audio.src = url;
                    audio.classList.remove('hidden');
                };

                mediaRecorder.start();
            });
        JS);
    }

    public function stopRecording()
    {
        $this->gravando = false;
        $this->js(<<<'JS'
            if (window.mediaRecorder && mediaRecorder.state !== "inactive") {
                mediaRecorder.stop();
            }
        JS);
    }

    public function updatedAudio()
    {
        $this->processAudio();
    }

    public function processAudio()
    {
        $path = $this->audio->store('anamnese_audios', 'public');

        $transcription = WhisperService::transcribe($path);

        if (!$transcription) {
            $this->anamnese = 'Erro ao transcrever áudio.';
            return;
        }

        $anamneseFinal = \App\Services\GptService::gerarAnamnese($transcription);

        $this->anamnese = $anamneseFinal ?? 'Erro ao gerar anamnese com IA.';
    }

    public function render()
    {
        return view('livewire.anamnese-recorder');
    }
}
