<?php

namespace App\Providers;

use App\Events\BugStatusChanged;
use App\Events\TestResultStatusChanged;
use App\Listeners\RecordBugStatusHistory;
use App\Listeners\SyncBugAndTestRunStatus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            TestResultStatusChanged::class,
            [SyncBugAndTestRunStatus::class, 'handle']
        );

        Event::listen(
            BugStatusChanged::class,
            [RecordBugStatusHistory::class, 'handle']
        );
    }
}
