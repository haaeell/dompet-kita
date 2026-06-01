<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\Category;
use App\Models\CategoryBudget;
use App\Models\Couple;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_from_previous_month_is_effective_for_selected_month(): void
    {
        [$user, $category, $bank] = $this->makeCoupleWorkspace();

        CategoryBudget::create([
            'couple_id' => $user->couple_id,
            'category_id' => $category->id,
            'budget_month' => '2026-05-01',
            'amount' => 500000,
        ]);

        Transaction::create([
            'couple_id' => $user->couple_id,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'bank_id' => $bank->id,
            'type' => 'expense',
            'amount' => 125000,
            'description' => 'Makan siang',
            'date' => '2026-06-10 10:00:00',
        ]);

        $this->actingAs($user)
            ->get('/budgets?month=2026-06')
            ->assertOk()
            ->assertSee('Makanan')
            ->assertSee('Rp 500.000')
            ->assertSee('Rp 125.000');
    }

    public function test_clearing_budget_stops_previous_month_inheritance(): void
    {
        [$user, $category] = $this->makeCoupleWorkspace();

        CategoryBudget::create([
            'couple_id' => $user->couple_id,
            'category_id' => $category->id,
            'budget_month' => '2026-05-01',
            'amount' => 500000,
        ]);

        $this->actingAs($user)
            ->post('/budgets', [
                'category_id' => $category->id,
                'budget_month' => '2026-06',
                'amount' => 500000,
                'clear_budget' => '1',
            ])
            ->assertRedirect('/budgets?month=2026-06');

        $this->assertDatabaseHas('category_budgets', [
            'couple_id' => $user->couple_id,
            'category_id' => $category->id,
            'budget_month' => '2026-06-01 00:00:00',
            'amount' => 0,
        ]);

        $this->assertTrue(CategoryBudget::activeForMonth($user->couple_id, now()->setDate(2026, 6, 1))->isEmpty());
    }

    public function test_budget_history_only_counts_spending_from_budgeted_categories(): void
    {
        [$user, $budgetedCategory, $bank] = $this->makeCoupleWorkspace();

        $unbudgetedCategory = Category::create([
            'couple_id' => $user->couple_id,
            'name' => 'Transport',
            'icon' => 'T',
            'color' => '#0891b2',
            'type' => 'expense',
        ]);

        CategoryBudget::create([
            'couple_id' => $user->couple_id,
            'category_id' => $budgetedCategory->id,
            'budget_month' => '2026-06-01',
            'amount' => 500000,
        ]);

        foreach ([
            [$budgetedCategory->id, 125000, 'Makan siang'],
            [$unbudgetedCategory->id, 300000, 'Bensin'],
        ] as [$categoryId, $amount, $description]) {
            Transaction::create([
                'couple_id' => $user->couple_id,
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'bank_id' => $bank->id,
                'type' => 'expense',
                'amount' => $amount,
                'description' => $description,
                'date' => '2026-06-10 10:00:00',
            ]);
        }

        $this->actingAs($user)
            ->get('/budgets?month=2026-06')
            ->assertOk()
            ->assertSee('Terpakai Rp 125.000')
            ->assertDontSee('Terpakai Rp 425.000');
    }

    private function makeCoupleWorkspace(): array
    {
        $couple = Couple::create([
            'couple_name' => 'Ari & Sari',
            'avatar_couple' => 'AS',
        ]);

        $user = User::create([
            'couple_id' => $couple->id,
            'name' => 'Ari',
            'email' => 'ari@example.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $category = Category::create([
            'couple_id' => $couple->id,
            'name' => 'Makanan',
            'icon' => 'M',
            'color' => '#db2777',
            'type' => 'expense',
        ]);

        $bank = Bank::create([
            'couple_id' => $couple->id,
            'name' => 'BCA',
            'account_name' => 'Ari',
            'initial_balance' => 1000000,
            'current_balance' => 1000000,
        ]);

        return [$user, $category, $bank];
    }
}
