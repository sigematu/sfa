<?php

/**
 * @var \App\View\AppView $this
 * @var \CakeLte\View\Helper\CakeLteHelper $this->CakeLte
 */

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') . ' | ' . strip_tags($this->CakeLte->getConfig('app-name')) ?></title>

    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <?= $this->Html->css('CakeLte./AdminLTE/plugins/fontawesome-free/css/all.min.css') ?>
    <!-- Theme style -->
    <?= $this->Html->css('CakeLte./AdminLTE/dist/css/adminlte.min.css') ?>
    <?= $this->Html->css('CakeLte.style') ?>
    <?= $this->Html->css('alter') ?>
    <?= $this->element('layout/css') ?>
    <?= $this->fetch('css') ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet" />
</head>

<body class="hold-transition <?= $this->CakeLte->getBodyClass() ?>">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand <?= $this->CakeLte->getHeaderClass() ?>">
            <?= $this->element('header/main') ?>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar <?= $this->CakeLte->getSidebarClass() ?>">
            <!-- Brand Logo -->
            <a href="<?= $this->Url->build('/') ?>" class="brand-link">
                <?= $this->Html->image($this->CakeLte->getConfig('app-logo'), ['alt' => $this->CakeLte->getConfig('app-name') . ' logo', 'class' => 'brand-image']) ?>
                <span class="brand-text font-weight-light"><?= $this->CakeLte->getConfig('app-name') ?></span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <?= $this->element('sidebar/main') ?>
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <?= $this->element('content/header') ?>
                </div><!-- /.container-fluid -->
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?= $this->Flash->render() ?>
                    <?= $this->fetch('content') ?>
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <?= $this->element('aside/main') ?>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <?= $this->element('footer/main') ?>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <?= $this->Html->script('CakeLte./AdminLTE/plugins/jquery/jquery.min.js') ?>
    <!-- Bootstrap 4 -->
    <?= $this->Html->script('CakeLte./AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>
    <!-- AdminLTE App -->
    <?= $this->Html->script('CakeLte./AdminLTE/dist/js/adminlte.min.js') ?>

    <?= $this->element('layout/script') ?>
    <?= $this->fetch('script') ?>

    <?= $this->Html->script('js.cookie') ?>
    <script>
        // Cookieを使用して展開状態を保存する関数
        $(document).on('shown.bs.collapse', '.collapse', function(e) {
            // 展開された要素のIDを取得
            var id = $(this).attr('id');

            if (id) {
                // Cookieから展開されたIDを取得
                var cookieValue = Cookies.get('searchOpen');                   
                var openItems = cookieValue ? JSON.parse(cookieValue) : [];
                
                // Cookieに展開されたIDが存在しない場合、追加
                if (!openItems.includes(id)) {
                    openItems.push(id);
                }

                // Cookieに展開されたIDを保存
                Cookies.set('searchOpen', JSON.stringify(openItems), { expires: 7 });
            }
        });

        // Cookieを使用して閉じた状態を保存する関数
        $(document).on('hidden.bs.collapse', '.collapse', function(e) {
            // 閉じられた要素のIDを取得
            var id = $(this).attr('id');

            if (id) {
                // Cookieから閉じられたIDを削除
                var cookieValue = Cookies.get('searchOpen');
                var openItems = cookieValue ? JSON.parse(cookieValue) : [];

                // Cookieから閉じられたIDを削除
                openItems = openItems.filter(item => item !== id);

                // Cookieに更新されたIDを保存
                Cookies.set('searchOpen', JSON.stringify(openItems), { expires: 7 });
            }
        });

        // ページ読み込み時に状態を復元
        // Cookieから展開されたIDを取得
        var cookieValue = Cookies.get('searchOpen');
        var openItems = cookieValue ? JSON.parse(cookieValue) : [];

        // Cookieに保存されたIDを元に要素を展開
        openItems.forEach(function(id) {
            $('#' + id).addClass('show');
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/ja.min.js"></script>
    <?= $this->fetch('lateScript') ?>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                language: 'ja',
                // placeholder: 'Select an option',
                placeholder: '選択してください',
                allowClear: true,
                // theme: 'classic'
                theme: 'bootstrap4'
            })
        });
    </script>

    <script>
        $(document).ready(function() {
            function activateWageMode(mode) {
                if (mode === 'hourly') {
                    $('#mode-hourly').addClass('btn-primary active').removeClass('btn-outline-secondary');
                    $('#mode-normal').removeClass('btn-primary active').addClass('btn-outline-secondary');
                    $('.sales-normal-field, .cost-normal-field').hide();
                    $('.sales-hourly-field, .cost-hourly-field').show();
                } else {
                    $('#mode-normal').addClass('btn-primary active').removeClass('btn-outline-secondary');
                    $('#mode-hourly').removeClass('btn-primary active').addClass('btn-outline-secondary');
                    $('.sales-normal-field, .cost-normal-field').show();
                    $('.sales-hourly-field, .cost-hourly-field').hide();
                }
            }

            $('#mode-normal').on('click', function() {
                $('#sales-hourly-rate, #cost-hourly-rate').val('');
                $('#period').val(<?= PERIOD_NORMAL ?>);
                activateWageMode('normal');
            });

            $('#mode-hourly').on('click', function() {
                $('#sales-lower, #sales-lower-price, #sales-upper, #sales-upper-price').val('');
                $('#cost-lower, #cost-lower-price, #cost-upper, #cost-upper-price').val('');
                $('#period').val(<?= PERIOD_HOURLY ?>);
                activateWageMode('hourly');
            });

            if (typeof window.initialWageMode !== 'undefined') {
                activateWageMode(window.initialWageMode);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            const copyMessage = 'コピーしました。';

            // 共通のクラスを持つボタンにイベントを設定
            $('.copy-button').click(function() {
                const targetTextarea = $(this).data('target-textarea'); // 対応するテキストエリアのID
                const targetMessage = $(this).data('target-message');   // 対応するメッセージ要素のID

                $(`#${targetTextarea}`).select(); // テキストエリアを選択
                document.execCommand('copy');     // クリップボードにコピー
                $(`#${targetMessage}`).text(copyMessage).show().fadeOut(1000); // メッセージを表示
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // 売上の設定関数
            function setSales(lower, upper, divisorLower, divisorUpper, roundFunc = Math.floor) {
                $('#sales-lower').val(lower);
                $('#sales-lower-price').val((roundFunc($('#sales').val().replace(/,/g , '') / divisorLower / 10) * 10).toLocaleString());
                $('#sales-upper').val(upper);
                $('#sales-upper-price').val((roundFunc($('#sales').val().replace(/,/g , '') / divisorUpper / 10) * 10).toLocaleString());
            }

            function setSales1yen(lower, upper, divisorLower, divisorUpper, roundFunc = Math.floor) {
                $('#sales-lower').val(lower);
                $('#sales-lower-price').val(roundFunc($('#sales').val().replace(/,/g , '') / divisorLower).toLocaleString());
                $('#sales-upper').val(upper);
                $('#sales-upper-price').val(roundFunc($('#sales').val().replace(/,/g , '') / divisorUpper).toLocaleString());
            }

            // 仕入の設定関数
            function setCost(lower, upper, divisorLower, divisorUpper) {
                $('#cost-lower').val(lower);
                $('#cost-lower-price').val((Math.floor($('#cost').val().replace(/,/g , '') / divisorLower / 10) * 10).toLocaleString());
                $('#cost-upper').val(upper);
                $('#cost-upper-price').val((Math.floor($('#cost').val().replace(/,/g , '') / divisorUpper / 10) * 10).toLocaleString());
            }

            // 各ボタンのクリックイベント
            $('.button-all').click(function() {
                setSales(140, 180, 140, 180);
                $('#cost').val(($('#sales').val().replace(/,/g , '') - 100000).toLocaleString());
                setCost(140, 180, 140, 180);
                $('#profit').val((100000).toLocaleString());
            });

            $('.button-client').click(function() {
                setSales(140, 180, 140, 180);
            });

            $('.button-client-middle').click(function() {
                setSales(140, 180, 160, 160);
            });

            $('.button-client-roundup1yen').click(function() {
                setSales1yen(140, 180, 140, 180, Math.ceil);
            });

            $('.button-client-lower_ceil').click(function() {
                $('#sales-lower').val(140);
                $('#sales-lower-price').val((Math.ceil($('#sales').val().replace(/,/g , '') / 140 / 10) * 10).toLocaleString());
                $('#sales-upper').val(180);
                $('#sales-upper-price').val((Math.floor($('#sales').val().replace(/,/g , '') / 180 / 10) * 10).toLocaleString());
            });

            $('.button-client-190').click(function() {
                setSales(140, 190, 140, 190);
            });

            $('.button-bp-180').click(function() {
                setCost(140, 180, 140, 180);
            });

            $('.button-bp-190').click(function() {
                setCost(140, 190, 140, 190);
            });

            $('.button-bp-200').click(function() {
                setCost(140, 200, 140, 200);
            });

            $('.button-bp-middle').click(function() {
                setCost(140, 180, 160, 160);
            });

            $('.button-profit').click(function() {
                $('#profit').val(($('#sales').val().replace(/,/g , '') - $('#cost').val().replace(/,/g , '')).toLocaleString());
            });

            $('.button-hourly').click(function() {
                var sales = parseInt($('#sales').val().replace(/,/g, '')) || 0;
                var cost  = parseInt($('#cost').val().replace(/,/g, '')) || 0;
                $('#sales-hourly-rate').val((Math.floor(sales / 160 / 10) * 10).toLocaleString());
                $('#cost-hourly-rate').val((Math.floor(cost / 160 / 10) * 10).toLocaleString());
            });

            $('.button-reset').click(function() {
                $('#sales, #sales-lower, #sales-lower-price, #sales-upper, #sales-upper-price, #sales-hourly-rate, #cost, #cost-lower, #cost-lower-price, #cost-upper, #cost-upper-price, #cost-hourly-rate, #profit').val('');
            });

            // フォームからフォーカスが外れた時に、数字を3桁区切りにする
            function threeDigit(id) {
                $(id).on('blur', function(){
                    // フォームが空でない場合
                    if($(id).val()) {
                        var inputVal = $(this).val().replace(/,/g , '');
                        $(this).val(Number(inputVal).toLocaleString());
                    }
                });
            }

            // edit時は、ページ表示時から数字を3桁区切りにする
            function threeDigitInit(id) {
                // フォームが空でない場合
                if($(id).val()) {
                    var inputVal = $(id).val().replace(/,/g , '');
                    $(id).val(Number(inputVal).toLocaleString());
                }
            }

            // 実行
            $('.threeDigit').each(function() {
                threeDigit('#' + $(this).attr('id'));
                threeDigitInit('#' + $(this).attr('id'));
            });
        });
    </script>

<style>
.sticky-thead-clone {
    position: fixed;
    top: 57px;
    z-index: 100;
    overflow: hidden;
    background: #fff;
    border-bottom: 2px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    pointer-events: none;
}
.sticky-thead-clone table {
    margin: 0;
    table-layout: fixed;
}

.tbl-scroll-btn {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1050;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 123, 255, 0.8);
    color: #fff;
    font-size: 22px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    transition: opacity 0.2s, background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}
.tbl-scroll-btn:hover  { background: rgba(0, 86, 179, 0.95); }
.tbl-scroll-btn.hidden { opacity: 0; pointer-events: none; }
#tbl-scroll-left  { left: 6px; }
#tbl-scroll-right { right: 6px; }
</style>

<button id="tbl-scroll-left"  class="tbl-scroll-btn hidden" title="左へスクロール">&#8249;</button>
<button id="tbl-scroll-right" class="tbl-scroll-btn hidden" title="右へスクロール">&#8250;</button>

<script>
(function () {
    var btnL = document.getElementById('tbl-scroll-left');
    var btnR = document.getElementById('tbl-scroll-right');
    var step = 320;
    var timer = null;
    var activeContainer = null;

    function getContainers() {
        return Array.from(document.querySelectorAll('.table-responsive'));
    }

    function pickContainer() {
        var containers = getContainers();
        if (!containers.length) return null;
        // ビューポート内で最も面積が大きいものを選択
        var best = null, bestArea = 0;
        containers.forEach(function (c) {
            var r = c.getBoundingClientRect();
            var visH = Math.max(0, Math.min(r.bottom, window.innerHeight) - Math.max(r.top, 0));
            var visW = Math.max(0, Math.min(r.right, window.innerWidth) - Math.max(r.left, 0));
            var area = visH * visW;
            if (area > bestArea) { bestArea = area; best = c; }
        });
        return best;
    }

    function updateButtons() {
        activeContainer = pickContainer();
        if (!activeContainer || activeContainer.scrollWidth <= activeContainer.clientWidth) {
            btnL.classList.add('hidden');
            btnR.classList.add('hidden');
            return;
        }
        var sl = activeContainer.scrollLeft;
        var maxSL = activeContainer.scrollWidth - activeContainer.clientWidth;
        btnL.classList.toggle('hidden', sl <= 0);
        btnR.classList.toggle('hidden', sl >= maxSL - 1);
    }

    function startScroll(delta) {
        activeContainer = pickContainer();
        if (!activeContainer) return;
        activeContainer.scrollBy({ left: delta, behavior: 'smooth' });
        timer = setInterval(function () {
            if (activeContainer) activeContainer.scrollLeft += delta;
        }, 80);
    }

    function stopScroll() { clearInterval(timer); timer = null; }

    btnL.addEventListener('mousedown',  function () { startScroll(-step); });
    btnR.addEventListener('mousedown',  function () { startScroll(step); });
    btnL.addEventListener('touchstart', function () { startScroll(-step); }, { passive: true });
    btnR.addEventListener('touchstart', function () { startScroll(step); },  { passive: true });
    ['mouseup', 'mouseleave', 'touchend'].forEach(function (ev) {
        btnL.addEventListener(ev, stopScroll);
        btnR.addEventListener(ev, stopScroll);
    });

    // 各 table-responsive のスクロールを監視
    function bindContainers() {
        getContainers().forEach(function (c) {
            c.addEventListener('scroll', updateButtons);
        });
    }

    window.addEventListener('scroll', updateButtons);
    window.addEventListener('resize', updateButtons);
    document.addEventListener('DOMContentLoaded', function () {
        bindContainers();
        updateButtons();
    });
    // DOMContentLoaded 後に呼ばれた場合も対応
    if (document.readyState !== 'loading') {
        bindContainers();
        updateButtons();
    }
})();
</script>

<script>
(function () {
    var NAVBAR_H = 57;

    function initStickyHeader(container) {
        var table = container.querySelector('table');
        if (!table) return;
        var thead = table.querySelector('thead');
        if (!thead) return;

        // クローン用ラッパー作成
        var clone = document.createElement('div');
        clone.className = 'sticky-thead-clone';
        clone.style.display = 'none';

        var cloneTable = document.createElement('table');
        cloneTable.className = table.className;
        cloneTable.appendChild(thead.cloneNode(true));
        clone.appendChild(cloneTable);
        document.body.appendChild(clone);

        function syncWidths() {
            var ths  = thead.querySelectorAll('th');
            var cths = cloneTable.querySelectorAll('th');
            var containerRect = container.getBoundingClientRect();
            clone.style.left  = containerRect.left + 'px';
            clone.style.width = containerRect.width + 'px';
            // 各 th の幅を実測して合わせる
            cloneTable.style.width = table.offsetWidth + 'px';
            ths.forEach(function (th, i) {
                if (cths[i]) cths[i].style.width = th.getBoundingClientRect().width + 'px';
            });
        }

        function syncScroll() {
            cloneTable.style.transform = 'translateX(-' + container.scrollLeft + 'px)';
        }

        function update() {
            var rect = thead.getBoundingClientRect();
            if (rect.bottom < NAVBAR_H) {
                // thead が navbar より上にスクロールされた
                var tableBottom = table.getBoundingClientRect().bottom;
                if (tableBottom > NAVBAR_H + 20) {
                    syncWidths();
                    syncScroll();
                    clone.style.display = 'block';
                } else {
                    clone.style.display = 'none';
                }
            } else {
                clone.style.display = 'none';
            }
        }

        container.addEventListener('scroll', function () {
            syncScroll();
        });

        window.addEventListener('scroll', update);
        window.addEventListener('resize', function () {
            update();
            if (clone.style.display !== 'none') syncWidths();
        });

        update();
    }

    function init() {
        document.querySelectorAll('.table-responsive').forEach(function (c) {
            initStickyHeader(c);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

</body>

</html>