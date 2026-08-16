<?php
if (!defined('_GNUBOARD_')) exit;
// portal 테마 멤버 스킨: 기본 스킨 위임
$_base_skin = G5_PATH.'/skin/member/basic/login_check.skin.php';
if (file_exists($_base_skin)) include_once($_base_skin);
