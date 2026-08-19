<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

$wr_id = isset($_REQUEST['sf_wr_id']) ? (int) $_REQUEST['sf_wr_id'] : (isset($_REQUEST['target']) ? (int) $_REQUEST['target'] : 0);
$action = isset($_REQUEST['action']) ? preg_replace('/[^a-z]/', '', $_REQUEST['action']) : 'list';
$login = moidam_video_login_url($bo_table);

if ($wr_id <= 0 || empty($write_table)) {
    moidam_video_ajax_json(array('ok' => false, 'error' => '잘못된 요청입니다.'));
}

$wr = get_write($write_table, $wr_id);
if (empty($wr['wr_id']) || !empty($wr['wr_is_comment'])) {
    moidam_video_ajax_json(array('ok' => false, 'error' => '글이 없습니다.'));
}

function moidam_video_comment_rows($write_table, $wr_id, $is_admin, $mb_id)
{
    $rows = array();
    $sql = " select wr_id, mb_id, wr_name, wr_content, wr_datetime, wr_option, wr_comment, wr_comment_reply
               from {$write_table}
              where wr_parent = '{$wr_id}' and wr_is_comment = 1
              order by wr_comment, wr_comment_reply, wr_id ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $opt = isset($row['wr_option']) ? $row['wr_option'] : '';
        $secret = (strpos($opt, 'secret') !== false);
        if ($secret && !$is_admin && $mb_id !== $row['mb_id']) {
            $content = '비밀댓글입니다.';
        } else {
            $content = get_text($row['wr_content']);
        }
        $rows[] = array(
            'wr_id' => (int) $row['wr_id'],
            'name' => get_text($row['wr_name']),
            'mb_id' => $row['mb_id'],
            'content' => $content,
            'datetime' => $row['wr_datetime'],
            'thread' => (int) $row['wr_comment'],
            'reply' => $row['wr_comment_reply'],
            'secret' => $secret ? 1 : 0,
        );
    }
    return $rows;
}

