<?php

use App\Models\User;
use App\Models\Activity;
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
            'activities' => Activity::query()->where('user_id', Auth::id())->paginate($this->perPage),
        ];
    }
}; ?>

<section class="w-full">

    <x-activities.layout heading="Activities" subheading="View and manage activities.">
        <div>
            <div class="mt-6 p-3 py-5 shadow sm:rounded-lg">
                <div class="space-y-2.5">
                    @forelse ($activities as $activity)
                        <div
                            x-data="{
                                showActivity{{ $activity->id }}: false,
                                toggle() {
                                    this.showActivity{{ $activity->id }}=!this.showActivity{{ $activity->id }}
                                    }
                                }"
                            class="p-4 flex justify-between items-center gap-x-2 bg-gray-100 dark:bg-gray-800 rounded-lg"
                        >
                            <flux:heading>
                                {{ $activity->title }} ~ <flux:badge color="lime" inset="top bottom">{{ $activity->status }}</flux:badge>
                            </flux:heading>
                            
                            <div>
                                <flux:modal.trigger name="view-activity{{ $activity->id }}">
                                    <flux:button icon:trailing="eye">View</flux:button>
                                </flux:modal.trigger>

                                <flux:modal name="view-activity{{ $activity->id }}" class="md:w-96">
                                    <div class="space-y-1">
                                        <flux:heading size="lg"><span class="text-xs uppercase">Status: </span>{{ $activity->status }}</flux:heading>
                                        <flux:text class="mt-2">{{ $activity->description }}</flux:text>
                                    </div>
                                </flux:modal>
                            </div>
                        </div>
                    @empty
                        <div class="">{{ __('No activities found.') }}</div>
                    @endforelse
                </div>

                <div class="flex justify-between items-center p-3">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>
    </x-activities.layout>
</section>
