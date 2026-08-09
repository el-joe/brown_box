<?php

use App\Jobs\ApproveReadyCommissions;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('stock:alert')->daily();
Schedule::job(new ApproveReadyCommissions())->daily();
