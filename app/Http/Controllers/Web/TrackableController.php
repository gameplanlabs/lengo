<?php

namespace App\Http\Controllers\Web;

use App\Actions\StatusUpdate;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyTarget;
use App\Models\Goal;
use App\Models\Objective;
use App\Models\Project;
use App\Models\Task;
use App\Models\Todo;
use App\Models\Trackable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class TrackableController extends Controller
{
    /**
     * Display a listing of the trackables.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Trackables/Index',[
            "trackables" => Trackable::query()
                ->where('user_id', auth()->id())
                ->whereNotNull('trackable_type')
                ->with(['user', 'category', 'visibility', 'trackable', ])
                ->latest()->paginate(12)
        ]);
    }

    /**
     * Display the specified trackable item.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, int $id): Response
    {
        // Retrieve the trackable item by ID, eager loading the necessary relationships
        $trackable = Trackable::with([
            'user',
            'category',
            'visibility',
            'trackable',
        ])->findOrFail($id);

        /* Get the trackable type from the trackable_type string */
        $trackableType = $this->getTrackableType($trackable);

        // Return the Inertia response with the trackable data
        return Inertia::render('Trackables/Show', [
            'trackable' => $trackable,
            'trackableType' => $trackableType,
        ]);
    }

    /*
     * Update a trackable and corresponding type with details
     */
    public function update(Request $request, int $id)
    {
        // authorize
        //$this->authorize();

        // fetch trackable by id
        $item = Trackable::query()->where('id', '=', $id)->first();
        // fetch trackable type from the trackable_type string
        $type = $this->getTrackableType($item); // e.g project, task, activity, etc

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
                    'Y-m-d H:i', $date->format('Y-m-d') . ' ' . $startTime);
                $combinedEndTimestamp = Carbon::createFromFormat(
                    'Y-m-d H:i', $date->format('Y-m-d') . ' ' . $endTime);

                // Calculate the duration between start and end timestamps
                $duration = $combinedStartTimestamp->diff($combinedEndTimestamp);
                // Get the total duration in hours as a decimal
                $durationInHours = $duration->h + ($duration->i / 60);

                $type->estimated_time = $durationInHours;
                $type->from = $combinedStartTimestamp;
                $type->to = $combinedEndTimestamp;
            }

            $type->due_at = $date;
        }

        // update progress
        if (!is_null($request->input('itemProgress'))) {
            $type->progress = $request->input('itemProgress');
        }
        // update status
        if (!is_null($request->input('status'))) {
            $type->status = $request->input('status');
        }
        // update priority
        if (!is_null($request->input('priority'))) {
            $type->priority = $request->input('priority');
        }
        // update category
        if (!is_null($request->input('category_id'))) {
            $item->category_id = $request->input('category_id');
        }
        // update visibility
        if (!is_null($request->input('visibility_id'))) {
            $item->visibility_id = $request->input('visibility_id');
        }
        // update project
        if (!is_null($request->input('project_id'))) {
            $type->project_id = $request->input('project_id');
        }

        // update actions
        if (!is_null($request->input('action'))) {
            $action = $request->input('action');

            if ($action == 'delete') {
                $type->delete();
                $item->delete();

                return redirect()->route('home');
            } else {
                StatusUpdate::update($type, $action);
            }
        }
        // save all
        $item->save();
        $type->save();

        // get updated trackable
        $updatedTrackable = Trackable::with([
            'user',
            'category',
            'visibility',
            'trackable',
        ])->findOrFail($id);

        // redirect to show page
        return redirect()->route('trackables.show', $updatedTrackable->id);
    }

    /**
     * Determine the trackable type based on the trackable_type string.
     *
     * @param Trackable $trackable
     * @return mixed|null
     */
    protected function getTrackableType(Trackable $trackable): mixed
    {
        $trackableTypeMap = [
            'App\Models\Goal' => Goal::query()
                ->with(['projects', 'objectives', 'tasks', 'todos', 'tasks', 'activities'])
                ->where('id', '=', $trackable->trackable_id)
                ->first(),
            'App\Models\Project' => Project::query()
                ->with(['goal', 'tasks', 'todos', 'tasks', 'activities'])
                ->where('id', '=', $trackable->trackable_id)
                ->first(),
            'App\Models\Task' => Task::query()
                ->with(['project', 'activities', 'todos', 'goal'])
                ->where('id', '=', $trackable->trackable_id)
                ->first(),
            'App\Models\Todo' => Todo::query()
                ->with(['project', 'activities', 'task', 'goal'])
                ->where('id', '=', $trackable->trackable_id)
                ->first(),
            'App\Models\Objective' => Objective::query()
                ->with(['goal', 'dailyPlan'])
                ->where('id', '=', $trackable->trackable_id)
                ->first(),
            'App\Models\Activity' => Activity::query()
                ->with(['project', 'todo', 'task', 'goal'])
                ->where('id', '=', $trackable->trackable_id)
                ->first(),
            'App\Models\DailyPlan' => DailyPlan::query()
                ->where('id', '=', $trackable->trackable_id)
                ->first(),
            //'App\Models\DailyTarget' => DailyTarget::query()->where('id', '=', $trackable->trackable_id)->first(),
        ];

        return $trackableTypeMap[$trackable->trackable_type] ?? null;
    }

    /**
     * Api get details of a trackable item such as a goal, project, activity, etc
     */
    public function getItem(Request $request, string $reference): JsonResponse
    {
        // authenticate user

        // get the model type from request
        $trackableType = $request->input('type'); // e.g. `goal` or `task`
        $typeModel = 'App\\Models\\'. ucfirst($trackableType);

        try {
            /* @var Model $trackableItem Load the type from the model with relations */
            $trackableItem = $typeModel::query()
                ->where('reference', $reference)
                ->first();

            $responseCode = 200;
            $result = [
                'trackableType' => $trackableItem,
                'trackable' => Trackable::firstWhere('trackable_id', $trackableItem->id)
            ];

            if (!$trackableItem) {
                $result = ['message' => 'Trackable item not found'];
                $responseCode = 404;
            }

        } catch (\Exception $e) {
            $result = ['message' => 'An error occurred: '. $e->getMessage()];
            $responseCode = 500;
        }

        // return json
        return response()->json($result, $responseCode);
    }
}
