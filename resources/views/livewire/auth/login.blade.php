<div class="flex justify-center" >
    <x-card class="w-1/2">
        <x-header :title="$title" separator progress-indicator>
        </x-header>
        <x-form wire:submit="authenticate">
            <x-input label="E-mail" icon="o-envelope" wire:model="email" />
            <x-input label="Senha" wire:model="password" icon="o-key" type="password" />

            <x-slot:actions>
                <x-button label="Entrar" class="btn-success" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>

</div>
