<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'category_id' => 'required|integer',
            'rating' => 'required|integer|between:1,5',
            'message' => 'nullable|string',
        ]);

        $feedback = Feedback::create($validated);

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'data' => $feedback
        ], 201);
    }

    public function index()
    {
        $feedback = Feedback::all();
        return response()->json(['data' => $feedback]);
    }

    public function show($id)
    {
        $feedback = Feedback::findOrFail($id);
        return response()->json(['data' => $feedback]);
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();
        return response()->json(['message' => 'Feedback deleted successfully']);
    }

    public function archive($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update(['archived' => true]);
        return response()->json(['data' => $feedback]);
    }

    public function updateStatus($id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:New,In Progress,Resolved,Closed',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update(['status' => $validated['status']]);
        return response()->json(['data' => $feedback]);
    }

    public function addResponse($id, Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'internal_notes' => 'nullable|string',
            'type' => 'nullable|string|in:Public,Internal',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'response' => $validated['message'],
            'internal_notes' => $validated['internal_notes'] ?? null,
            'status' => 'Resolved'
        ]);

        return response()->json(['data' => $feedback]);
    }

    public function updateResponse($id, Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'internal_notes' => 'nullable|string',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'response' => $validated['message'],
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        return response()->json(['data' => $feedback]);
    }
}
