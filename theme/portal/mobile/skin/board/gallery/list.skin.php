<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$list_count = count($list);
$thumb_w    = (int)($board['bo_gallery_width']  ?: 240);
$thumb_h    = (int)($board['bo_gallery_height'] ?: 160);

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);
?>
<div id="bo_list" class="mg_board" style="width:<?php echo $width; ?>">

<?php if ($is_category): ?>
<nav id="bo_cate" class="mg_cate_nav">
    <ul id="bo_cate_ul"><?php echo $category_option ?></ul>
</nav>
<?php endif; ?>

<form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL ?>/board_list_update.php"
      onsubmit="return fboardlist_submit(this);" method="post">
<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
<input type="hidden" name="sfl"      value="<?php echo $sfl ?>">
<input type="hidden" name="stx"      value="<?php echo $stx ?>">
<input type="hidden" name="spt"      value="<?php echo $spt ?>">
<input type="hidden" name="sca"      value="<?php echo $sca ?>">
<input type="hidden" name="sst"      value="<?php echo $sst ?>">
<input type="hidden" name="sod"      value="<?php echo $sod ?>">
<input type="hidden" name="page"     value="<?php echo $page ?>">
<input type="hidden" name="sw"       value="">

<!-- 상단 바 -->
<div class="mg_list_top">
    <span class="mg_total">전체 <strong><?php echo number_format($total_count) ?></strong>건</span>
    <div class="mg_top_right">
        <?php if ($admin_href): ?><a href="<?php echo $admin_href ?>" class="mg_btn_icon" title="관리자"><i class="fa fa-cog"></i></a><?php endif; ?>
        <?php if ($rss_href): ?><a href="<?php echo $rss_href ?>" class="mg_btn_icon" title="RSS"><i class="fa fa-rss"></i></a><?php endif; ?>
        <?php if ($is_admin == 'super' || $is_auth): ?>
        <div class="mg_more_wrap">
            <button type="button" class="mg_btn_icon mg_more_btn"><i class="fa fa-ellipsis-v"></i></button>
            <?php if ($is_checkbox): ?>
            <ul class="mg_more_menu" style="display:none">
                <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"><i class="fa fa-trash-o"></i> 선택삭제</button></li>
                <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"><i class="fa fa-files-o"></i> 선택복사</button></li>
                <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"><i class="fa fa-arrows"></i> 선택이동</button></li>
            </ul>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 검색바 -->
<div class="mg_search_bar">
    <form name="fsearch_top" class="mg_sch_form" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sca"      value="<?php echo $sca ?>">
        <input type="hidden" name="sop"      value="and">
        <div class="mg_sch_inner">
            <div class="mg_sch_select_wrap">
                <select name="sfl" class="mg_sch_select"><?php echo get_board_sfl_select_options($sfl); ?></select>
            </div>
            <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" class="mg_sch_input" placeholder="검색어 입력" maxlength="20">
            <button type="submit" class="mg_sch_btn"><i class="fa fa-search"></i></button>
        </div>
    </form>
</div>

<!-- 갤러리 그리드 -->
<ul class="mg_grid">
<?php if ($list_count == 0): ?>
    <li class="mg_empty">등록된 게시물이 없습니다.</li>
<?php endif; ?>
<?php for ($i = 0; $i < $list_count; $i++):
    $item  = $list[$i];
    $href  = $item['href'];
    $thumb = $item['is_notice'] ? null : get_list_thumbnail($bo_table, $item['wr_id'], $thumb_w, $thumb_h, false, true);
