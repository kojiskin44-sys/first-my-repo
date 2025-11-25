<?php
/**
 * フロントページテンプレート
 *
 * @package Student_Funding_Theme
 */

get_header();
?>

<div class="front-page">
    <!-- ヒーローセクション -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">学生の夢を応援しよう</h1>
                <p class="hero-description">
                    学生の社会活動・ビジネスアイデア・夢の実現を<br>
                    あなたの寄付でサポートするクラウドファンディングプラットフォーム
                </p>
                <div class="hero-buttons">
                    <a href="<?php echo get_post_type_archive_link('project'); ?>" class="btn btn-primary btn-large">
                        プロジェクトを見る
                    </a>
                    <?php if (current_user_can('publish_posts')) : ?>
                        <a href="<?php echo admin_url('post-new.php?post_type=project'); ?>" class="btn btn-outline btn-large">
                            プロジェクトを投稿
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- 新着プロジェクト -->
    <section class="projects-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">新着プロジェクト</h2>
                <a href="<?php echo get_post_type_archive_link('project'); ?>" class="view-all">
                    すべて見る →
                </a>
            </div>

            <div class="projects-grid">
                <?php
                $recent_projects = new WP_Query(array(
                    'post_type'      => 'project',
                    'posts_per_page' => 6,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ));

                if ($recent_projects->have_posts()) :
                    while ($recent_projects->have_posts()) : $recent_projects->the_post();
                        // プロジェクト情報を取得
                        $goal_amount = get_post_meta(get_the_ID(), '_goal_amount', true);
                        $current_amount = get_post_meta(get_the_ID(), '_current_amount', true);
                        $student_name = get_post_meta(get_the_ID(), '_student_name', true);
                        $student_school = get_post_meta(get_the_ID(), '_student_school', true);
                        $achievement_rate = student_funding_get_achievement_rate(get_the_ID());
                        $remaining_days = student_funding_get_remaining_days(get_the_ID());
                        $is_ended = student_funding_is_project_ended(get_the_ID());

                        $categories = get_the_terms(get_the_ID(), 'project_category');
                        ?>
                        <article class="project-card <?php echo $is_ended ? 'project-ended' : ''; ?>">
                            <a href="<?php the_permalink(); ?>" class="project-card-link">
                                <!-- プロジェクト画像 -->
                                <div class="project-thumbnail">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('project-thumbnail'); ?>
                                    <?php else : ?>
                                        <div class="placeholder-image">
                                            <span>画像なし</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- カテゴリーバッジ -->
                                    <?php if ($categories && !is_wp_error($categories)) : ?>
                                        <div class="project-categories">
                                            <?php foreach ($categories as $category) : ?>
                                                <span class="category-badge"><?php echo esc_html($category->name); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- 終了バッジ -->
                                    <?php if ($is_ended) : ?>
                                        <div class="ended-badge">終了</div>
                                    <?php endif; ?>
                                </div>

                                <!-- プロジェクト情報 -->
                                <div class="project-content">
                                    <h3 class="project-title"><?php the_title(); ?></h3>

                                    <div class="student-info">
                                        <span class="student-name"><?php echo esc_html($student_name); ?></span>
                                        <span class="student-school"><?php echo esc_html($student_school); ?></span>
                                    </div>

                                    <!-- 進捗バー -->
                                    <div class="progress-section">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $achievement_rate; ?>%"></div>
                                        </div>
                                        <div class="progress-stats">
                                            <span class="achievement-rate"><?php echo $achievement_rate; ?>% 達成</span>
                                        </div>
                                    </div>

                                    <!-- 統計情報 -->
                                    <div class="project-stats">
                                        <div class="stat-item">
                                            <span class="stat-value"><?php echo student_funding_format_amount($current_amount ? $current_amount : 0); ?></span>
                                            <span class="stat-label">現在の支援額</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value"><?php echo student_funding_format_amount($goal_amount); ?></span>
                                            <span class="stat-label">目標金額</span>
                                        </div>
                                        <div class="stat-item">
                                            <?php if ($remaining_days !== null) : ?>
                                                <span class="stat-value <?php echo $remaining_days <= 7 ? 'urgent' : ''; ?>">
                                                    残り<?php echo $remaining_days; ?>日
                                                </span>
                                                <span class="stat-label">締切まで</span>
                                            <?php else : ?>
                                                <span class="stat-value">-</span>
                                                <span class="stat-label">締切未設定</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <p class="no-projects">現在、プロジェクトはありません。</p>
                <?php endif; ?>
            </div><!-- .projects-grid -->
        </div><!-- .container -->
    </section>

    <!-- 注目プロジェクト（達成率順） -->
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">注目のプロジェクト</h2>
            </div>

            <div class="projects-grid">
                <?php
                // 達成率が高い順に取得
                $featured_projects = new WP_Query(array(
                    'post_type'      => 'project',
                    'posts_per_page' => 3,
                    'meta_key'       => '_current_amount',
                    'orderby'        => 'meta_value_num',
                    'order'          => 'DESC',
                ));

                if ($featured_projects->have_posts()) :
                    while ($featured_projects->have_posts()) : $featured_projects->the_post();
                        // プロジェクト情報を取得
                        $goal_amount = get_post_meta(get_the_ID(), '_goal_amount', true);
                        $current_amount = get_post_meta(get_the_ID(), '_current_amount', true);
                        $student_name = get_post_meta(get_the_ID(), '_student_name', true);
                        $student_school = get_post_meta(get_the_ID(), '_student_school', true);
                        $achievement_rate = student_funding_get_achievement_rate(get_the_ID());
                        $remaining_days = student_funding_get_remaining_days(get_the_ID());
                        $is_ended = student_funding_is_project_ended(get_the_ID());

                        $categories = get_the_terms(get_the_ID(), 'project_category');
                        ?>
                        <article class="project-card featured <?php echo $is_ended ? 'project-ended' : ''; ?>">
                            <a href="<?php the_permalink(); ?>" class="project-card-link">
                                <div class="project-thumbnail">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('project-thumbnail'); ?>
                                    <?php else : ?>
                                        <div class="placeholder-image">
                                            <span>画像なし</span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($categories && !is_wp_error($categories)) : ?>
                                        <div class="project-categories">
                                            <?php foreach ($categories as $category) : ?>
                                                <span class="category-badge"><?php echo esc_html($category->name); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($is_ended) : ?>
                                        <div class="ended-badge">終了</div>
                                    <?php endif; ?>
                                </div>

                                <div class="project-content">
                                    <h3 class="project-title"><?php the_title(); ?></h3>

                                    <div class="student-info">
                                        <span class="student-name"><?php echo esc_html($student_name); ?></span>
                                        <span class="student-school"><?php echo esc_html($student_school); ?></span>
                                    </div>

                                    <div class="progress-section">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $achievement_rate; ?>%"></div>
                                        </div>
                                        <div class="progress-stats">
                                            <span class="achievement-rate"><?php echo $achievement_rate; ?>% 達成</span>
                                        </div>
                                    </div>

                                    <div class="project-stats">
                                        <div class="stat-item">
                                            <span class="stat-value"><?php echo student_funding_format_amount($current_amount ? $current_amount : 0); ?></span>
                                            <span class="stat-label">現在の支援額</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value"><?php echo student_funding_format_amount($goal_amount); ?></span>
                                            <span class="stat-label">目標金額</span>
                                        </div>
                                        <div class="stat-item">
                                            <?php if ($remaining_days !== null) : ?>
                                                <span class="stat-value <?php echo $remaining_days <= 7 ? 'urgent' : ''; ?>">
                                                    残り<?php echo $remaining_days; ?>日
                                                </span>
                                                <span class="stat-label">締切まで</span>
                                            <?php else : ?>
                                                <span class="stat-value">-</span>
                                                <span class="stat-label">締切未設定</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div><!-- .projects-grid -->
        </div><!-- .container -->
    </section>

    <!-- CTAセクション -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>あなたも学生を応援しませんか？</h2>
                <p>小さな支援が、大きな夢を実現する力になります。</p>
                <a href="<?php echo get_post_type_archive_link('project'); ?>" class="btn btn-primary btn-large">
                    プロジェクト一覧を見る
                </a>
            </div>
        </div>
    </section>
</div><!-- .front-page -->

<?php
get_footer();
