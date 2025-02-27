<?php

namespace App\Http\Controllers\Web;

use App\Enums\TrackableStatus;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\Trackable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityController extends Controller
{
    /*
     * A list of all activities
     */
    public function index()
    {
        $act = Trackable::query()
            ->where('user_id', '=', Auth::id())
            ->where('trackable_type', '=', Activity::class)
            ->with(['trackable', 'category', 'user', 'visibility'])
            ->latest()
            ->paginate(12);

        return response()->json([
            'activities' => $act
        ]);
    }

    /*
     * Show a specific activity
     */
    public function show($id)
    {
        $activity = Activity::findOrFail($id);

        return response()->json([
            'activity' => $activity
        ]);
    }

    /*
     * Store a new activity
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'the_day' => 'required|date',
            'from' => 'required|string',
            'title' => 'required|string',
        ]);

        // get user id
        $userId = auth()->id();

        // Create a new activity with the validated data
        $activity = Activity::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'user_id' => $userId,
            'priority' => $request->input('priority') ?? 'high',
            'status' => $request->input('status') ?? TrackableStatus::IDEATION,
            'progress' => $request->input('progress') ?? 0.05
        ]);

        $dueDate = $request->input('due_at') ?: $request->input('the_day');

        // Parse the 'due date' input as a Carbon instance
        $date = Carbon::parse($dueDate);

        // check if the request contains a daily-plan instance
        if ($request->input('daily_plan_id')) {
            $dailyPlan = DailyPlan::find($request->input('daily_plan_id'));
        } else {
            $dailyPlan = DailyPlan::updateOrCreate([
                'title' => $date->toFormattedDayDateString(),
                'user_id' => $userId,
            ],
                [
                    'title' => $date->toFormattedDayDateString(),
                    'the_day' => $date->toDateTimeString(),
                    'user_id' => $userId,
                ]);
        }

        $activity->due_at = $date->toDateTimeString();
        $activity->daily_plan_id = $dailyPlan->id;

        // process timelines
        // if time needs to be updated
        if ($request->input('from') !== null && $request->input('to') !== null) {
            // Get the starting time and calculate duration
            $startTime = $request->input('from');
            $endTime = $request->input('to');

            // Combine the date and time into a single timestamp
            $combinedStartTimestamp = Carbon::createFromFormat(
                'Y-m-d H:i',
                $date->format('Y-m-d') . ' ' . $startTime);
            $combinedEndTimestamp = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $endTime);

            // Calculate the duration between start and end timestamps
            $duration = $combinedStartTimestamp->diff($combinedEndTimestamp);
            // Get the total duration in hours as a decimal
            $durationInHours = $duration->h + ($duration->i / 60);

            $activity->estimated_time = $durationInHours ?? 2;
            $activity->from = $combinedStartTimestamp->toDateTimeString() ?? null;
            $activity->to = $combinedEndTimestamp->toDateTimeString() ?? null;
        }

        // get the attached-to/parent relationship and construct values
        if ($request->input('todo_id') !== null){
            $activity->todo_id = $request->input('todo_id');
        }
        if ($request->input('task_id') !== null){
            $activity->task_id = $request->input('task_id');
        }
        if ($request->input('project_id') !== null){
            $activity->project_id = $request->input('project_id');
        }
        if ($request->input('daily_target_id') !== null){
            $activity->daily_target_id = $request->input('daily_target_id');
        }

        $activity->save();

        // Create a trackable for the activity
        $trackable = Trackable::create([
            'trackable_id' => $activity->id,
            'trackable_type' => Activity::class,
            'category_id' => $this->getCategoryByName($request->input('category'))->id,
            'visibility_id' => $this->getVisibilityByName($request->input('visibility'))->id,
            'user_id' => $userId,
        ]);

        // Redirect to the activity show page
        return redirect()->route('trackables.show', $trackable->id);
    }
}