?>
<li class="mg_item<?php echo ($item['is_notice'] ? ' mg_item_notice' : '') ?>">
    <?php if ($is_checkbox): ?>
    <span class="mg_chk">
        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $item['wr_id'] ?>" id="mg_chk_<?php echo $i ?>">
        <label for="mg_chk_<?php echo $i ?>"><span class="sound_only"><?php echo $item['subject'] ?></span></label>
    </span>
    <?php endif; ?>
    <!-- 썸네일만 <a>로 감쌈 — sv_use 중첩앵커 방지 -->
    <a href="<?php echo $href ?>" class="mg_thumb_link">
        <span class="mg_thumb">
            <?php if ($item['is_notice']): ?>
            <span class="mg_notice_cover"><span class="mg_badge mg_badge_notice">공지</span></span>
            <?php elseif ($thumb && $thumb['src']): ?>
            <img src="<?php echo $thumb['src'] ?>" alt="<?php echo htmlspecialchars($thumb['alt'], ENT_QUOTES) ?>" loading="lazy">
            <?php else: ?>
            <span class="mg_no_img"><i class="fa fa-image"></i></span>
            <?php endif; ?>
            <?php if ($item['icon_new']): ?><span class="mg_badge_new">N</span><?php endif; ?>
            <?php if ($item['icon_secret']): ?><span class="mg_badge_lock"><i class="fa fa-lock"></i></span><?php endif; ?>
        </span>
    </a>
    <!-- 정보 영역: <a> 바깥에 위치해야 sv_use 사이드뷰 정상 동작 -->
    <div class="mg_info">
        <?php if ($is_category && $item['ca_name']): ?>
        <span class="mg_badge mg_badge_cate"><?php echo $item['ca_name'] ?></span>
        <?php endif; ?>
        <a href="<?php echo $href ?>" class="mg_tit_link">
            <?php echo $item['subject'] ?>
            <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
        </a>
        <span class="mg_meta">
            <span class="bsk_wz_author">
                <span class="profile_img bsk_wz_pf"><?php echo get_member_profile_img($item['mb_id'] ?? '', 24, 24, get_text($item['wr_name'] ?? '')); ?></span>
                <span class="sv_use"><?php echo $item['name'] ?></span>
            </span>
            <span class="mg_date"><?php echo $item['datetime2'] ?></span>
        </span>
    </div>
</li>
<?php endfor; ?>
</ul>

<div class="pg_wrap mg_pg_wrap"><?php echo $write_pages ?></div>

</form>

<?php if ($write_href): ?>
<div class="mg_fab_wrap">
    <a href="<?php echo $write_href ?>" class="mg_fab" title="글쓰기"><i class="fa fa-pencil"></i></a>
</div>
<?php endif; ?>

</div>

<?php if ($is_checkbox): ?>
<noscript><p>자바스크립트를 사용하지 않는 경우 별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p></noscript>
<script>
function all_checked(sw) {
    var f = document.fboardlist;
    for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == 'chk_wr_id[]') f.elements[i].checked = sw;
    }
}
function fboardlist_submit(f) {
    var chk_count = 0;
    for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == 'chk_wr_id[]' && f.elements[i].checked) chk_count++;
    }
    if (!chk_count) { alert(document.pressed + '할 게시물을 하나 이상 선택하세요.'); return false; }
    if (document.pressed == '선택복사') { select_copy('copy'); return; }
    if (document.pressed == '선택이동') { select_copy('move'); return; }
    if (document.pressed == '선택삭제') {
        if (!confirm('선택한 게시물을 정말 삭제하시겠습니까?')) return false;
        f.removeAttribute('target');
        f.action = g5_bbs_url + '/board_list_update.php';
    }
    return true;
}
function select_copy(sw) {
    var f = document.fboardlist;
    window.open('', 'move', 'left=50,top=50,width=500,height=550,scrollbars=1');
    f.sw.value = sw;
    f.target = 'move';
    f.action = g5_bbs_url + '/move.php';
    f.submit();
}
</script>
<script>
jQuery(function($) {
    $('.mg_more_btn').on('click', function(e) {
        e.stopPropagation();
        $('.mg_more_menu').toggle();
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.mg_more_wrap').length) $('.mg_more_menu').hide();
    });
});
</script>
<?php endif; ?>
