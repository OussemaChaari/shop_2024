<?php
include '../config.php';

if (isset($_POST['add_category'])) {
    $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
    // Perform the insertion query
    $insertQuery = "INSERT INTO categories (name) VALUES ('$categoryName')";
    $result = mysqli_query($conn, $insertQuery);
    if ($result) {
        echo '<script>alert("Category \'' . $categoryName . '\' added successfully!");</script>';
    }
}
$selectQuery = "SELECT * FROM categories";
$result = mysqli_query($conn, $selectQuery);
if ($result) {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
if (isset($_POST['add_product'])) {
    // Collect form data
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription']);
    $productCategory = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $productQuantity = mysqli_real_escape_string($conn, $_POST['productQuantity']);
    $productPrice = mysqli_real_escape_string($conn, $_POST['productPrice']);

    // Handle image upload using uniqid() to generate a unique file name
    $targetDir = "uploads/";
    $targetFileName = uniqid() . '_' . basename($_FILES["productImage"]["name"]);
    $targetFile = $targetDir . $targetFileName;
    move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile);

    // Perform the insertion query
    $insertQuery = "INSERT INTO products (name, description, image, quantity, price, category_id) VALUES ('$productName', '$productDescription', '$targetFileName', '$productQuantity', '$productPrice', '$productCategory')";

    $result = mysqli_query($conn, $insertQuery);

    if ($result) {
        echo '<script>alert("Product \'' . $productName . '\' added successfully!");</script>';
    }
}
$selectQuery = "SELECT products.*, categories.name AS category_name
                FROM products
                INNER JOIN categories ON products.category_id = categories.id";

$result = mysqli_query($conn, $selectQuery);

