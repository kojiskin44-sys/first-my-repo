<?php
/**
 * Template Name: プロジェクト作成ページ
 *
 * @package Student_Funding_Theme
 */

// ログインチェック
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/?redirect_to=' . urlencode(get_permalink())));
    exit;
}

get_header();
?>

<div class="create-project-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">新しいプロジェクトを作成</h1>
            <p class="page-description">あなたの夢や挑戦を、多くの人に支援してもらいましょう</p>
        </header>

        <div class="create-project-container">
            <form id="create-project-form" class="project-form" enctype="multipart/form-data">
                <?php wp_nonce_field('create_project', 'create_project_nonce'); ?>

                <!-- エラーメッセージ -->
                <div id="form-error" class="form-error" style="display: none;"></div>

                <!-- 基本情報 -->
                <section class="form-section">
                    <h2 class="section-title">基本情報</h2>

                    <div class="form-group">
                        <label for="project_title">プロジェクトタイトル <span class="required">*</span></label>
                        <input type="text" id="project_title" name="project_title" required maxlength="100" class="form-control">
                        <p class="form-help">魅力的なタイトルをつけましょう（最大100文字）</p>
                    </div>

                    <div class="form-group">
                        <label for="project_category">カテゴリー <span class="required">*</span></label>
                        <select id="project_category" name="project_category" required class="form-control">
                            <option value="">選択してください</option>
                            <?php
                            $categories = get_terms(array(
                                'taxonomy'   => 'project_category',
                                'hide_empty' => false,
                            ));
                            if ($categories && !is_wp_error($categories)) {
                                foreach ($categories as $category) {
                                    echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="project_content">プロジェクトの説明 <span class="required">*</span></label>
                        <textarea id="project_content" name="project_content" required rows="10" class="form-control"></textarea>
                        <p class="form-help">プロジェクトの目的、背景、実現したいことなどを詳しく書きましょう</p>
                    </div>

                    <div class="form-group">
                        <label for="project_image">プロジェクト画像</label>
                        <input type="file" id="project_image" name="project_image" accept="image/*" class="form-control">
                        <p class="form-help">推奨サイズ: 1200×630px</p>
                        <div id="image-preview" class="image-preview"></div>
                    </div>

                    <div class="form-group">
                        <label for="video_url">プロジェクト動画URL（任意）</label>
                        <input type="url" id="video_url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                        <p class="form-help">YouTube、Vimeoなどの動画URLを入力できます</p>
                    </div>
                </section>

                <!-- 目標設定 -->
                <section class="form-section">
                    <h2 class="section-title">目標設定</h2>

                    <div class="form-group">
                        <label for="goal_amount">目標金額（円） <span class="required">*</span></label>
                        <input type="number" id="goal_amount" name="goal_amount" required min="1000" step="1000" class="form-control">
                        <p class="form-help">最低金額: 1,000円</p>
                    </div>

                    <div class="form-group">
                        <label for="deadline">支援締切日 <span class="required">*</span></label>
                        <input type="date" id="deadline" name="deadline" required class="form-control" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        <p class="form-help">今日から最低1日後の日付を設定してください</p>
                    </div>
                </section>

                <!-- 学生情報 -->
                <section class="form-section">
                    <h2 class="section-title">学生情報</h2>

                    <div class="form-group">
                        <label for="student_name">学生名 <span class="required">*</span></label>
                        <input type="text" id="student_name" name="student_name" required class="form-control" value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>">
                    </div>

                    <div class="form-group">
                        <label for="student_school">学校名 <span class="required">*</span></label>
                        <input type="text" id="student_school" name="student_school" required class="form-control">
                        <p class="form-help">例: ○○大学、○○高校</p>
                    </div>

                    <div class="form-group">
                        <label for="student_grade">学年 <span class="required">*</span></label>
                        <input type="text" id="student_grade" name="student_grade" required class="form-control">
                        <p class="form-help">例: 2年生、大学3年</p>
                    </div>
                </section>

                <!-- 決済情報 -->
                <section class="form-section">
                    <h2 class="section-title">決済情報</h2>

                    <div class="form-group">
                        <label for="paypal_link">PayPalリンク（任意）</label>
                        <input type="url" id="paypal_link" name="paypal_link" class="form-control" placeholder="https://paypal.me/...">
                        <p class="form-help">PayPalの支払いリンクを入力してください</p>
                    </div>

                    <div class="form-group">
                        <label for="bank_info">銀行振込情報（任意）</label>
                        <textarea id="bank_info" name="bank_info" rows="4" class="form-control"></textarea>
                        <p class="form-help">銀行名・支店名・口座番号などを入力してください</p>
                    </div>
                </section>

                <!-- リターン情報 -->
                <section class="form-section">
                    <h2 class="section-title">リターン情報</h2>

                    <div class="form-group">
                        <label for="return_info">リターン内容（任意）</label>
                        <textarea id="return_info" name="return_info" rows="4" class="form-control"></textarea>
                        <p class="form-help">支援者へのお礼や特典を記載してください（基本は辞退可能）</p>
                    </div>
                </section>

                <!-- 送信ボタン -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">
                        プロジェクトを作成する
                    </button>
                    <a href="<?php echo home_url('/my-page/'); ?>" class="btn btn-secondary">
                        キャンセル
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
get_footer();
