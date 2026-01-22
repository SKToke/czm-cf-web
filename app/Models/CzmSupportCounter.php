<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CzmSupportCounter extends AbstractModel
{
    protected $fillable = [
        'counter_label',
        'counter_value',
    ];

    public static function getAllCounters(): array
    {
        $counters = [];

        $counterRecords = CzmSupportCounter::all();
        foreach ($counterRecords as $index => $counterRecord) {
            if ($index == 0) {
                $counters[] = [
                    'label' => $counterRecord->counter_label,
                    'value' => (int)$counterRecord->counter_value,
                    'icon' => 'hand-holding-heart',
                ];
            }
            if ($index == 1) {
                $counters[] = [
                    'label' => $counterRecord->counter_label,
                    'value' => (int)$counterRecord->counter_value,
                    'icon' => 'globe',
                ];
            }
            if ($index == 2) {
                $counters[] = [
                    'label' => $counterRecord->counter_label,
                    'value' => (int)$counterRecord->counter_value,
                    'icon' => 'handshake',
                ];
            }
        }

        return $counters;
    }
}
