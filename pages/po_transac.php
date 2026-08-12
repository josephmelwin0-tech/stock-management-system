<?php
include'../includes/connection.php';
session_start();
$emp = isset($_SESSION['FIRST_NAME']) ? $_SESSION['FIRST_NAME'] . ' ' . $_SESSION['LAST_NAME'] : 'Manager';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
switch ($action) {
    case 'add':
        $po_number = mysqli_real_escape_string($db, $_POST['po_number']);
        $supplier_id = (int)$_POST['supplier'];
        $product_code = mysqli_real_escape_string($db, $_POST['product_code']);
        $qty = (int)$_POST['quantity'];
        $price = (float)$_POST['price'];
        $total_amount = $qty * $price;
        $today = date("Y-m-d");

        // Get Product Name
        $p_query = "SELECT NAME FROM product WHERE PRODUCT_CODE = '{$product_code}' LIMIT 1";
        $p_res = mysqli_query($db, $p_query);
        $p_name = "Purchased Product";
        if ($p_res && mysqli_num_rows($p_res) > 0) {
            $p_row = mysqli_fetch_assoc($p_res);
            $p_name = $p_row['NAME'];
        }

        // Insert into purchase_orders
        $po_query = "INSERT INTO purchase_orders (po_number, supplier_id, total_amount, status, created_at) 
                     VALUES ('{$po_number}', {$supplier_id}, {$total_amount}, 'PENDING', '{$today}')";
        mysqli_query($db, $po_query) or die(mysqli_error($db));
        $po_id = mysqli_insert_id($db);

        // Insert into purchase_order_details
        $pod_query = "INSERT INTO purchase_order_details (po_id, product_code, product_name, quantity_ordered, price) 
                      VALUES ({$po_id}, '{$product_code}', '{$p_name}', {$qty}, {$price})";
        mysqli_query($db, $pod_query) or die(mysqli_error($db));

        echo '<script type="text/javascript">alert("Purchase Order successfully created."); window.location = "purchase_orders.php";</script>';
        break;

    case 'receive':
        $po_id = (int)$_GET['po_id'];

        // Get PO Details
        $po_query = "SELECT po.supplier_id, pod.product_code, pod.product_name, pod.quantity_ordered, pod.price 
                     FROM purchase_orders po 
                     JOIN purchase_order_details pod ON po.po_id = pod.po_id 
                     WHERE po.po_id = {$po_id} LIMIT 1";
        $po_res = mysqli_query($db, $po_query) or die(mysqli_error($db));
        
        if ($po_res && mysqli_num_rows($po_res) > 0) {
            $po_data = mysqli_fetch_assoc($po_res);
            $supplier_id = $po_data['supplier_id'];
            $product_code = $po_data['product_code'];
            $p_name = $po_data['product_name'];
            $qty = $po_data['quantity_ordered'];
            $price = $po_data['price']; // This is our cost price from supplier
            $today = date("Y-m-d");

            // Update PO Status
            $update_po = "UPDATE purchase_orders SET status = 'RECEIVED' WHERE po_id = {$po_id}";
            mysqli_query($db, $update_po) or die(mysqli_error($db));

            // Fetch template product details to duplicate
            $tmpl_query = "SELECT DESCRIPTION, CATEGORY_ID, LOCATION, PRICE FROM product WHERE PRODUCT_CODE = '{$product_code}' LIMIT 1";
            $tmpl_res = mysqli_query($db, $tmpl_query);
            $desc = "Received Stock";
            $category_id = 9; // Others default
            $location = "Warehouse";
            $sale_price = $price * 1.30; // Auto markup price by 30% if not found
            
            if ($tmpl_res && mysqli_num_rows($tmpl_res) > 0) {
                $tmpl_row = mysqli_fetch_assoc($tmpl_res);
                $desc = mysqli_real_escape_string($db, $tmpl_row['DESCRIPTION']);
                $category_id = (int)$tmpl_row['CATEGORY_ID'];
                $location = mysqli_real_escape_string($db, $tmpl_row['LOCATION']);
                $sale_price = (float)$tmpl_row['PRICE'];
            }

            // Insert duplicate rows to increment stock counts
            for ($i = 0; $i < $qty; $i++) {
                $ins_product = "INSERT INTO product (PRODUCT_CODE, NAME, DESCRIPTION, QTY_STOCK, ON_HAND, PRICE, CATEGORY_ID, SUPPLIER_ID, DATE_STOCK_IN, REORDER_THRESHOLD, UNIT_COST, SALE_PRICE, LOCATION) 
                                VALUES ('{$product_code}', '{$p_name}', '{$desc}', 1, 1, {$sale_price}, {$category_id}, {$supplier_id}, '{$today}', 5, {$price}, {$sale_price}, '{$location}')";
                mysqli_query($db, $ins_product) or die(mysqli_error($db));
            }

            // Log to Stock History
            $history_query = "INSERT INTO stock_history (product_code, product_name, quantity, action_type, user) 
                              VALUES ('{$product_code}', '{$p_name}', {$qty}, 'RECEIVE', '{$emp}')";
            mysqli_query($db, $history_query) or die(mysqli_error($db));

            echo '<script type="text/javascript">alert("Stock successfully received and added to inventory."); window.location = "purchase_orders.php";</script>';
        } else {
            echo '<script type="text/javascript">alert("Error: Purchase Order details not found."); window.location = "purchase_orders.php";</script>';
        }
        break;
}
?>
