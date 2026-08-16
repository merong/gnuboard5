<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
add_stylesheet('<link rel="stylesheet" href="'.$search_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);

if (!function_exists('moidam_sch_excerpt')) {
    function moidam_sch_excerpt($text, $len = 40) {
        $text = html_entity_decode(strip_tags((string)$text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        return cut_str(trim($text), $len, '…');
    }
}
?>
<div id="ptl_sch_wrap">

<div class="ptl_sch_form_area">
    <form name="fsearch" onsubmit="return ptl_fsearch_submit(this);" method="get">
    <input type="hidden" name="srows" value="<?php echo $srows ?>">
    <div class="ptl_sch_form_inner">
        <?php echo $group_select ?>
        <script>document.getElementById("gr_id").value = "<?php echo $gr_id ?>";</script>
        <div class="ptl_sch_row">
            <div class="ptl_sch_select_wrap">
                <select name="sfl" class="ptl_sch_select">
                    <option value="wr_subject||wr_content"<?php echo get_selected($sfl, "wr_subject||wr_content") ?>>제목+내용</option>
                    <option value="wr_subject"<?php echo get_selected($sfl, "wr_subject") ?>>제목</option>
                    <option value="wr_content"<?php echo get_selected($sfl, "wr_content") ?>>내용</option>
                    <option value="mb_id"<?php echo get_selected($sfl, "mb_id") ?>>회원아이디</option>
                    <option value="wr_name"<?php echo get_selected($sfl, "wr_name") ?>>이름</option>
                </select>
            </div>
            <input type="text" name="stx" id="stx" value="<?php echo $text_stx ?>" class="ptl_sch_input" required placeholder="검색어 입력">
            <button type="submit" class="ptl_sch_btn"><i class="fa fa-search"></i> 검색</button>
        </div>
        <div class="ptl_sch_op">
            <label class="ptl_sch_radio"><input type="radio" name="sop" value="and"<?php echo ($sop == 'and') ? ' checked' : '' ?>> AND</label>
            <label class="ptl_sch_radio"><input type="radio" name="sop" value="or"<?php echo ($sop == 'or')  ? ' checked' : '' ?>> OR</label>
        </div>
    </div>
    </form>
</div>

<div id="ptl_sch_result">
<?php if ($stx): ?>

    <?php if ($board_count): ?>
    <div class="ptl_sch_summary">
        <p class="ptl_sch_summary_l"><strong class="ptl_sch_stx">"<?php echo htmlspecialchars($stx, ENT_QUOTES) ?>"</strong> 검색 결과</p>
        <span class="ptl_sch_counts">게시판 <em><?php echo $board_count ?></em>개 · 게시물 <em><?php echo number_format($total_count) ?></em>건</span>
    </div>
    <nav class="ptl_sch_board_nav">
        <ul>
            <li><a href="?<?php echo $search_query ?>&amp;gr_id=<?php echo $gr_id ?>" <?php echo $sch_all ?>>전체</a></li>
            <?php echo $str_board_list ?>
        </ul>
    </nav>
    <?php else: ?>
    <p class="ptl_sch_empty">"<?php echo htmlspecialchars($stx, ENT_QUOTES) ?>"에 대한 검색 결과가 없습니다.</p>
    <?php endif; ?>

    <?php if ($board_count): ?>
    <div class="ptl_sch_results">
    <?php
    $k = 0;
    for ($idx = $table_index; $idx < count($search_table) && $k < $rows; $idx++):
        $bo = $search_table[$idx];
    ?>
        <section class="ptl_sch_board_section">
            <div class="ptl_sch_board_head">
                <h2 class="ptl_sch_board_title">
                    <a href="<?php echo get_pretty_url($bo, '', $search_query) ?>"><?php echo $bo_subject[$idx] ?></a>
                </h2>
                <a href="<?php echo get_pretty_url($bo, '', $search_query) ?>" class="ptl_sch_board_more">더보기</a>
            </div>
            <ul class="ptl_sch_list bsk_wz_rows">
            <?php for ($i = 0; $i < count($list[$idx]) && $k < $rows; $i++, $k++):
                $item = $list[$idx][$i];
                $is_cmt = !empty($item['wr_is_comment']);
                $thumb_id = $is_cmt ? (int)$item['wr_parent'] : (int)$item['wr_id'];
                $thumb = $is_cmt ? array() : get_list_thumbnail($bo, $thumb_id, 140, 100, false, true);
                $has_img = !empty($thumb['src']);
                $href = $item['href'].($is_cmt ? '#c_'.$item['wr_id'] : '');
                $nick = get_text($item['wr_name'] ?? '');
                $excerpt = moidam_sch_excerpt($item['content'] ?? '', 40);
                $date = substr($item['wr_datetime'] ?? '', 5, 5);
            ?>
            <li class="ptl_sch_item bsk_wz_item<?php echo $has_img ? '' : ' bsk_wz_item--text'; ?>">
                <?php if ($has_img): ?>
                <a href="<?php echo $href ?>" class="bsk_wz_item_thumb_link">
                    <span class="bsk_wz_item_thumb"><img src="<?php echo $thumb['src'] ?>" alt=""></span>
                </a>
                <?php endif; ?>
                <div class="bsk_wz_item_info">
                    <a href="<?php echo $href ?>" class="bsk_wz_item_tit_link">
                        <p class="bsk_wz_item_tit">
                            <span class="bsk_wz_item_tit_txt"><?php if ($is_cmt): ?><em class="ptl_sch_cmt_tag">댓글</em> <?php endif; ?><?php echo $item['subject'] ?></span>
                            <?php echo moidam_cmt_badge($item['wr_comment'] ?? 0); ?>
                        </p>
                    </a>
                    <?php if ($excerpt): ?>
                    <p class="bsk_wz_item_excerpt"><?php echo $excerpt ?></p>
                    <?php endif; ?>
                    <div class="bsk_wz_item_meta">
                        <span class="bsk_wz_author">
                            <span class="profile_img bsk_wz_pf"><?php echo get_member_profile_img($item['mb_id'] ?? '', 24, 24, $nick); ?></span>
                            <span class="bsk_wz_nick"><?php echo $nick ?></span>
                        </span>
                        <span><?php echo $date ?></span>
                    </div>
                </div>
            </li>
            <?php endfor; ?>
            </ul>
        </section>
    <?php endfor; ?>
    </div>
    <?php endif; ?>

<?php endif; ?>
</div>

<div class="ptl_sch_pager"><?php echo $write_pages ?></div>
</div>

<script>
function ptl_fsearch_submit(f) {
    var stx = f.stx.value.trim();
    if (stx.length < 2) {
        alert('검색어는 두 글자 이상 입력하세요.');
        f.stx.select(); f.stx.focus();
        return false;
    }
    f.stx.value = stx;
    return true;
}
</script>
