<?php
/**
 * プロジェクトカテゴリーアーカイブテンプレート
 *
 * @package Student_Funding_Theme
 */

get_header();
?>

<div class="archive-page">
    <div class="container">
        <!-- ページヘッダー -->
        <header class="archive-header">
            <h1 class="archive-title"><?php single_term_title(); ?></h1>
            <?php
            $term_description = term_description();
            if (!empty($term_description)) :
                ?>
                <div class="archive-description"><?php echo $term_description; ?></div>
            <?php endif; ?>
        </header>

        <!-- フィルター -->
        <div class="archive-filters">
            <div class="filter-categories">
                <?php
                $current_term = get_queried_object();
                $categories = get_terms(array(
                    'taxonomy'   => 'project_category',
                    'hide_empty' => false,
                ));

                if ($categories && !is_wp_error($categories)) :
                    ?>
                    <a href="<?php echo get_post_type_archive_link('project'); ?>" class="filter-btn">
                        すべて
                    </a>
                    <?php
                    foreach ($categories as $category) :
                        $is_active = is_tax('project_category', $category->term_id) ? 'active' : '';
                        ?>
                        <a href="<?php echo get_term_link($category); ?>" class="filter-btn <?php echo $is_active; ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- プロジェクト一覧 -->
        <main class="archive-content">
            <?php
            // ページ番号を取得
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

            // カテゴリーのクエリ
            $args = array(
                'post_type'      => 'project',
                'posts_per_page' => 12,
                'paged'          => $paged,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'project_category',
                        'field'    => 'slug',
                        'terms'    => get_queried_object()->slug,
                    ),
                ),
            );

            $projects_query = new WP_Query($args);

            if ($projects_query->have_posts()) : ?>
                <div class="projects-grid">
                    <?php
                    while ($projects_query->have_posts()) : $projects_query->the_post();
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
                                    <h2 class="project-title"><?php the_title(); ?></h2>

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
                    ?>
                </div><!-- .projects-grid -->

                <!-- ページネーション -->
                <div class="pagination">
                    <?php
                    echo paginate_links(array(
                        'total'     => $projects_query->max_num_pages,
                        'current'   => $paged,
                        'prev_text' => '« 前へ',
                        'next_text' => '次へ »',
                        'mid_size'  => 2,
                    ));
                    ?>
                </div>
                <?php
                wp_reset_postdata();
                ?>

            <?php else : ?>
                <div class="no-projects">
                    <p>このカテゴリーにプロジェクトが見つかりませんでした。</p>
                    <a href="<?php echo get_post_type_archive_link('project'); ?>" class="btn btn-primary">
                        すべてのプロジェクトを見る
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div><!-- .container -->
</div><!-- .archive-page -->

<?php
get_footer();
