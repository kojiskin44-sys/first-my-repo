<?php
/**
 * Template Name: 学生プロフィール
 *
 * @package Student_Funding_Theme
 */

get_header();
?>

<div class="student-profile-page">
    <div class="container">
        <?php
        while (have_posts()) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="page-header">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                </header>

                <div class="page-content">
                    <?php the_content(); ?>
                </div>

                <?php
                // この学生が関連するプロジェクトを表示（カスタムフィールドで学生名が一致する場合）
                $student_name = get_post_meta(get_the_ID(), '_student_name', true);

                if ($student_name) :
                    $projects = new WP_Query(array(
                        'post_type'      => 'project',
                        'posts_per_page' => -1,
                        'meta_query'     => array(
                            array(
                                'key'     => '_student_name',
                                'value'   => $student_name,
                                'compare' => '=',
                            ),
                        ),
                    ));

                    if ($projects->have_posts()) :
                        ?>
                        <div class="student-projects">
                            <h2>プロジェクト一覧</h2>
                            <div class="projects-grid">
                                <?php
                                while ($projects->have_posts()) : $projects->the_post();
                                    $goal_amount = get_post_meta(get_the_ID(), '_goal_amount', true);
                                    $current_amount = get_post_meta(get_the_ID(), '_current_amount', true);
                                    $achievement_rate = student_funding_get_achievement_rate(get_the_ID());
                                    $remaining_days = student_funding_get_remaining_days(get_the_ID());
                                    ?>
                                    <article class="project-card">
                                        <a href="<?php the_permalink(); ?>" class="project-card-link">
                                            <div class="project-thumbnail">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <?php the_post_thumbnail('project-thumbnail'); ?>
                                                <?php else : ?>
                                                    <div class="placeholder-image">
                                                        <span>画像なし</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="project-content">
                                                <h3 class="project-title"><?php the_title(); ?></h3>

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
                                                            <span class="stat-value">残り<?php echo $remaining_days; ?>日</span>
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
                                ?>
                            </div><!-- .projects-grid -->
                        </div>
                        <?php
                    endif;
                endif;
                ?>
            </article>
            <?php
        endwhile;
        ?>
    </div><!-- .container -->
</div><!-- .student-profile-page -->

<?php
get_footer();
