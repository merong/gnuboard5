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

