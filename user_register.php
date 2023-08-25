<?php
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

$errors = [];

if (isset($_POST['submit'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if ($select_user->rowCount() > 0) {
      $errors[] = 'Email already exists!';
   }

   // Validate username
   if (strlen($name) < 6 || !preg_match('/^[a-zA-Z][a-zA-Z0-9\s]*$/', $name)) {
      $errors[] = 'Username should be at least 6 characters long and should start with a letter (no special characters allowed).';
   }

   // Validate email
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Invalid email address!';
   } else {
      $emailDomain = explode('@', $email)[1];
      if (!in_array($emailDomain, ['gmail.com', 'yahoo.com', 'mail.com'])) {
         $errors[] = 'Email domain must be either gmail.com, yahoo.com, or mail.com.';
      }
   }

   // Validate password
   if (strlen($_POST['pass']) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/', $_POST['pass'])) {
      $errors[] = 'Password must be at least 8 characters long and must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.';
   }

   // Confirm password
   if ($_POST['pass'] != $_POST['cpass']) {
      $errors[] = 'Confirm password does not match the password.';
   }

   if (empty($errors)) {
      $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
      $insert_user->execute([$name, $email, $cpass]);
      $message = 'Registered successfully, login now please!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Electronics Store: Register A New User</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post" style="border:2px solid;">
      <h3>register now</h3>
      <input style="border:2px solid white"  type="text" name="name" required placeholder="enter your username" maxlength="20"  class="box" oninput="validateUsername(this)">
      <span id="username-error" style="color: red;"></span>
      <input style="border:2px solid white" type="email" name="email" required placeholder="enter your email" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, ''); validateEmail(this)">
      <span id="email-error" style="color: red;"></span>
      <input style="border:2px solid white" type="password" name="pass" required placeholder="enter your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, ''); validatePassword(this)">
      <span id="password-error" style="color: red;"></span>
      <input style="border:2px solid white" type="password" name="cpass" required placeholder="confirm your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, ''); validateConfirmPassword(this)">
      <span id="confirm-password-error" style="color: red;"></span>
      <input type="submit" value="register now" class="btn" name="submit">
      <p>Already Have An Account?</p>
      <a href="user_login.php" class="option-btn">login now</a>
   </form>
   <?php
   if (!empty($errors)) {
      foreach ($errors as $error) {
         echo '<p style="color: red;">' . $error . '</p>';
      }
   } elseif (isset($message)) {
      echo '<p style="color: green;">' . $message . '</p>';
   }
   ?>
</section>

<?php include 'components/footer.php'; ?>

<script>
   // Validation functions
   function validateUsername(input) {
         var username = input.value;
         var regex = /^[a-zA-Z][a-zA-Z0-9\s]*$/;

         if (username.length < 6 || !regex.test(username)) {
            document.getElementById("username-error").innerText = 'Username should be at least 6 characters long and should start with a letter (no special characters allowed).';
         } else {
            document.getElementById("username-error").innerText = '';
         }
      }
   function validateEmail(input) {
      var email = input.value;
      var emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail|yahoo|mail)\.com$/;

      if (!emailRegex.test(email)) {
         document.getElementById("email-error").innerText = 'Email domain must be either gmail.com, yahoo.com, or mail.com.';
      } else {
         document.getElementById("email-error").innerText = '';
      }
   }

   function validatePassword(input) {
      var password = input.value;
      var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

      if (!passwordRegex.test(password)) {
         document.getElementById("password-error").innerText = 'Password must be at least 8 characters long and must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.';
      } else {
         document.getElementById("password-error").innerText = '';
      }
   }

   function validateConfirmPassword(input) {
      var confirmPassword = input.value;
      var password = document.querySelector('input[name="pass"]').value;

      if (confirmPassword !== password) {
         document.getElementById("confirm-password-error").innerText = 'Confirm password does not match the password.';
      } else {
         document.getElementById("confirm-password-error").innerText = '';
      }
   }
</script>


<script src="js/script.js"></script>
</body>
</html>
