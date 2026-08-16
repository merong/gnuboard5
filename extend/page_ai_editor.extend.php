<?php
if (!defined('_GNUBOARD_')) exit;

// page-ai-editor AJAX 업로드 플러그인. 에디터 어댑터는 plugin/editor/page-ai-editor/

// 이 요청의 게시판이 page-ai-editor 를 쓰면 모바일 글쓰기에도 DHTML 을 켭니다.
// common.php 가 bo_select_editor 로 cf_editor 를 덮어쓴 뒤에 extend 가 로드됩니다.
// config.php 에서 G5_IS_MOBILE_DHTML_USE 를 정의하지 않아야 이 define 이 동작합니다.
if (!defined('G5_IS_MOBILE_DHTML_USE')) {
    $__page_ai = 'page-ai-editor';
    $__use_page_ai = (isset($config['cf_editor']) && $config['cf_editor'] === $__page_ai)
        || (isset($board['bo_select_editor']) && $board['bo_select_editor'] === $__page_ai);
    if ($__use_page_ai) {
        define('G5_IS_MOBILE_DHTML_USE', true);
    }
    unset($__page_ai, $__use_page_ai);
}
