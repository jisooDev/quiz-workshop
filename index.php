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
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
                <style>

                    *{
                        font-family: Tahoma, sans-serif;
                    }
                    body{
                            background: url(./assets/bg/bg-<?php echo $selectedQuiz['id'];?>.png);
                            background-size: cover;
                            background-repeat: no-repeat;
                    }
            
                    .card-content{
                        display:flex;
                        flex-direction: column;
                    }
                    .card-menu{
                        margin-bottom:10px;
                        padding:5px;
                    }
                    .content-name{
                        text-shadow:
                        3px 3px 0 #fff,
                        -3px 3px 0 #fff,
                        -3px -3px 0 #fff,
                        3px -3px 0 #fff;
                    }
                    .card-menu .card-button{
                        cursor: pointer;
                        text-align:start;
                        min-width:270px;
                        min-height:50px;
                        padding: 10px;
                        border-radius: 15px;
                        font-size:18px;
                        color:#000;
                        box-shadow: rgba(255,255,255) 0px 1px 4px, rgb(255, 255, 255) 0px 0px 0px 3px;
                    }
                    .badge-index{
                        width: 150px;
                        margin: auto;
                        border-radius:15px;
                    }

                    .bg-1{
                        background:#A1EBFF;
                    }
                    .bg-2{
                        background:#FD98F3;
                    }
                    .bg-3{
                        background:#FFA835;
                    }
                    .bg-4{
                        color:#fff;
                        background:#886AB5;
                    }
                    .bg-5{
                        color:#fff;
                        background:#00BF63;
                    }

                    .bg-1-choice{
                        background: radial-gradient(circle at 50% 50%, #cdffd8, #94b9ff);
                    }
                    .bg-2-choice{
                        background: radial-gradient(circle at 50% 50%, #708aff, #ff8cd3);
                    }
                    .bg-3-choice{
                        background: linear-gradient(135deg, #ff7272, #ffb07f);
                    }
                    .bg-4-choice{
                        background: linear-gradient(90deg, #b594f6, #5cbbff);
                    }

                    .bg-5-choice{
                        background: radial-gradient(circle at 0% 0%, #19ff1b, #f6ffa9);
                    }
                    
                </style>
                <title>Quiz Website</title>
            </head>
            <body>

            <div class="mx-auto text-center py-2" style="color:#fff;font-size:18px;height:10vh;">
                <div class="mx-auto p-2" style="max-width:700px;">
                <?php 
                        echo $selectedQuiz['title_with_group'] ;
                    ?>
                </div>
            </div>
            <div class="container px-4 pb-4" style="height:90vh;">
                <div class="card-content p-2 text-center h-100" style="background:rgb(255,255,255,0.7);border-radius:15px;max-width:500px;margin:auto;">
                    <div><b>แบบทดสอบหลังเรียน</b></div>
                    <div class="badge-index my-2 py-1 bg-<?php echo $selectedQuiz['id']; ?>"><b>ข้อที่ <?php echo $_SESSION['currentQuestionIndex'] + 1; ?></b></div>
                    <div class="mb-3">
                       <b style="font-size:16px;"> <?php echo $questionData['question']; ?> </b>
                    </div>
                    <div class="flex-grow-1">
                        <form id="quiz-form" method="post">
                            <?php
                                foreach ($questionData['choices'] as $choice) {
                                    echo '<div class="card-menu" onclick="submitForm(\'' . $choice . '\')">
                                            <div class="card-button bg-'. $selectedQuiz['id'] .'-choice">' . $choice . '</div>
                                        </div>';
                                }
                                ?>
                                <input type="hidden" name="userAnswer" id="userAnswer">
                        </form>
                    </div>
                </div>          
            </div>

            <script>
                function submitForm(choice) {
                    document.querySelector('input[name="userAnswer"]').value = choice;
                    document.getElementById('quiz-form').submit();
                }
            </script>

            </body>
            </html>

            <?php
        } else {

            
            // Quiz completed
            echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
                    <style>
                        *{
                            font-family: Tahoma, sans-serif;
                        }
                        body{
                            background: url(./assets/bg/bg-'; echo $selectedQuiz['id'] ; echo '.png);
                            background-size: cover;
                            background-repeat: no-repeat;
                        }
                        .card-content{
                            display:flex;
                            flex-direction: column;
                        }
                        .card-menu{
                            margin-bottom:10px;
                            padding:5px;
                        }

                        .card-menu .card-button{
                            text-align:start;
                            min-width:270px;
                            min-height:50px;
                            color: #fff;
                            background:blue;
                            /* display: inline; */
                            padding: 10px;
                            border-radius: 15px;
                            font-size:18px;

                            /* box-shadow: rgba(0, 0, 0, 0.15) 2.4px 2.4px 3.2px; */
                            box-shadow: rgb(0, 0, 0, 0.7) 4px 4px 3.2px ;
                        }
                        .score{
                            margin: auto;
                            position:relative;
                        }
                        .score b{
                            position:absolute;
                            top: 56%;
                            left: 35%;
                            font-size: 70px;
                        }
                    </style>
                    <title>Document</title>
                </head>
                <body>
                    <div class="container p-4" style="height:100vh;">
                        <div class="card-content p-2 text-center h-100" style="background:rgb(255,255,255,0.7);border-radius:15px;max-width:500px;margin:auto;">
                            <div style="padding: 40px 0;font-size:55px;"><b>สรุปคะแนน</b></div>
                            <div class="flex-grow-1">';
                    
                                // echo '<div class="score"><b>' . $_SESSION['correctTotal'] . '/' .  $_SESSION['questionTotal'] . '</b></div>' ;
                                echo '<div class="score"> <img style="width:100%;height:100%;" src="./assets/icon-score.png" alt=""> <b> ' . $_SESSION['correctTotal'] . '/' .  $_SESSION['questionTotal'] . '</b></div>' ;

                                
                        echo '</div>
                            <div id="myLink" class="my-2">
                                <div style="cursor:pointer;">
                                    <img style="width:90px;height:90px;background:#fff;border-radius:100%;" src="./assets/icon-home.png" alt="">
                                </div>
                            </div>
                        </div>          
                    </div>


                    ';
                    
                    echo '<script>
                            document.getElementById("myLink").addEventListener("click", function(event) {
                                window.location.href = "/index.php"
                            })
                        </script>' ;
                    
    
                    
                    echo '
                </body>
                </html>
            
            ';
        }
    } else {
        echo 'yy';
    }
}
 else {
    $_SESSION['currentQuestionIndex'] = 0;
    $_SESSION['questionTotal'] = 0;
    $_SESSION['correctTotal'] = 0;

    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <style>

            *{
                font-family: Tahoma, sans-serif;
            }
            body{
                background: url(./assets/content.png);
                background-size: cover;
                background-repeat: no-repeat;
            }
            .card-content{
                display:flex;
                flex-direction: column;
            }
            .card-menu{
                margin-bottom:10px;
                padding:5px;
            }
            .content-name{
                text-shadow:
                3px 3px 0 #fff,
                -3px 3px 0 #fff,
                -3px -3px 0 #fff,
                3px -3px 0 #fff;
            }
            .card-menu .card-button{
                text-align:start;
                min-width:270px;
                min-height:50px;
                color: #fff;
                padding: 10px;
                border-radius: 15px;
                font-size:18px;
                box-shadow: rgb(0, 0, 0, 0.7) 4px 4px 3.2px ;
            }
            .bg-1{
                background: #69CFEF;
            }
            .bg-2{
                background: #3EACFF;
            }
            .bg-3{
                background: #0080E1;
            }
            .bg-4{
                background: #18669F;
            }
            .bg-5{
                background: #004F89;
            }
        </style>
        <title>Quiz Website</title>
    </head>
    <body>
        <div class="container p-4" style="height:100vh;">
            <div class="card-content p-2 text-center h-100" style="background:rgb(255,255,255,0.4);border-radius:15px;max-width:500px;margin:auto;">
                <div class="content-name" style="padding: 40px 0;font-size:65px;"><b>คลังความรู้</b></div>
                <div class="flex-grow-1">
                ';

                foreach ($quizData as $groupData) {
                    echo '
                    <a href="index.php?id=' . $groupData['id'] . '">
                        <div class="card-menu">
                            <div class="card-button bg-'. $groupData['id'] .'">' . $groupData['title'] . ' </div>
                        </div>
                    </a>';
                }
        
            echo '
                </div>
            </div>          
        </div>
    </body>
    </html>
    ';
   
}
?>

