<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>สมัครสมาชิก</title>
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .swal2-popup {
            font-family: 'Prompt', sans-serif;
        }
    </style>
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">

</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center font-prompt">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">สร้างบัญชีผู้ใช้</h1>

        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        html: `<ul class="text-left">${@json($errors->all()).map(error => `<li>${error}</li>`).join('')}</ul>`,
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#3b82f6',
                    });
                });
            </script>
        @endif

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'สมัครสมาชิกสำเร็จ!',
                        text: '{{ session('success') }}',
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#3b82f6',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login.show') }}";
                        }
                    });
                });
            </script>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">อีเมล</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">รหัสผ่าน</label>
                <input type="password" id="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-6">
                <label for="password_confirmation"
                    class="block text-gray-700 text-sm font-bold mb-2">ยืนยันรหัสผ่าน</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                สมัครสมาชิก
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('login.show') }}" class="text-blue-600 hover:text-blue-800 text-sm">มีบัญชีแล้ว?
                    เข้าสู่ระบบที่นี่</a>
            </div>
        </form>
    </div>

</body>

<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');

        submitButton.disabled = true;

        Swal.fire({
            title: 'กำลังดำเนินการ...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        axios.post(this.action, formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                Swal.fire({
                    title: 'สมัครสมาชิกสำเร็จ!',
                    text: response.data.message || 'คุณได้สมัครสมาชิกเรียบร้อยแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    showCancelButton: false,
                    allowOutsideClick: false,
                    buttonsStyling: false, 
                    customClass: {
                        confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login.show') }}";
                    }
                });
            })
            .catch(error => {
                let errorMessage = 'ไม่สามารถสมัครสมาชิกได้ในขณะนี้';

                if (error.response && error.response.data) {
                    if (error.response.data.errors) {
                        errorMessage = Object.values(error.response.data.errors).join('<br>');
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                }

                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    html: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#3b82f6',
                    buttonsStyling: true, 
                    customClass: {
                        confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded' 
                    }
                });
            })
            .finally(() => {
                submitButton.disabled = false;
            });
    });
</script>

</html>
