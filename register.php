<?php 
include 'connect.php';
session_start();

if(isset($_POST['signUp'])){
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email=$_POST['email'];
    $Userid =$_POST['Userid'];
    $password = md5($_POST['password']);
    $user_type = $_POST['user_type'];

    $checkEmail = "SELECT * FROM users WHERE Userid='$Userid'";
    $result = $conn->query($checkEmail);
    if($result->num_rows > 0){
        echo "Username Address Already Exists!";
    } else {
        $insertQuery = "INSERT INTO users (firstName, lastName, email,Userid, password, user_type)
                        VALUES ('$firstName', '$lastName', '$email','$Userid', '$password', '$user_type')";
        if($conn->query($insertQuery) === TRUE){
            header("location:index.php?status=registered");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if(isset($_POST['signIn'])){
    
    $password = md5($_POST['password']);
    $Userid = $_POST['Userid'];
    $select = "SELECT * FROM users WHERE Userid = '$Userid' AND password = '$password'";
    $result = mysqli_query($conn, $select);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_array($result);
        
        if($row['user_type'] == 'admin'){
            // Check if the entered password matches 'tkm123' (hashed)
            if($_POST['password'] == 'tkm123'){
                $_SESSION['admin_name'] = $row['firstName'] . ' ' . $row['lastName'];
                header('location:admin.php');
            } else {
                header('location:adminerror.php');
            }
        } elseif($row['user_type'] == 'user'){
            $_SESSION['Userid'] = $Userid; // Make sure this is set after a successful login

            $_SESSION['user_name'] = $row['firstName'] . ' ' . $row['lastName'];
            header('location:userspage.php');
        }
    } 
    else 
    {
        header('location:error.php');
        exit(); 
    }

}
?>
