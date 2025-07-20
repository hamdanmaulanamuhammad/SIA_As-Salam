<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - SIA As-Salam</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icons/favicon.png') }}">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <img src="{{ asset(request()->is('attendance-pengajar') ? 'assets/images/icons/attendance-active.svg' : 'assets/images/icons/attendance.svg') }}" alt="Attendance Icon" class="w-5 h-5" />
                            <span class="ml-4">Kehadiran</span>
                        </a>
                    </li>
                    <li class="relative px-6 py-3">
                        <a class="inline-flex items-center w-full text-sm font-semibold {{ request()->is('pengajar/akademik*') ? 'border-l-4 p-3 border-blue-600 bg-blue-100' : 'text-gray-800' }} transition-colors duration-150 hover:text-gray-800" href="{{ route('pengajar.akademik.index') }}">
                            <img src="{{ asset(request()->is('pengajar/akademik*') ? 'assets/images/icons/akademik-active.svg' : 'assets/images/icons/akademik.svg') }}" alt="Akademik Icon" class="w-5 h-5" />
                            <span class="ml-4">Akademik</span>
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
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                    <a class="flex px-4 py-2 hover:bg-gray-100" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <img src="{{ asset('assets/images/icons/logout.svg') }}" alt="Logout" class="w-4 h-4 mr-2">
                                        <span class="block text-sm text-gray-800">Logout</span>
                                    </a>
                                </div>
                            </div>
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
                document.getElementById('sidebar').classList.add('hidden'); // Hide the sidebar
                document.body.classList.remove('overflow-hidden'); // Remove overflow hidden class
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
