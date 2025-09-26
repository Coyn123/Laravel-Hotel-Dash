<div class="dashboard-wrapper"> {{-- ✅ single root element --}}

    {{-- Header --}}
    <header class="mb-6">
        <div class="header-inner flex items-center justify-between">
            <div class="brand flex items-center space-x-2">
                <div class="logo font-bold text-lg" aria-hidden="true">Coyner</div>
                <div class="title text-xl">Hospitality Dashboard</div>
            </div>
            <div class="controls ml-auto">
                <div class="notification-wrapper relative">
                    <button class="notification-btn"
                            id="notificationBtn"
                            aria-label="View notifications"
                            aria-expanded="false"
                            aria-controls="notificationBox">
                        🔔
                    </button>
                    <div class="notification-box absolute right-0 mt-2 w-64 bg-white shadow-lg rounded hidden"
                         id="notificationBox">
                        <h4 class="notification-title font-semibold p-2 border-b">Notifications</h4>
                        <div class="notification-content p-2" id="notificationContent">
                            <p>Loading…</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Floors view --}}
    <section class="mb-8">
        @livewire('floors-view')
    </section>
</div>
