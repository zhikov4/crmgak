<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ImportController;

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

        $leadsPerMonth = \App\Models\Lead::selectRaw("TO_CHAR(created_at, 'Mon YY') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupByRaw("TO_CHAR(created_at, 'Mon YY')")
            ->orderByRaw("MIN(created_at)")
            ->get();

        $leadsPerSource = \App\Models\Lead::selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByRaw('COUNT(*) DESC')
            ->take(7)
            ->get();

        return view('dashboard', compact(
            'totalLeads', 'newLeads', 'activePipelines', 'pipelineValue',
            'activeProjects', 'completedProjects', 'wonDeals', 'wonValue',
            'recentLeads', 'upcomingActivities', 'leadsPerMonth', 'leadsPerSource'
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

    Route::get('/analytics', function () {
        $totalLeads      = \App\Models\Lead::count();
        $totalPipelineValue = \App\Models\Pipeline::sum('value');
        $wonLeads        = \App\Models\Lead::where('status', 'won')->count();
        $conversionRate  = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100) : 0;
        $avgDealValue    = \App\Models\Pipeline::where('stage', 'won')->avg('value') ?? 0;

        $leadsPerStatus  = \App\Models\Lead::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->orderByRaw('COUNT(*) DESC')->get();

        $leadsPerSource  = \App\Models\Lead::selectRaw('source, COUNT(*) as total')
            ->groupBy('source')->orderByRaw('COUNT(*) DESC')->take(6)->get();

        $pipelinePerStage = \App\Models\Pipeline::selectRaw('stage, SUM(value) as total')
            ->groupBy('stage')->orderByRaw('SUM(value) DESC')->get();

        $projectsPerStatus = \App\Models\Project::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->get();

        return view('analytics', compact(
            'totalLeads', 'totalPipelineValue', 'conversionRate', 'avgDealValue',
            'leadsPerStatus', 'leadsPerSource', 'pipelinePerStage', 'projectsPerStatus'
        ));
    })->name('analytics');
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('/import/process', [ImportController::class, 'import'])->name('import.process');

});

require __DIR__.'/auth.php';