<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    @vite('resources/css/app.css')
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
                        <form action="{{ route('logout') }}" method="POST">
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
            <form action="{{ route('todos.store') }}" method="POST" class="space-y-4">
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

        <div id="incomplete-todos" class="todo-section">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-tasks text-blue-500 mr-3"></i> รายการที่ยังไม่ได้ทำ
            </h2>
            @foreach ($incompleteTodos as $todo)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $todo->title }}</h3>
                            @if ($todo->description)
                                <p class="text-gray-600 mt-2">{{ $todo->description }}</p>
                            @endif
                            <div class="flex items-center text-sm text-gray-500 mt-3">
                                <p class="mr-4">สร้างโดย: {{ $todo->user->username }}</p>
                                <p><i class="far fa-calendar-alt mr-1"></i> {{ $todo->created_at->format('d/m/Y H:i') }}
                                </p>
                                @if ($todo->completions->isNotEmpty())
                                    <button onclick="showCompletionDetails('{{ $todo->id }}')"
                                        class="text-blue-500 text-sm hover:underline flex items-center ml-4">
                                        <i class="fas fa-users mr-1"></i> ผู้ที่เสร็จสิ้นรายการ
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if (auth()->id() === $todo->user_id)
                                <form action="{{ route('todos.destroy', $todo) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <button
                                    onclick="openEditModal('{{ $todo->id }}', '{{ $todo->title }}', '{{ $todo->description }}')"
                                    class="p-2 text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endif
                            <form action="{{ route('todos.complete', $todo) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 text-green-500 hover:text-green-700">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-md font-medium text-gray-700 mb-3">ความคิดเห็น</h4>

                        @foreach ($todo->comments as $comment)
                            <div class="bg-gray-50 rounded p-4 mb-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $comment->user->username }}</p>
                                        <p class="text-gray-600 mt-1">{{ $comment->content }}</p>
                                        @if ($comment->image_path)
                                            <img src="{{ Storage::disk('s3')->url($comment->image_path) }}"
                                                alt="Comment image" class="mt-2 rounded max-w-xs">
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @if (auth()->id() === $comment->user_id)
                                        <div class="flex space-x-2">
                                            <button
                                                onclick="openEditCommentModal('{{ $comment->id }}', '{{ $comment->content }}')"
                                                class="text-blue-500 hover:text-blue-700 text-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <form action="{{ route('comments.store', $todo) }}" method="POST" class="mt-4"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex space-x-2">
                                <input type="text" name="content" placeholder="เพิ่มความคิดเห็น..."
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                <label class="cursor-pointer bg-gray-200 hover:bg-gray-300 rounded-md p-2">
                                    <i class="fas fa-image text-gray-600"></i>
                                    <input type="file" name="image" class="hidden comment-image-input"
                                        accept="image/*">
                                </label>
                                <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="image-preview mt-2" style="display: none;">
                                <img src="" alt="Preview" class="max-h-32 rounded">
                                <button type="button" class="remove-preview text-red-500 ml-2">
                                    <i class="fas fa-times"></i> ลบรูปภาพ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="completed-todos" class="todo-section hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i> รายการที่ทำแล้ว
            </h2>
            @foreach ($completedTodos as $todo)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $todo->title }}</h3>
                                <span
                                    class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">เสร็จแล้ว</span>
                            </div>
                            @if ($todo->description)
                                <p class="text-gray-600 mt-2">{{ $todo->description }}</p>
                            @endif
                            <div class="flex items-center text-sm text-gray-500 mt-3">
                                <p class="mr-4">สร้างโดย: {{ $todo->user->username }}</p>
                                <p class="mr-4"><i class="far fa-calendar-alt mr-1"></i> สร้างเมื่อ:
                                    {{ $todo->created_at->format('d/m/Y H:i') }}</p>
                                <button onclick="showCompletionDetails('{{ $todo->id }}')"
                                    class="text-blue-500 text-sm hover:underline flex items-center">
                                    <i class="fas fa-users mr-1"></i> ผู้ที่เสร็จสิ้นรายการ
                                </button>
                            </div>
                        </div>
                        @if (auth()->id() === $todo->user_id)
                            <div class="flex space-x-2">
                                <form action="{{ route('todos.destroy', $todo) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-md font-medium text-gray-700 mb-3">ความคิดเห็น</h4>

                        @foreach ($todo->comments as $comment)
                            <div class="bg-gray-50 rounded p-4 mb-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $comment->user->username }}
                                        </p>
                                        <p class="text-gray-600 mt-1">{{ $comment->content }}</p>
                                        @if ($comment->image_path)
                                            <img src="{{ Storage::disk('s3')->url($comment->image_path) }}"
                                                alt="Comment image" class="mt-2 rounded max-w-xs">
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @if (auth()->id() === $comment->user_id)
                                        <div class="flex space-x-2">
                                            <button
                                                onclick="openEditCommentModal('{{ $comment->id }}', '{{ $comment->content }}')"
                                                class="text-blue-500 hover:text-blue-700 text-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <form action="{{ route('comments.store', $todo) }}" method="POST" class="mt-4"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex space-x-2">
                                <input type="text" name="content" placeholder="เพิ่มความคิดเห็น..."
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                <label class="cursor-pointer bg-gray-200 hover:bg-gray-300 rounded-md p-2">
                                    <i class="fas fa-image text-gray-600"></i>
                                    <input type="file" name="image" class="hidden comment-image-input"
                                        accept="image/*">
                                </label>
                                <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="image-preview mt-2" style="display: none;">
                                <img src="" alt="Preview" class="max-h-32 rounded">
                                <button type="button" class="remove-preview text-red-500 ml-2">
                                    <i class="fas fa-times"></i> ลบรูปภาพ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="my-todos" class="todo-section hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-user text-purple-500 mr-3"></i> รายการที่คุณสร้าง
            </h2>
            @foreach ($myTodos as $todo)
                <div
                    class="bg-white rounded-lg shadow-md p-6 mb-6 @if ($todo->completions->isNotEmpty()) border-l-4 border-green-500 @endif">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ $todo->title }}
                                </h3>
                                @if ($todo->completions->isNotEmpty())
                                    <span
                                        class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">เสร็จแล้ว</span>
                                @endif
                            </div>
                            @if ($todo->description)
                                <p class="text-gray-600 mt-2">
                                    {{ $todo->description }}
                                </p>
                            @endif
                            <div class="flex items-center text-sm text-gray-500 mt-3">
                                <p class="mr-4"><i class="far fa-calendar-alt mr-1"></i> สร้างเมื่อ:
                                    {{ $todo->created_at->format('d/m/Y H:i') }}</p>
                                @if ($todo->completions->isNotEmpty())
                                    <button onclick="showCompletionDetails('{{ $todo->id }}')"
                                        class="text-blue-500 text-sm hover:underline flex items-center">
                                        <i class="fas fa-users mr-1"></i> ผู้ที่เสร็จสิ้นรายการ
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <form action="{{ route('todos.destroy', $todo) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <button
                                onclick="openEditModal('{{ $todo->id }}', '{{ $todo->title }}', '{{ $todo->description }}')"
                                class="p-2 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if ($todo->completions->isEmpty())
                                <form action="{{ route('todos.complete', $todo) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-2 text-green-500 hover:text-green-700">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-md font-medium text-gray-700 mb-3">ความคิดเห็น</h4>

                        @foreach ($todo->comments as $comment)
                            <div class="bg-gray-50 rounded p-4 mb-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $comment->user->username }}
                                        </p>
                                        <p class="text-gray-600 mt-1">{{ $comment->content }}</p>
                                        @if ($comment->image_path)
                                            <img src="{{ Storage::disk('s3')->url($comment->image_path) }}"
                                                alt="Comment image" class="mt-2 rounded max-w-xs">
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @if (auth()->id() === $comment->user_id)
                                        <div class="flex space-x-2">
                                            <button
                                                onclick="openEditCommentModal('{{ $comment->id }}', '{{ $comment->content }}')"
                                                class="text-blue-500 hover:text-blue-700 text-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <form action="{{ route('comments.store', $todo) }}" method="POST" class="mt-4"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex space-x-2">
                                <input type="text" name="content" placeholder="เพิ่มความคิดเห็น..."
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                <label class="cursor-pointer bg-gray-200 hover:bg-gray-300 rounded-md p-2">
                                    <i class="fas fa-image text-gray-600"></i>
                                    <input type="file" name="image" class="hidden comment-image-input"
                                        accept="image/*">
                                </label>
                                <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="image-preview mt-2" style="display: none;">
                                <img src="" alt="Preview" class="max-h-32 rounded">
                                <button type="button" class="remove-preview text-red-500 ml-2">
                                    <i class="fas fa-times"></i> ลบรูปภาพ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">แก้ไขรายการ</h3>
                <form id="edit-form" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit-title" class="block text-sm font-medium text-gray-700">หัวข้อ</label>
                        <input type="text" id="edit-title" name="title" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    <div>
                        <label for="edit-description"
                            class="block text-sm font-medium text-gray-700">รายละเอียด</label>
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

    <div id="edit-comment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">แก้ไขความคิดเห็น</h3>
                <form id="edit-comment-form" method="POST" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit-comment-content"
                            class="block text-sm font-medium text-gray-700">เนื้อหา</label>
                        <textarea id="edit-comment-content" name="content" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">รูปภาพ</label>
                        <div class="mt-1 flex items-center">
                            <label class="cursor-pointer bg-gray-200 hover:bg-gray-300 rounded-md p-2">
                                <i class="fas fa-image text-gray-600 mr-2"></i> เปลี่ยนรูปภาพ
                                <input type="file" name="image" class="hidden edit-image-input"
                                    accept="image/*">
                            </label>
                        </div>
                        <div class="edit-image-preview mt-2" style="display: none;">
                            <img src="" alt="Preview" class="max-h-32 rounded">
                            <button type="button" class="remove-edit-preview text-red-500 ml-2">
                                <i class="fas fa-times"></i> ลบรูปภาพ
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" onclick="closeEditCommentModal()"
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
        document.getElementById('incomplete-tab').addEventListener('click', function() {
            showTab('incomplete-todos');
            setActiveTab('incomplete-tab');
        });

        document.getElementById('completed-tab').addEventListener('click', function() {
            showTab('completed-todos');
            setActiveTab('completed-tab');
        });

        document.getElementById('my-todos-tab').addEventListener('click', function() {
            showTab('my-todos');
            setActiveTab('my-todos-tab');
        });

        function showTab(tabId) {
            document.querySelectorAll('.todo-section').forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(tabId).classList.remove('hidden');
        }

        function setActiveTab(tabId) {
            document.querySelectorAll('[id$="-tab"]').forEach(tab => {
                tab.classList.remove('border-b-2', 'border-blue-500');
            });
            document.getElementById(tabId).classList.add('border-b-2', 'border-blue-500');
        }

        function openEditModal(id, title, description) {
            const form = document.getElementById('edit-form');
            form.action = `/todos/${id}`;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description || '';
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        function openEditCommentModal(id, content) {
            const form = document.getElementById('edit-comment-form');
            form.action = `/comments/${id}`;
            document.getElementById('edit-comment-content').value = content || '';
            document.getElementById('edit-comment-modal').classList.remove('hidden');

            const previewContainer = document.querySelector('.edit-image-preview');
            previewContainer.style.display = 'none';
            previewContainer.querySelector('img').src = '';
        }

        function closeEditCommentModal() {
            document.getElementById('edit-comment-modal').classList.add('hidden');
        }

        function showCompletionDetails(todoId) {
            Swal.fire({
                title: 'กำลังโหลด...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/todos/${todoId}/completions`)
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    const contentContainer = document.getElementById('completion-details-content');
                    contentContainer.innerHTML = '';

                    if (data.completions && data.completions.length > 0) {
                        data.completions.forEach(completion => {
                            const completionItem = document.createElement('div');
                            completionItem.className = 'p-3 bg-gray-50 rounded';
                            completionItem.innerHTML = `
                            <div class="flex justify-between items-center">
                                <span class="font-medium">${completion.user.username}</span>
                                <span class="text-xs text-gray-500">${completion.formatted_completed_at}</span>
                            </div>
                        `;
                            contentContainer.appendChild(completionItem);
                        });
                    } else {
                        contentContainer.innerHTML = '<p class="text-center text-gray-500">ไม่พบข้อมูลการเสร็จสิ้น</p>';
                    }

                    document.getElementById('completion-details-modal').classList.remove('hidden');
                })
                .catch(error => {
                    Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
                    console.error(error);
                });
        }

        function closeCompletionDetailsModal() {
            document.getElementById('completion-details-modal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            setActiveTab('incomplete-tab');

            document.querySelectorAll('.comment-image-input').forEach(input => {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const previewContainer = this.closest('form').querySelector('.image-preview');
                    const previewImg = previewContainer.querySelector('img');

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewContainer.style.display = 'flex';
                    }
                    reader.readAsDataURL(file);
                });
            });

            document.querySelectorAll('.remove-preview').forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    form.querySelector('input[type="file"]').value = '';
                    form.querySelector('.image-preview').style.display = 'none';
                });
            });

            document.querySelector('.edit-image-input').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const previewContainer = document.querySelector('.edit-image-preview');
                const previewImg = previewContainer.querySelector('img');

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'flex';
                }
                reader.readAsDataURL(file);
            });

            document.querySelector('.remove-edit-preview').addEventListener('click', function() {
                document.querySelector('.edit-image-input').value = '';
                document.querySelector('.edit-image-preview').style.display = 'none';
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    Swal.fire({
                        title: 'กำลังดำเนินการ...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>
