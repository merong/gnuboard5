<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);
$list_count = count($list);
if (!function_exists('moidam_wz_excerpt')) {
    function moidam_wz_excerpt($content, $len = 40) {
        $text = html_entity_decode(strip_tags((string)$content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        return cut_str(trim($text), $len, '…');
    }
}
?>
<div id="bo_list" class="mb_board mb_board_wz">

<?php if ($is_category) { ?>
<nav id="bo_cate" class="mb_cate_nav"><ul id="bo_cate_ul"><?php echo $category_option ?></ul></nav>
<?php } ?>

<form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="spt" value="<?php echo $spt ?>">
<input type="hidden" name="sca" value="<?php echo $sca ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="sw" value="">

<div class="mb_list_top">
    <span class="mb_total">전체 <strong><?php echo number_format($total_count) ?></strong>건</span>
    <div class="mb_top_right">
        <?php if ($admin_href) { ?><a href="<?php echo $admin_href ?>" class="mb_btn_icon"><i class="fa fa-cog"></i></a><?php } ?>
        <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="mb_btn_write">글쓰기</a><?php } ?>
    </div>
</div>

<ul class="mwz_list">
<?php if ($list_count == 0) { ?>
    <li class="mb_empty">등록된 게시물이 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item = $list[$i];
    if (!empty($item['is_notice'])) continue;
    $thumb = get_list_thumbnail($bo_table, $item['wr_id'], 140, 100, false, true);
    $has_img = !empty($thumb['src']);
    $nick = get_text($item['wr_name'] ?? '');
    $excerpt = moidam_wz_excerpt($item['wr_content'] ?? '', 70);
?>
<li class="mwz_item<?php echo $has_img ? '' : ' mwz_item--text'; ?>">
    <?php if ($has_img) { ?>
    <a href="<?php echo $item['href'] ?>" class="mwz_thumb">
        <img src="<?php echo $thumb['src'] ?>" alt="">
    </a>
    <?php } ?>
    <div class="mwz_info">
        <a href="<?php echo $item['href'] ?>" class="mwz_tit">
            <span class="mwz_tit_txt"><?php echo $item['subject'] ?></span>
            <?php echo moidam_cmt_badge($item['wr_comment'] ?? 0); ?>
        </a>
        <?php if ($excerpt) { ?><p class="mwz_excerpt"><?php echo get_text($excerpt) ?></p><?php } ?>
        <div class="mwz_meta">
            <span class="bsk_wz_author">
                <span class="profile_img bsk_wz_pf"><?php echo get_member_profile_img($item['mb_id'] ?? '', 24, 24, $nick); ?></span>
                <span class="bsk_wz_nick"><?php echo $nick ?></span>
            </span>
            <span><?php echo $item['datetime2'] ?></span>
        </div>
    </div>
</li>
<?php } ?>
</ul>

<div class="pg_wrap mb_pg_wrap"><?php echo $write_pages; ?></div>
</form>
</div>

<?php if ($is_checkbox): ?>
<noscript><p>자바스크립트를 사용하지 않는 경우 별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p></noscript>
<script>
function all_checked(sw) {
    var f = document.fboardlist;
    for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]") f.elements[i].checked = sw;
    }
}
function fboardlist_submit(f) {
    var chk_count = 0;
    for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked) chk_count++;
    }
    if (!chk_count) { alert(document.pressed + "할 게시물을 하나 이상 선택하세요."); return false; }
    if (document.pressed == "선택복사" || document.pressed == "선택이동") { window.parent.board_move = f; }
    if (document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.")) return false;
        f.removeAttribute("target");
        f.action = g5_bbs_url + "/board_list_update.php";
    }
    return true;
}
jQuery(function($){
    $(".mb_more_btn").on("click", function(e){ e.stopPropagation(); $(this).siblings(".mb_more_menu").toggle(); });
    $(document).on("click", function(){ $(".mb_more_menu").hide(); });
});
</script>
<?php endif; ?>
