<?php
// 簡單的訪客計數器
$counter_file = 'visitor_count.txt';

// 如果檔案不存在，創建它
if (!file_exists($counter_file)) {
    file_put_contents($counter_file, '0');
}

// 讀取當前計數
$count = (int)file_get_contents($counter_file);

// 使用 session 確保同一用戶在同一次訪問中只計數一次
session_start();
if (!isset($_SESSION['counted'])) {
    $count++;
    file_put_contents($counter_file, $count);
    $_SESSION['counted'] = true;
}

// 返回計數（用於 AJAX 請求）
if (isset($_GET['action']) && $_GET['action'] === 'get_count') {
    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
    exit;
}

// 將數字轉換為可愛的格式
function formatCount($number) {
    return number_format($number);
}
?>
