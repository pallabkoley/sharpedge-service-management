<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(
            'layouts.sidebar.Backoffice_sidebar',
            function ($view) {

                $newTaskCount = 0;
                $hasNewTasks  = false;

                // Only for logged-in backoffice staff
                if (Auth::check() && Auth::user()->staff) {

                    $staffId = Auth::user()->staff->id;

                    $newTaskCount = Task::where('assigned_to', $staffId)
                        ->where('is_new_for_staff', 1)
                        ->count();

                    $hasNewTasks = $newTaskCount > 0;
                }

                // Share data with sidebar
                $view->with([
                    'newTaskCount' => $newTaskCount,
                    'hasNewTasks'  => $hasNewTasks,
                ]);
            }
        );
    }
}
