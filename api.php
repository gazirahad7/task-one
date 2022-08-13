<?php

function connectDB(){
try {
  $conn = new PDO("mysql:host=localhost;dbname=task_1", 'root', '');
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
} 
return $conn;
}
$conn = connectDB();

   // function for query builder
   function selectData($conn, $tableName){
    $stmt = $conn->prepare("SELECT * FROM $tableName");
    $stmt->execute();
    return $stmt->fetchAll();
    
}

// print the data with per fun
function printData($var){
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}


  if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $usersTable = selectData($conn, 'users');
  $ordersTable = selectData($conn, 'orders');
   $productsTable = selectData($conn, 'products');
  
  // given API
  $url = 'https://raw.githubusercontent.com/Bit-Code-Technologies/mockapi/main/purchase.json';
  $data = file_get_contents($url);
  $getApiData = json_decode($data, true);


  // for users table

   $apiUsers =  array_map(function($el){
     $user['name'] = $el['name'];
     $user['phone'] = $el['user_phone'];

     return $user;
}, $getApiData);

$apiUsers = array_map("unserialize", array_unique(array_map("serialize", $apiUsers)));

$uniqueUsers = array_filter($apiUsers , function($user) use ($usersTable){

  foreach($usersTable as $data){
    if($data['name'] == $user['name'] && $data['phone'] == $user['phone']){
      return false;
    }
  }
  return true;
});


if(count($uniqueUsers) > 0){
  foreach($uniqueUsers as $user){
    $stmt = $conn->prepare("INSERT INTO users (name, phone) VALUES (:name, :phone)");
    $stmt->bindParam(':name', $user['name']);
    $stmt->bindParam(':phone', $user['phone']);
    $stmt->execute();
  }
}

//  for orders table

$ordersApi =  array_map(function($el){
  $order['order_no'] = $el['order_no'];
  $order['user_phone'] = $el['user_phone'];
  $order['product_code'] = $el['product_code'];
  $order['purchase_quantity'] = $el['purchase_quantity'];
  $order['created_at'] = $el['created_at'];

  return $order;
}, $getApiData);


$newOrder = array_filter($ordersApi, function($order) use ($ordersTable){

  foreach($ordersTable as $el){
    if($el['order_no'] == $order['order_no']){
      return false;
    }
  }

  return true;
  
});


if(count($newOrder) > 0){
  foreach($newOrder as $order){
    $stmt = $conn->prepare("INSERT INTO orders (order_no, user_phone, product_code, purchase_quantity, created_at) VALUES (:order_no, :user_phone, :product_code, :purchase_quantity, :created_at)");
    $stmt->bindParam(':order_no', $order['order_no']);
    $stmt->bindParam(':user_phone', $order['user_phone']);
    $stmt->bindParam(':product_code', $order['product_code']);
    $stmt->bindParam(':purchase_quantity', $order['purchase_quantity']);
    $stmt->bindParam(':created_at', $order['created_at']);
    $stmt->execute();
    
  }

  

}


// for products table

$existProducts = array_map(function($el){
  $product['code'] = $el['product_code'];
  $product['name'] = $el['product_name'];
  $product['price'] = $el['product_price'];
 
  return $product;
} , $getApiData);

$existProducts = array_map("unserialize", array_unique(array_map("serialize", $existProducts)));

$uniqueProducts = array_filter($existProducts, function($product) use ($productsTable){


  foreach($productsTable as $el){
    if($product['code'] == $el['code']){
      return false;
    }

  }
  return true;

    });

if(count($uniqueProducts) > 0){
  foreach($uniqueProducts as $product){
    $stmt = $conn->prepare("INSERT INTO products (code, name, price) VALUES (:code, :name, :price)");
    $stmt->bindParam(':code', $product['code']);
    $stmt->bindParam(':name', $product['name']);
    $stmt->bindParam(':price', $product['price']);
    $stmt->execute();
  }
 }
  header('Location: index.php');
  }
    
?>