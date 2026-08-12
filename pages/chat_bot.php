<?php
include'../includes/connection.php';
session_start();

if (!isset($_SESSION['MEMBER_ID'])) {
    echo json_encode(["response" => "Please log in to chat with the system assistant."]);
    exit;
}

$user_msg = isset($_POST['message']) ? trim($_POST['message']) : '';
if (empty($user_msg)) {
    echo json_encode(["response" => "Hello! How can I assist you with the stock system today? You can ask about inventory, sales, suppliers, or customer counts."]);
    exit;
}

$clean_msg = strtolower($user_msg);

// Helper function to check if any of the keywords exist in the clean message
function contains_any($msg, $keywords) {
    foreach ($keywords as $kw) {
        if (strpos($msg, $kw) !== false) {
            return true;
        }
    }
    return false;
}

// Helper function to check if all keywords exist in the clean message
function contains_all($msg, $keywords) {
    foreach ($keywords as $kw) {
        if (strpos($msg, $kw) === false) {
            return false;
        }
    }
    return true;
}

$response = "";

// 1. HELP / GREETINGS
if (contains_any($clean_msg, ['hello', 'hi', 'hey', 'greetings', 'help', 'what can you do', 'what can i ask'])) {
    $response = "Hi " . $_SESSION['FIRST_NAME'] . "! I am your Stock System Assistant. Here are some examples of what you can ask me:<br>
    <ul>
      <li><i>\"What items are low on stock?\"</i></li>
      <li><i>\"What is our total stock value?\"</i></li>
      <li><i>\"How many customers do we have?\"</i></li>
      <li><i>\"How much money did we make today?\"</i></li>
      <li><i>\"List our suppliers\"</i></li>
      <li><i>\"Is Lenovo in stock?\" (or search any product name)</i></li>
    </ul>";
}
// 2. TODAY'S SALES / REVENUE
elseif (contains_any($clean_msg, ['today']) && contains_any($clean_msg, ['sale', 'sales', 'money', 'make', 'made', 'earn', 'earnings', 'revenue', 'transaction', 'transactions'])) {
    $today_date = date("Y-m-d");
    $sql = "SELECT GRANDTOTAL FROM transaction WHERE DATE LIKE '$today_date%'";
    $res = mysqli_query($db, $sql);
    $total_sales = 0;
    $count = 0;
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $clean_total = str_replace(',', '', $row['GRANDTOTAL']);
            $total_sales += floatval($clean_total);
            $count++;
        }
    }
    $response = "Today, we processed <b>" . $count . " transactions</b> with a total sales revenue of <b>$ " . number_format($total_sales, 2) . "</b>.";
}
// 3. LOW STOCK ITEMS
elseif (contains_any($clean_msg, ['low stock', 'reorder', 'out of stock', 'shortage', 'low-stock']) || (contains_any($clean_msg, ['low', 'short']) && contains_any($clean_msg, ['stock', 'item', 'product']))) {
    $sql = "SELECT PRODUCT_CODE, NAME, COUNT(PRODUCT_ID) AS qty, MAX(REORDER_THRESHOLD) AS threshold 
            FROM product GROUP BY PRODUCT_CODE HAVING qty < threshold";
    $res = mysqli_query($db, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $response = "Yes, we have some items that need reordering:<br><ul>";
        while ($row = mysqli_fetch_assoc($res)) {
            $response .= "<li><b>{$row['NAME']}</b> (Code: {$row['PRODUCT_CODE']}) - Current Stock: {$row['qty']} (Reorder threshold is {$row['threshold']})</li>";
        }
        $response .= "</ul>";
    } else {
        $response = "Great news! All products are currently above their reorder thresholds.";
    }
}
// 4. TOTAL STOCK VALUE
elseif (contains_any($clean_msg, ['total value', 'stock value', 'inventory value', 'valuation', 'worth']) || (contains_any($clean_msg, ['total', 'all']) && contains_any($clean_msg, ['value', 'worth', 'valuation']))) {
    $sql = "SELECT SUM(COALESCE(NULLIF(SALE_PRICE, 0), PRICE)) AS total_val FROM product";
    $res = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($res);
    $val = floatval($row['total_val'] ?? 0);
    $response = "The total estimated valuation of all products currently on hand is <b>$ " . number_format($val, 2) . "</b>.";
}
// 5. CUSTOMER COUNT
elseif (contains_any($clean_msg, ['customer', 'customers', 'client', 'clients'])) {
    $sql = "SELECT COUNT(*) AS count FROM customer";
    $res = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($res);
    $count = intval($row['count'] ?? 0);
    
    $list_sql = "SELECT FIRST_NAME, LAST_NAME FROM customer LIMIT 5";
    $list_res = mysqli_query($db, $list_sql);
    $names = [];
    while ($r = mysqli_fetch_assoc($list_res)) {
        if ($r['FIRST_NAME']) {
            $names[] = $r['FIRST_NAME'] . " " . $r['LAST_NAME'];
        }
    }
    $response = "We have a total of <b>" . $count . " customers</b> in our records. Recent entries include: " . implode(", ", $names) . ".";
}
// 6. SUPPLIERS
elseif (contains_any($clean_msg, ['supplier', 'suppliers', 'supply', 'companies', 'provide'])) {
    $sql = "SELECT COMPANY_NAME FROM supplier";
    $res = mysqli_query($db, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $suppliers = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $suppliers[] = $row['COMPANY_NAME'];
        }
        $response = "We work with the following suppliers:<br><ul><li>" . implode("</li><li>", $suppliers) . "</li></ul>";
    } else {
        $response = "No suppliers are currently registered in the database.";
    }
}
// 7. SPECIFIC PRODUCT SEARCH (Fallback lookup)
else {
    // Strip standard filler words
    $keyword = preg_replace('/(is|how|many|in|stock|available|quantity|of|find|search|the|a|please|show|me|do|we|have|what|tell)/i', '', $clean_msg);
    $keyword = trim($keyword);
    
    if (strlen($keyword) > 1) {
        $safe_kw = mysqli_real_escape_string($db, $keyword);
        $sql = "SELECT NAME, PRODUCT_CODE, COUNT(PRODUCT_ID) AS qty, MAX(PRICE) AS price, MAX(LOCATION) AS loc 
                FROM product 
                WHERE NAME LIKE '%{$safe_kw}%' OR PRODUCT_CODE LIKE '%{$safe_kw}%'
                GROUP BY PRODUCT_CODE LIMIT 3";
        $res = mysqli_query($db, $sql);
        
        if ($res && mysqli_num_rows($res) > 0) {
            $response = "Here are the matching inventory products I found:<br><ul>";
            while ($row = mysqli_fetch_assoc($res)) {
                $response .= "<li><b>{$row['NAME']}</b> (Code: {$row['PRODUCT_CODE']})<br>
                             &nbsp;&nbsp;- Stock Count: <b>{$row['qty']}</b><br>
                             &nbsp;&nbsp;- Unit Price: $ " . number_format($row['price'], 2) . "<br>
                             &nbsp;&nbsp;- Location: <i>{$row['loc']}</i></li>";
            }
            $response .= "</ul>";
        } else {
            $response = "I searched the database but couldn't find any products matching <b>\"" . htmlspecialchars($keyword) . "\"</b>. Please check the spelling or try another keyword like <i>\"low stock items\"</i> or <i>\"total stock value\"</i>.";
        }
    } else {
        $response = "I'm not sure how to answer that question. You can ask me things like:<br>
        - <i>\"How many keyboards are in stock?\"</i><br>
        - <i>\"What is our total stock value?\"</i><br>
        - <i>\"List our suppliers\"</i>";
    }
}

echo json_encode(["response" => $response]);
?>
