<?php
// 1) Запуск или возобновление сессии
session_start();                       

// 2) Подключение к БД через PDO из includes/db.php
require 'db.php';            

// 3) Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3.1) Получаем и валидируем входные данные
    $name  = trim($_POST['full_name']);    // ФИО администратора
    $email = trim($_POST['email']);        // Email администратора
    $pass  = $_POST['password'];           // Пароль в открытом виде

    // 3.2) Хешируем пароль безопасным алгоритмом bcrypt
    $hash = password_hash($pass, PASSWORD_DEFAULT);  

    // 3.3) Готовим INSERT-запрос с role_id = 1 (admin)
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password, role_id, dept_id)
        VALUES (:name, :email, :password, 1, :dept)
    ");

    try {
        // 3.4) Выполняем запрос с привязкой параметров
        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => $hash,
            ':dept'     => 1             // Можно заменить на нужный dept_id
        ]);
        echo "Администратор {$email} успешно создан.";  
    } catch (PDOException $e) {
        // 3.5) Обработка ошибок подключения и вставки
        echo "Ошибка при создании администратора: " . $e->getMessage(); 
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Создание админа</title>
  <style>
    /* 1) Импорт шрифта */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

/* 2) Переменные для палитры и базовых размеров */
:root {
  --font-base: 'Roboto', sans-serif;
  --color-bg: #f0f4f8;
  --color-card-bg: #ffffff;
  --color-primary: #0052cc;
  --color-accent: #ff6f61;
  --color-text: #333333;
  --color-text-light: #666666;
  --color-border: #e1e8ed;
  --radius: 6px;
  --transition: 0.3s ease;
}

/* 3) Сброс и базовые стили */
* {
  margin: 0; padding: 0; box-sizing: border-box;
}
body {
  font-family: var(--font-base);
  background-color: var(--color-bg);
  color: var(--color-text);
  line-height: 1.6;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* 4) Контейнер */
.container {
  width: 90%;
  max-width: 1200px;
  margin: 40px auto;
}

/* 5) Шапка */
header {
  background: var(--color-primary);
  color: #fff;
  padding: 20px 0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}
header .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
header h1 {
  font-weight: 500;
  font-size: 1.8rem;
}
nav a {
  color: #fff;
  margin-left: 20px;
  text-decoration: none;
  font-weight: 500;
  transition: color var(--transition);
}
nav a:hover {
  color: var(--color-accent);
}

/* 6) Футер */
footer {
  background: var(--color-card-bg);
  text-align: center;
  padding: 15px 0;
  margin-top: auto;
  border-top: 1px solid var(--color-border);
}
footer p {
  color: var(--color-text-light);
  font-size: 0.9rem;
}

/* 7) Карточки (для форм и контента) */
.card {
  background: var(--color-card-bg);
  padding: 30px;
  border-radius: var(--radius);
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  margin-bottom: 30px;
}

/* 8) Заголовки внутри */
h2 {
  margin-bottom: 20px;
  font-weight: 500;
  color: var(--color-primary);
}

/* 9) Кнопки */
button, .btn {
  display: inline-block;
  background: var(--color-primary);
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: var(--radius);
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background var(--transition), transform var(--transition);
}
button:hover, .btn:hover {
  background: var(--color-accent);
  transform: translateY(-2px);
}
button:active, .btn:active {
  transform: translateY(0);
}

/* 10) Формы */
form label {
  display: block;
  margin-bottom: 10px;
  font-weight: 400;
  color: var(--color-text-light);
}
form input,
form select {
  width: 100%;
  padding: 12px;
  margin-top: 5px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  font-size: 1rem;
  transition: border-color var(--transition), box-shadow var(--transition);
}
form input:focus,
form select:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(0,82,204,0.2);
}

/* 11) Таблицы */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
table th,
table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid var(--color-border);
}
table th {
  background: var(--color-card-bg);
  font-weight: 500;
}
table tbody tr:nth-child(even) {
  background: #f9fbfc;
}
table tbody tr:hover {
  background: rgba(0,82,204,0.05);
}
tr.late {
  background: rgba(255,111,97,0.2) !important;
}

/* 12) Сетка для форм и дашборда */
.grid-2 {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}
@media (min-width: 768px) {
  .grid-2 {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* 13) Мелкие утилиты */
.text-center { text-align: center; }
.mt-20 { margin-top: 20px; }
.mb-20 { margin-bottom: 20px; }

  </style>
</head>
<body>
  <h2>Создать аккаунт администратора</h2>
  <form method="post">
    <label>Полное имя:
      <input type="text" name="full_name" required>
    </label><br><br>
    <label>Email:
      <input type="email" name="email" required>
    </label><br><br>
    <label>Пароль:
      <input type="password" name="password" required>
    </label><br><br>
    <button type="submit">Создать администратора</button>
  </form>
</body>
</html>
