    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-content">
                <!-- フッターウィジェットエリア（将来的に追加可能） -->
                <div class="footer-widgets">
                    <div class="footer-widget">
                        <h3>このサイトについて</h3>
                        <p><?php echo get_bloginfo('description'); ?></p>
                    </div>

                    <div class="footer-widget">
                        <h3>リンク</h3>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_id'        => 'footer-menu',
                            'fallback_cb'    => false,
                            'depth'          => 1,
                        ));
                        ?>
                    </div>

                    <div class="footer-widget">
                        <h3>お問い合わせ</h3>
                        <p>学生の夢を応援するクラウドファンディングプラットフォーム</p>
                    </div>
                </div><!-- .footer-widgets -->

                <!-- フッター情報 -->
                <div class="site-info">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
                    <p>
                        <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">プライバシーポリシー</a> |
                        <a href="<?php echo esc_url(home_url('/terms/')); ?>">利用規約</a>
                    </p>
                </div><!-- .site-info -->
            </div><!-- .footer-content -->
        </div><!-- .container -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
