<?php
if (!defined('_GNUBOARD_')) exit;
$_pmm_css = G5_THEME_PATH.'/skin/member/portal/style.css';
echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css?v='.filemtime($_pmm_css).'">'.PHP_EOL;
?>
<div class="pmm_wrap">
    <div class="pmm_card">

        <div class="pmm_head">
            <h1 class="pmm_title">쪽지함</h1>
            <span class="pmm_count"><?php echo number_format($total_count) ?>통</span>
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

        <?php if (!empty($list)) { ?>
        <ul class="pmm_list">
        <?php foreach ((array)$list as $row) {
            $not_read  = (substr($row['me_read_datetime'], 0, 1) == 0);
            $is_unread = ($kind === 'recv' && $not_read);
            $preview   = utf8_strcut(strip_tags($row['me_memo']), 36, '…');
            $avatar    = get_member_profile_img($row['mb_id']);
            $who_label = ($kind === 'recv') ? '보낸 사람' : '받는 사람';
        ?>
            <li class="pmm_item<?php echo $is_unread ? ' pmm_unread' : '' ?>">
                <a href="<?php echo $row['view_href'] ?>" class="pmm_item_link" aria-label="쪽지 보기"></a>
                <div class="pmm_avatar"><?php echo $avatar ?></div>
                <div class="pmm_item_body">
                    <div class="pmm_item_top">
                        <span class="pmm_item_name"><span class="sound_only"><?php echo $who_label ?> </span><?php echo $row['name'] ?></span>
                        <?php if ($is_unread) { ?><span class="pmm_badge" aria-label="읽지 않음">N</span><?php } ?>
                        <time class="pmm_item_time"><?php echo $row['send_datetime'] ?></time>
                    </div>
                    <div class="pmm_item_bot">
                        <p class="pmm_item_preview"><?php echo $preview ?></p>
                        <?php if ($not_read) { ?>
                        <span class="pmm_item_read pmm_item_read_off">읽지 않음</span>
                        <?php } else { ?>
                        <span class="pmm_item_read">읽음 <span class="pmm_read_dt"><?php echo $row['read_datetime'] ?></span></span>
                        <?php } ?>
                    </div>
                </div>
                <a href="<?php echo $row['del_href'] ?>" onclick="del(this.href); return false;" class="pmm_del" title="삭제" aria-label="삭제"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
            </li>
        <?php } ?>
        </ul>
        <?php } else { ?>
        <div class="pmm_empty">
            <p><?php echo ($kind === 'recv') ? '받은 쪽지가 없습니다.' : '보낸 쪽지가 없습니다.'; ?></p>
        </div>
        <?php } ?>

        <div class="pmm_footer">
            <?php if ($write_pages) { ?><div class="pmm_pager"><?php echo $write_pages ?></div><?php } ?>
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
