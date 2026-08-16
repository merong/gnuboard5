<?php
// 1. 경로 설정 및 독립 실행 여부 확인
if (!defined('_GNUBOARD_')) {
    include_once('../../_common.php');
    $is_standalone = true;
} else {
    $is_standalone = false;
}

// 2. 내용관리 ID 연동 (company)
$co_id = 'company'; 
$co = get_content_db($co_id);

// 제목 설정
$g5['title'] = (isset($co['co_subject']) && $co['co_subject']) ? $co['co_subject'] : "마루밑다락방 소개";

// 독립 실행 시에만 헤더 호출
if ($is_standalone) {
    include_once(G5_THEME_PATH.'/head.php');
}

// 상단 히로 섹션 배경 이미지
$about_bg = G5_THEME_URL . '/img/ghibli_banner_1.jpg';
?>

<style>
    /* [핵심] 그누보드 기본 관리자 톱니바퀴 버튼 숨기기 */
    #ctt_admin, 
    .ctt_admin, 
    .btn_admin { 
        display: none !important; 
    }

    /* 전체 컨테이너 설정 */
    #attic_about { 
        position: relative !important; 
        display: block !important;
        font-family: 'Malgun Gothic', sans-serif; 
        color: #333; 
        line-height: 1.6; 
        background: #fff; 
        width: 100%;
        margin: 0;
        padding: 0;
    }
    
    /* 우리가 만든 커스텀 관리 버튼 (우측 상단 고정) */
    .about_admin_btn {
        position: absolute !important;
        top: 30px !important;
        right: 30px !important;
        left: auto !important;
        z-index: 999 !important;
        display: block !important;
    }
    .about_admin_btn a {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 42px !important;
        height: 42px !important;
        background: rgba(0, 0, 0, 0.4) !important;
        color: #fff !important;
        border-radius: 50% !important;
        text-decoration: none !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        transition: all 0.3s ease;
    }
    .about_admin_btn a:hover {
        background: #03c75a !important;
        border-color: #03c75a !important;
        transform: rotate(90deg);
    }

    /* 히로 섹션 (배너) */
    .about_hero { 
        position: relative; 
        background: #999 url('<?php echo $about_bg; ?>') no-repeat center center; 
        background-size: cover; 
        height: 380px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        text-align: center; 
        color: #fff;
    }
    .about_hero::after { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.45); }
    .hero_content { position: relative; z-index: 2; }
    .hero_content h2 { font-size: 40px; font-weight: 800; margin-bottom: 12px; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
    .hero_content p { font-size: 18px; opacity: 0.9; font-weight: 300; }

    /* 방문자 통계 바 */
    .about_stats { background: #03c75a; color: #fff; padding: 25px 0; text-align: center; }
    .stats_inner { display: flex; justify-content: center; align-items: center; gap: 60px; max-width: 1130px; margin: 0 auto; }
    .stat_item b { font-size: 28px; font-family: 'Tahoma', sans-serif; }
    .stat_item span { font-size: 13px; opacity: 0.85; display: block; margin-bottom: 2px; }

    /* 메인 본문 */
    .about_main_content { 
        padding: 60px 0; 
        max-width: 1000px; 
        margin: 0 auto; 
        min-height: 400px; 
    }

    .content_body { 
        font-size: 16px; 
        color: #444; 
        word-break: break-all;
    }
    
    /* 들여쓰기 강제 초기화 */
    .content_body p, 
    .content_body div, 
    .content_body span { 
        margin-left: 0 !important; 
        padding-left: 0 !important; 
        text-indent: 0 !important; 
    }

    .content_body p { margin-bottom: 20px; line-height: 1.8; }
    .content_body img { 
        max-width: 100% !important; 
        height: auto !important; 
        display: block; 
        margin: 25px 0 !important; 
        border-radius: 8px; 
    }

    .about_footer_sign { margin-top: 60px; padding-top: 30px; border-top: 1px solid #eee; text-align: right; }
    .about_footer_sign b { font-size: 18px; color: #222; display: block; margin-top: 5px; }

    @media (max-width: 768px) {
        .about_hero { height: 260px; }
        .hero_content h2 { font-size: 26px; }
        .stats_inner { flex-direction: column; gap: 15px; }
        .about_main_content { padding: 40px 20px; }
        .about_admin_btn { top: 15px !important; right: 15px !important; }
    }
</style>

<article id="attic_about">
    <!-- 우리가 만든 커스텀 관리 버튼 (우측 상단) -->
    <?php if ($is_admin) { ?>
    <div class="about_admin_btn">
        <a href="<?php echo G5_ADMIN_URL; ?>/contentform.php?w=u&amp;co_id=<?php echo $co_id; ?>" title="내용 수정">
            <i class="fa fa-cog" aria-hidden="true"></i>
        </a>
    </div>
    <?php } ?>

    <section class="about_hero">
        <div class="hero_content">
            <h2><?php echo ($co['co_subject']) ? $co['co_subject'] : "마루밑다락방"; ?></h2>
            <p>인문학적 영감과 AI 기술이 공존하는 공간</p>
        </div>
    </section>

    <section class="about_stats">
        <div class="stats_inner">
            <div class="stat_item">
                <span>누적 방문객</span>
                <b>9,111,956+</b>
            </div>
            <div class="stat_item">
                <span>어제 방문객</span>
                <b>3,232</b>
            </div>
            <div class="stat_item">
                <span>Since</span>
                <b>2011</b>
            </div>
        </div>
    </section>

    <div class="about_main_content">
        <div class="content_body">
            <?php 
            if (isset($co['co_id']) && $co['co_id']) {
                echo conv_content($co['co_content'], 1); 
            } else {
                echo "<p style='text-align:center; padding:50px 0;'>관리자 페이지에서 <b>ID: company</b>로 내용을 등록해 주세요.</p>";
            }
            ?>
        </div>
        <div class="about_footer_sign">
            <span>인문학적 영감, AI로 꽃피우다</span>
            <b>마루밑다락방 운영진 일동.</b>
        </div>
    </div>
</article>

<?php
if ($is_standalone) {
    include_once(G5_THEME_PATH.'/tail.php');
}
?>
