<?php
if (!defined('_GNUBOARD_')) exit;
include_once(__DIR__ . '/_lib.php');
$video_skin_url = moidam_video_skin_url();
add_stylesheet('<link rel="stylesheet" href="'.$video_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);

$row = moidam_video_row($bo_table, isset($view['wr_id']) ? $view['wr_id'] : 0);
$uid = $row['uid'];
$ready = $row['ready'] ? '1' : '0';
$iframe = moidam_video_iframe($uid);
$poster = moidam_video_poster_abs($uid, $row['thumb']);
if ($iframe && $poster) {
    $iframe .= (strpos($iframe, '?') === false ? '?' : '&') . 'poster=' . rawurlencode($poster);
}

ob_start();
include_once G5_THEME_PATH.'/skin/board/_base/view.skin.php';
$html = ob_get_clean();

$player = '<section class="bsk_video_player" aria-label="동영상">';
if ($iframe && $ready === '1') {
    $player .= '<div class="bsk_video_frame"><iframe src="'.htmlspecialchars($iframe, ENT_QUOTES).'" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen></iframe></div>';
} elseif ($uid) {
    $player .= '<div class="bsk_video_wait">영상을 처리하고 있습니다. 잠시 후 새로고침해 주세요.</div>';
} else {
    $player .= '<div class="bsk_video_wait">등록된 영상이 없습니다.</div>';
}
$player .= '</section>';

if (strpos($html, '<section class="bsk_view_body">') !== false) {
    echo str_replace('<section class="bsk_view_body">', $player.'<section class="bsk_view_body">', $html);
} else {
    echo $player.$html;
}
