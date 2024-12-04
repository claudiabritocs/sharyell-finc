<div class="pop_up_goal" style="display:none" x-show="pop_goal" x-on:click.outside="pop_goal = false">
    <form id="goal_form" action="{{route('dashboard-goal')}}" method="POST">
        @csrf
        <div class="over">
            <h1 class="title">Nova Meta de Gasto Livre do Mês</h1>
        </div>

        <div class="fields">
            <img class="closer" src="{{asset('img/layout/close.svg')}}" alt="fechar" x-on:click="pop_goal = false">
        
            <div class="number_live">
                <input class="input_number" type="text" placeholder="R$" name="goal_spend" 
                id="currency_goal" data-prefix="R$ " data-thousands="." data-decimal=",">
            </div>
        </div>
        
        <button class="btn_create_negativo" type="submit" form="goal_form">Enviar</button>
    </form>
</div>

