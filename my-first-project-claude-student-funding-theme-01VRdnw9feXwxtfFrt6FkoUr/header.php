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
                    // カスタムメニューがあればそれを表示、なければデフォルトメニュー
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => 'div',
                            'container_class' => 'menu-wrapper',
                        ));
                    } else {
                        // デフォルトメニュー
                        ?>
                        <div class="menu-wrapper">
                            <ul id="primary-menu" class="menu">
                                <li><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a></li>
                                <li><a href="<?php echo esc_url(get_post_type_archive_link('project')); ?>">新着</a></li>
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
                    <?php if (is_user_logged_in()) :
                        $current_user = wp_get_current_user();
                        $display_name = $current_user->display_name ? $current_user->display_name : $current_user->user_login;
                    ?>
                        <div class="user-menu-container">
                            <button class="user-menu-button btn-auth">
                                <?php echo esc_html($display_name); ?>
                                <svg class="user-menu-arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <div class="user-dropdown-menu">
                                <a href="<?php echo esc_url(home_url('/my-page/')); ?>" class="dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 8C10.21 8 12 6.21 12 4C12 1.79 10.21 0 8 0C5.79 0 4 1.79 4 4C4 6.21 5.79 8 8 8ZM8 10C5.33 10 0 11.34 0 14V16H16V14C16 11.34 10.67 10 8 10Z" fill="currentColor"/>
                                    </svg>
                                    プロフィール
                                </a>
                                <a href="<?php echo esc_url(admin_url()); ?>" class="dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14 0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H14C15.1 16 16 15.1 16 14V2C16 0.9 15.1 0 14 0ZM14 14H2V2H14V14ZM12 8H8V12H6V8H4L8 4L12 8Z" fill="currentColor"/>
                                    </svg>
                                    管理画面
                                </a>
                                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 14H2V2H6V0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H6V14ZM7 3L5.59 4.41L8.17 7H0V9H8.17L5.59 11.59L7 13L12 8L7 3Z" fill="currentColor"/>
                                    </svg>
                                    ログアウト
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn-auth">
                            ログイン・新規登録
                        </a>
                    <?php endif; ?>
                </div>
            </div><!-- .header-inner -->
        </div><!-- .container -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">
