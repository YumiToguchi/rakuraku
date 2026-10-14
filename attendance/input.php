<?php
// データベース接続
require_once 'db.php';

// 対象日付の決定
$target_date = isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])
    ? $_GET['date']
    : date('Y-m-d');

$is_today = ($target_date === date('Y-m-d'));

try {
    // 生徒一覧の取得（学科名も結合して取得)
    $stmtStudents = $pdo->query("
        SELECT
            s.student_id,
            s.student_name,
            s.grade,
            s.student_group,
            d.department_name
        FROM students s
        JOIN department d ON s.department_id = d.department_id
        WHERE s.status = 1
        ORDER BY s.student_id ASC
    ");
    $students = $stmtStudents->fetchAll();

    // 科目一覧の取得
    $stmtSubjects = $pdo->query("
        SELECT subject_id, subject_name
        FROM subjects
        ORDER BY subject_id ASC
    ");
    $subjects = $stmtSubjects->fetchAll();

    // 教員一覧の取得
    $stmtTeachers = $pdo->query("
        SELECT teacher_id, teacher_name
        FROM teachers
        ORDER BY teacher_id ASC
    ");
    $teachers = $stmtTeachers->fetchAll();

} catch (PDOException $e) {
    die('データ取得エラー: ' .htmlspecialchars($e->getMessage(), ENT_QUOTES,'UTF-8')) ;
}

// 最大時間数（通常7限）
$max_period = 7;
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>らくらく出席簿 - 出欠入力</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <!-- アプリヘッダー＆ロゴマーク -->
         <header class="app-header">
            <div class="logo-wrapper">
                <!-- SVGロゴ：チャコールグレーのバインダー + 淡いピンクのチェックマーク -->
                <svg width="44" height="44" viewBox="0 0 48 48" fill="none" xmls="http://www.w3.org/2000/svg">
                    <!-- バインダークリップ -->
                    <rect x="18" y="4" width="12" height="6" rx="2" fill="#343a40"/>
                    <!-- バインダー本体（チャコールグレー） -->
                    <rect x="8" y="8" width="32" height="36" rx="5" fill="#343a40"/>
                    <!-- 中の用紙（白） -->
                    <rect x="12" y="14" width="24" height="26" rx="2" fill="#ffffff"/>
                    <!-- ノートの罫線 -->
                    <line x1="16" y1="20" x2="24" y2="20" stroke="#dee2e6" stroke-width="2" stroke-linecap="round"/>
                    <line x1="16" y1="26" x2="28" y2="26" stroke="#dee2e6" stroke-width="2" stroke-linecap="round"/>
                    <!-- スムースチェックマーク（淡いピンク #F7CAD0、バインダーから心地よく飛び出す躍動感） -->
                    <path d="M16 29L24 37L41 15" stroke="#F7CAD0" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <!-- ロゴタイプ -->
                 <div>
                    <div class="logo-text-title"><span>らくらく</span>出席簿</div>
                    <div class="logo-text-subtitle">Smart Attendance System</div>
                 </div>
            </div>
        </header>

        <!-- 日付選択コントローラー -->
        <div class="date-controller">
            <span class="data-display-label">対象日</span>
            <div class="datepicker-box">
                <!-- カレンダーSVGアイコン -->
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <!-- 日付入力欄 -->
                <input type="date" class="date-input" value="<?= htmlspecialchars($target_date) ?>" onchange="="changeDate(this.value)">
            </div>
        </div>

         <p class="header-lead">入力または変更を行った時限の出欠のみが、右下の「確定して保存」で送信されます。</p>

        <!-- 出欠入力テーブル -->
        <table>
            <thead>
                <tr>
                    <th>学籍番号</th>
                    <th>氏名</th>
                    <th>班</th>
                    <!-- 1～7限のヘッダーをループで生成 -->
                    <?php for ($p = 1; $p <= $max_period; $p++): ?>
                        <th class="period-header">
                            <div><?= $p ?>限</div>

                            <!-- 科目選択プルダウン -->
                             <select id="subject_<?= $p ?>" name="subject_<?= $p ?>">
                                <option value="">- 科目選択 -</option>
                                <?php foreach ($subjects as $sb): ?>
                                    <option value="<?= $sb['subject_id'] ?>">
                                        <?= htmlspecialchars($sb['subject_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                             </select>

                             <!-- 教員選択プルダウン -->
                              <select id="teacher_<?= $p ?>" name="teacher_<?= $p ?>">
                                <option value="">- 教員選択 -</option>
                                <?php foreach($teachers as $tc): ?>
                                    <option value="<?= $tc['teacher_id'] ?>">
                                        <?= htmlspecialchars($tc['teacher_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                              </select>
                        </th>
                    <?php endfor ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $st): ?>
                    <tr>
                        <td><?= htmlspecialchars($st['student_id']) ?></td>
                        <td class="name-col"><?= htmlspecialchars($st['student_name']) ?></td>
                        <td><?= !empty($st['student_group']) ?  htmlspecialchars($st['student_group']) : "-" ?></td>

                        <!-- 生徒ごとに各時限のマス目を作成 -->
                         <?php for ($p = 1; $p <= $max_period; $p++): ?>
                            <!-- セル自体にも目印IDを付与 -->
                            <td id="cell_<?= $st['student_id'] ?>_<?= $p ?>">
                                <div class="radio-group">
                                    <!-- name属性を「att_学籍番号_時限」にすることでマス目ごとに独立した1つの選択肢グループになる -->
                                    <!-- クリックされたらmarkAsChanged()を呼び出す -->
                                     <label>
                                        <input type="radio" name="att_<?= $st['student_id'] ?>_<?= $p ?>" value="1" onchange="markAsChanged(<?= $st['student_id'] ?>, <?= $p ?>)">出
                                     </label>
                                     <label>
                                        <input type="radio" name="att_<?= $st['student_id'] ?>_<?= $p ?>" value="2" onchange="markAsChanged(<?= $st['student_id'] ?>, <?= $p ?>)">欠
                                     </label> 
                                     <label>
                                        <input type="radio" name="att_<?= $st['student_id'] ?>_<?= $p ?>" value="3" onchange="markAsChanged(<?= $st['student_id'] ?>, <?= $p ?>)">遅
                                     </label>
                                     <label>
                                        <input type="radio" name="att_<?= $st['student_id'] ?>_<?= $p ?>" value="4" onchange="markAsChanged(<?= $st['student_id'] ?>, <?= $p ?>)">公
                                     </label>
                                </div>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- 画面右下に固定配置された確定ボタン -->
        <div class="floating-footer">
            <button type="button" class="submit-btn" onclick="submitAttendance()">確定して保存</button>
        </div>

        <!-- PHPデータをJavaScriptで使えるように定義 -->
        <script>
            const students = <?= json_encode($students) ?>;
            const max_period = <?= $max_period ?>;
        </script>

        <!-- 分割したJSファイルの読込 -->
         <script src="js/attendance.js"></script>

    </body>
</html>