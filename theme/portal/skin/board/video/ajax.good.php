<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

$wr_id = isset($_REQUEST['sf_wr_id']) ? (int) $_REQUEST['sf_wr_id'] : (isset($_REQUEST['target']) ? (int) $_REQUEST['target'] : 0);
$login = moidam_video_login_url($bo_table);

if (!$is_member) {
    moidam_video_ajax_json(array(
        'ok' => false,
        'need_login' => true,
        'login_url' => $login,
        'error' => '회원만 작성 가능합니다.',
    ));
}
if ($wr_id <= 0 || empty($write_table)) {
    moidam_video_ajax_json(array('ok' => false, 'error' => '잘못된 요청입니다.'));
}

$wr = get_write($write_table, $wr_id);
if (empty($wr['wr_id']) || !empty($wr['wr_is_comment'])) {
    moidam_video_ajax_json(array('ok' => false, 'error' => '글이 없습니다.'));
}

set_session('ss_view_'.$bo_table.'_'.$wr_id, true);

$mb = addslashes($member['mb_id']);
$bo = addslashes($bo_table);
$row = sql_fetch(" select bg_flag from {$g5['board_good_table']}
                    where bo_table = '{$bo}' and wr_id = '{$wr_id}' and mb_id = '{$mb}' and bg_flag = 'good' ");
$liked = !empty($row['bg_flag']);

if ($liked) {
    sql_query(" delete from {$g5['board_good_table']}
                 where bo_table = '{$bo}' and wr_id = '{$wr_id}' and mb_id = '{$mb}' and bg_flag = 'good' ");
    sql_query(" update {$write_table} set wr_good = GREATEST(wr_good - 1, 0) where wr_id = '{$wr_id}' ");
    $liked = false;
} else {
    if (!empty($wr['mb_id']) && $wr['mb_id'] === $member['mb_id'] && !$is_admin) {
        moidam_video_ajax_json(array('ok' => false, 'error' => '자신의 글에는 좋아요할 수 없습니다.'));
    }
    sql_query(" insert ignore into {$g5['board_good_table']}
                set bo_table = '{$bo}', wr_id = '{$wr_id}', mb_id = '{$mb}', bg_flag = 'good', bg_datetime = '".G5_TIME_YMDHIS."' ");
    if (get_sql_affected_rows() > 0) {
        sql_query(" update {$write_table} set wr_good = wr_good + 1 where wr_id = '{$wr_id}' ");
    }
    $liked = true;
}

$cnt = sql_fetch(" select wr_good as c from {$write_table} where wr_id = '{$wr_id}' ");
moidam_video_ajax_json(array(
    'ok' => true,
    'liked' => $liked ? 1 : 0,
    'count' => isset($cnt['c']) ? (int) $cnt['c'] : 0,
    'wr_id' => $wr_id,
));
