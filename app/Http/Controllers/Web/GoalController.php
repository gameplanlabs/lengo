<?php

namespace App\Http\Controllers\Web;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Trackable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GoalController extends Controller
{
    /**
     * Display a listing of the goals.
     *
     * @return Response
     */
    public function index(): Response
    {
        $goals = Trackable::query()
            ->where('user_id', '=', Auth::id())
            ->where('trackable_type', '=', Goal::class)
            ->with(['trackable', 'category', 'user', 'visibility'])
            ->latest()
            ->paginate(12);

        return Inertia::render('Goals/Index',[
            "goals" => $goals
        ]);
    }

    /**
     * Store a new goal
     */
    public function store(Request $request)
    {
        // authorize request

        // validate
        $request->validate([
            'title' =>'required',
        ]);

        // get the current user or the admin if none is logged in
        $userId = Auth::id();

        // create the project
        $task = Goal::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'progress' => $request->input('progress') ?? 0.05,
            'status' => $request->input('status') ?? TrackableStatus::IDEATION,
            'user_id' => $userId
        ]);

        // if time needs to be updated
        if (!is_null($request->input('due_at'))) {
            // Parse the 'due date' input as a Carbon instance
            $date = Carbon::parse($request->input('due_at'));

            if (!is_null($request->input('from')) && !is_null($request->input('to'))) {
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

                $task->estimated_time = $durationInHours;
                $task->from = $combinedStartTimestamp;
                $task->to = $combinedEndTimestamp;
            }

            $task->due_at = $date;
        }

        $task->save();

        // create a trackable for the project
        $trackable = Trackable::create([
            'trackable_id' => $task->id,
            'trackable_type' => Goal::class,
            'category_id' => $this->getCategoryByName($request->input('category'))->id,
            'visibility_id' => $this->getVisibilityByName($request->input('visibility'))->id,
            'user_id' => $userId,
        ]);

        // redirect to the project page
        return redirect()->route('trackables.show', $trackable->id);
    }
}
