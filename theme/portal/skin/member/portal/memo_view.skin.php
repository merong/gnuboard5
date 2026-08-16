<?php
if (!defined('_GNUBOARD_')) exit;
$_pmm_css = G5_THEME_PATH.'/skin/member/portal/style.css';
echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css?v='.filemtime($_pmm_css).'">'.PHP_EOL;

$is_recv = ($kind === 'recv');
$nick    = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], $mb['mb_homepage']);
$th_avatar = get_member_profile_img($mb['mb_id']);
$who_label = $is_recv ? '보낸 사람' : '받는 사람';
$date_label = $is_recv ? '받은 시간' : '보낸 시간';
$not_read   = (substr($memo['me_read_datetime'], 0, 1) == 0);
$read_txt   = $not_read ? '읽지 않음' : substr($memo['me_read_datetime'], 2, 14);
$reply_href = './memo_form.php?me_recv_mb_id='.urlencode($mb['mb_id']).'&amp;me_id='.(int)$memo['me_id'];
?>
<div class="pmm_wrap">
    <div class="pmm_card">

        <div class="pmm_head">
            <h1 class="pmm_title"><?php echo $is_recv ? '받은 쪽지' : '보낸 쪽지' ?></h1>
            <button type="button" class="pmm_close" onclick="pmmCloseWin()" aria-label="닫기">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="12" fill="#1a1a1a"/>
                    <path d="M8 8l8 8M16 8l-8 8" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <nav class="pmm_tabs" aria-label="쪽지함 메뉴">
            <a href="./memo.php?kind=recv" class="pmm_tab<?php echo $kind=='recv'?' pmm_tab_on':'' ?>">받은쪽지</a>
            <a href="./memo.php?kind=send" class="pmm_tab<?php echo $kind=='send'?' pmm_tab_on':'' ?>">보낸쪽지</a>
            <a href="./memo_form.php" class="pmm_tab_write">쓰기</a>
        </nav>

        <div class="pmm_view_meta">
            <div class="pmm_avatar"><?php echo $th_avatar ?></div>
            <div class="pmm_view_who">
                <span class="pmm_item_name"><span class="sound_only"><?php echo $who_label ?> </span><?php echo $nick ?></span>
                <time class="pmm_item_time"><span class="sound_only"><?php echo $date_label ?> </span><?php echo $memo['me_send_datetime'] ?></time>
                <?php if ($not_read) { ?>
                <p class="pmm_item_read pmm_item_read_off">읽지 않음</p>
                <?php } else { ?>
                <p class="pmm_item_read">읽음 <?php echo $read_txt ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="pmm_view_body">
            <?php echo conv_content($memo['me_memo'], 0) ?>
        </div>

        <div class="pmm_actions">
            <?php if ($is_recv) { ?>
            <a href="<?php echo $reply_href ?>" class="pmm_btn pmm_btn_primary">답장</a>
            <?php } ?>
            <a href="<?php echo $del_link ?>" onclick="del(this.href); return false;" class="pmm_btn pmm_btn_outline pmm_btn_danger">삭제</a>
            <a href="<?php echo $list_link ?>" class="pmm_btn pmm_btn_outline">목록</a>
        </div>

        <?php if ($prev_link || $next_link) { ?>
        <div class="pmm_view_nav">
            <?php if ($prev_link) { ?>
            <a href="<?php echo $prev_link ?>" class="pmm_nav">이전 쪽지</a>
            <?php } else { ?><span></span><?php } ?>
            <?php if ($next_link) { ?>
            <a href="<?php echo $next_link ?>" class="pmm_nav">다음 쪽지</a>
            <?php } else { ?><span></span><?php } ?>
        </div>
        <?php } ?>

        <div class="pmm_footer">
            <p class="pmm_keep">보관 최장 <?php echo (int)$config['cf_memo_del'] ?>일</p>
        </div>

    </div>
</div>
<script>
function pmmCloseWin() {
    if (window.opener) { window.close(); return; }
    history.length > 1 ? history.back() : (location.href = '<?php echo G5_URL ?>');
}
</script>
