<?php 
include_once('./api.php');
$conn =  connectDB();
  function targetData($conn, $query){
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

$orderListQuery = "SELECT P.name productName, U.name userName,  SUM(O.purchase_quantity) sumQuantity, SUM(P.price) sumPrice, (P.price * SUM(O.purchase_quantity)) totalPrince FROM users as U JOIN orders AS O ON O.user_phone = U.phone JOIN products AS P ON P.code = O.product_code GROUP BY U.name ORDER BY (P.price  * SUM(O.purchase_quantity)) DESC";

$totalListQuery = "SELECT   SUM(O.purchase_quantity) AS sumQuantity, SUM(P.price) AS sumPrice, SUM(P.price *  O.purchase_quantity) AS totalPrince FROM users as U JOIN orders AS O ON O.user_phone = U.phone JOIN products AS P ON P.code = O.product_code";

$orderList = targetData($conn, $orderListQuery);
$totalList = targetData($conn, $totalListQuery);

//print_r($orderList);
?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/style.css">



    <title>Task 1 | PHP</title>
</head>

<body>


    <div class="container">
        <h2>Task 1: Design & create a database based on data and show a report.</h2>

        <form method="POST">
            <button class="btn  " type="submit">Generate Report</button>
        </form>


        <h4>Report (Top Customers by product):</h4>
        <div class="tableContainer">
            <?php if(!empty($orderList)): ?>

            <table>
                <thead>
                    <tr>
                        <th scope="col">SL</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Customer Name </th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($orderList as $key => $order): ?>

                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $order['productName'] ?></td>
                        <td><?= $order['userName'] ?></td>
                        <td><?= $order['sumQuantity'] ?></td>
                        <td><?= $order['sumPrice'] ?></td>
                        <td><?= $order['totalPrince'] ?></td>

                    </tr>


                    <?php endforeach; ?>


                <tfoot>

                    <td colspan="3">Total</td>

                    <td><?= $totalList[0]['sumQuantity'] ?></td>
                    <td><?= $totalList[0]['sumPrice'] ?></td>
                    <td><?= $totalList[0]['totalPrince'] ?></td>
                </tfoot>


                </tbody>
            </table>
            <?php endif; ?>

            <?php if(empty($orderList)): ?>
            <h5>No orders found ! Please generate report :)</h5>

        </div>
        <?php endif; ?>
        <div>
        </div>


    </div>

</body>

</html>