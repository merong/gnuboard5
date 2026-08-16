<?php
// 1. 경로 설정 및 독립 실행 여부 확인
if (!defined('_GNUBOARD_')) {
    include_once('../../_common.php');
    $is_standalone = true;
} else {
    $is_standalone = false;
}

// 제목 설정
$g5['title'] = "이용 가이드";

// 독립 실행 시에만 헤더 호출
if ($is_standalone) {
    include_once(G5_THEME_PATH.'/head.php');
}

// 상단 히로 섹션 배경 이미지
$guide_bg = G5_THEME_URL . '/img/ghibli_banner_2.jpg';
?>

<style>
    #ctt_admin, .ctt_admin, .btn_admin { display: none !important; }

    #attic_guide { 
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
    
    .guide_hero { 
        position: relative; 
        background: #999 url('<?php echo $guide_bg; ?>') no-repeat center center; 
        background-size: cover; 
        height: 350px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        text-align: center; 
        color: #fff;
    }
    .guide_hero::after { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.45); }
    .hero_content { position: relative; z-index: 2; }
    .hero_content h2 { font-size: 38px; font-weight: 800; margin-bottom: 10px; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
    .hero_content p { font-size: 18px; opacity: 0.9; font-weight: 300; }

    .guide_icons { background: #f8f9fa; padding: 40px 0; border-bottom: 1px solid #eee; }
    .icons_inner { display: flex; justify-content: center; align-items: center; gap: 80px; max-width: 1130px; margin: 0 auto; }
    .icon_item { text-align: center; }
    .icon_item i { font-size: 32px; color: #03c75a; margin-bottom: 15px; display: block; }
    .icon_item span { font-size: 15px; font-weight: bold; color: #555; }

    .guide_main_content { padding: 60px 0; max-width: 1000px; margin: 0 auto; min-height: 400px; }
    .content_body h3 { font-size: 22px; margin-top: 50px; margin-bottom: 25px; color: #111; font-weight: 800; display: flex; align-items: center; }
    .content_body h3:first-child { margin-top: 0; }
    .content_body h3:before { content: ''; display: inline-block; width: 4px; height: 18px; background: #03c75a; margin-right: 12px; border-radius: 2px; }
    
    .guide_list { margin-bottom: 50px; }
    .guide_list dt { font-size: 18px; font-weight: bold; color: #03c75a; margin-bottom: 10px; }
    .guide_list dd { margin-left: 0; margin-bottom: 30px; font-size: 16px; color: #555; }

    /* 운영 정책 아티클 스타일 */
    .policy_article { margin-bottom: 40px; }
    .policy_article h4 { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 1px solid #eee; }
    .policy_text { font-size: 15px; color: #555; line-height: 1.8; margin-bottom: 20px; }
    .policy_sublist { list-style: none; padding: 0; margin: 15px 0; background: #fafafa; border-radius: 8px; padding: 20px; }
    .policy_sublist li { position: relative; padding-left: 15px; margin-bottom: 8px; font-size: 14px; }
    .policy_sublist li:before { content: '•'; position: absolute; left: 0; color: #03c75a; font-weight: bold; }
    .policy_sublist li:last-child { margin-bottom: 0; }

    /* 포인트 테이블 스타일 */
    .point_table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border-top: 2px solid #03c75a; }
    .point_table th, .point_table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; font-size: 15px; }
    .point_table th { background: #fcfcfc; color: #111; width: 70%; }
    .point_table td { text-align: right; font-weight: bold; color: #03c75a; }
    .point_table .minus { color: #ff3a48; }

    .history_box { background: #f9f9f9; padding: 25px; border-radius: 10px; margin-bottom: 40px; border: 1px solid #eee; }
    .history_box p { margin-bottom: 8px; font-size: 14px; color: #666; }
    .history_box p:last-child { margin-bottom: 0; }
    .history_box b { color: #333; }

    .caution_box { background: #fff1f1; border: 1px solid #ffdada; padding: 20px; border-radius: 8px; margin-top: 20px; }
    .caution_box h5 { color: #d32f2f; margin-bottom: 10px; font-weight: bold; }
    .caution_box p { font-size: 14px; color: #616161; margin: 0; }

    .guide_footer_sign { margin-top: 60px; padding-top: 30px; border-top: 1px solid #eee; text-align: center; }
    .guide_footer_sign p { color: #888; font-size: 14px; }

    @media (max-width: 768px) {
        .guide_hero { height: 240px; }
        .hero_content h2 { font-size: 26px; }
        .icons_inner { gap: 30px; flex-wrap: wrap; }
        .icon_item { width: 40%; }
        .guide_main_content { padding: 40px 20px; }
    }
</style>

<article id="attic_guide">
    <section class="guide_hero">
        <div class="hero_content">
            <h2>이용 가이드</h2>
            <p>그누엑스퍼트를 즐겁게 이용하는 방법</p>
        </div>
    </section>

    <!-- 핵심 이용 수칙 아이콘 -->
    <section class="guide_icons">
        <div class="icons_inner">
            <div class="icon_item">
                <i class="fa fa-pencil-square-o"></i>
                <span>매너 있는 글쓰기</span>
            </div>
            <div class="icon_item">
                <i class="fa fa-shield"></i>
                <span>개인정보 보호</span>
            </div>
            <div class="icon_item">
                <i class="fa fa-users"></i>
                <span>존중하는 토론</span>
            </div>
            <div class="icon_item">
                <i class="fa fa-check-circle"></i>
                <span>운영 원칙 준수</span>
            </div>
        </div>
    </section>

    <div class="guide_main_content">
        <div class="content_body">
            
            <h3>커뮤니티 이용 수칙</h3>
            <dl class="guide_list">
                <dt>01. 상호 존중과 배려</dt>
                <dd>그누엑스퍼트는 다양한 연령대와 관점을 가진 분들이 모이는 곳입니다. 비속어, 비하 발언을 자제하고 상대방의 의견을 존중하는 품격 있는 토론 문화를 함께 만들어주세요.</dd>
                <dt>02. 주제에 맞는 게시판 이용</dt>
                <dd>역사, 철학, 사진 등 각 게시판의 주제에 맞는 글을 작성해 주세요. 홍보성 게시물이나 도배성 글은 운영 원칙에 따라 제한될 수 있습니다.</dd>
                <dt>03. 저작권 및 출처 표기</dt>
                <dd>외부 자료를 인용하거나 이미지를 사용할 때는 반드시 출처를 표기해 주세요. 타인의 지적 재산권을 존중하는 것이 인문학 정신의 시작입니다.</dd>
                <dt>04. 개인정보 보호</dt>
                <dd>공개된 게시판에 본인 또는 타인의 연락처, 주소 등 민감한 개인정보를 노출하지 않도록 주의해 주시기 바랍니다.</dd>
            </dl>

            <h3>통합검색 이용 안내</h3>
            <dl class="guide_list">
                <dt>01. 통합검색</dt>
                <dd>상단 검색창에 검색어를 입력하면 접근 가능한 모든 게시판에서 제목·내용에 검색어가 포함된 글을 게시판별로 모아 보여줍니다. 각 결과에는 본문 요약과 사진이 있는 글의 경우 썸네일이 함께 표시되며, "더보기"를 누르면 해당 게시판의 전체 검색 결과를 페이지 단위로 이어서 볼 수 있습니다.</dd>
                <dt>02. 이미지 검색</dt>
                <dd>검색어가 포함된 게시글 중 사진이나 이미지가 첨부된 글만 모아 갤러리 형태로 보여드립니다.</dd>
                <dt>03. 웹문서 검색</dt>
                <dd>외부 사이트 운영자가 <a href="<?php echo G5_PLUGIN_URL; ?>/portal-search-page/webmaster.php" target="_blank">웹마스터 도구</a>에서 소유확인을 마친 사이트의 페이지를 모아 보여주는 탭입니다.</dd>
                <dt>04. 사이트 검색</dt>
                <dd>그누엑스퍼트와 함께하는 협력·자매 사이트를 모아둔 탭입니다. 검색어와 관련된 사이트는 통합검색 결과 상단에도 함께 노출됩니다.</dd>
                <dt>05. 뉴스 검색</dt>
                <dd>제휴 언론사의 RSS를 통해 수집한 기사 중 검색어와 관련된 뉴스를 모아볼 수 있는 탭입니다. 최신 기사일수록 먼저 노출되며, 통합검색 결과 상단에도 함께 소개됩니다.</dd>
            </dl>
            <p style="font-size: 14px; color: #888;">※ 내 사이트를 웹문서·뉴스 게시자로 직접 등록하고 싶다면 <a href="<?php echo G5_PLUGIN_URL; ?>/portal-search-page/webmaster.php" target="_blank"><b>웹마스터 도구</b></a>에서 신청할 수 있습니다. 사이트 소유확인(메타태그 또는 인증파일)을 마치면 자동으로 검색 결과에 반영됩니다.</p>

            <h3>포인트 정책</h3>
            <div class="history_box">
                <p>그누엑스퍼트는 다음과 같은 기준으로 커뮤니티 활동 시 포인트를 적립 및 차감합니다.</p>
                <p>• <b>포인트 사용:</b> 사용함</p>
                <p>• <b>포인트 유효기간:</b> 제한 없음</p>
            </div>

            <table class="point_table">
                <thead>
                    <tr>
                        <th>활동 항목 (포인트 단위 : P)</th>
                        <th>포인트</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><th>회원가입 시</th><td>1,000 P</td></tr>
                    <tr><th>로그인 시</th><td>100 P</td></tr>
                    <tr><th>쪽지 보내기</th><td class="minus">- 500 P</td></tr>
                    <tr><th>질문답변(Q&amp;A) · 갤러리 게시판 글쓰기</th><td>5 P</td></tr>
                    <tr><th>질문답변(Q&amp;A) · 갤러리 게시판 댓글 쓰기</th><td>1 P</td></tr>
                    <tr><th>질문답변(Q&amp;A) · 갤러리 게시판 글 읽기</th><td class="minus">- 1 P</td></tr>
                    <tr><th>질문답변(Q&amp;A) · 갤러리 게시판 자료 다운로드</th><td class="minus">- 20 P</td></tr>
                    <tr><th>그 외 게시판 (자유게시판, 공지사항, 버그신고, 사용후기, 테마 스킨, 테마 플러그인)</th><td>포인트 미적용</td></tr>
                </tbody>
            </table>
            <p style="font-size: 14px; color: #888; margin-bottom: 50px;">※ 포인트 정책은 그누엑스퍼트 관리자 설정에 따라 변경될 수 있습니다.</p>

            <h3>서비스 운영 정책</h3>
            
            <div class="policy_article">
                <h4>제1조 (목적)</h4>
                <div class="policy_text">
                    본 운영 정책은 그누엑스퍼트(이하 ‘회사’)가 제공하는 계정 서비스 및 다양한 인터넷과 모바일 서비스를 운영함에 있어, 서비스 내에 발생할 수 있는 문제 상황에 대하여 일관성 있게 대처하기 위하여 서비스 운영의 기준과 이용자가 준수해야 할 세부적인 사항을 규정하고 있습니다. 본 운영 정책을 지키지 않을 경우 불익을 당할 수 있습니다.<br><br>
                    회사는 아동·청소년을 성착취물로부터 보호하고, 방송통신심의위원회의 심의 규정을 준수하여 안전한 디지털 환경을 조성함을 목적으로 합니다. 정책 변경 시 최소 7일 전에 공지사항을 통해 고지합니다.
                </div>
            </div>

            <div class="policy_article">
                <h4>제2조 (용어의 정의)</h4>
                <div class="policy_text">
                    1. <b>아동·청소년:</b> 만 19세 미만의 자를 말하며, 아동·청소년으로 인식될 수 있는 사람이나 표현물을 포함합니다.<br>
                    2. <b>아동·청소년 성착취물:</b> 아동·청소년이 등장하여 성적 행위를 하는 내용을 표현하는 영상, 화상, AI 생성물 등을 말합니다.
                </div>
            </div>

            <div class="policy_article">
                <h4>제3조 (등급 규칙 준수 및 게시물 규제)</h4>
                <div class="policy_text">
                    회사는 방송통신심의위원회의 SafeNet 등급기준 중 ’12세 이상 (중학생가)’ 레벨을 준수하고자 노력합니다. 다음과 같은 게시물은 규제 대상이며, 사전 통보 없이 삭제 또는 차단될 수 있습니다.
                </div>
                <ul class="policy_sublist">
                    <li>아동·청소년 성착취물 및 관련 정보를 공유하는 게시물</li>
                    <li>과도한 신체 노출이나 선정적인 행위를 묘사하는 콘텐츠</li>
                    <li>타인의 명예 훼손 및 불법 촬영물 유포</li>
                    <li>인종, 성(性), 국적, 종교, 정치적 분쟁 등 편견에 기반한 글</li>
                    <li>영리 목적의 광고 및 홍보성 게시물</li>
                    <li>허위사실 유포 및 타인에게 혐오감을 주는 글</li>
                    <li>도배, 욕설, 음란한 단어 및 표현 포함</li>
                    <li>개인정보(전화번호, 실명 등)가 포함된 글</li>
                    <li>불법 사이트(와레즈, 토렌트 등) 소개 및 권유</li>
                    <li>계정 거래, 현금 거래 등 불법적인 시도</li>
                    <li>저작권 침해 및 관계법령에 위배되는 모든 게시물</li>
                </ul>
            </div>

            <div class="policy_article">
                <h4>제4조 (계정 서비스 제한 조치)</h4>
                <div class="policy_text">
                    1. <b>주의/경고:</b> 경미한 규정 위반 시 시정 권고.<br>
                    2. <b>게시 제한:</b> 일정 기간 글쓰기 및 서비스 이용 제한.<br>
                    3. <b>영구 정지:</b> 중대한 위반(성착취물 유포 등) 시 계정 영구 폐쇄.
                </div>
            </div>

            <div class="policy_article">
                <h4>제5조 (아동 · 청소년 대상 성범죄 무관용 원칙)</h4>
                <div class="policy_text">
                    아동 및 청소년 대상 성범죄에 대해서는 <b>무관용 원칙</b>을 적용합니다. 위반 시 누적 정도와 관계없이 즉시 강력한 제재를 적용하며, 필요시 수사기관과 연계하여 대응합니다.
                </div>
                <div class="caution_box">
                    <h5>※ 주요 금지 행위 (무관용 대상)</h5>
                    <p>성착취물 제작·제공·소지·이용 행위, 아동·청소년 성매매 및 성범죄 모의, 그루밍(길들이기) 행위, 과도한 성적 대상화 등</p>
                </div>
            </div>

            <div class="policy_article">
                <h4>제6조 (신고 채널 운영)</h4>
                <div class="policy_text">
                    이용자는 서비스 내 신고 기능을 통해 부적절한 게시물을 신고할 수 있으며, 회사는 접수 후 신속하게 검토 및 조치합니다. 성범죄 발견 시 24시간 운영되는 신고 채널이나 '문의하기' 기능을 통해 제보해 주시기 바랍니다.
                </div>
            </div>

        </div>
        
        <div class="guide_footer_sign">
            <p>본 가이드 및 운영 정책은 그누엑스퍼트 이용자 모두의 안전을 위해 마련되었습니다.<br>궁금한 사항은 고객센터 1:1 문의 게시판을 이용해 주세요.</p>
        </div>
    </div>
</article>

<?php
if ($is_standalone) {
    include_once(G5_THEME_PATH.'/tail.php');
}
?>
