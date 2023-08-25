<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id))
{
   header('location:admin_login.php');
};

// Include your database connection file here
// For example: require_once('db_connection.php');

// Function to fetch inventory information
function getInventoryInformation()
{
    global $conn;
    // Implement your SQL query to fetch inventory information from the database
    $sql = "SELECT p.id, p.name, p.price, p.details, p.image_01, i.quantity
            FROM products p
            INNER JOIN inventory i ON p.id = i.product_id";
    $inventoryInfo = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $inventoryInfo;
}

// Example usage:
$inventoryData = getInventoryInformation();
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

</head>
<body>
<?php include '../components/admin_header.php'; ?>
<section class="inventory">

    <h1 class="heading">Inventory Information</h1>
        <table>
            <tr>
                <th class="thead">Product ID</th>
                <th class="thead">Product Name</th>
                <th class="thead">Price</th>
                <th class="thead">Details</th>
                <th class="thead">Quantity</th>
            </tr>
            <?php
        foreach ($inventoryData as $item) {
            $shortDetails = implode(' ', array_slice(explode(' ', $item['details']), 0,50));
            ?>
            <tr>
                <td class="tdata"><?php echo $item['id']; ?></td>
                <td class="tdata"><?php echo $item['name']; ?></td>
                <td class="tdata">&#8377;<?php echo $item['price']; ?></td>
                <td class="tdata"><?php echo $shortDetails; ?></td>
                <td class="tdata"><?php echo $item['quantity']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</section>
<script src="../js/admin_script.js"></script>
</body>
</html>


