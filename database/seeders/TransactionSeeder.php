<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $incomeDescriptions = [
            'Gaji Bulanan',
            'Bonus Kerja',
            'THR',
            'Freelance Project',
            'Hasil Investasi',
            'Penjualan Barang',
            'Cashback',
            'Hadiah',
            'Komisi',
            'Pendapatan Usaha'
        ];

        $expenseDescriptions = [
            'Belanja Bulanan',
            'Bayar Listrik',
            'Bayar Air',
            'Internet',
            'Makan Diluar',
            'Transportasi',
            'Belanja Online',
            'Hiburan',
            'Cicilan',
            'Kebutuhan Rumah',
            'BBM',
            'Perawatan Kendaraan'
        ];

        Transaction::truncate();

        for ($month = 1; $month <= 12; $month++) {

            $incomeCount = rand(8, 12);

            $baseIncome = 5000000 + ($month * 500000);

            for ($i = 1; $i <= $incomeCount; $i++) {

                Transaction::create([
                    'couple_id' => 1,
                    'user_id' => 1,
                    'category_id' => rand(1, 3),
                    'bank_id' => 1,
                    'type' => 'income',

                    'amount' => rand(
                        $baseIncome,
                        $baseIncome + 3000000
                    ),

                    'description' => $incomeDescriptions[array_rand($incomeDescriptions)],
                    'notes' => fake()->sentence(),

                    'date' => Carbon::create(
                        2026,
                        $month,
                        rand(
                            1,
                            Carbon::create(2026, $month)->daysInMonth
                        )
                    ),
                ]);
            }

            $expenseCount = rand(10, 15);

            $baseExpense = 1500000 + ($month * 200000);

            for ($i = 1; $i <= $expenseCount; $i++) {

                Transaction::create([
                    'couple_id' => 1,
                    'user_id' => 1,
                    'category_id' => rand(4, 10),
                    'bank_id' => 1,
                    'type' => 'expense',

                    'amount' => rand(
                        100000,
                        $baseExpense
                    ),

                    'description' => $expenseDescriptions[array_rand($expenseDescriptions)],
                    'notes' => fake()->sentence(),

                    'date' => Carbon::create(
                        2026,
                        $month,
                        rand(
                            1,
                            Carbon::create(2026, $month)->daysInMonth
                        )
                    ),
                ]);
            }
        }
    }
}