if ($result) {
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// fetch all users 
$selectQuery = "SELECT * FROM user_info";
$result = mysqli_query($conn, $selectQuery);
if ($result) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// fetch all orders
$selectQuery = "SELECT orders.*, user_info.name
                FROM orders
                JOIN user_info ON orders.user_id = user_info.id";
$result = mysqli_query($conn, $selectQuery);
if ($result) {
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function deleteCategory($categoryId)
{
    global $conn; // Assuming $conn is your database connection variable

    // Sanitize the input to prevent SQL injection
    $categoryId = mysqli_real_escape_string($conn, $categoryId);

    // Prepare and execute the SQL query
    $deleteQuery = "DELETE FROM categories WHERE id = '$categoryId'";
    $result = mysqli_query($conn, $deleteQuery);

    // Check if the deletion was successful
    if ($result) {
        echo '<script>alert("Category deleted successfully!");</script>';
    } else {
        echo '<script>alert("Failed to delete category!");</script>';
    }
}

// Check if the delete request is sent
if (isset($_GET['delete_category']) && isset($_GET['id'])) {
    $categoryIdToDelete = $_GET['id'];

    // Call the deleteCategory function
    deleteCategory($categoryIdToDelete);

    // Redirect to the page after deletion
    header("Location: dashboard.php");
    exit();
}
function deleteProduct($productId)
{
    global $conn; // Assuming $conn is your database connection variable

    // Sanitize the input to prevent SQL injection
    $productId = mysqli_real_escape_string($conn, $productId);

    // Prepare and execute the SQL query
    $deleteQuery = "DELETE FROM products WHERE id = '$productId'";
    $result = mysqli_query($conn, $deleteQuery);

    // Check if the deletion was successful
    if ($result) {
        echo '<script>alert("Product deleted successfully!");</script>';
    } else {
        echo '<script>alert("Failed to delete product!");</script>';
    }
}

// Check if the delete request is sent for products
if (isset($_GET['delete_product']) && isset($_GET['id'])) {
    $productIdToDelete = $_GET['id'];

    // Call the deleteProduct function
    deleteProduct($productIdToDelete);

    // Redirect to the page after deletion
    header("Location: dashboard.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>dashboard admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body style="overflow-x: hidden;">
    <?php include 'includes/header.php' ?>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php' ?>
        <div class="dashboard-content">
            <div id="section2" class="tab-content">
                <div class="displayFlexS">
                    <h2>List of categories</h2>
                    <button class="AddBtn" onclick="openModal('categoryModal')">Add catégorie</button>
                    <div id="categoryModal" class="modal">
                        <div class="modal-content">
                            <form action="" method="post">
                                <input type="text" class="inputBox" placeholder="Enter Category Name" id="categoryName"
                                    name="categoryName" required>
                                <br>
                                <div class="flexEnd">
                                    <button type="button" class="btn btnClose"
                                        onclick="closeModal('categoryModal')">Close</button>
                                    <button type="submit" name="add_category" class="btn btnSave">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php if (empty($categories)) {
                    echo '<div class="empty-table">There are no categories available.</div>';
                } else { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($categories as $categorie) {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $categorie['id'] ?>
                                    </td>
                                    <td>
                                        <?php echo $categorie['name'] ?>
                                    </td>
                                    <td>
                                        <a href="dashboard.php?delete_category&id=<?= $categorie['id']; ?>"
                                            onclick="confirmDelete(<?php echo $categorie['id']; ?>)"><i
                                                class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                            } ?>

                        </tbody>
                    </table>
                <?php }
                ?>
            </div>

            <div id="section3" class="tab-content">
                <div class="displayFlexS">
                    <h2>List of products</h2>
                    <button class="AddBtn" onclick="openModal('productsModal')">Add Product</button>
                    <div id="productsModal" class="modal">
                        <div class="modal-content">
                            <form action="" method="post" enctype="multipart/form-data"
                                style="padding:15px;margin-top:60px;">
                                <input type="text" class="inputBox" placeholder="Enter Product Name" id="productName"
                                    name="productName" required>
                                <br>
                                <textarea id="productDescription" class="inputBox"
                                    placeholder="Enter Product Description" name="productDescription"
                                    required></textarea>
                                <br>
                                <input type="file" id="productImage" class="inputBox" name="productImage"
                                    accept="image/*" required>
                                <br>
                                <select id="productCategory" class="inputBox" name="productCategory" required>
                                    <option value="">Choose Category</option>
                                    <?php
                                    // Loop through categories to generate options dynamically
                                    foreach ($categories as $category) {
                                        echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                                    }
                                    ?>
                                    <!-- Add more options as needed -->
                                </select>
                                <br>
                                <input type="number" class="inputBox" placeholder="Enter Quantity" id="productQuantity"
                                    name="productQuantity" required>
                                <br>
                                <input type="number" class="inputBox" placeholder="Enter Price" id="productPrice"
                                    name="productPrice" step="0.01" required>
                                <br>
                                <div class="flexEnd">
                                    <button type="button" class="btn btnClose"
                                        onclick="closeModal('productsModal')">Close</button>

                                    <button type="submit" class="btn btnSave" name="add_product">Add Product</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
                <?php if (empty($products)) {
                    echo '<div class="empty-table">There are no products available.</div>';
                } else { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Catgeory Name</th>
                                <th>Quantity</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($products as $product) {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $product['name'] ?>
                                    </td>
                                    <td><img src="uploads/<?= $product['image']; ?>" style="width:50px;height:50px;"
                                            alt="<?php echo $product['name'] ?>"></td>
                                    <td>
                                        <?php echo $product['category_name']; ?>
                                    </td>
                                    <td>
                                        <?= $product['quantity']; ?>
                                    </td>
                                    <td>
                                        <a href="dashboard.php?delete_product&id=<?= $product['id']; ?>"
                                            onclick="confirmDeleteProduct(<?php echo $product['id']; ?>)"><i
                                                class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
            <div id="section4" class="tab-content">
                <h2>Csutomers</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <tr>
                                <td>
                                    <?php echo $user['id']; ?>
                                </td>
                                <td>
                                    <?php echo $user['name']; ?>
                                </td>
                                <td>
                                    <?php echo $user['email']; ?>
                                </td>

                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="section5" class="tab-content">
                <h2>Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Total</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Show Order Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($orders as $order) {
                            ?>
                            <tr>
                                <td>
                                    <?php echo $order['order_id']; ?>
                                </td>
                                <td>
                                    <?php echo intval($order['total']); ?>
                                </td>
                                <td>
                                    <?php echo $order['name']; ?>
                                </td>
                                <td>
                                    <?php echo $order['date']; ?>
                                </td>
                                <td>
                                    <a href="fpdf/page_details.php?order_id=<?php echo $order['order_id']; ?>"
                                        class="btn btnSave"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Obtenez les liens du menu
            var menuLinks = document.querySelectorAll('.dashboard-nav a');

            // Obtenez les contenus de chaque section
            var sections = document.querySelectorAll('.tab-content');

            // Ajoutez un gestionnaire d'événements à chaque lien du menu
            menuLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();

                    // Masquez tous les contenus de section
                    sections.forEach(function (section) {
                        section.style.display = 'none';
                    });

                    // Obtenez l'ID de la section correspondant au lien cliqué
                    var targetId = link.getAttribute('href').substring(1);

                    // Affichez le contenu de la section correspondante
                    document.getElementById(targetId).style.display = 'block';
                });
            });
        });
        // side bar toggle
        let menuicn = document.querySelector(".menuicn");
        let nav = document.querySelector(".dashboard-nav");
        let menuItems = document.querySelectorAll(".menu_item");

        menuicn.addEventListener("click", () => {
            nav.classList.toggle("navclose");

            menuItems.forEach((item) => {
                item.classList.toggle("displayNone");
            });
        });
        // modal 
        function openModal(modalId) {
            var modal = document.getElementById(modalId);

            if (modal) {
                modal.style.display = "flex";
                document.body.classList.add("modal-open");
            }
        }

        function closeModal(modalId) {
            var modal = document.getElementById(modalId);

            if (modal) {
                modal.style.display = "none";
                document.body.classList.remove("modal-open");
            }
        }
        function confirmDelete(categoryId) {
            var confirmDelete = confirm("Are you sure you want to delete this category?");
            if (confirmDelete) {
                window.location.href = 'dashboard.php?delete_category&id=' + categoryId;
            }
        }
        function confirmDeleteProduct(productId) {
            var confirmDeleteProduct = confirm("Are you sure you want to delete this product?");
            if (confirmDeleteProduct) {
                window.location.href = 'dashboard.php?delete_product&id=' + productId;
            }
        }
    </script>
</body>

</html>