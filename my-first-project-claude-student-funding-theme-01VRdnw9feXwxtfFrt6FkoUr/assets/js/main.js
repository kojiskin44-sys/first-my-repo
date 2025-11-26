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

        // 会員登録フォーム
        initRegisterForm();

        // ログインフォーム
        initLoginForm();

        // マイページタブ
        initMyPageTabs();

        // お気に入りボタン
        initFavoriteButtons();

        // アーカイブページのフィルター
        initArchiveFilters();

        // プロジェクト作成フォーム
        initCreateProjectForm();
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
        var $copyBtn = $('#copy-project-link');

        if ($copyBtn.length === 0) {
            return;
        }

        $copyBtn.on('click', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var $btn = $(this);

            // クリップボードにコピー
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showCopySuccess($btn);
                }).catch(function() {
                    fallbackCopyToClipboard(url, $btn);
                });
            } else {
                fallbackCopyToClipboard(url, $btn);
            }
        });
    }

    // コピー成功時の表示
    function showCopySuccess($btn) {
        var originalText = $btn.find('.copy-text').text();
        $btn.addClass('copied');
        $btn.find('.copy-text').text('コピーしました！');

        setTimeout(function() {
            $btn.removeClass('copied');
            $btn.find('.copy-text').text(originalText);
        }, 2000);
    }

    // フォールバック用のコピー機能
    function fallbackCopyToClipboard(text, $btn) {
        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.top = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess($btn);
            }
        } catch (err) {
            console.error('コピーに失敗しました:', err);
        }

        document.body.removeChild(textArea);
    }

    // ===================================
    // 12. 会員登録フォーム
    // ===================================

    function initRegisterForm() {
        var $form = $('#register-form');
        if ($form.length === 0) return;

        var $errorDiv = $('#register-error');
        var $submitButton = $form.find('button[type="submit"]');

        $form.on('submit', function(e) {
            e.preventDefault();

            // エラーメッセージをクリア
            $errorDiv.hide().html('');

            // パスワード一致チェック
            var password = $form.find('#password').val();
            var passwordConfirm = $form.find('#password_confirm').val();

            if (password !== passwordConfirm) {
                $errorDiv.html('パスワードが一致しません。').show();
                return false;
            }

            // 送信ボタンを無効化
            var originalText = $submitButton.text();
            $submitButton.prop('disabled', true).text('登録中...');

            // フォームデータを取得
            var formData = {
                action: 'register_user',
                nonce: $form.find('[name="register_nonce"]').val(),
                username: $form.find('[name="username"]').val(),
                email: $form.find('[name="email"]').val(),
                password: password,
                password_confirm: passwordConfirm,
                display_name: $form.find('[name="display_name"]').val()
            };

            // AJAX送信
            $.ajax({
                url: studentFunding.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect;
                    } else {
                        $errorDiv.html(response.data.message).show();
                        $submitButton.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    $errorDiv.html('通信エラーが発生しました。').show();
                    $submitButton.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    // ===================================
    // 13. ログインフォーム
    // ===================================

    function initLoginForm() {
        var $form = $('#login-form');
        if ($form.length === 0) return;

        var $errorDiv = $('#login-error');
        var $submitButton = $form.find('button[type="submit"]');

        $form.on('submit', function(e) {
            e.preventDefault();

            // エラーメッセージをクリア
            $errorDiv.hide().html('');

            // 送信ボタンを無効化
            var originalText = $submitButton.text();
            $submitButton.prop('disabled', true).text('ログイン中...');

            // フォームデータを取得
            var formData = {
                action: 'login_user',
                nonce: $form.find('[name="login_nonce"]').val(),
                username: $form.find('[name="username"]').val(),
                password: $form.find('[name="password"]').val(),
                remember: $form.find('[name="remember"]').is(':checked') ? 1 : 0
            };

            // AJAX送信
            $.ajax({
                url: studentFunding.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect;
                    } else {
                        $errorDiv.html(response.data.message).show();
                        $submitButton.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    $errorDiv.html('通信エラーが発生しました。').show();
                    $submitButton.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    // ===================================
    // 14. マイページのタブ切り替え
    // ===================================

    function initMyPageTabs() {
        $('.tab-btn').on('click', function() {
            var tabId = $(this).data('tab');

            // タブボタンのアクティブ状態を切り替え
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');

            // タブコンテンツの表示を切り替え
            $('.tab-content').removeClass('active');
            $('#' + tabId + '-tab').addClass('active');
        });
    }

    // ===================================
    // 15. お気に入りボタン
    // ===================================

    function initFavoriteButtons() {
        $(document).on('click', '.favorite-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var projectId = $btn.data('project-id');

            $.ajax({
                url: studentFunding.ajaxurl,
                type: 'POST',
                data: {
                    action: 'toggle_favorite',
                    nonce: studentFunding.nonce,
                    project_id: projectId
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.is_favorite) {
                            $btn.addClass('favorited').html('★ お気に入り登録済み');
                        } else {
                            $btn.removeClass('favorited').html('☆ お気に入りに追加');
                        }
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('エラーが発生しました。');
                }
            });
        });
    }

    // ===================================
    // 16. アーカイブページのフィルター
    // ===================================

    function initArchiveFilters() {
        var $statusFilter = $('#status-filter');
        var $sortFilter = $('#sort-filter');

        // ステータスフィルターの変更時
        $statusFilter.on('change', function() {
            updateArchiveUrl('status', $(this).val());
        });

        // ソートフィルターの変更時
        $sortFilter.on('change', function() {
            updateArchiveUrl('orderby', $(this).val());
        });

        // URLを更新してページをリロード
        function updateArchiveUrl(param, value) {
            var url = new URL(window.location.href);

            if (value) {
                url.searchParams.set(param, value);
            } else {
                url.searchParams.delete(param);
            }

            // ページ番号をリセット
            url.searchParams.delete('paged');

            window.location.href = url.toString();
        }
    }

    // ===================================
    // 17. プロジェクト作成フォーム
    // ===================================

    function initCreateProjectForm() {
        var $form = $('#create-project-form');
        if ($form.length === 0) return;

        var $errorDiv = $('#form-error');
        var $submitButton = $form.find('button[type="submit"]');

        // 画像プレビュー
        $('#project_image').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').html('<img src="' + e.target.result + '" style="max-width: 300px; height: auto; border-radius: 8px;">');
                };
                reader.readAsDataURL(file);
            }
        });

        // フォーム送信
        $form.on('submit', function(e) {
            e.preventDefault();

            // エラーメッセージをクリア
            $errorDiv.hide().html('');

            // 送信ボタンを無効化
            var originalText = $submitButton.text();
            $submitButton.prop('disabled', true).text('作成中...');

            // FormDataを使用（画像アップロードのため）
            var formData = new FormData(this);
            formData.append('action', 'create_project');
            formData.append('nonce', $form.find('[name="create_project_nonce"]').val());

            // AJAX送信
            $.ajax({
                url: studentFunding.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        window.location.href = response.data.redirect;
                    } else {
                        $errorDiv.html(response.data.message).show();
                        $submitButton.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    $errorDiv.html('通信エラーが発生しました。').show();
                    $submitButton.prop('disabled', false).text(originalText);
                }
            });
        });
    }

})(jQuery);
