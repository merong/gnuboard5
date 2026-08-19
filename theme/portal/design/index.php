<?php
/**
 * 모담 디자인 토큰 · UI 키트
 *
 *  - URL: /theme/portal/design/  (누구나 볼 수 있는 스타일 가이드 페이지, 검색엔진 색인은 막는다)
 *  - 토큰 표는 theme/portal/css/tokens.css 를 그대로 파싱해서 그린다 → tokens.css 만 고치면 여기도 바뀐다.
 *  - 컴포넌트 시연은 실제 테마 CSS(tokens.css → default.css → custom.css → 스킨 CSS)와
 *    실제 스킨 마크업(클래스명)을 그대로 쓴다. 여기서 보이는 모양이 곧 사이트의 모양이다.
 *  - 상단 "미리보기" 툴바(프리셋/액센트/다크)는 이 페이지 안에서만 CSS 변수를 바꿔 보여 주는 것으로,
 *    실제 설정은 관리자 > 테마 편집 > 색상 탭(portal.settings.php PORTAL_THEME_COLOR_PRESETS)에서 바꾼다.
 */
$G5_PATH = dirname(__DIR__, 3);
require_once($G5_PATH.'/common.php');

header('X-Robots-Tag: noindex, nofollow');

include_once(G5_THEME_PATH.'/_cmt_badge.php');
require_once(G5_THEME_PATH.'/portal.settings.php');

/* ── tokens.css 파싱 — :root 블록 안의 "@group <kind> <label>" 주석으로 섹션을 나누고, "--name: value; (주석=설명)" 줄을 토큰으로 읽는다 ── */
function dk_parse_tokens($file)
{
    $out = array('groups' => array(), 'dark' => array(), 'mtime' => 0);
    if (!is_file($file)) return $out;
    $css = file_get_contents($file);
    $out['mtime'] = filemtime($file);

    // 다크 팔레트
    if (preg_match('/html\[data-theme="dark"\]\s*\{(.*?)\n\}/s', $css, $m)) {
        if (preg_match_all('/(--[\w-]+)\s*:\s*([^;]+);/', $m[1], $dm, PREG_SET_ORDER)) {
            foreach ($dm as $d) $out['dark'][$d[1]] = trim($d[2]);
        }
    }
    // 라이트(:root) 블록 — @group 주석 단위로 묶는다
    if (preg_match('/:root\s*\{(.*?)\n\}/s', $css, $m)) {
        $cur = -1;
        foreach (explode("\n", $m[1]) as $line) {
            if (preg_match('/\/\*\s*@group\s+(\w+)\s+(.+?)\s*\*\//u', $line, $g)) {
                $out['groups'][] = array('kind' => $g[1], 'label' => $g[2], 'tokens' => array());
                $cur = count($out['groups']) - 1;
                continue;
            }
            if ($cur < 0) continue;
            if (preg_match('/^\s*(--[\w-]+)\s*:\s*(.+?);\s*(?:\/\*\s*(.*?)\s*\*\/)?\s*$/u', $line, $t)) {
                $out['groups'][$cur]['tokens'][] = array(
                    'name'  => $t[1],
                    'value' => trim($t[2]),
                    'desc'  => isset($t[3]) ? $t[3] : '',
                );
            }
        }
    }
    return $out;
}

function dk_h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$dk_tokens_file = G5_THEME_PATH.'/css/tokens.css';
$dk = dk_parse_tokens($dk_tokens_file);
$dk_theme_colors = portal_theme_colors();
$dk_cur_preset   = $portal_settings['theme_color'] ?? 'default';
if (!isset(PORTAL_THEME_COLOR_PRESETS[$dk_cur_preset])) $dk_cur_preset = 'default';
$dk_has_video    = is_dir(G5_THEME_PATH.'/skin/board/video');
$dk_img1 = G5_THEME_URL.'/img/hero_day.jpg';
$dk_img2 = G5_THEME_URL.'/img/hero_jeju.jpg';
$dk_link_css = function ($rel) {
    $p = G5_THEME_PATH.'/'.$rel;
    if (!is_file($p)) return;
    echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/'.$rel.'?v='.filemtime($p).'">'.PHP_EOL;
};

$g5['title'] = '디자인 토큰 · UI 키트';
include_once(G5_PATH.'/head.php');

// 키트 전용 껍데기 + 게시판 스킨 CSS(컴포넌트 시연용). head 가 이미 닫혔으므로 본문 첫머리에 직접 출력한다.
$dk_link_css('design/ui-kit.css');
$dk_link_css('skin/board/cafe_style/style.css');
if ($dk_has_video) $dk_link_css('skin/board/video/style.css');
?>

