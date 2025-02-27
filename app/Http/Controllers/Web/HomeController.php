<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Category;
use App\Models\DailyPlan;
use App\Models\Goal;
use App\Models\Objective;
use App\Models\Project;
use App\Models\Task;
use App\Models\Todo;
use App\Models\Trackable;
use App\Models\Visibility;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function home()
    {
        $categories = Category::query()->latest()->get();
        $visibilities = Visibility::query()->latest()->get();
        $trackables = Trackable::query()->latest()->get();
        $goals = Goal::query()->latest()->get();
        $objectives = Objective::query()->latest()->get();
        $projects = Project::query()->latest()->get();
        $tasks = Task::query()->latest()->get();
        $todos = Todo::query()->latest()->get();
        $activities = Activity::query()->latest()->get();

        return Inertia::render('Welcome', [
            'categories' => $categories,
            'visibilities' => $visibilities,
            'trackables' => $trackables,
            'goals' => $goals,
            'objectives' => $objectives,
            'projects' => $projects,
            'tasks' => $tasks,
            'todos' => $todos,
            'activities' => $activities,
        ]);
    }
}
