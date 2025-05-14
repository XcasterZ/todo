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

        $request->user()->todos()->create($request->only('title', 'description'));

        return redirect()->route('home');
    }

    public function update(Request $request, Todo $todo)
    {
        $this->authorizeOwner($todo);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todo->update($request->only('title', 'description'));

        return redirect()->route('home');
    }

    public function destroy(Todo $todo)
    {
        $this->authorizeOwner($todo);

        $todo->comments()->delete();
        $todo->completions()->delete();
        
        $todo->delete();
        return redirect()->route('home');
    }

    public function complete(Todo $todo)
    {
        $exists = $todo->completions()->where('user_id', auth()->id())->exists();
        
        if (!$exists) {
            $todo->completions()->create([
                'user_id' => auth()->id(),
                'completed_at' => now(),
            ]);
        }

        return redirect()->route('home');
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
}