<?php

namespace TorMorten\Firestore;

use Illuminate\Support\ServiceProvider;
use Kreait\Laravel\Firebase\Facades\Firebase;
use ReflectionObject;
use TorMorten\Firestore\Http\FirestoreApi;

class FirestoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FirestoreApi::class);
        $this->app->singleton(Firestore::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/firestore.php',
            'firestore'
        );
    }

    public function boot()
    {
        $this->publishes([
            'firestore' => config_path('firestore.php'),
        ], 'firestore');
    }

}
