<?php
// データベース接続
require_once 'db.php';

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>らくらく出席簿 - 出欠入力</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    
</head>

<body>
    <!-- アプリヘッダー＆ロゴマーク -->
    <header class="app-header">
        <div class="logo-wrapper">
            <!-- SVGロゴ：チャコールグレーのバインダー + 淡いピンクのチェックマーク -->
            <svg width="44" height="44" viewBox="0 0 48 48" fill="none" xmls="http://www.w3.org/2000/svg">
                <!-- バインダークリップ -->
                <rect x="18" y="4" width="12" height="6" rx="2" fill="#343a40" />
                <!-- バインダー本体（チャコールグレー） -->
                <rect x="8" y="8" width="32" height="36" rx="5" fill="#343a40" />
                <!-- 中の用紙（白） -->
                <rect x="12" y="14" width="24" height="26" rx="2" fill="#ffffff" />
                <!-- ノートの罫線 -->
                <line x1="16" y1="20" x2="24" y2="20" stroke="#dee2e6" stroke-width="2" stroke-linecap="round" />
                <line x1="16" y1="26" x2="28" y2="26" stroke="#dee2e6" stroke-width="2" stroke-linecap="round" />
                <!-- スムースチェックマーク（淡いピンク #F7CAD0、バインダーから心地よく飛び出す躍動感） -->
                <path d="M16 29L24 37L41 15" stroke="#F7CAD0" stroke-width="5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>

            <!-- ロゴタイプ -->
            <div>
                <div class="logo-text-title"><span>らくらく</span>出席簿</div>
                <div class="logo-text-subtitle">Smart Attendance System</div>
            </div>
        </div>
    </header>
<div id="wrapper">
    <div id="today">
        <span>
            <?= date('Y/m/d') ?>
            （<?= ['日','月','火','水','木','金','土'][date('w')] ?>）
        </span>

        <svg xmlns="http://www.w3.org/2000/svg"
            width="24" height="24"
            viewBox="0 0 24 24"
            fill="none" stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <rect x="3" y="5" width="18" height="16" rx="2"/>
            <line x1="16" y1="3" x2="16" y2="7"/>
            <line x1="8" y1="3" x2="8" y2="7"/>
            <line x1="3" y1="11" x2="21" y2="11"/>
        </svg>
    </div>
    <main>
        <div id="title-flex">
            <h1>ログイン</h1>
            
        </div>
        <form action="input.php" method="post">
            <p>ログインID<br><input type="text" name="login"></p><br>
            <p>パスワード<br><input type="password" name="password"></p><br>
            <input type="submit" value="ログイン" class="submit-btn">
        </form>
    </main>
    </div>
</body>

</html>