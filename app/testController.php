<?php
class TestController {

    /**
     * TestController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Save current user data from form.
     * Save user name in db and user choosed test id in session
     * Get user id from db and save it in a session, because for now more users can use a same user name.
     *
     * @return array
     */
    public function setUser()
    {
        $return = array();

        $name = $_POST['name'];
        $test_id = $_POST['test'];

        if (empty($name) || empty($test_id)) {
            $return['success'] = 0;
            $return['error'] = 'Obligāti jāaizpilda visi formas lauki!';
        } else {
            $sql = "INSERT INTO users (name) VALUES ('$name')";
            $query = dbQuery::make($sql);
            $result = $query->execute();

            if ($result) {
                $user_id = $this->getUserId($name);
                Configuration::make()->setUserSession($user_id, $name, $test_id);

                $return['success'] = 1;
                $return['error'] = '';
            } else {
                $return['success'] = 0;
                $return['error'] = 'Sistēmas kļūda!';
            }
        }

        return $return;
    }

    /**
     * Get all tests from database to use in UI dropdown
     *
     * @return mixed
     */
    public function getTests()
    {
        $sql = "SELECT id, name FROM test";
        $query = dbQuery::make($sql);
        $result = $query->execute();

        return $result;
    }

    /**
     * Get random question from questions of test, which is not already used
     *
     * @param $testId
     * @param $questionNo
     * @param $userId
     * @return mixed
     */
    public function getQuestion($testId, $questionNo, $userId)
    {
        if ($questionNo < $this->getTestQCount($testId)) {
            $questionsDone = $this->getDoneQuestions($testId, $userId);

            $availableQuestions = $this->getAvailableQuestions($testId, $questionsDone);
            //print_r($availableQuestions);

            $randomQId = array_rand($availableQuestions, 1);

            $randomQValue = $availableQuestions[$randomQId];

            $_SESSION['question_no']++;
            $_SESSION['question_id'] = $randomQId;

            return $randomQValue;
        } else {
            header("location: result.php");
        }
    }

    /**
     * Get value(text) from question id
     *
     * @param $questionId
     * @return mixed
     */
    public function getQuestionValue($questionId)
    {
        $sql = "SELECT value FROM questions WHERE id = '$questionId'";
        $query = dbQuery::make($sql);
        $result = $query->execute();

        foreach ($result as $item) {
            return $item['value'];
        }
    }

    /**
     * Get count of questions in current test
     *
     * @param $test_id
     * @return int
     */
    public function getTestQCount($test_id)
    {
        $sql = "SELECT id FROM questions WHERE test_id = '$test_id'";
        $query = dbQuery::make($sql);
        $result = $query->execute();

        return $query->getCount($result);
    }

    /**
     * Qet all questions with which current user is already done
     *
     * @param $testId
     * @param $userId
     * @return array
     */
    public function getDoneQuestions($testId, $userId)
    {
        $questionsDone = array();

        $sql = "SELECT question_id FROM results WHERE test_id = $testId AND user_id = $userId";
        $query = dbQuery::make($sql);
        $results = $query->execute();

        foreach ($results as $result) {
            $questionsDone[] = $result['question_id'];
        }

        return $questionsDone;
    }

    /**
     * Get all questions for the test, which is not already used for current user
     *
     * @param $testId
     * @param $questionsDone
     * @return array
     */
    public function getAvailableQuestions($testId, $questionsDone)
    {
        //get all available current test questions
        $questionIds = array();

        (count($questionsDone) > 0) ? $select = "AND id NOT IN (" . implode(',', $questionsDone) . ")" : (string)$select = "";

        $sql = "SELECT id, value
                FROM questions 
                WHERE test_id = '$testId' 
                " . $select . " ";
        $query = dbQuery::make($sql);
        $allQuestions = $query->execute();

        foreach ($allQuestions as $question) {
            $questionIds[$question['id']] = $question['value'];
        }

        return $questionIds;
    }

    /**
     * Get all available answers for specific question
     *
     * @param $questionId
     * @return mixed
     */
    public function getAnswers($questionId)
    {
        $sql = "SELECT id, value FROM answers WHERE question_id = $questionId";
        $query = dbQuery::make($sql);
        $results = $query->execute();

        return $results;
    }

    /**
     *Save an each answer in db
     *
     * @param $answerId
     * @return mixed
     */
    public function saveAnswer($answerId)
    {
        $userId = $_SESSION['user_id'];
        $testId = $_SESSION['test_id'];
        $questionId = $_SESSION['question_id'];

        $sql = "INSERT INTO results (user_id, test_id, question_id, answer_id) 
                VALUES ($userId, $testId, $questionId, $answerId)";
        $query = dbQuery::make($sql);
        $result = $query->execute();

        return $result;
    }

    /**
     * Calculate final result - total correct answered questions for the test
     *
     * @param $testId
     * @param $userId
     * @return array
     */
    public function calculateResult($testId, $userId)
    {
        $finalResult = array();
        $totalAnswers = $this->getTestQCount($testId);
        $correctAnswers = 0;

        $sql = "SELECT answer_id 
                FROM results 
                WHERE user_id = $userId
                AND test_id = $testId
                ";
        $query = dbQuery::make($sql);
        $result = $query->execute();

        foreach ($result as $item) {
            $answerId = $item['answer_id'];

            $sql = "SELECT is_correct 
                FROM answers 
                WHERE id = $answerId AND is_correct = 1";
            $query = dbQuery::make($sql);
            $correct = $query->execute();

            if ((int)count($correct) === 1) {
                $correctAnswers++;
            }
        }

        $this->saveResult($userId, $testId, $correctAnswers);
        $finalResult['correct'] = $correctAnswers;
        $finalResult['total'] = $totalAnswers;

        return $finalResult;
    }

    /**
     * Save result - answer in db for each user, test, question
     *
     * @param $userId
     * @param $testId
     * @param $count
     */
    protected function saveResult($userId, $testId, $count)
    {
        $sql = "INSERT INTO final_results (user_id, test_id, correct_answers) 
                VALUES ($userId, $testId, $count)";
        $query = dbQuery::make($sql);
        $query->execute();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getUserId($name)
    {
        $sql = "SELECT id FROM users WHERE name = '$name'";
        $query = dbQuery::make($sql);
        $result = $query->execute();
        $no = 0;

        foreach ($result as $item) {
            $no++;
            if ($no == count($result)) return $item['id'];
        }
    }
}