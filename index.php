<?php
# Hugging Face and Bot tokens
$modeUrl = "https://api-inference.huggingface.co/models/gpt2"; // Update this if necessary
$hugFace = "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"; // (Use your Hugging Face token here)
$botToken = "8186610571:AAGQuFiDmn3j21ntnecn_Bd9HMlay46J04A";

$headers = [
    "Authorization: Bearer $hugFace",
    "Content-Type: application/json"
];

// Get incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), TRUE);

// Get chat ID and message
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

// Prepare data for Hugging Face
$data = [
    "inputs" => "You are a helpful chatbot. User says: \"$message\".",
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
$responseRaw = file_get_contents($modeUrl, false, $context);

// Log the raw Hugging Face response for debugging
error_log("Hugging Face Response: " . $responseRaw); // Log to view the response in Railway's logs

// Decode Hugging Face response
$responseData = json_decode($responseRaw, true);

// Check if the response has generated text and return it
if (isset($responseData[0]["generated_text"])) {
    $response = $responseData[0]["generated_text"];
} else {
    // If the response doesn't contain the generated text, return the raw response for debugging
    $response = "Sorry, I couldn't think of anything 😢. Raw response: " . print_r($responseData, true);
}

// Send the response back to Telegram
file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($response));
?>
