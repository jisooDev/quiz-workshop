<?php
session_start();

// Read the JSON file
$quizData = json_decode(file_get_contents('database.json'), true);


if (isset($_GET['id'])) { 
    $quiz_id = $_GET['id'];
    // Check if the current question index is set in the session, if not, set it to 0
    $selectedQuiz = null;
    foreach($quizData as $quiz){
        if($quiz['id'] == $quiz_id){
            $selectedQuiz = $quiz;
            break;
        }
    }

    if($selectedQuiz){
        $_SESSION['questionTotal'] = count($selectedQuiz['quiz_detail']);
        // Check if the form is submitted, and if so, process the answer
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
            // Assuming you have a form with an input named 'userAnswer'
            $userAnswer = isset($_POST['userAnswer']) ? $_POST['userAnswer'] : null;

            // Validate and process the answer as needed
            // For simplicity, let's just check if the answer is correct
            if ($userAnswer === $selectedQuiz['quiz_detail'][$_SESSION['currentQuestionIndex']]['answer']) {
                $_SESSION['correctTotal']++;
            } 

            // Move to the next question
            $_SESSION['currentQuestionIndex']++;

        }

        // Display the current question if there are more questions
        if ($_SESSION['currentQuestionIndex'] < count($selectedQuiz['quiz_detail'])) {
            $questionData = $selectedQuiz['quiz_detail'][$_SESSION['currentQuestionIndex']];
            ?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Quiz Website</title>
            </head>
            <body>

            <div>
                <h3>Question <?php echo $_SESSION['currentQuestionIndex'] + 1; ?>: <?php echo $questionData['question']; ?></h3>

                <form method="post">
                    <ul>
                        <?php
                        foreach ($questionData['choices'] as $choice) {
                            echo '<li><label><input type="radio" name="userAnswer" value="' . $choice . '">' . $choice . '</label></li>';
                        }
                        ?>
                    </ul>

                    <input type="submit" value="Next">
                </form>
            </div>

            </body>
            </html>

            <?php
        } else {
            // Quiz completed
            echo '<p>Quiz completed!</p>' . $_SESSION['correctTotal'] . '/' .  $_SESSION['questionTotal'];
        }
    } else {
        echo 'yy';
    }
}
 else {
    $_SESSION['currentQuestionIndex'] = 0;
    $_SESSION['questionTotal'] = 0;
    $_SESSION['correctTotal'] = 0;

    echo '<h2>Select Quiz Group</h2>';
    foreach ($quizData as $groupData) {
        echo '<a href="index.php?id=' . $groupData['id'] . '"><button>' . $groupData['title'] . '</button></a>';
    }
}
?>
