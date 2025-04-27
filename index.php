<?php
# Hugging Face and Bot tokens
$modeUrl = "https://api-inference.huggingface.co/models/gpt2";  // Use GPT-2 for simplicity
$hugFace = "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"; // Your Hugging Face token
$botToken = "8186610571:AAGQuFiDmn3j21ntnecn_Bd9HMlay46J04A"; // Your Telegram bot token

$headers = [
    "Authorization: Bearer $hugFace",
    "Content-Type: application/json"
];

// Get incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), TRUE);

// Get chat ID and message
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

// Prepare data for Hugging Face (send only the message as input)
$data = [
    "inputs" => $message,
    "parameters" => [
        "max_length" => 50, // Limit response length
    ],
];

// Prepare HTTP options
$options = [
    "http" => [
        "header"  => implode("\r\n", $headers),
        "method"  => "POST",
        "content" => json_encode($data),
    ],
];

// Send request to Hugging Face
$context = stream_context_create($options);
$responseRaw = @file_get_contents($modeUrl, false, $context);

// Check if there was an error with the request
if ($responseRaw === FALSE) {
    $response = "Error: Could not connect to Hugging Face API. Please check the model URL and your API token.";
} else {
    // Log the raw response for debugging
    error_log("Hugging Face Response: " . $responseRaw); // Log to view the response

    // Decode Hugging Face response
    $responseData = json_decode($responseRaw, true);

    // Check if the response contains generated text and return it
    if (isset($responseData[0]["generated_text"])) {
        $response = $responseData[0]["generated_text"];
    } else {
        // If the response doesn't contain the generated text, return the raw response for debugging
        $response = "Sorry, I couldn't think of anything 😢. Raw response: " . print_r($responseData, true);
    }
}

// Send the response back to Telegram
file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($response));
?>
