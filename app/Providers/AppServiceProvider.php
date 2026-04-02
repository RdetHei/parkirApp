<?php

namespace App\Providers;

use App\Events\ParkingCheckedIn;
use App\Events\ParkingCheckedOut;
use App\Listeners\SendParkingCheckInEmail;
use App\Listeners\SendParkingCheckInWhatsApp;
use App\Listeners\SendParkingCheckOutEmail;
use App\Listeners\SendParkingCheckOutWhatsApp;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Models\Transaksi;
use App\Observers\TransaksiObserver;

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
        Event::listen(ParkingCheckedIn::class, SendParkingCheckInEmail::class);
        Event::listen(ParkingCheckedIn::class, SendParkingCheckInWhatsApp::class);
        Event::listen(ParkingCheckedOut::class, SendParkingCheckOutEmail::class);
        Event::listen(ParkingCheckedOut::class, SendParkingCheckOutWhatsApp::class);

        Transaksi::observe(TransaksiObserver::class);

        $this->loadMigrationsFrom([
            database_path('migrations/framework'),
            database_path('migrations/app'),
        ]);
    }
}

