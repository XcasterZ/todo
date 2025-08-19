<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="{{ route('home') }}" class="flex items-center py-4 px-2">
                            <span class="font-semibold text-gray-500 text-lg">Todo List</span>
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <span class="py-2 px-4 text-gray-700 font-medium">สวัสดี, {{ auth()->user()->username }}</span>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="py-2 px-4 bg-red-500 text-white rounded hover:bg-red-600 transition duration-300 flex items-center">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login.show') }}"
                            class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-300 flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </a>
                        <a href="{{ route('register.show') }}"
                            class="py-2 px-4 bg-green-500 text-white rounded hover:bg-green-600 transition duration-300 flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-plus-circle text-blue-500 mr-2"></i> เพิ่มรายการใหม่
            </h2>
            <form id="create-todo-form" class="space-y-4">
                @csrf
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">หัวข้อ</label>
                    <input type="text" id="title" name="title" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">รายละเอียด
                        (ไม่จำเป็น)</label>
                    <textarea id="description" name="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="flex border-b">
                <button id="incomplete-tab"
                    class="flex-1 py-4 px-6 text-center font-medium text-gray-700 hover:text-blue-500 focus:outline-none border-b-2 border-blue-500">
                    <i class="fas fa-tasks mr-2"></i> รายการที่ยังไม่ได้ทำ
                </button>
                <button id="completed-tab"
                    class="flex-1 py-4 px-6 text-center font-medium text-gray-700 hover:text-green-500 focus:outline-none">
                    <i class="fas fa-check-circle mr-2"></i> รายการที่ทำแล้ว
                </button>
                <button id="my-todos-tab"
                    class="flex-1 py-4 px-6 text-center font-medium text-gray-700 hover:text-purple-500 focus:outline-none">
                    <i class="fas fa-user mr-2"></i> รายการที่คุณสร้าง
                </button>
            </div>
        </div>

        <div id="todo-container">
        </div>
    </div>

    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">แก้ไขรายการ</h3>
                <form id="edit-todo-form" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-todo-id">
                    <div>
                        <label for="edit-title" class="block text-sm font-medium text-gray-700">หัวข้อ</label>
                        <input type="text" id="edit-title" name="title" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    <div>
                        <label for="edit-description" class="block text-sm font-medium text-gray-700">รายละเอียด</label>
                        <textarea id="edit-description" name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                            ยกเลิก
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="completion-details-modal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">ผู้ที่เสร็จสิ้นรายการ</h3>
                    <button onclick="closeCompletionDetailsModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="completion-details-content" class="space-y-3 max-h-80 overflow-y-auto">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentTab = 'incomplete';
        let currentTodos = [];
        let loadingSwal = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadTodos();
            setupEventListeners();
        });

        function showLoading(title = 'กำลังโหลด...') {
            loadingSwal = Swal.fire({
                title: title,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function hideLoading() {
            if (loadingSwal) {
                loadingSwal.close();
                loadingSwal = null;
            }
        }

        async function loadTodos() {
            try {
                showLoading('กำลังโหลดข้อมูล...');

                let url = '/todos/ajax';
                if (currentTab === 'completed') {
                    url += '?completed=1';
                } else if (currentTab === 'my-todos') {
                    url += '?my_todos=1';
                }

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                currentTodos = data.todos;
                renderTodos(data.todos);
            } catch (error) {
                console.error('Error loading todos:', error);
                Swal.fire('ผิดพลาด', 'ไม่สามารถโหลดข้อมูล Todo ได้', 'error');
            } finally {
                hideLoading();
            }
        }

        function renderTodos(todos) {
            const container = document.getElementById('todo-container');
            container.innerHTML = '';

            if (todos.length === 0) {
                container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">ไม่พบรายการ Todo</p>
                </div>
            `;
                return;
            }

            todos.forEach(todo => {
                const todoElement = createTodoElement(todo);
                container.appendChild(todoElement);
            });
        }

        function createTodoElement(todo) {
            const isCompletedByCurrentUser = todo.completions &&
                todo.completions.some(completion => completion.user.id === {{ auth()->id() ?? 0 }});

            const isMine = todo.user.id === {{ auth()->id() ?? 0 }};

            if (currentTab === 'completed' && !isCompletedByCurrentUser) {
                return null;
            }

            // ถ้าเป็นแท็บ "รายการที่ยังไม่ได้ทำ" และ Todo นี้เสร็จแล้ว ให้ไม่แสดง
            if (currentTab === 'incomplete' && isCompletedByCurrentUser) {
                return null;
            }

            const todoElement = document.createElement('div');
            todoElement.className =
                `bg-white rounded-lg shadow-md p-6 mb-6 ${isCompletedByCurrentUser ? 'border-l-4 border-green-500' : ''}`;
            todoElement.dataset.id = todo.id;

            let todoHeader = `
         <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center">
                    <h3 class="text-lg font-semibold text-gray-800">${todo.title}</h3>
                    ${isCompletedByCurrentUser ? '<span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">เสร็จแล้ว</span>' : ''}
                </div>
                ${todo.description ? `<p class="text-gray-600 mt-2">${todo.description}</p>` : ''}
                <div class="flex items-center text-sm text-gray-500 mt-3">
                    <p class="mr-4">สร้างโดย: ${todo.user.username}</p>
                    <p><i class="far fa-calendar-alt mr-1"></i> ${new Date(todo.created_at).toLocaleString()}</p>
                    ${todo.completions && todo.completions.length > 0 ? `
                                        <button onclick="showCompletionDetails(${todo.id})" class="text-blue-500 text-sm hover:underline flex items-center ml-4">
                                            <i class="fas fa-users mr-1"></i> ผู้ที่เสร็จสิ้นรายการ (${todo.completions.length})
                                        </button>
                                    ` : ''}
                </div>
            </div>
            <div class="flex space-x-2">
           `;

            if (isMine) {
                todoHeader += `
            <button onclick="deleteTodo(${todo.id})" class="p-2 text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
            <button onclick="openEditModal(${todo.id}, '${escapeHtml(todo.title)}', '${escapeHtml(todo.description || '')}')" 
                class="p-2 text-blue-500 hover:text-blue-700">
                <i class="fas fa-edit"></i>
            </button>
            `;
            }

            if (!isCompletedByCurrentUser) {
                todoHeader += `
            <button onclick="completeTodo(${todo.id})" class="p-2 text-green-500 hover:text-green-700">
                <i class="fas fa-check"></i>
            </button>
            `;
            }

            todoHeader += `</div></div>`;

            let commentsHtml = `
                <div class="mt-6 border-t pt-4">
                    <h4 class="text-md font-medium text-gray-700 mb-3">ความคิดเห็น</h4>
                    <div id="comments-${todo.id}">
            `;

            if (todo.comments && todo.comments.length > 0) {
                todo.comments.forEach(comment => {
                    const isCommentMine = comment.user.id === {{ auth()->id() ?? 0 }};

                    commentsHtml += `
                        <div class="bg-gray-50 rounded p-4 mb-3" data-comment-id="${comment.id}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">${comment.user.username}</p>
                                    <p class="text-gray-600 mt-1">${comment.content || ''}</p>
            ${comment.image_path ? `
                                                                                                                                            <img src="${comment.image_path}" alt="Comment image" class="mt-2 rounded max-w-xs">
                                                                                                                                        ` : ''}
                                    <p class="text-xs text-gray-500 mt-1">
                                        ${new Date(comment.created_at).toLocaleString()}
                                    </p>
                                </div>
                                ${isCommentMine ? `
                                                                                                                                                                                                        <div class="flex space-x-2">
                                                                                                                                                                                                            <button onclick="openEditCommentModal(${comment.id}, '${escapeHtml(comment.content || '')}', '${comment.image_path || ''}')" 
                                                                                                                                                                                                                class="text-blue-500 hover:text-blue-700 text-sm">
                                                                                                                                                                                                                <i class="fas fa-edit"></i>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                            <button onclick="deleteComment(${comment.id})" 
                                                                                                                                                                                                                class="text-red-500 hover:text-red-700 text-sm">
                                                                                                                                                                                                                <i class="fas fa-trash"></i>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    ` : ''}
                            </div>
                        </div>
                    `;
                });
            }

            commentsHtml += `</div>`;

            commentsHtml += `
                <form onsubmit="addComment(event, ${todo.id})" class="mt-4" enctype="multipart/form-data">
          <div class="flex space-x-2">
            <input type="text" name="content" placeholder="เพิ่มความคิดเห็น..." 
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            
            <label class="cursor-pointer bg-gray-200 hover:bg-gray-300 rounded-md p-2 relative">
                <i class="fas fa-image text-gray-600"></i>
                <input type="file" name="image" id="comment-image-upload-${todo.id}" class="hidden" accept="image/*" 
                    onchange="previewImage(event, ${todo.id})">
            </label>
            
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                <i class="fas fa-paper-plane"></i>
            </button>
         </div>

            <div id="image-preview-container-${todo.id}" class="mt-2 hidden">
            <img id="image-preview-${todo.id}" src="#" alt="Preview" class="max-w-xs max-h-40 rounded-md">
            <button type="button" onclick="removePreview(${todo.id})" class="ml-2 text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i> ลบรูป
            </button>
            </div>
            </form>
            `;

            todoElement.innerHTML = todoHeader + commentsHtml;
            return todoElement;
        }

        function setupEventListeners() {
            document.getElementById('incomplete-tab').addEventListener('click', () => {
                currentTab = 'incomplete';
                setActiveTab('incomplete-tab');
                loadTodos();
            });

            document.getElementById('completed-tab').addEventListener('click', () => {
                currentTab = 'completed';
                setActiveTab('completed-tab');
                loadTodos();
            });

            document.getElementById('my-todos-tab').addEventListener('click', () => {
                currentTab = 'my-todos';
                setActiveTab('my-todos-tab');
                loadTodos();
            });

            document.getElementById('create-todo-form').addEventListener('submit', createTodo);

            document.getElementById('edit-todo-form').addEventListener('submit', updateTodo);

            document.getElementById('logout-form')?.addEventListener('submit', function(e) {
                e.preventDefault();
                this.submit();
            });
        }

        async function createTodo(e) {
            e.preventDefault();
            showLoading('กำลังสร้างรายการ...');

            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('/todos', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to create todo');
                }

                if (currentTab === 'incomplete' || currentTab === 'my-todos') {
                    const newTodoElement = createTodoElement(data.todo);
                    document.getElementById('todo-container').prepend(newTodoElement);
                }

                form.reset();

                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'สร้างรายการ Todo เรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                console.error('Error creating todo:', error);
                Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถสร้างรายการ Todo ได้', 'error');
            } finally {
                hideLoading();
            }
        }

        async function completeTodo(todoId) {
            try {
                showLoading('กำลังอัพเดตสถานะ...');

                const response = await fetch(`/todos/${todoId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to complete todo');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: data.message || 'ทำรายการ Todo เสร็จเรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });

                // โหลดข้อมูลใหม่ตามแท็บปัจจุบัน
                loadTodos();
            } catch (error) {
                console.error('Error completing todo:', error);
                Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถทำรายการ Todo ให้เสร็จได้', 'error');
            } finally {
                hideLoading();
            }
        }

        async function deleteTodo(todoId) {
            try {
                const result = await Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "คุณจะไม่สามารถย้อนกลับการกระทำนี้ได้!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                });

                if (!result.isConfirmed) return;

                showLoading('กำลังลบรายการ...');

                const response = await fetch(`/todos/${todoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to delete');
                }

                const todoElement = document.querySelector(`div[data-id="${todoId}"]`);
                if (todoElement) {
                    todoElement.remove();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'ลบแล้ว!',
                    text: 'รายการ Todo ของคุณถูกลบแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                console.error('Error deleting todo:', error);
                Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถลบรายการ Todo ได้', 'error');
            } finally {
                hideLoading();
            }
        }

        function openEditModal(id, title, description) {
            document.getElementById('edit-todo-id').value = id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description || '';
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        async function updateTodo(e) {
            e.preventDefault();
            showLoading('กำลังอัพเดตรายการ...');

            const form = e.target;
            const formData = new FormData(form);
            const todoId = document.getElementById('edit-todo-id').value;

            try {
                const response = await fetch(`/todos/${todoId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update todo');
                }

                const todoIndex = currentTodos.findIndex(todo => todo.id == todoId);
                if (todoIndex !== -1) {
                    currentTodos[todoIndex] = data.todo;
                }

                renderTodos(currentTodos);
                closeEditModal();

                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'อัพเดตรายการ Todo เรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                console.error('Error updating todo:', error);
                Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถอัพเดตรายการ Todo ได้', 'error');

                if (error.message.includes('not found')) {
                    loadTodos();
                }
            } finally {
                hideLoading();
            }
        }

        async function addComment(e, todoId) {
            e.preventDefault();
            showLoading('กำลังเพิ่มความคิดเห็น...');

            const form = e.target;
            const formData = new FormData(form);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch(`/todos/${todoId}/comments`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to add comment');
                }

                const previewContainer = document.getElementById(`image-preview-container-${todoId}`);
                previewContainer.classList.add('hidden');
                document.getElementById(`comment-image-upload-${todoId}`).value = '';

                const commentsContainer = document.getElementById(`comments-${todoId}`);
                const newComment = document.createElement('div');
                newComment.className = 'bg-gray-50 rounded p-4 mb-3';
                newComment.dataset.commentId = data.comment.id;
                newComment.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-700">${data.comment.user.username}</p>
                        <p class="text-gray-600 mt-1">${data.comment.content || ''}</p>
                        ${data.comment.image_path ? `
                                                                                        <img src="${data.comment.image_path}" alt="Comment image" class="mt-2 rounded max-w-xs">
                                                                                    ` : ''}
                        <p class="text-xs text-gray-500 mt-1">
                            ${new Date(data.comment.created_at).toLocaleString()}
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="openEditCommentModal(${data.comment.id}, '${escapeHtml(data.comment.content || '')}')" 
                            class="text-blue-500 hover:text-blue-700 text-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteComment(${data.comment.id})" 
                            class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

                commentsContainer.prepend(newComment);
                form.reset();

                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'เพิ่มความคิดเห็นเรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                console.error('Error adding comment:', error);
                Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถเพิ่มความคิดเห็นได้', 'error');
            } finally {
                hideLoading();
            }
        }

        async function deleteComment(commentId) {
            try {
                const result = await Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "คุณจะไม่สามารถย้อนกลับการกระทำนี้ได้!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                });

                if (!result.isConfirmed) return;

                showLoading('กำลังลบความคิดเห็น...');

                const response = await fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete comment');
                }

                document.querySelector(`[data-comment-id="${commentId}"]`).remove();

                Swal.fire({
                    icon: 'success',
                    title: 'ลบแล้ว!',
                    text: 'ความคิดเห็นของคุณถูกลบแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                console.error('Error deleting comment:', error);
                Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถลบความคิดเห็นได้', 'error');
            } finally {
                hideLoading();
            }
        }

        function openEditCommentModal(commentId, content, imagePath = null) {
            const imageUrl = imagePath ? `${imagePath}?${new Date().getTime()}` : null;

            const html = `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">เนื้อหาความคิดเห็น</label>
                <textarea id="edit-comment-content" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" rows="3">${content || ''}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รูปภาพ</label>
                ${imagePath ? `
                                        
                                    ` : ''}
                
                <label class="cursor-pointer bg-gray-200 hover:bg-gray-300 rounded-md p-2 inline-block">
                    <i class="fas fa-image text-gray-600 mr-2"></i>เปลี่ยนรูปภาพ
                    <input type="file" id="edit-comment-image" class="hidden" accept="image/*">
                </label>
                <div id="edit-image-preview-container" class="mt-2 hidden">
                    <img id="edit-image-preview" src="#" alt="Preview" class="max-w-xs max-h-40 rounded-md">
                    <button type="button" onclick="removeEditPreview()" class="ml-2 text-red-500 hover:text-red-700 text-sm">
                        <i class="fas fa-times"></i> ลบรูป
                    </button>
                </div>
            </div>
        </div>
    `;

            Swal.fire({
                title: 'แก้ไขความคิดเห็น',
                html: html,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const modal = Swal.getPopup();
                    const contentElement = modal.querySelector('#edit-comment-content');

                    if (!contentElement) {
                        Swal.showValidationMessage('ไม่พบฟิลด์เนื้อหาความคิดเห็น');
                        return false;
                    }

                    const formData = new FormData();
                    formData.append('content', contentElement.value);
                    formData.append('_method', 'PUT');

                    const removeImageCheckbox = modal.querySelector('#remove-image-checkbox');
                    if (imagePath && removeImageCheckbox?.checked) {
                        formData.append('remove_image', 'true');
                    }

                    const imageInput = modal.querySelector('#edit-comment-image');
                    if (imageInput?.files && imageInput.files[0]) {
                        formData.append('image', imageInput.files[0]);
                    }

                    return fetch(`/comments/${commentId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`ไม่สามารถอัพเดต: ${error.message}`);
                            return false;
                        });
                },
                allowOutsideClick: () => !Swal.isLoading(),
                didOpen: () => {

                    const modal = Swal.getPopup();
                    const imageInput = modal.querySelector('#edit-comment-image');
                    if (imageInput) {
                        imageInput.addEventListener('change', function(e) {
                            if (e.target.files && e.target.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function(event) {
                                    const previewImg = modal.querySelector('#edit-image-preview');
                                    const previewContainer = modal.querySelector(
                                        '#edit-image-preview-container');
                                    if (previewImg && previewContainer) {
                                        previewImg.src = event.target.result;
                                        previewContainer.classList.remove('hidden');
                                    }
                                };
                                reader.readAsDataURL(e.target.files[0]);
                            }
                        });
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                    if (commentElement) {
                        const contentElement = commentElement.querySelector('.text-gray-600');
                        if (contentElement) {
                            contentElement.textContent = result.value.comment.content;
                        }

                        const imgElement = commentElement.querySelector('img');
                        if (result.value.comment.image_path) {
                            if (imgElement) {
                                imgElement.src = result.value.comment.image_path;
                            } else {
                                const contentContainer = commentElement.querySelector('div > div:first-child');
                                if (contentContainer) {
                                    const newImg = document.createElement('img');
                                    newImg.src = result.value.comment.image_path;
                                    newImg.alt = "Comment image";
                                    newImg.className = "mt-2 rounded max-w-xs";
                                    contentContainer.appendChild(newImg);
                                }
                            }
                        } else if (imgElement) {
                            imgElement.remove();
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'อัพเดตความคิดเห็นเรียบร้อยแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function removeEditPreview() {
            const imageInput = document.getElementById('edit-comment-image');
            const previewImg = document.getElementById('edit-image-preview');
            const previewContainer = document.getElementById('edit-image-preview-container');

            if (imageInput) imageInput.value = '';
            if (previewImg) previewImg.src = '#';
            if (previewContainer) previewContainer.classList.add('hidden');
        }

        // ฟังก์ชันอัพเดตความคิดเห็น
        async function updateComment(commentId, content) {
            return await fetch(`/comments/${commentId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({
                    content
                })
            });
        }

        async function showCompletionDetails(todoId) {
            try {
                const response = await fetch(`/todos/${todoId}/completions`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                const contentContainer = document.getElementById('completion-details-content');
                contentContainer.innerHTML = '';

                if (data.completions && data.completions.length > 0) {
                    data.completions.forEach(completion => {
                        const completionItem = document.createElement('div');
                        completionItem.className = 'p-3 bg-gray-50 rounded';
                        completionItem.innerHTML = `
                            <div class="flex justify-between items-center">
                                <span class="font-medium">${completion.user.username}</span>
                                <span class="text-xs text-gray-500">${new Date(completion.completed_at).toLocaleString()}</span>
                            </div>
                        `;
                        contentContainer.appendChild(completionItem);
                    });
                } else {
                    contentContainer.innerHTML = '<p class="text-center text-gray-500">ไม่พบข้อมูลการเสร็จสิ้น</p>';
                }

                document.getElementById('completion-details-modal').classList.remove('hidden');
            } catch (error) {
                console.error('Error loading completion details:', error);
                Swal.fire('ผิดพลาด', 'ไม่สามารถโหลดข้อมูลการเสร็จสิ้นได้', 'error');
            }
        }

        function closeCompletionDetailsModal() {
            document.getElementById('completion-details-modal').classList.add('hidden');
        }

        function previewImage(event, todoId) {
            const input = event.target;
            const previewContainer = document.getElementById(`image-preview-container-${todoId}`);
            const previewImg = document.getElementById(`image-preview-${todoId}`);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePreview(todoId) {
            const input = document.getElementById(`comment-image-upload-${todoId}`);
            const previewContainer = document.getElementById(`image-preview-container-${todoId}`);
            const previewImg = document.getElementById(`image-preview-${todoId}`);

            input.value = '';
            previewImg.src = '#';
            previewContainer.classList.add('hidden');
        }

        function setActiveTab(tabId) {
            document.querySelectorAll('[id$="-tab"]').forEach(tab => {
                tab.classList.remove('border-b-2', 'border-blue-500');
            });
            document.getElementById(tabId).classList.add('border-b-2', 'border-blue-500');
        }

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>
