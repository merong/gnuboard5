<?php
if (!defined('_GNUBOARD_')) exit;
include_once G5_THEME_PATH.'/_cmt_badge.php';

define('PORTAL_DATA_PATH',     G5_THEME_PATH.'/data');
define('PORTAL_SETTINGS_FILE', PORTAL_DATA_PATH.'/portal.settings.json');

// 테마 포인트 컬러 프리셋. 'default'는 테마 원래 색상(css/tokens.css :root 기본값)과 동일해야 한다.
// 새 프리셋을 추가할 때는 이 배열에 한 줄만 추가하면 관리자 UI/저장 검증/실제 반영에 전부 쓰인다.
// hover 는 프리셋마다 명시적으로 지정한다. 'default'는 테마가 원래 쓰던 hover 색상(#02b050)을
// 그대로 보존하기 위해 일부러 아래 portal_theme_darken_hex() 계산값을 쓰지 않고 고정 지정한다.
define('PORTAL_THEME_COLOR_PRESETS', [
    'default' => ['label' => '기본(그린)',   'color' => '#03c75a', 'hover' => '#02b050'],
    'brick'   => ['label' => '브릭레드',      'color' => '#95271D', 'hover' => '#7F2119'],
    'forest'  => ['label' => '포레스트그린',  'color' => '#276F27', 'hover' => '#215E21'],
    'sky'     => ['label' => '스카이블루',    'color' => '#78A4CB', 'hover' => '#668BAD'],
    'navy'    => ['label' => '네이비',        'color' => '#0B1849', 'hover' => '#09143E'],
]);

/* 헥스 색상을 $percent(%) 만큼 어둡게 만든다 (hover 색상 자동 계산용). */
function portal_theme_darken_hex($hex, $percent = 12) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) return '#'.$hex;

    $rgb = str_split($hex, 2);
    $out = '#';
    foreach ($rgb as $c) {
        $v = max(0, (int)round(hexdec($c) * (1 - $percent / 100)));
        $out .= str_pad(dechex($v), 2, '0', STR_PAD_LEFT);
    }
    return $out;
}

/* 현재 설정된 테마 포인트 컬러(기본/hover)를 반환한다. head.sub.php 에서 사용. */
function portal_theme_colors() {
    global $portal_settings;
    $key = $portal_settings['theme_color'] ?? 'default';
    if (!isset(PORTAL_THEME_COLOR_PRESETS[$key])) $key = 'default';

    $preset  = PORTAL_THEME_COLOR_PRESETS[$key];
    $primary = $preset['color'];
    $hover   = $preset['hover'] ?? portal_theme_darken_hex($primary, 12);
    return ['primary' => $primary, 'hover' => $hover];
}

function portal_get_settings() {
    static $cache = null;
    if ($cache !== null) return $cache;

    $defaults = [
        'logo_text'          => 'LOGO',
        'logo_img'           => '',
        'logo_link'          => '',
        'banners'            => [],
        'top_banner'         => ['img' => '', 'link' => '', 'target' => '_self', 'alt' => '', 'exclude_members' => []],
        'footer_menu'        => [
            ['label' => '회사소개',        'url' => '#'],
            ['label' => '인재채용',        'url' => '#'],
            ['label' => '제휴제안',        'url' => '#'],
            ['label' => '이용약관',        'url' => '#'],
            ['label' => '개인정보처리방침', 'url' => '#', 'class' => 'privacy'],
            ['label' => '고객센터',        'url' => '#'],
        ],
        'footer_copyright'   => 'Copyright &copy; <b>YourCompany</b> All rights reserved.',
        'footer_info'        => '<span><b>사업자등록번호:</b> 123-45-67890</span><span><b>대표이사:</b> 홍길동</span><span><b>주소:</b> 서울특별시 강남구 테헤란로 123</span>',
        'side_latest_bo'     => 'news',
        'side_popular_bo'    => 'news',
        'side_popular_days'  => 30,
        'latest_exclude'     => [],
        'top_ad_script'      => '',
        'board_sidebar_ad'   => '',
        'ad_exclude_members' => [],
        'theme_color'        => 'default',
    ];

    if (file_exists(PORTAL_SETTINGS_FILE)) {
        $json = @file_get_contents(PORTAL_SETTINGS_FILE);
        if ($json) {
            $saved = json_decode($json, true);
            if (is_array($saved)) {
                $defaults = array_merge($defaults, $saved);
            }
        }
    }

    $cache = $defaults;
    return $cache;
}

$portal_settings = portal_get_settings();

/* 현재 로그인 회원이 제외 아이디 목록에 포함되어 있는지 여부 */
function portal_member_excluded($exclude) {
    global $member;

    if (empty($exclude)) return false;

    $mb_id = isset($member['mb_id']) ? $member['mb_id'] : '';
    if ($mb_id === '') return false;

    return in_array($mb_id, $exclude, true);
}

