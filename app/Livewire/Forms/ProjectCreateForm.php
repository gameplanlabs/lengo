<?php

namespace App\Livewire\Forms;

use App\Models\Project;
use App\Models\Trackable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProjectCreateForm extends Form
{
    #[Validate('required')]
    public string $title = '';
    public string $description = '';
    public string $progress = '0.05';
    public string $status = 'ideation';

    public int $categoryId = 0;
    public int $visibilityId = 0;
    public ?string $due_at = null;
    public ?string $from = null;
    public ?string $to = null;

    public function save()
    {
        $this->validate();
        $project = Project::create([
            'title' => $this->title,
            'description' => $this->description,
            'progress' => $this->progress,
            'status' => $this->status,
            'user_id' => $userId=Auth::id()
        ]);

        // create trackable associated with this project
        $trackable = Trackable::create([
            'trackable_id' => $project->id,
            'trackable_type' => Project::class,
            'user_id' => $userId,
        ]);

        if ($this->categoryId !== 0) {
            $trackable->category_id = $this->categoryId;
        }

        if ($this->visibilityId !== 0) {
            $trackable->visibility_id = $this->visibilityId;
        }

        if ($this->due_at !== null) {
            $date = Carbon::parse($this->due_at);

            if ($this->from !== null && $this->to !== null) {
                $startTime = $this->from;
                $endTime = $this->to;
                $duration = $endTime - $startTime;

                $trackable->update([
                    'due_at' => $date,
                    'from' => $startTime,
                    'to' => $endTime,
                    'duration' => $duration,
                ]);
            } else {
                $trackable->update([
                    'due_at' => $date,
                ]);
            }

            $project->due_at = $date;
        }

        $trackable->save();
        $project->save();
    }
}
