<?php
/*
 * 공통 사이드바 파셜
 * 호출 전 필요 변수:
 *   $notice_bo, $notice_label  – 위젯1 게시판
 *   $side_bo,   $side_label    – 위젯2 게시판
 * globals: $portal_settings, $is_admin, $is_member, $member, $config
 */
if (!defined('_GNUBOARD_')) exit;

$_sl_bo   = trim($portal_settings['side_latest_bo']  ?? '');
$_sp_bo   = trim($portal_settings['side_popular_bo'] ?? '');
$_sp_days = (int)($portal_settings['side_popular_days'] ?? 30);

$_sl_board = $_sl_bo ? get_board_db($_sl_bo, true) : null;
$_sl_label = ($_sl_board && $_sl_board['bo_table']) ? get_text($_sl_board['bo_subject']) : $_sl_bo;

$_sp_board = $_sp_bo ? get_board_db($_sp_bo, true) : null;
$_sp_label = ($_sp_board && $_sp_board['bo_table']) ? get_text($_sp_board['bo_subject']) : $_sp_bo;
?>

<!-- 로그인 위젯 -->
<div class="widget_box widget_login">
    <?php echo outlogin('theme/basic'); ?>
</div>

<!-- 사이드 위젯 1 -->
<?php if ($notice_bo): ?>
<div class="widget_box">
    <div class="widget_title">
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $notice_bo ?>"><?php echo $notice_label ?></a>
    </div>
    <div class="widget_content">
        <?php echo latest('theme/basic', $notice_bo, 5, 25); ?>
    </div>
</div>
<?php endif; ?>

<!-- 사이드 위젯 2 -->
<?php if ($side_bo): ?>
<div class="widget_box">
    <div class="widget_title">
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $side_bo ?>"><?php echo $side_label ?></a>
    </div>
    <div class="widget_content">
        <?php echo latest('theme/basic', $side_bo, 5, 25); ?>
    </div>
</div>
<?php endif; ?>

<!-- 최신글 위젯 (전체 게시판) -->
<?php
$_lat_exclude = $portal_settings['latest_exclude'] ?? [];
$_all_latest  = portal_all_latest(8, $_lat_exclude);
if ($_all_latest):
?>
<div class="widget_box widget_side_latest">
    <div class="widget_title">
        <span class="wtitle_badge">새글</span>
        전체 최신글
    </div>
    <div class="widget_content">
        <ul class="sl_list">
        <?php foreach ($_all_latest as $item): ?>
            <li class="sl_item">
                <a href="<?php echo get_pretty_url($item['bo_table'], $item['wr_id']); ?>" class="sl_link">
                    <span class="sl_board"><?php echo $item['bo_subject']; ?></span>
                    <span class="sl_tit"><?php echo cut_str(get_text($item['wr_subject']), 22, '…'); ?></span>
                </a>
                <span class="sl_date"><?php echo substr($item['wr_datetime'], 5, 5); ?></span>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- 최신 댓글 (짧게) -->
<?php
$_latest_comments = portal_latest_comments(5, $_lat_exclude ?? []);
?>
<div class="widget_box widget_latest_comments">
    <div class="widget_title">
        <span class="wtitle_badge">댓글</span>
        최신 댓글
    </div>
    <div class="widget_content">
        <?php if ($_latest_comments): ?>
        <ul class="lc_list">
        <?php foreach ($_latest_comments as $cmt):
            $cmt_parent = (int)($cmt['wr_parent'] ?: $cmt['wr_id']);
            $cmt_href = get_pretty_url($cmt['bo_table'], $cmt_parent) . '#c_' . (int)$cmt['wr_id'];
            $cmt_text = cut_str(trim(preg_replace('/\s+/', ' ', strip_tags($cmt['wr_content']))), 42, '…');
            $cmt_name = get_text($cmt['wr_name']);
        ?>
            <li class="lc_item">
                <a href="<?php echo $cmt_href ?>" class="lc_link">
                    <span class="lc_meta">
                        <span class="lc_board"><?php echo $cmt['bo_subject']; ?></span>
                        <span class="lc_name"><?php echo $cmt_name; ?></span>
                    </span>
                    <span class="lc_txt"><?php echo get_text($cmt_text); ?></span>
                </a>
                <span class="lc_date"><?php echo substr($cmt['wr_datetime'], 5, 5); ?></span>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="lc_empty">아직 댓글이 없어요.</p>
        <?php endif; ?>
    </div>
</div>

