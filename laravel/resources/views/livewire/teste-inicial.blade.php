<div>

    <input type="text" wire:model.live="inputTeste">

    <select wire:model.live='yn'>
        <option value="true">Sim</option>
        <option value="false" selected>Não</option>
    </select>

    <select>
        @if($yn == 'false')
            <option value="0" selected>Aguardando primeiro select</option>
        @else
            <option value="a">a</option>
            <option value="b">b</option>
            <option value="c">c</option>
        @endif
    </select>

    <h2 style="color:white;">
        {{$inputTeste}}
    </h2>

    <button wire:click='teste'>
        Botão {{$eu}}
    </button>
</div>
