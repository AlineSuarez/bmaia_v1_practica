<?php
use Carbon\Carbon;

function user_date($value)
{
    try {
        $carbon = $value instanceof Carbon ? $value : Carbon::parse($value);
        $format = config('app.date_format', 'DD/MM/YYYY');
        return $carbon->locale(app()->getLocale())->isoFormat($format);
    } catch (\Exception $e) {
        return $value;
    }
}
