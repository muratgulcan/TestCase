<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all();
    }

    public function store(Request $request)
    {
        try {
            if (Auth::user()->role_id == 1) {
                $validator = Validator::make($request->all(), [
                    'time' => 'required',
                    'title' => 'required',
                    'subject' => 'required',
                    'user_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 400);
                }

                $user = User::findorFail($request->input('user_id'));

                $task = Task::create([
                    'time' => $request->input('time'),
                    'title' => $request->input('title'),
                    'subject' => $request->input('subject'),
                    'user_id' => $request->input('user_id'),
                    'status' => 'Start verildi'
                ]);

                $toEmail = $user->email;
                $subject = 'Verilen görev hk.';
                $content = 'Oluşturulan görev mail olarak iletilmiştir.';

                Mail::raw($content, function ($message) use ($toEmail, $subject) {
                    $message->to($toEmail)
                        ->subject($subject);
                });

                return response()->json(['task' => $task], 201);
            } else {
                return response()->json(['message' => 'Yetkiniz yok'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'SERVER ERROR'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            if (Auth::user()->role_id == 1) {

                if (!$task) {
                    return response()->json(['message' => 'Görev bulunamadı'], 404);
                }

                $task->time = $request->input('time') ?? $task->time;
                $task->title = $request->input('title') ?? $task->title;
                $task->subject = $request->input('subject') ?? $task->subject;
                $task->user_id = $request->input('user_id') ?? $task->user_id;
                $task->save();

                return response()->json(['task' => $task], 200);
            } else {
                $task->status = $request->input('status') ?? $task->status;
                $task->save();
                return response()->json(['task' => $task], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'SERVER ERROR'
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            if (Auth::user()->role_id == 1) {
                $task = Task::findOrFail($id);
                $task->delete();
                return response()->json(['message' => 'Başarıyla silindi'], 200);
            } else {
                return response()->json(['message' => 'Yetkiniz yok'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'SERVER ERROR'
            ], 500);
        }
    }
}
