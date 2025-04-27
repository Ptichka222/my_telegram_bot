
<?php
# 8186610571:AAGQuFiDmn3j21ntnecn_Bd9HMlay46J04A
$botToken = "8186610571:AAGQuFiDmn3j21ntnecn_Bd9HMlay46J04A";

// Get incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), TRUE);

// Get the chat ID and the message text
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

// A simple response
$response = "You said: $message";

// Send the response back using Telegram API
file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($response));


?>
