<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Category;
use App\Models\DailyPlan;
use App\Models\Goal;
use App\Models\Project;
use App\Models\Task;
use App\Models\Todo;
use App\Models\Trackable;
use App\Models\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DailyPlanController extends Controller
{
    /*
     * A list of all daily plans
     */
    public function index()
    {
        $dp = Trackable::query()
            ->where('user_id', '=', Auth::id())
            ->where('trackable_type', '=', DailyPlan::class)
            ->with(['trackable', 'category', 'user', 'visibility'])
            ->latest()
            ->paginate(12);
        return response()->json([
            'dailyplans' => DailyPlan::all(),
            'activities' => Activity::query()
                ->where('user_id', '=', Auth::id())
                ->whereNotNull('daily_plan_id')->get()
        ]);
    }

    /*
     * Show the form to create a new daily plan
     */
    public function create()
    {
        // Fetch necessary models
        $goals = Goal::all();
        $projects = Project::all();
        $tasks = Task::all();
        $todos = Todo::all();

        return response()->json([
            'goals' => $goals,
            'projects' => $projects,
            'tasks' => $tasks,
            'todos' => $todos,
        ]);
    }

    public function store(Request $request)
    {
        // Validate and create daily plan
        $request->validate([
            'the_day' => 'required|date',
            'dailyplannable_id' => 'required|integer',
            'dailyplannable_type' => 'required|string',
            'review' => 'nullable|string',
        ]);

        DailyPlan::create($request->all());

        return redirect()->route('daily-plans.index')->with('success', 'Daily plan created successfully.');
    }

    /*
     * Show specific daily-plan
     */
    public function show(Request $request, $date = null): Response
    {
        $date = is_null($date) ? $request->input('date') : $date;
        // Fetch a daily-plan with the current date
        $dailyPlan = DailyPlan::query()
            ->whereDate('the_day', '=', Carbon::parse($date)->toDateTimeString())
            ->first();

        $userId = auth()->user()->id;

        // If no daily plan found, create a new one for the current date and user
        if (is_null($dailyPlan)) {
            $dailyPlan = DailyPlan::updateOrCreate([
                'user_id' => $userId,
                'title' => Carbon::parse($date)->toFormattedDayDateString(),
            ], [
                'user_id' => $userId,
                'title' => Carbon::parse($date)->toFormattedDayDateString(),
                'the_day' => Carbon::parse($date)->toDateTimeString(),
            ]);
        }

        // Fetch necessary models
        $goals = Goal::query()->where('user_id', '=', $userId)->get();
        $projects = Project::query()->where('user_id', '=', $userId)->get();
        $tasks = Task::query()->where('user_id', '=', $userId)->get();
        $todos = Todo::query()->where('user_id', '=', $userId)->get();
        $visibilities = Visibility::query()->where('user_id', '=', $userId)->get();
        $categories = Category::query()->where('user_id', '=', $userId)->get();

        // load dailyplan with activities
        $activities = Activity::query()
            ->where('user_id', '=', Auth::id())
            ->with(['todo', 'task', 'project', 'trackable'])
            ->where('daily_plan_id', '=', $dailyPlan->id)
            ->get();

        return response()->json([
            'todayPlan' => $dailyPlan,
            'todayActivities' => $activities,
            'activities' => $activities,
            'goals' => $goals,
            'projects' => $projects,
            'tasks' => $tasks,
            'todos' => $todos,
            'visibilities' => $visibilities,
            'categories' => $categories,
        ]);
    }

    /**
     * Show today's plan w/activities page.
     */
    public function today(Request $request)
    {
        // Fetch necessary models
        $goals = Goal::all();
        $projects = Project::all();
        $tasks = Task::all();
        $todos = Todo::all();
        $activities = Activity::all();
        $visibilities = Visibility::all();
        $categories = Category::all();

        $todayPlan = DailyPlan::query()
            ->with(['user', 'trackable', 'dailyplannable'])
            ->whereDate('the_day', Carbon::today())
            ->first();

        // create new today's Dailyplan if it doesn't exist
        if (is_null($todayPlan)) {
            $todayPlan = DailyPlan::updateOrCreate([
                'user_id' => auth()->user()->id ?? $this->getAdmin()->id,
                'title' => Carbon::today()->toFormattedDayDateString(),
                'the_day' => Carbon::today()->toDateTimeString(),
            ],[
                'user_id' => auth()->user()->id ?? $this->getAdmin()->id,
                'title' => Carbon::today()->toFormattedDayDateString(),
                'the_day' => Carbon::today()->toDateTimeString(),
            ]);
        }

        $todayActivities = Activity::query()
            ->where('user_id', '=', Auth::id())
            ->with(['todo', 'task', 'project', 'trackable'])
            ->where('daily_plan_id', '=', $todayPlan->id)
            ->get();

        return response()->json('DailyPlans/Today',[
            'todayPlan' => $todayPlan,
            'todayActivities' => $todayActivities,
            'activities' => $activities,
            'goals' => $goals,
            'projects' => $projects,
            'tasks' => $tasks,
            'todos' => $todos,
            'visibilities' => $visibilities,
            'categories' => $categories,
        ]);
    }

    /**
     * Show tomorrow's plan w/activities page.
     */
    public function tomorrow(Request $request): Response
    {
        // Fetch necessary models
        $goals = Goal::all();
        $projects = Project::all();
        $tasks = Task::all();
        $todos = Todo::all();
        $activities = Activity::all();
        $visibilities = Visibility::all();
        $categories = Category::all();

        $todayPlan = DailyPlan::query()
            ->with(['user', 'trackable', 'dailyplannable'])
            ->whereDate('the_day', Carbon::tomorrow())
            ->first();

        // create new today's Dailyplan if it doesn't exist
        if (is_null($todayPlan)) {
            $todayPlan = DailyPlan::updateOrCreate([
                'user_id' => auth()->user()->id ?? $this->getAdmin()->id,
                'title' => Carbon::tomorrow()->toFormattedDayDateString(),
                'the_day' => Carbon::tomorrow()->toDateTimeString(),
            ],[
                'user_id' => auth()->user()->id ?? $this->getAdmin()->id,
                'title' => Carbon::tomorrow()->toFormattedDayDateString(),
                'the_day' => Carbon::tomorrow()->toDateTimeString(),
            ]);
        }

        $todayActivities = Activity::query()
            ->where('user_id', '=', Auth::id())
            ->with(['todo', 'task', 'project', 'trackable'])
            ->where('daily_plan_id', '=', $todayPlan->id)
            ->get();

        return Inertia::render('DailyPlans/Tomorrow',[
            'todayPlan' => $todayPlan,
            'todayActivities' => $todayActivities,
            'activities' => $activities,
            'goals' => $goals,
            'projects' => $projects,
            'tasks' => $tasks,
            'todos' => $todos,
            'visibilities' => $visibilities,
            'categories' => $categories,
        ]);
    }

    /**
     * Show yesterday's plan w/activities page.
     */
    public function yesterday(Request $request): Response
    {
        // Fetch necessary models
        $goals = Goal::all();
        $projects = Project::all();
        $tasks = Task::all();
        $todos = Todo::all();
        $activities = Activity::all();
        $visibilities = Visibility::all();
        $categories = Category::all();

        $todayPlan = DailyPlan::query()
            ->with(['user', 'trackable', 'dailyplannable'])
            ->whereDate('the_day', Carbon::yesterday())
            ->first();

        // create new today's Dailyplan if it doesn't exist
        if (is_null($todayPlan)) {
            $todayPlan = DailyPlan::updateOrCreate([
                'user_id' => auth()->user()->id ?? $this->getAdmin()->id,
                'title' => Carbon::yesterday()->toFormattedDayDateString(),
                'the_day' => Carbon::yesterday()->toDateTimeString(),
            ],[
                'user_id' => auth()->user()->id ?? $this->getAdmin()->id,
                'title' => Carbon::yesterday()->toFormattedDayDateString(),
                'the_day' => Carbon::yesterday()->toDateTimeString(),
            ]);
        }

        $todayActivities = Activity::query()
            ->where('user_id', '=', Auth::id())
            ->where('daily_plan_id', '=', $todayPlan->id)
            ->with(['todo', 'task', 'project', 'trackable'])
            ->get();

        return Inertia::render('DailyPlans/Yesterday',[
            'todayPlan' => $todayPlan,
            'todayActivities' => $todayActivities,
            'activities' => $activities,
            'goals' => $goals,
            'projects' => $projects,
            'tasks' => $tasks,
            'todos' => $todos,
            'visibilities' => $visibilities,
            'categories' => $categories,
        ]);
    }

    /*
     * Fetch all activities for specific date
     */
    public function getActivities(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $dailyPlan = DailyPlan::query()
            ->with(['user', 'trackable', 'dailyplannable'])
            ->whereDate('the_day', Carbon::parse($request->date))
            ->first();

        if ($dailyPlan) {
            $activities = Activity::query()
                ->with(['todo', 'task', 'project', 'trackable'])
                ->where('daily_plan_id', '=', $dailyPlan->id)
                ->where('user_id', '=', Auth::id())
                ->get();

            $results = [
                'activities' => $activities,
                'dailyPlan' => $dailyPlan,
            ];
            $resCode = 200;
        } else {
            $results = [];
            $resCode = 404;
        }
        return response()->json($results, $resCode);
    }
}
