<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\Couple;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BankOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_create_uses_bank_user_id_not_account_name(): void
    {
        [$owner, $partner] = $this->makeCoupleUsers();

        Bank::create([
            'couple_id' => $owner->couple_id,
            'user_id' => $owner->id,
            'name' => 'BCA Ari',
            'account_name' => $partner->name,
            'initial_balance' => 1000000,
            'current_balance' => 1000000,
        ]);

        Bank::create([
            'couple_id' => $owner->couple_id,
            'user_id' => $partner->id,
            'name' => 'Mandiri Sari',
            'account_name' => $owner->name,
            'initial_balance' => 1000000,
            'current_balance' => 1000000,
        ]);

        $this->actingAs($owner)
            ->get('/transactions/create')
            ->assertOk()
            ->assertSee('BCA Ari')
            ->assertDontSee('Mandiri Sari');
    }

    public function test_dashboard_bank_filter_uses_bank_user_id_not_account_name(): void
    {
        [$owner, $partner] = $this->makeCoupleUsers();

        Bank::create([
            'couple_id' => $owner->couple_id,
            'user_id' => $owner->id,
            'name' => 'Dompet Owner',
            'account_name' => $partner->name,
            'initial_balance' => 700000,
            'current_balance' => 700000,
        ]);

        $this->actingAs($owner)
            ->get('/?user_id=' . $partner->id)
            ->assertOk()
            ->assertDontSee('Dompet Owner');
    }

    private function makeCoupleUsers(): array
    {
        $couple = Couple::create([
            'couple_name' => 'Ari & Sari',
            'avatar_couple' => 'AS',
        ]);

        $owner = User::create([
            'couple_id' => $couple->id,
            'name' => 'Ari',
            'email' => 'ari-bank@example.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $partner = User::create([
            'couple_id' => $couple->id,
            'name' => 'Sari',
            'email' => 'sari-bank@example.test',
            'password' => Hash::make('password'),
            'role' => 'partner',
        ]);

        return [$owner, $partner];
    }
}
