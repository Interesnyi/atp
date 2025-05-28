<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Models\Task;
use App\Models\User;

// --- Telegram config ---
$telegramToken = '7380923864:AAGa1IykapRZ1lvxmycz7ICH9KXPyIMuiVA';
$chatId = '-4827456933';

function sendTelegram($chat_id, $text, $token, &$error = null) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text
    ];
    $result = @file_get_contents($url . '?' . http_build_query($data));
    if ($result === false) {
        $error = error_get_last()['message'] ?? 'Unknown error';
        return false;
    }
    $json = json_decode($result, true);
    if (empty($json['ok'])) {
        $error = $json['description'] ?? 'Telegram API error';
        return false;
    }
    return true;
}

$taskModel = new Task();
$now = date('Y-m-d H:i:00');
$tasks = $taskModel->getTasksToRemind($now); // Реализуйте этот метод: remind_at <= $now, notified_at IS NULL, status = 'active'

$log = [];
foreach ($tasks as $task) {
    $user = (new User())->getUserById($task['executor_id']);
    $subject = "Напоминание: {$task['title']}";
    $body = $task['description'] . "\nСрок до: " . $task['due_date'];
    $error = null;
    $success = sendTelegram($chatId, $subject . "\n" . $body, $telegramToken, $error);
    if ($success) {
        $taskModel->markTaskNotified($task['id']);
        $log[] = "[OK] {$task['title']} — отправлено";
    } else {
        $log[] = "[Ошибка] {$task['title']} — $error";
    }
}

return $log; 