/* 현재 로그인 회원에게 광고를 노출해도 되는지 여부 (제외 아이디로 지정된 회원은 false) */
function portal_ad_allowed() {
    global $portal_settings;
    return !portal_member_excluded($portal_settings['ad_exclude_members'] ?? []);
}

/* 현재 로그인 회원에게 헤더 배너를 노출해도 되는지 여부 (제외 아이디로 지정된 회원은 false) */
function portal_top_banner_allowed() {
    global $portal_settings;
    $tb = $portal_settings['top_banner'] ?? [];
    return !portal_member_excluded($tb['exclude_members'] ?? []);
}

/* 전체 게시판 최신글: 접근 가능한 모든 게시판에서 최신 N개 반환 */
function portal_all_latest($count = 8, $exclude = []) {
    global $g5, $member, $is_admin;

    $mb_level = isset($member['mb_level']) ? (int)$member['mb_level'] : 1;

    $sql = "SELECT bo_table, bo_subject FROM {$g5['board_table']}
            WHERE bo_list_level <= {$mb_level}
              AND bo_device <> 'mobile'";
    if (!$is_admin) $sql .= " AND bo_use_cert = ''";

    $br = sql_query($sql, false);
    if (!$br) return [];

    $boards = [];
    while ($b = sql_fetch_array($br)) {
        if (!in_array($b['bo_table'], $exclude)) {
            $boards[$b['bo_table']] = get_text($b['bo_subject']);
        }
    }
    if (!$boards) return [];

    $count = max(1, (int)$count);
    $unions = [];
    foreach (array_keys($boards) as $bt) {
        $wt = $g5['write_prefix'] . $bt;
        $unions[] = "SELECT '" . addslashes($bt) . "' AS bo_table, wr_id, wr_subject, wr_datetime
                     FROM {$wt} WHERE wr_is_comment = 0";
    }

    $union_sql = "SELECT * FROM (" . implode(' UNION ALL ', $unions) . ") AS _all
                  ORDER BY wr_datetime DESC LIMIT {$count}";

    $result = sql_query($union_sql, false);
    if (!$result) return [];

    $rows = [];
    while ($row = sql_fetch_array($result)) {
        $row['bo_subject'] = $boards[$row['bo_table']] ?? $row['bo_table'];
        $rows[] = $row;
    }
    return $rows;
}

/* 전체 게시판 최신 댓글 */
function portal_latest_comments($count = 12, $exclude = []) {
    global $g5, $member, $is_admin;

    $mb_level = isset($member['mb_level']) ? (int)$member['mb_level'] : 1;

    $sql = "SELECT bo_table, bo_subject FROM {$g5['board_table']}
            WHERE bo_list_level <= {$mb_level}
              AND bo_device <> 'mobile'";
    if (!$is_admin) $sql .= " AND bo_use_cert = ''";

    $br = sql_query($sql, false);
    if (!$br) return [];

    $boards = [];
    while ($b = sql_fetch_array($br)) {
        if (!in_array($b['bo_table'], $exclude)) {
            $boards[$b['bo_table']] = get_text($b['bo_subject']);
        }
    }
    if (!$boards) return [];

    $count = max(1, (int)$count);
    $unions = [];
    foreach (array_keys($boards) as $bt) {
        $wt = $g5['write_prefix'] . $bt;
        $unions[] = "SELECT '" . addslashes($bt) . "' AS bo_table,
                            wr_id, wr_parent, wr_name, wr_content, wr_datetime
                     FROM {$wt} WHERE wr_is_comment = 1";
    }

    $union_sql = "SELECT * FROM (" . implode(' UNION ALL ', $unions) . ") AS _all
                  ORDER BY wr_datetime DESC LIMIT {$count}";

    $result = sql_query($union_sql, false);
    if (!$result) return [];

    $rows = [];
    while ($row = sql_fetch_array($result)) {
        $row['bo_subject'] = $boards[$row['bo_table']] ?? $row['bo_table'];
        $rows[] = $row;
    }
    return $rows;
}

/* 인기 기사: 조회수 상위 N개 반환 */
function portal_popular_articles($bo_table, $count = 5, $days = 30) {
    global $g5;
    if (!$bo_table || !preg_match('/^[a-zA-Z0-9_]+$/', $bo_table)) return [];

    $write_table = $g5['write_prefix'] . $bo_table;
    $count = max(1, (int)$count);
    $days  = (int)$days;

    $date_cond = ($days > 0) ? " AND wr_datetime >= DATE_SUB(NOW(), INTERVAL {$days} DAY)" : '';
    $sql = "SELECT wr_id, wr_subject, wr_hit, wr_datetime
            FROM {$write_table}
            WHERE wr_is_comment = 0{$date_cond}
            ORDER BY wr_hit DESC, wr_datetime DESC
            LIMIT {$count}";

    $result = sql_query($sql, false);
    if (!$result) return [];

    $rows = [];
    while ($row = sql_fetch_array($result)) {
        $row['bo_table'] = $bo_table;
        $rows[] = $row;
    }
    return $rows;
}
