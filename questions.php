<?php
require_once(''.__DIR__.'/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
} else {
    $test = new TestController();

    if (isset($_SESSION['question_id'])) {
        $question = $test->getQuestionValue($_SESSION['question_id']);
    } else {
        $question = $test->getQuestion($_SESSION['test_id'], $_SESSION['question_no'], $_SESSION['user_id']);
    }

    $answers = $test->getAnswers($_SESSION['question_id']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalabe=0">
    <meta charset="utf-8" >
    <title>Jautājumi</title>
    <link href="css/custom.css" rel="stylesheet" />
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/process.js"></script>
</head>
<body>
<h2 class="title"><?php echo $_SESSION['question_no'] . '. '. $question; ?></h2>
<div class="container">
    <div id="questions-block">
        <input type="hidden" id="progress" value="0">
        <?php
        $output = '';
        $i = 1;

        foreach ($answers as $answer) {
            $answerId = $answer['id'];
            $answerValue = $answer['value'];

            if (($i % 2) != 0) $output .= '<div class="row">';

            $output .= '<div class="col" id="a'. $i .'" onclick="setAnswer('. $answerId .', ' . $i . ')" >
                        <a>' . $answerValue . '</a>
                    </div>';
            if (($i % 2) == 0) $output .= '</div>';
            $i++;
        }

        echo $output;
        ?>
    </div>
</div>
<div class="bottom-container">
    <div class="progress-bar">
        <div id="current-progress"></div>
    </div>
    <div class="bottom">
        <input type="hidden" id="AnswerValue" value="">
        <a id="notify">Izvēlies vienu atbilžu variantu</a>
        <a id="next">Tālāk</a>
    </div>
</div>
</body>
</html>