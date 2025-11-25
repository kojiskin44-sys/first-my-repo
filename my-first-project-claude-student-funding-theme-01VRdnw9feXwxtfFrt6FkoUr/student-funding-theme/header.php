<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="container">
            <div class="header-inner">
                <!-- ロゴ -->
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) :
                            ?>
                            <p class="site-description"><?php echo $description; ?></p>
                        <?php endif; ?>
                        <?php
                    }
                    ?>
                </div><!-- .site-branding -->

                <!-- ナビゲーションメニュー -->
                <nav id="site-navigation" class="main-navigation">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="hamburger-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span class="menu-text">メニュー</span>
                    </button>
                    <?php
                    // メニューが設定されている場合はそれを表示、ない場合はデフォルトメニューを表示
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => 'div',
                            'container_class' => 'menu-wrapper',
                            'fallback_cb'    => false,
                        ));
                    } else {
                        // デフォルトメニュー
                        ?>
                        <div class="menu-wrapper">
                            <ul id="primary-menu" class="menu">
                                <li><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a></li>
                                <li><a href="<?php echo get_post_type_archive_link('project'); ?>">新着</a></li>
                                <li><a href="<?php echo esc_url(home_url('/ranking/')); ?>">ランキング</a></li>
                                <li><a href="<?php echo esc_url(home_url('/about/')); ?>">会社概要</a></li>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </nav><!-- #site-navigation -->

                <!-- ヘッダーアクション -->
                <div class="header-actions">
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo esc_url(admin_url()); ?>" class="btn btn-outline">
                            管理画面
                        </a>
                        <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn-outline">
                            ログアウト
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn-outline">
                            ログイン
                        </a>
                    <?php endif; ?>
                </div>
            </div><!-- .header-inner -->
        </div><!-- .container -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">
