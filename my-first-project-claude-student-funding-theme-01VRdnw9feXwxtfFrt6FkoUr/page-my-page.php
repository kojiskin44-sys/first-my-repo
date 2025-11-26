<?php
/**
 * Template Name: マイページ
 *
 * @package Student_Funding_Theme
 */

// ログインしていない場合はログインページへリダイレクト
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

$current_user = wp_get_current_user();

get_header();
?>

<div class="my-page">
    <div class="container">
        <h1 class="page-title">マイページ</h1>

        <!-- ユーザー情報 -->
        <div class="user-info-section">
            <div class="user-header">
                <div class="user-avatar">
                    <?php echo get_avatar($current_user->ID, 80); ?>
                </div>
                <div class="user-details">
                    <h2><?php echo esc_html($current_user->display_name); ?></h2>
                    <p class="user-email"><?php echo esc_html($current_user->user_email); ?></p>
                </div>
                <div class="user-actions">
                    <a href="<?php echo esc_url(home_url('/edit-profile/')); ?>" class="btn btn-outline">プロフィール編集</a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn-outline">ログアウト</a>
                </div>
            </div>
        </div>

        <!-- タブナビゲーション -->
        <div class="my-page-tabs">
            <button class="tab-btn active" data-tab="supports">支援したプロジェクト</button>
            <button class="tab-btn" data-tab="projects">作成したプロジェクト</button>
            <button class="tab-btn" data-tab="favorites">お気に入り</button>
        </div>

        <!-- タブコンテンツ -->
        <div class="tab-contents">
            <!-- 支援したプロジェクト -->
            <div id="supports-tab" class="tab-content active">
                <h2>支援したプロジェクト</h2>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'project_supports';

                $supports = $wpdb->get_results($wpdb->prepare(
                    "SELECT s.*, p.post_title, p.ID as project_id
                     FROM $table_name s
                     LEFT JOIN {$wpdb->posts} p ON s.project_id = p.ID
                     WHERE s.supporter_email = %s AND s.status = 'completed'
                     ORDER BY s.support_date DESC
                     LIMIT 50",
                    $current_user->user_email
                ));

                if ($supports) :
                    ?>
                    <div class="supports-list">
                        <?php foreach ($supports as $support) : ?>
                            <div class="support-item">
                                <div class="support-info">
                                    <h3><a href="<?php echo get_permalink($support->project_id); ?>"><?php echo esc_html($support->post_title); ?></a></h3>
                                    <p class="support-date"><?php echo date('Y年m月d日', strtotime($support->support_date)); ?></p>
                                </div>
                                <div class="support-amount">
                                    <span class="amount"><?php echo student_funding_format_amount($support->support_amount); ?></span>
                                    <span class="label">支援金額</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="no-data">まだ支援したプロジェクトはありません。</p>
                    <a href="<?php echo get_post_type_archive_link('project'); ?>" class="btn btn-primary">プロジェクトを探す</a>
                <?php endif; ?>
            </div>

            <!-- 作成したプロジェクト -->
            <div id="projects-tab" class="tab-content">
                <h2>作成したプロジェクト</h2>
                <div class="create-project-btn-wrapper">
                    <a href="<?php echo esc_url(home_url('/create-project/')); ?>" class="btn btn-primary">新しいプロジェクトを作成</a>
                </div>

                <?php
                $user_projects = new WP_Query(array(
                    'post_type'      => 'project',
                    'author'         => $current_user->ID,
                    'posts_per_page' => 50,
                    'post_status'    => array('publish', 'pending', 'draft'),
                ));

                if ($user_projects->have_posts()) :
                    ?>
                    <div class="user-projects-list">
                        <?php while ($user_projects->have_posts()) : $user_projects->the_post();
                            $goal_amount = get_post_meta(get_the_ID(), '_goal_amount', true);
                            $current_amount = get_post_meta(get_the_ID(), '_current_amount', true);
                            $achievement_rate = student_funding_get_achievement_rate(get_the_ID());
                            $supporter_count = get_post_meta(get_the_ID(), '_supporter_count', true);
                        ?>
                            <div class="user-project-item">
                                <div class="project-thumbnail">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <div class="placeholder">画像なし</div>
                                    <?php endif; ?>
                                </div>
                                <div class="project-info">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p class="project-status">ステータス: <?php echo get_post_status() === 'publish' ? '公開中' : (get_post_status() === 'pending' ? '承認待ち' : '下書き'); ?></p>
                                    <div class="project-stats">
                                        <span>達成率: <?php echo $achievement_rate; ?>%</span>
                                        <span>支援者: <?php echo $supporter_count ? $supporter_count : 0; ?>人</span>
                                        <span>支援額: <?php echo student_funding_format_amount($current_amount ? $current_amount : 0); ?></span>
                                    </div>
                                </div>
                                <div class="project-actions">
                                    <a href="<?php echo get_edit_post_link(); ?>" class="btn btn-outline btn-small">編集</a>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-small">表示</a>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p class="no-data">まだプロジェクトを作成していません。</p>
                <?php endif; ?>
            </div>

            <!-- お気に入り -->
            <div id="favorites-tab" class="tab-content">
                <h2>お気に入りのプロジェクト</h2>
                <?php
                $favorites = get_user_meta($current_user->ID, 'favorite_projects', true);
                if ($favorites && is_array($favorites) && count($favorites) > 0) :
                    $favorite_projects = new WP_Query(array(
                        'post_type'      => 'project',
                        'post__in'       => $favorites,
                        'posts_per_page' => 50,
                    ));

                    if ($favorite_projects->have_posts()) :
                        ?>
                        <div class="favorites-grid">
                            <?php while ($favorite_projects->have_posts()) : $favorite_projects->the_post();
                                $goal_amount = get_post_meta(get_the_ID(), '_goal_amount', true);
                                $current_amount = get_post_meta(get_the_ID(), '_current_amount', true);
                                $achievement_rate = student_funding_get_achievement_rate(get_the_ID());
                            ?>
                                <div class="favorite-item">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium'); ?>
                                        <?php endif; ?>
                                        <h3><?php the_title(); ?></h3>
                                        <p class="achievement">達成率: <?php echo $achievement_rate; ?>%</p>
                                    </a>
                                </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="no-data">まだお気に入りのプロジェクトはありません。</p>
                    <a href="<?php echo get_post_type_archive_link('project'); ?>" class="btn btn-primary">プロジェクトを探す</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
