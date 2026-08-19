<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
if (!defined('MOIDAM_USE_SWIPER')) define('MOIDAM_USE_SWIPER', true);
include_once(G5_THEME_PATH.'/head.php');

/*
 * ──────────────────────────────────────────────────────────────────
 *  기본환경설정 > 여분필드 매핑 (관리자 > 기본환경설정에서 설정)
 *
 *  cf_1_subj  = "탭 게시판 목록"      cf_1  = 게시판 아이디, 쉼표로 여러 개 가능 (예: news,sports,enter,culture,life)
 *  cf_2_subj  = (미사용, 자유롭게 활용 가능)
 *  cf_3_subj  = (미사용, 자유롭게 활용 가능)
 *  cf_4_subj  = "갤러리 게시판"       cf_4  = 게시판 아이디 (예: gallery)
 *  cf_5_subj  = "하단 좌 게시판"      cf_5  = 게시판 아이디, 쉼표로 여러 개 가능 (예: free,notice)
 *  cf_6_subj  = "하단 우 게시판"      cf_6  = 게시판 아이디, 쉼표로 여러 개 가능 (예: qa,life)
 *  cf_7_subj  = "사이드 위젯1 게시판" cf_7  = 게시판 아이디 (예: notice)
 *  cf_8_subj  = "사이드 위젯2 게시판" cf_8  = 게시판 아이디 (예: free)
 *  cf_9_subj  = "개별 블럭1 게시판"   cf_9  = 게시판 아이디 (비워두면 블럭 숨김)
 *  cf_10_subj = "개별 블럭2 게시판"   cf_10 = 게시판 아이디 (비워두면 블럭 숨김)
 * ──────────────────────────────────────────────────────────────────
 */
global $config;

$tab_bos    = array_values(array_filter(array_map('trim', explode(',', $config['cf_1'] ?: 'news,sports,enter'))));
$gallery_bo = trim($config['cf_4']) ?: 'gallery';
$left_bos   = array_values(array_filter(array_map('trim', explode(',', $config['cf_5'] ?: 'free'))));
$right_bos  = array_values(array_filter(array_map('trim', explode(',', $config['cf_6'] ?: 'qa'))));
$notice_bo  = trim($config['cf_7']) ?: 'notice';
$side_bo    = trim($config['cf_8']) ?: 'free';
$extra1_bo  = trim($config['cf_9']);
$extra2_bo  = trim($config['cf_10']);

// 게시판 아이디 → 제목 반환 (존재하지 않으면 아이디 그대로 표시)
$_bo_label = function($bo_table) {
    $b = get_board_db($bo_table, true);
    return ($b && $b['bo_table']) ? get_text($b['bo_subject']) : $bo_table;
};

$gallery_label = $_bo_label($gallery_bo);
$notice_label  = $_bo_label($notice_bo);
$side_label    = $_bo_label($side_bo);
$extra1_label  = $extra1_bo ? $_bo_label($extra1_bo) : '';
$extra2_label  = $extra2_bo ? $_bo_label($extra2_bo) : '';

// 탭 게시판별 라벨/HTML id (영문+숫자+언더바만 허용)
$tab_items = array_map(function($bo) use ($_bo_label) {
    return array(
        'bo'    => $bo,
        'label' => $_bo_label($bo),
        'id'    => 'tab_' . preg_replace('/[^a-z0-9_]/i', '', $bo),
    );
}, $tab_bos);
?>

<?php
$_hero_bg = G5_THEME_URL.'/img/hero_day.jpg';

/* 퀵링크용 게시판 카운트 */
$_ql_boards = array(
    array('bo' => 'daily',   'label' => '일상공유',   'desc' => '오늘의 기록', 'icon' => 'fa-sun-o'),
    array('bo' => 'free',    'label' => '자유게시판', 'desc' => '수다·잡담',   'icon' => 'fa-comments-o'),
    array('bo' => 'food',    'label' => '맛집·먹거리','desc' => '맛있는 발견', 'icon' => 'fa-cutlery'),
    array('bo' => 'hobby',   'label' => '취미·관심사','desc' => '취미 모임',   'icon' => 'fa-heart-o'),
    array('bo' => 'qa',      'label' => '질문답변',   'desc' => '궁금할 때',   'icon' => 'fa-question-circle'),
    array('bo' => 'video',   'label' => '동영상',     'desc' => '영상 모음',   'icon' => 'fa-play-circle'),
);
$_ql_counts = array();
foreach ($_ql_boards as $_qb) {
    $_b = get_board_db($_qb['bo'], true);
    $_ql_counts[$_qb['bo']] = (int)($_b['bo_count_write'] ?? 0);
}
?>

<!-- 시네마틱 히어로 -->
<section class="moidam_hero" style="--moidam-hero-bg: url('<?php echo $_hero_bg ?>');" aria-label="모담 소개">
    <div class="moidam_hero_bg" aria-hidden="true"></div>
    <div class="moidam_hero_overlay" aria-hidden="true"></div>
    <div class="moidam_hero_row">
        <div class="moidam_hero_inner">
            <p class="moidam_hero_eyebrow">모두의 담소 · 모담</p>
            <h1 class="moidam_hero_title">오늘, 모담에서 <br>이야기해요</h1>
            <p class="moidam_hero_sub">모두의 담소가 모이는 곳. <span class="moidam_sub_extra">일상과 사진을 함께 나눠요.</span></p>
            <div class="moidam_hero_ctas">
                <a class="moidam_cta moidam_cta_primary" href="<?php echo G5_BBS_URL ?>/write.php?bo_table=daily">일상 쓰러 가기</a>
                <a class="moidam_cta moidam_cta_ghost" href="<?php echo G5_BBS_URL ?>/board.php?bo_table=daily">일상공유 보기</a>
                <a class="moidam_cta moidam_cta_ghost" href="<?php echo G5_BBS_URL ?>/board.php?bo_table=gallery">갤러리</a>
            </div>
        </div>
        <aside class="moidam_hero_demo" aria-label="테스트 계정 안내">
            <p class="moidam_hero_demo_label">테스트 계정</p>
            <dl class="moidam_hero_demo_dl">
                <div>
                    <dt>아이디</dt>
                    <dd>user1 ~ user9</dd>
                </div>
                <div>
                    <dt>비밀번호</dt>
                    <dd>user1234</dd>
                </div>
            </dl>
            <p class="moidam_hero_demo_hint">데모 계정으로 로그인하세요.</p>
            <a class="moidam_hero_demo_login" href="<?php echo G5_BBS_URL ?>/login.php">로그인</a>
        </aside>
    </div>
