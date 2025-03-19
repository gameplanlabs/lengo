<?php

use App\Models\User;
use App\Models\Project;
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
            'projects' => Project::query()->where('user_id', Auth::id())->paginate($this->perPage),
        ];
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-projects.layout heading="Projects" subheading="View and manage your projects.">
        <div>
            <div class="mt-6 p-3 py-5 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($projects as $project)
                        <div class="mb-4 grid space-y-2">
                            <flux:heading>{{ $project->title }}</flux:heading>
                            <flux:subheading><span class="text-xs uppercase">Status: </span>{{ $project->status }}
                            </flux:subheading>
                            <flux:separator text="Details" />
                            <flux:subheading>{{ $project->description }}</flux:subheading>
                        </div>
                    @empty
                        <div class="">{{ __('No projects found.') }}</div>
                    @endforelse
                </div>

                <div class="flex justify-between items-center p-3">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </x-projects.layout>
</section>
