<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
add_stylesheet('<link rel="stylesheet" href="'.$search_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);

if (!function_exists('moidam_sch_excerpt')) {
    function moidam_sch_excerpt($text, $len = 70) {
        $text = html_entity_decode(strip_tags((string)$text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        return cut_str(trim($text), $len, '…');
    }
}
?>
<div id="msch_wrap">

<div class="msch_form_area">
    <form name="fsearch" onsubmit="return msch_submit(this);" method="get">
    <input type="hidden" name="srows" value="<?php echo $srows ?>">
    <?php echo $group_select ?>
    <script>document.getElementById("gr_id").value = "<?php echo $gr_id ?>";</script>
    <div class="msch_row">
        <select name="sfl" class="msch_select">
            <option value="wr_subject||wr_content"<?php echo get_selected($sfl, "wr_subject||wr_content") ?>>제목+내용</option>
            <option value="wr_subject"<?php echo get_selected($sfl, "wr_subject") ?>>제목</option>
            <option value="wr_content"<?php echo get_selected($sfl, "wr_content") ?>>내용</option>
            <option value="mb_id"<?php echo get_selected($sfl, "mb_id") ?>>회원아이디</option>
            <option value="wr_name"<?php echo get_selected($sfl, "wr_name") ?>>이름</option>
        </select>
        <input type="text" name="stx" value="<?php echo $text_stx ?>" class="msch_input" required placeholder="검색어 입력" maxlength="20">
        <button type="submit" class="msch_btn"><i class="fa fa-search"></i><span class="sound_only">검색</span></button>
    </div>
    </form>
</div>

<div class="msch_result">
<?php if ($stx): ?>
    <?php if ($board_count): ?>
    <div class="msch_summary">
        <strong>"<?php echo htmlspecialchars($stx, ENT_QUOTES) ?>"</strong>
        <span>게시판 <?php echo $board_count ?> · 게시물 <?php echo number_format($total_count) ?></span>
    </div>
    <nav class="msch_nav">
        <ul>
            <li><a href="?<?php echo $search_query ?>&amp;gr_id=<?php echo $gr_id ?>" <?php echo $sch_all ?>>전체</a></li>
            <?php echo $str_board_list ?>
        </ul>
    </nav>
    <?php else: ?>
    <p class="msch_empty">"<?php echo htmlspecialchars($stx, ENT_QUOTES) ?>"에 대한 검색 결과가 없습니다.</p>
    <?php endif; ?>

    <?php if ($board_count): ?>
    <?php
    $k = 0;
    for ($idx = $table_index; $idx < count($search_table) && $k < $rows; $idx++):
        $bo = $search_table[$idx];
    ?>
    <section class="msch_section">
        <div class="msch_head">
            <h2><a href="<?php echo get_pretty_url($bo, '', $search_query) ?>"><?php echo $bo_subject[$idx] ?></a></h2>
            <a href="<?php echo get_pretty_url($bo, '', $search_query) ?>" class="msch_more">더보기</a>
        </div>
        <ul class="msch_list">
        <?php for ($i = 0; $i < count($list[$idx]) && $k < $rows; $i++, $k++):
            $item = $list[$idx][$i];
            $is_cmt = !empty($item['wr_is_comment']);
            $thumb_id = $is_cmt ? (int)$item['wr_parent'] : (int)$item['wr_id'];
            $thumb = $is_cmt ? array() : get_list_thumbnail($bo, $thumb_id, 140, 100, false, true);
            $has_img = !empty($thumb['src']);
            $href = $item['href'].($is_cmt ? '#c_'.$item['wr_id'] : '');
            $nick = get_text($item['wr_name'] ?? '');
            $excerpt = moidam_sch_excerpt($item['content'] ?? '', 70);
            $date = substr($item['wr_datetime'] ?? '', 5, 5);
        ?>
        <li class="msch_item<?php echo $has_img ? '' : ' is-text'; ?>">
            <?php if ($has_img): ?>
            <a href="<?php echo $href ?>" class="msch_thumb"><img src="<?php echo $thumb['src'] ?>" alt=""></a>
            <?php endif; ?>
            <div class="msch_info">
                <a href="<?php echo $href ?>" class="msch_tit">
                    <span class="msch_tit_txt"><?php if ($is_cmt): ?><em class="msch_cmt_tag">댓글</em><?php endif; ?><?php echo $item['subject'] ?></span>
                    <?php echo moidam_cmt_badge($item['wr_comment'] ?? 0); ?>
                </a>
                <?php if ($excerpt): ?><p class="msch_excerpt"><?php echo $excerpt ?></p><?php endif; ?>
                <div class="msch_meta">
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
    <?php endif; ?>
<?php endif; ?>
</div>

<div class="msch_pager"><?php echo $write_pages ?></div>
</div>
<script>
function msch_submit(f) {
    var stx = f.stx.value.trim();
    if (stx.length < 2) { alert('검색어는 두 글자 이상 입력하세요.'); f.stx.focus(); return false; }
    f.stx.value = stx;
    return true;
}
</script>
