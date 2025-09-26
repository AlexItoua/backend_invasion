<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Policies\MessagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Conversation::class => MessagePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
