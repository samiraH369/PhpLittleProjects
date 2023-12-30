<?php
    include("database.php");
    include("header.php");
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/kute.js@2.1.2/dist/kute.min.js"></script>
    <script src="script.js" defer></script>
</head>
<body>
    <p><b>Please fill this form to <i style="color: cyan;">register</i></b></p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <label for="email">Enter your email address : </label><br>
        <input type="email" name="email" id="email" required placeholder="Ex : example@gmail.com"><br>

        <label for="username">Enter your username :</label><br>
        <input type="text" name="username" id="username" required placeholder="Ex : Paladin67"><br>

        <label for="password">Enter your password :</label><br>
        <input type="password" name="password" id="password" required placeholder="*********" minlength="8"><br>

        <input type="submit" value="Submit" name="submit" id="submit"><br>
        <label id="displayErrors"></label>

    </form>
    <p id="redirection">Already registered? Click here to <a href="login.php">login</a></p><br>
</body>
</html>

<?php
    include('footer.php');
?>

<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
        
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
        
        if(!empty($email) && !empty($username) && !empty($password)){
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $username, $hashedPassword);

            try{
                $stmt->execute();

                //Register the username in the $_SESSION variable to use it later.
                
                $_SESSION["username"] = $username;

                $sql = "SELECT * FROM users where username = '$username'";   
                $result = mysqli_query($conn, $sql);

                if(mysqli_num_rows($result) > 0){
                    $row = mysqli_fetch_assoc($result);
                    $id_user = $row["id"];
                }

                header("Location: home.php?username=$username&id=$id_user");
                exit();
            }
            catch(mysqli_sql_exception){
                echo "  <script>
                            let displayErrors = document.getElementById('displayErrors');
                            displayErrors.textContent = 'This email/username is already taken!❌';
                            displayErrors.style.color = 'red';
                            displayErrors.style.fontSize = '30px'
                        </script>";
            }
            $stmt->close();
           
        }
    }
    $conn->close();
?>

