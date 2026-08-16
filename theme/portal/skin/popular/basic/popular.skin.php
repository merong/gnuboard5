<?php
if (!defined('_GNUBOARD_')) exit;
?>
<ol class="popular_list">
<?php
if (isset($list) && is_array($list)) {
    foreach ($list as $i => $row) {
        $rank = $i + 1;
        $cls  = ($rank <= 3) ? ' top3' : '';
?>
    <li class="popular_item<?php echo $cls; ?>">
        <span class="popular_rank"><?php echo $rank; ?></span>
        <a href="<?php echo G5_BBS_URL ?>/search.php?sfl=wr_subject&amp;sop=and&amp;stx=<?php echo urlencode($row['pp_word']); ?>" class="popular_word"><?php echo get_text($row['pp_word']); ?></a>
    </li>
<?php
    }
}
?>
</ol>
