<?php
if (!defined('_GNUBOARD_')) exit;
if (function_exists('cf_stream_delete_row') && !empty($write['wr_id'])) {
    cf_stream_delete_row($bo_table, (int) $write['wr_id']);
}
