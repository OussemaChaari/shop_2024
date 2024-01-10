<?php
include 'config.php';
if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];
    $quantity_principal = $_POST['quantity_principal'];
    $user_id = $_POST['user_id'];


    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
    
    if (mysqli_num_rows($select_cart) > 0) {
        $messageError[] = 'product already added to cart!';
    } else {
        if ($product_quantity > $quantity_principal) {
            $messageError[] = 'Quantity requested exceeds available stock!';
        } else {
            mysqli_query($conn, "INSERT INTO `cart` (user_id, name, price, image, quantity) VALUES ('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');            
            $messageSuccess[] = 'product added to cart!';
        }
    }
}
if (isset($_POST['update_cart'])) {
    $update_quantity = $_POST['cart_quantity'];
    $update_id = $_POST['cart_id'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
    $messageSuccess[] = 'cart quantity updated successfully!';
}
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
    header('location:cart.php');
}
if (isset($_GET['delete_all']) && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:cart.php');
}
if (isset($_GET['validate']) && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $grand_total = $_GET['grand_total'];
    // Créer une nouvelle commande
    $create_order_query = mysqli_query($conn, "INSERT INTO `orders` (total, user_id,date) VALUES ('$grand_total', '$user_id',CURRENT_TIMESTAMP)") or die('Erreur lors de la création de la commande');

    // Récupérer l'ID de la commande nouvellement créée
    $order_id = mysqli_insert_id($conn);

    // Mettre à jour l'ID de la commande dans le tableau 'cart' pour les articles associés au panier de l'utilisateur
    $update_cart_query = mysqli_query($conn, "UPDATE `cart` SET order_id = '$order_id' WHERE user_id = '$user_id' AND order_id IS NULL") or die('Erreur lors de la mise à jour du panier');

     // Fetch cart items
     $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    
     // Loop through cart items
     while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
         $product_name = $fetch_cart['name'];
         $product_quantity_in_cart = $fetch_cart['quantity'];
         
         // Fetch current product quantity from the database
         $product_query = mysqli_query($conn, "SELECT * FROM `products` WHERE name = '$product_name'") or die('query failed');
         $fetch_product = mysqli_fetch_assoc($product_query);
         $current_product_quantity = $fetch_product['quantity'];
         
         // Update product quantity in the database
         $new_product_quantity = $current_product_quantity - $product_quantity_in_cart;
         mysqli_query($conn, "UPDATE `products` SET quantity = '$new_product_quantity' WHERE name = '$product_name'") or die('query failed');
     }
    // Effacer le panier ou effectuer d'autres actions nécessaires
    // $clear_cart_query = mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id' AND order_id = '$order_id'") or die('Erreur lors de la suppression du panier');

    $messageSuccess[] = 'Validation réussie !';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cart</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php include 'alert.php' ?>
    <?php include 'nav.php' ?>

    <div class="container">
        <a href="index.php" class="btn">Back to store</a>
        <div class="shopping-cart">
            <table>
                <thead>
                    <th>image</th>
                    <th>name</th>
                    <th>price</th>
                    <th>quantity</th>
                    <th>total price</th>
                    <th>action</th>
                </thead>
                <tbody>
                    <?php
                    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                    $grand_total = 0;
                    if (mysqli_num_rows($cart_query) > 0) {
                        while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
                            ?>
                            <tr>
                                <td><img src="admin/uploads/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                                <td>
                                    <?php echo $fetch_cart['name']; ?>
                                </td>
                                <td>$
                                    <?php echo intval($fetch_cart['price']); ?>/-
                                </td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                                        <input type="number" min="1" name="cart_quantity"
                                            value="<?php echo $fetch_cart['quantity']; ?>">
                                        <input type="submit" name="update_cart" value="update" class="option-btn">
                                    </form>
                                </td>
                                <td>$
                                    <?php echo $sub_total = (intval($fetch_cart['price']) * $fetch_cart['quantity']); ?>/-
                                </td>
                                <td><a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn"
                                        onclick="return confirm('remove item from cart?');">remove</a></td>
                            </tr>
                            <?php
                            $grand_total += $sub_total;
                        }
                    } else {
                        echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">no item added</td></tr>';
                    }
                    ?>
                    <tr class="table-bottom">
                        <td colspan="4">grand total :</td>
                        <td>$
                            <?php echo $grand_total; ?>/-
                        </td>
                        <td><a href='cart.php?delete_all&user_id=<?= $user_id; ?>' onclick="return confirm('delete all from cart?');"
                                class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">delete all</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="cart-btn">
                <a href="cart.php?validate&user_id=<?= $user_id; ?>&grand_total=<?= $grand_total; ?>" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Checkout</a>
            </div>

        </div>
    </div>
</body>

</html>