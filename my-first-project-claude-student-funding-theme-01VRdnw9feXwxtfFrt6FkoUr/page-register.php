<?php
/**
 * Template Name: 会員登録
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

<div class="register-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <h1 class="auth-title">新規会員登録</h1>
                <p class="auth-description">プロジェクトの支援や作成には会員登録が必要です</p>

                <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success') : ?>
                    <div class="notice notice-success">
                        <p>会員登録が完了しました！ログインしてください。</p>
                        <a href="<?php echo esc_url(home_url('/login/')); ?>" class="btn btn-primary">ログインページへ</a>
                    </div>
                <?php else : ?>
                    <form id="register-form" class="auth-form" method="post">
                        <?php wp_nonce_field('user_register', 'register_nonce'); ?>

                        <!-- ユーザー名 -->
                        <div class="form-group">
                            <label for="username">ユーザー名 <span class="required">*</span></label>
                            <input type="text" id="username" name="username" required
                                   pattern="[a-zA-Z0-9_]+"
                                   title="半角英数字とアンダースコアのみ使用できます">
                            <p class="form-help">半角英数字とアンダースコア（_）のみ使用できます</p>
                        </div>

                        <!-- メールアドレス -->
                        <div class="form-group">
                            <label for="email">メールアドレス <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <!-- パスワード -->
                        <div class="form-group">
                            <label for="password">パスワード <span class="required">*</span></label>
                            <input type="password" id="password" name="password" required minlength="8">
                            <p class="form-help">8文字以上で入力してください</p>
                        </div>

                        <!-- パスワード確認 -->
                        <div class="form-group">
                            <label for="password_confirm">パスワード（確認） <span class="required">*</span></label>
                            <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
                        </div>

                        <!-- 表示名 -->
                        <div class="form-group">
                            <label for="display_name">表示名 <span class="required">*</span></label>
                            <input type="text" id="display_name" name="display_name" required>
                            <p class="form-help">サイト上で表示される名前です</p>
                        </div>

                        <!-- 利用規約 -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="agree_terms" required>
                                <a href="<?php echo esc_url(home_url('/terms/')); ?>" target="_blank">利用規約</a>と<a href="<?php echo esc_url(home_url('/privacy/')); ?>" target="_blank">プライバシーポリシー</a>に同意する
                            </label>
                        </div>

                        <!-- エラーメッセージ -->
                        <div id="register-error" class="form-error" style="display: none;"></div>

                        <!-- 送信ボタン -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-large btn-block">
                                会員登録する
                            </button>
                        </div>
                    </form>

                    <div class="auth-links">
                        <p>すでにアカウントをお持ちの方は<a href="<?php echo esc_url(home_url('/login/')); ?>">ログイン</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
