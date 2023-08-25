<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:user_login.php');
}

if (isset($_POST['order'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if ($check_cart->rowCount() > 0) {
      $out_of_stock_products = [];
      if ($fetch_cart !== false) {
         // Your existing code inside this block
         while ($fetch_cart = $check_cart->fetch(PDO::FETCH_ASSOC)) {
            $product_id = $fetch_cart['pid'];
            $quantity_in_cart = $fetch_cart['quantity'];
    
            // Fetch the current inventory quantity for the product
            $select_inventory = $conn->prepare("SELECT quantity FROM `inventory` WHERE product_id = ?");
            $select_inventory->execute([$product_id]);
            $fetch_inventory = $select_inventory->fetch(PDO::FETCH_ASSOC);
            $current_inventory_quantity = $fetch_inventory['quantity'];
    
            if ($current_inventory_quantity < $quantity_in_cart) {
               // Insufficient inventory quantity, add to the out-of-stock products list
               $out_of_stock_products[] = $fetch_cart['name'];
            } else {
               // Sufficient inventory quantity, reduce it
               $new_quantity = $current_inventory_quantity - $quantity_in_cart;
               $update_inventory = $conn->prepare("UPDATE `inventory` SET quantity = ? WHERE product_id = ?");
               $update_inventory->execute([$new_quantity, $product_id]);
            }
         }
     }

      // Check if any products are out of stock
      if (count($out_of_stock_products) > 0) {
         $message[] = 'Sorry, the following products are out of stock: ' . implode(', ', $out_of_stock_products);
      } else {
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);

         $message[] = 'order placed successfully!';
      }
   } else {
      $message[] = 'your cart is empty';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Electronics Store: Check Out</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form id="myForm" action="" method="POST" style="border:2px solid">

   <h3>your orders</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items = [];
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '&#8377;'.$fetch_cart['price'].'/- x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">your cart is empty!</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
         <div class="grand-total">grand total : <span>&#8377;<?= $grand_total; ?>/-</span></div>
      </div>

      <h3>place your orders</h3>

      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" placeholder="enter your name" class="box" maxlength="20" required>
            <span style="color:red;"class="error-message" id="name-error-message"></span>
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="text" name="number" placeholder="enter your number" class="box" maxlength="10" required>
            <span style="color:red;" class="error-message" id="number-error-message"></span>
         </div>
         <div class="inputBox">
    <span>your email :</span>
    <input type="email" name="email" placeholder="enter your email" class="box" maxlength="50" required>
    <span style="color:red;" class="error-message" id="email-error-message"></span>
  </div>
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">cash on delivery</option>
               <!-- <option value="credit card">credit card</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option> -->
            </select>
         </div>
         <div class="inputBox">
    <span>address line 01 :</span>
    <input type="text" name="flat" placeholder="e.g. flat number" class="box" maxlength="50" required>
    <span style="color:red;" class="error-message" id="flat-error-message"></span>
  </div>
  <div class="inputBox">
    <span>address line 02 :</span>
    <input type="text" name="street" placeholder="e.g. street name" class="box" maxlength="50" required>
    <span style="color:red;" class="error-message" id="street-error-message"></span>
  </div>
  <div class="inputBox">
    <span>city :</span>
    <input type="text" name="city" placeholder="e.g. Bangalore" class="box" maxlength="50" required>
    <span style="color:red;" class="error-message" id="city-error-message"></span>
  </div>
  <div class="inputBox">
    <span>state :</span>
    <input type="text" name="state" placeholder="e.g. Karnataka" class="box" maxlength="50" required>
    <span style="color:red;" class="error-message" id="state-error-message"></span>
  </div>
  <div class="inputBox">
    <span>country :</span>
    <input type="text" name="country" placeholder="e.g. India" class="box" maxlength="50" required>
    <span style="color:red;" class="error-message" id="country-error-message"></span>
  </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" min="10000" max="999999" onkeypress="if(this.value.length == 6) return false;" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

</section>
<script>
  const nameInput = document.querySelector('input[name="name"]');
  const numberInput = document.querySelector('input[name="number"]');
  const errorMessage = document.getElementById("name-error-message");
  const errorMessagee = document.getElementById("number-error-message");

  nameInput.addEventListener("input", validateName);
  numberInput.addEventListener("input", validateNumber);

  function validateName() {
    const name = nameInput.value.trim();
    const nameRegex = /^[A-Za-z\s]+$/;

    if (name === "") {
      errorMessage.textContent = "Name is required";
      nameInput.classList.add("error");
      return false;
    } else if (!nameRegex.test(name)) {
      errorMessage.textContent = "Invalid name format";
      nameInput.classList.add("error");
      return false;
    } else {
      errorMessage.textContent = "";
      nameInput.classList.remove("error");
      return true;
    }
  }

  function validateNumber() {
    const number = numberInput.value.trim();
    const numberRegex = /^[0-9]{10}$/;

    if (number === "") {
      errorMessagee.textContent = "Number is required";
      numberInput.classList.add("error");
      return false;
    } else if (!numberRegex.test(number)) {
      errorMessagee.textContent = "Invalid phone number format";
      numberInput.classList.add("error");
      return false;
    } else {
      errorMessagee.textContent = "";
      numberInput.classList.remove("error");
      return true;
    }
  }
  const emailInput = document.querySelector('input[name="email"]');
  const emailErrorMessage = document.getElementById("email-error-message");

  emailInput.addEventListener("input", validateEmail);

  function validateEmail() {
    const email = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "") {
      emailErrorMessage.textContent = "Email is required";
      emailInput.classList.add("error");
      return false;
    } else if (!emailRegex.test(email)) {
      emailErrorMessage.textContent = "Invalid email format";
      emailInput.classList.add("error");
      return false;
    } else {
      emailErrorMessage.textContent = "";
      emailInput.classList.remove("error");
      return true;
    }
  }
  
  const flatInput = document.querySelector('input[name="flat"]');
  const flatErrorMessage = document.getElementById("flat-error-message");

  flatInput.addEventListener("input", validateFlat);

  function validateFlat() {
    const flat = flatInput.value.trim();
    const flatRegex = /^[0-9]{1,3}$/;

    if (flat === "") {
      flatErrorMessage.textContent = "Flat number is required";
      flatInput.classList.add("error");
      return false;
    } else if (!flatRegex.test(flat)) {
      flatErrorMessage.textContent = "Invalid flat number format";
      flatInput.classList.add("error");
      return false;
    } else {
      flatErrorMessage.textContent = "";
      flatInput.classList.remove("error");
      return true;
    }
  }

  const streetInput = document.querySelector('input[name="street"]');
  const streetErrorMessage = document.getElementById("street-error-message");

  streetInput.addEventListener("input", validateStreet);

  function validateStreet() {
    const street = streetInput.value.trim();
    const streetRegex = /^[A-Za-z\s]+$/;

    if (street === "") {
      streetErrorMessage.textContent = "Street name is required";
      streetInput.classList.add("error");
      return false;
    } else if (!streetRegex.test(street)) {
      streetErrorMessage.textContent = "Invalid street name format";
      streetInput.classList.add("error");
      return false;
    } else {
      streetErrorMessage.textContent = "";
      streetInput.classList.remove("error");
      return true;
    }
  }

  const cityInput = document.querySelector('input[name="city"]');
  const stateInput = document.querySelector('input[name="state"]');
  const countryInput = document.querySelector('input[name="country"]');
  const cityErrorMessage = document.getElementById("city-error-message");
  const stateErrorMessage = document.getElementById("state-error-message");
  const countryErrorMessage = document.getElementById("country-error-message");

  cityInput.addEventListener("input", validateCity);
  stateInput.addEventListener("input", validateState);
  countryInput.addEventListener("input", validateCountry);

  function validateCity() {
    const city = cityInput.value.trim();
    const cityRegex = /^[A-Za-z\s]+$/;

    if (city === "") {
      cityErrorMessage.textContent = "City name is required";
      cityInput.classList.add("error");
      return false;
    } else if (!cityRegex.test(city)) {
      cityErrorMessage.textContent = "Invalid city name format";
      cityInput.classList.add("error");
      return false;
    } else {
      cityErrorMessage.textContent = "";
      cityInput.classList.remove("error");
      return true;
    }
  }

  function validateState() {
    const state = stateInput.value.trim();
    const stateRegex = /^[A-Za-z\s]+$/;

    if (state === "") {
      stateErrorMessage.textContent = "State name is required";
      stateInput.classList.add("error");
      return false;
    } else if (!stateRegex.test(state)) {
      stateErrorMessage.textContent = "Invalid state name format";
      stateInput.classList.add("error");
      return false;
    } else {
      stateErrorMessage.textContent = "";
      stateInput.classList.remove("error");
      return true;
    }
  }

  function validateCountry() {
    const country = countryInput.value.trim();
    const countryRegex = /^[A-Za-z\s]+$/;

    if (country === "") {
      countryErrorMessage.textContent = "Country name is required";
      countryInput.classList.add("error");
      return false;
    } else if (!countryRegex.test(country)) {
      countryErrorMessage.textContent = "Invalid country name format";
      countryInput.classList.add("error");
      return false;
    } else {
      countryErrorMessage.textContent = "";
      countryInput.classList.remove("error");
      return true;
    }
  }



  const form = document.getElementById("myForm");
  form.addEventListener("submit", function (event) {
    // Check all validations and store the results in an array
    const validations = [
      validateName(),
      validateNumber(),
      validateEmail(),
      validateFlat(),
      validateStreet(),
      validateCity(),
      validateState(),
      validateCountry(),
    ];

    // Check if any validation failed (i.e., false in the validations array)
    if (validations.includes(false)) {
      event.preventDefault(); // Prevent form submission
    }
  });
</script>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html> 
