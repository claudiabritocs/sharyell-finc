<?php

namespace App\Livewire\Dashboard;

use App\Models\Categories;
use App\Models\Goal;
use App\Models\Ledger;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $categories;
    public $categories_fixo;
    public $categories_investimentos;
    public $monthSpent;
    public $goals;
    public $goal_percent;

    public $total_free;
    public $total_fixed;
    public $total_all;

    public $start_date;
    public $end_date;
    public $month;

    public $monthNames = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
    ];

    public function mount()
    {
        $user = Auth::user();

        $this->month = date('m'); // Obtém o mês atual

        //GASTOS LIVRES
        $this->categories = Categories::where('categories.user_id', $user->id)
            ->where('categories.type', 'saida')
            ->leftJoin('colors', 'colors.id', '=', 'categories.color')
            ->join('ledger', 'ledger.category_id', '=', 'categories.id')
            ->where('ledger.type', 'livre')
            ->whereMonth('ledger.created_at', '=', $this->month)
            ->select('colors.color as hex', 'ledger.value as value', 'categories.*')
            ->get();

        //GASTOS FIXOS
        $this->categories_fixo = Categories::where('categories.user_id', $user->id)
            ->where('categories.type', 'saida')
            ->leftJoin('colors', 'colors.id', '=', 'categories.color')
            ->join('ledger', 'ledger.category_id', '=', 'categories.id')
            ->where('ledger.type', 'fixo')
            ->whereMonth('ledger.created_at', '=', $this->month)
            ->select('colors.color as hex', 'ledger.value as value','categories.*')
            ->get();

        //GASTOS POR MÊS
        $this->monthSpent = Ledger::where('ledger.user_id', $user->id)
            ->join('categories', 'ledger.category_id', '=', 'categories.id')
            ->where('categories.type', 'saida')
            ->selectRaw('DATE_FORMAT(ledger.created_at, "%m-%Y") as month, SUM(value) as total_spent')
            ->groupBy('month')
            ->get();

        //GASTOS LIVRES TOTAIS
        $this->total_free = Ledger::where('ledger.user_id', $user->id)
            ->where('ledger.type', 'livre')
            ->join('categories', 'categories.id', '=', 'ledger.category_id')
            ->where('categories.type', 'saida')
            ->whereMonth('ledger.created_at', '=', $this->month)
            ->select('ledger.*')
            ->get();
        
        $this->total_free = $this->total_free->sum('value');

        //GASTOS FIXOS TOTAIS
        $this->total_fixed = Ledger::where('ledger.user_id', $user->id)
            ->where('ledger.type', 'fixo')
            ->join('categories', 'categories.id', '=', 'ledger.category_id')
            ->where('categories.type', 'saida')
            ->whereMonth('ledger.created_at', '=', $this->month)
            ->select('ledger.*')
            ->get();
        
        $this->total_fixed = $this->total_fixed->sum('value');

        $this->total_all = $this->total_free + $this->total_fixed;

        //META DE GASTO
        $this->goals = Goal::where('user_id', $user->id)->whereMonth('month', '=', $this->month)->first();

        // CALCULO DA PORCENTAGEM DA META
        if (isset($this->goals->goal_spend)) {
            $this->goal_percent = round(($this->total_all / $this->goals->goal_spend) * 100, 2);
            
            if ($this->goal_percent >= 100) {
                $this->goal_percent = 100;
            }
        }
    }

    public function render()
    {
        //Chamada para gráfico principal de GASTO LIVRE
        $total = $this->categories->groupBy('titulo')->map(function ($category) {
            $category->value = $category->sum('value');
            return $category; // Retorna a categoria completa com o valor atualizado
        });
        
        $pieChartModel = (new PieChartModel())->setTitle(' ')->withDataLabels()->setAnimated(true)->setOpacity(0.85);

        $this->categories = $this->categories->unique('titulo');

        foreach ($this->categories as $category) {
            if (isset($total[$category->titulo])) {
                $pieChartModel->addSlice($category->titulo, $total[$category->titulo]->value, $category->hex);
            }
        }

        //Chamada para gráfico principal de GASTO FIXO
        $total_fixo = $this->categories_fixo->groupBy('titulo')->map(function ($category_fixo) {
            $category_fixo->value = $category_fixo->sum('value');
            return $category_fixo; // Retorna a categoria completa com o valor atualizado
        });

        $pieChartModelFixo = (new PieChartModel())->setTitle(' ')->withDataLabels()->setAnimated(true)->setOpacity(0.85);

        $this->categories_fixo = $this->categories_fixo->unique('titulo');

        foreach ($this->categories_fixo as $cat_fixo) {
            if (isset($total_fixo[$cat_fixo->titulo])) {
                $pieChartModelFixo->addSlice($cat_fixo->titulo, $total_fixo[$cat_fixo->titulo]->value, $cat_fixo->hex);
            }
        }
        
        //Grafico dos meses de gastos
        $lineChartModel = (new LineChartModel())->setTitle(' ')->withDataLabels()->setAnimated(true)->multiLine();

        foreach ($this->monthSpent as $month) {
            $month->total_spent = (double)number_format($month->total_spent / 100, 2, '.', '');
            $lineChartModel->addSeriesPoint('Linha', $month->month, $month->total_spent)->addColor('#b70000');
        }

        return view('livewire.dashboard.index', compact('pieChartModel', 'pieChartModelFixo', 'lineChartModel'));
    }

    public function filterDate() {
        $user = Auth::user();

        if (!isset($this->start_date)) {
            return to_route('dashboard')->with('problem', 'Por favor selecione ao menos uma data inicial.');
        }
        else if (isset($this->start_date) and isset($this->end_date)) {

            $this->categories = Categories::where('categories.user_id', $user->id)
                ->where('categories.type', 'saida')
                ->leftJoin('colors', 'colors.id', '=', 'categories.color')
                ->join('ledger', 'ledger.category_id', '=', 'categories.id')
                ->where('ledger.type', 'livre')
                ->where('ledger.created_at', '>=', $this->start_date)->where('ledger.created_at', '<=', $this->end_date)
                ->select('colors.color as hex', 'ledger.value as value',
                'categories.*')
                ->get();

            $this->categories_fixo = Categories::where('categories.user_id', $user->id)
                ->where('categories.type', 'saida')
                ->leftJoin('colors', 'colors.id', '=', 'categories.color')
                ->join('ledger', 'ledger.category_id', '=', 'categories.id')
                ->where('ledger.type', 'fixo')
                ->where('ledger.created_at', '>=', $this->start_date)->where('ledger.created_at', '<=', $this->end_date)
                ->select('colors.color as hex', 'ledger.value as value',
                'categories.*')
                ->get();

            //GASTOS LIVRES TOTAIS
            $this->total_free = Ledger::where('ledger.user_id', $user->id)
                ->where('ledger.type', 'livre')
                ->join('categories', 'categories.id', '=', 'ledger.category_id')
                ->where('categories.type', 'saida')
                ->where('ledger.created_at', '>=', $this->start_date)->where('ledger.created_at', '<=', $this->end_date)
                ->select('ledger.*')
                ->get();
    
            $this->total_free = $this->total_free->sum('value');

            //GASTOS FIXOS TOTAIS
            $this->total_fixed = Ledger::where('ledger.user_id', $user->id)
                ->where('ledger.type', 'fixo')
                ->join('categories', 'categories.id', '=', 'ledger.category_id')
                ->where('categories.type', 'saida')
                ->where('ledger.created_at', '>=', $this->start_date)->where('ledger.created_at', '<=', $this->end_date)
                ->select('ledger.*')
                ->get();
            
            $this->total_fixed = $this->total_fixed->sum('value');

            $this->total_all = $this->total_free + $this->total_fixed;

        } elseif (isset($this->start_date)) {

            $this->categories = Categories::where('categories.user_id', $user->id)
                ->where('categories.type', 'saida')
                ->leftJoin('colors', 'colors.id', '=', 'categories.color')
                ->join('ledger', 'ledger.category_id', '=', 'categories.id')
                ->where('ledger.type', 'livre')
                ->where('ledger.created_at', '>=', $this->start_date)
                ->select('colors.color as hex', 'ledger.value as value',
                'categories.*')
                ->get();

            $this->categories_fixo = Categories::where('categories.user_id', $user->id)
                ->where('categories.type', 'saida')
                ->leftJoin('colors', 'colors.id', '=', 'categories.color')
                ->join('ledger', 'ledger.category_id', '=', 'categories.id')
                ->where('ledger.type', 'fixo')
                ->where('ledger.created_at', '>=', $this->start_date)
                ->select('colors.color as hex', 'ledger.value as value',
                'categories.*')
                ->get();

            //GASTOS LIVRES TOTAIS
            $this->total_free = Ledger::where('ledger.user_id', $user->id)
                ->where('ledger.type', 'livre')
                ->join('categories', 'categories.id', '=', 'ledger.category_id')
                ->where('categories.type', 'saida')
                ->where('ledger.created_at', '>=', $this->start_date)
                ->select('ledger.*')
                ->get();
    
            $this->total_free = $this->total_free->sum('value');

            //GASTOS FIXOS TOTAIS
            $this->total_fixed = Ledger::where('ledger.user_id', $user->id)
                ->where('ledger.type', 'fixo')
                ->join('categories', 'categories.id', '=', 'ledger.category_id')
                ->where('categories.type', 'saida')
                ->where('ledger.created_at', '>=', $this->start_date)
                ->select('ledger.*')
                ->get();
            
            $this->total_fixed = $this->total_fixed->sum('value');

            $this->total_all = $this->total_free + $this->total_fixed;
        }

        $this->monthSpent = Ledger::where('ledger.user_id', $user->id)
            ->join('categories', 'ledger.category_id', '=', 'categories.id')
            ->where('categories.type', 'saida')
            ->selectRaw('DATE_FORMAT(ledger.created_at, "%m-%Y") as month, SUM(value) as total_spent')
            ->groupBy('month')
            ->get();
    }

    public function cleanFilter() {
        return to_route('dashboard');
    }
}
