<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private function makeTransactionFor(User $user, array $overrides = []): Transaction
    {
        return Transaction::factory()->for($user)->create(array_merge([
            'type' => '支出',
            'amount' => 1200,
            'category' => '食費',
            'date' => '2026-10-01',
            'memo' => 'ランチ',
        ], $overrides));
    }

    public function test_registered_transaction_belongs_to_the_logged_in_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/register', [
            'type' => '支出',
            'amount' => 3000,
            'category' => '日用品',
            'date' => '2026-10-05',
            'memo' => '洗剤',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 3000,
            'memo' => '洗剤',
        ]);
    }

    public function test_edit_form_of_another_users_transaction_returns_404(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $transaction = $this->makeTransactionFor($owner);

        $this->actingAs($other)->get("/edit_form/{$transaction->id}")->assertNotFound();
    }

    public function test_owner_can_open_the_edit_form_of_their_own_transaction(): void
    {
        $owner = User::factory()->create();
        $transaction = $this->makeTransactionFor($owner);

        $this->actingAs($owner)->get("/edit_form/{$transaction->id}")->assertOk();
    }

    public function test_another_user_cannot_update_someone_elses_transaction(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $transaction = $this->makeTransactionFor($owner, ['amount' => 1200]);

        $this->actingAs($other)->post("/edit/{$transaction->id}", [
            'type' => '収入',
            'amount' => 999999,
            'category' => 'その他',
            'date' => '2026-10-09',
            'memo' => '乗っ取り',
        ])->assertNotFound();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 1200,
            'memo' => 'ランチ',
        ]);
    }

    public function test_another_user_cannot_delete_someone_elses_transaction(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $transaction = $this->makeTransactionFor($owner);

        $this->actingAs($other)->post("/delete/{$transaction->id}")->assertNotFound();

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_owner_can_delete_their_own_transaction(): void
    {
        $owner = User::factory()->create();
        $transaction = $this->makeTransactionFor($owner);

        $this->actingAs($owner)->post("/delete/{$transaction->id}")->assertRedirect('/');

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_home_only_shows_the_logged_in_users_transactions(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $this->makeTransactionFor($owner, ['memo' => '自分のメモ', 'date' => '2026-10-02']);
        $this->makeTransactionFor($other, ['memo' => '他人のメモ', 'date' => '2026-10-02']);

        $response = $this->actingAs($owner)->get('/?year=2026&month=10');

        $response->assertOk();
        $response->assertSee('自分のメモ');
        $response->assertDontSee('他人のメモ');
    }
}
