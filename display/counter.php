<?php
// 簡單的訪客計數器
define('COUNTER_FILE', 'visitor_count.txt');

// 確保 session 已啟動
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 如果檔案不存在，創建它
if (!file_exists(COUNTER_FILE)) {
    file_put_contents(COUNTER_FILE, '0');
}

// 讀取當前計數
$count = (int) file_get_contents(COUNTER_FILE);

// 使用 session 確保同一用戶在同一次訪問中只計數一次
if (!isset($_SESSION['counted'])) {
    $count++;
    file_put_contents(COUNTER_FILE, (string) $count);
    $_SESSION['counted'] = true;
}

// 返回計數（用於 AJAX 請求）
if (($_GET['action'] ?? '') === 'get_count') {
    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
    exit;
}
?>
