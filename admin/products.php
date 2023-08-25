<?php
// Assuming this code is in a file named "register_admin.php"

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

$message = array(); // Initialize $message as an array

if (isset($_POST['add_product'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   $image_02 = $_FILES['image_02']['name'];
   $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = '../uploaded_img/' . $image_02;

   $image_03 = $_FILES['image_03']['name'];
   $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = '../uploaded_img/' . $image_03;

   // Additional field for product quantity
   $quantity = $_POST['quantity'];

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
       $message[] = 'Product name already exists!';
   } else {
       // Insert the product into the products table
       $insert_products = $conn->prepare("INSERT INTO `products` (name, details, price, image_01, image_02, image_03) VALUES (?, ?, ?, ?, ?, ?)");
       $insert_products->execute([$name, $details, $price, $image_01, $image_02, $image_03]);

       // Get the last inserted product_id
       $product_id = $conn->lastInsertId();

       if ($insert_products) {
           // Insert the product into the inventory table with the given quantity
           $insert_inventory = $conn->prepare("INSERT INTO `inventory` (product_id, quantity) VALUES (?, ?)");
           $insert_inventory->execute([$product_id, $quantity]);

           if ($image_size_01 > 2000000 || $image_size_02 > 2000000 || $image_size_03 > 2000000) {
               $message[] = 'Image size is too large!';
           } else {
               move_uploaded_file($image_tmp_name_01, $image_folder_01);
               move_uploaded_file($image_tmp_name_02, $image_folder_02);
               move_uploaded_file($image_tmp_name_03, $image_folder_03);
               $message[] = 'New product added!';
           }
       }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // Fetch the product details from the products table
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);

   // Delete the product images from the server
   unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_03']);

   // Delete the product from the products table
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);

   // Delete the product from the inventory table
   $delete_inventory = $conn->prepare("DELETE FROM `inventory` WHERE product_id = ?");
   $delete_inventory->execute([$delete_id]);

   // Delete associated entries from the cart and wishlist tables
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);

   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);

   header('location:products.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Managing Products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .error {
         color: white;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">

   <h1 class="heading">add product</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <span> Product Name (Required)</span>
            <input style="color:white;" type="text" class="box" required maxlength="100" placeholder="Enter Product Name" name="name" oninput="validateProductName(this);">
            <span class="error" id="productNameError"></span>
         </div>
         <div class="inputBox">
            <span> Product Price (Required)</span>
            <input style="color:white;" type="number" min="0" class="box" required max="9999999999" placeholder="Enter Product Price" onkeypress="if(this.value.length == 10) return false;" name="price">
         </div>
         <div class="inputBox">
            <span>Image-1 (Required)</span>
            <input style="color:white;" type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
         </div>
         <div class="inputBox">
            <span>image 02 (required)</span>
            <input style="color:white" type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
         </div>
         <div class="inputBox">
            <span>image 03 (required)</span>
            <input style="color:white" type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
         </div>
         <div class="inputBox">
            <span>Product Description (Required)</span>
            <textarea style="color:white;" name="details" placeholder="Enter Product Description" class="box" required maxlength="500" cols="30" rows="10"></textarea>
         </div>
         <div class="inputBox">
            <span>Product Quantity (Required)</span>
            <input style="color:white;" type="number" min="0" class="box" required max="100" placeholder="Enter Product Quantity" onkeypress="if(this.value.length == 3) return false;" name="quantity">
            <span class="error" id="quantityError"></span>
         </div>
      </div>
      
      <input type="submit" value="add product" class="btn" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="heading">Products Added</h1>

   <div class="box-container">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products->execute();
         if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) { 
      ?>
      <div class="box">
         <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="price">&#8377;<span><?= $fetch_products['price']; ?></span>/-</div>
         <div class="details"><span><?= $fetch_products['details']; ?></span></div>
         <div class="flex-btn">
            <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
         </div>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">no products added yet!</p>';
         }
      ?>
   </div>

</section>

<script>
   function validateForm() {
      // ... Your existing validation code ...

      // Additional validation for product name and quantity
      var productName = document.getElementsByName("name")[0].value;
      var quantity = document.getElementsByName("quantity")[0].value;

      var productNameError = document.getElementById("productNameError");
      var quantityError = document.getElementById("quantityError");

      if (productName.match(/^\d/)) {
         productNameError.innerText = "Product name should not start with a digit.";
         return false;
      } else {
         productNameError.innerText = "";
      }

      if (quantity > 100) {
         quantityError.innerText = "Product quantity must not exceed 100.";
         return false;
      } else {
         quantityError.innerText = "";
      }

      // Form is valid, allow submission
      return true;
   }

   function validateProductName(input) {
      var productName = input.value;
      var productNameError = document.getElementById("productNameError");

      if (productName.match(/^\d/)) {
         productNameError.innerText = "Product name should not start with a digit.";
      } else {
         productNameError.innerText = "";
      }
   }
</script>
<script src="../js/admin_script.js"></script>
</body>
</html>
