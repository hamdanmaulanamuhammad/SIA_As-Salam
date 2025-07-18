<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - SIA As-Salam</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="flex items-center min-h-screen p-6 bg-gray-50">
        <div class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl">
            <div class="flex flex-col overflow-y-auto md:flex-row">
                <div class="h-32 md:h-auto md:w-1/2">
                    <img aria-hidden="true" class="object-contain w-full h-full" src="{{ asset('assets/images/LogoAuth.png') }}" alt="Office" />
                </div>
                <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                    <div class="w-full">
                        <h1 class="mb-6 text-2xl font-semibold text-gray-700">Register - SIA As-Salam</h1>
                        <form action="{{ route('register.store') }}" method="post">
                            @csrf
                            <label class="block text-base" for="username">
                                <span class="text-gray-700">Username</span>
                                <input id="username" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue" name="username" placeholder="Username" value="{{ old('username') }}" required />
                            </label>
                            <label class="block mt-4 text-base" for="full_name">
                                <span class="text-gray-700">Nama Lengkap</span>
                                <input id="full_name" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue" name="full_name" placeholder="Nama Lengkap" value="{{ old('full_name') }}" required />
                            </label>
                            <label class="block mt-4 text-base" for="email">
                                <span class="text-gray-700">Email</span>
                                <input id="email" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue" name="email" placeholder="Email" value="{{ old('email') }}" required />
                            </label>
                            <label class="block mt-4 text-base" for="phone">
                                <span class="text-gray-700">No Telp</span>
                                <input id="phone" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue" name="phone" placeholder="No. Telp" value="{{ old('phone') }}" required />
                            </label>
                            <label class="block mt-4 text-base" for="university">
                                <span class="text-gray-700">Universitas</span>
                                <input id="university" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue" name="university" placeholder="Nama Universitas" value="{{ old('university') }}" required />
                            </label>
                            <label class="block mt-4 text-base" for="address">
                                <span class="text-gray-700">Alamat</span>
                                <input id="address" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue" name="address" placeholder="Alamat" value="{{ old('address') }}" required />
                            </label>
                            <label class="block mt-4 text-base" for="password">
                                <span class="text-gray-700">Password</span>
                                <div class="relative">
                                    <input id="password" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue pr-10" name="password" type="password" placeholder="***************" required />
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                                        <i id="password-eye" class="fas fa-eye text-gray-500 hover:text-gray-700"></i>
                                    </button>
                                </div>
                            </label>
                            <label class="block mt-4 text-base mb-10" for="password_confirmation">
                                <span class="text-gray-700">Konfirmasi Password</span>
                                <div class="relative">
                                    <input id="password_confirmation" class="block w-full p-2 mt-1 text-sm md:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue pr-10" name="password_confirmation" type="password" placeholder="***************" required />
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password_confirmation')">
                                        <i id="password_confirmation-eye" class="fas fa-eye text-gray-500 hover:text-gray-700"></i>
                                    </button>
                                </div>
                            </label>
                            <button type="submit" class="block w-full px-4 py-3 mt-4 text-sm md:text-lg font-medium leading-5 text-center text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">Register</button>
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

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Show success message
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // Show error messages
        @if($errors->any())
            let errorMessage = '';
            @foreach($errors->all() as $error)
                errorMessage += '{{ $error }}\n';
            @endforeach

            Swal.fire({
                title: 'Error!',
                text: errorMessage,
                icon: 'error',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif
    </script>
</body>
</html>
