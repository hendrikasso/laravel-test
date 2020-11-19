<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use HasFactory;

    /**
     * Daily hours 06.00 – 22.00
     * Nightly hours 22.00 – 06.00
     * Converted to minutes for easier calculations
     */
    const DAILY_RANGE_BEGIN = 360;
    const DAILY_RANGE_END = 1320;
    const NIGHTLY_RANGE_BEGIN = 1320;
    const NIGHTLY_RANGE_END = 1800;

    protected $fillable = [
        'name',
        'begin',
        'end',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model) { return self::calculateHours($model); });

        self::updating(function($model) { return self::calculateHours($model); });
    }

    /**
     * Calculate duration, daily_hours and nightly_hours from begin and end time-strings
     * This assumes that begin and end fields are filled and validated correctly
     * Logic is following:
     * 1) we strech start and end times over 48h period
     * 2) we test ranges overlapping as many times they can accour on this 48h period (daily 2x, nightly 3x)
     */
    protected static function calculateHours($model) {
        list($beginHour, $beginMin) = explode(':', $model->begin);
        list($endHour, $endMin) = explode(':', $model->end);

        // precision is in minutes so lets convert everything to minutes
        $beginTime = (int)$beginHour * 60 + (int)$beginMin;
        $endTime = (int)$endHour * 60 + (int)$endMin;
        $minutesInDay = 24 * 60;

        // if begin time is equal or bigger then we add 24h to end time, because its streching to next day
        if ($beginTime >= $endTime) {
            $endTime = $endTime + (24 * 60);
        }

        // we set daily minutes to zero and add calculated overlapping from each daily period
        // daily ranges can overlap 2x time during 48h (06:00 – 22:00, 30:00 – 46:00)
        $dailyMinutes = 0;
        $dailyMinutes += self::calculateOverlapping($beginTime, $endTime, self::DAILY_RANGE_BEGIN, self::DAILY_RANGE_END);
        $dailyMinutes += self::calculateOverlapping($beginTime, $endTime, self::DAILY_RANGE_BEGIN + $minutesInDay, self::DAILY_RANGE_END + $minutesInDay);

        // we set nigthly minutes to zero and add calculated overlapping from each nightly period
        // nightly ranges can overlap 3x time during 48h (00:00 - 06:00, 22:00 - 30:00, 46.00 – 48.00)
        $nightlyMinutes = 0;
        $nightlyMinutes += self::calculateOverlapping($beginTime, $endTime, self::NIGHTLY_RANGE_BEGIN - $minutesInDay, self::NIGHTLY_RANGE_END - $minutesInDay);
        $nightlyMinutes += self::calculateOverlapping($beginTime, $endTime, self::NIGHTLY_RANGE_BEGIN, self::NIGHTLY_RANGE_END);
        $nightlyMinutes += self::calculateOverlapping($beginTime, $endTime, self::NIGHTLY_RANGE_BEGIN + $minutesInDay, self::NIGHTLY_RANGE_END + $minutesInDay);

        // convert everything back to hours
        $model->duration = round(($endTime - $beginTime) / 60, 1);
        $model->daily_hours = round($dailyMinutes / 60, 1);
        $model->nightly_hours = round($nightlyMinutes / 60, 1);

        return $model;
    }

    /**
     * Calculate overlapping between the range
     */
    protected static function calculateOverlapping($beginTime, $endTime, $rangeBegin, $rangeEnd) {
        if ($endTime > $rangeBegin && $beginTime < $rangeEnd) {
            $overlappingBegin = max($beginTime, $rangeBegin);
            $overlappingEnd = min($endTime, $rangeEnd);
    
            return $overlappingEnd - $overlappingBegin;
        }
    
        return 0;
    }
}