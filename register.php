<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 2;       // по умолчанию viewer
    $dept = intval($_POST['dept_id']);

    $stmt = $pdo->prepare("INSERT INTO users (full_name,email,password,role_id,dept_id) VALUES (?,?,?,?,?)");
    $stmt->execute([$name, $email, $pass, $role, $dept]);
    header('Location: index.php');
}

$departments = $pdo->query("SELECT * FROM departments")->fetchAll();
?>
<?php include 'header.php'; ?>
<h2>Регистрация</h2>
<form method="post">
  <label>Полное имя <input type="text" name="full_name" required></label>
  <label>Email <input type="email" name="email" required></label>
  <label>Пароль <input type="password" name="password" required></label>
  <label>Отдел
    <select name="dept_id" required>
      <?php foreach($departments as $d): ?>
        <option value="<?= $d['dept_id'] ?>"><?= $d['dept_name'] ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button type="submit">Зарегистрироваться</button>
</form>
<?php include 'footer.php'; ?>
