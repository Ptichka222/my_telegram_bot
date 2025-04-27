<?php
// Ollama and Telegram Bot settings
$modeUrl = "https://f129-188-113-217-187.ngrok-free.app/api/generate"; // Ollama local server
$botToken = "8186610571:AAGQuFiDmn3j21ntnecn_Bd9HMlay46J04A"; // Your Telegram Bot Token

// Get incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Get chat ID and user message
$chatId = $update["message"]["chat"]["id"] ?? null;
$message = $update["message"]["text"] ?? null;

// Exit if no message
if (!$chatId || !$message) {
    exit;
}

// Prepare data for Ollama
$data = [
    "model" => "llama3", // Or whatever model you have in Ollama
    "prompt" => "You are a helpful and friendly assistant. User says: \"$message\". Reply warmly:",
];

// Prepare HTTP options
$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\nAuthorization: Bearer dummy\r\n",
        "method"  => "POST",
        "content" => json_encode($data),
        "timeout" => 60 // timeout in seconds
    ]
];


// Send request to Ollama
$context = stream_context_create($options);
$responseRaw = file_get_contents($modeUrl, false, $context);

// Check for errors
if ($responseRaw === false) {
    error_log("Error connecting to Ollama.");
    $reply = "I'm having trouble thinking right now 😔.";
} else {
    $responseData = json_decode($responseRaw, true);

    if (isset($responseData["response"])) {
        $reply = $responseData["response"];
    } else {
        $reply = "Sorry, I couldn't think of anything 😢. Raw response: " . print_r($responseData, true);
    }
}

// Send the reply back to Telegram
file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($reply));
?>
