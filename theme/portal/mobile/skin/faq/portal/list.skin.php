<?php
if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.$faq_skin_url.'/style.css">'.PHP_EOL;
?>
<div id="mptl_faq_wrap">

<?php echo '<div class="mptl_faq_hhtml">'.conv_content($fm['fm_mobile_head_html'], 1).'</div>'; ?>

<!-- 검색 폼 -->
<div class="mptl_faq_sch">
    <form name="faq_search_form" method="get">
        <input type="hidden" name="fm_id" value="<?php echo $fm_id ?>">
        <div class="mptl_faq_sch_inner">
            <input type="text" name="stx" id="stx" value="<?php echo $stx ?>" required class="mptl_faq_sch_input" placeholder="FAQ 검색" maxlength="15">
            <button type="submit" class="mptl_faq_sch_btn"><i class="fa fa-search"></i></button>
        </div>
    </form>
</div>

<!-- 분류 탭 -->
<?php if (count($faq_master_list)): ?>
<nav class="mptl_faq_cate">
    <ul>
        <?php foreach ($faq_master_list as $v):
            $is_active = ($v['fm_id'] == $fm_id);
        ?>
        <li>
            <a href="<?php echo $category_href ?>?fm_id=<?php echo $v['fm_id'] ?>"
               class="<?php echo $is_active ? 'mptl_faq_cate_on' : '' ?>">
                <?php echo $v['fm_subject'] ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- FAQ 목록 -->
<div class="mptl_faq_list_wrap">
<?php if (count($faq_list)): ?>
    <ol class="mptl_faq_list">
        <?php foreach ($faq_list as $v):
            if (empty($v)) continue;
        ?>
        <li class="mptl_faq_item">
            <button class="mptl_faq_q" type="button" onclick="return mptl_faq_toggle(this);">
                <span class="mptl_faq_q_mark">Q</span>
                <span class="mptl_faq_q_txt"><?php echo conv_content($v['fa_subject'], 1) ?></span>
                <i class="mptl_faq_q_icon fa fa-chevron-down"></i>
            </button>
            <div class="mptl_faq_a" style="display:none">
                <span class="mptl_faq_a_mark">A</span>
                <div class="mptl_faq_a_content"><?php echo conv_content($v['fa_content'], 1) ?></div>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
<?php elseif ($stx): ?>
    <div class="mptl_faq_empty"><i class="fa fa-search"></i><p>"<?php echo htmlspecialchars($stx, ENT_QUOTES) ?>"에 대한 FAQ가 없습니다.</p></div>
<?php else: ?>
    <div class="mptl_faq_empty">
        <i class="fa fa-question-circle"></i><p>등록된 FAQ가 없습니다.</p>
    </div>
<?php endif; ?>
</div>

<?php echo get_paging($page_rows, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=') ?>
<?php echo '<div class="mptl_faq_thtml">'.conv_content($fm['fm_mobile_tail_html'], 1).'</div>'; ?>

</div><!-- /#mptl_faq_wrap -->

<script src="<?php echo G5_JS_URL ?>/viewimageresize.js"></script>
<script>
function mptl_faq_toggle(btn) {
    var $item = jQuery(btn).closest('.mptl_faq_item');
    var $a    = $item.find('.mptl_faq_a');
    var $icon = jQuery(btn).find('.mptl_faq_q_icon');
    if ($a.is(':visible')) {
        $a.slideUp(200);
        $item.removeClass('mptl_faq_open');
        $icon.css('transform', '');
    } else {
        jQuery('.mptl_faq_item.mptl_faq_open .mptl_faq_a').slideUp(200);
        jQuery('.mptl_faq_item.mptl_faq_open').removeClass('mptl_faq_open');
        jQuery('.mptl_faq_q_icon').css('transform', '');
        $a.slideDown(220);
        $item.addClass('mptl_faq_open');
        $icon.css('transform', 'rotate(180deg)');
    }
    return false;
}
</script>
