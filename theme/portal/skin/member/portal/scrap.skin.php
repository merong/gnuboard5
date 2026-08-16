<?php if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css">'.PHP_EOL;
?>
<div class="sc_wrap">

    <!-- 헤더 -->
    <div class="sc_header">
        <h1 class="sc_title"><i class="fa fa-bookmark-o"></i> 스크랩 목록</h1>
        <button type="button" class="sc_close_x" onclick="window.close()">×</button>
    </div>

    <!-- 목록 -->
    <ul class="sc_list">
    <?php foreach ((array)$list as $row) { ?>
    <li class="sc_item">
        <div class="sc_item_inner">
            <a href="<?php echo $row['opener_href_wr_id'] ?>"
               class="sc_subject" target="_blank"
               onclick="opener.document.location.href='<?php echo $row['opener_href_wr_id'] ?>'; return false;"
            ><?php echo $row['subject'] ?></a>
            <div class="sc_meta">
                <a href="<?php echo $row['opener_href'] ?>"
                   class="sc_board" target="_blank"
                   onclick="opener.document.location.href='<?php echo $row['opener_href'] ?>'; return false;"
                ><i class="fa fa-folder-o"></i> <?php echo $row['bo_subject'] ?></a>
                <span class="sc_date"><i class="fa fa-clock-o"></i> <?php echo $row['ms_datetime'] ?></span>
            </div>
        </div>
        <a href="<?php echo $row['del_href'] ?>" onclick="del(this.href); return false;"
           class="sc_del" title="삭제"><i class="fa fa-trash-o"></i></a>
    </li>
    <?php } ?>
    <?php if (empty($list)) { ?>
    <li class="sc_empty"><i class="fa fa-bookmark-o"></i><br>스크랩한 글이 없습니다.</li>
    <?php } ?>
    </ul>

    <!-- 페이징 + 닫기 -->
    <div class="sc_footer">
        <?php echo get_paging($config['cf_write_pages'], $page, $total_page, "?{$qstr}&amp;page=") ?>
        <button type="button" class="sc_close_btn" onclick="window.close()">닫기</button>
    </div>

</div>
