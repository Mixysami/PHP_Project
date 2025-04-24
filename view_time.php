<?php
include 'header.php';
require 'db.php';

echo "<h2>Отчёты</h2>";
// Параметры поиска
$user_filter = $_GET['user_id'] ?? '';
$month = $_GET['month'] ?? date('n');
$year  = $_GET['year'] ?? date('Y');

// Форма фильтра
$all_users = $pdo->query("SELECT user_id, full_name FROM users")->fetchAll();
?>
<form method="get" class="grid-2">
  <label>Сотрудник
    <select name="user_id">
      <option value="">Все</option>
      <?php foreach($all_users as $u): ?>
        <option value="<?= $u['user_id'] ?>"
          <?= $u['user_id']==$user_filter?'selected':'' ?>>
          <?= $u['full_name'] ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Месяц <input type="number" name="month" value="<?= $month ?>" min="1" max="12"></label>
  <label>Год <input type="number" name="year" value="<?= $year ?>" min="2000" max="<?= date('Y') ?>"></label>
  <button type="submit">Показать</button>
</form>

<?php
// Выборка записей и сводки
$params = [];
$sql = "
 SELECT tr.*, u.full_name, ts.late_count
 FROM time_records tr
 JOIN users u ON tr.user_id = u.user_id
 JOIN tardiness_summary ts ON ts.user_id = tr.user_id
  AND ts.year = ? AND ts.month = ?
";
$params[] = $year; $params[] = $month;

if ($user_filter) {
  $sql .= " WHERE tr.user_id = ?";
  $params[] = $user_filter;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

if ($rows):
?>
<table>
  <thead>
    <tr>
      <th>Сотрудник</th><th>Дата</th><th>Приход</th><th>Опоздал</th><th>Опозданий в мес.</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($rows as $r): ?>
      <tr class="<?= $r['is_late'] ? 'late':'' ?>">
        <td><?= $r['full_name'] ?></td>
        <td><?= $r['work_date'] ?></td>
        <td><?= $r['check_in'] ?></td>
        <td><?= $r['is_late'] ? 'Да':'Нет' ?></td>
        <td><?= $r['late_count'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
  <p>Нет данных за выбранный период.</p>
<?php endif;

include 'footer.php';
