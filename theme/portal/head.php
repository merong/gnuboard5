<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 코어 root head.php 는 pre_head 이벤트를 발생시킨 뒤 테마 head.php 로 위임하는데,
// 테마가 활성화된 페이지(index.php 등)는 root head.php 를 거치지 않고 이 파일을 직접
// include 하므로, pre_head 훅에 의존하는 extend 파일들이 동작하도록 여기서도 동일하게 발생시킨다.
run_event('pre_head');

// 코어의 head.sub.php를 불러와 <html>, <head> 태그를 엽니다.
// 테마 head.sub.php 가 존재하면 root 가 위임하므로 항상 root 를 호출
include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_THEME_PATH.'/portal.settings.php');
// default.css 는 theme.config.php 의 add_stylesheet() + head.sub.php 에서 큐잉되어
// html_end() 가 <head> 안에 삽입하므로 여기서 <link> 직접 출력 불필요
?>

<div id="portal_wrap">
    <?php if (!empty($portal_settings['top_ad_script']) && portal_ad_allowed()): ?>
    <div id="portal_top_ad"><?php echo $portal_settings['top_ad_script']; ?></div>
    <?php endif; ?>
    <!-- Top Header: 좌측 홈/쇼핑몰, 우측 로그인·회원가입 또는 로그아웃·정보수정, 관리자 -->
    <div id="portal_top_header">
        <div class="inner">
            <div class="top_header_left">
                <a href="<?php echo G5_URL ?>">홈</a>
                <?php if (defined('G5_USE_SHOP') && G5_USE_SHOP) { ?>
                <a href="<?php echo G5_SHOP_URL ?>">쇼핑몰</a>
                <?php } ?>
            </div>
            <div class="top_header_right">
                <?php if ($is_member) { ?>
                <a href="<?php echo G5_BBS_URL ?>/logout.php">로그아웃</a>
                <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=register_form.php">정보수정</a>
                <?php } else { ?>
                <a href="<?php echo G5_BBS_URL ?>/login.php">로그인</a>
                <a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a>
                <?php } ?>
                <?php if ($is_admin) { ?>
                <a href="<?php echo G5_ADMIN_URL ?>">관리자</a>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Header: 로고, 검색창, 우측 상단 메뉴 -->
    <header id="portal_header">
        <div class="inner">
            <div class="logo">
                <?php
                $logo_href = $portal_settings['logo_link'] ?: G5_URL;
                $logo_img  = $portal_settings['logo_img'] ?: G5_THEME_URL.'/img/moidam-logo.svg';
                $logo_text = $portal_settings['logo_text'] ?: ($config['cf_title'] ?: '모담');
                ?>
                <a href="<?php echo htmlspecialchars($logo_href); ?>">
                    <?php if ($logo_img): ?>
                    <img src="<?php echo htmlspecialchars($logo_img); ?>" alt="<?php echo htmlspecialchars($logo_text); ?>" class="logo_img">
                    <?php else: ?>
                    <?php echo htmlspecialchars($logo_text); ?>
                    <?php endif; ?>
                </a>
            </div>
            
            <?php $portal_search_plugin_file = $_SERVER['DOCUMENT_ROOT'].'/plugin/portal-search-page/index.php'; ?>
            <?php $portal_search_plugin_on  = is_file($portal_search_plugin_file); ?>
            <?php $portal_search_action = $portal_search_plugin_on ? G5_PLUGIN_URL.'/portal-search-page/index.php' : G5_BBS_URL.'/search.php'; ?>
            <div class="search_wrap">
                <form name="fsearchbox" method="get" action="<?php echo $portal_search_action ?>" onsubmit="return fsearchbox_submit(this);">
                    <input type="text" name="stx" id="sch_stx" placeholder="검색어를 입력하세요">
                    <?php if ($portal_search_plugin_on): ?>
                    <button type="button" id="sch_keyboard_btn" class="sch_keyboard_btn" title="입력도구" aria-label="입력도구"><i class="fa fa-keyboard-o" aria-hidden="true"></i></button>
                    <button type="button" id="sch_voice_btn" class="sch_voice_btn" title="음성 검색" aria-label="음성 검색"><i class="fa fa-microphone" aria-hidden="true"></i></button>
                    <?php endif; ?>
                    <button type="submit" id="sch_submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sch_text"> 검색</span></button>
                </form>
                <?php
                $_sch_recent = popular('theme/suggest', 8, 30);
                if (trim(strip_tags($_sch_recent))) {
                    echo '<div class="sch_recent" id="sch_recent" hidden>'.$_sch_recent.'</div>';
                }
                ?>
            </div>

            <?php if ($is_admin == 'super' && $portal_search_plugin_on): ?>
            <a href="<?php echo G5_PLUGIN_URL ?>/portal-search-page/admin.php" class="search_admin_btn" title="검색 플러그인 관리자 설정">
                <i class="fa fa-cog" aria-hidden="true"></i>
            </a>
            <?php endif; ?>

            <?php $cp_admin_file = $_SERVER['DOCUMENT_ROOT'].'/plugin/a-character-profile/admin.php'; ?>
            <?php if ($is_admin == 'super' && is_file($cp_admin_file)): ?>
            <a href="<?php echo G5_PLUGIN_URL ?>/a-character-profile/admin.php" class="search_admin_btn" title="인물 프로필 관리">
                <i class="fa fa-id-card" aria-hidden="true"></i>
            </a>
            <?php endif; ?>

            <?php $top_banner = $portal_settings['top_banner'] ?? []; ?>
            <?php if (!empty($top_banner['img']) && portal_top_banner_allowed()): ?>
            <div class="header_banner">
                <?php if (!empty($top_banner['link'])): ?><a href="<?php echo htmlspecialchars($top_banner['link']); ?>" target="<?php echo htmlspecialchars($top_banner['target'] ?: '_self'); ?>"><?php endif; ?>
                <img src="<?php echo htmlspecialchars($top_banner['img']); ?>" alt="<?php echo htmlspecialchars($top_banner['alt'] ?? ''); ?>" width="120" height="60">
                <?php if (!empty($top_banner['link'])): ?></a><?php endif; ?>
            </div>
            <?php endif; ?>


            <button class="mobile_menu_btn" id="mobile_menu_btn" aria-label="메뉴 열기" aria-expanded="false">
                <i class="fa fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- GNB (메인 내비게이션 바) -->
    <nav id="portal_gnb">
        <!-- 모바일 햄버거 메뉴 상단: 로그인/회원가입 + 관리자 버튼 -->
        <div class="gnb_mobile_user">
            <?php if ($is_member) { ?>
            <a href="<?php echo G5_BBS_URL ?>/logout.php" class="gnb_user_btn gnb_user_logout">로그아웃</a>
            <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=register_form.php" class="gnb_user_btn gnb_user_mypage">정보수정</a>
            <?php } else { ?>
            <a href="<?php echo G5_BBS_URL ?>/login.php" class="gnb_user_btn gnb_user_login">로그인</a>
            <a href="<?php echo G5_BBS_URL ?>/register.php" class="gnb_user_btn gnb_user_join">회원가입</a>
            <?php } ?>
            <?php if ($is_admin) { ?>
            <a href="<?php echo G5_ADMIN_URL ?>" class="gnb_user_btn gnb_user_admin">관리자</a>
            <?php } ?>
        </div>
        <div class="inner">
            <ul class="gnb_list">
                <?php
                $menu_datas = get_menu_db(0, true);
                $i = 0;
                foreach ($menu_datas as $row) {
                    if (empty($row)) continue;
                    $has_sub = !empty($row['sub']);
                ?>
                <li class="gnb_item<?php echo $has_sub ? ' has_sub' : ''; ?>">
                    <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>">
                        <?php echo $row['me_name']; ?>
                        <?php if ($has_sub) { ?><span class="gnb_arrow" aria-hidden="true"></span><?php } ?>
                    </a>
                    <?php if ($has_sub) { ?>
                    <ul class="gnb_sub">
                        <?php foreach ($row['sub'] as $sub) { ?>
                        <li>
                            <a href="<?php echo $sub['me_link']; ?>" target="_<?php echo $sub['me_target']; ?>">
                                <?php echo $sub['me_name']; ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php
                    $i++;
                }
                if ($i == 0) { ?>
                <li class="gnb_empty"><a href="<?php echo G5_ADMIN_URL ?>/menu_list.php">관리자 접속 후 메뉴를 설정해주세요.</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <script>
    // 코어의 검색기록 드롭다운 UI를 덮어씌움. 검색어가 비어 있어도 그대로 제출해
    // 검색 페이지의 빈 상태 화면으로 이동시킨다(브라우저 필수입력 경고를 띄우지 않음).
    function fsearchbox_submit(f) {
        return true;
    }

    jQuery(function($){
        var $recent = $('#sch_recent');
        var hideT;
        if ($recent.length) {
            $('#sch_stx').on('focus', function(){
                clearTimeout(hideT);
                $recent.removeAttr('hidden');
            }).on('blur', function(){
                hideT = setTimeout(function(){ $recent.attr('hidden', 'hidden'); }, 180);
            });
            $recent.on('mousedown', function(e){ e.preventDefault(); });
        }
        var $gnb = $('#portal_gnb');
        var $btn = $('#mobile_menu_btn');
        $btn.on('click', function(e){
            e.stopPropagation();
            var open = $gnb.toggleClass('mobile_open').hasClass('mobile_open');
            $btn.toggleClass('is_open', open).attr('aria-expanded', open);
        });
        $(document).on('click', function(e){
            if(!$(e.target).closest('#portal_gnb, #mobile_menu_btn').length){
                $gnb.removeClass('mobile_open');
                $btn.removeClass('is_open').attr('aria-expanded', 'false');
            }
        });
    });

    </script>

    <?php if ($portal_search_plugin_on): ?>
    <script src="<?php echo G5_PLUGIN_URL ?>/portal-search-page/js/search-tools.js?ver=<?php echo G5_SERVER_TIME; ?>"></script>
    <script>
    PtsSearchTools.init({
        formName: 'fsearchbox',
        inputId: 'sch_stx',
        voiceBtnId: 'sch_voice_btn',
        keyboardBtnId: 'sch_keyboard_btn'
    });
    </script>
    <?php endif; ?>

    <!-- Main Container Start -->
    <div id="portal_container">
        <div class="inner">
<?php
// 게시판 페이지(board.php)이면 사이드바 그리드 래퍼 오픈
if (isset($board['bo_table']) && $board['bo_table']) {
    echo '<div class="portal_board_layout">'.PHP_EOL;
    echo '<div class="portal_board_main">'.PHP_EOL;
}
?>
