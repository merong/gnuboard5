# 그누보드5 포털 테마 (theme/portal) 작업 내역

이 문서는 그누보드5를 기반으로 네이버/다음 스타일의 포털 테마를 제작하기 위한 기본 템플릿 코드입니다. AI 코딩 어시스턴트는 이 코드를 컨텍스트로 삼아 추가적인 스킨 제작이나 레이아웃 수정을 진행해 주세요.

## 1. 테마 설정 (theme/portal/theme.config.php)

<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 테마의 고유 경로를 설정합니다.
define('G5_THEME_DIR', 'portal');
define('G5_THEME_URL', G5_URL.'/theme/'.G5_THEME_DIR);
define('G5_THEME_PATH', G5_PATH.'/theme/'.G5_THEME_DIR);

define('G5_THEME_CSS_URL', G5_THEME_URL.'/css');
define('G5_THEME_IMG_URL', G5_THEME_URL.'/img');
define('G5_THEME_JS_URL', G5_THEME_URL.'/js');

// 반응형 테마로 설정합니다 (PC/모바일 동일한 뷰)
define('G5_IS_MOBILE', true); 
?>

## 2. 상단 레이아웃 (theme/portal/head.php)

<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 코어의 head.sub.php를 불러와 <html>, <head> 태그를 엽니다.
if(file_exists(G5_THEME_PATH.'/head.sub.php')) {
    include_once(G5_THEME_PATH.'/head.sub.php');
} else {
    include_once(G5_PATH.'/head.sub.php');
}
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
?>
<!-- 테마 전용 CSS 로드 -->
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL; ?>/default.css?ver=<?php echo G5_SERVER_TIME ?>">

<div id="portal_wrap">
    <!-- Header: 로고, 검색창, 우측 상단 메뉴 -->
    <header id="portal_header">
        <div class="inner">
            <div class="logo">
                <a href="<?php echo G5_URL ?>">LOGO</a>
            </div>
            
            <div class="search_wrap">
                <form name="fsearchbox" method="get" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);">
                    <input type="text" name="stx" id="sch_stx" placeholder="검색어를 입력하세요" required>
                    <button type="submit" id="sch_submit"><i class="fa fa-search" aria-hidden="true"></i> 검색</button>
                </form>
            </div>
            
            <div class="top_menu">
                <?php if ($is_member) { ?>
                    <a href="<?php echo G5_BBS_URL ?>/logout.php">로그아웃</a>
                    <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=register_form.php">정보수정</a>
                <?php } else { ?>
                    <a href="<?php echo G5_BBS_URL ?>/login.php">로그인</a>
                    <a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a>
                <?php } ?>
                <?php if ($is_admin) { ?>
                    <a href="<?php echo G5_ADMIN_URL ?>"><b>관리자</b></a>
                <?php } ?>
            </div>
        </div>
    </header>

    <!-- GNB (메인 내비게이션 바) -->
    <nav id="portal_gnb">
        <div class="inner">
            <ul class="gnb_list">
                <?php
                // 그누보드 메뉴 DB에서 메뉴 데이터를 가져옵니다.
                $menu_datas = get_menu_db(0, true);
                $i = 0;
                foreach( $menu_datas as $row ){
                    if( empty($row) ) continue;
                ?>
                    <li>
                        <!-- 메뉴 링크와 타겟(새창 여부) 출력 -->
                        <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>">
                            <?php echo $row['me_name']; ?>
                        </a>
                    </li>
                <?php
                    $i++;
                } // end foreach

                // 등록된 메뉴가 없을 경우 관리자에게 안내
                if ($i == 0) {  ?>
                    <li class="gnb_empty"><a href="<?php echo G5_ADMIN_URL ?>/menu_list.php">관리자 접속 후 메뉴를 설정해주세요.</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <!-- Main Container Start -->
    <div id="portal_container">
        <div class="inner">

## 3. 메인 화면 (theme/portal/index.php)

<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_THEME_PATH.'/head.php');
?>

