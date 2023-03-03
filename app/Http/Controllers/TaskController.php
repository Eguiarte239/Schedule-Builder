<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user_id', Auth::user()->id)->get();
        return new ResourceCollection(TaskResource::collection($tasks));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            "title" => 'required|string|max:255',
            "start_time" => 'required|date|after_or_equal:today',
            "end_time" => 'required|date|after_or_equal:start_time',
            "hour_estimate" => 'required|integer|between:0,100.99',
            "content" => 'required|string|max:500',
            "image.*" => 'nullable|mimes:jpeg,png,gif|max:2048',
            'priority' => 'required|in:Low,Medium,High,Urgent',
        ]);

        $task = new Task($data);
        $task->user_id = $user->id;
        $task->save();

        return response($task, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $credentials = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);
        $task = Task::where('title', $credentials['title'])
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $title)
    {
        $credentials = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);
        $user = $request->user();
        $task = Task::where('title', $credentials['title'])->where('user_id', $user->id)->firstOrFail();
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $data = $request->validate([
            "title" => 'required|string|max:255',
            "start_time" => 'required|date|after_or_equal:today',
            "end_time" => 'required|date|after_or_equal:start_time',
            "hour_estimate" => 'required|integer|between:0,100.99',
            "content" => 'required|string|max:500',
            "image.*" => 'nullable|mimes:jpeg,png,gif|max:2048',
            'priority' => 'required|in:Low,Medium,High,Urgent',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/images');
            $data['image'] = $image;
        }

        $task->update($data);

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $title)
    {
        $credentials = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);
        $user = $request->user();
        $task = Task::where('title', $credentials['title'])->where('user_id', $user->id)->firstOrFail();

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
