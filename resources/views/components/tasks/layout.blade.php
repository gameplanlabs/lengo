<div class="flex items-start max-md:flex-col">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item icon="square-3-stack-3d" href="{{ route('tasks.index') }}" wire:navigate>All
            </flux:navlist.item>
            <flux:navlist.item icon="document-plus" href="{{ route('tasks.create') }}" wire:navigate>Create
            </flux:navlist.item>
            <flux:navlist.item icon="home" href="{{ route('home') }}" wire:navigate>Home</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
