<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoCompletion;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::with(['user', 'comments.user', 'completions.user'])
            ->latest()
            ->get();
            
        $incompleteTodos = $todos->filter(function($todo) {
            return !$todo->completions->contains('user_id', auth()->id());
        });
        
        $completedTodos = $todos->filter(function($todo) {
            return $todo->completions->contains('user_id', auth()->id());
        });
        
        $myTodos = $todos->where('user_id', auth()->id());
        
        return view('index', compact('incompleteTodos', 'completedTodos', 'myTodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todo = $request->user()->todos()->create($request->only('title', 'description'));

        return response()->json([
            'success' => true,
            'message' => 'Todo created successfully',
            'todo' => $todo->load('user') 
        ]);
    }

    public function update(Request $request, Todo $todo)
    {
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Todo not found'
            ], 404);
        }

        $this->authorizeOwner($todo);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todo->update($request->only('title', 'description'));

        return response()->json([
            'success' => true,
            'message' => 'Todo updated successfully',
            'todo' => $todo->load('user', 'comments.user', 'completions.user') 
        ]);
    }

    public function destroy(Todo $todo)
    {
        $this->authorizeOwner($todo);

        $todo->comments()->delete();
        $todo->completions()->delete();
        
        $todo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully'
        ]);
    }

    public function complete(Todo $todo)
    {
        $exists = $todo->completions()->where('user_id', auth()->id())->exists();
        
        if (!$exists) {
            $completion = $todo->completions()->create([
                'user_id' => auth()->id(),
                'completed_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Todo completed successfully',
                'completion' => $completion
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Todo already completed by this user'
        ], 400);
    }


    public function getCompletions(Todo $todo)
    {
        $completions = $todo->completions()
            ->with('user')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($completion) {
                $completion->formatted_completed_at = \Carbon\Carbon::parse($completion->completed_at)
                    ->setTimezone('Asia/Bangkok')
                    ->format('d/m/Y H:i');
                return $completion;
            });

        return response()->json([
            'completions' => $completions
        ]);
    }


    protected function authorizeOwner(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
    
    public function getTodosAjax(Request $request)
    {
        $query = Todo::with(['user', 'comments.user', 'completions.user'])
                    ->latest();

        if ($request->has('completed')) {
            $query->whereHas('completions', function($q) {
                $q->where('user_id', auth()->id()); 
            });
        }

        if ($request->has('my_todos')) {
            $query->where('user_id', auth()->id());
        }

        $todos = $query->get();

        return response()->json([
            'todos' => $todos
        ]);
    }
}