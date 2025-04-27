<?php
# Hugging Face and Bot tokens
$modeUrl = "https://api-inference.huggingface.co/models/tiiuae/falcon-7b-instruct";
$hugFace = "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"; // (corrected: use your Hugging Face token here)
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
    "inputs" => "You are a helpful chatbot. User says: \"$message\". Reply nicely:",
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
$responseRaw = file_get_contents($modelUrl, false, $context);

// Decode Hugging Face response
$responseData = json_decode($responseRaw, true);

// Get the AI generated text
if (isset($responseData[0]["generated_text"])) {
    $response = $responseData[0]["generated_text"];
} else {
    $response = "Sorry, I couldn't think of anything 😢";
}

// Send the AI response back to Telegram
file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($response));
?>
