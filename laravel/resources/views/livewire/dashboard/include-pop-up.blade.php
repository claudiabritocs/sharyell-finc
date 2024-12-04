<div class="pop_up_earn" style="display:none" x-show="pop_earn" x-on:click.outside="pop_earn = false">
    <form id="earn_form" action="{{route('dashboard-earn')}}" method="POST">
        @csrf
        <div class="over">
            <h1 class="title">Nova Entrada</h1>

            <div class="checker">
                <input type="checkbox" value="1" name="remember" id="remember"
                    class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 
                    dark:focus:ring-teal-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-200 
                    dark:border-gray-600">

                <label for="remember" class="label_default">Entrada recorrente</label>
            </div>
        </div>

        <div class="fields">
            <img class="closer" src="{{asset('img/layout/close.svg')}}" alt="fechar" x-on:click="pop_earn = false">

            <select class="select_default" name='category_id'>
                <option value="0" selected disabled>Categoria</option>
                @foreach ($categories as $cat)    
                <option value="{{$cat->id}}">{{$cat->titulo}}</option>
                @endforeach
            </select>
        
            <input class="input_text" type="text" placeholder="Nome do estabelecimento" name='descricao'>
        
            <div class="number_live">
                <input class="input_number" type="text" placeholder="R$" name="value" 
                id="currency" data-prefix="R$ " data-thousands="." data-decimal=",">
            </div>

            <input class="date_input" type="date" name='date'>
        </div>
        
        <button class="btn_create_negativo" type="submit" form="earn_form">Enviar</button>
    </form>
</div>
