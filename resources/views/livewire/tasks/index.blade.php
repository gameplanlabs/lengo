<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $perPage = 12;
    /**
     * Mount the component.
     */
    public function with()
    {
        return [
            'tasks' => Task::query()->where('user_id', Auth::id())->paginate($this->perPage),
        ];
    }
}; ?>

<section class="w-full">
    <x-tasks.layout heading="Tasks" subheading="View and manage tasks.">
        <div>
            <div class="">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($tasks as $task)
                        <div class="mb-4 grid space-y-2 mt-6 p-3 py-5 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                            <flux:heading>{{ $task->title }}</flux:heading>
                            <flux:subheading><span class="text-xs uppercase">Status: </span>{{ $task->status }}
                            </flux:subheading>
                            <flux:subheading>{{ $task->description }}</flux:subheading>
                        </div>
                    @empty
                        <div class="">{{ __('No tasks found.') }}</div>
                    @endforelse
                </div>

                <div class="flex justify-between items-center p-3">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </x-tasks.layout>
</section>
