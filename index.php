<?php
require_once(''.__DIR__.'/config.php');

$test = new TestController();
$error = '';

if (isset($_POST['start'])) {
    $result = $test->setUser();

    if ($result['success'] == 1) {
        header('Location: questions.php');
    } else {
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalabe=0">
<meta charset="utf-8" >
<title>Tests</title>
    <script src="js/jquery-3.1.1.min.js"></script>
    <link href="css/custom.css" rel="stylesheet" />
</head>
<body>
<h2 class="title">Testa uzdevums</h2>
<p class="error"><?php echo $error; ?></p>
<div class="input_block">
    <form method="post">
        <input type="text" name="name" placeholder="Ievadiet savu vārdu">
        <select name="test">
            <option value="">
                Izvēlieties testu
            </option>
            <?php
            $allTests = TestController::getTests();

            foreach ($allTests as $test) {
                echo "<option value='" .$test['id']."'>" . $test['name'] . "</option>";
            }
            ?>
        </select>
        <input type="submit" name="start" value="SĀKT">
    </form>
</div>
</body>
</html>