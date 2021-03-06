<?php

session_start();

//RANDOM STRING GENERATION FUNCTION (FOR RECCODE)
function generateRandomString($length = 8) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

//INITIALIZING VARIABLES
$username = "";
$email = "";
$fname = "";
$lname = "";
$rest_name = "";
$rest_cap = 0;
$rest_loc = "";
$rest_address = "";
$rest_phone = "";
$rest_start = "";
$rest_end = "";

$feedbacks = array();
$errors = array();

$recCode = "";

include("dbconnect.php");
// USER REGISTRATION PROCESS START
if (isset($_POST['reg_user'])) {
    //GETTING INPUT FROM FORM
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);
    $recCode = generateRandomString();

    //PUSHING ERRORS TO THE ERRORS ARRAY IF THE FIELDS ARE EMPTY
    if (empty($fname)) {
        array_push($errors, "First Name is required");
    }
    if (empty($lname)) {
        array_push($errors, "Last Name is required");
    }
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($email)) {
        array_push($errors, "Email is required");
    }
    if (empty($password_1)) {
        array_push($errors, "Password is required");
    }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

    //CHECKING IF THE USERNAME OR EMAIL ALREADY EXISTS IN DATABASE
    $user_check_query = "SELECT * FROM user WHERE uname='$username' OR email='$email' LIMIT 1";
    $restaurant_check_query = "SELECT * FROM restaurant_owner WHERE uname='$username' OR email='$email' LIMIT 1";
    $restaurant_signup_check_query = "SELECT * FROM rest_signup WHERE uname='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($conn, $user_check_query);
    $result2 = mysqli_query($conn, $restaurant_check_query);
    $result3 = mysqli_query($conn, $restaurant_signup_check_query);
    $user = mysqli_fetch_assoc($result);
    $rest = mysqli_fetch_assoc($result2);
    $restSignup = mysqli_fetch_assoc($result3);


    //PUSHES THE RELATED ERRORS TO THE ERRORS ARRAY IF USERNAME OR EMAIL ALREADY EXISTS
    if ($user || $rest || $restSignup) {
        if ($user['uname'] === $username || $rest['uname'] === $username || $restSignup['uname'] = $username) {
            array_push($errors, "Username already exists");
        }

        if ($user['email'] === $email || $rest['email'] === $email) {
            array_push($errors, "email already exists");
        }
    }
    //IF THERE ARE NO ERRORS IN ERRORS ARRAY: INSERTS THE GIVEN DATA TO THE DATABASE AND PUSHES FEEDBACKS TO FEEDBACKS ARRAY
    if (count($errors) == 0) {
        $password = md5($password_1);

        $query = "INSERT INTO user VALUES('$username','$fname','$lname', '$email', '$password', '$recCode',0,0)";
        mysqli_query($conn, $query);
        array_push($feedbacks, "You are registered now.");
        array_push($feedbacks, "You will be redirected to the Sign In screen when you click 'OK' button.");
    }
}

