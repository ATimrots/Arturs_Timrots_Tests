<?php
require_once('../config.php');

$answerId = $_POST['answer'];
$result = array();

$test = new TestController();
$total = $test->getTestQCount($_SESSION['test_id']);

if ($test->saveAnswer($answerId)) {
    if ($_SESSION['question_no'] < $total) {
        $progress = $_SESSION['question_no'] * 100 / $total;
         //get a new question
        $newQuestion = $test->getQuestion($_SESSION['test_id'], $_SESSION['question_no'], $_SESSION['user_id']);

        //get an new answers
        $answers = $test->getAnswers($_SESSION['question_id']);

        $output = '';
        $i = 1;

        foreach ($answers as $answer) {
            $answerId = $answer['id'];
            $answerValue = $answer['value'];

            if ($i == 1) {
                $output .= '<input type="hidden" id="question-title" value="' . $_SESSION['question_no'] . '. ' . $newQuestion . '">';
                $output .= '<input type="hidden" id="progress" value="' . $progress . '">';
            }
            if (($i % 2) != 0) $output .= '<div class="row">';
            $output .= '<div class="col" id="a'. $i .'" onclick="setAnswer('. $answerId .', ' . $i . ')" >
                        <a>' . $answerValue . '</a>
                    </div>';
            if (($i % 2) == 0 || $i == count($answers)) $output .= '</div>';
            $i++;
        }

        echo $output;
    } else {
        echo 'done';
    }
} else {
    echo 'Sistēmas kļūda - Neizdevās saglabāt atbildi!';
}