</section>

<!-- 포털 스타일 2단 레이아웃 -->
<div class="portal_main_grid">

    <!-- 왼쪽 영역 (메인 콘텐츠) -->
    <div class="grid_left">


        <!-- 게시판 퀵링크 -->
        <section class="moidam_ql_wrap" aria-label="게시판 바로가기">
            <div class="moidam_ql_head">
                <h2 class="moidam_ql_title">어디로 가볼까요?</h2>
                <p class="moidam_ql_desc">자주 찾는 게시판을 한눈에</p>
            </div>
            <nav class="moidam_quicklinks" aria-label="게시판 바로가기">
                <?php foreach ($_ql_boards as $_qb): ?>
                <a class="moidam_ql_card" href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $_qb['bo'] ?>">
                    <span class="moidam_ql_icon" aria-hidden="true"><i class="fa <?php echo $_qb['icon'] ?>"></i></span>
                    <span class="moidam_ql_body">
                        <strong><?php echo $_qb['label'] ?></strong>
                        <em><?php echo $_qb['desc'] ?> · <?php echo number_format($_ql_counts[$_qb['bo']]) ?>글</em>
                    </span>
                </a>
                <?php endforeach; ?>
            </nav>
        </section>

        <!-- 유머 피드 (상단 고정 블록) -->
        <div class="half_board moidam_humor_feed">
            <div class="moidam_section_head">
                <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=humor"><i class="fa fa-smile-o" aria-hidden="true"></i> 오늘의 유머</a></h3>
                <div class="moidam_section_actions">
                    <a class="moidam_text_link" href="<?php echo G5_BBS_URL ?>/board.php?bo_table=humor">더보기</a>
                    <a class="moidam_text_link moidam_text_link_accent" href="<?php echo G5_BBS_URL ?>/write.php?bo_table=humor">유머 쓰기</a>
                </div>
            </div>
            <?php echo latest('theme/news_portal', 'humor', 8, 42); ?>
        </div>

        <!-- 1. 카테고리 탭 전환 방식 최신글 -->
        <div class="portal_tabs" role="region" aria-label="카테고리별 최신글">
            <div class="tab_nav" role="tablist" aria-label="게시판 탭">
                <?php foreach ($tab_items as $_ti => $_tab): ?>
                <button type="button" role="tab" id="btn_<?php echo $_tab['id'] ?>" aria-controls="<?php echo $_tab['id'] ?>" aria-selected="<?php echo $_ti === 0 ? 'true' : 'false' ?>" class="<?php echo $_ti === 0 ? 'active' : '' ?>" onclick="openTab(event, '<?php echo $_tab['id'] ?>')"><?php echo $_tab['label'] ?></button>
                <?php endforeach; ?>
            </div>

            <div class="tab_swipe">
            <div class="tab_swipe_track">
            <?php foreach ($tab_items as $_ti => $_tab): ?>
            <div id="<?php echo $_tab['id'] ?>" class="tab_content<?php echo $_ti === 0 ? ' active' : '' ?>" role="tabpanel" aria-labelledby="btn_<?php echo $_tab['id'] ?>"<?php echo $_ti === 0 ? '' : ' hidden' ?>>
                <?php echo latest('theme/news_portal', $_tab['bo'], 6, 40); ?>
            </div>
            <?php endforeach; ?>
            </div>
            </div>
        </div>

        <!-- 2. 갤러리 방식 최신글 -->
        <div class="gallery_section">
            <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $gallery_bo ?>"><?php echo $gallery_label ?></a></h3>
            <?php echo latest('theme/pic_block', $gallery_bo, 12, 23); ?>
        </div>

        <!-- 2b. 동영상 최신 위젯 -->
        <div class="gallery_section moidam_video_section">
            <div class="moidam_section_head">
                <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=video"><i class="fa fa-play-circle" aria-hidden="true"></i> 동영상</a></h3>
                <div class="moidam_section_actions">
                    <a class="moidam_text_link" href="<?php echo G5_BBS_URL ?>/board.php?bo_table=video">더보기</a>
                    <a class="moidam_text_link moidam_text_link_accent" href="<?php echo G5_BBS_URL ?>/write.php?bo_table=video">영상 올리기</a>
                </div>
            </div>
            <?php echo latest('theme/video_block', 'video', 12, 32); ?>
        </div>

        <!-- 3. 라이프 그리드: 일상 / 맛집 / 취미 / 후기 -->
        <div class="board_section moidam_life_grid">
            <div class="half_col">
                <?php foreach ($left_bos as $_lbo): ?>
                <div class="half_board">
                    <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $_lbo ?>"><?php echo $_bo_label($_lbo) ?></a></h3>
                    <?php echo latest('theme/basic', $_lbo, 5, 28); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="half_col">
                <?php foreach ($right_bos as $_rbo): ?>
                <div class="half_board">
                    <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $_rbo ?>"><?php echo $_bo_label($_rbo) ?></a></h3>
                    <?php echo latest('theme/basic', $_rbo, 5, 28); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. 자유 / 질문답변 -->
        <?php if ($extra1_bo || $extra2_bo): ?>
        <div class="board_section">
            <?php if ($extra1_bo): ?>
            <div class="half_col">
                <div class="half_board">
                    <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $extra1_bo ?>"><?php echo $extra1_label ?></a></h3>
                    <?php echo latest('theme/basic', $extra1_bo, 5, 28); ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($extra2_bo): ?>
            <div class="half_col">
                <div class="half_board">
                    <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $extra2_bo ?>"><?php echo $extra2_label ?></a></h3>
                    <?php echo latest('theme/basic', $extra2_bo, 5, 28); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- 맛집 사진 하이라이트 -->
        <div class="gallery_section moidam_food_pics">
            <h3><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=food">맛집·먹거리 포토</a></h3>
            <?php echo latest('theme/pic_block', 'food', 8, 23); ?>
        </div>

    </div>

    <!-- 오른쪽 영역 (공통 사이드바) -->
    <div class="grid_right">
        <?php include_once(G5_THEME_PATH . '/_sidebar.php'); ?>
    </div>

