<?php
if (!defined('_GNUBOARD_')) exit;

$colspan = 5;
if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);
?>

<!-- 게시판 목록 시작 { -->
<div id="bo_list" class="cafe_board" style="width:<?php echo $width; ?>">

    <?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo $board['bo_subject'] ?> 카테고리</h2>
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

    <!-- 상단 버튼 영역 -->
    <div id="bo_btn_top">
        <div id="bo_list_total">
            <span>Total <?php echo number_format($total_count) ?>건</span>
            <?php echo $page ?> 페이지
        </div>
        <ul class="btn_bo_user">
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn" title="관리자"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only">관리자</span></a></li><?php } ?>
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01 btn" title="RSS"><i class="fa fa-rss" aria-hidden="true"></i><span class="sound_only">RSS</span></a></li><?php } ?>
            <?php if ($is_admin == 'super' || $is_auth) { ?>
            <li>
                <button type="button" class="btn_more_opt is_list_btn btn" title="게시판 관리 옵션"><i class="fa fa-ellipsis-v" aria-hidden="true"></i><span class="sound_only">게시판 관리 옵션</span></button>
                <?php if ($is_checkbox) { ?>
                <ul class="more_opt is_list_btn" style="display:none">
                    <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"><i class="fa fa-trash-o" aria-hidden="true"></i> 선택삭제</button></li>
                    <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"><i class="fa fa-files-o" aria-hidden="true"></i> 선택복사</button></li>
                    <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"><i class="fa fa-arrows" aria-hidden="true"></i> 선택이동</button></li>
                </ul>
                <?php } ?>
            </li>
            <?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="cafe_btn_write_top"><i class="fa fa-pencil"></i> 글쓰기</a></li><?php } ?>
        </ul>
    </div>

    <?php include_once(G5_THEME_PATH.'/skin/board/_popular_tabs.php'); ?>

    <!-- 상단 검색 -->
    <div class="cafe_sch_top">
        <form name="fsearch_top" class="cafe_sch_form" method="get">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sca"      value="<?php echo $sca ?>">
            <input type="hidden" name="sop"      value="and">
            <select name="sfl" class="cafe_sch_select">
                <?php echo get_board_sfl_select_options($sfl); ?>
            </select>
            <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" class="cafe_sch_input" placeholder="검색어를 입력하세요" maxlength="20">
            <button type="submit" class="cafe_btn_sch"><i class="fa fa-search"></i><span class="sound_only">검색</span></button>
        </form>
    </div>

    <div class="tbl_head01 tbl_wrap board_list">
        <table>
        <caption><?php echo $board['bo_subject'] ?> 목록</caption>
        <thead>
        <tr>
            <?php if ($is_checkbox) { ?>
            <th scope="col" class="all_chk chk_box">
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);" class="selec_chk">
                <label for="chkall"><span></span><b class="sound_only">전체선택</b></label>
            </th>
            <?php } ?>
            <th scope="col" class="td_num2">번호</th>
            <th scope="col">제목</th>
            <th scope="col" class="td_name">글쓴이</th>
            <th scope="col" class="td_num"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>조회 </a></th>
            <?php if ($is_good) { ?><th scope="col" class="td_num"><?php echo subject_sort_link('wr_good', $qstr2, 1) ?>추천 </a></th><?php } ?>
            <?php if ($is_nogood) { ?><th scope="col" class="td_num"><?php echo subject_sort_link('wr_nogood', $qstr2, 1) ?>비추천 </a></th><?php } ?>
            <th scope="col" class="td_datetime"><?php echo subject_sort_link('wr_datetime', $qstr2, 1) ?>날짜 </a></th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i = 0; $i < count($list); $i++) {
            $lt_class = ($i % 2 == 0) ? 'even' : '';
        ?>
        <tr class="<?php if ($list[$i]['is_notice']) echo 'bo_notice'; ?> <?php echo $lt_class ?>">
            <?php if ($is_checkbox) { ?>
            <td class="td_chk chk_box">
                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="selec_chk">
                <label for="chk_wr_id_<?php echo $i ?>"><span></span><b class="sound_only"><?php echo $list[$i]['subject'] ?></b></label>
            </td>
            <?php } ?>

            <td class="td_num2">
            <?php
            if ($list[$i]['is_notice'])
                echo '<span class="cafe_notice_badge">공지</span>';
            else if ($wr_id == $list[$i]['wr_id'])
                echo '<span class="bo_current">열람중</span>';
            else
                echo $list[$i]['num'];
            ?>
            </td>

            <td class="td_subject" style="padding-left:<?php echo $list[$i]['reply'] ? (strlen($list[$i]['wr_reply'])*10) : '0'; ?>px">
                <?php if ($is_category && $list[$i]['ca_name']) { ?>
                <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></a>
                <?php } ?>
                <div class="bo_tit">
                    <a href="<?php echo $list[$i]['href'] ?>">
                        <?php echo $list[$i]['icon_reply'] ?>
                        <?php if (isset($list[$i]['icon_secret'])) echo rtrim($list[$i]['icon_secret']); ?>
                        <?php echo $list[$i]['subject'] ?>
                    </a>
                    <?php if ($list[$i]['icon_new']) echo '<span class="icon_new">N<span class="sound_only">새글</span></span>'; ?>
                    <?php if (isset($list[$i]['icon_hot'])) echo rtrim($list[$i]['icon_hot']); ?>
                    <?php if (isset($list[$i]['icon_file'])) echo rtrim($list[$i]['icon_file']); ?>
                    <?php if (isset($list[$i]['icon_link'])) echo rtrim($list[$i]['icon_link']); ?>
                    <?php echo moidam_cmt_badge($list[$i]['wr_comment'] ?? 0); ?>
                </div>
            </td>

            <td class="td_name sv_use"><?php echo $list[$i]['name'] ?></td>
            <td class="td_num"><?php echo $list[$i]['wr_hit'] ?></td>
            <?php if ($is_good) { ?><td class="td_num"><?php echo $list[$i]['wr_good'] ?></td><?php } ?>
            <?php if ($is_nogood) { ?><td class="td_num"><?php echo $list[$i]['wr_nogood'] ?></td><?php } ?>
            <td class="td_datetime"><?php echo $list[$i]['datetime2'] ?></td>
        </tr>
        <?php } ?>
        <?php if (count($list) == 0) echo '<tr><td colspan="'.$colspan.'" class="empty_table">게시물이 없습니다.</td></tr>'; ?>
        </tbody>
        </table>
    </div>

    <!-- 페이지 -->
    <div class="pg_wrap">
        <?php echo $write_pages; ?>
    </div>
    <!-- 페이지 -->

    <!-- 하단 글쓰기 -->
    <?php if ($write_href) { ?>
    <div class="cafe_bottom">
        <a href="<?php echo $write_href ?>" class="cafe_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a>
    </div>
    <?php } ?>

    </form>
</div>

<?php if ($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<script>
function all_checked(sw) {
    var f = document.fboardlist;
    for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;
    for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }
    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }
    if (document.pressed == "선택복사") { select_copy("copy"); return; }
    if (document.pressed == "선택이동") { select_copy("move"); return; }
    if (document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다."))
            return false;
        f.removeAttribute("target");
        f.action = g5_bbs_url + "/board_list_update.php";
    }
    return true;
}

function select_copy(sw) {
    var f = document.fboardlist;
    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");
    f.sw.value = sw;
    f.target = "move";
    f.action = g5_bbs_url + "/move.php";
    f.submit();
}
</script>
<script>
jQuery(function($) {
    $(".btn_more_opt.is_list_btn").on("click", function(e) {
        e.stopPropagation();
        $(".more_opt.is_list_btn").toggle();
    });
    $(document).on("click", function(e) {
        if (!$(e.target).closest('.is_list_btn').length)
            $(".more_opt.is_list_btn").hide();
    });
});
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
