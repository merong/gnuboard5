<?php if (!defined('_GNUBOARD_')) exit;
$list_count = count($list);
?>
<div id="bsk_list_wrap">

<?php if ($is_category) { ?>
<nav class="bsk_cate_nav"><ul><?php echo $category_option ?></ul></nav>
<?php } ?>

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

<div class="bsk_toolbar">
    <span class="bsk_total">전체 <strong><?php echo number_format($total_count) ?></strong>건</span>
    <div class="bsk_btn_group">
        <?php if ($admin_href)                   { ?><a href="<?php echo $admin_href ?>" class="bsk_btn bsk_btn_admin"><i class="fa fa-cog"></i></a><?php } ?>
        <?php if ($rss_href)                     { ?><a href="<?php echo $rss_href ?>"   class="bsk_btn bsk_btn_icon" title="RSS"><i class="fa fa-rss"></i></a><?php } ?>
        <button type="button" class="bsk_btn bsk_btn_icon bsk_search_toggle" title="검색"><i class="fa fa-search"></i></button>
        <?php if ($is_admin == 'super' || $is_auth) { ?>
        <button type="button" class="bsk_btn bsk_btn_icon bsk_admin_opt_toggle"><i class="fa fa-ellipsis-v"></i></button>
        <?php if ($is_checkbox) { ?>
        <ul class="bsk_admin_opt_menu" style="display:none">
            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button></li>
            <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value">선택복사</button></li>
            <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value">선택이동</button></li>
        </ul>
        <?php } ?>
        <?php } ?>
        <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a><?php } ?>
    </div>
</div>

<!-- 목록 헤더 -->
<div class="bsk_list_header">
    <?php if ($is_checkbox) { ?>
    <span class="bsk_list_col_chk">
        <input type="checkbox" id="chkall" onclick="if(this.checked) all_checked(true); else all_checked(false);">
        <label for="chkall"><span class="sound_only">전체선택</span></label>
    </span>
    <?php } ?>
    <span class="bsk_list_col_num">번호</span>
    <span class="bsk_list_col_subj">제목</span>
    <span class="bsk_list_col_meta">
        <span class="bsk_list_col_author">글쓴이</span>
        <span class="bsk_list_col_date"><?php echo subject_sort_link('wr_datetime', $qstr2, 1) ?>날짜</a></span>
        <span class="bsk_list_col_hit"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>조회</a></span>
    </span>
</div>

<ul class="bsk_list">
<?php if ($list_count == 0) { ?>
    <li class="bsk_empty">게시물이 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item   = $list[$i];
    $indent = $item['reply'] ? (strlen($item['wr_reply']) * 12) : 0;
?>
<li class="bsk_list_item<?php echo $item['is_notice'] ? ' bsk_notice_item' : '' ?><?php echo ($wr_id == $item['wr_id']) ? ' bsk_current_item' : '' ?>">
    <?php if ($is_checkbox) { ?>
    <span class="bsk_list_col_chk">
        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $item['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
        <label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $item['subject'] ?></span></label>
    </span>
    <?php } ?>

    <span class="bsk_list_col_num">
        <?php if ($item['is_notice']) { ?>
            <em class="bsk_badge bsk_badge_notice">공지</em>
        <?php } elseif ($wr_id == $item['wr_id']) { ?>
            <em class="bsk_badge bsk_badge_current">열람중</em>
        <?php } else { echo $item['num']; } ?>
    </span>

    <span class="bsk_list_col_subj" style="<?php echo $indent ? "padding-left:{$indent}px;" : '' ?>">
        <?php if ($is_category && $item['ca_name']) { ?>
        <a href="<?php echo $item['ca_name_href'] ?>" class="bsk_badge bsk_badge_cate"><?php echo $item['ca_name'] ?></a>
        <?php } ?>
        <a href="<?php echo $item['href'] ?>" class="bsk_list_tit">
            <?php if ($item['icon_secret']) { ?><i class="fa fa-lock bsk_icon_lock"></i> <?php } ?>
            <?php echo $item['icon_reply'] ?>
            <?php echo $item['subject'] ?>
        </a>
        <?php if ($item['icon_new'])     { ?><span class="bsk_badge bsk_badge_new">N</span><?php } ?>
        <?php if ($item['icon_hot'])     { ?><span class="bsk_badge bsk_badge_hot">인기</span><?php } ?>
        <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
        <?php if ($item['icon_file'])    { ?><i class="fa fa-paperclip bsk_icon_file"></i><?php } ?>
    </span>

    <span class="bsk_list_col_meta">
        <span class="bsk_list_col_author sv_use"><?php echo $item['name'] ?></span>
        <span class="bsk_list_col_date"><?php echo $item['datetime2'] ?></span>
        <span class="bsk_list_col_hit"><?php echo number_format($item['wr_hit']) ?></span>
    </span>
</li>
<?php } ?>
</ul>

<div class="bsk_pager"><?php echo $write_pages ?></div>

<?php if ($write_href) { ?>
<div class="bsk_toolbar bsk_toolbar_bottom">
    <div class="bsk_btn_group">
        <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a><?php } ?>
    </div>
</div>
<?php } ?>