// RESTAURANT REGISTRATION PROCESS START
if (isset($_POST['reg_rest'])) {
    //GETTING INPUTS FROM FORM
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $rest_name = mysqli_real_escape_string($conn, $_POST['rest_name']);
    $rest_loc = mysqli_real_escape_string($conn, $_POST['rest_loc']);
    $rest_address = mysqli_real_escape_string($conn, $_POST['rest_address']);
    $rest_cap = mysqli_real_escape_string($conn, $_POST['rest_cap']);
    $rest_phone = mysqli_real_escape_string($conn, $_POST['rest_phone']);
    $rest_start = filter_input(INPUT_POST, 'rest_start');
    $rest_end = filter_input(INPUT_POST, 'rest_end');
    $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);

    //PUSHING ERRORS TO THE ERRORS ARRAY IF THE FIELDS ARE EMPTY
    if (empty($fname)) {
        array_push($errors, "First Name is required");
    }
    if (empty($lname)) {
        array_push($errors, "Last Name is required");
    }
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($email)) {
        array_push($errors, "Email is required");
    }
    if (empty($password_1)) {
        array_push($errors, "Password is required");
    }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }
    if ($rest_start > $rest_end) {
        array_push($errors, "Opening time of the restaurant cannot be bigger than closing time.");
    }

    //CHECKING IF THE USERNAME OR EMAIL ALREADY EXISTS IN DATABASE
    $user_check_query = "SELECT * FROM user WHERE uname='$username' OR email='$email' LIMIT 1";
    $restaurant_check_query = "SELECT * FROM restaurant_owner WHERE uname='$username' OR email='$email' LIMIT 1";
    $restaurant_signup_check_query = "SELECT * FROM rest_signup WHERE uname='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($conn, $user_check_query);
    $result2 = mysqli_query($conn, $restaurant_check_query);
    $result3 = mysqli_query($conn, $restaurant_signup_check_query);
    $user = mysqli_fetch_assoc($result);
    $rest = mysqli_fetch_assoc($result2);
    $restSignup = mysqli_fetch_assoc($result3);


    //PUSHES THE RELATED ERRORS TO THE ERRORS ARRAY IF USERNAME OR EMAIL ALREADY EXISTS
    if ($user || $rest || $restSignup) {
        if ($user['uname'] === $username || $rest['uname'] === $username || $restSignup['username'] = $username) {
            array_push($errors, "Username already exists");
        }

        if ($user['email'] === $email || $rest['email'] === $email) {
            array_push($errors, "email already exists");
        }
    }

    //INITIALIZING signUpId EMPTY, BECAUSE IT IS AN AUTO INCREMENT FIELD
    $signUpId = null;

    //IF THERE IS NO ERRORS IN ERRORS ARRAY: INSERTS THE GIVEN DATA TO THE DATABASE AND PUSHES FEEDBACKS TO FEEDBACKS ARRAY
    if (count($errors) == 0) {
        $signupId = null;
        $password = md5($password_1);
        $query = "INSERT INTO rest_signup VALUES('$signupId', '$username', '$fname', '$lname', '$rest_name', '$email', '$password', '$rest_loc', '$rest_phone', "
                . "'$rest_address', '$rest_start', '$rest_end', '$rest_cap')";
        mysqli_query($conn, $query);
        array_push($feedbacks, "Your sign-up will be reviewed by an admin and u are going to receive an email about the sign-up result.");
        array_push($feedbacks, "You will be redirected to the Home screen when you click 'OK' button.");
    }
}

