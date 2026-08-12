<?php

include'../includes/connection.php';
session_start();
$emp = isset($_SESSION['FIRST_NAME']) ? $_SESSION['FIRST_NAME'] . ' ' . $_SESSION['LAST_NAME'] : 'Manager';
?>
          <!-- Page Content -->
          <div class="col-lg-12">
            <?php
              $pc = $_POST['prodcode'];
              $name = $_POST['name'];
              $desc = $_POST['description'];
              $qty = $_POST['quantity'];
              $oh = $_POST['onhand'];
              $pr = $_POST['price']; 
              $cat = $_POST['category'];
              $supp = $_POST['supplier'];
              $dats = $_POST['datestock']; 
              
              $cat_text = $_POST['category_text'];
              $reorder = $_POST['reorder_threshold'];
              $cost = $_POST['unit_cost'];
              $loc = $_POST['location'];
        
              switch($_GET['action']){
                case 'add':  
                for($i=0; $i < $qty; $i++){
                    $query = "INSERT INTO product
                              (PRODUCT_ID, PRODUCT_CODE, NAME, DESCRIPTION, QTY_STOCK, ON_HAND, PRICE, CATEGORY_ID, SUPPLIER_ID, DATE_STOCK_IN, CATEGORY, REORDER_THRESHOLD, UNIT_COST, SALE_PRICE, LOCATION)
                              VALUES (Null,'{$pc}','{$name}','{$desc}',1,1,{$pr},{$cat},{$supp},'{$dats}','{$cat_text}',{$reorder},{$cost},{$pr},'{$loc}')";
                    mysqli_query($db,$query)or die ('Error in updating product in Database '.$query);
                }

                // Log to stock history
                $history_query = "INSERT INTO stock_history (product_code, product_name, quantity, action_type, user) VALUES ('" . mysqli_real_escape_string($db, $pc) . "', '" . mysqli_real_escape_string($db, $name) . "', " . $qty . ", 'ADD', '" . mysqli_real_escape_string($db, $emp) . "')";
                mysqli_query($db, $history_query) or die(mysqli_error($db));
                break;
              }
            ?>
              <script type="text/javascript">window.location = "product.php";</script>
          </div>

<?php
include'../includes/footer.php';
?>