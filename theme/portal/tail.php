<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 게시판 페이지이면 사이드바 그리드 래퍼 닫기
if (isset($board['bo_table']) && $board['bo_table']) {
    echo '</div><!-- /.portal_board_main -->'.PHP_EOL;
    include_once(G5_THEME_PATH . '/_board_sidebar.php');
    echo '</div><!-- /.portal_board_layout -->'.PHP_EOL;
}
?>
        </div> <!-- // .inner -->
    </div> <!-- //portal_container -->

    <!-- Footer -->
    <?php
    global $portal_settings;
    $ft_copyright = isset($portal_settings['footer_copyright']) ? $portal_settings['footer_copyright'] : 'Copyright &copy; <b>YourCompany</b> All rights reserved.';
    $ft_info      = isset($portal_settings['footer_info'])      ? $portal_settings['footer_info']      : '<span><b>사업자등록번호:</b> 123-45-67890</span><span><b>대표이사:</b> 홍길동</span><span><b>주소:</b> 서울특별시 강남구 테헤란로 123</span>';
    ?>
    <footer id="portal_footer">
        <div class="inner">
            <?php
            $ft_menu = isset($portal_settings['footer_menu']) && is_array($portal_settings['footer_menu'])
                       ? $portal_settings['footer_menu'] : [];
            if ($ft_menu): ?>
            <ul class="footer_links">
                <?php foreach ($ft_menu as $fm):
                    $fm_label = htmlspecialchars($fm['label'] ?? '', ENT_QUOTES);
                    $fm_url   = htmlspecialchars($fm['url']   ?? '#',  ENT_QUOTES);
                    $fm_class = !empty($fm['class']) ? ' class="'.htmlspecialchars($fm['class'], ENT_QUOTES).'"' : '';
                ?>
                <li><a href="<?php echo $fm_url ?>"<?php echo $fm_class ?>><?php echo $fm_label ?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <div class="footer_info">
                <?php echo $ft_info; ?>
                <div class="copyright"><?php echo $ft_copyright; ?></div>
            </div>
        </div>
    </footer>

    <!-- ToTop 버튼: 일정 스크롤 이후 노출, 클릭 시 맨 위로 부드럽게 이동 -->
    <button type="button" id="portal_totop_btn" class="portal_totop_btn" title="맨 위로" aria-label="맨 위로">
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </button>
</div> <!-- //portal_wrap -->

<script>
/* ToTop 버튼 표시/스크롤 */
jQuery(function($){
    var $totop = $('#portal_totop_btn');
    var SHOW_AT = 300;

    function syncTotop() {
        $totop.toggleClass('show', $(window).scrollTop() > SHOW_AT);
    }
    syncTotop();
    $(window).on('scroll', syncTotop);

    $totop.on('click', function(){
        $('html, body').animate({ scrollTop: 0 }, 300);
    });
});
</script>

<script>
/* 사이드뷰 드롭다운이 다른 행/아이템 위에 표시되도록 z-index 조정 */
jQuery(function($){
    $(document).on('click', '.sv_member, .sv_guest', function(){
        $('.sv_zfix').css({position:'', zIndex:''}).removeClass('sv_zfix');
        var $host = $(this).closest('tr, li, .nv_news_item');
        if ($host.length) {
            $host.css({position:'relative', zIndex: 50}).addClass('sv_zfix');
        }
    });
    $(document).on('click', function(e){
        if (!$(e.target).closest('.sv_wrap').length) {
            $('.sv_zfix').css({position:'', zIndex:''}).removeClass('sv_zfix');
        }
    });
});
</script>

<?php
// 도트 지렁이 플러그인 인클루드
//include_once(G5_PLUGIN_PATH.'/dot_worm/dot_worm.php');
?>

<?php
$__swipe_js = G5_THEME_PATH.'/js/tabs-swipe.js';
if (is_file($__swipe_js)) {
    echo '<script src="'.G5_THEME_URL.'/js/tabs-swipe.js?v='.filemtime($__swipe_js).'"></script>'.PHP_EOL;
}
if (defined('MOIDAM_USE_SWIPER') && MOIDAM_USE_SWIPER) {
    echo '<script src="https://cdn.jsdelivr.net/npm/swiper@14.1.0/swiper-bundle.min.js"></script>'.PHP_EOL;
    $__moidam_swiper_js = G5_THEME_PATH.'/js/moidam-swiper.js';
    if (is_file($__moidam_swiper_js)) {
        echo '<script src="'.G5_THEME_URL.'/js/moidam-swiper.js?v='.filemtime($__moidam_swiper_js).'"></script>'.PHP_EOL;
    }
}
?>

<?php
// 코어의 tail.sub.php를 불러와 </body>, </html> 태그를 닫습니다.
if(file_exists(G5_THEME_PATH.'/tail.sub.php')) {
    include_once(G5_THEME_PATH.'/tail.sub.php');
} else {
    include_once(G5_PATH.'/tail.sub.php');
}
?>
