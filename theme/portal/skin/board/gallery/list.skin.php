<?php if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
$list_count = count($list);
$cols       = max(2, (int)($bo_gallery_cols ?: 4));
$thumb_w    = (int)($board['bo_gallery_width']  ?: 240);
$thumb_h    = (int)($board['bo_gallery_height'] ?: 160);
?>
<div id="bsk_gall_wrap">

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
        <?php if ($admin_href) { ?><a href="<?php echo $admin_href ?>" class="bsk_btn bsk_btn_admin"><i class="fa fa-cog"></i></a><?php } ?>
        <button type="button" class="bsk_btn bsk_btn_icon bsk_search_toggle"><i class="fa fa-search"></i></button>
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

<?php if ($is_checkbox) { ?>
<div class="bsk_gall_allchk">
    <input type="checkbox" id="chkall" onclick="if(this.checked) all_checked(true); else all_checked(false);">
    <label for="chkall">전체선택</label>
</div>
<?php } ?>

<ul class="bsk_gall_grid bsk_gall_cols_<?php echo $cols ?>">
<?php if ($list_count == 0) { ?>
    <li class="bsk_empty" style="grid-column:1/-1">게시물이 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item  = $list[$i];
    $href  = $item['href'];
    $thumb = $item['is_notice'] ? null : get_list_thumbnail($bo_table, $item['wr_id'], $thumb_w, $thumb_h, false, true);
?>
<li class="bsk_gall_item<?php echo ($wr_id == $item['wr_id']) ? ' bsk_current_item' : '' ?>">
    <?php if ($is_checkbox) { ?>
    <span class="bsk_gall_chk">
        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $item['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
        <label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $item['subject'] ?></span></label>
    </span>
    <?php } ?>
    <!-- 썸네일만 <a>로 감쌈 — sv_use 중첩앵커 방지 -->
    <a href="<?php echo $href ?>" class="bsk_gall_link">
        <span class="bsk_gall_thumb">
            <?php if ($item['is_notice']) { ?>
            <span class="bsk_gall_notice_cover"><em class="bsk_badge bsk_badge_notice">공지</em></span>
            <?php } elseif ($thumb && $thumb['src']) { ?>
            <img src="<?php echo $thumb['src'] ?>" alt="<?php echo htmlspecialchars($thumb['alt'], ENT_QUOTES) ?>">
            <?php } else { ?>
            <span class="bsk_gall_no_img"><i class="fa fa-image"></i></span>
            <?php } ?>
            <?php if ($item['icon_new'])  { ?><span class="bsk_gall_badge_new">N</span><?php } ?>
            <?php if ($item['icon_secret']) { ?><span class="bsk_gall_badge_lock"><i class="fa fa-lock"></i></span><?php } ?>
        </span>
    </a>
    <!-- 정보 영역: <a> 바깥에 위치해야 sv_use 사이드뷰 정상 동작 -->
    <div class="bsk_gall_info">
        <?php if ($is_category && $item['ca_name']) { ?>
        <span class="bsk_badge bsk_badge_cate"><?php echo $item['ca_name'] ?></span>
        <?php } ?>
        <a href="<?php echo $href ?>" class="bsk_gall_tit_link">
            <span class="bsk_gall_tit"><?php echo $item['subject'] ?>
                <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
            </span>
        </a>
        <span class="bsk_gall_meta">
            <span class="sv_use"><?php echo $item['name'] ?></span>
            <span class="bsk_meta_sep">·</span>
            <span><?php echo $item['datetime2'] ?></span>
            <span class="bsk_meta_sep">·</span>
            <span><i class="fa fa-eye"></i> <?php echo number_format($item['wr_hit']) ?></span>
        </span>
    </div>
</li>
<?php } ?>
</ul>

<div class="bsk_pager"><?php echo $write_pages ?></div>

<?php if ($write_href) { ?>
<div class="bsk_toolbar bsk_toolbar_bottom">
    <div class="bsk_btn_group">
        <a href="<?php echo $write_href ?>" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a>
    </div>
</div>
<?php } ?>
</form>

<div class="bsk_search_wrap" style="display:none">
    <div class="bsk_search_overlay"></div>
    <div class="bsk_search_box">
        <form name="fsearch" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sca"      value="<?php echo $sca ?>">
        <input type="hidden" name="sop"      value="and">
        <select name="sfl" class="bsk_select bsk_select_sm"><?php echo get_board_sfl_select_options($sfl) ?></select>
        <div class="bsk_search_input_row">
            <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required placeholder="검색어 입력" class="bsk_input bsk_search_input">
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
<script>
function all_checked(sw) { var f = document.fboardlist; for (var i=0; i<f.length; i++) { if (f.elements[i].name=='chk_wr_id[]') f.elements[i].checked=sw; } }
function fboardlist_submit(f) {
    var chk=0; for (var i=0; i<f.length; i++) { if (f.elements[i].name=='chk_wr_id[]' && f.elements[i].checked) chk++; }
    if (!chk) { alert(document.pressed+'할 게시물을 하나 이상 선택하세요.'); return false; }
    if (document.pressed=='선택복사') { select_copy('copy'); return; }
    if (document.pressed=='선택이동') { select_copy('move'); return; }
    if (document.pressed=='선택삭제') { if (!confirm('선택한 게시물을 정말 삭제하시겠습니까?')) return false; f.removeAttribute('target'); f.action=g5_bbs_url+'/board_list_update.php'; }
    return true;
}
function select_copy(sw) { var f=document.fboardlist; window.open('','move','left=50,top=50,width=500,height=550,scrollbars=1'); f.sw.value=sw; f.target='move'; f.action=g5_bbs_url+'/move.php'; f.submit(); }
</script>
<?php } ?>
