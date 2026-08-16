<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);
$list_count = count($list);
?>
<?php include_once(G5_THEME_PATH.'/skin/board/_popular_tabs.php'); ?>

<div id="bo_list" class="mb_board mb_board_list">

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
        <?php if ($admin_href) { ?><a href="<?php echo $admin_href ?>" class="mb_btn_icon" title="관리자"><i class="fa fa-cog"></i></a><?php } ?>
        <?php if ($is_admin == 'super' || $is_auth) { ?>
        <div class="mb_more_wrap">
            <button type="button" class="mb_btn_icon mb_more_btn"><i class="fa fa-ellipsis-v"></i></button>
            <?php if ($is_checkbox) { ?>
            <ul class="mb_more_menu" style="display:none">
                <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button></li>
                <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value">선택복사</button></li>
                <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value">선택이동</button></li>
            </ul>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>

<div class="mb_search_bar">
    <form name="fsearch_top" class="mb_sch_form" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sca" value="<?php echo $sca ?>">
        <input type="hidden" name="sop" value="and">
        <div class="mb_sch_inner">
            <select name="sfl" class="mb_sch_select"><?php echo get_board_sfl_select_options($sfl); ?></select>
            <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" class="mb_sch_input" placeholder="검색어 입력" maxlength="20">
            <button type="submit" class="mb_sch_btn"><i class="fa fa-search"></i></button>
        </div>
    </form>
</div>

<ul class="mb_tit_list">
<?php if ($list_count == 0) { ?>
    <li class="mb_empty">등록된 게시물이 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item = $list[$i];
    $nick = get_text($item['wr_name'] ?? strip_tags($item['name'] ?? ''));
?>
<li class="mb_tit_item<?php echo $item['is_notice'] ? ' is-notice' : ''; ?>">
    <?php if ($is_checkbox) { ?>
    <span class="mb_chk">
        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $item['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
        <label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $item['subject'] ?></span></label>
    </span>
    <?php } ?>
    <a href="<?php echo $item['href'] ?>" class="mb_tit_link">
        <span class="mb_tit_txt">
            <?php if ($item['is_notice']) { ?><em class="mb_badge notice">공지</em><?php } ?>
            <?php if ($item['icon_hot']) { ?><em class="mb_badge hot">인기</em><?php } ?>
            <?php echo $item['subject'] ?>
        </span>
        <?php echo moidam_cmt_badge($item['wr_comment'] ?? 0); ?>
        <?php if ($item['icon_new'] && !$item['is_notice']) { ?><span class="mb_dot_new"></span><?php } ?>
    </a>
    <div class="mb_tit_meta">
        <span class="bsk_wz_author">
            <span class="profile_img bsk_wz_pf"><?php echo get_member_profile_img($item['mb_id'] ?? '', 24, 24, $nick); ?></span>
            <span class="bsk_wz_nick"><?php echo $nick ?></span>
        </span>
        <span class="mb_date"><?php echo $item['datetime2'] ?></span>
    </div>
</li>
<?php } ?>
</ul>

<div class="pg_wrap mb_pg_wrap"><?php echo $write_pages; ?></div>
</form>

<?php if ($write_href) { ?>
<div class="mb_fab_wrap"><a href="<?php echo $write_href ?>" class="mb_fab" title="글쓰기"><i class="fa fa-pencil"></i></a></div>
<?php } ?>
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
