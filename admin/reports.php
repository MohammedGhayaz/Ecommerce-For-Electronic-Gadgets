<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Managing Reports</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      @media print {
         .buttons {
            display: none;
         }
      }
   </style>
</head>

<body>
   <?php include '../components/admin_header.php'; ?>
   <section class="reports">
      <h1 class="heading">Reports</h1>

      <?php
      // Function to get the list of sold products with their total quantities and prices
      // ... Your existing code ...
      
      function getSoldProducts($conn)
{
    $sql = "SELECT 
                o.id as order_id,
                o.placed_on as order_date,
                o.user_id,
                p.name as product_name,
                p.id as product_id,
                SUM(p.price) as product_price,
                SUM(SUBSTRING_INDEX(SUBSTRING_INDEX(o.total_products, ' x ', -1), ')', 1)) as quantity,
                SUM(p.price) * SUM(SUBSTRING_INDEX(SUBSTRING_INDEX(o.total_products, ' x ', -1), ')', 1)) as total_price,
                o.total_products
            FROM 
                orders o
            INNER JOIN 
                products p ON SUBSTRING_INDEX(o.total_products, ' (', 1) = p.name
            WHERE 
                o.payment_status = 'completed'
            GROUP BY 
                o.id, p.id"; // Group the orders by order ID and product ID

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as an associative array
    return $products;
}

      

      
    

      $soldProducts = getSoldProducts($conn);
      ?>
<div id="table-container">
   <table border="1">
      <tr>
         <th class="thead">Sl. No</th>
         <th class="thead">Order ID</th>
         <th class="thead">Order Date</th>
         <th class="thead">Product Name</th>
         <th class="thead">Price</th>
         <th class="thead">Quantity</th>
         <th class="thead">Total Price</th>
      </tr>
      <?php
      $slNo = 1;
      if (!empty($soldProducts)) {
         foreach ($soldProducts as $product) {
            echo "<tr>";
            echo "<td class='tdata'>" . $slNo . "</td>";
            echo "<td class='tdata'>" . $product['order_id'] . "</td>";
            echo "<td class='tdata'>" . $product['order_date'] . "</td>";
            echo "<td class='tdata'>" . $product['product_name'] . "</td>";

                  $quantity = 1;
                  preg_match('/\((\d+)\s*x\s*(\d+)\)/', $product['total_products'], $matches);
                  if (count($matches) >= 3) {
                     $quantity = intval($matches[2]);
                  }

                  echo "<td class='tdata'>" . $product['product_price'] . "</td>";
                  echo "<td class='tdata'>" . $quantity . "</td>";
                  echo "<td class='tdata'>" . $product['total_price'] . "</td>";
                  echo "</tr>";
                  $slNo++;
               }
            } else {
               echo "<tr><td colspan='7'>No products sold yet.</td></tr>";
            }
            ?>
         </table>
      </div>

      <div class="buttons">
         <button class="btn1" onclick="window.print()">Print</button>
         <button class="btn1" onclick="exportToCSV()">Export</button>
         <button class="btn1" onclick="createChart()">Chart</button>
      </div>

      <div class="chart-container" style="display: none;">
         <canvas id="orderChart"></canvas>
      </div>
   </section>

   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
      function getOrderDataFromTable() {
         const tableRows = document.querySelectorAll("#table-container tr");
         const data = [];

         // Skip the first row (header)
         for (let i = 1; i < tableRows.length; i++) {
            const row = tableRows[i];
            const cells = row.querySelectorAll("td");

            const date = cells[2].textContent.trim();
            const amount = parseFloat(cells[6].textContent.trim());

            data.push({ order_date: date, order_amount: amount });
         }

         return data;
      }

      function createChart() {
         const orderData = getOrderDataFromTable();
         const ctx = document.getElementById('orderChart').getContext('2d');
         const dates = orderData.map(item => item.order_date);
         const amounts = orderData.map(item => item.order_amount);

         new Chart(ctx, {
            type: 'bar',
            data: {
               labels: dates,
               datasets: [{
                  label: 'Total Order Amount',
                  data: amounts,
                  backgroundColor: 'rgba(75, 192, 192, 0.6)',
                  borderColor: 'rgba(75, 192, 192, 1)',
                  borderWidth: 1
               }]
            },
            options: {
               scales: {
                  x: {
                     stacked: true
                  },
                  y: {
                     beginAtZero: true
                  }
               }
            }
         });

         // Display the chart container
         const chartContainer = document.querySelector('.chart-container');
         chartContainer.style.display = 'block';
      }

      function exportToCSV() {
         // ... (same exportToCSV function as before)
      }
   </script>

   <script src="../js/admin_script.js"></script>
</body>

</html>





