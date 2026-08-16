<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
 * theme.config.php 는 common.php 에서 ob_start() 보다 먼저 로드된다.
 * G5_THEME_URL/PATH/CSS_URL 등 상수는 common.php 가 이미 정의했으므로
 * 여기서 재정의하면 PHP 노티스만 발생하고 값이 바뀌지 않는다 → 모두 제거.
 *
 * ★ define('G5_IS_MOBILE', true) 을 제거한 이유:
 *    이 상수가 true 이면 common.php 의 스킨 경로 계산에서 bo_mobile_skin 을 사용하게 되고,
 *    모바일 스킨이 비어 있는 게시판은 list.skin.php 자체를 찾지 못해 생 HTML이 출력된다.
 *    반응형은 CSS 에서 처리하고 G5_IS_MOBILE 은 기기 자동감지에 맡긴다.
 *
 * ★ add_stylesheet() 를 여기서 호출하는 이유:
 *    board.php 는 G5_PATH.'/head.sub.php' 를 직접 포함하므로 테마의 head.php 를
 *    거치지 않는다. add_stylesheet() 는 html_process 정적 배열에 누적되고,
 *    tail.sub.php 의 html_end() 가 output buffer 를 후처리하여 </title> 직후
 *    <link> 뒤에 삽입하므로 어떤 페이지에서도 <head> 안에 정상 로드된다.
 */

// 포털 테마 CSS 는 head.sub.php 에서 직접 echo 로 출력 (add_stylesheet 미사용)
