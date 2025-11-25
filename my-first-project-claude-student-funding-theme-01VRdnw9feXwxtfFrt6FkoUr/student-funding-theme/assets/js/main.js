/**
 * Student Funding Theme JavaScript
 *
 * @package Student_Funding_Theme
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // ===================================
    // 1. ページ読み込み時の初期化
    // ===================================

    $(document).ready(function() {
        // 進捗バーのアニメーション
        initProgressBars();

        // モーダルの初期化
        initModal();

        // 支援フォームの初期化
        initSupportForm();

        // モバイルメニュー
        initMobileMenu();

        // リンクコピー機能
        initCopyLink();
    });

    // ===================================
    // 2. 進捗バーアニメーション
    // ===================================

    function initProgressBars() {
        $('.progress-fill').each(function() {
            var $this = $(this);
            var targetWidth = $this.attr('style').match(/width:\s*(\d+)%/);

            if (targetWidth) {
                var width = targetWidth[1];
                $this.css('width', '0%');

                // アニメーションを遅延実行
                setTimeout(function() {
                    $this.css('width', width + '%');
                }, 300);
            }
        });
    }

    // ===================================
    // 3. モーダル制御
    // ===================================

    function initModal() {
        var $modal = $('#support-modal');
        var $openButton = $('#open-support-modal');
        var $closeButton = $('#close-support-modal');

        // モーダルを開く
        $openButton.on('click', function(e) {
            e.preventDefault();
            $modal.addClass('active');
            $('body').css('overflow', 'hidden');
        });

        // モーダルを閉じる
        $closeButton.on('click', function(e) {
            e.preventDefault();
            closeModal();
        });

        // 背景クリックで閉じる
        $modal.on('click', function(e) {
            if ($(e.target).is('#support-modal')) {
                closeModal();
            }
        });

        // ESCキーで閉じる
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $modal.hasClass('active')) {
                closeModal();
            }
        });

        function closeModal() {
            $modal.removeClass('active');
            $('body').css('overflow', '');
        }
    }

    // ===================================
    // 4. 支援フォーム処理
    // ===================================

    function initSupportForm() {
        var $form = $('#support-form');
        var $errorDiv = $('#form-error');

        $form.on('submit', function(e) {
            e.preventDefault();

            // エラーメッセージをクリア
            $errorDiv.hide().text('');

            // バリデーション
            if (!validateForm()) {
                return false;
            }

            // 送信ボタンを無効化
            var $submitButton = $form.find('button[type="submit"]');
            var originalText = $submitButton.text();
            $submitButton.prop('disabled', true).text('処理中...');

            // フォームデータを取得
            var formData = {
                action: 'process_support',
                nonce: studentFunding.nonce,
                project_id: $form.find('input[name="project_id"]').val(),
                support_amount: $form.find('input[name="support_amount"]').val(),
                supporter_name: $form.find('input[name="supporter_name"]').val(),
                supporter_email: $form.find('input[name="supporter_email"]').val(),
                payment_method: $form.find('input[name="payment_method"]:checked').val(),
                is_anonymous: $form.find('input[name="is_anonymous"]').is(':checked') ? 1 : 0,
                decline_return: $form.find('input[name="decline_return"]').is(':checked') ? 1 : 0,
                message: $form.find('textarea[name="message"]').val()
            };

            // AJAX送信
            $.ajax({
                url: studentFunding.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // 成功時はリダイレクト
                        if (response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        }
                    } else {
                        // エラー表示
                        showError(response.data.message || '申込みに失敗しました。');
                        $submitButton.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    showError('通信エラーが発生しました。もう一度お試しください。');
                    $submitButton.prop('disabled', false).text(originalText);
                }
            });
        });

        // バリデーション関数
        function validateForm() {
            var errors = [];

            // 支援額のチェック
            var amount = parseInt($form.find('input[name="support_amount"]').val());
            if (!amount || amount < 100) {
                errors.push('支援額は100円以上を入力してください。');
            }

            // メールアドレスのチェック
            var email = $form.find('input[name="supporter_email"]').val();
            if (!email || !isValidEmail(email)) {
                errors.push('有効なメールアドレスを入力してください。');
            }

            // 決済方法のチェック
            var paymentMethod = $form.find('input[name="payment_method"]:checked').val();
            if (!paymentMethod) {
                errors.push('決済方法を選択してください。');
            }

            // エラーがあれば表示
            if (errors.length > 0) {
                showError(errors.join('<br>'));
                return false;
            }

            return true;
        }

        // メールアドレスの形式チェック
        function isValidEmail(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        // エラーメッセージを表示
        function showError(message) {
            $errorDiv.html(message).show();
            // エラー表示位置までスクロール
            $('html, body').animate({
                scrollTop: $errorDiv.offset().top - 100
            }, 300);
        }
    }

    // ===================================
    // 5. モバイルメニュー
    // ===================================

    function initMobileMenu() {
        var $menuToggle = $('.menu-toggle');
        var $menuWrapper = $('.menu-wrapper');

        $menuToggle.on('click', function() {
            $menuWrapper.toggleClass('active');
            $(this).toggleClass('active');

            // アクセシビリティ
            var expanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !expanded);
        });

        // ウィンドウリサイズ時にメニューをリセット
        $(window).on('resize', function() {
            if ($(window).width() > 768) {
                $menuWrapper.removeClass('active');
                $menuToggle.removeClass('active').attr('aria-expanded', 'false');
            }
        });
    }

    // ===================================
    // 6. スムーススクロール
    // ===================================

    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });

    // ===================================
    // 7. 画像の遅延読み込み（Lazy Loading）
    // ===================================

    if ('loading' in HTMLImageElement.prototype) {
        // ネイティブのLazy Loadingがサポートされている場合
        var images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(function(img) {
            img.src = img.dataset.src;
        });
    } else {
        // Intersection Observer を使った代替実装
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            var lazyImages = document.querySelectorAll('img.lazy');
            lazyImages.forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    // ===================================
    // 8. 支援額のリアルタイム計算
    // ===================================

    $('#support_amount').on('input', function() {
        var amount = parseInt($(this).val());
        if (amount && amount >= 100) {
            // 手数料の計算などを追加可能
            // 例: var fee = Math.ceil(amount * 0.05);
        }
    });

    // ===================================
    // 9. 匿名チェックボックスの処理
    // ===================================

    $('#is_anonymous').on('change', function() {
        var $nameInput = $('#supporter_name');
        if ($(this).is(':checked')) {
            $nameInput.val('').prop('disabled', true);
        } else {
            $nameInput.prop('disabled', false);
        }
    });

    // ===================================
    // 10. トップへ戻るボタン（オプション）
    // ===================================

    // トップへ戻るボタンの表示/非表示
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });

    // トップへスクロール
    $(document).on('click', '.back-to-top', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 600);
    });

    // ===================================
    // 11. リンクコピー機能
    // ===================================

    function initCopyLink() {
        var $copyButton = $('#copy-project-link');

        if ($copyButton.length === 0) {
            return;
        }

        $copyButton.on('click', function(e) {
            e.preventDefault();

            // 現在のページURLを取得
            var pageUrl = window.location.href;

            // クリップボードにコピー
            if (navigator.clipboard && navigator.clipboard.writeText) {
                // モダンブラウザ
                navigator.clipboard.writeText(pageUrl).then(function() {
                    showCopySuccess($copyButton);
                }).catch(function() {
                    fallbackCopyToClipboard(pageUrl, $copyButton);
                });
            } else {
                // レガシーブラウザ用のフォールバック
                fallbackCopyToClipboard(pageUrl, $copyButton);
            }
        });
    }

    // コピー成功時の表示
    function showCopySuccess($button) {
        var originalText = $button.find('.copy-text').text();

        $button.addClass('copied');
        $button.find('.copy-text').text('コピーしました！');

        setTimeout(function() {
            $button.removeClass('copied');
            $button.find('.copy-text').text(originalText);
        }, 2000);
    }

    // フォールバックのコピー機能
    function fallbackCopyToClipboard(text, $button) {
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(text).select();

        try {
            document.execCommand('copy');
            showCopySuccess($button);
        } catch (err) {
            alert('リンクのコピーに失敗しました。手動でコピーしてください: ' + text);
        }

        $temp.remove();
    }

})(jQuery);
