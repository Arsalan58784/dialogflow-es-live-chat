# InteractCX - AI Chatbot Assistant

InteractCX is a modern, real-time AI chatbot application built with Laravel 13, Google Dialogflow, and Laravel Reverb (WebSockets). It features a premium, responsive UI and persistent chat history.

## Features
- **AI-Powered**: Integrates with Google Dialogflow for natural language understanding.
- **Real-Time**: Uses Laravel Reverb for instant WebSocket-based message broadcasting.
- **Modern UI**: A clean interface built with Bootstrap 5 and custom CSS.
- **Persistence**: Chat history is saved in the browser's LocalStorage.
- **Robustness**: Includes comprehensive error handling for both API and WebSocket layers.

## Tech Stack
- **Backend**: Laravel 13 (PHP 8.2+)
- **Broadcasting**: Laravel Reverb
- **AI Service**: Google Cloud Dialogflow
- **Frontend**: Blade, JavaScript (Axios + Laravel Echo)
- **Styling**: Bootstrap 5, Google Fonts (Outfit)

---

## Prerequisites
Ensure you have the following installed:
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- A Google Cloud Project with Dialogflow API enabled

---

## Setup Instructions

### 1. Clone the Repository
```bash
git clone <repository-url>
cd chatbot
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
Copy the `.env.example` to `.env` and configure your settings:
```bash
cp .env.example .env
php artisan key:generate
```
**Required Variables:**
- `BROADCAST_CONNECTION=reverb`
- `DIALOGFLOW_PROJECT_ID=your-project-id`

### 4. Google Cloud Credentials
1. Create a Service Account in your Google Cloud Console.
2. Download the JSON key file.
3. Rename it to `google-credentials.json` and place it in `storage/app/`.

### 5. Start the Application

**Term 1: Start Laravel Reverb (WebSockets)**
```bash
php artisan reverb:start
```

**Term 2: Start the Web Server**
```bash
php artisan serve
```

**Term 3: Compile Assets (Vite)**
```bash
npm run dev
# OR for production
npm run build
```

---

## Usage
1. Open your browser and navigate to `http://localhost:8000`.
2. Type a message in the chat input and press enter.
3. The message is sent to the Laravel backend, processed via Dialogflow, and the response is broadcasted back to the UI in real-time.
4. Use the **Trash** icon in the header to clear your chat history.

## Architecture & Logic
- **`App\Services\DialogflowService`**: Handles the communication with Google Cloud.
- **`App\Http\Controllers\ChatController`**: Validates input, interacts with the service, and triggers broadcasting.
- **`App\Events\MessageProcessed`**: The event broadcasted over the `chat-channel`.
Broadcasting: Uses Laravel Echo on the frontend to listen for the MessageProcessed event on the chat-channel.
- **`welcome.blade.php`**: Contains the single-page interface and the JavaScript logic for real-time updates and persistence.

## Troubleshooting
- **SSL Certificate Issue (Local)**: If you encounter cURL error 60, ensure your `php.ini` points to a valid `cacert.pem` file.
- **Dialogflow API**: Ensure the Dialogflow API is enabled in the Google Cloud Console and the Service Account has the "Dialogflow API Client" role.
- **WebSocket Connection**: If messages don't appear in the UI, ensure both `php artisan reverb:start` and `php artisan serve` are running.

---

## Documentation & Quality
- All PHP code follows PSR standards and is documented via PHPDoc.
- Frontend logic includes robust error handling to notify users of connectivity issues.
- Server logs capture API failures for easier debugging.
