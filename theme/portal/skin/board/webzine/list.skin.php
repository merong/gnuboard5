<?php if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
$list_count = count($list);
$thumb_w    = (int)($board['bo_gallery_width']  ?: 640);
$thumb_h    = (int)($board['bo_gallery_height'] ?: 400);
$thumb_sw   = 140;
$thumb_sh   = 100;
if (!function_exists('moidam_wz_excerpt')) {
    function moidam_wz_excerpt($content, $len = 40) {
        $text = html_entity_decode(strip_tags((string)$content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        return cut_str(trim($text), $len, '…');
    }
}
?>
<div id="bsk_wz_wrap">

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
<div class="bsk_wz_allchk">
    <input type="checkbox" id="chkall" onclick="if(this.checked) all_checked(true); else all_checked(false);">
    <label for="chkall">전체선택</label>
</div>
<?php } ?>

<?php if ($list_count == 0) { ?>
<p class="bsk_empty bsk_wz_empty">게시물이 없습니다.</p>
<?php } else { ?>

<?php
// Separate notices and normal posts
$notice_items = [];
$normal_items = [];
foreach ($list as $item) {
    if ($item['is_notice']) $notice_items[] = $item;
    else $normal_items[] = $item;
}
?>

<?php if (count($notice_items) > 0) { ?>
<ul class="bsk_wz_notices">
<?php foreach ($notice_items as $item) { ?>
<li class="bsk_wz_notice_item">
    <?php if ($is_checkbox) { ?>
    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $item['wr_id'] ?>">
    <?php } ?>
    <em class="bsk_badge bsk_badge_notice">공지</em>
    <a href="<?php echo $item['href'] ?>" class="bsk_wz_notice_link"><?php echo $item['subject'] ?></a>
    <span class="bsk_wz_notice_date"><?php echo $item['datetime2'] ?></span>
</li>
<?php } ?>
</ul>
<?php } ?>

<?php if (count($normal_items) > 0) { ?>
<ul class="bsk_wz_grid bsk_wz_rows">
<?php foreach ($normal_items as $idx => $item) {
    $thumb = get_list_thumbnail($bo_table, $item['wr_id'], $thumb_sw, $thumb_sh, false, true);
    $has_img = !empty($thumb['src']);
?>
<li class="bsk_wz_item<?php echo ($wr_id == $item['wr_id']) ? ' bsk_current_item' : ''; echo $has_img ? '' : ' bsk_wz_item--text'; ?>">
    <?php if ($is_checkbox) { ?>
    <span class="bsk_wz_chk">
        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $item['wr_id'] ?>" id="chk_wr_id_<?php echo $idx ?>">
        <label for="chk_wr_id_<?php echo $idx ?>"><span class="sound_only"><?php echo $item['subject'] ?></span></label>
    </span>
    <?php } ?>
    <?php if ($has_img) { ?>
    <a href="<?php echo $item['href'] ?>" class="bsk_wz_item_thumb_link">
        <span class="bsk_wz_item_thumb">
            <img src="<?php echo $thumb['src'] ?>" alt="<?php echo htmlspecialchars($thumb['alt'], ENT_QUOTES) ?>">
            <?php if ($item['icon_new'])    { ?><span class="bsk_gall_badge_new">N</span><?php } ?>
            <?php if ($item['icon_secret']) { ?><span class="bsk_gall_badge_lock"><i class="fa fa-lock"></i></span><?php } ?>
        </span>
    </a>
    <?php } ?>
    <div class="bsk_wz_item_info">
        <a href="<?php echo $item['href'] ?>" class="bsk_wz_item_tit_link">
            <p class="bsk_wz_item_tit"><span class="bsk_wz_item_tit_txt"><?php echo $item['subject'] ?></span><?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?></p>
        </a>
        <p class="bsk_wz_item_excerpt"><?php echo get_text(moidam_wz_excerpt($item['wr_content'] ?? '', 40)); ?></p>
        <div class="bsk_wz_item_meta">
            <span class="bsk_wz_author">
                <span class="profile_img bsk_wz_pf"><?php echo get_member_profile_img($item['mb_id'] ?? '', 24, 24, get_text($item['wr_name'] ?? '')); ?></span>
                <span class="bsk_wz_nick"><?php echo get_text($item['wr_name'] ?? strip_tags($item['name'] ?? '')); ?></span>
            </span>
            <span><?php echo $item['datetime2'] ?></span>
        </div>
    </div>
</li>
<?php } ?>
</ul>
<?php } ?>
<?php } ?>

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
