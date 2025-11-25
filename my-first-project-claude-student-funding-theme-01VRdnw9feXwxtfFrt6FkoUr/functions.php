<?php
/**
 * Student Funding Theme Functions
 *
 * @package Student_Funding_Theme
 * @version 1.0.0
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

// ===================================
// 1. テーマセットアップ
// ===================================

/**
 * テーマの初期設定
 */
function student_funding_setup() {
    // テーマのテキストドメイン読み込み
    load_theme_textdomain('student-funding-theme', get_template_directory() . '/languages');

    // タイトルタグのサポート
    add_theme_support('title-tag');

    // アイキャッチ画像のサポート
    add_theme_support('post-thumbnails');

    // カスタム画像サイズ
    add_image_size('project-thumbnail', 800, 600, true);
    add_image_size('project-large', 1200, 800, true);
    add_image_size('student-avatar', 150, 150, true);

    // HTML5サポート
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // カスタムロゴ
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // メニューの登録
    register_nav_menus(array(
        'primary' => __('プライマリーメニュー', 'student-funding-theme'),
        'footer'  => __('フッターメニュー', 'student-funding-theme'),
    ));
}
add_action('after_setup_theme', 'student_funding_setup');

/**
 * CSS・JavaScriptの読み込み
 */
function student_funding_enqueue_scripts() {
    // スタイルシート
    wp_enqueue_style('student-funding-style', get_template_directory_uri() . '/assets/css/style.css', array(), '1.0.0');

    // JavaScript
    wp_enqueue_script('student-funding-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);

    // AJAX用の設定をJavaScriptに渡す
    wp_localize_script('student-funding-main', 'studentFunding', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('student_funding_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'student_funding_enqueue_scripts');

// ===================================
// 2. カスタム投稿タイプ「project」
// ===================================

/**
 * プロジェクト投稿タイプの登録
 */
function student_funding_register_project_post_type() {
    $labels = array(
        'name'               => 'プロジェクト',
        'singular_name'      => 'プロジェクト',
        'menu_name'          => 'プロジェクト',
        'add_new'            => '新規追加',
        'add_new_item'       => '新しいプロジェクトを追加',
        'edit_item'          => 'プロジェクトを編集',
        'new_item'           => '新しいプロジェクト',
        'view_item'          => 'プロジェクトを表示',
        'search_items'       => 'プロジェクトを検索',
        'not_found'          => 'プロジェクトが見つかりませんでした',
        'not_found_in_trash' => 'ゴミ箱にプロジェクトはありません',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'project'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-heart',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'        => true,
    );

    register_post_type('project', $args);
}
add_action('init', 'student_funding_register_project_post_type');

/**
 * プロジェクトカテゴリーの登録
 */
function student_funding_register_taxonomies() {
    $labels = array(
        'name'              => 'プロジェクトカテゴリー',
        'singular_name'     => 'カテゴリー',
        'search_items'      => 'カテゴリーを検索',
        'all_items'         => 'すべてのカテゴリー',
        'edit_item'         => 'カテゴリーを編集',
        'update_item'       => 'カテゴリーを更新',
        'add_new_item'      => '新しいカテゴリーを追加',
        'new_item_name'     => '新しいカテゴリー名',
        'menu_name'         => 'カテゴリー',
    );

    register_taxonomy('project_category', array('project'), array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'project-category'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'student_funding_register_taxonomies');

/**
 * デフォルトカテゴリーの作成
 */
function student_funding_create_default_categories() {
    // カテゴリーが既に存在するかチェック
    $categories_exist = term_exists('ビジネス', 'project_category');

    if (!$categories_exist) {
        $default_categories = array(
            'ビジネス',
            '学業（留学）',
            '部活',
            '社会活動',
            'クリエイティブ'
        );

        foreach ($default_categories as $category) {
            if (!term_exists($category, 'project_category')) {
                wp_insert_term($category, 'project_category');
            }
        }
    }
}
add_action('init', 'student_funding_create_default_categories', 20);

// ===================================
// 3. カスタムメタボックス
// ===================================

/**
 * メタボックスの追加
 */
function student_funding_add_meta_boxes() {
    // プロジェクト情報
    add_meta_box(
        'project_details',
        'プロジェクト詳細',
        'student_funding_project_details_callback',
        'project',
        'normal',
        'high'
    );

    // 学生プロフィール
    add_meta_box(
        'student_profile',
        '学生プロフィール',
        'student_funding_student_profile_callback',
        'project',
        'normal',
        'high'
    );

    // 支援情報
    add_meta_box(
        'support_info',
        '支援情報（自動更新）',
        'student_funding_support_info_callback',
        'project',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'student_funding_add_meta_boxes');

/**
 * プロジェクト詳細メタボックス
 */
function student_funding_project_details_callback($post) {
    wp_nonce_field('student_funding_save_meta', 'student_funding_meta_nonce');

    $goal_amount = get_post_meta($post->ID, '_goal_amount', true);
    $deadline = get_post_meta($post->ID, '_deadline', true);
    $paypal_link = get_post_meta($post->ID, '_paypal_link', true);
    $bank_info = get_post_meta($post->ID, '_bank_info', true);
    $return_info = get_post_meta($post->ID, '_return_info', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="goal_amount">目標金額（円）</label></th>
            <td>
                <input type="number" id="goal_amount" name="goal_amount" value="<?php echo esc_attr($goal_amount); ?>" class="regular-text" min="0" step="1000">
                <p class="description">例: 100000</p>
            </td>
        </tr>
        <tr>
            <th><label for="deadline">支援締切日</label></th>
            <td>
                <input type="date" id="deadline" name="deadline" value="<?php echo esc_attr($deadline); ?>" class="regular-text">
                <p class="description">支援を受け付ける最終日を設定してください</p>
            </td>
        </tr>
        <tr>
            <th><label for="paypal_link">PayPalリンク</label></th>
            <td>
                <input type="url" id="paypal_link" name="paypal_link" value="<?php echo esc_url($paypal_link); ?>" class="regular-text">
                <p class="description">PayPalの支払いリンクを入力してください</p>
            </td>
        </tr>
        <tr>
            <th><label for="bank_info">銀行振込情報</label></th>
            <td>
                <textarea id="bank_info" name="bank_info" rows="5" class="large-text"><?php echo esc_textarea($bank_info); ?></textarea>
                <p class="description">銀行名・支店名・口座番号などを入力してください</p>
            </td>
        </tr>
        <tr>
            <th><label for="return_info">リターン情報</label></th>
            <td>
                <textarea id="return_info" name="return_info" rows="5" class="large-text"><?php echo esc_textarea($return_info); ?></textarea>
                <p class="description">リターン内容を記載（基本は辞退可能）</p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * 学生プロフィールメタボックス
 */
function student_funding_student_profile_callback($post) {
    $student_name = get_post_meta($post->ID, '_student_name', true);
    $student_school = get_post_meta($post->ID, '_student_school', true);
    $student_grade = get_post_meta($post->ID, '_student_grade', true);
    $student_photo_id = get_post_meta($post->ID, '_student_photo_id', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="student_name">学生名</label></th>
            <td>
                <input type="text" id="student_name" name="student_name" value="<?php echo esc_attr($student_name); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="student_school">学校名</label></th>
            <td>
                <input type="text" id="student_school" name="student_school" value="<?php echo esc_attr($student_school); ?>" class="regular-text">
                <p class="description">例: ○○大学、○○高校</p>
            </td>
        </tr>
        <tr>
            <th><label for="student_grade">学年</label></th>
            <td>
                <input type="text" id="student_grade" name="student_grade" value="<?php echo esc_attr($student_grade); ?>" class="regular-text">
                <p class="description">例: 2年生、大学3年</p>
            </td>
        </tr>
        <tr>
            <th><label>学生写真</label></th>
            <td>
                <div id="student-photo-preview">
                    <?php if ($student_photo_id):
                        echo wp_get_attachment_image($student_photo_id, 'student-avatar');
                    endif; ?>
                </div>
                <input type="hidden" id="student_photo_id" name="student_photo_id" value="<?php echo esc_attr($student_photo_id); ?>">
                <button type="button" class="button" id="upload_student_photo_button">写真を選択</button>
                <button type="button" class="button" id="remove_student_photo_button">写真を削除</button>
                <script>
                jQuery(document).ready(function($) {
                    var mediaUploader;
                    $('#upload_student_photo_button').click(function(e) {
                        e.preventDefault();
                        if (mediaUploader) {
                            mediaUploader.open();
                            return;
                        }
                        mediaUploader = wp.media({
                            title: '学生写真を選択',
                            button: { text: '写真を選択' },
                            multiple: false
                        });
                        mediaUploader.on('select', function() {
                            var attachment = mediaUploader.state().get('selection').first().toJSON();
                            $('#student_photo_id').val(attachment.id);
                            $('#student-photo-preview').html('<img src="' + attachment.url + '" style="max-width: 150px;">');
                        });
                        mediaUploader.open();
                    });
                    $('#remove_student_photo_button').click(function(e) {
                        e.preventDefault();
                        $('#student_photo_id').val('');
                        $('#student-photo-preview').html('');
                    });
                });
                </script>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * 支援情報メタボックス（読み取り専用）
 */
function student_funding_support_info_callback($post) {
    $current_amount = get_post_meta($post->ID, '_current_amount', true);
    $supporter_count = get_post_meta($post->ID, '_supporter_count', true);
    $goal_amount = get_post_meta($post->ID, '_goal_amount', true);

    $current_amount = $current_amount ? $current_amount : 0;
    $supporter_count = $supporter_count ? $supporter_count : 0;
    $achievement_rate = $goal_amount > 0 ? round(($current_amount / $goal_amount) * 100, 1) : 0;
    ?>
    <p><strong>現在の支援額:</strong> <?php echo number_format($current_amount); ?> 円</p>
    <p><strong>支援者数:</strong> <?php echo number_format($supporter_count); ?> 人</p>
    <p><strong>達成率:</strong> <?php echo $achievement_rate; ?>%</p>
    <p class="description">※これらの値は支援があると自動で更新されます</p>
    <?php
}

/**
 * メタデータの保存
 */
function student_funding_save_meta($post_id) {
    // ノンス検証
    if (!isset($_POST['student_funding_meta_nonce']) ||
        !wp_verify_nonce($_POST['student_funding_meta_nonce'], 'student_funding_save_meta')) {
        return;
    }

    // 自動保存の場合は処理しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // プロジェクト詳細の保存
    $fields = array(
        'goal_amount',
        'deadline',
        'paypal_link',
        'bank_info',
        'return_info',
        'student_name',
        'student_school',
        'student_grade',
        'student_photo_id',
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_project', 'student_funding_save_meta');

// ===================================
// 4. 支援データ用テーブル作成
// ===================================

/**
 * 支援データ保存用のカスタムテーブルを作成
 */
function student_funding_create_support_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        project_id bigint(20) NOT NULL,
        supporter_name varchar(255) DEFAULT NULL,
        supporter_email varchar(255) NOT NULL,
        support_amount int(11) NOT NULL,
        payment_method varchar(50) NOT NULL,
        is_anonymous tinyint(1) DEFAULT 0,
        decline_return tinyint(1) DEFAULT 0,
        message text,
        support_date datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'pending',
        PRIMARY KEY  (id),
        KEY project_id (project_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'student_funding_create_support_table');
add_action('after_switch_theme', 'student_funding_create_support_table');

// ===================================
// 5. 支援フォーム処理
// ===================================

/**
 * 支援フォームのAJAX処理
 */
function student_funding_process_support() {
    // ノンス検証
    check_ajax_referer('student_funding_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';

    // データの取得とサニタイズ
    $project_id = intval($_POST['project_id']);
    $supporter_name = sanitize_text_field($_POST['supporter_name']);
    $supporter_email = sanitize_email($_POST['supporter_email']);
    $support_amount = intval($_POST['support_amount']);
    $payment_method = sanitize_text_field($_POST['payment_method']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $decline_return = isset($_POST['decline_return']) ? 1 : 0;
    $message = sanitize_textarea_field($_POST['message']);

    // バリデーション
    if (!$project_id || !$supporter_email || $support_amount <= 0) {
        wp_send_json_error(array('message' => '必須項目が入力されていません。'));
        return;
    }

    if (!is_email($supporter_email)) {
        wp_send_json_error(array('message' => '有効なメールアドレスを入力してください。'));
        return;
    }

    // 支援データを保存
    $result = $wpdb->insert(
        $table_name,
        array(
            'project_id'       => $project_id,
            'supporter_name'   => $supporter_name,
            'supporter_email'  => $supporter_email,
            'support_amount'   => $support_amount,
            'payment_method'   => $payment_method,
            'is_anonymous'     => $is_anonymous,
            'decline_return'   => $decline_return,
            'message'          => $message,
            'status'           => 'pending', // 初期状態は「保留中」
        ),
        array('%d', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%s')
    );

    if ($result === false) {
        wp_send_json_error(array('message' => 'データベースエラーが発生しました。'));
        return;
    }

    // プロジェクトの支援額と支援者数を更新
    student_funding_update_project_stats($project_id);

    // 決済方法に応じてリダイレクトURLを返す
    $response = array(
        'success' => true,
        'support_id' => $wpdb->insert_id,
    );

    if ($payment_method === 'paypal') {
        $paypal_link = get_post_meta($project_id, '_paypal_link', true);
        $response['redirect_url'] = $paypal_link ? $paypal_link : home_url('/support-complete/');
    } else {
        $response['redirect_url'] = home_url('/support-complete/?support_id=' . $wpdb->insert_id);
    }

    wp_send_json_success($response);
}
add_action('wp_ajax_process_support', 'student_funding_process_support');
add_action('wp_ajax_nopriv_process_support', 'student_funding_process_support');

/**
 * プロジェクトの統計情報を更新
 */
function student_funding_update_project_stats($project_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';

    // 承認済み（completed）の支援の合計金額と件数を取得
    $stats = $wpdb->get_row($wpdb->prepare(
        "SELECT SUM(support_amount) as total_amount, COUNT(*) as supporter_count
         FROM $table_name
         WHERE project_id = %d AND status = 'completed'",
        $project_id
    ));

    $total_amount = $stats->total_amount ? $stats->total_amount : 0;
    $supporter_count = $stats->supporter_count ? $stats->supporter_count : 0;

    // メタデータを更新
    update_post_meta($project_id, '_current_amount', $total_amount);
    update_post_meta($project_id, '_supporter_count', $supporter_count);
}

// ===================================
// 6. ヘルパー関数
// ===================================

/**
 * 達成率を計算
 */
function student_funding_get_achievement_rate($project_id) {
    $goal_amount = get_post_meta($project_id, '_goal_amount', true);
    $current_amount = get_post_meta($project_id, '_current_amount', true);

    if (!$goal_amount || $goal_amount == 0) {
        return 0;
    }

    $rate = ($current_amount / $goal_amount) * 100;
    return min(round($rate, 1), 100); // 100%を上限とする
}

/**
 * 残り日数を計算
 */
function student_funding_get_remaining_days($project_id) {
    $deadline = get_post_meta($project_id, '_deadline', true);

    if (!$deadline) {
        return null;
    }

    $deadline_timestamp = strtotime($deadline);
    $today_timestamp = strtotime('today');
    $diff = $deadline_timestamp - $today_timestamp;

    if ($diff < 0) {
        return 0; // 終了済み
    }

    return ceil($diff / (60 * 60 * 24));
}

/**
 * 支援者一覧を取得
 */
function student_funding_get_supporters($project_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';

    $supporters = $wpdb->get_results($wpdb->prepare(
        "SELECT supporter_name, support_amount, is_anonymous, support_date
         FROM $table_name
         WHERE project_id = %d AND status = 'completed'
         ORDER BY support_date DESC",
        $project_id
    ));

    return $supporters;
}

/**
 * フォーマットされた金額を返す
 */
function student_funding_format_amount($amount) {
    return number_format($amount) . ' 円';
}

/**
 * プロジェクトが終了しているかチェック
 */
function student_funding_is_project_ended($project_id) {
    $remaining_days = student_funding_get_remaining_days($project_id);
    return $remaining_days !== null && $remaining_days <= 0;
}

// ===================================
// 7. 管理画面のカスタマイズ
// ===================================

/**
 * 支援データ管理ページの追加
 */
function student_funding_add_admin_menu() {
    add_menu_page(
        '支援データ管理',
        '支援データ',
        'manage_options',
        'student-funding-supports',
        'student_funding_supports_page',
        'dashicons-money-alt',
        26
    );
}
add_action('admin_menu', 'student_funding_add_admin_menu');

/**
 * 支援データ管理ページの表示
 */
function student_funding_supports_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';

    // ステータス更新処理
    if (isset($_POST['update_status']) && isset($_POST['support_id']) && isset($_POST['new_status'])) {
        check_admin_referer('update_support_status');
        $support_id = intval($_POST['support_id']);
        $new_status = sanitize_text_field($_POST['new_status']);

        $wpdb->update(
            $table_name,
            array('status' => $new_status),
            array('id' => $support_id),
            array('%s'),
            array('%d')
        );

        // ステータスがcompletedに変わった場合、プロジェクトの統計を更新
        if ($new_status === 'completed') {
            $support = $wpdb->get_row($wpdb->prepare("SELECT project_id FROM $table_name WHERE id = %d", $support_id));
            if ($support) {
                student_funding_update_project_stats($support->project_id);
            }
        }

        echo '<div class="notice notice-success"><p>ステータスを更新しました。</p></div>';
    }

    // 支援データ一覧取得
    $supports = $wpdb->get_results(
        "SELECT s.*, p.post_title as project_title
         FROM $table_name s
         LEFT JOIN {$wpdb->posts} p ON s.project_id = p.ID
         ORDER BY s.support_date DESC
         LIMIT 100"
    );

    ?>
    <div class="wrap">
        <h1>支援データ管理</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>プロジェクト</th>
                    <th>支援者名</th>
                    <th>メールアドレス</th>
                    <th>支援額</th>
                    <th>決済方法</th>
                    <th>日時</th>
                    <th>ステータス</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($supports): ?>
                    <?php foreach ($supports as $support): ?>
                        <tr>
                            <td><?php echo esc_html($support->id); ?></td>
                            <td><?php echo esc_html($support->project_title); ?></td>
                            <td><?php echo $support->is_anonymous ? '匿名' : esc_html($support->supporter_name); ?></td>
                            <td><?php echo esc_html($support->supporter_email); ?></td>
                            <td><?php echo student_funding_format_amount($support->support_amount); ?></td>
                            <td><?php echo $support->payment_method === 'paypal' ? 'PayPal' : '銀行振込'; ?></td>
                            <td><?php echo esc_html($support->support_date); ?></td>
                            <td>
                                <?php
                                $status_labels = array(
                                    'pending' => '保留中',
                                    'completed' => '完了',
                                    'cancelled' => 'キャンセル',
                                );
                                echo esc_html($status_labels[$support->status] ?? $support->status);
                                ?>
                            </td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field('update_support_status'); ?>
                                    <input type="hidden" name="support_id" value="<?php echo esc_attr($support->id); ?>">
                                    <select name="new_status">
                                        <option value="pending" <?php selected($support->status, 'pending'); ?>>保留中</option>
                                        <option value="completed" <?php selected($support->status, 'completed'); ?>>完了</option>
                                        <option value="cancelled" <?php selected($support->status, 'cancelled'); ?>>キャンセル</option>
                                    </select>
                                    <button type="submit" name="update_status" class="button button-small">更新</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">支援データがありません</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * メディアアップローダーのスクリプトを読み込む
 */
function student_funding_admin_scripts($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'student_funding_admin_scripts');

// ===================================
// 8. サンプルデータの読み込み
// ===================================

// サンプルデータ作成機能を読み込む
require_once get_template_directory() . '/sample-data.php';
