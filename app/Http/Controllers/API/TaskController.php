<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Task;

use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all();
    }

    public function indexByUser($user_id)
    {
        $tasks = Task::where('user_id', $user_id)->get();

        return response()->json($tasks);
    }


    public function store(Request $request)
    {
        return Task::create([
            'title' => $request->title,
            'user_id' => $request->user_id,
            'description' => $request->description
        ]);
    }

    public function show($id)
    {
        return Task::find($id);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        $task->title = $request->input("title");
        $task->description = $request->input("description");

        $task->status = $request->input("status");

        $task->save();

        return Task::find($id);
    }

    public function destroy($id)
    {
        return Task::find($id)->delete();
    }
}
