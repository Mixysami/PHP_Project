<?php
include 'header.php';
if ($_SESSION['role_id'] != 1) {
    echo "<p>Доступ запрещён.</p>";
    include 'footer.php';
    exit;
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = intval($_POST['user_id']);
    $date = $_POST['work_date'];
    $in   = $_POST['check_in'];
    $late = ($in > '09:00:00') ? 1 : 0;

    // вставка записи
    $stmt = $pdo->prepare("
      INSERT INTO time_records (user_id, work_date, check_in, is_late)
      VALUES (?,?,?,?)
      ON DUPLICATE KEY UPDATE check_in = VALUES(check_in), is_late = VALUES(is_late)
    ");
    $stmt->execute([$uid, $date, $in, $late]);

    // обновление сводки
    $yr = date('Y', strtotime($date));
    $mo = date('n', strtotime($date));
    $pdo->prepare("
      INSERT INTO tardiness_summary (user_id, year, month, late_count)
      VALUES (?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE late_count = late_count + ?
    ")->execute([$uid, $yr, $mo, $late, $late]);

    echo "<p>Запись сохранена.</p>";
}

$users = $pdo->query("SELECT user_id, full_name FROM users")->fetchAll();
?>
<h2>Добавить/обновить запись времени</h2>
<form method="post">
  <label>Сотрудник
    <select name="user_id" required>
      <?php foreach($users as $u): ?>
        <option value="<?= $u['user_id'] ?>"><?= $u['full_name'] ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Дата <input type="date" name="work_date" required></label>
  <label>Время прихода <input type="time" name="check_in" required></label>
  <button type="submit">Сохранить</button>
</form>
<?php include 'footer.php'; ?>
