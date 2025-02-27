<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\DailyTarget;
use App\Models\Goal;
use App\Models\Project;
use App\Models\Task;
use App\Models\Todo;
use App\Models\Visibility;
use Illuminate\Http\Request;

class GeneralStatsController extends Controller
{
    /*
     * Return all goals for auth user
     */
    public function getGoals(Request $request)
    {
        // fetch projects
        $goals = Goal::where('user_id', $request->user()->id)->get();

        return response()->json($goals, 200);
    }

    /*
     * Return all projects for auth user
     */
    public function getProjects(Request $request)
    {
        // fetch projects
        $projects = Project::where('user_id', $request->user()->id)->get();

        return response()->json($projects, 200);
    }

    /*
     * Fetch all tasks for auth user
     */
    public function getTasks(Request $request)
    {
        // fetch tasks
        $tasks = Task::where('user_id', $request->user()->id)->get();

        return response()->json($tasks, 200);
    }

    /*
     * Fetch all todos for the auth user
     */
    public function getTodos(Request $request)
    {
        // fetch todos
        $todos = Todo::where('user_id', $request->user()->id)->get();

        return response()->json($todos, 200);
    }

    /*
     * Fetch all activities of the user
     */
    public function getActivities(Request $request)
    {
        // fetch activities
        $activities = Activity::where('user_id', $request->user()->id)->get();

        return response()->json($activities, 200);
    }

    /*
     * Fetch all daily targets of the user
     */
    public function getDailyTargets(Request $request)
    {
        // fetch activities
        $dailyTargets = DailyTarget::where('user_id', $request->user()->id)->get();

        return response()->json($dailyTargets, 200);
    }

    /*
     * Fetch all categories
     */
    public function getCategories(Request $request)
    {
        // fetch categories
        $categories = Category::all();

        return response()->json($categories, 200);
    }

    /*
     * Fetch all visibility states
     */
    public function getVisibilities(Request $request)
    {
        // fetch visibilities
        $visibilities = Visibility::all();

        return response()->json($visibilities, 200);
    }
}
