<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\Client\SessionsClient;
use Google\Cloud\Dialogflow\V2\DetectIntentRequest;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service class for interacting with Google Dialogflow.
 * Handles intent detection and response parsing.
 */
class DialogflowService
{
    /**
     * @var string|null The Google Cloud project ID.
     */
    protected $projectId;

    /**
     * DialogflowService constructor.
     * Sets up credentials and project configuration.
     */
    public function __construct()
    {
        // Use config() for flexibility and caching support
        $this->projectId = config('services.dialogflow.project_id');

        // Set the path to the Service Account credentials
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/google-credentials.json'));
    }

    /**
     * Detects the intent of the user's message using Dialogflow V2.
     *
     * @param string $text The message text from the user.
     * @param string $sessionId A unique identifier for the conversation session.
     * @return array Contains fulfillmentText and intent name.
     * @throws Exception
     */
    public function detectIntent($text, $sessionId)
    {
        $sessionsClient = new SessionsClient();
        
        try {
            $session = $sessionsClient->sessionName($this->projectId, $sessionId);

            // 1. Configure the Text Input
            $textInput = (new TextInput())
                ->setText($text ?? '')
                ->setLanguageCode('en-US');

            // 2. Wrap into Query Input
            $queryInput = (new QueryInput())
                ->setText($textInput);

            // 3. Prepare the request object
            $request = (new DetectIntentRequest())
                ->setSession($session)
                ->setQueryInput($queryInput);

            // 4. Call the Dialogflow API
            $response = $sessionsClient->detectIntent($request);
            $queryResult = $response->getQueryResult();

            return [
                'fulfillmentText' => $queryResult->getFulfillmentText(),
                'intent' => $queryResult->getIntent() ? $queryResult->getIntent()->getDisplayName() : 'Default Fallback',
            ];

        } catch (Exception $e) {
            // Log the error for internal tracking
            Log::error('Dialogflow API Error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'text' => $text,
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to be handled by the controller
            throw $e;
        } finally {
            // Always close the connection to avoid resource leaks
            $sessionsClient->close();
        }
    }
}