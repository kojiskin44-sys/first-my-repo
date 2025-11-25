<?php
/**
 * サンプルプロジェクトデータのインポート
 *
 * このファイルを実行すると、5件のダミープロジェクトが自動的に作成されます。
 * 使い方: このファイルをfunctions.phpから呼び出すか、直接実行してください。
 *
 * @package Student_Funding_Theme
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * サンプルプロジェクトを作成する関数
 */
function student_funding_create_sample_projects() {
    // すでにサンプルデータが存在するかチェック
    $existing = get_option('student_funding_sample_data_created');
    if ($existing) {
        return; // すでに作成済みの場合は何もしない
    }

    // カテゴリーを作成（存在しない場合）
    $categories = array(
        'social' => '社会活動',
        'business' => 'ビジネス',
        'creative' => 'クリエイティブ',
        'study' => '学業',
    );

    $category_ids = array();
    foreach ($categories as $slug => $name) {
        $term = term_exists($name, 'project_category');
        if (!$term) {
            $term = wp_insert_term($name, 'project_category', array('slug' => $slug));
        }
        if (!is_wp_error($term)) {
            $category_ids[$slug] = is_array($term) ? $term['term_id'] : $term;
        }
    }

    // プロジェクトデータ
    $projects = array(
        // プロジェクト1: 途上国の教育支援
        array(
            'title' => '途上国の子どもたちに教育を届けたい！',
            'content' => '<h2>なぜこのプロジェクトを始めたのか</h2>

<p>私は昨年、ボランティアでカンボジアを訪れました。そこで出会った子どもたちは、学校に行きたくても教科書やノートがなく、勉強ができない状況でした。日本では当たり前のように手に入る文房具や教材が、彼らにとっては高嶺の花なのです。</p>

<p>特に印象的だったのは、9歳のソピアという女の子でした。彼女は毎日、土の上に木の棒で文字を書いて勉強していました。「いつか先生になりたい」と輝く目で語る彼女の姿を見て、私は何かしなければと強く思いました。</p>

<h2>このプロジェクトの目的</h2>

<p>カンボジアの農村部にある小学校3校に、教科書、ノート、文房具を届けることが目標です。約150名の子どもたちが、1年間安心して勉強できる環境を整えます。</p>

<h2>活動内容</h2>

<ul>
<li>現地の教育NPOと連携して、必要な教材をリストアップ</li>
<li>日本で資金を集め、現地で教材を購入</li>
<li>3つの小学校に直接届ける（2025年4月予定）</li>
<li>継続的な支援体制の構築</li>
</ul>

<h2>資金の使い道</h2>

<ul>
<li>教科書・ノート購入費：60,000円</li>
<li>文房具セット：20,000円</li>
<li>輸送費・現地交通費：15,000円</li>
<li>現地コーディネート費：5,000円</li>
</ul>

<h2>期待される効果</h2>

<p>このプロジェクトにより、150人の子どもたちが1年間しっかりと勉強できる環境が整います。教育を受けることで、彼らの将来の選択肢が広がり、地域全体の発展にもつながると信じています。</p>

<p>また、私自身も現地に赴き、活動の様子を定期的に報告します。支援してくださった皆様に、子どもたちの成長をお届けできることを楽しみにしています。</p>',
            'student_name' => '田中 美咲',
            'student_school' => '○○大学 国際関係学部',
            'student_grade' => '3年生',
            'goal_amount' => 100000,
            'deadline' => date('Y-m-d', strtotime('+60 days')),
            'bank_info' => "銀行名: ○○銀行\n支店名: 渋谷支店\n口座種別: 普通\n口座番号: 1234567\n口座名義: タナカ ミサキ",
            'return_info' => "【1,000円】\n・お礼のメッセージ\n・活動報告メール\n\n【3,000円】\n・上記に加えて\n・子どもたちからの手紙（PDF）\n\n【5,000円以上】\n・上記に加えて\n・現地の写真データ\n・活動報告書（PDF）\n\n【10,000円以上】\n・上記に加えて\n・カンボジアの民芸品\n・活動報告書（冊子版）郵送\n\n※すべてのリターンは辞退可能です",
            'category' => 'social',
        ),

        // プロジェクト2: 海洋プラスチック削減
        array(
            'title' => '海洋プラスチック削減プロジェクト - 地元の海を守る！',
            'content' => '<h2>プロジェクトの背景</h2>

<p>私の地元、湘南の海岸には毎日大量のプラスチックごみが流れ着きます。美しかった海岸は、今やペットボトルやビニール袋で溢れています。このままでは、私たちの大切な海が失われてしまいます。</p>

<p>海洋環境学を専攻する学生として、このままでは何も変わらない。まず自分たちの地元から変えていこうと、このプロジェクトを立ち上げました。</p>

<h2>活動内容</h2>

<p><strong>毎週のビーチクリーン活動</strong></p>
<ul>
<li>毎週土曜日、朝7時から2時間の清掃活動</li>
<li>集めたプラスチックは種類別に分類・記録</li>
<li>データを活用して、発生源の特定と対策を提案</li>
</ul>

<p><strong>地域への啓発活動</strong></p>
<ul>
<li>地元の小学校で環境教育ワークショップを開催</li>
<li>プラスチックごみの実態を伝える展示会の実施</li>
<li>SNSでの情報発信</li>
</ul>

<p><strong>代替品の普及</strong></p>
<ul>
<li>地元の商店街と協力して、マイバッグ・マイボトルの普及活動</li>
<li>リサイクル素材で作ったエコグッズの配布</li>
</ul>

<h2>資金の使い道</h2>

<ul>
<li>清掃用具（トング、手袋、袋など）：30,000円</li>
<li>ワークショップ教材費：25,000円</li>
<li>展示会開催費：20,000円</li>
<li>エコグッズ製作費：20,000円</li>
<li>広報費（チラシ、ポスターなど）：5,000円</li>
</ul>

<h2>目指す未来</h2>

<p>3ヶ月間の活動を通じて、地元の海岸から1トン以上のプラスチックごみを回収することを目指します。そして、この活動をモデルケースとして、他の地域にも広げていきたいと考えています。</p>

<p>「きれいな海を次の世代に」― これが私たちの合言葉です。</p>',
            'student_name' => '鈴木 海斗',
            'student_school' => '△△大学 海洋環境学科',
            'student_grade' => '2年生',
            'goal_amount' => 100000,
            'deadline' => date('Y-m-d', strtotime('+45 days')),
            'bank_info' => "銀行名: △△銀行\n支店名: 藤沢支店\n口座種別: 普通\n口座番号: 7654321\n口座名義: スズキ カイト",
            'return_info' => "【1,000円】\n・活動報告メール\n・お礼のメッセージ\n\n【3,000円】\n・上記に加えて\n・ビーチクリーンの写真データ\n\n【5,000円以上】\n・上記に加えて\n・手作りエコバッグ\n・活動報告書\n\n【10,000円以上】\n・上記に加えて\n・ビーチクリーン活動への招待\n・海洋プラスチックアート作品\n\n※リターン辞退も可能です",
            'category' => 'social',
        ),

        // プロジェクト3: AIを活用した学習支援アプリ
        array(
            'title' => 'AIで学習革命！誰でも使える学習支援アプリを開発したい',
            'content' => '<h2>解決したい課題</h2>

<p>現在の教育現場では、一人ひとりの理解度に合わせた学習が難しい状況です。わからないところがあっても、授業は先に進んでしまう。復習したくても、どこから手をつけていいかわからない。そんな悩みを抱える学生が多くいます。</p>

<p>私自身も数学が苦手で、高校時代に大変苦労しました。その経験から、「誰もが自分のペースで学べるツール」を作りたいと思い、このプロジェクトを始めました。</p>

<h2>アプリの特徴</h2>

<p><strong>AIによる個別最適化学習</strong></p>
<ul>
<li>ユーザーの理解度を分析し、最適な問題を出題</li>
<li>つまずきポイントを自動検出して、弱点を克服</li>
<li>学習履歴から次に学ぶべき内容を提案</li>
</ul>

<p><strong>わかりやすい解説動画</strong></p>
<ul>
<li>各単元ごとに短い解説動画を用意</li>
<li>アニメーションを使った視覚的な説明</li>
<li>何度でも繰り返し視聴可能</li>
</ul>

<p><strong>ゲーミフィケーション要素</strong></p>
<ul>
<li>問題を解くとポイントがもらえる</li>
<li>レベルアップシステムでモチベーション維持</li>
<li>友達と競い合える機能</li>
</ul>

<h2>対象科目</h2>

<p>第一弾として「数学（中学1年〜高校2年）」に特化したアプリを開発します。将来的には、他の科目にも展開していく予定です。</p>

<h2>資金の使い道</h2>

<ul>
<li>AIエンジン開発費：80,000円</li>
<li>サーバー費用（1年分）：30,000円</li>
<li>解説動画制作費：40,000円</li>
<li>デザイン外注費：30,000円</li>
<li>アプリ開発ツール費：20,000円</li>
</ul>

<h2>リリース予定</h2>

<p>2025年6月にβ版をリリース予定です。まずは無料で提供し、多くの学生に使ってもらいたいと考えています。支援してくださった方には、優先的にβ版テスターになっていただけます。</p>

<h2>将来のビジョン</h2>

<p>このアプリを通じて、日本中の学生が「わからない」から「わかる！」に変わる瞬間を増やしたい。教育格差をなくし、誰もが平等に学べる社会を実現したいと考えています。</p>',
            'student_name' => '山田 拓也',
            'student_school' => '□□大学 情報工学部',
            'student_grade' => '4年生',
            'goal_amount' => 200000,
            'deadline' => date('Y-m-d', strtotime('+90 days')),
            'bank_info' => "銀行名: □□銀行\n支店名: 新宿支店\n口座種別: 普通\n口座番号: 9876543\n口座名義: ヤマダ タクヤ",
            'return_info' => "【1,000円】\n・お礼のメッセージ\n・開発進捗メール配信\n\n【3,000円】\n・上記に加えて\n・β版テスター権利\n\n【5,000円】\n・上記に加えて\n・アプリ内で名前をクレジット表示\n・限定ステッカー\n\n【10,000円以上】\n・上記に加えて\n・正式版の永久無料利用権\n・機能リクエスト権\n\n【30,000円以上】\n・上記すべてに加えて\n・開発チームとのオンラインミーティング\n・プロジェクトTシャツ\n\n※リターン辞退可能",
            'category' => 'business',
        ),

        // プロジェクト4: 音楽フェスティバル
        array(
            'title' => '学生による学生のための音楽フェス開催！',
            'content' => '<h2>音楽で人と人をつなぐ</h2>

<p>コロナ禍で多くのライブイベントが中止になり、若手ミュージシャンの発表の場が失われました。私たちは音楽サークルで活動していますが、「自分たちで何かできないか」と考え、学生主導の音楽フェスティバルを企画しました。</p>

<p>テーマは「つながり」。音楽を通じて、学生、地域の人々、アーティストが一体となれるイベントを目指します。</p>

<h2>イベント概要</h2>

<p><strong>日時：</strong> 2025年5月10日（土）12:00〜19:00<br>
<strong>場所：</strong> ○○公園 野外ステージ<br>
<strong>出演：</strong> 学生バンド10組 + ゲストアーティスト2組<br>
<strong>入場料：</strong> 無料（投げ銭制）</p>

<h2>イベントの特徴</h2>

<p><strong>完全学生主導</strong></p>
<ul>
<li>企画から運営まですべて学生が担当</li>
<li>10の大学から50名以上のスタッフが参加</li>
<li>音響、照明、装飾、広報など各チームで協力</li>
</ul>

<p><strong>新人発掘オーディション</strong></p>
<ul>
<li>出演バンドは公開オーディションで選出</li>
<li>プロの音楽プロデューサーが審査員</li>
<li>グランプリ受賞者にはレコーディング支援</li>
</ul>

<p><strong>地域との連携</strong></p>
<ul>
<li>地元の飲食店が出店するフードエリア</li>
<li>地域の子どもたち向けの楽器体験コーナー</li>
<li>環境に配慮したエコフェス（リユース食器使用）</li>
</ul>

<h2>資金の使い道</h2>

<ul>
<li>音響・照明機材レンタル：100,000円</li>
<li>ステージ設営費：60,000円</li>
<li>出演アーティスト交通費：30,000円</li>
<li>広告宣伝費：25,000円</li>
<li>保険料・許可申請費：20,000円</li>
<li>装飾・備品費：15,000円</li>
</ul>

<h2>目指すもの</h2>

<p>このフェスを、毎年開催される「学生音楽の祭典」として定着させたいと考えています。若手アーティストの登竜門となり、地域に愛されるイベントに育てていきます。</p>

<p>音楽には人を笑顔にする力があります。その力で、たくさんの人に幸せを届けたい。そんな思いでこのプロジェクトに取り組んでいます。</p>

<p>当日は、支援者の皆さまを特別エリアにご招待します。ぜひ一緒に盛り上がりましょう！</p>',
            'student_name' => '佐藤 優香',
            'student_school' => '◇◇大学 芸術学部',
            'student_grade' => '3年生',
            'goal_amount' => 250000,
            'deadline' => date('Y-m-d', strtotime('+30 days')),
            'bank_info' => "銀行名: ◇◇銀行\n支店名: 池袋支店\n口座種別: 普通\n口座番号: 1122334\n口座名義: サトウ ユウカ",
            'return_info' => "【1,000円】\n・お礼のメッセージ\n・当日の写真データ\n\n【3,000円】\n・上記に加えて\n・フェスオリジナルステッカー\n・特設サイトにお名前掲載\n\n【5,000円】\n・上記に加えて\n・フェスTシャツ\n・VIPエリアご招待\n\n【10,000円以上】\n・上記に加えて\n・バックステージツアー\n・出演アーティストとの交流会\n・記録DVD\n\n【30,000円以上】\n・上記すべてに加えて\n・ステージ上でのスポンサー紹介\n・来年のフェス無料招待\n\n※リターン辞退可能",
            'category' => 'creative',
        ),

        // プロジェクト5: 宇宙工学研究
        array(
            'title' => '学生が作る人工衛星プロジェクト - 宇宙への挑戦！',
            'content' => '<h2>宇宙を身近にしたい</h2>

<p>「宇宙」と聞くと、遠い存在に感じる人が多いかもしれません。しかし、私たち学生でも、小型人工衛星（CubeSat）を作り、宇宙に打ち上げることができる時代になりました。</p>

<p>私たちは大学の宇宙工学研究室で、独自の超小型人工衛星の開発に取り組んでいます。このプロジェクトは、次世代の宇宙開発を担う人材を育てるとともに、宇宙をもっと身近に感じてもらうことを目指しています。</p>

<h2>プロジェクト概要</h2>

<p><strong>衛星名：</strong> GAKUSEI-SAT 1号<br>
<strong>サイズ：</strong> 10cm × 10cm × 10cm (1U CubeSat)<br>
<strong>ミッション：</strong> 地球観測・教育支援<br>
<strong>打ち上げ予定：</strong> 2026年</p>

<h2>ミッションの内容</h2>

<p><strong>1. 地球観測</strong></p>
<ul>
<li>小型カメラで地球の写真を撮影</li>
<li>気象データの収集</li>
<li>災害時の緊急観測</li>
</ul>

<p><strong>2. 教育支援</strong></p>
<ul>
<li>小中学校向けの宇宙教育プログラム</li>
<li>衛星からのメッセージ受信体験</li>
<li>リアルタイム追跡アプリの提供</li>
</ul>

<p><strong>3. 技術実証</strong></p>
<ul>
<li>新型通信システムのテスト</li>
<li>省電力技術の実証</li>
<li>学生による運用ノウハウの蓄積</li>
</ul>

<h2>現在の進捗</h2>

<p>設計フェーズは完了し、現在は部品の調達と組み立てフェーズに入っています。これまでに以下の成果を上げました：</p>

<ul>
<li>基本設計の完了と審査通過</li>
<li>JAXAとの連携協定締結</li>
<li>打ち上げロケットの搭載枠確保</li>
<li>地上局の設置完了</li>
</ul>

<h2>資金の使い道</h2>

<ul>
<li>電子部品購入費：150,000円</li>
<li>センサー・カメラ：100,000円</li>
<li>バッテリー・太陽電池：80,000円</li>
<li>通信機器：70,000円</li>
<li>試験装置レンタル：50,000円</li>
<li>部品予備費：50,000円</li>
</ul>

<h2>期待される成果</h2>

<p>このプロジェクトは、学生が実際の宇宙開発プロジェクトを経験できる貴重な機会です。設計から製造、試験、運用まで、すべてのプロセスに学生が関わります。</p>

<p>また、撮影した地球の写真や観測データは、教育現場で活用されます。子どもたちが宇宙に興味を持つきっかけとなり、将来の宇宙開発を担う人材の育成につながることを期待しています。</p>

<h2>チームメンバー</h2>

<p>現在、10名の学生が参加しています：</p>
<ul>
<li>衛星設計チーム（3名）</li>
<li>電気・通信チーム（3名）</li>
<li>ソフトウェアチーム（2名）</li>
<li>運用・広報チーム（2名）</li>
</ul>

<p>私たちと一緒に、宇宙への挑戦を応援してください！</p>',
            'student_name' => '高橋 航',
            'student_school' => '☆☆工業大学 航空宇宙工学科',
            'student_grade' => '大学院1年',
            'goal_amount' => 500000,
            'deadline' => date('Y-m-d', strtotime('+120 days')),
            'bank_info' => "銀行名: ☆☆銀行\n支店名: 本郷支店\n口座種別: 普通\n口座番号: 5556667\n口座名義: タカハシ ワタル",
            'return_info' => "【1,000円】\n・お礼のメッセージ\n・プロジェクトニュースレター\n\n【3,000円】\n・上記に加えて\n・衛星の設計図（PDF）\n・打ち上げライブ配信の案内\n\n【5,000円】\n・上記に加えて\n・衛星に名前を刻印\n・プロジェクトステッカー\n\n【10,000円以上】\n・上記に加えて\n・衛星が撮影した地球の写真（額装）\n・研究室見学会へのご招待\n・活動報告書（製本版）\n\n【30,000円以上】\n・上記すべてに加えて\n・打ち上げ見学ツアーご招待\n・衛星の部品サンプル\n・オンライン講演会での特別紹介\n\n【50,000円以上】\n・上記すべてに加えて\n・衛星からのメッセージ送信権\n・チーム公式スポンサーとして名前掲載\n・記念楯の贈呈\n\n※リターン辞退可能",
            'category' => 'study',
        ),
    );

    // プロジェクトを作成
    foreach ($projects as $index => $project_data) {
        // 投稿データを準備
        $post_data = array(
            'post_title'   => $project_data['title'],
            'post_content' => $project_data['content'],
            'post_status'  => 'publish',
            'post_type'    => 'project',
            'post_author'  => 1,
        );

        // 投稿を作成
        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            // カテゴリーを設定
            if (isset($category_ids[$project_data['category']])) {
                wp_set_post_terms($post_id, array($category_ids[$project_data['category']]), 'project_category');
            }

            // カスタムフィールドを設定
            update_post_meta($post_id, '_goal_amount', $project_data['goal_amount']);
            update_post_meta($post_id, '_deadline', $project_data['deadline']);
            update_post_meta($post_id, '_bank_info', $project_data['bank_info']);
            update_post_meta($post_id, '_return_info', $project_data['return_info']);
            update_post_meta($post_id, '_student_name', $project_data['student_name']);
            update_post_meta($post_id, '_student_school', $project_data['student_school']);
            update_post_meta($post_id, '_student_grade', $project_data['student_grade']);

            // 初期値を設定
            update_post_meta($post_id, '_current_amount', 0);
            update_post_meta($post_id, '_supporter_count', 0);

            // サンプルの支援データを作成（プロジェクト1と3にのみ）
            if ($index === 0 || $index === 2) {
                student_funding_create_sample_supports($post_id, $project_data['goal_amount']);
            }
        }
    }

    // フラグを設定して、再度実行されないようにする
    update_option('student_funding_sample_data_created', true);
}

