<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - SIA As-Salam</title>
    @vite('resources/css/app.css') <!-- Menggunakan Vite untuk mengimpor CSS -->
</head>
<body>
    <div class="flex items-center min-h-screen p-6 bg-gray-50">
        <div class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl">
            <div class="flex flex-col overflow-y-auto md:flex-row">
                <div class="h-32 md:h-auto md:w-1/2">
                    <img aria-hidden="true" class="object-cover w-full h-full" src="{{ asset('assets/images/logo-square.jpeg') }}" alt="Logo" />
                </div>
                <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                    <div class="w-full">
                        <h1 class="mb-6 text-2xl font-semibold text-gray-700">Login - SIA As-Salam</h1>
                        <form action="{{ route('dashboard-admin') }}" method="GET"> <!-- Mengarahkan ke dashboard tanpa logika login -->
                            @csrf
                            <label class="block text-base">
                                <span class="text-gray-700">Email</span>
                                <input class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="email" placeholder="email" required />
                            </label>
                            <label class="block mt-4 text-base mb-10">
                                <span class="text-gray-700">Password</span>
                                <input class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="password" placeholder="***************" type="password" required />
                            </label>
                            <button type="submit" class="block w-full px-4 py-3 mt-4 text-sm lg:text-lg font-medium leading-5 text-center text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">Log in</button>
                        </form>
                        <hr class="my-8" />
                        <p class="mt-1">
                            <a class="text-sm lg:text-lg font-medium text-blue-600 hover:underline" href="{{ route('register') }}">Register</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
