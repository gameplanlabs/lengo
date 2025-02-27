<?php

namespace App\Http\Controllers\Web;

use App\Enums\TrackableStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\Todo;
use App\Models\Trackable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;

class TodoController extends Controller
{
    /*
     * A list of all to-dos
     */
    public function index(): Response
    {
        $todos = Trackable::query()
            ->where('user_id', '=', Auth::id())
            ->where('trackable_type', '=', Todo::class)
            ->with(['trackable', 'category', 'user', 'visibility'])
            ->latest()
            ->paginate(12);
        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'projects' => Trackable::query()
                ->where('trackable_type', '=', Project::class)
                ->where('user_id', '=', Auth::id())
                ->with(['trackable', 'category', 'user', 'visibility'])
                ->get(),
            'tasks' => Trackable::query()
                ->where('trackable_type', '=', Task::class)
                ->where('user_id', '=', Auth::id())
                ->with(['trackable'])
                ->get(),
        ]);
    }

    /*
     * Store a to-do in the database
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

        // create the to-do
        $todo = Todo::create([
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

                $todo->estimated_time = $durationInHours;
                $todo->from = $combinedStartTimestamp;
                $todo->to = $combinedEndTimestamp;
            }
            $todo->due_at = $date;
        }

        if ($request->input('task_id') !== null) {
            $todo->task_id = $request->input('task_id');
        }
        if ($request->input('project_id') !== null) {
            $todo->project_id = $request->input('project_id');
        }

        $todo->save();

        // create a trackable for the to-do
        $trackable = Trackable::create([
            'trackable_id' => $todo->id,
            'trackable_type' => Todo::class,
            'category_id' => $this->getCategoryByName($request->input('category'))->id,
            'visibility_id' => $this->getVisibilityByName($request->input('visibility'))->id,
            'user_id' => $userId,
        ]);

        // redirect to the project page
        return redirect()->route('trackables.show', $trackable->id);
    }
}
