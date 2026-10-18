<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // ログイン中のユーザー経由で作ると、user_id が自動で入る
        $request->user()->transactions()->create([
            'type'=> $request->type,
            'amount'=> $request->amount,
            'category'=> $request->category,
            'date'=> $request->date,
            'memo'=> $request->memo,
        ]);
        return redirect('/');
    }

    // 編集フォームを表示
    public function showEditForm(Request $request, $id) {
        $transaction = $this->findOwnedTransaction($request, $id);
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
        $transaction = $this->findOwnedTransaction($request, $id);
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
    public function deleteTransaction(Request $request, $id) {
        $transaction = $this->findOwnedTransaction($request, $id);
        $transaction->delete();

        return redirect('/');
    }

    /**
     * ログイン中のユーザー自身の取引だけを id で探す。
     * 他人の id を指定された場合は見つからない扱いになり、404 になる。
     */
    private function findOwnedTransaction(Request $request, $id) {
        return $request->user()->transactions()->findOrFail($id);
    }
}
