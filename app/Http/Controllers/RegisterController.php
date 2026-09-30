<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class RegisterController extends Controller
{
    public function showRegisterForm() {
        return view('register');
    }

    public function register(Request $request) {
        $request->validate([
            'type'=>'required|in:支出,収入',
            'amount'=> 'required|integer|min:1',
            'category'=> 'nullable|in:食費,日用品,交通費,趣味,光熱費,その他',
            'date'=> 'required|date',
            'memo'=>'nullable|string'
        ]);
        $transaction = Transaction::create([
            'type'=> $request->type,
            'amount'=> $request->amount,
            'category'=> $request->category,
            'date'=> $request->date,
            'memo'=> $request->memo,
        ]);
        return redirect('/');
    }
}
