<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
while (ob_get_level() > 0) {
    ob_end_clean();
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$items = moidam_video_list_items(isset($list) ? $list : array(), isset($bo_table) ? $bo_table : '');
$page_n = isset($page) ? (int) $page : 1;
$total_n = isset($total_page) ? (int) $total_page : 1;
if ($page_n < 1) {
    $page_n = 1;
}
if ($total_n < 1) {
    $total_n = 1;
}

echo json_encode(array(
    'ok' => true,
    'page' => $page_n,
    'total_page' => $total_n,
    'total_count' => isset($total_count) ? (int) $total_count : count($items),
    'has_more' => $page_n < $total_n,
    'items' => $items,
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
exit;
