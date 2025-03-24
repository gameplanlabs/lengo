<?php

use App\Enums\TrackableStatus;
use App\Models\User;
use App\Livewire\Forms\TaskCreateForm;
use App\Models\Project;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public TaskCreateForm $form;

    public array $availableStatuses = [];
    public ?Collection $availableProjects = null;

    public function mount()
    {
        $this->availableStatuses = TrackableStatus::getAvailableStatuses();
        $this->availableProjects = Project::where('user_id', auth()->id())->get(['id', 'title']);
    }

    public function create()
    {
        $this->form->save();

        session()->flash('success', 'Task created successfully.');

        $this->redirectRoute('tasks.index');
    }
}; ?>

<section class="w-full">
    <x-tasks.layout heading="Create Task" subheading="Start a new task.">
        <div class="mt-6 p-3 py-5 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <form wire:submit="create">
                <div class="space-y-6">
                    <!-- Name -->
                    <flux:input wire:model="form.title" id="title" label="{{ __('Title') }}" type="text"
                        name="title" required autofocus autocomplete="title" placeholder="Task title" />

                    <flux:input wire:model="form.due_at" id="due_at" label="{{ __('Deadline') }}" type="datetime-local"
                        name="due_at" required autofocus autocomplete="due_at" placeholder="Task deadline" />

                    <flux:textarea rows="2" label="{{ __('Details') }}" wire:model="form.description" />

                    <flux:select wire:model="form.status" label="Task Status" size="sm" placeholder="Choose status...">
                        @foreach ($availableStatuses as $status)
                            <flux:select.option value="{{ $status }}">{{ ucfirst($status) }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="form.projectId" label="Project" size="sm" placeholder="Choose a project...">
                        @foreach ($availableProjects as $project)
                            <flux:select.option value="{{ $project->id }}">{{ ucfirst($project->title) }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="sm:col-span-6">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create
                        </button>
                    </div>
                </div>
            </form>
    </x-tasks.layout>
</section>
