<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\LedgerEntry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_and_sales_record_ledger_entries(): void
    {
        $user = User::factory()->create();

        // 1. Create an Expense and verify ledger
        $category = ExpenseCategory::query()->create(['name' => 'Utility', 'slug' => 'utility']);

        $this->actingAs($user)->post('/expenses', [
            'category' => 'Utility',
            'amount' => 1500,
            'payment_method' => 'cash',
            'date' => now()->toDateString(),
            'description' => 'Electricity bill',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('ledger_entries', [
            'ledger_type' => 'expense',
            'debit' => 1500,
            'credit' => 0,
        ]);

        // 2. View unified ledger page
        $response = $this->actingAs($user)->get('/accounts/ledger');
        $response->assertStatus(200);
        $response->assertSee('Electricity bill');
    }
}
