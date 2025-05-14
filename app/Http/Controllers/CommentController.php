<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function store(Request $request, Todo $todo)
    {
        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $filename = Str::random(40) . '.' . $request->file('image')->getClientOriginalExtension();
                $s3Path = 'todo/' . $filename;
                $uploaded = Storage::disk('s3')->putFileAs('todo', $request->file('image'), $filename);

                if (!$uploaded) {
                    throw new \Exception('Failed to upload file to S3');
                }

                $imagePath = $s3Path; 
            }

            $todo->comments()->create([
                'content' => $request->content,
                'image_path' => $imagePath,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Comment added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error  while adding comment');
        }
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorizeOwner($comment);

        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        try {
            $data = ['content' => $request->content];

            if ($request->boolean('remove_image') && $comment->image_path) {
                Storage::disk('s3')->delete($comment->image_path);
                $data['image_path'] = null;
            }

            if ($request->hasFile('image')) {
                if ($comment->image_path) {
                    Storage::disk('s3')->delete($comment->image_path);
                }

                $filename = Str::random(40) . '.' . $request->file('image')->getClientOriginalExtension();
                $s3Path = 'todo/' . $filename;
                $uploaded = Storage::disk('s3')->putFileAs('todo', $request->file('image'), $filename);

                if (!$uploaded) {
                    throw new \Exception('Failed to upload file to S3');
                }

                $data['image_path'] = $s3Path; 
            }

            $comment->update($data);

            return back()->with('success', 'Comment updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred while updating comment');
        }
    }

    public function destroy(Comment $comment)
    {
        $this->authorizeOwner($comment);

        try {
            if ($comment->image_path) {
                Storage::disk('s3')->delete($comment->image_path);
            }

            $comment->delete();

            return back()->with('success', 'Comment deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred while deleting comment');
        }
    }

    protected function authorizeOwner(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
