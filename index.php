<?php
// Hugging Face and Bot tokens
$modeUrl = "https://huggingface.co/TheBloke/phi-2-GGUF";
$hugFace = "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"; // (corrected: use your Hugging Face token here)
$botToken = "8186610571:AAGQuFiDmn3j21ntnecn_Bd9HMlay46J04A";

// Prepare headers for Hugging Face request
$headers = [
    "Authorization: Bearer $hugFace",
    "Content-Type: application/json"
];

// Get incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), TRUE);

// Get chat ID and message from Telegram
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

// Prepare data for Hugging Face request
$data = [
    "inputs" => "You are a helpful chatbot. User says: \"$message\". Reply nicely:"
];

// Setup HTTP options for the request to Hugging Face
$options = [
    "http" => [
        "header"  => implode("\r\n", $headers),
        "method"  => "POST",
        "content" => json_encode($data),
    ],
];

// Send request to Hugging Face API
$context = stream_context_create($options);
$responseRaw = file_get_contents($modeUrl, false, $context);

// Check if the response from Hugging Face is valid
if ($responseRaw === FALSE) {
    $response = "Sorry, I couldn't connect to the AI server. Please try again later.";
} else {
    // Decode Hugging Face response
    $responseData = json_decode($responseRaw, true);

    // Get the AI generated text or fallback to a default message
    if (isset($responseData[0]["generated_text"])) {
        $response = $responseData[0]["generated_text"];
    } else {
        $response = "Sorry, I couldn't think of anything 😢";
    }
}

// Send the AI response back to Telegram
file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($response));

?>