/**
 * サンプルの支援データを作成
 */
function student_funding_create_sample_supports($project_id, $goal_amount) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';

    // ランダムな支援者データ
    $supporters = array(
        array('name' => '山本 花子', 'amount' => 5000),
        array('name' => '佐々木 太郎', 'amount' => 3000),
        array('name' => '', 'amount' => 1000), // 匿名
        array('name' => '田中 次郎', 'amount' => 10000),
        array('name' => '鈴木 三郎', 'amount' => 2000),
        array('name' => '', 'amount' => 5000), // 匿名
        array('name' => '伊藤 四郎', 'amount' => 1000),
        array('name' => '高橋 五郎', 'amount' => 3000),
    );

    // ランダムに3〜5件の支援を作成
    $num_supports = rand(3, 5);
    shuffle($supporters);

    for ($i = 0; $i < $num_supports; $i++) {
        $supporter = $supporters[$i];
        $is_anonymous = empty($supporter['name']) ? 1 : 0;

        $wpdb->insert(
            $table_name,
            array(
                'project_id'       => $project_id,
                'supporter_name'   => $supporter['name'],
                'supporter_email'  => 'sample' . $i . '@example.com',
                'support_amount'   => $supporter['amount'],
                'payment_method'   => rand(0, 1) ? 'paypal' : 'bank',
                'is_anonymous'     => $is_anonymous,
                'decline_return'   => rand(0, 1),
                'message'          => '応援しています！',
                'status'           => 'completed',
                'support_date'     => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
            ),
            array('%d', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%s')
        );
    }

    // 支援額を更新
    student_funding_update_project_stats($project_id);
}

/**
 * テーマ有効化時に実行
 */
function student_funding_activation_hook() {
    // カスタムテーブルを作成
    student_funding_create_support_table();

    // サンプルプロジェクトを作成
    student_funding_create_sample_projects();

    // パーマリンクをフラッシュ
    flush_rewrite_rules();
}

// テーマ有効化フック（after_switch_themeで実行）
add_action('after_switch_theme', 'student_funding_activation_hook');

/**
 * サンプルデータをリセットする関数（管理画面から実行可能）
 */
function student_funding_reset_sample_data() {
    // 既存のサンプルプロジェクトを削除
    $args = array(
        'post_type'      => 'project',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    );
    $project_ids = get_posts($args);

    foreach ($project_ids as $project_id) {
        wp_delete_post($project_id, true);
    }

    // フラグをリセット
    delete_option('student_funding_sample_data_created');

    // 支援データをクリア
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';
    $wpdb->query("TRUNCATE TABLE $table_name");

    // 再度サンプルデータを作成
    student_funding_create_sample_projects();
}
