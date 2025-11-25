<?php
/**
 * プロジェクト詳細テンプレート
 *
 * @package Student_Funding_Theme
 */

get_header();

while (have_posts()) : the_post();
    // プロジェクト情報を取得
    $goal_amount = get_post_meta(get_the_ID(), '_goal_amount', true);
    $current_amount = get_post_meta(get_the_ID(), '_current_amount', true);
    $current_amount = $current_amount ? $current_amount : 0;
    $deadline = get_post_meta(get_the_ID(), '_deadline', true);
    $student_name = get_post_meta(get_the_ID(), '_student_name', true);
    $student_school = get_post_meta(get_the_ID(), '_student_school', true);
    $student_grade = get_post_meta(get_the_ID(), '_student_grade', true);
    $student_photo_id = get_post_meta(get_the_ID(), '_student_photo_id', true);
    $return_info = get_post_meta(get_the_ID(), '_return_info', true);
    $bank_info = get_post_meta(get_the_ID(), '_bank_info', true);

    $achievement_rate = student_funding_get_achievement_rate(get_the_ID());
    $remaining_days = student_funding_get_remaining_days(get_the_ID());
    $is_ended = student_funding_is_project_ended(get_the_ID());
    $supporter_count = get_post_meta(get_the_ID(), '_supporter_count', true);
    $supporter_count = $supporter_count ? $supporter_count : 0;
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class('single-project'); ?>>
        <div class="container">
            <!-- プロジェクトヘッダー -->
            <header class="project-header">
                <div class="project-meta-top">
                    <?php
                    $categories = get_the_terms(get_the_ID(), 'project_category');
                    if ($categories && !is_wp_error($categories)) :
                        foreach ($categories as $category) :
                            ?>
                            <span class="category-badge"><?php echo esc_html($category->name); ?></span>
                        <?php endforeach;
                    endif;
                    ?>
                    <?php if ($is_ended) : ?>
                        <span class="ended-badge">終了</span>
                    <?php endif; ?>

                    <!-- リンクコピーボタン -->
                    <button class="copy-link-btn" id="copy-project-link" data-url="<?php echo esc_url(get_permalink()); ?>" title="リンクをコピー">
                        <span class="copy-icon">🔗</span>
                        <span class="copy-text">シェア</span>
                    </button>
                </div>

                <h1 class="project-title"><?php the_title(); ?></h1>

                <div class="student-info-header">
                    <?php if ($student_photo_id) : ?>
                        <div class="student-avatar">
                            <?php echo wp_get_attachment_image($student_photo_id, 'student-avatar'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="student-details">
                        <p class="student-name"><?php echo esc_html($student_name); ?></p>
                        <p class="student-school"><?php echo esc_html($student_school); ?> <?php echo esc_html($student_grade); ?></p>
                    </div>
                </div>
            </header>

            <div class="project-layout">
                <!-- メインコンテンツ -->
                <div class="project-main">
                    <!-- メイン画像 -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="project-main-image">
                            <?php the_post_thumbnail('project-large'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- 進捗情報 -->
                    <div class="project-progress-section">
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar large">
                                <div class="progress-fill" style="width: <?php echo $achievement_rate; ?>%" data-progress="<?php echo $achievement_rate; ?>"></div>
                            </div>
                        </div>

                        <div class="progress-info">
                            <div class="info-item">
                                <span class="info-value"><?php echo student_funding_format_amount($current_amount); ?></span>
                                <span class="info-label">現在の支援額</span>
                            </div>
                            <div class="info-item">
                                <span class="info-value"><?php echo $achievement_rate; ?>%</span>
                                <span class="info-label">達成率</span>
                            </div>
                            <div class="info-item">
                                <span class="info-value"><?php echo number_format($supporter_count); ?>人</span>
                                <span class="info-label">支援者</span>
                            </div>
                            <div class="info-item">
                                <?php if ($remaining_days !== null) : ?>
                                    <span class="info-value <?php echo $remaining_days <= 7 ? 'urgent' : ''; ?>">残り<?php echo $remaining_days; ?>日</span>
                                    <span class="info-label">締切まで</span>
                                <?php else : ?>
                                    <span class="info-value">-</span>
                                    <span class="info-label">締切未設定</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ストーリー -->
                    <div class="project-story">
                        <h2>プロジェクトのストーリー</h2>
                        <div class="story-content">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- 学生プロフィール -->
                    <div class="student-profile-section">
                        <h2>学生プロフィール</h2>
                        <div class="student-profile-card">
                            <?php if ($student_photo_id) : ?>
                                <div class="profile-avatar">
                                    <?php echo wp_get_attachment_image($student_photo_id, 'student-avatar'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="profile-info">
                                <h3><?php echo esc_html($student_name); ?></h3>
                                <p class="profile-school"><?php echo esc_html($student_school); ?></p>
                                <p class="profile-grade"><?php echo esc_html($student_grade); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- リターン情報 -->
                    <?php if ($return_info) : ?>
                        <div class="return-info-section">
                            <h2>リターンについて</h2>
                            <div class="return-content">
                                <?php echo wpautop(esc_html($return_info)); ?>
                                <p class="return-note"><small>※リターンは辞退することもできます</small></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div><!-- .project-main -->

                <!-- サイドバー（支援フォーム） -->
                <aside class="project-sidebar">
                    <div class="support-box <?php echo $is_ended ? 'support-ended' : ''; ?>">
                        <div class="support-summary">
                            <div class="goal-info">
                                <p class="goal-label">目標金額</p>
                                <p class="goal-amount"><?php echo student_funding_format_amount($goal_amount); ?></p>
                            </div>
                            <div class="current-info">
                                <p class="current-label">現在</p>
                                <p class="current-amount"><?php echo student_funding_format_amount($current_amount); ?></p>
                            </div>
                        </div>

                        <?php if (!$is_ended) : ?>
                            <button class="btn btn-primary btn-large btn-support" id="open-support-modal">
                                このプロジェクトを支援する
                            </button>

                            <div class="support-notes">
                                <ul>
                                    <li>会員登録なしでも支援できます</li>
                                    <li>リターンは辞退することもできます</li>
                                    <li>PayPal または 銀行振込が利用可能</li>
                                </ul>
                            </div>
                        <?php else : ?>
                            <div class="support-ended-message">
                                <p>このプロジェクトは終了しました</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </aside>
            </div><!-- .project-layout -->
        </div><!-- .container -->

        <!-- 支援モーダル -->
        <?php if (!$is_ended) : ?>
            <div id="support-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>プロジェクトを支援する</h2>
                        <button class="modal-close" id="close-support-modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <form id="support-form" class="support-form">
                            <!-- プロジェクトID（隠しフィールド） -->
                            <input type="hidden" name="project_id" value="<?php echo get_the_ID(); ?>">

                            <!-- 支援額 -->
                            <div class="form-group">
                                <label for="support_amount">支援額（円） <span class="required">*</span></label>
                                <input type="number" id="support_amount" name="support_amount" min="100" step="100" required>
                                <p class="form-help">最低支援額: 100円</p>
                            </div>

                            <!-- お名前（ニックネーム） -->
                            <div class="form-group">
                                <label for="supporter_name">お名前（ニックネーム可）</label>
                                <input type="text" id="supporter_name" name="supporter_name">
                                <p class="form-help">空欄の場合は「匿名」として表示されます</p>
                            </div>

                            <!-- メールアドレス -->
                            <div class="form-group">
                                <label for="supporter_email">メールアドレス <span class="required">*</span></label>
                                <input type="email" id="supporter_email" name="supporter_email" required>
                                <p class="form-help">確認メールを送信します</p>
                            </div>

                            <!-- 決済方法 -->
                            <div class="form-group">
                                <label>決済方法 <span class="required">*</span></label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="payment_method" value="paypal" checked>
                                        PayPal
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="payment_method" value="bank">
                                        銀行振込
                                    </label>
                                </div>
                            </div>

                            <!-- 匿名支援 -->
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="is_anonymous" id="is_anonymous">
                                    匿名で支援する
                                </label>
                            </div>

                            <!-- リターン辞退 -->
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="decline_return" id="decline_return">
                                    リターンを辞退する
                                </label>
                            </div>

                            <!-- 応援メッセージ -->
                            <div class="form-group">
                                <label for="message">応援メッセージ（任意）</label>
                                <textarea id="message" name="message" rows="4"></textarea>
                            </div>

                            <!-- エラーメッセージ -->
                            <div id="form-error" class="form-error" style="display: none;"></div>

                            <!-- 送信ボタン -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary btn-large btn-block">
                                    支援を確定する
                                </button>
                            </div>

                            <p class="form-note">
                                <small>「支援を確定する」をクリックすると、選択した決済方法のページへ移動します。</small>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </article>

    <?php
endwhile;

get_footer();
