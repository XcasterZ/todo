<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">
    <style>
        .swal2-confirm {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
        }
        .swal2-confirm:hover {
            background-color: #2563eb !important;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center font-prompt">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">เข้าสู่ระบบ</h1>

        <form id="loginForm" method="POST">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required autofocus>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">รหัสผ่าน</label>
                <input type="password" id="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <button type="submit" id="submitButton"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                เข้าสู่ระบบ
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('register.show') }}" class="text-blue-600 hover:text-blue-800 text-sm">ยังไม่มีบัญชี?
                    สมัครสมาชิกที่นี่</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
    
            const form = e.target;
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;
    
            Swal.fire({
                title: 'กำลังดำเนินการ...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
    
            const formData = {
                username: document.getElementById('username').value,
                password: document.getElementById('password').value,
                _token: document.querySelector('meta[name="csrf-token"]').content
            };
    
            axios.post("{{ route('login') }}", formData)
                .then(response => {
                    Swal.fire({
                        title: 'เข้าสู่ระบบสำเร็จ!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#3b82f6',
                        buttonsStyling: true,
                        background: '#ffffff',
                        backdrop: 'rgba(0,0,0,0.4)'
                    }).then(() => {
                        window.location.href = response.data.redirect || "{{ route('home') }}";
                    });
                })
                .catch(error => {
                    let errorMessage = 'ไม่สามารถเข้าสู่ระบบได้ในขณะนี้';
                    
                    if (error.response && error.response.data) {
                        errorMessage = error.response.data.message || 
                                     'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
                    }
    
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#3b82f6',
                        buttonsStyling: true,
                        background: '#ffffff'
                    });
                })
                .finally(() => {
                    submitButton.disabled = false;
                });
        });
    </script>
</body>
</html>