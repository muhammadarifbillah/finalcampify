<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Returns / Escrow Settings
    |--------------------------------------------------------------------------
    |
    | Daily fine used for rental late fee calculation.
    | Default follows the admin UI prototype: Rp 50.000 / hari.
    |
    */
    'daily_fine' => (int) env('RETURNS_DAILY_FINE', 10000),
];