</div>

<!-- 이용 가이드 + 커뮤니티 안내 -->
<div class="guide_bar">
    <div class="guide_bar_inner">
        <i class="fa fa-info-circle guide_bar_icon" aria-hidden="true"></i>
        <span class="guide_bar_text">오늘 하루, 모담에 남겨볼까요?</span>
        <a href="<?php echo G5_BBS_URL ?>/write.php?bo_table=daily" class="guide_bar_btn">일상 쓰러 가기</a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=daily" class="guide_bar_btn guide_bar_btn_ghost">일상공유 보기</a>
    </div>
</div>

<?php if ($is_admin == 'super'): ?>
<!-- 포털 편집 모달 -->
<div id="portal_editor_overlay" class="pe_overlay" onclick="closePortalEditor(event)" aria-hidden="true">
    <div class="pe_modal" role="dialog" aria-modal="true" aria-labelledby="pe_modal_title">

        <div class="pe_header">
            <span id="pe_modal_title" class="pe_title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> 테마 편집</span>
            <button type="button" class="pe_close" onclick="closePortalEditor()" aria-label="닫기">&times;</button>
        </div>

        <!-- 탭 -->
        <div class="pe_tabs" role="tablist">
            <button class="pe_tab active" id="tab_logo"   role="tab" onclick="switchPeTab(this,'pane_logo'  )">로고</button>
            <button class="pe_tab"        id="tab_banner" role="tab" onclick="switchPeTab(this,'pane_banner')">배너</button>
            <button class="pe_tab"        id="tab_topbanner" role="tab" onclick="switchPeTab(this,'pane_topbanner')">헤더배너</button>
            <button class="pe_tab"        id="tab_footer" role="tab" onclick="switchPeTab(this,'pane_footer')">푸터</button>
            <button class="pe_tab"        id="tab_widget" role="tab" onclick="switchPeTab(this,'pane_widget')">위젯</button>
            <button class="pe_tab"        id="tab_ad"     role="tab" onclick="switchPeTab(this,'pane_ad'    )">광고</button>
            <button class="pe_tab"        id="tab_color"  role="tab" onclick="switchPeTab(this,'pane_color' )">색상</button>
        </div>

        <div class="pe_body">

            <!-- 로고 탭 -->
            <div class="pe_pane active" id="pane_logo">
                <label class="pe_label">로고 텍스트 <span class="pe_hint">(이미지 미설정 시 표시)</span></label>
                <input type="text" id="pe_logo_text" class="pe_input" placeholder="LOGO">

                <label class="pe_label">로고 이미지 URL</label>
                <div class="pe_logo_url_row">
                    <input type="text" id="pe_logo_img" class="pe_input" placeholder="https://...">
                    <button type="button" class="pe_btn_svg_upload" onclick="document.getElementById('pe_logo_svg_file').click()" title="SVG 파일 업로드">
                        <i class="fa fa-upload"></i> SVG 업로드
                    </button>
                    <input type="file" id="pe_logo_svg_file" accept=".svg,image/svg+xml" style="display:none">
                </div>
                <div class="pe_preview_wrap">
                    <img id="pe_logo_preview" src="" alt="" style="display:none;max-height:60px;">
                    <span id="pe_logo_preview_none" class="pe_hint">이미지 URL 입력 시 미리보기</span>
                    <span id="pe_logo_svg_status" class="pe_hint" style="display:none;color:var(--portal-primary);"></span>
                </div>

                <label class="pe_label">로고 링크 URL</label>
                <input type="text" id="pe_logo_link" class="pe_input" placeholder="(비워두면 사이트 홈)">
            </div>

            <!-- 배너 탭 -->
            <div class="pe_pane" id="pane_banner">
                <div id="pe_banner_list"></div>
                <button type="button" class="pe_btn_add" onclick="addPeBanner()"><i class="fa fa-plus" aria-hidden="true"></i> 배너 추가</button>
                <p class="pe_hint" style="margin-top:6px;">최대 10개 · 이미지 URL 필수 · 링크/alt 선택</p>
            </div>

            <!-- 헤더 배너 탭 -->
            <div class="pe_pane" id="pane_topbanner">
                <label class="pe_label">헤더 배너 이미지 <span class="pe_hint">(검색창 옆, 120&times;60px 권장)</span></label>
                <div class="pe_logo_url_row">
                    <input type="text" id="pe_topbanner_img" class="pe_input" placeholder="https://...">
                    <button type="button" class="pe_btn_svg_upload" onclick="document.getElementById('pe_topbanner_file').click()" title="이미지 업로드">
                        <i class="fa fa-upload"></i> 이미지 업로드
                    </button>
                    <input type="file" id="pe_topbanner_file" accept=".jpg,.jpeg,.png,.gif,.webp,image/*" style="display:none">
                </div>
                <div class="pe_preview_wrap">
                    <img id="pe_topbanner_preview" src="" alt="" style="display:none;width:120px;height:60px;object-fit:cover;border-radius:4px;">
                    <span id="pe_topbanner_preview_none" class="pe_hint">이미지 업로드 또는 URL 입력 시 미리보기</span>
                    <span id="pe_topbanner_status" class="pe_hint" style="display:none;color:var(--portal-primary);"></span>
                </div>

                <label class="pe_label" style="margin-top:14px;">링크 URL <span class="pe_hint">(선택)</span></label>
                <input type="text" id="pe_topbanner_link" class="pe_input" placeholder="https://...">

                <label style="margin-top:8px;display:block;">
                    <input type="checkbox" id="pe_topbanner_blank"> 새 탭에서 열기
                </label>

                <label class="pe_label" style="margin-top:14px;">alt 텍스트 <span class="pe_hint">(선택)</span></label>
                <input type="text" id="pe_topbanner_alt" class="pe_input" placeholder="배너 설명">
                <p class="pe_hint">jpg, png, gif, webp · 최대 1MB</p>

                <hr style="margin:20px 0;border:none;border-top:1px solid #eee;">

                <label class="pe_label">배너 제외 회원 아이디</label>
                <input type="text" id="pe_topbanner_exclude_members" class="pe_input" placeholder="예: admin,vip_user1,vip_user2">
                <p class="pe_hint"><i class="fa fa-info-circle"></i> 여기에 등록된 아이디로 로그인한 회원에게는 헤더 배너가 표시되지 않습니다. 쉼표로 구분해 여러 명 등록 가능합니다.</p>
            </div>

            <!-- 푸터 탭 -->
            <div class="pe_pane" id="pane_footer">

                <label class="pe_label">푸터 메뉴 <span class="pe_hint">최대 10개</span></label>
                <div id="pe_footer_menu_list"></div>
                <button type="button" class="pe_btn_add" onclick="addFooterMenuItem()">
                    <i class="fa fa-plus" aria-hidden="true"></i> 메뉴 추가
                </button>

                <label class="pe_label" style="margin-top:18px;">카피라이트</label>
                <input type="text" id="pe_footer_copyright" class="pe_input" placeholder="Copyright &copy; YourCompany">
                <p class="pe_hint">HTML 태그 사용 가능 (예: &lt;b&gt;회사명&lt;/b&gt;)</p>

                <label class="pe_label" style="margin-top:14px;">회사 정보 <span class="pe_hint">(카피라이트 위 라인)</span></label>
                <textarea id="pe_footer_info" class="pe_textarea" rows="4" placeholder="사업자등록번호, 대표이사, 주소 등"></textarea>
                <p class="pe_hint">HTML 태그 사용 가능</p>
            </div>

            <!-- 광고 탭 -->
            <div class="pe_pane" id="pane_ad">
                <label class="pe_label">최상단 광고 스크립트</label>
                <textarea id="pe_top_ad_script" class="pe_textarea" rows="8" placeholder="<!-- 예: Google AdSense 스크립트 태그를 여기에 붙여넣으세요 -->&#10;<script async src=&quot;https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js&quot;></script>&#10;..."></textarea>
                <p class="pe_hint"><i class="fa fa-info-circle"></i> 사이트 로고·검색창 위 최상단에 출력됩니다. 비워두면 광고 영역이 숨겨집니다.</p>
                <p class="pe_hint" style="color:#e53935;"><i class="fa fa-warning"></i> script 태그를 포함한 HTML을 직접 입력하세요. 최고관리자만 수정 가능합니다.</p>

                <hr style="margin:20px 0;border:none;border-top:1px solid #eee;">

                <label class="pe_label">게시판 사이드바 광고 스크립트</label>
                <textarea id="pe_board_sidebar_ad" class="pe_textarea" rows="8" placeholder="<!-- 게시판 사이드바 상단에 표시할 광고 스크립트 -->&#10;<script async src=&quot;https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js&quot;></script>&#10;..."></textarea>
                <p class="pe_hint"><i class="fa fa-info-circle"></i> 게시판(목록·보기·쓰기) 사이드바 최상단에 출력됩니다. 비워두면 광고 영역이 숨겨집니다.</p>
                <p class="pe_hint" style="color:#e53935;"><i class="fa fa-warning"></i> script 태그를 포함한 HTML을 직접 입력하세요. 최고관리자만 수정 가능합니다.</p>

                <hr style="margin:20px 0;border:none;border-top:1px solid #eee;">

                <label class="pe_label">광고 제외 회원 아이디</label>
                <input type="text" id="pe_ad_exclude_members" class="pe_input" placeholder="예: admin,vip_user1,vip_user2">
                <p class="pe_hint"><i class="fa fa-info-circle"></i> 여기에 등록된 아이디로 로그인한 회원에게는 위 광고(최상단·게시판 사이드바)가 모두 표시되지 않습니다. 쉼표로 구분해 여러 명 등록 가능합니다.</p>
            </div>

            <!-- 위젯 탭 -->
            <div class="pe_pane" id="pane_widget">
                <label class="pe_label">최신글 위젯 — 게시판 아이디</label>
                <input type="text" id="pe_side_latest_bo" class="pe_input" placeholder="예: news">
                <p class="pe_hint" style="margin-bottom:14px;">사이드바 최신글 위젯에 표시할 게시판</p>

                <label class="pe_label">인기글 위젯 — 게시판 아이디</label>
                <input type="text" id="pe_side_popular_bo" class="pe_input" placeholder="예: news">

                <label class="pe_label">인기글 기간</label>
                <select id="pe_side_popular_days" class="pe_input" style="height:36px;">
                    <option value="7">최근 7일</option>
                    <option value="30">최근 30일</option>
                    <option value="90">최근 90일</option>
                    <option value="0">전체 기간</option>
                </select>

                <label class="pe_label" style="margin-top:18px;">전체 최신글 — 제외 게시판</label>
                <input type="text" id="pe_latest_exclude" class="pe_input" placeholder="예: gallery,photo,admin">
                <p class="pe_hint">전체 최신글 위젯에서 제외할 게시판 아이디, 쉼표로 구분</p>
            </div>

            <!-- 색상 탭 -->
            <div class="pe_pane" id="pane_color">
                <label class="pe_label">테마 포인트 컬러</label>
                <p class="pe_hint" style="margin-bottom:14px;">로고·버튼·링크 등 사이트 전체에 쓰이는 포인트 컬러를 선택하세요.</p>
                <input type="hidden" id="pe_theme_color" value="default">
                <div class="pe_color_swatches">
                    <?php foreach (PORTAL_THEME_COLOR_PRESETS as $pe_color_key => $pe_color_preset): ?>
                    <button type="button" class="pe_color_swatch" data-color="<?php echo htmlspecialchars($pe_color_key); ?>" style="background-color:<?php echo htmlspecialchars($pe_color_preset['color']); ?>;" title="<?php echo htmlspecialchars($pe_color_preset['label']); ?>">
                        <i class="fa fa-check" aria-hidden="true"></i>
                        <span class="pe_color_swatch_label"><?php echo htmlspecialchars($pe_color_preset['label']); ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

        </div><!-- .pe_body -->

        <div class="pe_footer">
            <span id="pe_msg" class="pe_msg"></span>
            <button type="button" class="pe_btn_save" onclick="savePortalSettings()">
                <i class="fa fa-save" aria-hidden="true"></i> 저장
            </button>
        </div>

    </div>
