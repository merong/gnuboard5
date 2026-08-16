<?php
$sub_menu = "200150";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('error' => '잘못된 요청입니다.'), JSON_UNESCAPED_UNICODE);
    exit;
}

// check_admin_token() 과 동일하되, 실패 시 HTML alert 대신 JSON
$token = get_session('ss_admin_token');
set_session('ss_admin_token', '');
if (!$token || !isset($_POST['token']) || $token !== $_POST['token']) {
    echo json_encode(array('error' => '올바른 방법으로 이용해 주십시오.'), JSON_UNESCAPED_UNICODE);
    exit;
}

$me_id = isset($_POST['me_id']) ? (int) $_POST['me_id'] : 0;
if ($me_id < 1) {
    echo json_encode(array('error' => '쪽지 번호가 없습니다.'), JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = " select m.*, send_mb.mb_nick as send_nick, recv_mb.mb_nick as recv_nick
            from {$g5['memo_table']} m
            left join {$g5['member_table']} send_mb on send_mb.mb_id = m.me_send_mb_id
            left join {$g5['member_table']} recv_mb on recv_mb.mb_id = m.me_recv_mb_id
            where m.me_id = '{$me_id}' ";
$memo = sql_fetch($sql);
if (!$memo['me_id']) {
    echo json_encode(array('error' => '등록된 쪽지가 없습니다.'), JSON_UNESCAPED_UNICODE);
    exit;
}

$send_nick = $memo['send_nick'] ? get_text($memo['send_nick']) : '';
$recv_nick = $memo['recv_nick'] ? get_text($memo['recv_nick']) : '';
$is_unread = ($memo['me_read_datetime'] <= '0000-00-00 00:00:00');

echo json_encode(array(
    'error'     => '',
    'me_id'     => (int) $memo['me_id'],
    'send'      => get_text($memo['me_send_mb_id']) . ($send_nick ? ' (' . $send_nick . ')' : ''),
    'recv'      => get_text($memo['me_recv_mb_id']) . ($recv_nick ? ' (' . $recv_nick . ')' : ''),
    'send_time' => get_text($memo['me_send_datetime']),
    'read_time' => $is_unread ? '' : get_text($memo['me_read_datetime']),
    'unread'    => $is_unread ? 1 : 0,
    'type'      => $memo['me_type'] === 'send' ? '보낸쪽지' : '받은쪽지',
    'body'      => conv_content($memo['me_memo'], 0),
), JSON_UNESCAPED_UNICODE);