<div class="dk_sheet" id="dk_sheet">
    <div class="dk_head">
        <div>
            <h1>모담 디자인 토큰 &amp; UI 키트 <small>theme/portal</small></h1>
            <p>색상·타이포·모양·간격 토큰과 실제 컴포넌트(버튼·입력·뱃지·카드·목록·위젯)를 한 화면에서 확인합니다.
               토큰은 <code>css/tokens.css</code> 한 파일이 단일 출처이고, 이 페이지는 그 파일을 읽어 그립니다.</p>
        </div>
        <div class="dk_head_src">
            <a href="<?php echo G5_THEME_URL ?>/css/tokens.css" target="_blank" rel="noopener">css/tokens.css</a> · 수정 <?php echo $dk['mtime'] ? date('Y-m-d H:i', $dk['mtime']) : '-' ?><br>
            font: <code>var(--moidam-font-sans)</code> 14px / 1.5 · .inner <code>var(--moidam-inner)</code><br>
            현재 포인트 컬러 프리셋: <b><?php echo dk_h(PORTAL_THEME_COLOR_PRESETS[$dk_cur_preset]['label']) ?></b> <?php echo dk_h($dk_theme_colors['primary']) ?>
        </div>
    </div>

    <!-- 미리보기 툴바: 이 페이지 안에서만 CSS 변수를 바꿔 본다 -->
    <div class="dk_toolbar" id="dk_toolbar">
        <div class="dk_tb_group">
            <span class="dk_tb_label">포인트 컬러 프리셋</span>
            <?php foreach (PORTAL_THEME_COLOR_PRESETS as $pk => $pv): ?>
            <button type="button" class="dk_swatch<?php echo $pk === $dk_cur_preset ? ' is-on is-site' : '' ?>"
                    data-primary="<?php echo dk_h($pv['color']) ?>" data-hover="<?php echo dk_h($pv['hover']) ?>"
                    style="background:<?php echo dk_h($pv['color']) ?>" title="<?php echo dk_h($pv['label'].' '.$pv['color']) ?>">
                <span class="dk_sr"><?php echo dk_h($pv['label']) ?></span>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="dk_tb_group">
            <label class="dk_tb_label" for="dk_accent">앰버 액센트</label>
            <input type="color" id="dk_accent" class="dk_color_input" value="#f0a04b">
            <span class="dk_mono dk_muted" id="dk_accent_val" style="font-size:11px">#f0a04b</span>
        </div>
        <div class="dk_tb_group">
            <button type="button" class="dk_tb_btn" id="dk_dark">다크모드</button>
            <button type="button" class="dk_tb_btn" id="dk_reset">초기화</button>
        </div>
        <span class="dk_tb_hint">미리보기 전용 · 실제 설정은 관리자 &gt; 테마 편집 &gt; 색상 · 토큰 이름을 클릭하면 복사</span>
    </div>

    <?php
    /* ── 1. 토큰 표 (tokens.css 파싱) ── */
    $dk_by_kind = array();
    foreach ($dk['groups'] as $g) $dk_by_kind[$g['kind']][] = $g;
    ?>

    <?php if (empty($dk['groups'])): ?>
    <section class="dk_sec"><h2>토큰</h2><p class="dk_note"><code>css/tokens.css</code> 를 읽지 못했습니다.</p></section>
    <?php endif; ?>

    <?php if (!empty($dk_by_kind['color'])): ?>
    <section class="dk_sec" id="dk_colors">
        <h2>색상 <small>--portal-* · --moidam-* (칩은 현재 적용값, 점은 다크 값)</small></h2>
        <?php foreach ($dk_by_kind['color'] as $g): ?>
        <div class="dk_sub_h"><?php echo dk_h($g['label']) ?></div>
        <div class="dk_grid">
            <?php foreach ($g['tokens'] as $t):
                $dark = $dk['dark'][$t['name']] ?? '';
                $val_short = preg_replace('/^color-mix\(in srgb,\s*var\((--[\w-]+)\)\s*(\d+%),\s*var\((--[\w-]+)\)\)$/', 'mix($1 $2 + $3)', $t['value']);
            ?>
            <div class="dk_tok" data-copy="var(<?php echo dk_h($t['name']) ?>)" title="<?php echo dk_h($t['value']) ?>">
                <div class="dk_chip" style="background:var(<?php echo dk_h($t['name']) ?>)">
                    <?php if ($dark): ?><span class="dk_chip_dark" style="background:<?php echo dk_h($dark) ?>" title="다크: <?php echo dk_h($dark) ?>"></span><?php endif; ?>
                </div>
                <span class="dk_tok_name"><?php echo dk_h($t['name']) ?></span>
                <span class="dk_tok_val"><?php echo dk_h($val_short) ?></span>
                <?php if ($t['desc']): ?><span class="dk_tok_desc"><?php echo dk_h($t['desc']) ?></span><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <p class="dk_note" style="margin-top:16px">포인트 컬러 프리셋(관리자 &gt; 테마 편집 &gt; 색상):
            <?php $i = 0; foreach (PORTAL_THEME_COLOR_PRESETS as $pk => $pv): ?>
            <?php echo $i++ ? ' · ' : '' ?><span class="dk_mono"><?php echo dk_h($pv['label']) ?> <?php echo dk_h($pv['color']) ?> / <?php echo dk_h($pv['hover']) ?></span>
            <?php endforeach; ?>
            — 선택한 프리셋이 <code>--portal-primary</code>/<code>--portal-primary-hover</code> 를 덮어쓰고, 나머지 토큰은 그대로 유지됩니다.
        </p>
    </section>
    <?php endif; ?>

    <?php if (!empty($dk_by_kind['type'])): ?>
    <section class="dk_sec" id="dk_type">
        <h2>타이포그래피 <small>--moidam-font-* · --moidam-fs-*</small></h2>
        <div class="dk_sub_h">토큰</div>
        <div class="dk_rows">
            <?php foreach ($dk_by_kind['type'] as $g) foreach ($g['tokens'] as $t):
                $n = $t['name']; $v = $t['value'];
                if (strpos($n, '--moidam-font-') === 0) {
                    $sample = '<span style="font-family:var('.dk_h($n).');font-size:18px;font-weight:700">모담 Moidam 가나다라 Aa 0123</span>';
                } elseif (strpos($n, '--moidam-fs-') === 0) {
                    $sample = '<span style="font-size:var('.dk_h($n).');line-height:1.3">모담에서 이야기해요 Aa 0123</span>';
                } elseif (strpos($n, '--moidam-tracking') === 0) {
                    $sample = '<span style="font-size:18px;font-weight:800;letter-spacing:var('.dk_h($n).')">오늘, 모담에서 이야기해요</span>';
                } else {
                    $sample = '<span style="font-size:14px;line-height:var('.dk_h($n).');display:inline-block;max-width:420px;white-space:normal">모담은 일상·유머·맛집·갤러리·동영상을 함께 나누는 커뮤니티입니다. 기본 행간은 이 정도로 읽힙니다.</span>';
                }
            ?>
            <div class="dk_row dk_tok" data-copy="var(<?php echo dk_h($n) ?>)">
                <div class="dk_row_sample"><?php echo $sample ?></div>
                <div class="dk_row_meta"><b><?php echo dk_h($n) ?></b><?php echo dk_h($v) ?><?php if ($t['desc']): ?> · <?php echo dk_h($t['desc']) ?><?php endif; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="dk_sub_h">실제 쓰임 (사이트 규칙 그대로)</div>
        <div class="dk_rows">
            <div class="dk_row"><div class="dk_row_sample"><span style="font-family:var(--moidam-font-display);font-size:32px;font-weight:800;letter-spacing:-0.06em;color:var(--portal-primary)">모담</span><span style="display:inline-block;width:8px;height:8px;margin-left:6px;margin-bottom:14px;border-radius:50%;background:var(--moidam-accent);box-shadow:0 0 0 3px var(--moidam-accent-soft);vertical-align:middle"></span></div><div class="dk_row_meta"><b>로고 (#portal_header .logo a)</b>32px / 800 / -0.06em · --moidam-font-display</div></div>
            <div class="dk_row"><div class="dk_row_sample"><span style="font-size:clamp(24px,2.4vw,34px);font-weight:800;letter-spacing:-0.04em;line-height:1.25;color:var(--moidam-ink)">오늘, 모담에서 이야기해요</span></div><div class="dk_row_meta"><b>히어로 제목 (.moidam_hero_title)</b>clamp(24px, 2.4vw, 34px) / 800 / -0.04em</div></div>
            <div class="dk_row"><div class="dk_row_sample"><span style="font-size:18px;font-weight:800;letter-spacing:-0.03em;color:var(--moidam-ink)">어디로 가볼까요?</span></div><div class="dk_row_meta"><b>섹션 제목 (.moidam_ql_title)</b>18px / 800 / -0.03em</div></div>
            <div class="dk_row"><div class="dk_row_sample"><span style="font-size:16px;font-weight:700;letter-spacing:-0.02em">갤러리</span> <span class="dk_muted" style="font-size:12px">/</span> <span style="font-size:15px;font-weight:700;letter-spacing:-0.02em">일상공유</span></div><div class="dk_row_meta"><b>카드 제목 (.gallery_section h3 / .half_board h3)</b>16px · 15px / 700 / -0.02em</div></div>
            <div class="dk_row"><div class="dk_row_sample"><span style="font-size:14px;font-weight:700;letter-spacing:-0.02em;color:var(--moidam-ink-2)">커뮤니티</span> <span class="dk_muted" style="font-size:12px">/</span> <span style="font-size:14px;font-weight:600">새로운 향초 하나 피워봄</span></div><div class="dk_row_meta"><b>GNB · 위젯 제목 / 목록 제목 (.nv_news_tit)</b>14px / 700 · 600</div></div>
            <div class="dk_row"><div class="dk_row_sample"><span style="font-size:13px">중고거래 앱으로 옷장 비우기</span> <span style="font-size:12px;color:var(--moidam-text-muted);margin-left:10px">민수네</span><span style="font-size:11px;color:var(--moidam-text-faint);margin-left:10px">08-10</span></div><div class="dk_row_meta"><b>목록 (.pl_link / td_subject) · 메타</b>13px / 400 · 닉네임 12px · 날짜 11px</div></div>
            <div class="dk_row"><div class="dk_row_sample"><span style="font-size:10px;font-weight:800;letter-spacing:.5px;background:var(--portal-primary);color:var(--portal-on-primary);padding:1px 6px;border-radius:3px">새글</span></div><div class="dk_row_meta"><b>뱃지</b>10px / 800 / +0.5px</div></div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($dk_by_kind['shape']) || !empty($dk_by_kind['shadow']) || !empty($dk_by_kind['space']) || !empty($dk_by_kind['layout'])): ?>
    <section class="dk_sec" id="dk_shape">
        <h2>모양 · 그림자 · 간격 · 레이아웃 <small>--moidam-radius-* · --moidam-shadow* · --moidam-space* · --moidam-inner …</small></h2>
        <?php if (!empty($dk_by_kind['shape'])): ?>
        <div class="dk_sub_h">모양 (border-radius)</div>
        <div class="dk_shape_row">
            <?php foreach ($dk_by_kind['shape'] as $g) foreach ($g['tokens'] as $t): ?>
            <div class="dk_shape dk_tok" data-copy="var(<?php echo dk_h($t['name']) ?>)" style="border-radius:var(<?php echo dk_h($t['name']) ?>);<?php echo $t['value'] === '999px' ? 'height:44px;width:220px;align-items:center;' : '' ?>"><?php echo dk_h(str_replace('--moidam-radius', 'radius', $t['name'])) ?> <?php echo dk_h($t['value']) ?><?php if ($t['desc']): ?> · <?php echo dk_h($t['desc']) ?><?php endif; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($dk_by_kind['shadow'])): ?>
        <div class="dk_sub_h">그림자</div>
        <div class="dk_shape_row">
            <?php foreach ($dk_by_kind['shadow'] as $g) foreach ($g['tokens'] as $t): ?>
            <div class="dk_shape dk_tok" data-copy="var(<?php echo dk_h($t['name']) ?>)" style="border-radius:var(--moidam-radius);box-shadow:var(<?php echo dk_h($t['name']) ?>);width:220px"><?php echo dk_h($t['name']) ?><br><?php echo dk_h($t['value']) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($dk_by_kind['space'])): ?>
        <div class="dk_sub_h">간격</div>
        <div class="dk_rows">
            <?php foreach ($dk_by_kind['space'] as $g) foreach ($g['tokens'] as $t): ?>
            <div class="dk_row dk_tok" data-copy="var(<?php echo dk_h($t['name']) ?>)">
                <div class="dk_row_sample dk_bar_wrap">
                    <?php if (preg_match('/^\d+px$/', $t['value'])): ?><span class="dk_bar" style="width:var(<?php echo dk_h($t['name']) ?>)"></span><?php else: ?><span class="dk_bar" style="width:20px;height:18px;background:transparent;border-style:dashed"></span><?php endif; ?>
                    <span class="dk_muted" style="font-size:12px"><?php echo dk_h($t['desc']) ?></span>
                </div>
                <div class="dk_row_meta"><b><?php echo dk_h($t['name']) ?></b><?php echo dk_h($t['value']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($dk_by_kind['layout'])): ?>
        <div class="dk_sub_h">레이아웃 (막대는 1/4 축척)</div>
        <div class="dk_rows">
            <?php foreach ($dk_by_kind['layout'] as $g) foreach ($g['tokens'] as $t): ?>
            <div class="dk_row dk_tok" data-copy="var(<?php echo dk_h($t['name']) ?>)">
                <div class="dk_row_sample dk_bar_wrap">
                    <?php if (preg_match('/^\d+px$/', $t['value'])): ?><span class="dk_bar" style="width:calc(var(<?php echo dk_h($t['name']) ?>) / 4)"></span><?php endif; ?>
                    <span class="dk_muted" style="font-size:12px"><?php echo dk_h($t['desc']) ?></span>
                </div>
                <div class="dk_row_meta"><b><?php echo dk_h($t['name']) ?></b><?php echo dk_h($t['value']) ?></div>
            </div>
            <?php endforeach; ?>
            <div class="dk_row"><div class="dk_row_sample dk_muted" style="font-size:12px">메인: .inner 1130 = 본문(flex 1) + 28(--moidam-space-lg) + 사이드 336 · 게시판: 본문(flex 1) + 24 + 사이드 280 · 카드 사이 20 · 위젯 사이 14 · 사이드 위젯 16</div><div class="dk_row_meta"><b>그리드</b>.portal_main_grid · .portal_board_layout</div></div>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- ── 2. 컴포넌트 ── -->
    <section class="dk_sec" id="dk_buttons">
        <h2>버튼 <small>.moidam_cta · .guide_bar_btn · .ol_btn_* · .cafe_btn_* · .bsk_btn*</small></h2>
        <div class="dk_row_kit">
            <a class="moidam_cta moidam_cta_primary" href="#dk_buttons" onclick="return false">일상 쓰러 가기</a>
            <span class="dk_demo_dark"><a class="moidam_cta moidam_cta_ghost" href="#dk_buttons" onclick="return false">일상공유 보기</a></span>
            <a class="guide_bar_btn" href="#dk_buttons" onclick="return false">일상 쓰러 가기</a>
            <a class="guide_bar_btn guide_bar_btn_ghost" href="#dk_buttons" onclick="return false">일상공유 보기</a>
            <a class="moidam_hero_demo_login" href="#dk_buttons" onclick="return false" style="margin-top:0">로그인</a>
        </div>
        <div class="dk_label"><code>.moidam_cta_primary</code> / <code>.moidam_cta_ghost</code>(히어로 CTA) · <code>.guide_bar_btn</code>(가이드 바) · <code>.moidam_hero_demo_login</code> — pill(<code>--moidam-radius-pill</code>), 앰버 위 글자 <code>--moidam-on-accent</code></div>
        <div class="dk_row_kit" style="margin-top:14px">
            <button type="button" class="ol_btn_login" style="width:160px;margin:0">로그인</button>
            <a href="#dk_buttons" onclick="return false" class="ol_btn_join" style="width:160px;margin:0">회원가입</a>
            <a href="#dk_buttons" onclick="return false" class="cafe_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a>
            <a href="#dk_buttons" onclick="return false" class="cafe_btn_write_top"><i class="fa fa-pencil"></i> 글쓰기</a>
            <a href="#dk_buttons" onclick="return false" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a>
            <a href="#dk_buttons" onclick="return false" class="bsk_btn bsk_btn_icon"><i class="fa fa-list"></i> 목록</a>
            <a href="#dk_buttons" onclick="return false" class="bsk_btn">수정</a>
            <a href="#dk_buttons" onclick="return false" class="bsk_btn bsk_btn_danger">삭제</a>
            <a href="#dk_buttons" onclick="return false" class="bsk_btn bsk_btn_sm">작은 버튼</a>
            <button type="button" class="bsk_btn bsk_btn_submit">등록</button>
            <span class="btn_bo_user" style="display:inline-flex;gap:4px"><a href="#dk_buttons" onclick="return false" class="btn_admin btn" title="관리자"><i class="fa fa-cog fa-fw"></i></a><button type="button" class="btn_more_opt btn" title="게시판 관리 옵션"><i class="fa fa-ellipsis-v"></i></button></span>
            <?php if ($dk_has_video): ?>
            <span class="bsk_viewmode"><button type="button" class="bsk_viewmode_btn is-on">목록</button><button type="button" class="bsk_viewmode_btn">숏폼으로 보기</button></span>
            <?php endif; ?>
        </div>
        <div class="dk_label"><code>.ol_btn_login</code>/<code>.ol_btn_join</code>(아웃로그인, radius 10) · <code>.cafe_btn_write</code>/<code>.cafe_btn_write_top</code>(cafe_style, 34px) · <code>.bsk_btn</code> + <code>_write</code>/<code>_icon</code>/<code>_danger</code>/<code>_sm</code>/<code>_submit</code>(게시판 공통, radius 4) · <code>.btn_more_opt</code><?php if ($dk_has_video): ?> · <code>.bsk_viewmode</code>(동영상 목록/숏폼 토글)<?php endif; ?></div>
    </section>

    <section class="dk_sec" id="dk_inputs">
        <h2>입력 <small>.ol_input · .cafe_sch_* · .bsk_input · .bsk_select</small></h2>
        <div class="dk_row_kit">
            <input type="text" class="ol_input" placeholder="아이디" style="width:200px">
            <form class="cafe_sch_form" onsubmit="return false" style="display:inline-flex;align-items:center;gap:4px">
                <select class="cafe_sch_select"><option>제목</option><option>내용</option><option>글쓴이</option></select>
                <input type="text" class="cafe_sch_input" placeholder="검색어를 입력하세요">
                <button type="submit" class="cafe_btn_sch"><i class="fa fa-search"></i><span class="sound_only">검색</span></button>
            </form>
            <span class="bsk_search_input_row" style="display:inline-flex;gap:6px;margin:0"><select class="bsk_select"><option>제목</option><option>내용</option></select><input type="text" class="bsk_input bsk_search_input" placeholder="검색어" style="width:160px"><button type="button" class="bsk_btn bsk_btn_submit"><i class="fa fa-search"></i> 검색</button></span>
            <input type="text" class="bsk_input bsk_input_sm" placeholder="이름">
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:13px"><input type="checkbox" checked> 체크박스</label>
        </div>
        <div class="dk_label">헤더 검색창은 위 실제 헤더의 <code>#portal_header .search_wrap</code>(pill 44px, focus: 앰버 1px + <code>--moidam-accent-soft</code> 3px 링) 참고 · <code>.ol_input</code>(아웃로그인, radius 10) · <code>.cafe_sch_select</code>/<code>.cafe_sch_input</code>/<code>.cafe_btn_sch</code>(cafe_style, 32px, radius 4) · <code>.bsk_select</code>/<code>.bsk_input</code>(게시판 검색 모달, focus: <code>--portal-primary</code>) · 회원 폼(<code>.pms_*</code>) 포커스 링은 <code>--moidam-accent-ring</code></div>
    </section>

    <section class="dk_sec" id="dk_badges">
        <h2>뱃지 · 라벨 <small>.nv_badge · .wtitle_badge · .sl_board · .brd_side_wbadge · .cmt_badge · .bsk_badge_*</small></h2>
        <div class="dk_row_kit">
            <em class="nv_badge badge_hot">인기</em><em class="nv_badge badge_notice">공지</em>
            <span class="wtitle_badge">새글</span><span class="wtitle_badge">댓글</span><span class="wtitle_badge hot">인기</span>
            <span class="sl_board">동영상</span><span class="brd_side_wbadge">30일</span>
            <span style="display:inline-flex;align-items:center;gap:6px"><?php echo moidam_cmt_badge(1).moidam_cmt_badge(10).moidam_cmt_badge(120) ?></span>
            <span style="display:inline-flex;align-items:center;gap:4px"><span class="brd_side_rank brd_side_rank_top">1</span><span class="brd_side_rank">4</span></span>
            <span class="bsk_badge bsk_badge_notice">공지</span><span class="bsk_badge bsk_badge_new">N</span><span class="bsk_badge bsk_badge_hot">HOT</span><span class="bsk_badge bsk_badge_cate">카테고리</span>
            <span class="cafe_notice_badge">공지</span>
            <?php if ($dk_has_video): ?><span class="bsk_video_dur dk_static">0:28</span><?php endif; ?>
        </div>
        <div class="dk_label"><code>.nv_badge.badge_hot</code>(<code>--moidam-hot-text</code> on <code>--moidam-hot-soft</code>) · <code>.badge_notice</code> · <code>.wtitle_badge</code>(.hot = <code>--moidam-hot</code>) · <code>.sl_board</code> · <code>.brd_side_wbadge</code>(<code>--portal-primary-soft</code>) · <code>moidam_cmt_badge($n)</code>(댓글 수, 10+/100+ 크기 단계) · <code>.brd_side_rank(_top)</code> · <code>.bsk_badge_*</code> · <code>.cafe_notice_badge</code></div>
    </section>

    <section class="dk_sec" id="dk_cards">
        <h2>카드 · 슬라이드 <small>.moidam_ql_card · .pl_gal_link · .bsk_gall_item<?php echo $dk_has_video ? ' · .moidam_vl_thumb' : '' ?></small></h2>
        <div class="dk_row_kit" style="align-items:flex-start;gap:16px">
            <div class="moidam_quicklinks" style="display:grid;grid-template-columns:minmax(0,237px);gap:0;margin:0">
                <a class="moidam_ql_card" href="#dk_cards" onclick="return false"><span class="moidam_ql_icon" aria-hidden="true"><i class="fa fa-sun-o"></i></span><span class="moidam_ql_body"><strong>일상공유</strong><em>오늘의 기록 · 29글</em></span></a>
            </div>
            <ul class="pl_gallery" style="grid-template-columns:repeat(2,minmax(0,164px));gap:12px;margin:0;width:100%;max-width:340px">
                <li class="pl_gal_item"><a href="#dk_cards" onclick="return false" class="pl_gal_link"><span class="pl_gal_thumb"><img src="<?php echo $dk_img1 ?>" alt=""></span><span class="pl_gal_tit">골목 카페 창가 자리<?php echo moidam_cmt_badge(3) ?></span></a></li>
                <?php if ($dk_has_video): ?>
                <li class="pl_gal_item moidam_video_latest"><a href="#dk_cards" onclick="return false" class="pl_gal_link"><span class="pl_gal_thumb moidam_vl_thumb" style="position:relative;display:block;overflow:hidden"><img src="<?php echo $dk_img2 ?>" alt="" style="width:100%;height:100%;object-fit:cover"><span class="moidam_vl_play"><i class="fa fa-play"></i></span><span class="moidam_vl_dur">0:13</span></span><span class="pl_gal_tit">사막 — Commons 무료 영상<?php echo moidam_cmt_badge(10) ?></span></a></li>
                <?php endif; ?>
            </ul>
            <ul class="bsk_gall_grid" style="display:grid;grid-template-columns:minmax(0,190px);gap:0;margin:0">
                <li class="bsk_gall_item">
                    <a href="#dk_cards" onclick="return false" class="bsk_gall_link"><span class="bsk_gall_thumb"><img src="<?php echo $dk_img2 ?>" alt=""><span class="bsk_gall_badge_new">N</span></span></a>
                    <div class="bsk_gall_info">
                        <a href="#dk_cards" onclick="return false" class="bsk_gall_tit_link"><span class="bsk_gall_tit">비 오는 창의 물방울 <?php echo moidam_cmt_badge(2) ?></span></a>
                        <span class="bsk_gall_meta"><span>민수네</span><span class="bsk_meta_sep">·</span><span>08-10</span><span class="bsk_meta_sep">·</span><span><i class="fa fa-eye"></i> 314</span></span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="dk_label"><code>.moidam_ql_card</code>(72px, 아이콘 칩 42px <code>--moidam-accent-soft</code>) · 메인 갤러리<?php echo $dk_has_video ? '/영상' : '' ?> 슬라이드 <code>.pl_gal_link</code>(164px, radius 8) · <code>.bsk_gall_item</code>(게시판 갤러리 카드, radius 8, hover lift)</div>
    </section>

    <section class="dk_sec" id="dk_lists">
        <h2>목록 · 위젯 <small>.half_board · .nv_news_list · .portal_tabs · .moidam_pop · .widget_box · .brd_side_widget</small></h2>
        <div class="dk_kit_grid">
            <div>
                <div class="half_board">
                    <h3><a href="#dk_lists" onclick="return false">일상공유</a></h3>
                    <ul class="pl_list">
                        <li class="pl_item"><a href="#dk_lists" onclick="return false" class="pl_link"><span class="pl_tit">새로운 향초 하나 피워봄</span><?php echo moidam_cmt_badge(1) ?><span class="pl_new_dot"></span></a><span class="pl_nick">하늘이</span><span class="pl_date">08-06</span></li>
                        <li class="pl_item pl_notice"><a href="#dk_lists" onclick="return false" class="pl_link"><em class="pl_badge pl_badge_notice">공지</em><span class="pl_tit">8월 정기 점검 안내</span></a><span class="pl_nick">운영자</span><span class="pl_date">08-01</span></li>
                        <li class="pl_item"><a href="#dk_lists" onclick="return false" class="pl_link"><span class="pl_tit">팟캐스트 대신 라디오 켠 날</span></a><span class="pl_nick">준호</span><span class="pl_date">08-02</span></li>
                    </ul>
                    <a href="#dk_lists" onclick="return false" class="pl_more">더보기 &rsaquo;</a>
                </div>
                <div class="dk_label"><code>.half_board</code> + <code>.pl_list</code>(<code>skin/latest/basic</code>) — 하단 2단 게시판·사이드 위젯 공용. 공지 <code>.pl_badge_notice</code>, 새글 점 <code>.pl_new_dot</code></div>
            </div>
            <div>
                <div class="half_board moidam_humor_feed">
                    <div class="moidam_section_head">
                        <h3><a href="#dk_lists" onclick="return false"><i class="fa fa-smile-o"></i> 오늘의 유머</a></h3>
                        <div class="moidam_section_actions"><a class="moidam_text_link" href="#dk_lists" onclick="return false">더보기</a><a class="moidam_text_link moidam_text_link_accent" href="#dk_lists" onclick="return false">유머 쓰기</a></div>
                    </div>
                    <ul class="nv_news_list">
                        <li class="nv_news_item"><a href="#dk_lists" onclick="return false" class="nv_news_link"><span class="nv_news_tit"><em class="nv_badge badge_hot">인기</em> 월요일 커피는 의무</span></a><span class="nv_news_meta"><span class="nv_source">모이담유머</span><span class="nv_dot_sep">·</span><span class="nv_time">08-11</span></span></li>
                        <li class="nv_news_item"><a href="#dk_lists" onclick="return false" class="nv_news_link"><span class="nv_news_tit">침대와의 협상 <?php echo moidam_cmt_badge(1) ?><span class="nv_new_dot" title="새글"></span></span></a><span class="nv_news_meta"><span class="nv_source">모이담유머</span><span class="nv_dot_sep">·</span><span class="nv_time">08-07</span></span></li>
                        <li class="nv_news_item is_notice"><a href="#dk_lists" onclick="return false" class="nv_news_link"><span class="nv_news_tit"><em class="nv_badge badge_notice">공지</em> 유머 게시판 이용 안내</span></a><span class="nv_news_meta"><span class="nv_source">운영자</span><span class="nv_dot_sep">·</span><span class="nv_time">07-30</span></span></li>
                    </ul>
                    <a href="#dk_lists" onclick="return false" class="nv_more_btn">더보기 &rsaquo;</a>
                </div>
                <div class="dk_label"><code>.moidam_section_head</code> + <code>.nv_news_list</code>(<code>skin/latest/news_portal</code>) — 유머 피드·탭 게시판 공용</div>
            </div>
            <div>
                <div class="portal_tabs">
                    <div class="tab_nav" role="tablist"><button type="button" class="active">일상공유</button><button type="button">자유게시판</button><button type="button">맛집·먹거리</button></div>
                    <div class="tab_content active">
                        <ul class="nv_news_list">
                            <li class="nv_news_item"><a href="#dk_lists" onclick="return false" class="nv_news_link"><span class="nv_news_tit"><em class="nv_badge badge_hot">인기</em> 저녁 전화로 부모님께 안부</span></a><span class="nv_news_meta"><span class="nv_source">서연맘</span><span class="nv_dot_sep">·</span><span class="nv_time">08-04</span></span></li>
                            <li class="nv_news_item"><a href="#dk_lists" onclick="return false" class="nv_news_link"><span class="nv_news_tit">주말 산책로 추천해요</span></a><span class="nv_news_meta"><span class="nv_source">하늘이</span><span class="nv_dot_sep">·</span><span class="nv_time">08-03</span></span></li>
                        </ul>
                        <a href="#dk_lists" onclick="return false" class="nv_more_btn">더보기 &rsaquo;</a>
                    </div>
                </div>
                <div class="dk_label"><code>.portal_tabs</code> — 활성 탭: 포인트 컬러 글자 + 앰버 3px 밑줄</div>
            </div>
            <div>
                <div class="moidam_pop">
                    <div class="moidam_pop_nav" role="tablist"><button type="button" class="moidam_pop_tab">오늘 인기글</button><button type="button" class="moidam_pop_tab">주간 인기글</button><button type="button" class="moidam_pop_tab is-on">월간 인기글</button></div>
                    <div class="moidam_pop_swipe"><div class="moidam_pop_track">
                    <ol class="moidam_pop_list is-on">
                        <li><span class="moidam_pop_rank">1</span><a href="#dk_lists" onclick="return false" class="moidam_pop_tit">이어폰 줄 없는 세상 적응기</a><?php echo moidam_cmt_badge(4) ?><span class="moidam_pop_hit"><i class="fa fa-eye"></i> 412</span></li>
                        <li><span class="moidam_pop_rank">2</span><a href="#dk_lists" onclick="return false" class="moidam_pop_tit">플랜트 부모 되는 중입니다</a><span class="moidam_pop_hit"><i class="fa fa-eye"></i> 406</span></li>
                        <li><span class="moidam_pop_rank">3</span><a href="#dk_lists" onclick="return false" class="moidam_pop_tit">공기튀김기 활용 레시피</a><span class="moidam_pop_hit"><i class="fa fa-eye"></i> 388</span></li>
                    </ol>
                    </div></div>
                </div>
                <div class="dk_label"><code>.moidam_pop</code>(<code>skin/board/_popular_tabs.php</code>) — 게시판 상단 인기글 탭</div>
            </div>
            <div>
                <div class="dk_demo_side">
                    <div class="widget_box widget_side_latest">
                        <div class="widget_title"><span class="wtitle_badge">새글</span> 전체 최신글</div>
                        <div class="widget_content">
                            <ul class="sl_list">
                                <li class="sl_item"><a href="#dk_lists" onclick="return false" class="sl_link"><span class="sl_board">동영상</span><span class="sl_tit">사막 — Commons 무료 영상</span></a><span class="sl_date">08-17</span></li>
                                <li class="sl_item"><a href="#dk_lists" onclick="return false" class="sl_link"><span class="sl_board">갤러리</span><span class="sl_tit">골목 카페 창가 자리</span></a><span class="sl_date">08-11</span></li>
                                <li class="sl_item"><a href="#dk_lists" onclick="return false" class="sl_link"><span class="sl_board">자유게시판</span><span class="sl_tit">주말 산책로 추천해요</span></a><span class="sl_date">08-03</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="dk_label"><code>.widget_box</code> + <code>.widget_title</code>(<code>.wtitle_badge</code>) + <code>.sl_list</code>(<code>_sidebar.php</code> 전체 최신글)</div>
            </div>
            <div>
                <div class="dk_demo_side">
                    <div class="brd_side_widget">
                        <div class="brd_side_whead"><span class="brd_side_wtitle">그룹 인기글</span><span class="brd_side_wbadge">30일</span></div>
                        <ul class="brd_side_post_list">
                            <li class="brd_side_post_item"><span class="brd_side_rank brd_side_rank_top">1</span><a href="#dk_lists" onclick="return false" class="brd_side_post_link">공기튀김기 활용 레시피</a></li>
                            <li class="brd_side_post_item"><span class="brd_side_rank brd_side_rank_top">2</span><a href="#dk_lists" onclick="return false" class="brd_side_post_link">플랜트 부모 되는 중입니다</a></li>
                            <li class="brd_side_post_item"><span class="brd_side_rank">4</span><a href="#dk_lists" onclick="return false" class="brd_side_post_link">이어폰 줄 없는 세상 적응기</a></li>
                        </ul>
                    </div>
                    <div class="brd_side_widget" style="margin-top:16px">
                        <div class="brd_side_whead"><span class="brd_side_wtitle">같은 그룹 게시판</span></div>
                        <ul class="brd_side_board_list">
                            <li class="brd_side_board_item brd_side_board_cur"><a href="#dk_lists" onclick="return false"><i class="fa fa-angle-right" aria-hidden="true"></i> 자유게시판</a></li>
                            <li class="brd_side_board_item"><a href="#dk_lists" onclick="return false">일상공유</a></li>
                            <li class="brd_side_board_item"><a href="#dk_lists" onclick="return false">맛집·먹거리</a></li>
                        </ul>
                    </div>
                </div>
                <div class="dk_label"><code>.brd_side_widget</code>(<code>_board_sidebar.php</code>) — 헤드 <code>--portal-surface-alt</code> + 포인트 컬러 2px 선</div>
            </div>
        </div>
    </section>

    <section class="dk_sec" id="dk_board">
        <h2>게시판 목록 · 페이저 <small>#bo_list.cafe_board .tbl_head01 · .pg_wrap · .bsk_pager · .bsk_toolbar</small></h2>
        <div id="bo_list" class="cafe_board">
            <div class="tbl_head01 tbl_wrap board_list">
                <table>
                    <caption>목록 예시</caption>
                    <thead><tr><th scope="col" class="td_num2">번호</th><th scope="col">제목</th><th scope="col" class="td_name">글쓴이</th><th scope="col" class="td_num">조회</th><th scope="col" class="td_datetime">날짜</th></tr></thead>
                    <tbody>
                        <tr class="bo_notice"><td class="td_num2"><span class="cafe_notice_badge">공지</span></td><td class="td_subject"><div class="bo_tit"><a href="#dk_board" onclick="return false">8월 정기 점검 안내</a></div></td><td class="td_name">운영자</td><td class="td_num">1,204</td><td class="td_datetime">08-01</td></tr>
                        <tr class="even"><td class="td_num2">42</td><td class="td_subject"><div class="bo_tit"><a href="#dk_board" onclick="return false">이어폰 줄 없는 세상 적응기</a><span class="icon_new">N<span class="sound_only">새글</span></span> <?php echo moidam_cmt_badge(4) ?></div></td><td class="td_name">민수네</td><td class="td_num">412</td><td class="td_datetime">08-18</td></tr>
                        <tr><td class="td_num2">41</td><td class="td_subject"><div class="bo_tit"><a href="#dk_board" onclick="return false">플랜트 부모 되는 중입니다</a></div></td><td class="td_name">하늘이</td><td class="td_num">406</td><td class="td_datetime">08-17</td></tr>
                        <tr class="even"><td class="td_num2">40</td><td class="td_subject"><div class="bo_tit"><a href="#dk_board" onclick="return false">[후기] 공기튀김기 활용 레시피</a> <?php echo moidam_cmt_badge(12) ?></div></td><td class="td_name">서연맘</td><td class="td_num">388</td><td class="td_datetime">08-16</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="pg_wrap">
                <nav class="pg"><span class="sound_only">열린</span><strong class="pg_current">1</strong><span class="sound_only">페이지</span><a href="#dk_board" onclick="return false" class="pg_page">2<span class="sound_only">페이지</span></a><a href="#dk_board" onclick="return false" class="pg_page">3<span class="sound_only">페이지</span></a><a href="#dk_board" onclick="return false" class="pg_page pg_next">다음</a><a href="#dk_board" onclick="return false" class="pg_page pg_end">맨끝</a></nav>
            </div>
        </div>
        <div class="dk_label"><code>#bo_list.cafe_board</code>(<code>skin/board/cafe_style</code>) — 행 hover <code>--cs-bg-hover</code>, 공지 행 <code>--cs-bg-notice</code>, 현재 페이지는 포인트 컬러 채움</div>
        <div class="dk_sub_h" style="margin-top:18px">bsk 공통 (갤러리·웹진·동영상·글 보기)</div>
        <div class="bsk_toolbar"><div class="bsk_btn_group"><a href="#dk_board" onclick="return false" class="bsk_btn bsk_btn_icon"><i class="fa fa-list"></i> 목록</a><a href="#dk_board" onclick="return false" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a></div><div class="bsk_btn_group"><a href="#dk_board" onclick="return false" class="bsk_btn">수정</a><a href="#dk_board" onclick="return false" class="bsk_btn bsk_btn_danger">삭제</a></div></div>
        <div class="bsk_pager"><span class="sound_only">열린</span><strong class="pg_current">1</strong><span class="sound_only">페이지</span><a href="#dk_board" onclick="return false" class="pg_page">2</a><a href="#dk_board" onclick="return false" class="pg_page">3</a><a href="#dk_board" onclick="return false" class="pg_page pg_next">다음</a></div>
        <div class="dk_label"><code>.bsk_toolbar</code> + <code>.bsk_btn_group</code> · <code>.bsk_pager</code></div>
    </section>

    <section class="dk_sec" id="dk_usage">
        <h2>사용 규칙 <small>theme/portal/CLAUDE.md 요약</small></h2>
        <p class="dk_note">
            · 브랜드 색(그린/네이비/앰버)은 hex 대신 <code>var(--portal-primary)</code> / <code>var(--portal-primary-hover)</code> / <code>var(--moidam-accent)</code> 계열만 쓴다.
              프리셋을 바꿔도 hex 로 박힌 곳만 옛 색으로 남는 버그를 막기 위해서다. (예외: 네이버 공유 버튼 <code>#03c75a</code> 같은 외부 브랜드 고정색)<br>
            · 다크모드 대응이 필요한 배경/선/글자는 <code>--portal-bg/surface/surface-alt/border/text/text-sub</code>, <code>--moidam-ink/-ink-2/-text-muted/-text-faint/-line/-card-border</code> 를 쓴다 (다크 값은 <code>tokens.css</code> 의 <code>html[data-theme="dark"]</code> 블록).<br>
            · 카드는 <code>--moidam-radius</code> + <code>--moidam-card-border</code> + <code>--moidam-shadow-soft</code>, 작은 UI 는 <code>--moidam-radius-sm/-xs</code>, pill 은 <code>--moidam-radius-pill</code>.<br>
            · 새 스킨의 로컬 변수(예: <code>--cs-primary</code>)는 정의부만 토큰을 가리키게 하면 파일 전체가 따라온다 (<code>skin/board/cafe_style/style.css</code> 참고).<br>
            · 토큰을 추가/변경할 때는 <code>css/tokens.css</code> 만 고치고 <code>@group</code> 주석 형식을 유지한다 — 이 페이지가 그 주석을 섹션 구분자로 쓴다.
        </p>
    </section>
</div>

<script>
(function () {
    var root = document.documentElement;
    var PRIMARY_VARS = ['--portal-primary', '--portal-primary-hover'];
    var ACCENT_VARS  = ['--moidam-accent', '--moidam-accent-hover', '--moidam-accent-soft', '--moidam-accent-ring'];

    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex.split('').map(function (c) { return c + c; }).join('');
        var n = parseInt(hex, 16);
        return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
    }
    function darken(hex, pct) {
        var rgb = hexToRgb(hex).map(function (v) { return Math.max(0, Math.round(v * (1 - pct / 100))); });
        return '#' + rgb.map(function (v) { return ('0' + v.toString(16)).slice(-2); }).join('');
    }

    // 프리셋 미리보기
    var swatches = document.querySelectorAll('#dk_toolbar .dk_swatch');
    Array.prototype.forEach.call(swatches, function (btn) {
        btn.addEventListener('click', function () {
            root.style.setProperty('--portal-primary', btn.getAttribute('data-primary'));
            root.style.setProperty('--portal-primary-hover', btn.getAttribute('data-hover'));
            Array.prototype.forEach.call(swatches, function (b) { b.classList.remove('is-on'); });
            btn.classList.add('is-on');
        });
    });

    // 액센트 미리보기
    var accent = document.getElementById('dk_accent');
    var accentVal = document.getElementById('dk_accent_val');
    if (accent) {
        accent.addEventListener('input', function () {
            var hex = accent.value, rgb = hexToRgb(hex);
            root.style.setProperty('--moidam-accent', hex);
            root.style.setProperty('--moidam-accent-hover', darken(hex, 10));
            root.style.setProperty('--moidam-accent-soft', 'rgba(' + rgb.join(',') + ',0.16)');
            root.style.setProperty('--moidam-accent-ring', 'rgba(' + rgb.join(',') + ',0.22)');
            accentVal.textContent = hex;
        });
    }

    // 다크모드 미리보기
    var dark = document.getElementById('dk_dark');
    if (dark) {
        dark.addEventListener('click', function () {
            var on = root.getAttribute('data-theme') !== 'dark';
            if (on) root.setAttribute('data-theme', 'dark'); else root.removeAttribute('data-theme');
            dark.classList.toggle('is-on', on);
        });
    }

    // 초기화
    var reset = document.getElementById('dk_reset');
    if (reset) {
        reset.addEventListener('click', function () {
            PRIMARY_VARS.concat(ACCENT_VARS).forEach(function (v) { root.style.removeProperty(v); });
            root.removeAttribute('data-theme');
            if (dark) dark.classList.remove('is-on');
            if (accent) { accent.value = '#f0a04b'; accentVal.textContent = '#f0a04b'; }
            Array.prototype.forEach.call(swatches, function (b) { b.classList.toggle('is-on', b.classList.contains('is-site')); });
        });
    }

    // 토큰 이름 클릭 → var(--…) 복사
    document.getElementById('dk_sheet').addEventListener('click', function (e) {
        var tok = e.target.closest ? e.target.closest('.dk_tok') : null;
        if (!tok || !tok.getAttribute('data-copy')) return;
        var text = tok.getAttribute('data-copy');
        var done = function () {
            tok.classList.add('is-copied');
            setTimeout(function () { tok.classList.remove('is-copied'); }, 900);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () {});
        } else {
            var ta = document.createElement('textarea');
            ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); done(); } catch (err) {}
            document.body.removeChild(ta);
        }
    });
})();
</script>

<?php
include_once(G5_PATH.'/tail.php');
