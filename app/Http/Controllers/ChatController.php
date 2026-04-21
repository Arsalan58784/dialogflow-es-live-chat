<?php

namespace App\Http\Controllers;

use App\Events\MessageProcessed;
use App\Services\DialogflowService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Controller to handle incoming chat messages.
 * Integrates with Dialogflow and handles WebSocket broadcasting.
 */
class ChatController extends Controller
{
    /**
     * @var DialogflowService
     */
    protected $dialogflowService;

    /**
     * ChatController constructor.
     *
     * @param DialogflowService $dialogflowService
     */
    public function __construct(DialogflowService $dialogflowService)
    {
        $this->dialogflowService = $dialogflowService;
    }

    /**
     * Handle the incoming chat request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'sometimes|string'
        ]);

        $message = $request->input('message');
        $sessionId = $request->input('session_id', 'default-session');

        try {
            // 2. Send the message to Dialogflow
            $botResponse = $this->dialogflowService->detectIntent($message, $sessionId);

            // 3. Broadcast the bot response over WebSockets (Reverb)
            // toOthers() ensures the sender doesn't receive their own broadcast if they have a socket ID
            broadcast(new MessageProcessed($botResponse['fulfillmentText']))->toOthers();

            // 4. Return the response to the sender via AJAX
            return response()->json([
                'status' => 'success',
                'reply' => $botResponse['fulfillmentText'],
                'intent' => $botResponse['intent']
            ]);

        } catch (Exception $e) {
            // Log the failure for debugging
            Log::error('Chat Processing Failure: ' . $e->getMessage());

            // Return a clean error response to the frontend
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, we are having trouble connecting to the AI service. Please try again later.'
            ], 500);
        }
    }
}