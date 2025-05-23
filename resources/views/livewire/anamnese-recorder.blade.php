<div id="anamnese-root" class="space-y-4">
    <div class="flex gap-2">
        <x-button color="success" wire:click="startRecording">
            🎤 Iniciar Gravação
        </x-button>
        <x-button color="error" wire:click="stopRecording">
            🛑 Finalizar
        </x-button>
    </div>

    <audio id="audioPlayback" controls class="mt-4 hidden"></audio>

    <div wire:loading wire:target="processAudio">
        ⏳ Processando áudio e gerando anamnese...
    </div>

    @if ($gravando)
        <div class="flex items-center gap-2 text-error font-semibold animate-pulse">
            <div class="w-3 h-3 bg-error rounded-full animate-ping"></div>
            Gravando...
        </div>
    @endif

    @if ($anamnese)
        <div class="mt-6 p-4 border rounded bg-base-200">
            <h3 class="font-bold mb-2">🧠 Anamnese Gerada:</h3>
            <textarea class="textarea textarea-bordered w-full" rows="10">{{ $anamnese }}</textarea>
        </div>
    @endif
</div>
