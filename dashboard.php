<?php
include 'header.php';
echo "<h2>Добро пожаловать, {$_SESSION['full_name']}!</h2>";

if ($_SESSION['role_id'] == 1) {
    echo "<p>Вы — администратор. Полный доступ.</p>";
} else {
    echo "<p>Вы — наблюдатель. Только просмотр и поиск.</p>";
}
include 'footer.php';
