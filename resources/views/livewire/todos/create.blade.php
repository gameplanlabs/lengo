<?php

use App\Enums\TrackableStatus;
use App\Livewire\Forms\TodoCreateForm;
use Livewire\Volt\Component;

new class extends Component {
    public TodoCreateForm $form;

    public array $availableStatuses = [];

    public function mount()
    {
        $this->availableStatuses = TrackableStatus::getAvailableStatuses();
    }

    public function create()
    {
        $this->form->save();

        session()->flash('success', 'Todo created successfully.');

        $this->redirectRoute('todos.index');
    }
}; ?>

<section class="w-full">
    <x-todos.layout heading="Create Todo" subheading="Start a new project.">
        <div class="mt-6 p-3 py-5 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <form wire:submit="create">
                <div class="space-y-6">
                    <!-- Name -->
                    <flux:input wire:model="form.title" id="title" label="{{ __('Title') }}" type="text"
                        name="title" required autofocus autocomplete="title" placeholder="Todo title" />

                    <flux:textarea rows="2" label="{{ __('Details') }}" wire:model="form.description" />

                    <flux:select label="Todo Status" size="sm" placeholder="Choose status...">
                        @foreach ($availableStatuses as $status)
                            <flux:select.option value="{{ $status }}">{{ ucfirst($status) }}</flux:select.option>
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
    </x-todos.layout>
</section>
