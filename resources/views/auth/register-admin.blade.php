<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Admin - SIA As-Salam</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icons/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="flex items-center min-h-screen p-6 bg-gray-50">
        <div class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl">
            <div class="flex flex-col overflow-y-auto md:flex-row">
                <div class="h-32 md:h-auto md:w-1/2">
                    <img aria-hidden="true" class="object-contain w-full h-full" src="{{ asset('assets/images/LogoAuth.png') }}" alt="Logo" />
                </div>
                <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                    <div class="w-full">
                        <h1 class="mb-6 text-2xl font-semibold text-gray-700">Register Admin - SIA As-Salam</h1>
                        <form id="registerForm" method="POST" action="{{ route('admin.register.store') }}">
                            @csrf
                            <label class="block text-base" for="username">
                                <span class="text-gray-700">Username</span>
                                <input id="username" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="username" placeholder="Username" required />
                            </label>
                            <label class="block text-base mt-4" for="full_name">
                                <span class="text-gray-700">Full Name</span>
                                <input id="full_name" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="full_name" placeholder="Full Name" required />
                            </label>
                            <label class="block text-base mt-4" for="email">
                                <span class="text-gray-700">Email</span>
                                <input id="email" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="email" placeholder="Email" type="email" required />
                            </label>
                            <label class="block text-base mt-4" for="phone">
                                <span class="text-gray-700">Phone</span>
                                <input id="phone" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="phone" placeholder="Phone" required />
                            </label>
                            <label class="block text-base mt-4" for="university">
                                <span class="text-gray-700">University</span>
                                <input id="university" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="university" placeholder="University" required />
                            </label>
                            <label class="block text-base mt-4" for="address">
                                <span class="text-gray-700">Address</span>
                                <input id="address" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600" name="address" placeholder="Address" required />
                            </label>
                            <label class="block text-base mt-4" for="password">
                                <span class="text-gray-700">Password</span>
                                <div class="relative">
                                    <input id="password" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600 pr-10" name="password" placeholder="***************" type="password" required />
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                                        <i id="password-eye" class="fas fa-eye text-gray-500 hover:text-gray-700"></i>
                                    </button>
                                </div>
                            </label>
                            <label class="block text-base mt-4 mb-10" for="password_confirmation">
                                <span class="text-gray-700">Confirm Password</span>
                                <div class="relative">
                                    <input id="password_confirmation" class="block w-full p-2 mt-1 text-sm lg:text-lg border-gray-300 rounded-md focus:border-blue-600 focus:outline-none focus:shadow-outline-blue form-input dark:bg-gray-100 dark:border-gray-600 pr-10" name="password_confirmation" placeholder="***************" type="password" required />
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password_confirmation')">
                                        <i id="password_confirmation-eye" class="fas fa-eye text-gray-500 hover:text-gray-700"></i>
                                    </button>
                                </div>
                            </label>
                            <button type="submit" class="block w-full px-4 py-3 mt-4 text-sm lg:text-lg font-medium leading-5 text-center text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">Register Admin</button>
                        </form>
                        <hr class="my-8" />
                        <p class="mt-1">
                            <a class="text-sm lg:text-lg font-medium text-blue-600 hover:underline" href="{{ route('login') }}">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi toggle password
            window.togglePassword = function(fieldId) {
                const passwordField = document.getElementById(fieldId);
                const eyeIcon = document.getElementById(fieldId + '-eye');
                if (passwordField && eyeIcon) {
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
            }

            // Handle form submission dengan AJAX
            const registerForm = document.getElementById('registerForm');
            registerForm.addEventListener('submit', async function(event) {
                event.preventDefault(); // Mencegah submit default

                const formData = new FormData(registerForm);
                try {
                    const response = await fetch('{{ route('admin.register.store') }}', {
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
                            window.location.href = result.redirect; // Redirect ke login
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
                        text: 'Terjadi kesalahan saat mendaftar admin. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
</body>
</html>
