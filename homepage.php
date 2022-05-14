<?php
    // $UserID = 2;
    session_start();
    if (!isset($_SESSION['UserID'])){
        header("refresh:5; login.php"); // redirect after 5 second pause
        echo "You're not logged in. Redirecting you to login page in 5 seconds or click <a href=\"login.php\">here</a>.";
        exit();
    }
    $UserID = $_SESSION['UserID'];
    include 'connection.php';

    $sql = "SELECT * FROM Users NATURAL JOIN user_status WHERE UserID = $UserID";
    $result = $con->query($sql);
    $obj = $result->fetch_assoc();
    
    $username = $obj["username"];
    $first_name = $obj["first_name"];
    $last_name = $obj["last_name"];
    $status_name = $obj["status_name"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
    <?php include 'components/navbar.php'?>

</head>
<body style="background:#eee;color: #708090;">

    <div class="container">
        <div style="margin-top:50px; margin-bottom: 20px;">
          <?php
              echo "<h1 style='color:black'><strong>Welcome back, $first_name $last_name.</strong></h1>";
              echo "<h5>You are currently a <strong>$status_name</strong>.</h5>";
          ?>
        </div>
        <br>
        <p>Your recent activities:</p>
        <div class="row">
          <!-- Main content -->
          <div class="col-lg-9 mb-3">

            <?php
            $sql = "WITH UserQuestions (QID, AID, qa_datetime) AS ( SELECT QID, NULL, q_datetime FROM Question WHERE UserID = $UserID ), UserAnswers (QID, AID,qa_datetime) AS ( SELECT NULL, AnsID, a_datetime FROM Answer WHERE UserID = $UserID ) SELECT * FROM UserQuestions UNION SELECT * FROM UserAnswers ORDER BY qa_datetime DESC";
            $result = $con->query($sql);
            if (mysqli_num_rows($result) == 0) {
              echo "<div class='card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0'>";
              echo "<div class='row align-items-center'>";
                echo "<div class='col-md-8 mb-3 mb-sm-0'>";
                  echo "<p class='text-sm'>No activities.</p>";
                echo "</div>";
              echo "</div>";
            echo "</div>";
            }
            while ($obj = $result->fetch_assoc()) {
              $qa_datetime = $obj['qa_datetime'];
              $qa_qid = $obj['QID'];
              $qa_aid = $obj['AID'];
              if ($qa_qid != "") {
                $qsql = "SELECT * FROM Question WHERE QID = $qa_qid";
                $qresult = $con->query($qsql);
                $qobj = $qresult->fetch_assoc();
                $title = $qobj["title"];
                $body = $qobj["body"];
                $QID = $qobj["QID"];
                echo "<div class='card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0'>";
                echo "<div class='row align-items-center'>";
                  echo "<div class='col-md-8 mb-3 mb-sm-0'>";
                    echo "<p class='text-sm'>You asked this question on $qa_datetime: </p>";
                    echo "<h5><a href='question.php?QID=$QID' class='text-primary'>$title</a></h5>";
                    if ($qobj["resolved"] == 1) {
                      echo "<p class='text-success mr-2'>Resolved</p>";
                    } else {
                      echo "<p class='text-danger mr-2'>Unresolved</p>";
                    }
                    echo "<p class='text-sm' style='color:black'>$body</p>";
                  echo "</div>";

                //   echo "<div class='col-md-4 op-7'>";
                //   echo "<div class='row text-center op-7'>";
                //     echo "<div class='col px-1'><i class='ion-ios-eye-outline icon-1x'></i><span class='d-block text-sm'>";
                //       if ($qobj["resolved"] == 1) {
                //         echo "<p class='text-success mr-2'>Resolved</p>";
                //       } else {
                //         echo "<p class='text-danger mr-2'>Unresolved</p>";
                //       }
                //     echo "</span> </div>";
                //   echo "</div>";
                // echo "</div>";


                echo "</div>";
              echo "</div>";
              } else {
                $asql = "SELECT * FROM (Answer JOIN Question ON Answer.QID = Question.QID) JOIN Users ON Question.UserID = Users.UserID WHERE AnsID = $qa_aid";
                $aresult = $con->query($asql);
                $aobj = $aresult->fetch_assoc();
                $title = $aobj["title"];
                $question_user = $aobj["username"];
                $body = $aobj["answer_body"];
                $q_datetime = $aobj["q_datetime"];
                $aQID = $aobj["QID"];
                $question_user_id = $aobj["UserID"];
                echo "<div class='card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0'>";
                echo "<div class='row align-items-center'>";
                  echo "<div class='col-md-8 mb-3 mb-sm-0'>";
                    echo "<p class='text-sm'>You answered this question on $qa_datetime: </p>";
                    echo "<h5><a href='question.php?QID=$aQID' class='text-primary'>$title</a></h5>";
                    echo "<p class='text-sm'>";
                      echo "<span class='op-6'>Posted on $q_datetime</span>";
                      echo "<span class='op-6'> by </span>";
                      echo "<a class='text-black' href='user_profile.php?ViewingUserID=$question_user_id'>$question_user</a>";
                    echo "</p>";
                    if ($aobj["resolved"] == 1) {
                      echo "<p class='text-success mr-2'>Resolved</p>";
                    } else {
                      echo "<p class='text-danger mr-2'>Unresolved</p>";
                    }
                    echo "<p class='text-sm' style='color:black'><strong>Your answer:</strong> $body</p>";
                  echo "</div>";

                //   echo "<div class='col-md-4 op-7'>";
                //   echo "<div class='row text-center op-7'>";
                //     echo "<div class='col px-1'><i class='ion-ios-eye-outline icon-1x'></i><span class='d-block text-sm'>";
                //       if ($aobj["resolved"] == 1) {
                //         echo "<p class='text-success mr-2'>Resolved</p>";
                //       } else {
                //         echo "<p class='text-danger mr-2'>Unresolved</p>";
                //       }
                //     echo "</span> </div>";
                //   echo "</div>";
                // echo "</div>";

                
                echo "</div>";
              echo "</div>";

              }
            }

            ?>
            <!-- End of post 1 -->
            <!-- <div class="card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0">
              <div class="row align-items-center">
                <div class="col-md-8 mb-3 mb-sm-0">
                  <p class="text-sm">You answered this question on [date]: </p>
                  <h5>
                    <a href="#" class="text-primary">How do I do CS?</a>
                  </h5>
                  <p class="text-sm">
                    <span class="op-6">Posted on</span> 
                    <a class="text-black" href="#">[date]</a> 
                    <span class="op-6">by</span> 
                    <a class="text-black" href="#">[user]</a>
                  </p>
                  <div class="text-sm op-5"> 
                    <a class="text-black mr-2" href="#">#C++</a> 
                    <a class="text-black mr-2" href="#">#AppStrap Theme</a> 
                    <a class="text-black mr-2" href="#">#Wordpress</a> 
                  </div>
                </div>
              </div>
            </div> -->
            <!-- /End of post 1 -->
          </div>
          <?php include 'components/sidebar.php'?>
        </div>
      </div>

</body>
</html>
