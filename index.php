<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>store</title>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
   <?php include 'alert.php' ?>
   <?php include 'nav.php' ?>
   <div class="container">
      <div class="products" style="margin-top:20px;">
         <div class="box-container">
            <?php
            $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            if (mysqli_num_rows($select_product) > 0) {
               while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                  ?>
                  <form method="post" class="box" action="cart.php">
                     <img src="admin/uploads/<?php echo $fetch_product['image']; ?>" alt="">
                     <div class="name">
                        <?php echo $fetch_product['name']; ?>
                     </div>
                     <div class="price">$
                        <?php echo intval($fetch_product['price']); ?>/-
                     </div>
                     <input type="number" min="1" name="product_quantity" value="1">
                     <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                     <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                     <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                     <input type="hidden" name="quantity_principal" value="<?= $fetch_product['quantity']; ?>">
                     <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                     <?php if($fetch_product['quantity']==0){?>
                        <div style="padding: 10px;background-color: #f44336;color: #fff;margin-bottom: 10px;margin-top: 10px;border-radius:5px;">
                           This product is Out of Stock
                        </div>
                    <?php }else{ ?>
                     <input type="submit" value="add to cart" name="add_to_cart" class="btn">
                    <?php } ?>
                  </form>
                  <?php
               };
            };
            ?>
         </div>
      </div>
   </div>
</body>
</html>