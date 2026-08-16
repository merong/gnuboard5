<?php
if (!defined('_GNUBOARD_')) exit;
// 모바일 회원 스킨: PC 포털 스킨으로 위임 (CSS URL도 PC 경로 고정)
$member_skin_url = G5_THEME_URL.'/skin/member/portal';
include_once(G5_THEME_PATH.'/skin/member/portal/profile.skin.php');
