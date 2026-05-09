<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamViewController; 
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        $leadsQuery    = \App\Models\Lead::query();
        $pipelineQuery = \App\Models\Pipeline::query();
        $projectQuery  = \App\Models\Project::query();

        if ($user->isStaff()) {
            $leadsQuery->where('assigned_to', $user->id);
            $pipelineQuery->where('assigned_to', $user->id);
            $projectQuery->where('assigned_to', $user->id);
        } elseif ($user->isManajer()) {
            $staffIds   = $user->staffMembers()->pluck('id')->toArray();
            $staffIds[] = $user->id;
            $leadsQuery->whereIn('assigned_to', $staffIds);
            $pipelineQuery->whereIn('assigned_to', $staffIds);
            $projectQuery->whereIn('assigned_to', $staffIds);
        }

        $totalLeads         = $leadsQuery->count();
        $newLeads           = (clone $leadsQuery)->whereMonth('created_at', now()->month)->count();
        $activePipelines    = (clone $pipelineQuery)->whereNotIn('stage', ['won','lost'])->count();
        $pipelineValue      = (clone $pipelineQuery)->whereNotIn('stage', ['won','lost'])->sum('value');
        $activeProjects     = (clone $projectQuery)->where('status', 'in_progress')->count();
        $completedProjects  = (clone $projectQuery)->where('status', 'completed')->count();
        $wonDeals           = (clone $pipelineQuery)->where('stage', 'won')->count();
        $wonValue           = (clone $pipelineQuery)->where('stage', 'won')->sum('value');
        $recentLeads        = (clone $leadsQuery)->latest()->take(5)->get();
        $upcomingActivities = \App\Models\Activity::where('status', 'planned')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(5)->get();

        $leadsPerMonth = (clone $leadsQuery)
            ->selectRaw("TO_CHAR(created_at, 'Mon YY') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupByRaw("TO_CHAR(created_at, 'Mon YY')")
            ->orderByRaw("MIN(created_at)")
            ->get();

        $leadsPerSource = (clone $leadsQuery)
            ->selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByRaw('COUNT(*) DESC')
            ->take(7)->get();

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
        $user  = auth()->user();
        $query = \App\Models\Lead::query();

        if ($user->isStaff()) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->isManajer()) {
            $staffIds   = $user->staffMembers()->pluck('id')->toArray();
            $staffIds[] = $user->id;
            $query->whereIn('assigned_to', $staffIds);
        }

        $totalLeads         = $query->count();
        $totalPipelineValue = \App\Models\Pipeline::sum('value');
        $wonLeads           = (clone $query)->where('status', 'won')->count();
        $conversionRate     = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100) : 0;
        $avgDealValue       = \App\Models\Pipeline::where('stage', 'won')->avg('value') ?? 0;
        $leadsPerStatus     = (clone $query)->selectRaw('status, COUNT(*) as total')->groupBy('status')->orderByRaw('COUNT(*) DESC')->get();
        $leadsPerSource     = (clone $query)->selectRaw('source, COUNT(*) as total')->groupBy('source')->orderByRaw('COUNT(*) DESC')->take(6)->get();
        $pipelinePerStage   = \App\Models\Pipeline::selectRaw('stage, SUM(value) as total')->groupBy('stage')->orderByRaw('SUM(value) DESC')->get();
        $projectsPerStatus  = \App\Models\Project::selectRaw('status, COUNT(*) as total')->groupBy('status')->get();

        return view('analytics', compact(
            'totalLeads', 'totalPipelineValue', 'conversionRate', 'avgDealValue',
            'leadsPerStatus', 'leadsPerSource', 'pipelinePerStage', 'projectsPerStatus'
        ));
    })->name('analytics');

    Route::get('/reports', function () {
        $bulan   = request('bulan', now()->format('Y-m'));
        $periode = \Carbon\Carbon::createFromFormat('Y-m', $bulan);

        $totalLeads         = \App\Models\Lead::count();
        $newLeads           = \App\Models\Lead::whereMonth('created_at', $periode->month)->whereYear('created_at', $periode->year)->count();
        $wonLeads           = \App\Models\Lead::where('status', 'won')->count();
        $lostLeads          = \App\Models\Lead::where('status', 'lost')->count();
        $totalPipelineValue = \App\Models\Pipeline::sum('value');
        $wonValue           = \App\Models\Pipeline::where('stage', 'won')->sum('value');
        $activeProjects     = \App\Models\Project::whereIn('status', ['planning','in_progress'])->count();
        $completedProjects  = \App\Models\Project::where('status', 'completed')->count();
        $conversionRate     = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100) : 0;
        $leadsByStatus      = \App\Models\Lead::selectRaw('status, COUNT(*) as total, SUM(value) as nilai')->groupBy('status')->orderByRaw('COUNT(*) DESC')->get();
        $leadsBySource      = \App\Models\Lead::selectRaw('source, COUNT(*) as total')->groupBy('source')->orderByRaw('COUNT(*) DESC')->get();
        $activePipelines    = \App\Models\Pipeline::with('lead')->whereNotIn('stage', ['won','lost'])->orderBy('value', 'desc')->take(10)->get();
        $recentActivities   = \App\Models\Activity::with('createdBy')->whereMonth('created_at', $periode->month)->whereYear('created_at', $periode->year)->orderBy('created_at', 'desc')->take(10)->get();
        $projects           = \App\Models\Project::with('lead')->orderBy('created_at', 'desc')->take(8)->get();
        $leadsPerMonth      = \App\Models\Lead::selectRaw("TO_CHAR(created_at, 'Mon YY') as month, COUNT(*) as total")->where('created_at', '>=', now()->subMonths(6))->groupByRaw("TO_CHAR(created_at, 'Mon YY')")->orderByRaw("MIN(created_at)")->get();

        return view('reports', compact(
            'bulan', 'periode', 'totalLeads', 'newLeads', 'wonLeads', 'lostLeads',
            'totalPipelineValue', 'wonValue', 'activeProjects', 'completedProjects',
            'conversionRate', 'leadsByStatus', 'leadsBySource', 'activePipelines',
            'recentActivities', 'projects', 'leadsPerMonth'
        ));
    })->name('reports');

    Route::get('/reports/print', function () {
        $bulan   = request('bulan', now()->format('Y-m'));
        $periode = \Carbon\Carbon::createFromFormat('Y-m', $bulan);

        $totalLeads         = \App\Models\Lead::count();
        $newLeads           = \App\Models\Lead::whereMonth('created_at', $periode->month)->whereYear('created_at', $periode->year)->count();
        $wonLeads           = \App\Models\Lead::where('status', 'won')->count();
        $lostLeads          = \App\Models\Lead::where('status', 'lost')->count();
        $totalPipelineValue = \App\Models\Pipeline::sum('value');
        $wonValue           = \App\Models\Pipeline::where('stage', 'won')->sum('value');
        $activeProjects     = \App\Models\Project::whereIn('status', ['planning','in_progress'])->count();
        $completedProjects  = \App\Models\Project::where('status', 'completed')->count();
        $conversionRate     = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100) : 0;
        $leadsByStatus      = \App\Models\Lead::selectRaw('status, COUNT(*) as total, SUM(value) as nilai')->groupBy('status')->orderByRaw('COUNT(*) DESC')->get();
        $leadsBySource      = \App\Models\Lead::selectRaw('source, COUNT(*) as total')->groupBy('source')->orderByRaw('COUNT(*) DESC')->get();
        $activePipelines    = \App\Models\Pipeline::with('lead')->whereNotIn('stage', ['won','lost'])->orderBy('value', 'desc')->take(10)->get();
        $projects           = \App\Models\Project::with('lead')->orderBy('created_at', 'desc')->take(10)->get();

        return view('reports-print', compact(
            'bulan', 'periode', 'totalLeads', 'newLeads', 'wonLeads', 'lostLeads',
            'totalPipelineValue', 'wonValue', 'activeProjects', 'completedProjects',
            'conversionRate', 'leadsByStatus', 'leadsBySource', 'activePipelines', 'projects'
        ));
    })->name('reports.print');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('/import/process', [ImportController::class, 'import'])->name('import.process');

    Route::get('/products-list', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products-list', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products-list/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products-list/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // User Management - hanya direktur
    Route::middleware(['role:direktur'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Team View - hanya direktur dan manajer
    Route::middleware(['role:direktur,manajer'])->group(function () {
        Route::get('/team', [TeamViewController::class, 'index'])->name('team.index');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

});

require __DIR__.'/auth.php';