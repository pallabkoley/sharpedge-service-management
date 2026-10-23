<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        // 🔓 Allow all actions for backoffice users (bypass policies)
        Gate::before(function (User $user, $ability) {
            if ($user->role && strtolower($user->role->name) === 'backoffice') {
                return true;
            }
        });

        // Share task count with Backoffice sidebar
        View::composer('layouts.sidebar.Backoffice_sidebar', function ($view) {

            $newTaskCount = 0;
            $hasNewTasks  = false;

            if (Auth::check() && Auth::user()->staff) {

                $staffId = Auth::user()->staff->id;

                $newTaskCount = Task::where('assigned_to', $staffId)
                    ->where('is_new_for_staff', 1)
                    ->count();

                $hasNewTasks = $newTaskCount > 0;
            }

            $view->with([
                'newTaskCount' => $newTaskCount,
                'hasNewTasks'  => $hasNewTasks,
            ]);
        });
    }
}