</div>

<script>
(function(){
    var S = <?php echo json_encode($portal_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?>;
    var saveUrl = '<?php echo G5_THEME_URL; ?>/portal.save.php';

    /* ── 모달 열기/닫기 ── */
    var _editorInit = false;
    window.openPortalEditor = function(tabId) {
        if (!_editorInit) { fillForm(S); _editorInit = true; }
        document.getElementById('portal_editor_overlay').classList.add('open');
        document.body.classList.add('pe_lock');
        if (tabId) {
            var btn = document.getElementById(tabId);
            if (btn) btn.click();
        }
    };
    window.closePortalEditor = function(e) {
        if (e && e.target !== document.getElementById('portal_editor_overlay')) return;
        document.getElementById('portal_editor_overlay').classList.remove('open');
        document.body.classList.remove('pe_lock');
    };

    /* ── 탭 전환 ── */
    window.switchPeTab = function(btn, paneId) {
        document.querySelectorAll('.pe_tab').forEach(function(t){ t.classList.remove('active'); });
        document.querySelectorAll('.pe_pane').forEach(function(p){ p.classList.remove('active'); });
        btn.classList.add('active');
        document.getElementById(paneId).classList.add('active');
    };

    /* ── 폼 채우기 ── */
    function fillForm(s) {
        document.getElementById('pe_logo_text').value  = s.logo_text  || '';
        document.getElementById('pe_logo_img').value   = s.logo_img   || '';
        document.getElementById('pe_logo_link').value  = s.logo_link  || '';
        document.getElementById('pe_footer_copyright').value  = s.footer_copyright   || '';
        document.getElementById('pe_footer_info').value       = s.footer_info        || '';
        document.getElementById('pe_side_latest_bo').value    = s.side_latest_bo     || '';
        document.getElementById('pe_side_popular_bo').value   = s.side_popular_bo    || '';
        var spd = document.getElementById('pe_side_popular_days');
        spd.value = s.side_popular_days !== undefined ? String(s.side_popular_days) : '30';
        document.getElementById('pe_latest_exclude').value = (s.latest_exclude || []).join(',');
        document.getElementById('pe_top_ad_script').value       = s.top_ad_script      || '';
        document.getElementById('pe_board_sidebar_ad').value  = s.board_sidebar_ad   || '';
        document.getElementById('pe_ad_exclude_members').value = (s.ad_exclude_members || []).join(',');
        updateLogoPreview();
        renderBannerList(s.banners || []);
        renderFooterMenuList(s.footer_menu || []);

        var tb = s.top_banner || {};
        document.getElementById('pe_topbanner_img').value    = tb.img  || '';
        document.getElementById('pe_topbanner_link').value   = tb.link || '';
        document.getElementById('pe_topbanner_alt').value    = tb.alt  || '';
        document.getElementById('pe_topbanner_blank').checked = tb.target === '_blank';
        document.getElementById('pe_topbanner_exclude_members').value = (tb.exclude_members || []).join(',');
        updateTopBannerPreview();

        setThemeColor(s.theme_color || 'default');
    }

    /* ── 테마 포인트 컬러 스와치 ── */
    function setThemeColor(key) {
        document.getElementById('pe_theme_color').value = key;
        document.querySelectorAll('.pe_color_swatch').forEach(function(btn) {
            btn.classList.toggle('selected', btn.getAttribute('data-color') === key);
        });
    }
    document.querySelectorAll('.pe_color_swatch').forEach(function(btn) {
        btn.addEventListener('click', function() {
            setThemeColor(btn.getAttribute('data-color'));
        });
    });

    /* ── 로고 미리보기 ── */
    function updateLogoPreview() {
        var url  = document.getElementById('pe_logo_img').value.trim();
        var prev = document.getElementById('pe_logo_preview');
        var none = document.getElementById('pe_logo_preview_none');
        if (url) {
            prev.src = url;
            prev.style.display = 'block';
            none.style.display = 'none';
        } else {
            prev.style.display = 'none';
            none.style.display = '';
        }
    }
    document.getElementById('pe_logo_img').addEventListener('input', updateLogoPreview);

    /* ── 헤더 배너 미리보기 ── */
    function updateTopBannerPreview() {
        var url  = document.getElementById('pe_topbanner_img').value.trim();
        var prev = document.getElementById('pe_topbanner_preview');
        var none = document.getElementById('pe_topbanner_preview_none');
        if (url) {
            prev.src = url;
            prev.style.display = 'block';
            none.style.display = 'none';
        } else {
            prev.style.display = 'none';
            none.style.display = '';
        }
    }
    document.getElementById('pe_topbanner_img').addEventListener('input', updateTopBannerPreview);

    /* ── 헤더 배너 이미지 업로드 ── */
    document.getElementById('pe_topbanner_file').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        var status = document.getElementById('pe_topbanner_status');
        var none   = document.getElementById('pe_topbanner_preview_none');
        status.textContent = '업로드 중...';
        status.style.display = '';
        status.style.color = 'var(--portal-primary)';
        none.style.display = 'none';
        var fd = new FormData();
        fd.append('type', 'top_banner');
        fd.append('file', file);
        fetch('<?php echo G5_THEME_URL ?>/portal.upload.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(res) {
                if (res.ok) {
                    document.getElementById('pe_topbanner_img').value = res.url;
                    status.textContent = '업로드 완료!';
                    setTimeout(function(){ status.style.display = 'none'; }, 2000);
                    updateTopBannerPreview();
                } else {
                    status.textContent = res.msg || '업로드 실패';
                    status.style.color = '#e53935';
                }
            })
            .catch(function() {
                status.textContent = '업로드 중 오류가 발생했습니다.';
                status.style.color = '#e53935';
            });
        this.value = '';
    });

    /* ── SVG 로고 업로드 ── */
    document.getElementById('pe_logo_svg_file').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        var status = document.getElementById('pe_logo_svg_status');
        var none   = document.getElementById('pe_logo_preview_none');
        status.textContent = '업로드 중...';
        status.style.display = '';
        none.style.display = 'none';
        var fd = new FormData();
        fd.append('type', 'logo_svg');
        fd.append('file', file);
        fetch('<?php echo G5_THEME_URL ?>/portal.upload.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(res) {
                if (res.ok) {
                    document.getElementById('pe_logo_img').value = res.url;
                    status.textContent = '업로드 완료!';
                    setTimeout(function(){ status.style.display = 'none'; }, 2000);
                    updateLogoPreview();
                } else {
                    status.textContent = res.msg || '업로드 실패';
                    status.style.color = '#e53935';
                }
            })
            .catch(function() {
                status.textContent = '업로드 중 오류가 발생했습니다.';
                status.style.color = '#e53935';
            });
        this.value = '';
    });

    /* ── 배너 목록 렌더링 ── */
    function renderBannerList(banners) {
        var list = document.getElementById('pe_banner_list');
        list.innerHTML = '';
        banners.forEach(function(b, i){ list.appendChild(makeBannerRow(b, i)); });
    }
    function makeBannerRow(b, i) {
        var px = (b.pos_x !== undefined) ? b.pos_x : 50;
        var py = (b.pos_y !== undefined) ? b.pos_y : 50;
        var row = document.createElement('div');
        row.className = 'pe_banner_row';
        row.innerHTML =
            '<div class="pe_banner_num">'+(i+1)+'</div>' +
            '<div class="pe_banner_fields">' +
                '<input type="text" class="pe_input pe_bn_img"    placeholder="이미지 URL (필수)" value="'+esc(b.img||'')+'"><br>' +
                '<input type="text" class="pe_input pe_bn_link"   placeholder="링크 URL (선택)" value="'+esc(b.link||'')+'"><br>' +
                '<input type="text" class="pe_input pe_bn_alt"    placeholder="alt 텍스트 (선택)" value="'+esc(b.alt||'')+'"><br>' +
                '<label><input type="checkbox" class="pe_bn_blank"'+(b.target==='_blank'?' checked':'')+'>새 탭에서 열기</label>' +
                '<div class="pe_bn_pos_row">' +
                    '<label class="pe_bn_pos_label">X(좌우) <input type="range" class="pe_bn_pos_x" min="0" max="100" value="'+px+'"> <span class="pe_bn_pos_val">'+px+'</span>%</label>' +
                    '<label class="pe_bn_pos_label">Y(상하) <input type="range" class="pe_bn_pos_y" min="0" max="100" value="'+py+'"> <span class="pe_bn_pos_val">'+py+'</span>%</label>' +
                '</div>' +
            '</div>' +
            '<button type="button" class="pe_banner_del" onclick="this.closest(\'.pe_banner_row\').remove();renumberBanners();" aria-label="삭제">&times;</button>';
        row.querySelectorAll('.pe_bn_pos_row input[type=range]').forEach(function(r) {
            r.addEventListener('input', function() {
                this.nextElementSibling.textContent = this.value;
            });
        });
        return row;
    }
    window.addPeBanner = function() {
        var list = document.getElementById('pe_banner_list');
        if (list.children.length >= 10) { alert('배너는 최대 10개까지 등록할 수 있습니다.'); return; }
        list.appendChild(makeBannerRow({img:'',link:'',alt:'',target:'_self'}, list.children.length));
    };
    window.renumberBanners = function() {
        document.querySelectorAll('#pe_banner_list .pe_banner_num').forEach(function(el, i){ el.textContent = i+1; });
    };

    /* ── 푸터 메뉴 ── */
    function renderFooterMenuList(items) {
        var list = document.getElementById('pe_footer_menu_list');
        list.innerHTML = '';
        items.forEach(function(m){ list.appendChild(makeFooterMenuRow(m)); });
    }
    function makeFooterMenuRow(m) {
        var row = document.createElement('div');
        row.className = 'pe_fmenu_row';
        row.innerHTML =
            '<span class="pe_fmenu_drag" title="드래그로 순서 변경">&#9776;</span>' +
            '<input type="text" class="pe_input pe_fm_label" placeholder="메뉴명" value="'+esc(m.label||'')+'">' +
            '<input type="text" class="pe_input pe_fm_url"   placeholder="URL (# 또는 https://...)" value="'+esc(m.url||'')+'">' +
            '<input type="text" class="pe_input pe_fm_class" placeholder="CSS 클래스 (선택, 예: privacy)" value="'+esc(m.class||'')+'" style="width:120px">' +
            '<button type="button" class="pe_banner_del" onclick="this.closest(\'.pe_fmenu_row\').remove();" aria-label="삭제">&times;</button>';
        return row;
    }
    window.addFooterMenuItem = function() {
        var list = document.getElementById('pe_footer_menu_list');
        if (list.children.length >= 10) { alert('메뉴는 최대 10개까지 등록할 수 있습니다.'); return; }
        list.appendChild(makeFooterMenuRow({label:'', url:'#', class:''}));
    };

    /* ── 푸터 메뉴 드래그앤드롭 ── */
    (function() {
        var list = document.getElementById('pe_footer_menu_list');
        var dragging = null;

        // 핸들(☰)에서 마우스다운 시에만 해당 행의 draggable 활성화
        list.addEventListener('mousedown', function(e) {
            var handle = e.target.closest('.pe_fmenu_drag');
            if (!handle) return;
            handle.closest('.pe_fmenu_row').setAttribute('draggable', 'true');
        });

        // draggable이 아닌 행에서 dragstart가 발생하면 차단
        list.addEventListener('dragstart', function(e) {
            var row = e.target.closest('.pe_fmenu_row');
            if (!row || row.getAttribute('draggable') !== 'true') { e.preventDefault(); return; }
            dragging = row;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', '');
            setTimeout(function() { row.classList.add('pe_fmenu_dragging'); }, 0);
        });

        // 다른 행 위에서 위치 판단 후 즉시 재삽입
        list.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!dragging) return;
            var row = e.target.closest('.pe_fmenu_row');
            if (!row || row === dragging) return;
            var rect = row.getBoundingClientRect();
            if (e.clientY < rect.top + rect.height / 2) {
                list.insertBefore(dragging, row);
            } else {
                list.insertBefore(dragging, row.nextElementSibling);
            }
        });

        // 드래그 종료 시 스타일 정리 및 draggable 해제
        list.addEventListener('dragend', function() {
            if (dragging) {
                dragging.classList.remove('pe_fmenu_dragging');
                dragging.removeAttribute('draggable');
                dragging = null;
            }
        });

        // 드롭 없이 취소된 경우(Esc 등)도 정리
        document.addEventListener('mouseup', function() {
            list.querySelectorAll('.pe_fmenu_row[draggable]').forEach(function(r) {
                r.removeAttribute('draggable');
            });
        });
    })();

    /* WAF 우회용 base64 인코딩 (한글 포함) */
    function _b64enc(str) {
        return btoa(unescape(encodeURIComponent(str)));
    }

    /* ── 저장 ── */
    window.savePortalSettings = function() {
        var msg  = document.getElementById('pe_msg');
        var btn  = document.querySelector('.pe_btn_save');
        msg.textContent = '저장 중…';
        msg.className = 'pe_msg';
        btn.disabled = true;

        var banners = [];
        document.querySelectorAll('#pe_banner_list .pe_banner_row').forEach(function(row){
            banners.push({
                img:    row.querySelector('.pe_bn_img').value.trim(),
                link:   row.querySelector('.pe_bn_link').value.trim(),
                alt:    row.querySelector('.pe_bn_alt').value.trim(),
                target: row.querySelector('.pe_bn_blank').checked ? '_blank' : '_self',
                pos_x:  parseInt(row.querySelector('.pe_bn_pos_x').value, 10) || 50,
                pos_y:  parseInt(row.querySelector('.pe_bn_pos_y').value, 10) || 50,
            });
        });

        var topBannerExclude = document.getElementById('pe_topbanner_exclude_members').value.trim();
        var topBanner = {
            img:             document.getElementById('pe_topbanner_img').value.trim(),
            link:            document.getElementById('pe_topbanner_link').value.trim(),
            alt:             document.getElementById('pe_topbanner_alt').value.trim(),
            target:          document.getElementById('pe_topbanner_blank').checked ? '_blank' : '_self',
            exclude_members: topBannerExclude ? topBannerExclude.split(',').map(function(x){ return x.trim(); }).filter(Boolean) : [],
        };

        var footerMenu = [];
        document.querySelectorAll('#pe_footer_menu_list .pe_fmenu_row').forEach(function(row){
            var label = row.querySelector('.pe_fm_label').value.trim();
            var url   = row.querySelector('.pe_fm_url').value.trim();
            if (!label && !url) return;
            footerMenu.push({
                label: label,
                url:   url || '#',
                class: row.querySelector('.pe_fm_class').value.trim(),
            });
        });

        var body = new URLSearchParams({
            logo_text:          document.getElementById('pe_logo_text').value,
            logo_img:           document.getElementById('pe_logo_img').value,
            logo_link:          document.getElementById('pe_logo_link').value,
            footer_copyright:   _b64enc(document.getElementById('pe_footer_copyright').value),
            footer_info:        _b64enc(document.getElementById('pe_footer_info').value),
            side_latest_bo:     document.getElementById('pe_side_latest_bo').value,
            side_popular_bo:    document.getElementById('pe_side_popular_bo').value,
            side_popular_days:  document.getElementById('pe_side_popular_days').value,
            latest_exclude:     document.getElementById('pe_latest_exclude').value.trim(),
            banners:            _b64enc(JSON.stringify(banners)),
            top_banner:         _b64enc(JSON.stringify(topBanner)),
            footer_menu:        _b64enc(JSON.stringify(footerMenu)),
            top_ad_script:      _b64enc(document.getElementById('pe_top_ad_script').value),
            board_sidebar_ad:   _b64enc(document.getElementById('pe_board_sidebar_ad').value),
            ad_exclude_members: document.getElementById('pe_ad_exclude_members').value.trim(),
            theme_color:        document.getElementById('pe_theme_color').value,
        });

        fetch(saveUrl, { method: 'POST', body: body,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' } })
        .then(function(r){ return r.json(); })
        .then(function(res){
            btn.disabled = false;
            if (res.ok) {
                msg.textContent = '저장되었습니다.';
                msg.className = 'pe_msg ok';
                S.logo_text        = document.getElementById('pe_logo_text').value;
                S.logo_img         = document.getElementById('pe_logo_img').value;
                S.logo_link        = document.getElementById('pe_logo_link').value;
                S.footer_copyright = document.getElementById('pe_footer_copyright').value;
                S.footer_info      = document.getElementById('pe_footer_info').value;
                S.banners           = banners.filter(function(b){ return b.img; });
                S.top_banner        = topBanner;
                S.footer_menu       = footerMenu;
                S.side_latest_bo    = document.getElementById('pe_side_latest_bo').value;
                S.side_popular_bo   = document.getElementById('pe_side_popular_bo').value;
                S.side_popular_days = parseInt(document.getElementById('pe_side_popular_days').value, 10);
                var exc = document.getElementById('pe_latest_exclude').value.trim();
                S.latest_exclude = exc ? exc.split(',').map(function(x){ return x.trim(); }).filter(Boolean) : [];
                S.top_ad_script = document.getElementById('pe_top_ad_script').value;
                S.board_sidebar_ad = document.getElementById('pe_board_sidebar_ad').value;
                var adExc = document.getElementById('pe_ad_exclude_members').value.trim();
                S.ad_exclude_members = adExc ? adExc.split(',').map(function(x){ return x.trim(); }).filter(Boolean) : [];
                S.theme_color = document.getElementById('pe_theme_color').value;
                setTimeout(function(){ location.reload(); }, 900);
            } else {
                msg.textContent = res.msg || '저장 실패';
                msg.className = 'pe_msg err';
            }
        })
        .catch(function(){
            btn.disabled = false;
            msg.textContent = '네트워크 오류가 발생했습니다.';
            msg.className = 'pe_msg err';
        });
    };

    function esc(s) { return s.replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
})();
</script>
<?php endif; ?>

<script>
function openTab(evt, tabId) {
    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tab_content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
        tabcontent[i].hidden = true;
    }

    tablinks = document.getElementsByClassName("tab_nav")[0].getElementsByTagName("button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
        tablinks[i].setAttribute("aria-selected", "false");
    }

    var panel = document.getElementById(tabId);
    panel.classList.add("active");
    panel.hidden = false;
    evt.currentTarget.classList.add("active");
    evt.currentTarget.setAttribute("aria-selected", "true");
}

