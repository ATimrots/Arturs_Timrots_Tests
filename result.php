<?php
require_once(''.__DIR__.'/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
} else {
    $name = $_SESSION['user_name'];
    $test = new TestController();
    $result = $test->calculateResult($_SESSION['test_id'], $_SESSION['user_id']);

    Configuration::make()->destroySession();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalabe=0">
<meta charset="utf-8" >
<title>Rezultāts</title>
    <script src="js/jquery-3.1.1.min.js"></script>
    <link href="css/custom.css" rel="stylesheet" />
</head>
<body>
<h2 class="title">Paldies, <?php echo $name; ?>!</h2>
<p class="result-info">Tu esi atbildējis preizi uz <?php echo $result['correct']; ?> no <?php echo $result['total']; ?> jautājumiem</p>
<p class="result-info"><a href="index.php">pildīt jaunu testu</a></p>
</body>
</html>