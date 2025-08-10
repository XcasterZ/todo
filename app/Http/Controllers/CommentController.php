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
        \Log::info('Store comment request received', [
            'todo_id' => $todo->id,
            'user_id' => auth()->id(),
            'has_image' => $request->hasFile('image'),
            'content_length' => strlen($request->content ?? '')
        ]);

        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $filename = Str::random(40) . '.' . $request->file('image')->getClientOriginalExtension();
                $s3Path = 'todo/' . $filename;
                \Log::info('Uploading image to S3', ['path' => $s3Path]);

                $uploaded = Storage::disk('s3')->putFileAs('todo', $request->file('image'), $filename);

                if (!$uploaded) {
                    \Log::error('Failed to upload file to S3', ['path' => $s3Path]);
                    throw new \Exception('Failed to upload file to S3');
                }

                $imagePath = Storage::disk('s3')->url($s3Path); // เปลี่ยนเป็น URL เต็มรูปแบบ
            }

            $comment = $todo->comments()->create([
                'content' => $request->content,
                'image_path' => $imagePath,
                'user_id' => auth()->id(),
            ]);

            $comment->load('user');

            \Log::info('Comment created successfully');
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully!',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            \Log::error('Error while adding comment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error while adding comment'
            ], 500);
        }
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorizeOwner($comment);
        \Log::info('Update comment request', [
            'comment_id' => $comment->id,
            'user_id' => auth()->id(),
            'remove_image' => $request->boolean('remove_image'),
            'has_image' => $request->hasFile('image')
        ]);

        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image',
            'remove_image' => 'nullable|boolean',
        ]);

        try {
            $data = ['content' => $request->content];

            if ($request->boolean('remove_image') && $comment->image_path) {
                \Log::info('Removing image from S3', ['path' => $comment->image_path]);
                // ลบเฉพาะส่วน path ไม่รวม domain
                $pathToDelete = str_replace(Storage::disk('s3')->url(''), '', $comment->image_path);
                Storage::disk('s3')->delete($pathToDelete);
                $data['image_path'] = null;
            }

            if ($request->hasFile('image')) {
                if ($comment->image_path) {
                    \Log::info('Replacing existing image', ['old_path' => $comment->image_path]);
                    $pathToDelete = str_replace(Storage::disk('s3')->url(''), '', $comment->image_path);
                    Storage::disk('s3')->delete($pathToDelete);
                }

                $filename = Str::random(40) . '.' . $request->file('image')->getClientOriginalExtension();
                $s3Path = 'todo/' . $filename;
                \Log::info('Uploading new image to S3', ['path' => $s3Path]);

                $uploaded = Storage::disk('s3')->putFileAs('todo', $request->file('image'), $filename);

                if (!$uploaded) {
                    \Log::error('Failed to upload file to S3', ['path' => $s3Path]);
                    throw new \Exception('Failed to upload file to S3');
                }

                $data['image_path'] = Storage::disk('s3')->url($s3Path); // เปลี่ยนเป็น URL เต็มรูปแบบ
            }

            $comment->update($data);
            $comment->load('user');

            \Log::info('Comment updated successfully');
            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully!',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            \Log::error('Error occurred while updating comment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating comment'
            ], 500);
        }
    }

    public function destroy(Comment $comment)
    {
        $this->authorizeOwner($comment);
        \Log::info('Delete comment request', [
            'comment_id' => $comment->id,
            'user_id' => auth()->id(),
            'has_image' => !empty($comment->image_path)
        ]);

        try {
            if ($comment->image_path) {
                \Log::info('Deleting image from S3', ['path' => $comment->image_path]);
                $pathToDelete = str_replace(Storage::disk('s3')->url(''), '', $comment->image_path);
                Storage::disk('s3')->delete($pathToDelete);
            }

            $commentId = $comment->id;
            $comment->delete();

            \Log::info('Comment deleted successfully');
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!',
                'comment_id' => $commentId
            ]);
        } catch (\Exception $e) {
            \Log::error('Error occurred while deleting comment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while deleting comment'
            ], 500);
        }
    }

    protected function authorizeOwner(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            \Log::warning('Unauthorized comment access attempt', [
                'comment_id' => $comment->id,
                'user_id' => auth()->id()
            ]);
            abort(403, 'Unauthorized action.');
        }
    }
}