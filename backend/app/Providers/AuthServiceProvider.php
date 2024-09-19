<?php
namespace App\Providers;

use App\Models\Strain;

use App\Policies\StrainPolicy;


use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Strain::class => StrainPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
