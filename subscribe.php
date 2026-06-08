<?php require_once 'config/db.php';
if(!isLoggedIn()) redirect('login.php');
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, animal_species, breed) VALUES (?, ?, ?)");
    $stmt->execute([getCurrentUserId(), $_POST['species'], $_POST['breed']]);
    echo "Подписка оформлена!";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Подписка</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
<?php include 'nav.inc.php'; ?>
    <div class="container">
        <h1>Подпишитесь на новости о новых помётах</h1>
        <form method="post">
            <label>Вид</label>
            <input name="species">
            <label>Порода</label>
            <input name="breed">
            <button>Подписаться</button>
        </form>
    </div>
<?php include 'footer.inc.php'; ?>
</body>
</html>