// LOGIN PROCESS START
if (isset($_POST['login_user'])) {
    //GETTING INPUT FROM FORM
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
//PUSHING ERRORS TO THE ERRORS ARRAY IF THE FIELDS ARE EMPTY
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
    //IF THERE ARE NO ERRORS
    if (count($errors) == 0) {

        $password2 = md5($password); //ENCRYPTS THE PASSWORD
        //CHECKS IF USERNAME AND PASSWORD MATCHES
        $query1 = "SELECT * FROM user WHERE uname='$username' AND psw='$password2'";
        $query2 = "SELECT * FROM restaurant_owner WHERE uname='$username' AND psw='$password2'";
        $query3 = "SELECT * FROM admin WHERE uname='$username' AND psw='$password2'";
        $results1 = mysqli_query($conn, $query1);
        $results2 = mysqli_query($conn, $query2);
        $results3 = mysqli_query($conn, $query3);
        $arr1 = mysqli_fetch_array($results1, MYSQLI_ASSOC);
        $arr2 = mysqli_fetch_array($results2, MYSQLI_ASSOC);
        $arr3 = mysqli_fetch_array($results3, MYSQLI_ASSOC);

        //LOGS THE USER IN AND SETS THE SESSION VARIABLES
        if (mysqli_num_rows($results1) == 1) {
            if ($arr1['isBanned'] == '0') {//CHECK IF USER IS BANNED
                $_SESSION['username'] = $username;
                $_SESSION['success'] = "You are now logged in";
                header('location: index.php');
            } else {
                array_push($errors, "You are banned.");
            }
        } else if (mysqli_num_rows($results2) == 1) {
            if ($arr2['isBanned'] == '0') {//CHECK IF USER IS BANNED or shutdowned
                $_SESSION['username'] = $username;
                $_SESSION['success'] = "You are now logged in";
                header('location: RestaurantOwner.php');
            } else {
                if ($arr2['isBanned'] != '0') {
                    array_push($errors, "You are banned.");
                }
            }
        } else if (mysqli_num_rows($results3) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            header('location: Admin.php');
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}


//FORGOT PASSWORD PROCESS START
if (isset($_POST['forgotSend'])) {
    //GETS EMAIL INPUT FROM FORM
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    //CHECKS IF THE FIELD IS EMPTY; IF IT IS, PUSHES AN ERROR
    if (empty($email)) {
        array_push($errors, "Email is required");
    }

    //IF THERE IS NO ERRORS
    if (count($errors) == 0) {
        //CHECKS IF THE GIVEN EMAIL MATCHES FOR A USER IN DATABASE
        $query1 = "SELECT * FROM user WHERE email='$email' ";
        $query2 = "SELECT * FROM restaurant_owner WHERE email='$email' ";
        $query3 = "SELECT * FROM admin WHERE email='$email' ";
        $results1 = mysqli_query($conn, $query1);
        $results2 = mysqli_query($conn, $query2);
        $results3 = mysqli_query($conn, $query3);

        //IF GIVEN EMAIL MATCHES WITH A USER
        if (mysqli_num_rows($results1) == 1) {
            $count1 = mysqli_fetch_assoc($results1);
            $to_email = $email;
            $rec_code = $count1['recCode'];
            $subject = "Restaurant Sign Up";
            $body = "Your Recovery Code is " . $rec_code . "";
            $headers = "From: Restaurant Booking System";

            if (mail($to_email, $subject, $body, $headers)) {//SENDS EMAIL
                array_push($feedbacks, "Email successfully sent to " . $to_email . "");
            }
        } else if (mysqli_num_rows($results2) == 1) { //IF GIVEN EMAIL MATCHES WITH A RESTAURANT OWNER
            $count2 = mysqli_fetch_assoc($results2);
            $to_email = $email;
            $rec_code = $count2['recCode'];
            $subject = "Restaurant Sign Up";
            $body = "Your Recovery Code is " . $rec_code . "";
            $headers = "From: Restaurant Booking System";

            if (mail($to_email, $subject, $body, $headers)) {//SENDS EMAIL
                array_push($feedbacks, "Email successfully sent to " . $to_email . "");
            }
        } else if (mysqli_num_rows($results3) == 1) {//IF GIVEN EMAIL MATCHES WITH AN ADMIN
            $count3 = mysqli_fetch_assoc($results3);
            $to_email = $email;
            $rec_code = $count3['recCode'];
            $subject = "Restaurant Sign Up";
            $body = "Your Recovery Code is " . $rec_code . "";
            $headers = "From: Restaurant Booking System";

            if (mail($to_email, $subject, $body, $headers)) {//SENDS EMAIL
                array_push($feedbacks, "Email successfully sent to " . $to_email . "");
            }
        }
    }
}

//SECOND PAGE OF FORGOT PASSWORD
if (isset($_POST['forgot2Send'])) {
    //GETS INPUTS FROM FORM
    $recIn = mysqli_real_escape_string($conn, $_POST['recIn']);
    $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);
    //CHECKS IF INPUTS ARE EMPTY
    if (empty($recIn)) {
        array_push($errors, "Recovery Code is required");
    }if (empty($password_1)) {
        array_push($errors, "Password is required");
    }
    if (empty($password_2)) {
        array_push($errors, "Re-Password is required");
    }

    //IF THERE ARE NO ERRORS
    if (count($errors) == 0) {


        if ($password_1 != $password_2) {   //IF TWO GIVEN PASSWORDS ARE NOT MATCHING
            array_push($errors, "Password do not match.");
        } else {    //IF TWO GIVEN PASSWORDS ARE MATCHING
            //CHECKS IF THE GIVEN RECCODE IS MATCHING WITH AN ACCOUNT, AND DETERMINES THE TYPE OF ACCOUNT
            $query1 = "SELECT * FROM user WHERE recCode='$recIn'";
            $query2 = "SELECT * FROM restaurant_owner WHERE recCode='$recIn'";
            $query3 = "SELECT * FROM admin WHERE recCode='$recIn'";
            $results1 = mysqli_query($conn, $query1);
            $results2 = mysqli_query($conn, $query2);
            $results3 = mysqli_query($conn, $query3);
            $recIn2 = generateRandomString(); //GENERATES A NEW RECOVERY CODE FOR ACCOUNT
            $password = md5($password_1); //ENCRYPTS THE GIVEN PASSWORD
            if (mysqli_num_rows($results1) == 1) { //IF ACCOUNT TYPE IS USER
                $changeP = mysqli_query($conn, "UPDATE user SET psw = '$password', recCode = '$recIn2'  WHERE (recCode = '$recIn')");
                array_push($feedbacks, "Your password has been changed.");
                array_push($feedbacks, "You will be redirected to the Sign In screen when you click 'OK' button.");
            } else if (mysqli_num_rows($results2) == 1) {//IF ACCOUNT TYPE IS RESTAURANT OWNER
                $changeP = mysqli_query($conn, "UPDATE restaurant_owner SET psw = '$password', recCode = '$recIn2'  WHERE (recCode = '$recIn')");
                array_push($feedbacks, "Your password has been changed.");
                array_push($feedbacks, "You will be redirected to the Sign In screen when you click 'OK' button.");
            } else if (mysqli_num_rows($results3) == 1) {//IF ACCOUNT TYPE IS ADMIN
                $changeP = mysqli_query($conn, "UPDATE admin SET psw = '$password', recCode = '$recIn2'  WHERE (recCode = '$recIn')");
                array_push($feedbacks, "Your password has been changed.");
                array_push($feedbacks, "You will be redirected to the Sign In screen when you click 'OK' button.");
            } else {//IF THERE IS NO ACCOUNT MATCHING PUSHES AN ERROR
                array_push($errors, "recCode is not correct.");
            }
        }
    }
}

//SUBMIT TICKET

if (isset($_POST['sub_request'])) { //STARTS WHEN CLICKING A BUTTON
//CHECK THE VARIABLES IF THEY ARE EMPTY PUSH ERROR
    $textArea = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $username = $_SESSION['username'];
    $date = date('Y/m/d');

    if (empty($textArea)) {
        array_push($errors, "Description is required");
    }
    if ($category == "Category") {
        array_push($errors, "You must choose a category.");
    }
//END OF CHECK IF IT IS EMPTY OR NOT
    //CHECKS THE USERS TYPE, AND SELECTS FROM DATABASE, IF THE THERE ARE MORE THAN 5 UNRESPONDED TICKETS, SHOW ERROR.
    $variable = "";
    $user_check = "SELECT uname FROM user WHERE uname='$username'";
    $rest_check = "SELECT uname FROM restaurant_owner where uname='$username'";
    $query1 = mysqli_query($conn, $user_check);
    $query2 = mysqli_query($conn, $rest_check);
    if (mysqli_num_rows($query1) == 1) {
        $variable = "user_uname";
    } else if (mysqli_num_rows($query2) == 1) {
        $variable = "rest_uname";
    }

    if ($variable == "user_uname") {
        $ticket_check_query = "SELECT * FROM ticket WHERE user_uname='$username' AND isResponded= '0'";
    } else if ($variable == "rest_uname") {
        $ticket_check_query = "SELECT * FROM ticket WHERE rest_uname='$username' AND isResponded= '0'";
    }


    $result = mysqli_query($conn, $ticket_check_query);
    $count = mysqli_num_rows($result);
    if ($count >= 5) {
        array_push($errors, "You have more than 4 open requests. You must wait until one of your requests is responded.");
    }
    //END OF SHOWING UNRESPONDED ERROR.
//IF THERE ARE NO ERROR, FIRST CHECK IF IT IS USER OR RESTAURANT OWNER AND ADD IT INTO VARIABLE.
    if (count($errors) == 0) {
        $variable = "";
        $user_check = "SELECT uname FROM user WHERE uname='$username'";
        $rest_check = "SELECT uname FROM restaurant_owner where uname='$username'";
        $query1 = mysqli_query($conn, $user_check);
        $query2 = mysqli_query($conn, $rest_check);
        if (mysqli_num_rows($query1) == 1) {
            $variable = "user_uname";
        } else if (mysqli_num_rows($query2) == 1) {
            $variable = "rest_uname";
        }
// END OF CHECKING USER TYPE.
        //IF IT IS USER, INSERT INTO TICKET WITH USER TYPE. ELSE INSERT INTO TICKET WITH RESTAURANT TYPE.
        if ($variable == "user_uname") {
            $query4 = "INSERT INTO ticket(user_uname,category, description, date, isResponded)  VALUES('$username','$category','$textArea','$date','0')";
            mysqli_query($conn, $query4);
            $notification2SQL = "insert into notification(toName,text,link,isRead) values('$username','You submitted a request ticket. Click to view your request tickets.' ,'viewMyTickets.php' ,0)";
            $queryNoti2 = mysqli_query($conn, $notification2SQL);
        } else if ($variable == "rest_uname") {
            $query5 = "INSERT INTO ticket(rest_uname,category, description, date, isResponded)  VALUES('$username','$category','$textArea','$date','0')";
            mysqli_query($conn, $query5);
            $notification2SQL = "insert into notification(toName,text,link,isRead) values('$username','You submitted a request ticket. Click to view your request tickets.' ,'viewMyTickets.php' ,0)";
            $queryNoti2 = mysqli_query($conn, $notification2SQL);
        }
        //END OF INSERTING
        //SHOWING FEEDBACK
        $adminnamesSQL = "SELECT * FROM admin";
        $queryAdminNames = mysqli_query($conn, $adminnamesSQL);
        while ($row = mysqli_fetch_array($queryAdminNames, MYSQLI_ASSOC)) {
            $uname45 = $row['uname'];
            $notification3SQL = "insert into notification(toName,text,link,isRead) values('$uname45','A Request Ticket has been submitted. Click to go to Admin Panel.' ,'Admin.php' ,0)";
            $queryNoti3 = mysqli_query($conn, $notification3SQL);
        }
        array_push($feedbacks, "Your ticket has been sent.");
        array_push($feedbacks, "You will be redirected to your Tickets screen when you click 'OK' button.");
        //END OF FEEDBACK
    }
}
    