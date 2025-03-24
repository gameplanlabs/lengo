<?php

use App\Models\User;
use App\Models\Todo;
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
            'todos' => Todo::query()->where('user_id', Auth::id())->paginate($this->perPage),
        ];
    }
}; ?>

<section class="w-full">

    <x-todos.layout heading="Todos" subheading="View and manage todos.">
        <div>
            <div class="mt-6 p-3 py-5 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($todos as $project)
                        <div class="mb-4 grid space-y-2">
                            <flux:heading>{{ $project->title }}</flux:heading>
                            <flux:subheading><span class="text-xs uppercase">Status: </span>{{ $project->status }}
                            </flux:subheading>
                            <flux:separator text="Details" />
                            <flux:subheading>{{ $project->description }}</flux:subheading>
                        </div>
                    @empty
                        <div class="">{{ __('No todos found.') }}</div>
                    @endforelse
                </div>

                <div class="flex justify-between items-center p-3">
                    {{ $todos->links() }}
                </div>
            </div>
        </div>
    </x-todos.layout>
</section>
