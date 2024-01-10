<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:login.php');
};
if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location:login.php');
};
?>
<div style="display: flex;align-items: center;justify-content: space-between;    padding: 10px 20px;background: black;">
<img src="logo/logo.png" alt="" style="width:50px;">
<div class="user-profile" style="display: flex;align-items: center;">
    <?php
    $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
    };
    ?>
    <p style="margin-right:20px;color:white;"> Welcome <span><?php echo $fetch_user['name']; ?></span> </p>
    <?php
    $cart_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM `cart` WHERE user_id = '$user_id'");
    $cart_count_result = mysqli_fetch_assoc($cart_count_query);
    $cart_item_count = $cart_count_result['count']; ?>
    <div class="flex">
        <a style="background: #4CAF50;color: white;padding: 10px;border-radius: 5px;margin-right:20px;" href="cart.php">
            <?php echo $cart_item_count; ?>
            <i class="fa fa-shopping-cart"></i>
        </a>
        <a href="index.php?logout=<?php echo $user_id; ?>"
            onclick="return confirm('are your sure you want to logout?');" class="delete-btn">logout</a>
    </div>
</div>
</div>
