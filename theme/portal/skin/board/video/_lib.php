<?php
if (!defined('_GNUBOARD_')) exit;

function moidam_video_duration($sec)
{
    $sec = (int) $sec;
    if ($sec <= 0) {
        return '';
    }
    $h = (int) floor($sec / 3600);
    $m = (int) floor(($sec % 3600) / 60);
    $s = $sec % 60;
    if ($h > 0) {
        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }
    return sprintf('%d:%02d', $m, $s);
}

function moidam_video_cfg()
{
    if (function_exists('cf_stream_load')) {
        return cf_stream_load();
    }
    return array();
}

function moidam_video_customer_code()
{
    $cfg = moidam_video_cfg();
    $code = isset($cfg['customer_code']) ? $cfg['customer_code'] : '';
    return preg_replace('/^customer-/i', '', preg_replace('/[^a-zA-Z0-9-]/', '', (string) $code));
}

function moidam_video_iframe($uid)
{
    if (function_exists('cf_stream_iframe_src')) {
        return cf_stream_iframe_src($uid);
    }
    $uid = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $uid);
    $code = moidam_video_customer_code();
    if ($uid === '' || $code === '') {
        return '';
    }
    return 'https://customer-' . $code . '.cloudflarestream.com/' . rawurlencode($uid) . '/iframe';
}

function moidam_video_thumb_src($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://[^/]+(/.*)$#i', $path, $m)) {
        $path = $m[1];
    } elseif (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if ($path !== '' && $path[0] !== '/') {
        $path = '/' . $path;
    }
    return $path;
}

function moidam_video_row($bo_table, $wr_id)
{
    $empty = array('uid' => '', 'duration' => 0, 'thumb' => '', 'ready' => 0);
    $wr_id = (int) $wr_id;
    if ($wr_id <= 0) {
        return $empty;
    }
    if (function_exists('cf_stream_get')) {
        $row = cf_stream_get($bo_table, $wr_id);
        if (is_array($row)) {
            return array(
                'uid' => isset($row['uid']) ? (string) $row['uid'] : '',
                'duration' => isset($row['duration']) ? (int) $row['duration'] : 0,
                'thumb' => isset($row['thumb']) ? (string) $row['thumb'] : '',
                'ready' => !empty($row['ready']) ? 1 : 0,
            );
        }
    }
    return $empty;
}

function moidam_video_public_host()
{
    $fwd = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? trim(explode(',', $_SERVER['HTTP_X_FORWARDED_HOST'])[0]) : '';
    $host = $fwd !== '' ? $fwd : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) $host);
    if ($host === '' || preg_match('#^(127\.0\.0\.1|localhost)(:|$)#i', $host)) {
        return '';
    }
    return $host;
}

function moidam_video_poster_abs($uid, $local_thumb)
{
    $path = moidam_video_thumb_src($local_thumb);
    $host = moidam_video_public_host();
    if ($path !== '' && $host !== '' && $path[0] === '/') {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        return ($https ? 'https' : 'http') . '://' . $host . $path;
    }
    $uid = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $uid);
    $code = moidam_video_customer_code();
    if ($uid !== '' && $code !== '') {
        return 'https://customer-' . $code . '.cloudflarestream.com/' . $uid . '/thumbnails/thumbnail.jpg';
    }
    return '';
}

function moidam_video_skin_url()
{
    return G5_THEME_URL.'/skin/board/video';
}

function moidam_video_list_items($list, $bo_table)
{
    $items = array();
    $customer = moidam_video_customer_code();
    if (!is_array($list)) {
        return $items;
    }
    foreach ($list as $item) {
        if (!is_array($item) || empty($item['wr_id'])) {
            continue;
        }
        $row = moidam_video_row($bo_table, $item['wr_id']);
        $items[] = array(
            'wr_id' => (int) $item['wr_id'],
            'subject' => get_text(strip_tags(isset($item['wr_subject']) ? $item['wr_subject'] : (isset($item['subject']) ? $item['subject'] : ''))),
            'href' => isset($item['href']) ? $item['href'] : '',
            'uid' => isset($row['uid']) ? $row['uid'] : '',
            'duration' => isset($row['duration']) ? (int) $row['duration'] : 0,
            'thumb' => moidam_video_thumb_src(isset($row['thumb']) ? $row['thumb'] : ''),
            'ready' => !empty($row['ready']) ? 1 : 0,
            'customer' => $customer,
            'name' => get_text(strip_tags(isset($item['wr_name']) ? $item['wr_name'] : (isset($item['name']) ? $item['name'] : ''))),
            'datetime' => isset($item['datetime2']) ? $item['datetime2'] : (isset($item['datetime']) ? $item['datetime'] : ''),
            'hit' => (int) (isset($item['wr_hit']) ? $item['wr_hit'] : 0),
            'good' => (int) (isset($item['wr_good']) ? $item['wr_good'] : 0),
            'comment' => (int) (isset($item['wr_comment']) ? $item['wr_comment'] : 0),
            'liked' => 0,
        );
    }
    $liked = moidam_video_liked_map($bo_table, array_column($items, 'wr_id'));
    foreach ($items as $i => $it) {
        $items[$i]['liked'] = !empty($liked[(int) $it['wr_id']]) ? 1 : 0;
    }
    return $items;
}

function moidam_video_liked_map($bo_table, $wr_ids)
{
    global $g5, $member;
    $out = array();
    if (empty($member['mb_id']) || !is_array($wr_ids) || !$wr_ids) {
        return $out;
    }
    $ids = array();
    foreach ($wr_ids as $id) {
        $id = (int) $id;
        if ($id > 0) {
            $ids[] = $id;
        }
    }
    if (!$ids) {
        return $out;
    }
    $mb = addslashes($member['mb_id']);
    $bo = addslashes($bo_table);
    $sql = " select wr_id from {$g5['board_good_table']}
              where bo_table = '{$bo}'
                and mb_id = '{$mb}'
                and bg_flag = 'good'
                and wr_id in (".implode(',', $ids).") ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $out[(int) $row['wr_id']] = 1;
    }
    return $out;
}

function moidam_video_login_url($bo_table)
{
    $q = array('bo_table' => $bo_table);
    if (defined('G5_IS_MOBILE') && G5_IS_MOBILE) {
        $q['device'] = 'mobile';
    }
    return G5_BBS_URL.'/login.php?url='.urlencode(G5_BBS_URL.'/board.php?'.http_build_query($q));
}

function moidam_video_ajax_json($payload)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
    exit;
}

