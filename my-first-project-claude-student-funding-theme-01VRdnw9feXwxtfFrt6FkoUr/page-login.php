<?php
/**
 * Template Name: ログイン
 *
 * @package Student_Funding_Theme
 */

// 既にログインしている場合はマイページへリダイレクト
if (is_user_logged_in()) {
    wp_redirect(home_url('/my-page/'));
    exit;
}

get_header();
?>

<div class="login-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <h1 class="auth-title">ログイン</h1>
                <p class="auth-description">プロジェクトの支援や作成にはログインが必要です</p>

                <form id="login-form" class="auth-form" method="post">
                    <?php wp_nonce_field('user_login', 'login_nonce'); ?>

                    <!-- ユーザー名またはメールアドレス -->
                    <div class="form-group">
                        <label for="login_username">ユーザー名またはメールアドレス <span class="required">*</span></label>
                        <input type="text" id="login_username" name="username" required>
                    </div>

                    <!-- パスワード -->
                    <div class="form-group">
                        <label for="login_password">パスワード <span class="required">*</span></label>
                        <input type="password" id="login_password" name="password" required>
                    </div>

                    <!-- ログイン状態を保持 -->
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" value="1">
                            ログイン状態を保持する
                        </label>
                    </div>

                    <!-- エラーメッセージ -->
                    <div id="login-error" class="form-error" style="display: none;"></div>

                    <!-- 送信ボタン -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large btn-block">
                            ログイン
                        </button>
                    </div>
                </form>

                <div class="auth-links">
                    <p><a href="<?php echo esc_url(wp_lostpassword_url()); ?>">パスワードをお忘れですか？</a></p>
                    <p>アカウントをお持ちでない方は<a href="<?php echo esc_url(home_url('/register/')); ?>">新規登録</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
