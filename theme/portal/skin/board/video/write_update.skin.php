<?php
if (!defined('_GNUBOARD_')) exit;
if (function_exists('cf_stream_save_from_request') && !empty($_POST['cf_uid'])) {
    cf_stream_save_from_request($bo_table, (int) $wr_id);
}