<!-- 인기글 위젯 -->
<?php
$_popular_list = ($_sp_bo && $_sp_board) ? portal_popular_articles($_sp_bo, 5, $_sp_days) : [];
if ($_popular_list):
?>
<div class="widget_box widget_popular_art">
    <div class="widget_title">
        <span class="wtitle_badge hot">인기</span>
        <?php echo $_sp_label ?> 인기글
        <?php if ($_sp_days > 0): ?><span class="widget_title_sub"><?php echo $_sp_days ?>일</span><?php endif; ?>
    </div>
    <div class="widget_content">
        <ol class="popular_art_list">
        <?php foreach ($_popular_list as $pi => $pa): ?>
            <li class="popular_art_item">
                <span class="pa_rank"><?php echo $pi + 1; ?></span>
                <a href="<?php echo get_pretty_url($pa['bo_table'], $pa['wr_id']); ?>" class="pa_title">
                    <?php echo cut_str(get_text($pa['wr_subject']), 28, '…'); ?>
                </a>
                <span class="pa_hit"><?php echo number_format($pa['wr_hit']); ?></span>
            </li>
        <?php endforeach; ?>
        </ol>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $_sp_bo ?>" class="widget_more">더보기 &rsaquo;</a>
    </div>
</div>
<?php endif; ?>

<!-- 인기 검색어 위젯 -->
<div class="widget_box">
    <div class="widget_title">인기 검색어</div>
    <div class="widget_content">
        <?php echo popular('theme/basic'); ?>
    </div>
</div>

<!-- 포인트 랭킹 -->
<div class="widget_box widget_point_rank">
    <div class="widget_title">포인트 랭킹</div>
    <div class="widget_content">
        <ol class="pr_list">
        <?php
        $_pr_sql = "SELECT mb_id, mb_nick, mb_point
                    FROM {$g5['member_table']}
                    WHERE mb_leave_date = '' AND mb_intercept_date = ''
                    ORDER BY mb_point DESC, mb_today_login DESC
                    LIMIT 8";
        $_pr_rs = sql_query($_pr_sql);
        $_pr_i = 0;
        while ($_pr = sql_fetch_array($_pr_rs)) {
            $_pr_i++;
            $_pr_nick = get_text($_pr['mb_nick']);
        ?>
            <li class="pr_item">
                <span class="pr_rank<?php echo $_pr_i <= 3 ? ' pr_top' : ''; ?>"><?php echo $_pr_i; ?></span>
                <span class="profile_img pr_pf"><?php echo get_member_profile_img($_pr['mb_id'], 28, 28, $_pr_nick); ?></span>
                <span class="pr_nick"><?php echo $_pr_nick; ?></span>
                <span class="pr_point"><?php echo number_format((int)$_pr['mb_point']); ?> P</span>
            </li>
        <?php }
        if (!$_pr_i) echo '<li class="pr_empty">아직 랭킹이 없어요.</li>';
        ?>
        </ol>
    </div>
</div>

<?php
global $g5, $config;
$_st_login = sql_fetch("SELECT COUNT(*) as c FROM ".$g5["login_table"]);
$_st_board = sql_fetch("SELECT SUM(bo_count_write) as c FROM ".$g5["board_table"]);
$_side_visitors = (int)($_st_login["c"] ?? 0);
$_side_posts    = (int)($_st_board["c"] ?? 0);
?>
<!-- 사이트 현황 -->
<div class="widget_box widget_visit widget_site_stats">
    <div class="widget_title">사이트 현황</div>
    <div class="widget_content">
        <ul class="visit_list">
            <li class="visit_item">
                <span class="visit_label">게시글</span>
                <span class="visit_val"><?php echo number_format($_side_posts) ?></span>
            </li>
            <li class="visit_item">
                <span class="visit_label">접속자</span>
                <span class="visit_val"><?php echo number_format(max($_side_visitors, 1)) ?></span>
            </li>
        </ul>
    </div>
</div>

<!-- 방문 현황 위젯 -->
<div class="widget_box widget_visit">
    <div class="widget_title">방문 현황</div>
    <div class="widget_content">
        <?php echo visit('theme/basic'); ?>
    </div>
</div>

<?php if ($is_admin == 'super'): ?>
<!-- 관리자 편집 패널 트리거 -->
<div class="widget_box widget_editor_trigger">
    <button type="button" class="editor_open_btn" onclick="openPortalEditor('tab_logo')">
        <i class="fa fa-pencil" aria-hidden="true"></i> 테마 편집
    </button>
</div>
<?php endif; ?>
