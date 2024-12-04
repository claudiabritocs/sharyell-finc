<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() {

        return view('application.dashboard');
    }

    public function earnAdd(Request $request) {

        try {
            $user = Auth::user();

            $request->validate([
                'category_id' => 'required',
                'descricao' => 'required',
                'value' => 'required',
                'date' => 'required',
            ]);
            
            $valor = str_replace([' ', ',', '.', 'R', '$'], '', $request->value);

            $ledger = new Ledger();
            $ledger->user_id = $user->id;
            $ledger->category_id = $request->category_id;
            $ledger->descricao = $request->descricao;
            $ledger->value = $valor;
            $ledger->date = $request->date;

            if(isset($request->remember)) {
                $ledger->remember = $request->remember;
            } else {
                $ledger->remember = 0;
            }

            $ledger->save();

            return to_route('dashboard')->with('success', 'Registro incluído com sucesso.');

        } catch (\Exception $e) {
            return to_route('dashboard')->with('problem', 'Erro ao incluir registro: ' . $e->getMessage());
        }
    }

    public function spendAdd(Request $request) {

        try {
            $user = Auth::user();

            $request->validate([
                'category_id' => 'required',
                'descricao' => 'required',
                'value' => 'required',
                'date' => 'required',
            ]);
            
            $valor = str_replace([' ', ',', '.', 'R', '$'], '', $request->value);

            $ledger = new Ledger();
            $ledger->user_id = $user->id;
            $ledger->category_id = $request->category_id;
            $ledger->descricao = $request->descricao;
            $ledger->value = $valor;
            $ledger->date = $request->date;

            if(isset($request->remember)) {
                $ledger->remember = $request->remember;
            } else {
                $ledger->remember = 0;
            }

            $ledger->save();

            return to_route('dashboard')->with('success', 'Registro incluído com sucesso.');

        } catch (\Exception $e) {
            return to_route('dashboard')->with('problem', 'Erro ao incluir registro: ' . $e->getMessage());
        }
    }

    public function setGoal(Request $request) {

        try {
            $user = Auth::user();

            $month = date('m');

            $month_save = date('Y-m') . '-01';

            $request->validate([
                'goal_spend' => 'required',
            ]);
            
            $valor = str_replace([' ', ',', '.', 'R', '$'], '', $request->goal_spend);

            $goal = Goal::where('user_id', $user->id)->whereMonth('month', '=', $month)->first();

            if($goal) {
                $goal->goal_spend = $valor;
                $goal->update([
                    'goal_spend' => $valor,
                ]);

            } else {
                $goal = new Goal();
                $goal->user_id = $user->id;
                $goal->goal_spend = $valor;
                $goal->month = $month_save;
                $goal->save();
                
            }

            return to_route('dashboard')->with('success', 'Registro alterado com sucesso.');

        } catch (\Exception $e) {
            return to_route('dashboard')->with('problem', 'Erro ao incluir registro: ' . $e->getMessage());
        }
    }
}
