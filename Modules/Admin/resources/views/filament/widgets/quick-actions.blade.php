<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-bold tracking-tight">Quick Operational Actions</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-filament::button
                    href="/admin/provider-profiles/create"
                    tag="a"
                    icon="heroicon-m-user-plus"
                    color="gray"
                    outlined
                >
                    Onboard New Provider
                </x-filament::button>

                <x-filament::button
                    href="/admin/projects/create"
                    tag="a"
                    icon="heroicon-m-briefcase"
                    color="gray"
                    outlined
                >
                    Initialize New Project
                </x-filament::button>

                <x-filament::button
                    href="/admin/provider-services/create"
                    tag="a"
                    icon="heroicon-m-shopping-bag"
                    color="gray"
                    outlined
                >
                    Add Marketplace Offering
                </x-filament::button>

                <x-filament::button
                    href="/admin/team-members/create"
                    tag="a"
                    icon="heroicon-m-shield-check"
                    color="gray"
                    outlined
                >
                    Manage Agency Users
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
