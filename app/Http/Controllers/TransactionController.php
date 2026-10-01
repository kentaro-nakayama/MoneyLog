<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    // 新規登録フォームを表示
    public function showRegisterForm() {
        return view('register');
    }

    // 新規登録を保存
    public function registerTransaction(Request $request) {
        $request->validate([
            'type'=>'required|in:収入,支出',
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

    // 編集フォームを表示
    public function showEditForm($id) {
        $transaction = Transaction::findOrFail($id);
        return view('edit', compact('transaction'));
    }

    // 編集内容を保存
    public function editTransaction(Request $request, $id) {
        $request->validate([
            'type'=>'required|in:収入,支出',
            'amount'=> 'required|integer|min:1',
            'category'=> 'nullable|in:食費,日用品,交通費,趣味,光熱費,その他',
            'date'=> 'required|date',
            'memo'=>'nullable|string'
        ]);

        // 編集対象のデータを探して、送信された内容で上書きする
        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'type'=> $request->type,
            'amount'=> $request->amount,
            'category'=> $request->category,
            'date'=> $request->date,
            'memo'=> $request->memo,
        ]);

        return redirect('/');
    }

    // 削除
    public function deleteTransaction($id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect('/');
    }
}
