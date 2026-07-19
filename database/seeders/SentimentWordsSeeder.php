<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentWordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positiveWords = [
            'growth', 'increase', 'improve', 'positive', 'gain',
            'profit', 'success', 'boost', 'strong', 'recovery',
            'stable', 'progress', 'advance', 'opportunity', 'innovation',
            'investment', 'surplus', 'expand', 'thrive', 'prosper',
            'agreement', 'cooperation', 'partnership', 'alliance', 'safety'
        ];

        $negativeWords = [
            'crisis', 'decline', 'drop', 'fall', 'negative',
            'loss', 'risk', 'threat', 'collapse', 'recession',
            'disruption', 'shortage', 'conflict', 'war', 'sanction',
            'inflation', 'deficit', 'debt', 'disaster', 'corruption',
            'tariff', 'embargo', 'protest', 'shutdown', 'delay',
            'strike', 'blockade', 'scarcity', 'ban', 'failure'
        ];

        foreach ($positiveWords as $word) {
            PositiveWord::firstOrCreate(['word' => $word]);
        }

        foreach ($negativeWords as $word) {
            NegativeWord::firstOrCreate(['word' => $word]);
        }
    }
}