<style>
/* 메인 페이지 전용 CSS (테스트용, 나중에 default.css로 이동 권장) */
.portal_tabs { margin-bottom: 20px; background: #fff; border: 1px solid #e4e8eb; border-radius: 8px; overflow: hidden; }
.tab_nav { display: flex; border-bottom: 1px solid #e4e8eb; background: #f8f9fa; }
.tab_nav button { flex: 1; padding: 15px 0; border: none; background: none; font-size: 15px; font-weight: bold; color: #666; cursor: pointer; border-right: 1px solid #e4e8eb; transition: all 0.2s; }
.tab_nav button:last-child { border-right: none; }
.tab_nav button:hover { background: #fff; }
.tab_nav button.active { background: #fff; color: #03c75a; border-bottom: 2px solid #03c75a; }
.tab_content { display: none; padding: 20px; }
.tab_content.active { display: block; }

.widget_title { padding: 15px; border-bottom: 1px solid #e4e8eb; font-weight: bold; font-size: 15px; background: #f8f9fa; color: #333; }
.widget_content { padding: 15px; }
.gallery_section h3 { font-size: 16px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #333; }
</style>

<!-- 포털 스타일 2단 레이아웃 -->
<div class="portal_main_grid">
    
    <!-- 왼쪽 영역 (메인 콘텐츠) -->
    <div class="grid_left">
        
        <!-- 상단 배너 영역 -->
        <div class="main_banner">
            <h2>메인 뉴스 / 이벤트 배너 영역</h2>
            <p>이곳에 슬라이드 이미지나 메인 뉴스가 들어갑니다.</p>
        </div>

        <!-- 1. 카테고리 탭 전환 방식 최신글 -->
        <div class="portal_tabs">
            <div class="tab_nav">
                <button class="active" onclick="openTab(event, 'tab_news')">뉴스</button>
                <button onclick="openTab(event, 'tab_sports')">스포츠</button>
                <button onclick="openTab(event, 'tab_enter')">연예</button>
            </div>
            
            <!-- 뉴스 탭 내용 (news 게시판 연동) -->
            <div id="tab_news" class="tab_content active">
                <?php echo latest('theme/basic', 'news', 6, 40); ?>
            </div>
            
            <!-- 스포츠 탭 내용 (sports 게시판 연동) -->
            <div id="tab_sports" class="tab_content">
                <?php echo latest('theme/basic', 'sports', 6, 40); ?>
            </div>
            
            <!-- 연예 탭 내용 (enter 게시판 연동) -->
            <div id="tab_enter" class="tab_content">
                <?php echo latest('theme/basic', 'enter', 6, 40); ?>
            </div>
        </div>

        <!-- 2. 갤러리 방식 최신글 -->
        <div class="gallery_section">
            <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=gallery">포토 갤러리</a></h3>
            <!-- 그누보드 기본 내장 스킨인 pic_block(이미지 블록형) 사용 -->
            <?php echo latest('theme/pic_block', 'gallery', 4, 23); ?>
        </div>

        <!-- 하단 2단 분할 게시판 영역 -->
        <div class="board_section" style="margin-top:20px;">
            <div class="half_board">
                <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=free">자유게시판</a></h3>
                <?php echo latest('theme/basic', 'free', 5, 23); ?>
            </div>
            <div class="half_board">
                <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=qa">질문답변</a></h3>
                <?php echo latest('theme/basic', 'qa', 5, 23); ?>
            </div>
        </div>

    </div>

    <!-- 오른쪽 영역 (위젯 및 로그인) -->
    <div class="grid_right">
        <!-- 외부 로그인 (outlogin) 위젯 출력 -->
        <div class="widget_box widget_login">
            <?php echo outlogin('theme/basic'); ?>
        </div>

        <!-- 3. 단독 카테고리 최신글 (예: 공지사항) -->
        <div class="widget_box">
            <div class="widget_title">
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=notice">공지사항</a>
            </div>
            <div class="widget_content">
                <?php echo latest('theme/basic', 'notice', 5, 25); ?>
            </div>
        </div>

        <!-- 사이드바 추가 최신글 위젯 (예: 자유게시판) -->
        <div class="widget_box">
            <div class="widget_title">
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=free">자유게시판 새글</a>
            </div>
            <div class="widget_content">
                <?php echo latest('theme/basic', 'free', 5, 25); ?>
            </div>
        </div>

        <!-- 인기 검색어 위젯 -->
        <div class="widget_box">
            <div class="widget_title">인기 검색어</div>
            <div class="widget_content" style="padding: 10px;">
                <?php echo popular('theme/basic'); ?>
            </div>
        </div>
    </div>
    
</div>

<script>
// 탭 메뉴 전환을 위한 자바스크립트 함수
function openTab(evt, tabId) {
    var i, tabcontent, tablinks;
    
    tabcontent = document.getElementsByClassName("tab_content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    
    tablinks = document.getElementsByClassName("tab_nav")[0].getElementsByTagName("button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    
    document.getElementById(tabId).classList.add("active");
    evt.currentTarget.classList.add("active");
}
</script>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>

## 4. 하단 레이아웃 (theme/portal/tail.php)

<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
        </div> <!-- // .inner -->
    </div> <!-- //portal_container -->

    <!-- Footer -->
    <footer id="portal_footer">
        <div class="inner">
            <ul class="footer_links">
                <li><a href="#">회사소개</a></li>
                <li><a href="#">인재채용</a></li>
                <li><a href="#">제휴제안</a></li>
                <li><a href="#">이용약관</a></li>
                <li><a href="#" class="privacy">개인정보처리방침</a></li>
                <li><a href="#">고객센터</a></li>
            </ul>
            <div class="footer_info">
                <span><b>사업자등록번호:</b> 123-45-67890</span>
                <span><b>대표이사:</b> 홍길동</span>
                <span><b>주소:</b> 서울특별시 강남구 테헤란로 123</span><br>
                <div class="copyright">Copyright © <b>YourCompany</b> All rights reserved.</div>
            </div>
        </div>
    </footer>
</div> <!-- //portal_wrap -->

<?php
// 코어의 tail.sub.php를 불러와 </body>, </html> 태그를 닫습니다.
if(file_exists(G5_THEME_PATH.'/tail.sub.php')) {
    include_once(G5_THEME_PATH.'/tail.sub.php');
} else {
    include_once(G5_PATH.'/tail.sub.php');
}
?>

## 5. 포털 레이아웃 스타일 (theme/portal/css/default.css)

@charset "utf-8";

/* Reset & Basic */
body, h1, h2, h3, h4, h5, h6, ul, p { margin: 0; padding: 0; }
body { font-family: 'Malgun Gothic', 'Apple SD Gothic Neo', sans-serif; background-color: #f5f6f7; color: #333; }
a { text-decoration: none; color: #333; }
ul, li { list-style: none; }

/* Layout */
.inner { width: 1130px; margin: 0 auto; }

/* Header */
#portal_header { background: #fff; padding: 30px 0; border-bottom: 1px solid #e4e8eb; }
#portal_header .inner { display: flex; justify-content: space-between; align-items: center; }
#portal_header .logo a { font-size: 36px; font-weight: 900; color: #03c75a; letter-spacing: -1px; }

#portal_header .search_wrap form { display: flex; border: 2px solid #03c75a; border-radius: 4px; overflow: hidden; }
#portal_header .search_wrap input { width: 400px; padding: 12px 15px; border: none; outline: none; font-size: 16px; font-weight: bold; }
#portal_header .search_wrap button { padding: 0 25px; background: #03c75a; color: #fff; border: none; cursor: pointer; font-size: 16px; font-weight: bold; }

#portal_header .top_menu a { font-size: 13px; color: #666; margin-left: 15px; }
#portal_header .top_menu a:hover { text-decoration: underline; }

/* GNB */
#portal_gnb { background: #fff; border-bottom: 1px solid #e4e8eb; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
#portal_gnb .gnb_list { display: flex; height: 50px; align-items: center; gap: 30px; }
#portal_gnb .gnb_list a { font-size: 15px; font-weight: bold; padding: 15px 5px; }
#portal_gnb .gnb_list a:hover { color: #03c75a; }
.gnb_empty a { color:#ff4747 !important; }

/* Container */
#portal_container { padding: 30px 0; min-height: 600px; }

/* Grid Layout (Index) */
.portal_main_grid { display: flex; gap: 30px; }
.portal_main_grid .grid_left { flex: 1; min-width: 0; }
.portal_main_grid .grid_right { width: 350px; flex-shrink: 0; }

/* Left Sections */
.main_banner { height: 250px; background: #e9ecef; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #666; border: 1px solid #dadddf; margin-bottom: 20px;}
.board_section { display: flex; gap: 20px; margin-bottom: 20px; }
.half_board { flex: 1; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e4e8eb; min-height: 200px; }
.half_board h3 { font-size: 16px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #333; }
.gallery_section { background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e4e8eb; min-height: 200px; }

/* Right Sections (Widgets) */
.widget_box { background: #fff; border: 1px solid #e4e8eb; border-radius: 8px; margin-bottom: 15px; overflow: hidden; }
.dummy_widget { height: 180px; display: flex; justify-content: center; align-items: center; color: #999; font-weight: bold; background: #f8f9fa; }
.widget_login { min-height: 120px; }

/* Footer */
#portal_footer { background: #fafbfc; border-top: 1px solid #e4e8eb; padding: 40px 0; }
#portal_footer .footer_links { display: flex; gap: 20px; margin-bottom: 20px; }
#portal_footer .footer_links a { font-size: 13px; color: #666; }
#portal_footer .footer_links a.privacy { font-weight: bold; color: #333; }
#portal_footer .footer_info { font-size: 13px; color: #888; line-height: 1.6; }
#portal_footer .footer_info span { margin-right: 15px; }
#portal_footer .copyright { margin-top: 10px; font-family: tahoma, sans-serif; }
