<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Bank;
use App\Models\Couple;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_assets_page_calculates_net_worth(): void
    {
        [$user, $bank] = $this->workspace();

        Asset::create([
            'couple_id' => $user->couple_id,
            'user_id' => $user->id,
            'name' => 'Emas',
            'type' => 'emas',
            'purchase_value' => 1000000,
            'current_value' => 1500000,
        ]);

        Debt::create([
            'couple_id' => $user->couple_id,
            'user_id' => $user->id,
            'type' => 'hutang',
            'amount' => 300000,
            'paid_amount' => 100000,
            'counterparty' => 'Teman',
            'purpose' => 'Pinjaman',
            'due_date' => now()->addWeek(),
            'bank_id' => $bank->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get('/assets')
            ->assertOk()
            ->assertSee('Rp 2.300.000')
            ->assertSee('Emas');
    }

    private function workspace(): array
    {
        $couple = Couple::create([
            'couple_name' => 'Ari & Sari',
            'avatar_couple' => 'AS',
        ]);

        $user = User::create([
            'couple_id' => $couple->id,
            'name' => 'Ari',
            'email' => 'ari-asset@example.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $bank = Bank::create([
            'couple_id' => $couple->id,
            'user_id' => $user->id,
            'name' => 'BCA',
            'account_name' => 'Ari',
            'initial_balance' => 1000000,
            'current_balance' => 1000000,
        ]);

        return [$user, $bank];
    }
}