if ($action === 'write') {
    if (!$is_member) {
        moidam_video_ajax_json(array(
            'ok' => false,
            'need_login' => true,
            'login_url' => $login,
            'error' => '회원만 작성 가능합니다.',
        ));
    }
    if ($member['mb_level'] < $board['bo_comment_level']) {
        moidam_video_ajax_json(array('ok' => false, 'error' => '댓글을 쓸 권한이 없습니다.'));
    }
    $content = isset($_POST['wr_content']) ? trim($_POST['wr_content']) : '';
    $content = preg_replace("/\r\n|\r/", "\n", $content);
    if ($content === '') {
        moidam_video_ajax_json(array('ok' => false, 'error' => '댓글을 입력해 주세요.'));
    }
    if (function_exists('mb_strlen') && mb_strlen($content, 'UTF-8') > 2000) {
        moidam_video_ajax_json(array('ok' => false, 'error' => '댓글이 너무 깁니다.'));
    }
    if (isset($_SESSION['ss_datetime']) && $_SESSION['ss_datetime'] >= (G5_SERVER_TIME - (int) $config['cf_delay_sec']) && !$is_admin) {
        moidam_video_ajax_json(array('ok' => false, 'error' => '너무 빠르게 올릴 수 없습니다.'));
    }
    set_session('ss_datetime', G5_SERVER_TIME);

    $comment_id = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;
    $tmp_comment_reply = '';
    if ($comment_id > 0) {
        $reply_array = get_write($write_table, $comment_id, true);
        if (empty($reply_array['wr_id']) || empty($reply_array['wr_is_comment'])) {
            moidam_video_ajax_json(array('ok' => false, 'error' => '답글할 댓글이 없습니다.'));
        }
        if ((int) $reply_array['wr_parent'] !== (int) $wr_id) {
            moidam_video_ajax_json(array('ok' => false, 'error' => '댓글을 등록할 수 없습니다.'));
        }
        $tmp_comment = (int) $reply_array['wr_comment'];
        if (strlen($reply_array['wr_comment_reply']) >= 5) {
            moidam_video_ajax_json(array('ok' => false, 'error' => '더 이상 답글할 수 없습니다.'));
        }
        $reply_len = strlen($reply_array['wr_comment_reply']) + 1;
        $sql = " select MAX(SUBSTRING(wr_comment_reply, {$reply_len}, 1)) as reply
                   from {$write_table}
                  where wr_parent = '{$wr_id}'
                    and wr_comment = '{$tmp_comment}'
                    and SUBSTRING(wr_comment_reply, {$reply_len}, 1) <> '' ";
        if ($reply_array['wr_comment_reply']) {
            $sql .= " and wr_comment_reply like '".addslashes($reply_array['wr_comment_reply'])."%' ";
        }
        $row = sql_fetch($sql);
        if (!$row['reply']) {
            $reply_char = 'A';
        } else if ($row['reply'] === 'Z') {
            moidam_video_ajax_json(array('ok' => false, 'error' => '더 이상 답글할 수 없습니다.'));
        } else {
            $reply_char = chr(ord($row['reply']) + 1);
        }
        $tmp_comment_reply = $reply_array['wr_comment_reply'] . $reply_char;
    } else {
        $sql = " select max(wr_comment) as max_comment from {$write_table}
                  where wr_parent = '{$wr_id}' and wr_is_comment = 1 ";
        $max = sql_fetch($sql);
        $tmp_comment = ((int) $max['max_comment']) + 1;
        $tmp_comment_reply = '';
    }

    $mb_id = addslashes($member['mb_id']);
    $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
    $wr_email = addslashes($member['mb_email']);
    $wr_homepage = addslashes(isset($member['mb_homepage']) ? clean_xss_tags($member['mb_homepage']) : '');
    $wr_content = addslashes($content);
    $ca = addslashes(isset($wr['ca_name']) ? $wr['ca_name'] : '');
    $wr_num = addslashes($wr['wr_num']);
    $ip = addslashes($_SERVER['REMOTE_ADDR']);
    $tmp_comment_reply_sql = addslashes($tmp_comment_reply);

    sql_query(" insert into {$write_table}
                set ca_name = '{$ca}',
                    wr_option = '',
                    wr_num = '{$wr_num}',
                    wr_reply = '',
                    wr_parent = '{$wr_id}',
                    wr_is_comment = 1,
                    wr_comment = '{$tmp_comment}',
                    wr_comment_reply = '{$tmp_comment_reply_sql}',
                    wr_subject = '',
                    wr_content = '{$wr_content}',
                    mb_id = '{$mb_id}',
                    wr_password = '',
                    wr_name = '{$wr_name}',
                    wr_email = '{$wr_email}',
                    wr_homepage = '{$wr_homepage}',
                    wr_datetime = '".G5_TIME_YMDHIS."',
                    wr_last = '',
                    wr_ip = '{$ip}' ");
    $new_id = sql_insert_id();
    sql_query(" update {$write_table} set wr_comment = wr_comment + 1, wr_last = '".G5_TIME_YMDHIS."' where wr_id = '{$wr_id}' ");
    sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id )
                values ( '".addslashes($bo_table)."', '{$new_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$mb_id}' ) ");
    sql_query(" update {$g5['board_table']} set bo_count_comment = bo_count_comment + 1 where bo_table = '".addslashes($bo_table)."' ");
    if (function_exists('insert_point')) {
        insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id}-{$new_id} 댓글쓰기", $bo_table, $new_id, '댓글');
    }
}

$parent = sql_fetch(" select wr_comment from {$write_table} where wr_id = '{$wr_id}' ");
$comments = moidam_video_comment_rows($write_table, $wr_id, $is_admin, isset($member['mb_id']) ? $member['mb_id'] : '');
moidam_video_ajax_json(array(
    'ok' => true,
    'wr_id' => $wr_id,
    'count' => isset($parent['wr_comment']) ? (int) $parent['wr_comment'] : count($comments),
    'comments' => $comments,
    'is_member' => $is_member ? 1 : 0,
    'login_url' => $login,
));
