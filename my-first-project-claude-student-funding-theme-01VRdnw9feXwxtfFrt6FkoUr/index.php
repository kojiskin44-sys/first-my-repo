<?php
/**
 * メインテンプレートファイル
 *
 * @package Student_Funding_Theme
 */

get_header();
?>

<div class="container">
    <main id="primary" class="site-main">

        <?php if (have_posts()) : ?>

            <header class="page-header">
                <?php
                if (is_home() && !is_front_page()) :
                    ?>
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                    <?php
                endif;
                ?>
            </header><!-- .page-header -->

            <div class="posts-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <header class="entry-header">
                                <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>'); ?>
                            </header>

                            <div class="entry-meta">
                                <span class="posted-on"><?php echo get_the_date(); ?></span>
                            </div>

                            <div class="entry-summary">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="read-more">
                                続きを読む →
                            </a>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div><!-- .posts-grid -->

            <?php
            // ページネーション
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => '« 前へ',
                'next_text' => '次へ »',
            ));
            ?>

        <?php else : ?>

            <div class="no-results">
                <h1>投稿が見つかりませんでした</h1>
                <p>お探しの内容は見つかりませんでした。検索をお試しください。</p>
                <?php get_search_form(); ?>
            </div>

        <?php endif; ?>

    </main><!-- #primary -->
</div><!-- .container -->

<?php
get_footer();
