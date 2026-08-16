<?php
$sub_menu = "200150";
require_once './_common.php';

check_demo();

auth_check_menu($auth, $sub_menu, 'd');

$me_read = isset($_REQUEST['me_read']) ? preg_replace('/[^a-z]/', '', (string) $_REQUEST['me_read']) : '';
if (!in_array($me_read, array('read', 'unread'), true)) {
    $me_read = '';
}
if ($me_read) {
    $qstr .= ($qstr ? '&amp;' : '') . 'me_read=' . urlencode($me_read);
}

check_admin_token();

$ids = array();
if (isset($_POST['chk']) && is_array($_POST['chk'])) {
    foreach ($_POST['chk'] as $v) {
        $v = (int) $v;
        if ($v > 0) {
            $ids[] = $v;
        }
    }
} else {
    $me_id = isset($_REQUEST['me_id']) ? (int) $_REQUEST['me_id'] : 0;
    if ($me_id > 0) {
        $ids[] = $me_id;
    }
}

$ids = array_values(array_unique($ids));
if (!$ids) {
    alert('삭제할 쪽지를 선택해 주세요.', './memo_list.php?' . $qstr);
}

function adm_memo_delete_one($me_id)
{
    global $g5;

    $me_id = (int) $me_id;
    if ($me_id < 1) {
        return;
    }

    $row = sql_fetch(" select me_id, me_send_id, me_recv_mb_id from {$g5['memo_table']} where me_id = '{$me_id}' ");
    if (!$row['me_id']) {
        return;
    }

    $pair = array($me_id);
    if ((int) $row['me_send_id'] > 0) {
        $pair[] = (int) $row['me_send_id'];
    }
    $pair_in = implode(',', array_unique($pair));

    $recv_ids = array();
    $res = sql_query(" select me_id, me_recv_mb_id from {$g5['memo_table']} where me_id in ({$pair_in}) or me_send_id in ({$pair_in}) ");
    $del = array();
    while ($r = sql_fetch_array($res)) {
        $del[] = (int) $r['me_id'];
        if ($r['me_recv_mb_id'] !== '') {
            $recv_ids[] = $r['me_recv_mb_id'];
        }
    }
    if (!$del) {
        return;
    }

    $del_in = implode(',', array_unique($del));
    sql_query(" delete from {$g5['memo_table']} where me_id in ({$del_in}) ");

    foreach (array_unique($recv_ids) as $mb_id) {
        $mb_id_esc = sql_real_escape_string($mb_id);
        $cnt = (int) get_memo_not_read($mb_id_esc);
        sql_query(" update {$g5['member_table']} set mb_memo_cnt = '{$cnt}' where mb_id = '{$mb_id_esc}' ");
    }
}

foreach ($ids as $me_id) {
    adm_memo_delete_one($me_id);
}

goto_url('./memo_list.php?' . $qstr);
