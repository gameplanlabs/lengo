<?php

namespace App\Http\Controllers\Web;

use App\Enums\TrackableStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Trackable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /*
     * A list of all daily plans
     */
    public function index(): \Inertia\Response
    {
        $projects = Trackable::query()
            ->where('user_id', '=', Auth::id())
            ->where('trackable_type', '=', Project::class)
            ->with(['trackable', 'category', 'user', 'visibility'])
            ->latest()
            ->paginate(12);

        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    /*
     * Store the project
     */
    public function store(Request $request)
    {
        // validate
        $request->validate([
            'title' =>'required',
        ]);

        // get the current user or the admin if none is logged in
        $userId = Auth::id();

        // check subscription
        // if subscribed, continue
        // If not subscribed, and projects count is <= 5, continue
        // else, require subscription or go back

        // create the project
        $project = Project::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'progress' => $request->input('progress') ?? 0.05,
            'status' => $request->input('status') ?? TrackableStatus::IDEATION,
            'user_id' => $userId
        ]);

        // if time needs to be updated
        if ($request->input('due_at') !== null) {
            // Parse the 'due date' input as a Carbon instance
            $date = Carbon::parse($request->input('due_at'));

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

                $project->estimated_time = $durationInHours;
                $project->from = $combinedStartTimestamp;
                $project->to = $combinedEndTimestamp;
            }

            $project->due_at = $date;
        }

        if ($request->input('goal_id') !== null) {
            $project->goal_id = $request->input('goal_id');
        }

        $project->save();

        // create a trackable for the project
        $trackable = Trackable::create([
            'trackable_id' => $project->id,
            'trackable_type' => Project::class,
            'category_id' => $this->getCategoryByName($request->input('category'))->id,
            'visibility_id' => $this->getVisibilityByName($request->input('visibility'))->id,
            'user_id' => $userId,
        ]);

        // redirect to the project page
        return redirect()->route('trackables.show', $trackable->id);
    }
}