</form>

<!-- 검색 -->
<div class="bsk_search_wrap" style="display:none">
    <div class="bsk_search_overlay"></div>
    <div class="bsk_search_box">
        <form name="fsearch" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sca"      value="<?php echo $sca ?>">
        <input type="hidden" name="sop"      value="and">
        <select name="sfl" class="bsk_select bsk_select_sm"><?php echo get_board_sfl_select_options($sfl) ?></select>
        <div class="bsk_search_input_row">
            <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required
                   placeholder="검색어 입력" class="bsk_input bsk_search_input">
            <button type="submit" class="bsk_btn bsk_btn_submit"><i class="fa fa-search"></i></button>
        </div>
        <button type="button" class="bsk_search_close"><i class="fa fa-times"></i> 닫기</button>
        </form>
    </div>
</div>

</div>

<script>
jQuery(function($) {
    $('.bsk_search_toggle').click(function() { $('.bsk_search_wrap').toggle(); });
    $('.bsk_search_overlay, .bsk_search_close').click(function() { $('.bsk_search_wrap').hide(); });
    $('.bsk_admin_opt_toggle').click(function(e) { e.stopPropagation(); $('.bsk_admin_opt_menu').toggle(); });
    $(document).click(function(e) { if (!$(e.target).closest('.bsk_admin_opt_toggle,.bsk_admin_opt_menu').length) $('.bsk_admin_opt_menu').hide(); });
});
</script>

<?php if ($is_checkbox) { ?>
<noscript><p>자바스크립트를 사용하지 않는 경우 별도의 확인 없이 선택삭제 처리됩니다.</p></noscript>
<script>
function all_checked(sw) {
    var f = document.fboardlist;
    for (var i=0; i<f.length; i++) { if (f.elements[i].name == 'chk_wr_id[]') f.elements[i].checked = sw; }
}
function fboardlist_submit(f) {
    var chk = 0;
    for (var i=0; i<f.length; i++) { if (f.elements[i].name == 'chk_wr_id[]' && f.elements[i].checked) chk++; }
    if (!chk) { alert(document.pressed + '할 게시물을 하나 이상 선택하세요.'); return false; }
    if (document.pressed == '선택복사') { select_copy('copy'); return; }
    if (document.pressed == '선택이동') { select_copy('move'); return; }
    if (document.pressed == '선택삭제') {
        if (!confirm('선택한 게시물을 정말 삭제하시겠습니까?')) return false;
        f.removeAttribute('target'); f.action = g5_bbs_url+'/board_list_update.php';
    }
    return true;
}
function select_copy(sw) {
    var f = document.fboardlist;
    var sub = window.open('', 'move', 'left=50,top=50,width=500,height=550,scrollbars=1');
    f.sw.value = sw; f.target = 'move'; f.action = g5_bbs_url+'/move.php'; f.submit();
}
</script>
<?php } ?>
