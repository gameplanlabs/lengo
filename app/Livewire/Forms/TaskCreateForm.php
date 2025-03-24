<?php

namespace App\Livewire\Forms;

use App\Models\Task;
use App\Models\Trackable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TaskCreateForm extends Form
{
    #[Validate('required')]
    public string $title = '';
    public string $description = '';
    public string $progress = '0.05';
    public string $status = 'planning';

    public int $categoryId = 0;
    public int $visibilityId = 0;
    #[Validate('required')]
    public int $projectId = 0;
    public int $goalId = 0;
    public ?string $due_at = null;
    public ?string $from = null;
    public ?string $to = null;

    public function save()
    {
        $this->validate();
        $userId = Auth::id();
        $task = Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'progress' => $this->progress,
            'status' => $this->status,
            'user_id' => $userId,
            'project_id' => $this->projectId
        ]);

        // create trackable associated with this task
        $trackable = Trackable::create([
            'trackable_id' => $task->id,
            'trackable_type' => Task::class,
            'user_id' => $userId,
        ]);

        if ($this->categoryId !== 0) {
            $trackable->category_id = $this->categoryId;
        }

        if ($this->visibilityId !== 0) {
            $trackable->visibility_id = $this->visibilityId;
        }
        if ($this->goalId!== 0) {
            $task->goal_id = $this->goalId;
        }

        if ($this->due_at !== null) {
            $date = Carbon::parse($this->due_at);

            if ($this->from !== null && $this->to !== null) {
                $startTime = $this->from;
                $endTime = $this->to;
                $duration = $endTime - $startTime;

                $task->update([
                    'from' => $startTime,
                    'to' => $endTime,
                    'due_at' => $date,
                    'estimated_time' => $duration,
                    'actual_time' => $duration,
                ]);
            } else {
                $task->update([
                    'due_at' => $date,
                ]);
            }

            $task->due_at = $date;
        }

        $trackable->save();
        $task->save();
    }
}
