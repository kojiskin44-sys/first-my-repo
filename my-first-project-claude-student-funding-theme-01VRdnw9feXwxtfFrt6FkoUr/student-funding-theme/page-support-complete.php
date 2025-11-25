<?php
/**
 * Template Name: 支援完了ページ
 *
 * @package Student_Funding_Theme
 */

get_header();

// 支援IDを取得
$support_id = isset($_GET['support_id']) ? intval($_GET['support_id']) : 0;

// 支援情報を取得
$support = null;
if ($support_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_supports';
    $support = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $support_id
    ));
}
?>

<div class="support-complete-page">
    <div class="container">
        <div class="complete-content">
            <?php if ($support) : ?>
                <!-- 支援情報がある場合 -->
                <div class="complete-icon">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="#4CAF50"/>
                        <path d="M25 40L35 50L55 30" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h1 class="complete-title">支援申込みが完了しました！</h1>

                <div class="complete-message">
                    <p>ご支援いただき、誠にありがとうございます。</p>
                    <p>以下の内容で支援申込みを受け付けました。</p>
                </div>

                <div class="support-details">
                    <h2>支援内容</h2>
                    <table class="details-table">
                        <tr>
                            <th>プロジェクト</th>
                            <td>
                                <?php
                                $project = get_post($support->project_id);
                                if ($project) {
                                    echo '<a href="' . get_permalink($project->ID) . '">' . esc_html($project->post_title) . '</a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>支援額</th>
                            <td class="amount"><?php echo student_funding_format_amount($support->support_amount); ?></td>
                        </tr>
                        <tr>
                            <th>決済方法</th>
                            <td><?php echo $support->payment_method === 'paypal' ? 'PayPal' : '銀行振込'; ?></td>
                        </tr>
                        <tr>
                            <th>お名前</th>
                            <td><?php echo $support->is_anonymous ? '匿名' : esc_html($support->supporter_name); ?></td>
                        </tr>
                        <tr>
                            <th>メールアドレス</th>
                            <td><?php echo esc_html($support->supporter_email); ?></td>
                        </tr>
                    </table>
                </div>

                <?php if ($support->payment_method === 'bank') : ?>
                    <!-- 銀行振込の場合 -->
                    <div class="bank-info-section">
                        <h2>お振込先情報</h2>
                        <div class="bank-info-box">
                            <?php
                            $bank_info = get_post_meta($support->project_id, '_bank_info', true);
                            if ($bank_info) {
                                echo wpautop(esc_html($bank_info));
                            } else {
                                echo '<p>振込先情報は別途メールでお送りします。</p>';
                            }
                            ?>
                        </div>
                        <div class="bank-notes">
                            <h3>お振込みについて</h3>
                            <ul>
                                <li>振込手数料はご負担いただきますようお願いいたします</li>
                                <li>お振込み名義は、ご登録いただいたお名前でお願いします</li>
                                <li>お振込み確認後、支援が確定されます</li>
                                <li>確認メールを送信しましたので、ご確認ください</li>
                            </ul>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- PayPalの場合 -->
                    <div class="paypal-info-section">
                        <h2>PayPal決済について</h2>
                        <div class="paypal-notes">
                            <p>PayPalでの決済が完了すると、支援が確定されます。</p>
                            <p>確認メールを送信しましたので、ご確認ください。</p>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <!-- 支援情報がない場合 -->
                <div class="complete-icon">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="#4CAF50"/>
                        <path d="M25 40L35 50L55 30" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h1 class="complete-title">支援ありがとうございます！</h1>

                <div class="complete-message">
                    <p>ご支援いただき、誠にありがとうございます。</p>
                    <p>学生の夢を応援していただき、心より感謝申し上げます。</p>
                </div>
            <?php endif; ?>

            <div class="next-actions">
                <h2>次のステップ</h2>
                <div class="action-buttons">
                    <?php if ($support && $support->project_id) : ?>
                        <a href="<?php echo get_permalink($support->project_id); ?>" class="btn btn-primary">
                            プロジェクトに戻る
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo get_post_type_archive_link('project'); ?>" class="btn btn-outline">
                        他のプロジェクトを見る
                    </a>
                    <a href="<?php echo home_url(); ?>" class="btn btn-outline">
                        トップページへ
                    </a>
                </div>
            </div>

            <div class="thank-you-message">
                <p>学生の夢の実現を応援していただき、本当にありがとうございます。</p>
                <p>今後とも、どうぞよろしくお願いいたします。</p>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
