<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register A New Admin</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .error {
         color: red;
      }
   </style>
</head>
<body>
   <?php
   include '../components/connect.php';

   session_start();

   $admin_id = $_SESSION['admin_id'];

   if (!isset($admin_id)) {
      header('location:admin_login.php');
   }

   $message = array();

   if (isset($_POST['submit'])) {
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $pass = sha1($_POST['pass']);
      $pass = filter_var($pass, FILTER_SANITIZE_STRING);
      $cpass = sha1($_POST['cpass']);
      $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

      $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
      $select_admin->execute([$name]);

      if ($select_admin->rowCount() > 0) {
         $message[] = 'Username already exists!';
      } else {
         if ($pass != $cpass) {
            $message[] = 'Confirm password does not match!';
         } else {
            $strongPasswordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
            if (!preg_match($strongPasswordPattern, $_POST['pass'])) {
               $message[] = 'Password should include at least one uppercase letter, one lowercase letter, one digit, and one special character (@ $ ! % * ? &).';
            } else {
               $insert_admin = $conn->prepare("INSERT INTO `admins`(name, password) VALUES(?,?)");
               $insert_admin->execute([$name, $cpass]);
               $message[] = 'New admin registered successfully!';
            }
         }
      }
   }
   ?>

   <?php include '../components/admin_header.php'; ?>

   <section class="form-container">
      <?php if (is_array($message) && !empty($message)) : ?>
         <div class="message">
            <?php foreach ($message as $msg) : ?>
               <p><?php echo $msg; ?></p>
            <?php endforeach; ?>
         </div>
      <?php endif; ?>

      <form action="" method="post" onsubmit="return validateForm();">
         <h3>register now</h3>
         <input style="border:2px solid white;" type="text" name="name" required placeholder="Enter Your Username" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, ''); validateUsername(this);">
         <span class="error" id="usernameError"></span>
         <input style="border:2px solid white;" type="password" name="pass" required placeholder="Enter Your Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, ''); validatePassword(this);">
         <span class="error" id="passwordError"></span>
         <input style="border:2px solid white;" type="password" name="cpass" required placeholder="Confirm Your Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, ''); validateConfirmPassword(this);">
         <span class="error" id="confirmPasswordError"></span>
         <input type="submit" value="register now" class="btn" name="submit">
      </form>
   </section>

   <script>
      function validateForm() {
         var username = document.getElementsByName("name")[0].value;
         var password = document.getElementsByName("pass")[0].value;
         var confirmPassword = document.getElementsByName("cpass")[0].value;

         var usernameError = document.getElementById("usernameError");
         var passwordError = document.getElementById("passwordError");
         var confirmPasswordError = document.getElementById("confirmPasswordError");

         if (username.length <= 6 || !isNaN(username[0])) {
            usernameError.innerText = "Username should be more than 6 characters and should not start with a digit.";
            return false;
         } else {
            usernameError.innerText = "";
         }

         if (password !== confirmPassword) {
            confirmPasswordError.innerText = "Passwords do not match.";
            return false;
         } else {
            confirmPasswordError.innerText = "";
         }

         // Additional password validation
         var strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
         if (!strongPasswordPattern.test(password)) {
            passwordError.innerText = "Password should include at least one uppercase letter, one lowercase letter, one digit, and one special character (@ $ ! % * ? &).";
            return false;
         } else {
            passwordError.innerText = "";
         }

         // Form is valid, allow submission
         return true;
      }

      function validateUsername(input) {
         var username = input.value;
         var usernameError = document.getElementById("usernameError");

         if (username.length <= 6 || !isNaN(username[0])) {
            usernameError.innerText = "Username should be more than 6 characters and should not start with a digit.";
         } else {
            usernameError.innerText = "";
         }
      }

      function validatePassword(input) {
         var password = input.value;
         var passwordError = document.getElementById("passwordError");

         // Additional password validation
         var strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
         if (!strongPasswordPattern.test(password)) {
            passwordError.innerText = "Password should include at least one uppercase letter, one lowercase letter, one digit, and one special character (@ $ ! % * ? &).";
         } else {
            passwordError.innerText = "";
         }
      }

      function validateConfirmPassword(input) {
         var confirmPassword = input.value;
         var confirmPasswordError = document.getElementById("confirmPasswordError");
         var passwordInput = document.getElementsByName("pass")[0];
         var password = passwordInput.value;

         if (confirmPassword !== password) {
            confirmPasswordError.innerText = "Passwords do not match.";
         } else {
            confirmPasswordError.innerText = "";
         }
      }
   </script>
   <script src="../js/admin_script.js"></script>
</body>
</html>
