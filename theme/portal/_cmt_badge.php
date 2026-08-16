<?php
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('moidam_cmt_badge')) {
    function moidam_cmt_badge($cnt = 0) {
        $n = (int)$cnt;
        if ($n < 1) return '';
        $cls = 'cmt_badge';
        if ($n >= 100) $cls .= ' cmt_badge--xl';
        elseif ($n >= 10) $cls .= ' cmt_badge--lg';
        $num = $n > 99 ? '99+' : (string)$n;
        $label = '댓글 ' . $n;
        $svg = '<svg class="cmt_badge_svg" viewBox="0 0 24 22" aria-hidden="true" focusable="false"><path d="M4.4 1.5h15.2c1.6 0 2.9 1.3 2.9 2.9v8.2c0 1.6-1.3 2.9-2.9 2.9h-6.2l-3.9 4.6c-.36.42-1 .15-1-.42v-4.18H4.4c-1.6 0-2.9-1.3-2.9-2.9V4.4c0-1.6 1.3-2.9 2.9-2.9z"/></svg>';
        return '<span class="'.$cls.'" title="'.$label.'" aria-label="'.$label.'">'.$svg.'<em class="cmt_badge_num">'.htmlspecialchars($num, ENT_QUOTES).'</em></span>';
    }
}
