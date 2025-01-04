<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - SIA As-Salam</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="flex items-center min-h-screen p-6 bg-gray-50">
        <div class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl">
            <div class="flex flex-col overflow-y-auto md:flex-row">
                <div class="h-32 md:h-auto md:w-1/2">
                    <img aria-hidden="true" class="object-contain w-full h-full" src="{{ asset('assets/images/logo-square.jpeg') }}" alt="Office" />
                </div>
                <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                    <div class="w-full">
                        <h1 class="mb-6 text-2xl font-semibold text-gray-700">Register - SIA As-Salam</h1>
                        <form action="{{ route('register.store') }}" method="post">
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <label class="block text-base" for="username">
                                <span class="text-gray-700">Username</span>
                                <input id="username" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="username" placeholder="Username" required />
                            </label>
                            <label class="block mt-4 text-base" for="full_name">
                                <span class="text-gray-700">Nama Lengkap</span>
                                <input id="full_name" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="full_name" placeholder="Nama Lengkap" required />
                            </label>
                            <label class="block mt-4 text-base" for="email">
                                <span class="text-gray-700">Email</span>
                                <input id="email" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="email" placeholder="Email" required />
                            </label>
                            <label class="block mt-4 text-base" for="phone">
                                <span class="text-gray-700">No Telp</span>
                                <input id="phone" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="phone" placeholder="No. Telp" required />
                            </label>
                            <label class="block mt-4 text-base" for="university">
                                <span class="text-gray-700">Universitas</span>
                                <input id="university" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="university" placeholder="Nama Universitas" required />
                            </label>
                            <label class="block mt-4 text-base" for="address">
                                <span class="text-gray-700">Alamat</span>
                                <input id="address" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="address" placeholder="Alamat" required />
                            </label>
                            <label class="block mt-4 text-base" for="password">
                                <span class="text-gray-700">Password</span>
                                <input id="password" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="password" type="password" placeholder="***************" required />
                            </label>
                            <label class="block mt-4 text-base mb-10" for="password_confirmation">
                                <span class="text-gray-700">Konfirmasi Password</span>
                                <input id="password_confirmation" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md" name="password_confirmation" type="password" placeholder="***************" required />
                            </label>
                            <button type="submit" class="block w-full px-4 py-3 mt-4 text-sm md:text-lg font-medium leading-5 text-center text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg">Register</button>
                        </form>
                        <hr class="my-8" />
                        <p class="mt-1">
                            <a class="text-sm md:text-lg font-medium text-blue-600 hover:underline" href="{{ route('login') }}">Already have an account? Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
 