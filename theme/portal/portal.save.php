<?php
/* 포털 테마 설정 저장 AJAX 엔드포인트 */
ob_start();
$G5_PATH = dirname(dirname(dirname(__FILE__)));
require_once($G5_PATH.'/common.php');
require_once(G5_THEME_PATH.'/portal.settings.php');
$_stray = ob_get_clean();  // 인클루드 중 발생한 불필요한 출력 폐기

header('Content-Type: application/json; charset=utf-8');

if ($is_admin !== 'super') {
    echo json_encode(['ok' => false, 'msg' => '최고관리자만 접근 가능합니다.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => '잘못된 요청입니다.']);
    exit;
}

// 회원 아이디 목록 정제 (영문/숫자/언더바만 허용)
function portal_sanitize_id_list($ids) {
    if (!is_array($ids)) return [];
    return array_values(array_filter(
        array_map(function($x) { return preg_replace('/[^a-zA-Z0-9_]/', '', trim($x)); }, $ids),
        function($x) { return $x !== ''; }
    ));
}

$data = [];

// 로고
$data['logo_text'] = strip_tags(trim($_POST['logo_text'] ?? '')) ?: 'LOGO';
$data['logo_img']  = clean_xss_tags(trim($_POST['logo_img']  ?? ''));
$data['logo_link'] = clean_xss_tags(trim($_POST['logo_link'] ?? ''));

// 배너 (base64 인코딩된 JSON)
$banners_raw = trim(base64_decode($_POST['banners'] ?? '') ?: '[]');
$banners_in  = json_decode($banners_raw, true);
$data['banners'] = [];
if (is_array($banners_in)) {
    foreach (array_slice($banners_in, 0, 10) as $b) {
        $img = clean_xss_tags(trim($b['img'] ?? ''));
        if ($img === '') continue;
        $data['banners'][] = [
            'img'    => $img,
            'link'   => clean_xss_tags(trim($b['link']   ?? '')),
            'target' => (($b['target'] ?? '') === '_blank') ? '_blank' : '_self',
            'alt'    => strip_tags(trim($b['alt'] ?? '')),
            'pos_x'  => max(0, min(100, (int)($b['pos_x'] ?? 50))),
            'pos_y'  => max(0, min(100, (int)($b['pos_y'] ?? 50))),
        ];
    }
}

// 헤더 배너 (검색창 옆, 120x60, base64 인코딩된 JSON)
$tb_raw = trim(base64_decode($_POST['top_banner'] ?? '') ?: '{}');
$tb_in  = json_decode($tb_raw, true);
$data['top_banner'] = ['img' => '', 'link' => '', 'target' => '_self', 'alt' => '', 'exclude_members' => []];
if (is_array($tb_in)) {
    $data['top_banner'] = [
        'img'             => clean_xss_tags(trim($tb_in['img']  ?? '')),
        'link'            => clean_xss_tags(trim($tb_in['link'] ?? '')),
        'target'          => (($tb_in['target'] ?? '') === '_blank') ? '_blank' : '_self',
        'alt'             => strip_tags(trim($tb_in['alt'] ?? '')),
        'exclude_members' => portal_sanitize_id_list($tb_in['exclude_members'] ?? []),
    ];
}

// 푸터 메뉴 (base64 인코딩된 JSON)
$fmenu_raw = trim(base64_decode($_POST['footer_menu'] ?? '') ?: '[]');
$fmenu_in  = json_decode($fmenu_raw, true);
$data['footer_menu'] = [];
if (is_array($fmenu_in)) {
    foreach (array_slice($fmenu_in, 0, 10) as $fm) {
        $label = strip_tags(trim($fm['label'] ?? ''));
        if ($label === '') continue;
        $data['footer_menu'][] = [
            'label' => $label,
            'url'   => clean_xss_tags(trim($fm['url'] ?? '#')) ?: '#',
            'class' => preg_replace('/[^a-zA-Z0-9_\- ]/', '', trim($fm['class'] ?? '')),
        ];
    }
}

// 푸터 (base64 인코딩)
$data['footer_copyright'] = trim(base64_decode($_POST['footer_copyright'] ?? '') ?: '');
$data['footer_info']      = trim(base64_decode($_POST['footer_info']      ?? '') ?: '');

// 위젯 설정
$data['side_latest_bo']    = preg_replace('/[^a-zA-Z0-9_]/', '', trim($_POST['side_latest_bo']   ?? ''));
$data['side_popular_bo']   = preg_replace('/[^a-zA-Z0-9_]/', '', trim($_POST['side_popular_bo']  ?? ''));
$data['side_popular_days'] = (int)($_POST['side_popular_days'] ?? 30);

// 최상단 광고 스크립트 (base64 인코딩으로 전달)
$data['top_ad_script'] = trim(base64_decode($_POST['top_ad_script'] ?? '') ?: '');

// 게시판 사이드바 광고 스크립트 (base64 인코딩)
$data['board_sidebar_ad'] = trim(base64_decode($_POST['board_sidebar_ad'] ?? '') ?: '');

// 광고 제외 회원 아이디 (쉼표 구분)
$data['ad_exclude_members'] = portal_sanitize_id_list(explode(',', trim($_POST['ad_exclude_members'] ?? '')));

// 테마 포인트 컬러 프리셋 (화이트리스트 검증, 목록에 없으면 기본값)
$theme_color_in = trim($_POST['theme_color'] ?? '');
$data['theme_color'] = isset(PORTAL_THEME_COLOR_PRESETS[$theme_color_in]) ? $theme_color_in : 'default';

// 전체 최신글 제외 게시판
$exclude_raw = trim($_POST['latest_exclude'] ?? '');
$data['latest_exclude'] = array_values(array_filter(
    array_map(
        function($x) { return preg_replace('/[^a-zA-Z0-9_]/', '', trim($x)); },
        explode(',', $exclude_raw)
    ),
    function($x) { return $x !== ''; }
));

// 데이터 디렉터리 생성
if (!is_dir(PORTAL_DATA_PATH)) {
    @mkdir(PORTAL_DATA_PATH, 0755, true);
}

if (!is_writable(PORTAL_DATA_PATH)) {
    echo json_encode(['ok' => false, 'msg' => 'data/ 폴더에 쓰기 권한이 없습니다.']);
    exit;
}

$bytes = file_put_contents(
    PORTAL_SETTINGS_FILE,
    json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo json_encode($bytes !== false
    ? ['ok' => true]
    : ['ok' => false, 'msg' => '파일 저장에 실패했습니다.']
);
exit;
