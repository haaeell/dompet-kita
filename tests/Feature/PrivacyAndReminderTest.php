<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\Category;
use App\Models\Couple;
use App\Models\FeatureAnnouncement;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PrivacyAndReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_transaction_is_hidden_from_partner_dashboard(): void
    {
        [$owner, $partner, $category, $bank] = $this->workspace();

        Transaction::create([
            'couple_id' => $owner->couple_id,
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'bank_id' => $bank->id,
            'type' => 'expense',
            'amount' => 100000,
            'description' => 'Hadiah privat',
            'date' => now(),
            'is_private' => true,
        ]);

        $this->actingAs($partner)
            ->get('/')
            ->assertOk()
            ->assertDontSee('Hadiah privat');

        $this->actingAs($owner)
            ->get('/')
            ->assertOk()
            ->assertSee('Hadiah privat');
    }

    public function test_bill_reminder_can_be_created(): void
    {
        [$owner, , $category, $bank] = $this->workspace();

        $this->actingAs($owner)
            ->post('/reminders', [
                'title' => 'Internet rumah',
                'amount' => 350000,
                'due_date' => now()->addDays(3)->toDateString(),
                'repeat' => 'monthly',
                'user_id' => $owner->id,
                'bank_id' => $bank->id,
                'category_id' => $category->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bill_reminders', [
            'couple_id' => $owner->couple_id,
            'title' => 'Internet rumah',
            'repeat' => 'monthly',
        ]);

        $this->actingAs($owner)
            ->get('/reminders')
            ->assertOk()
            ->assertSee('Kalender Tagihan')
            ->assertSee('billReminderCalendar');
    }

    public function test_feature_announcement_is_shown_until_user_reads_it(): void
    {
        [$owner] = $this->workspace();

        $announcement = FeatureAnnouncement::create([
            'title' => 'Kalender tagihan sudah hadir',
            'type' => 'feature',
            'version' => 'v1.2',
            'body' => 'Sekarang jadwal tagihan bisa dilihat dalam kalender besar.',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->actingAs($owner)
            ->get('/')
            ->assertOk()
            ->assertSee('Kalender tagihan sudah hadir')
            ->assertSee('Saya mengerti');

        $this->actingAs($owner)
            ->put("/feature-announcements/{$announcement->id}/read")
            ->assertRedirect();

        $this->actingAs($owner)
            ->get('/')
            ->assertOk()
            ->assertDontSee('Kalender tagihan sudah hadir');
    }

    private function workspace(): array
    {
        $couple = Couple::create([
            'couple_name' => 'Ari & Sari',
            'avatar_couple' => 'AS',
        ]);

        $owner = User::create([
            'couple_id' => $couple->id,
            'name' => 'Ari',
            'email' => 'ari-privacy@example.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $partner = User::create([
            'couple_id' => $couple->id,
            'name' => 'Sari',
            'email' => 'sari-privacy@example.test',
            'password' => Hash::make('password'),
            'role' => 'partner',
        ]);

        $category = Category::create([
            'couple_id' => $couple->id,
            'name' => 'Belanja',
            'icon' => 'B',
            'color' => '#db2777',
            'type' => 'expense',
        ]);

        $bank = Bank::create([
            'couple_id' => $couple->id,
            'user_id' => $owner->id,
            'name' => 'BCA',
            'account_name' => 'Ari',
            'initial_balance' => 1000000,
            'current_balance' => 1000000,
        ]);

        return [$owner, $partner, $category, $bank];
    }
}
