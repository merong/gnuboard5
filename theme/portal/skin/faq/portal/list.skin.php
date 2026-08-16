<?php
if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.$faq_skin_url.'/style.css">'.PHP_EOL;
?>
<div id="ptl_faq_wrap">

<?php if ($himg_src): ?>
<div class="ptl_faq_himg"><img src="<?php echo $himg_src ?>" alt=""></div>
<?php endif; ?>
<?php echo '<div class="ptl_faq_hhtml">'.conv_content($fm['fm_head_html'], 1).'</div>'; ?>

<!-- 검색 폼 -->
<div class="ptl_faq_sch">
    <form name="faq_search_form" method="get">
        <input type="hidden" name="fm_id" value="<?php echo $fm_id ?>">
        <div class="ptl_faq_sch_inner">
            <input type="text" name="stx" id="stx" value="<?php echo $stx ?>" required class="ptl_faq_sch_input" placeholder="FAQ 검색어 입력" maxlength="15">
            <button type="submit" class="ptl_faq_sch_btn"><i class="fa fa-search"></i> 검색</button>
        </div>
    </form>
</div>

<!-- 분류 탭 -->
<?php if (count($faq_master_list)): ?>
<nav class="ptl_faq_cate">
    <ul>
        <?php foreach ($faq_master_list as $v):
            $is_active = ($v['fm_id'] == $fm_id);
        ?>
        <li>
            <a href="<?php echo $category_href ?>?fm_id=<?php echo $v['fm_id'] ?>"
               class="<?php echo $is_active ? 'ptl_faq_cate_on' : '' ?>">
                <?php if ($is_active): ?><span class="sound_only">현재 선택: </span><?php endif; ?>
                <?php echo $v['fm_subject'] ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- FAQ 목록 -->
<div class="ptl_faq_list_wrap">
<?php if (count($faq_list)): ?>
    <ol class="ptl_faq_list">
        <?php foreach ($faq_list as $v):
            if (empty($v)) continue;
        ?>
        <li class="ptl_faq_item">
            <button class="ptl_faq_q" type="button" onclick="return ptl_faq_toggle(this);">
                <span class="ptl_faq_q_mark">Q</span>
                <span class="ptl_faq_q_txt"><?php echo conv_content($v['fa_subject'], 1) ?></span>
                <i class="ptl_faq_q_icon fa fa-chevron-down"></i>
            </button>
            <div class="ptl_faq_a" style="display:none">
                <span class="ptl_faq_a_mark">A</span>
                <div class="ptl_faq_a_content"><?php echo conv_content($v['fa_content'], 1) ?></div>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
<?php elseif ($stx): ?>
    <div class="ptl_faq_empty"><i class="fa fa-search"></i><p>"<?php echo htmlspecialchars($stx, ENT_QUOTES) ?>"에 대한 FAQ가 없습니다.</p></div>
<?php else: ?>
    <div class="ptl_faq_empty">
        <i class="fa fa-question-circle"></i>
        <p>등록된 FAQ가 없습니다.</p>
        <?php if ($is_admin): ?>
        <a href="<?php echo G5_ADMIN_URL ?>/faqmasterlist.php" class="ptl_faq_admin_link">FAQ 관리하기</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>

<?php echo get_paging($page_rows, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=') ?>

<?php echo '<div class="ptl_faq_thtml">'.conv_content($fm['fm_tail_html'], 1).'</div>'; ?>
<?php if ($timg_src): ?>
<div class="ptl_faq_timg"><img src="<?php echo $timg_src ?>" alt=""></div>
<?php endif; ?>

<?php if ($admin_href): ?>
<div class="ptl_faq_admin"><a href="<?php echo $admin_href ?>" class="ptl_faq_admin_link"><i class="fa fa-cog"></i> FAQ 수정</a></div>
<?php endif; ?>

</div><!-- /#ptl_faq_wrap -->

<script src="<?php echo G5_JS_URL ?>/viewimageresize.js"></script>
<script>
function ptl_faq_toggle(btn) {
    var $item = jQuery(btn).closest('.ptl_faq_item');
    var $a    = $item.find('.ptl_faq_a');
    var $icon = jQuery(btn).find('.ptl_faq_q_icon');
    if ($a.is(':visible')) {
        $a.slideUp(200);
        $item.removeClass('ptl_faq_open');
        $icon.css('transform', '');
    } else {
        jQuery('.ptl_faq_item.ptl_faq_open .ptl_faq_a').slideUp(200);
        jQuery('.ptl_faq_item.ptl_faq_open').removeClass('ptl_faq_open');
        jQuery('.ptl_faq_q_icon').css('transform', '');
        $a.slideDown(220, function() { $a.viewimageresize2 && $a.viewimageresize2(); });
        $item.addClass('ptl_faq_open');
        $icon.css('transform', 'rotate(180deg)');
    }
    return false;
}
</script>
