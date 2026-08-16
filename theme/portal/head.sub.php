<?php
/*
 * 포털 테마 head.sub.php
 *
 * 이 파일이 존재하면 root G5_PATH/head.sub.php 가 이 파일을 require_once 하고 return 한다.
 * html_process::run() 출력버퍼 후처리가 서버 환경에 따라 동작하지 않을 수 있으므로
 * add_stylesheet() / add_javascript() 대신 직접 echo 로 출력한다.
 */
if (!defined('_GNUBOARD_')) exit;

// ── 페이지 타이틀 처리 ──
$g5_debug['php']['begin_time'] = $begin_time = get_microtime();

if (!isset($g5['title'])) {
    $g5['title'] = $config['cf_title'];
    $g5_head_title = $g5['title'];

    // 메인 페이지: 사이트 태그라인이 설정되어 있으면 브라우저 타이틀에 함께 노출
    if (function_exists('stf_get_meta')) {
        $g5_tagline = stf_get_meta()['tagline'] ?? '';
        if ($g5_tagline !== '') {
            $g5_head_title = implode(' - ', array_filter(array($g5_head_title, $g5_tagline)));
        }
    }
} else {
    $g5_head_title = implode(' | ', array_filter(array($g5['title'], $config['cf_title'])));
}
$g5['title']     = strip_tags($g5['title']);
$g5_head_title   = strip_tags($g5_head_title);

// ── 현재 접속자 위치 기록 ──
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super')
    $g5['lo_url'] = '';
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10">
<meta name="HandheldFriendly" content="true">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<?php if ($config['cf_add_meta']) echo $config['cf_add_meta'].PHP_EOL; ?>
<title><?php echo $g5_head_title; ?></title>
<link rel="icon" href="<?php echo G5_THEME_URL ?>/img/favicon.svg" type="image/svg+xml">
<link rel="icon" href="<?php echo G5_THEME_URL ?>/img/favicon.png" sizes="32x32" type="image/png">
<link rel="apple-touch-icon" href="<?php echo G5_THEME_URL ?>/img/apple-touch-icon.png">
<?php
// ── 기본 CSS 로드 ──
if (defined('G5_IS_ADMIN')) {
    if (!defined('_THEME_PREVIEW_'))
        $_adm_css = G5_ADMIN_PATH.'/css/admin.css';
        $_adm_ver = (is_file($_adm_css) ? filemtime($_adm_css) : G5_CSS_VER);
        echo '<link rel="stylesheet" href="'.run_replace('head_css_url', G5_ADMIN_URL.'/css/admin.css?ver='.$_adm_ver, G5_URL).'">'.PHP_EOL;
} else {
    echo '<link rel="stylesheet" href="'.run_replace('head_css_url', G5_CSS_URL.'/default.css?ver='.G5_CSS_VER, G5_URL).'">'.PHP_EOL;
}

// ── 포털 테마 CSS (직접 echo) ──
if (!defined('G5_IS_ADMIN')) {
    echo '<link rel="stylesheet" href="'.G5_THEME_CSS_URL.'/default.css?ver='.G5_SERVER_TIME.'">'.PHP_EOL;
    $portal_custom_css = G5_THEME_PATH.'/css/custom.css';
    if (is_file($portal_custom_css)) {
        echo '<link rel="stylesheet" href="'.G5_THEME_CSS_URL.'/custom.css?ver='.filemtime($portal_custom_css).'">'.PHP_EOL;
    }

    // 관리자가 고른 포인트 컬러 프리셋을 :root 커스텀 프로퍼티로 덮어쓴다 (기본값이어도 항상 출력,
    // portal.settings.php 의 PORTAL_THEME_COLOR_PRESETS/portal_theme_colors() 참고).
    require_once(__DIR__.'/portal.settings.php');
    $portal_theme_colors = portal_theme_colors();
    echo '<style>:root{--portal-primary:'.$portal_theme_colors['primary'].';--portal-primary-hover:'.$portal_theme_colors['hover'].';}</style>'.PHP_EOL;

    echo '<link rel="stylesheet" href="'.G5_JS_URL.'/font-awesome/css/font-awesome.min.css">'.PHP_EOL;
    $member_skin_css = G5_THEME_PATH.'/skin/member/portal/style.css';
    echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css?v='.filemtime($member_skin_css).'">'.PHP_EOL;
}
?>
<!--[if lte IE 8]>
<script src="<?php echo G5_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
var g5_url           = "<?php echo G5_URL ?>";
var g5_bbs_url       = "<?php echo G5_BBS_URL ?>";
var g5_is_member     = "<?php echo isset($is_member) ? $is_member : ''; ?>";
var g5_is_admin      = "<?php echo isset($is_admin)  ? $is_admin  : ''; ?>";
var g5_is_mobile     = "<?php echo G5_IS_MOBILE ?>";
var g5_bo_table      = "<?php echo isset($bo_table) ? $bo_table : ''; ?>";
var g5_sca           = "<?php echo isset($sca)      ? $sca      : ''; ?>";
var g5_editor        = "<?php echo ($config['cf_editor'] && isset($board['bo_use_dhtml_editor']) && $board['bo_use_dhtml_editor']) ? $config['cf_editor'] : ''; ?>";
var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
<?php if (defined('G5_USE_SHOP') && G5_USE_SHOP) { ?>
var g5_shop_url = "<?php echo G5_SHOP_URL; ?>";
<?php } ?>
<?php if (defined('G5_IS_ADMIN')) { ?>
var g5_admin_url = "<?php echo G5_ADMIN_URL; ?>";
<?php } ?>
</script>
<?php
// ── jQuery / 공통 JS (직접 echo) ──
echo '<script src="'.G5_JS_URL.'/jquery-1.12.4.min.js"></script>'.PHP_EOL;
echo '<script src="'.G5_JS_URL.'/jquery-migrate-1.4.1.min.js"></script>'.PHP_EOL;
if (defined('_SHOP_')) {
    if (!G5_IS_MOBILE)
        echo '<script src="'.G5_JS_URL.'/jquery.shop.menu.js?ver='.G5_JS_VER.'"></script>'.PHP_EOL;
} else {
    echo '<script src="'.G5_JS_URL.'/jquery.menu.js?ver='.G5_JS_VER.'"></script>'.PHP_EOL;
}
echo '<script src="'.G5_JS_URL.'/common.js?ver='.G5_JS_VER.'"></script>'.PHP_EOL;
echo '<script src="'.G5_JS_URL.'/wrest.js?ver='.G5_JS_VER.'"></script>'.PHP_EOL;
echo '<script src="'.G5_JS_URL.'/placeholders.min.js"></script>'.PHP_EOL;

if (G5_IS_MOBILE)
    echo '<script src="'.G5_JS_URL.'/modernizr.custom.70111.js"></script>'.PHP_EOL;

if (!defined('G5_IS_ADMIN'))
    echo $config['cf_add_script'];
?>
</head>
<body<?php echo isset($g5['body_script']) ? $g5['body_script'] : ''; ?>>
<?php
if ($is_member) {
    $sr_admin_msg = '';
    if ($is_admin == 'super')       $sr_admin_msg = '최고관리자 ';
    else if ($is_admin == 'group')  $sr_admin_msg = '그룹관리자 ';
    else if ($is_admin == 'board')  $sr_admin_msg = '게시판관리자 ';
    echo '<div id="hd_login_msg">'.$sr_admin_msg.get_text($member['mb_nick']).'님 로그인 중 ';
    echo '<a href="'.G5_BBS_URL.'/logout.php">로그아웃</a></div>';
}
