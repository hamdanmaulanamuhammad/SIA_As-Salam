<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - SIA As-Salam</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icons/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app" class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 z-20 flex-shrink-0 w-64 overflow-y-auto bg-white hidden md:block">
            <div class="py-4 text-gray-500 h-full">
                <a class="ml-6 text-lg font-bold text-gray-800" href="#">SIA As-Salam</a>
                <ul class="mt-6">
                    <li class="relative px-6 py-3">
                        <a class="inline-flex items-center w-full text-sm font-semibold {{ request()->routeIs('dashboard-pengajar') ? 'border-l-4 p-3 border-blue-600 bg-blue-100' : 'text-gray-800' }} transition-colors duration-150 hover:text-gray-800" href="{{ route('dashboard-pengajar') }}">
                            <img src="{{ asset(request()->routeIs('dashboard-pengajar') ? 'assets/images/icons/house-active.svg' : 'assets/images/icons/house.svg') }}" alt="Dashboard Icon" class="w-5 h-5" />
                            <span class="ml-4">Dashboard</span>
                        </a>
                    </li>
                    <li class="relative px-6 py-3">
                        <a class="inline-flex items-center w-full text-sm font-semibold {{ request()->is('attendance-pengajar') ? 'border-l-4 p-3 border-blue-600 bg-blue-100' : 'text-gray-800' }} transition-colors duration-150 hover:text-gray-800" href="{{ url('/attendance-pengajar') }}">
                            <img src="{{ asset(request()->is('attendance-pengajar') ? 'assets/images/icons/presence-active.svg' : 'assets/images/icons/presence.svg') }}" alt="Attendance Icon" class="w-5 h-5" />
                            <span class="ml-4">Kehadiran</span>
                        </a>
                    </li>
                    <li class="relative px-6 py-3">
                        <a class="inline-flex items-center w-full text-sm font-semibold {{ request()->is('pengajar/akademik*') ? 'border-l-4 p-3 border-blue-600 bg-blue-100' : 'text-gray-800' }} transition-colors duration-150 hover:text-gray-800" href="{{ route('pengajar.akademik.index') }}">
                            <img src="{{ asset(request()->is('pengajar/akademik*') ? 'assets/images/icons/akademik-active.svg' : 'assets/images/icons/akademik.svg') }}" alt="Akademik Icon" class="w-5 h-5" />
                            <span class="ml-4">Akademik</span>
                        </a>
                    </li>
                    <li class="relative px-6 py-3">
                        <a class="inline-flex items-center w-full text-sm font-semibold {{ request()->routeIs('pengajar.santri*') ? 'border-l-4 p-3 border-blue-600 bg-blue-100' : 'text-gray-800' }} transition-colors duration-150 hover:text-gray-800" href="{{ route('pengajar.santri.index') }}">
                            <img src="{{ asset(request()->routeIs('pengajar.santri*') ? 'assets/images/icons/attendance-active.svg' : 'assets/images/icons/attendance.svg') }}" alt="Santri Icon" class="w-5 h-5" />
                            <span class="ml-4">Santri</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <div class="flex flex-col flex-1 w-full md:ml-64">
            <header class="z-10 py-2 bg-white shadow-md">
                <div class="container flex items-center justify-between h-full px-6 mx-auto">
                    <button id="menu-button" class="p-1 mr-5 -ml-1 rounded-md md:hidden text-blue-600 focus:outline-none" aria-label="Menu">
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <h2 class="my-2 text-xl font-semibold text-gray-700">@yield('title')</h2>
                    <div class="flex items-center">
                        <div class="relative">
                            <button id="profile-button" class="align-middle rounded-full focus:outline-none" aria-label="Account" aria-haspopup="true">
                                <img class="object-cover w-8 h-8 rounded-full" src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://placehold.co/100x100' }}" alt="Profile Picture" aria-hidden="true" />
                            </button>
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                @auth
                                    {{ Auth::user()->username }}
                                @else
                                    Guest
                                @endauth
                            </span>
                            <div id="dropdown" class="absolute right-0 z-20 hidden mt-2 w-48 bg-white rounded-md shadow-lg">
                                <div class="py-1">
                                    <a class="flex px-4 py-2 hover:bg-gray-100" href="{{ route('profile.pengajar.index') }}">
                                        <img src="{{ asset('assets/images/icons/profile.svg') }}" alt="Profile" class="w-3 h-3 mr-3">
                                        <span class="block text-sm text-gray-800">Profile</span>
                                    </a>
                                    <a id="logout-link" class="flex px-4 py-2 hover:bg-gray-100" href="#">
                                        <img src="{{ asset('assets/images/icons/logout.svg') }}" alt="Logout" class="w-4 h-4 mr-2">
                                        <span class="block text-sm text-gray-800">Logout</span>
                                    </a>
                                </div>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="h-full overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const app = {
            isSideMenuOpen: false,
            init() {
                document.getElementById('menu-button').addEventListener('click', () => this.toggleSideMenu());
                document.getElementById('profile-button').addEventListener('click', (event) => {
                    event.stopPropagation(); // Prevent the click from bubbling up
                    this.toggleDropdown();
                });
                document.getElementById('logout-link').addEventListener('click', (event) => {
                    event.preventDefault();
                    this.handleLogout();
                });
                document.addEventListener('click', this.closeDropdown.bind(this));
                window.addEventListener('resize', () => this.handleResize());
            },
            toggleSideMenu() {
                this.isSideMenuOpen = !this.isSideMenuOpen;
                document.getElementById('sidebar').classList.toggle('hidden', !this.isSideMenuOpen);
                document.body.classList.toggle('overflow-hidden', this.isSideMenuOpen);
            },
            closeSideMenu() {
                this.isSideMenuOpen = false;
                document.getElementById('sidebar').classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            },
            toggleDropdown() {
                const dropdown = document.getElementById('dropdown');
                dropdown.classList.toggle('hidden');
            },
            closeDropdown() {
                const dropdown = document.getElementById('dropdown');
                dropdown.classList.add('hidden');
            },
            handleResize() {
                if (window.innerWidth >= 768) {
                    this.isSideMenuOpen = true;
                    document.getElementById('sidebar').classList.remove('hidden');
                } else {
                    this.isSideMenuOpen = false;
                    document.getElementById('sidebar').classList.add('hidden');
                }
            },
            async handleLogout() {
                const form = document.getElementById('logout-form');
                const formData = new FormData(form);
                try {
                    const response = await fetch('{{ route('logout') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: result.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.href = result.redirect;
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: result.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat logout. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        };

        // Menambahkan event listener untuk menutup sidebar saat mengklik di luar sidebar
        document.addEventListener('click', (event) => {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('menu-button');
            if (app.isSideMenuOpen && !sidebar.contains(event.target) && !menuButton.contains(event.target)) {
                app.closeSideMenu();
            }
        });

        document.addEventListener('DOMContentLoaded', () => app.init());

    </script>
    @yield('scripts')
</body>
</html>
