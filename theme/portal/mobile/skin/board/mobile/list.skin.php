<?php
if (!defined('_GNUBOARD_')) exit;

$colspan = 3;
if ($is_checkbox) $colspan++;

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>
<style>
/* ── 인라인 강제 적용: add_stylesheet 타이밍/캐시 우회 ── */
#bo_list .mb_card_list {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 10px !important;
    margin: 14px 12px 0 !important;
    padding: 0 !important;
    list-style: none !important;
    background: transparent !important;
    border: none !important;
}
#bo_list li.mb_card {
    display: flex !important;
    flex-direction: column !important;
    padding: 16px !important;
    min-height: 150px !important;
    background: #fff !important;
    border: 1px solid #efefef !important;
    border-radius: 10px !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
    margin: 0 !important;
}
#bo_list li.mb_card .mb_card_link {
    display: block !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #1a1a1a !important;
    line-height: 1.55 !important;
    flex: 1 !important;
    margin-bottom: 10px !important;
    overflow: hidden !important;
}
#bo_list li.mb_card .mb_card_meta {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    margin-top: auto !important;
    font-size: 11px !important;
    color: #999 !important;
}
</style>

<!-- 모바일 게시판 목록 시작 { -->
<div id="bo_list" class="mb_board" style="width:<?php echo $width; ?>">

    <?php if ($is_category) { ?>
    <nav id="bo_cate" class="mb_cate_nav">
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
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

    <!-- 상단: 총건수 + 관리옵션 -->
    <div class="mb_list_top">
        <span class="mb_total">전체 <strong><?php echo number_format($total_count) ?></strong>건</span>
        <div class="mb_top_right">
            <?php if ($admin_href) { ?><a href="<?php echo $admin_href ?>" class="mb_btn_icon" title="관리자"><i class="fa fa-cog"></i></a><?php } ?>
            <?php if ($rss_href) { ?><a href="<?php echo $rss_href ?>" class="mb_btn_icon" title="RSS"><i class="fa fa-rss"></i></a><?php } ?>
            <?php if ($is_admin == 'super' || $is_auth) { ?>
            <div class="mb_more_wrap">
                <button type="button" class="mb_btn_icon mb_more_btn"><i class="fa fa-ellipsis-v"></i></button>
                <?php if ($is_checkbox) { ?>
                <ul class="mb_more_menu" style="display:none">
                    <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"><i class="fa fa-trash-o"></i> 선택삭제</button></li>
                    <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"><i class="fa fa-files-o"></i> 선택복사</button></li>
                    <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"><i class="fa fa-arrows"></i> 선택이동</button></li>
                </ul>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- 검색 -->
    <div class="mb_search_bar">
        <form name="fsearch_top" class="mb_sch_form" method="get">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sca"      value="<?php echo $sca ?>">
            <input type="hidden" name="sop"      value="and">
            <div class="mb_sch_inner">
                <div class="mb_sch_select_wrap">
                    <select name="sfl" class="mb_sch_select"><?php echo get_board_sfl_select_options($sfl); ?></select>
                </div>
                <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" class="mb_sch_input" placeholder="검색어 입력" maxlength="20">
                <button type="submit" class="mb_sch_btn"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>

    <!-- 카드 목록 -->
    <ul class="mb_card_list">
    <?php if (count($list) == 0): ?>
        <li class="mb_empty">등록된 게시물이 없습니다.</li>
    <?php endif; ?>
    <?php for ($i = 0; $i < count($list); $i++):
        $is_notice = $list[$i]['is_notice'];
    ?>
    <li class="mb_card<?php echo $is_notice ? ' mb_card_notice' : '' ?>">
        <?php if ($is_checkbox): ?>
        <div class="mb_card_chk chk_box">
            <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="selec_chk">
            <label for="chk_wr_id_<?php echo $i ?>"><span></span></label>
        </div>
        <?php endif; ?>
        <div class="mb_card_top">
            <?php if ($is_notice): ?>
            <span class="mb_badge mb_badge_notice">공지</span>
            <?php elseif ($is_category && $list[$i]['ca_name']): ?>
            <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="mb_badge mb_badge_cate"><?php echo $list[$i]['ca_name'] ?></a>
            <?php else: ?>
            <span class="mb_card_top_empty"></span>
            <?php endif; ?>
            <?php if ($list[$i]['icon_new']) echo '<span class="mb_badge_new">N</span>'; ?>
        </div>
        <!-- 윗줄: 제목 -->
        <a href="<?php echo $list[$i]['href'] ?>" class="mb_card_link">
            <?php echo $list[$i]['icon_reply'] ?>
            <?php if (isset($list[$i]['icon_secret'])) echo rtrim($list[$i]['icon_secret']); ?>
            <?php echo $list[$i]['subject'] ?>
            <?php echo moidam_cmt_badge($list[$i]['wr_comment'] ?? 0); ?>
        </a>
        <!-- 아랫줄: 작성 정보 -->
        <div class="mb_card_meta">
            <span class="mb_card_author sv_use"><?php echo $list[$i]['name'] ?></span>
            <span class="mb_card_date"><?php echo $list[$i]['datetime2'] ?></span>
        </div>
    </li>
    <?php endfor; ?>
    </ul>

    <!-- 페이지네이션 -->
    <div class="pg_wrap mb_pg_wrap"><?php echo $write_pages; ?></div>

    </form>

    <!-- 글쓰기 버튼 -->
    <?php if ($write_href): ?>
    <div class="mb_fab_wrap">
        <a href="<?php echo $write_href ?>" class="mb_fab" title="글쓰기">
            <i class="fa fa-pencil"></i>
        </a>
    </div>
    <?php endif; ?>

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
    if (document.pressed == "선택복사") { select_copy("copy"); return; }
    if (document.pressed == "선택이동") { select_copy("move"); return; }
    if (document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.")) return false;
        f.removeAttribute("target");
        f.action = g5_bbs_url + "/board_list_update.php";
    }
    return true;
}
function select_copy(sw) {
    var f = document.fboardlist;
    var sub_win = window.open("", "move", "left=50,top=50,width=500,height=550,scrollbars=1");
    f.sw.value = sw;
    f.target = "move";
    f.action = g5_bbs_url + "/move.php";
    f.submit();
}
</script>
<script>
jQuery(function($) {
    $(".mb_more_btn").on("click", function(e) {
        e.stopPropagation();
        $(".mb_more_menu").toggle();
    });
    $(document).on("click", function(e) {
        if (!$(e.target).closest('.mb_more_wrap').length) $(".mb_more_menu").hide();
    });
});
</script>
<?php endif; ?>
<!-- } 모바일 게시판 목록 끝 -->
