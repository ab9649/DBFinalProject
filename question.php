<?php
    $QID = $_GET['QID'];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
    <?php include 'components/navbar.php'?>

</head>
<body style="background:#eee;color: #708090;">

    <div class="container">
        <br>
        <div class="row">
          <!-- Main content -->
          <div class="col-lg-9 mb-3">

            <?php
            $qsql = "SELECT * FROM Question natural join users WHERE QID = $QID";
            $qresult = $con->query($qsql);
            $qobj = $qresult->fetch_assoc();
            $title = $qobj["title"];
            $username = $qobj["username"];
            $body = $qobj["body"];
            $qdatetime = $qobj["q_datetime"];

                echo "<div class='card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0'>";
                echo "<div class='row align-items-center'>";
                  echo "<div class='col-md-8 mb-3 mb-sm-0'>";
                    echo "<p class='text-sm'>$username asked this question on: $qdatetime: </p>";
                    echo "<h5><a href='question.php?QID=$QID' class='text-primary'>$title</a></h5>";
                    echo "<p class='text-sm' style='color:black'>$body</p>";
                    if ($qobj["resolved"] == 1) {
                        echo "<p class='text-success mr-2'>Resolved</p>";
                      } else {
                        echo "<p class='text-danger mr-2'>Unresolved</p>";
                      }
                  echo "</div>";
                echo "</div>";
              echo "</div>";


              if ($qobj["resolved"] == 1) {
                echo "BEST ANSWER";
                $resolvedsql = "SELECT * from bestanswer natural join answer natural join users where QID = $QID";
                $aresult = $con->query($resolvedsql);
                $baobj = $aresult->fetch_assoc();
                $adatetime = $baobj["a_datetime"];
                $username = $baobj["username"];
                $answer_body = $baobj["answer_body"];
                $ansid = $baobj["AnsID"];
                

                $votesql = "SELECT count(*) as votes from thumbed_up where ansid = $ansid";
                $vresult = $con->query($votesql);
                $vobj = $vresult->fetch_assoc();
                $votes = $vobj["votes"];

              echo "<div class='card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0'>";
                echo "<div class='row align-items-center'>";
                echo "<h5><class='text-primary'>$votes Upvotes</h5>";
                  echo "<div class='col-md-8 mb-3 mb-sm-0'>";
                    echo "<p class='text-sm'>$username answered this question on: $adatetime </p>";
                    echo "<p class='text-sm' style='color:black'>$answer_body</p>";

                  echo "</div>";
                echo "</div>";
              echo "</div>";
            }

            $asql = "SELECT* from answer natural join users where QID = $QID and ansid not in (select ansid from bestanswer)";
            $aresult = $con->query($asql);
            if (mysqli_num_rows($aresult) == 0) {
                echo "<div class='card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0'>";
                echo "<div class='row align-items-center'>";
                  echo "<div class='col-md-8 mb-3 mb-sm-0'>";
                    echo "<p class='text-sm'>No Answers Yet.</p>";
                  echo "</div>";
                echo "</div>";
              echo "</div>";
            }
            else{
            while ($aobj = $aresult->fetch_assoc()) {
                $username = $aobj["username"];
                $adatetime = $aobj["a_datetime"];
                $answer_body = $aobj["answer_body"];
                $ansid = $aobj["AnsID"];

                $votesql = "SELECT count(*) as votes from thumbed_up where ansid = $ansid";
                $vresult = $con->query($votesql);
                $vobj = $vresult->fetch_assoc();
                $votes = $vobj["votes"];
                
                echo "OTHER ANSWERS";
                echo "<div class='card row-hover pos-relative py-3 px-3 mb-3 border-primary border-top-0 border-right-0 border-bottom-0 rounded-0'>";
                echo "<div class='row align-items-center'>";
                echo "<h5><class='text-primary'>$votes Upvotes</h5>";
                  echo "<div class='col-md-8 mb-3 mb-sm-0'>";
                    echo "<p class='text-sm'>$username answered this question on: $adatetime </p>";
                    echo "<p class='text-sm' style='color:black'>$answer_body</p>";

                  echo "</div>";
                echo "</div>";
              echo "</div>";
            }
        }
            ?>
          </div>
          <?php include 'components/sidebar.php'?>
        </div>
      </div>

</body>
</html>
