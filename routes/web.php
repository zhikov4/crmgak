<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $totalLeads         = \App\Models\Lead::count();
        $newLeads           = \App\Models\Lead::whereMonth('created_at', now()->month)->count();
        $activePipelines    = \App\Models\Pipeline::whereNotIn('stage', ['won','lost'])->count();
        $pipelineValue      = \App\Models\Pipeline::whereNotIn('stage', ['won','lost'])->sum('value');
        $activeProjects     = \App\Models\Project::where('status', 'in_progress')->count();
        $completedProjects  = \App\Models\Project::where('status', 'completed')->count();
        $wonDeals           = \App\Models\Pipeline::where('stage', 'won')->count();
        $wonValue           = \App\Models\Pipeline::where('stage', 'won')->sum('value');
        $recentLeads        = \App\Models\Lead::latest()->take(5)->get();
        $upcomingActivities = \App\Models\Activity::where('status', 'planned')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalLeads', 'newLeads', 'activePipelines', 'pipelineValue',
            'activeProjects', 'completedProjects', 'wonDeals', 'wonValue',
            'recentLeads', 'upcomingActivities'
        ));
    })->name('dashboard');

    Route::resource('leads', LeadController::class);
    Route::resource('projects', ProjectController::class);

    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
    Route::post('/pipeline', [PipelineController::class, 'store'])->name('pipeline.store');
    Route::patch('/pipeline/{pipeline}/stage', [PipelineController::class, 'updateStage'])->name('pipeline.updateStage');
    Route::delete('/pipeline/{pipeline}', [PipelineController::class, 'destroy'])->name('pipeline.destroy');

    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    Route::patch('/activities/{activity}/done', [ActivityController::class, 'markDone'])->name('activities.done');

});

require __DIR__.'/auth.php';