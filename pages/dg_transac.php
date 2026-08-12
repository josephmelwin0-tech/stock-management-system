<?php
include'../includes/connection.php';
session_start();
$emp = isset($_SESSION['FIRST_NAME']) ? $_SESSION['FIRST_NAME'] . ' ' . $_SESSION['LAST_NAME'] : 'Manager';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
switch ($action) {
    case 'add':
        $product_code = mysqli_real_escape_string($db, $_POST['product_code']);
        $qty = (int)$_POST['quantity'];
        $notes = mysqli_real_escape_string($db, $_POST['notes']);
        $today = date("Y-m-d H:i a");

        // Get Product Name and Current Stock Count
        $p_query = "SELECT NAME, COUNT(PRODUCT_ID) AS in_stock FROM product WHERE PRODUCT_CODE = '{$product_code}' GROUP BY PRODUCT_CODE LIMIT 1";
        $p_res = mysqli_query($db, $p_query);
        
        if ($p_res && mysqli_num_rows($p_res) > 0) {
            $p_row = mysqli_fetch_assoc($p_res);
            $p_name = $p_row['NAME'];
            $in_stock = (int)$p_row['in_stock'];

            // Validation: Cannot write off more stock than currently on hand
            if ($qty > $in_stock) {
                echo '<script type="text/javascript">alert("Error: Cannot report ' . $qty . ' damaged items. Only ' . $in_stock . ' items exist in inventory."); window.location = "damaged_goods.php";</script>';
                exit;
            }

            // Deduct from product table (delete up to $qty rows)
            $delete_query = "DELETE FROM product WHERE PRODUCT_CODE = '{$product_code}' LIMIT {$qty}";
            mysqli_query($db, $delete_query) or die(mysqli_error($db));

            // Insert into damaged_goods
            $dg_query = "INSERT INTO damaged_goods (product_code, product_name, quantity, notes, reported_by) 
                         VALUES ('{$product_code}', '{$p_name}', {$qty}, '{$notes}', '{$emp}')";
            mysqli_query($db, $dg_query) or die(mysqli_error($db));

            // Log to Stock History (negative quantity for reduction)
            $history_query = "INSERT INTO stock_history (product_code, product_name, quantity, action_type, user) 
                              VALUES ('{$product_code}', '{$p_name}', -{$qty}, 'DAMAGE', '{$emp}')";
            mysqli_query($db, $history_query) or die(mysqli_error($db));

            echo '<script type="text/javascript">alert("Damaged goods report recorded successfully. Stock updated."); window.location = "damaged_goods.php";</script>';
        } else {
            echo '<script type="text/javascript">alert("Error: Product details not found."); window.location = "damaged_goods.php";</script>';
        }
        break;
}
?>