/* ── 배너 슬라이더 ── */
(function(){
    var track  = document.getElementById('banner_track');
    if (!track) return;
    var slides = track.querySelectorAll('.banner_slide');
    var dots   = document.querySelectorAll('#portal_banner_wrap .banner_dot');
    var total  = slides.length;
    if (total < 2) return;

    var cur = 0, timer;

    function go(idx) {
        cur = (idx + total) % total;
        track.style.transform = 'translateX(-' + (cur * 100) + '%)';
        dots.forEach(function(d, i){ d.classList.toggle('on', i === cur); });
    }

    function startAuto() { timer = setInterval(function(){ go(cur + 1); }, 4500); }
    function stopAuto()  { clearInterval(timer); }

    var prev = document.querySelector('#portal_banner_wrap .banner_prev');
    var next = document.querySelector('#portal_banner_wrap .banner_next');
    if (prev) prev.addEventListener('click', function(){ stopAuto(); go(cur - 1); startAuto(); });
    if (next) next.addEventListener('click', function(){ stopAuto(); go(cur + 1); startAuto(); });
    dots.forEach(function(d){ d.addEventListener('click', function(){ stopAuto(); go(+this.dataset.idx); startAuto(); }); });

    startAuto();
})();
</script>

<?php include_once(G5_THEME_PATH.'/tail.php'); ?>
