<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];

   // Fetch logged-in user's information
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $select_user->execute([$user_id]);
   $logged_in_user = $select_user->fetch(PDO::FETCH_ASSOC);
} else {
   $user_id = '';
}

if (isset($_POST['send'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   $errors = [];

   // Validate username as same as logged-in user's username
   if ($name !== $logged_in_user['name']) {
      $errors[] = 'Username must be the same as your logged-in username.';
   }

   // Validate email as same as logged-in user's email
   if ($email !== $logged_in_user['email']) {
      $errors[] = 'Email must be the same as your logged-in email.';
   }

   // Validate number to contain exactly 10 digits
   if (strlen($number) !== 10 || !ctype_digit($number)) {
      $errors[] = 'Number should contain exactly 10 digits.';
   }

   // Validate message to be at least 10 words
   $wordCount = str_word_count($msg);
   if ($wordCount < 10) {
      $errors[] = 'Message should be at least 10 words long.';
   }

   // If there are no errors, insert the message
   if (empty($errors)) {
      $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
      $select_message->execute([$name, $email, $number, $msg]);

      if ($select_message->rowCount() > 0) {
         $message[] = 'Already sent message!';
      } else {
         $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
         $insert_message->execute([$user_id, $name, $email, $number, $msg]);
         $message[] = 'Sent message successfully!';
      }
   } else {
      // Display errors
      foreach ($errors as $error) {
         $message[] = $error;
      }
   }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Electronics Store: You Can Contact Us Here!</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <style>
      /* Style for read-only textboxes */
      input[readonly], textarea[readonly] {
         background-color: #e9e9e9; /* A darker shade of gray */
         color: #555; /* Darker text color for better visibility */
         cursor: not-allowed; /* Show a "not-allowed" cursor for read-only elements */
      }
      .validation-message {
         color: red;
         font-size: 12px;
      }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="contact">

   <form action="" method="post" style="border: 2px solid;">
      <h3>get in touch</h3>
      <input type="text" name="name" placeholder="Enter Your Name" required maxlength="20" class="box" value="<?php echo $logged_in_user['name'] ?? ''; ?>" readonly>
      <input type="email" name="email" placeholder="Enter Your Email" required maxlength="50" class="box" value="<?php echo $logged_in_user['email'] ?? ''; ?>" readonly>
      <input type="number" name="number" id="number" max="9999999999" placeholder="Enter Your Number" required oninput="checkNumber()" class="box">
      <textarea name="msg" class="box" placeholder="Enter Your Message" cols="30" rows="10"></textarea>
      <input type="submit" value="send message" name="send" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script>
   // Function to check the number length and update the number input dynamically
   function checkNumber() {
      const numberInput = document.getElementById('number');
      const numberValue = numberInput.value;
      const numberValidationMessage = document.getElementById('number-validation-message');

      if (numberValue.length !== 10 || !/^\d+$/.test(numberValue)) {
         numberValidationMessage.textContent = 'Number should contain exactly 10 digits.';
      } else {
         numberValidationMessage.textContent = '';
      }
   }
</script>

<script src="js/script.js"></script>

</body>
</html>
