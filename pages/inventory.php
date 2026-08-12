<?php
include'../includes/connection.php';

include'../includes/sidebar.php';
  $query = 'SELECT ID, t.TYPE
            FROM users u
            JOIN type t ON t.TYPE_ID=u.TYPE_ID WHERE ID = '.$_SESSION['MEMBER_ID'].'';
  $result = mysqli_query($db, $query) or die (mysqli_error($db));
  
  while ($row = mysqli_fetch_assoc($result)) {
            $Aa = $row['TYPE'];
                   
  if ($Aa=='User'){
?>
  <script type="text/javascript">
    //then it will be redirected
    alert("Restricted Page! You will be redirected to POS");
    window.location = "pos.php";
  </script>
<?php
  }           
}
            ?>
            
            <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
              <h4 class="m-0 font-weight-bold text-primary">Inventory</h4>
              <button type="button" onclick="exportTableToCSV('inventory.csv')" class="btn btn-success"><i class="fas fa-file-csv fa-fw"></i> Export CSV</button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"> 
               <thead>
                   <tr>
                     <th>Product Code</th>
                     <th>Name</th>
                     <th>Quantity</th>
                     <th>On Hand</th>
                     <th>Category</th>
                     <th>Date Stock In</th>
                     <th>Action</th>
                   </tr>
               </thead>
          <tbody>

<?php                  
    $query = 'SELECT PRODUCT_ID, PRODUCT_CODE, NAME, COUNT(`QTY_STOCK`) AS "QTY_STOCK", COUNT(`ON_HAND`) AS "ON_HAND", CNAME, p.CATEGORY, DATE_STOCK_IN, REORDER_THRESHOLD FROM product p join category c on p.CATEGORY_ID=c.CATEGORY_ID GROUP BY PRODUCT_CODE';
        $result = mysqli_query($db, $query) or die (mysqli_error($db));
      
            while ($row = mysqli_fetch_assoc($result)) {
                $qty_stock = $row['QTY_STOCK'];
                $reorder = $row['REORDER_THRESHOLD'];
                $border_class = 'stock-green';
                if ($qty_stock < $reorder) {
                    $border_class = 'stock-red';
                } elseif ($qty_stock <= $reorder + 3) {
                    $border_class = 'stock-amber';
                }
                                  
                echo '<tr class="'. $border_class .'">';
                echo '<td class="sku-text">'. $row['PRODUCT_CODE'].'</td>';
                echo '<td>'. $row['NAME'].'</td>';
                echo '<td class="number-text">'. $row['QTY_STOCK'].'</td>';
                echo '<td class="number-text">'. $row['ON_HAND'].'</td>';
                echo '<td>'. (!empty($row['CATEGORY']) ? $row['CATEGORY'] : $row['CNAME']) .'</td>';
                echo '<td>'. $row['DATE_STOCK_IN'].'</td>';
                      echo '<td align="right">
                              <a type="button" class="btn btn-primary bg-gradient-primary" href="inv_searchfrm.php?action=edit & id='.$row['PRODUCT_CODE'] . '"><i class="fas fa-fw fa-th-list"></i> View</a>
                          </td>';
                echo '</tr> ';
                        }
?> 
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>

<?php
include'../includes/footer.php';
?>
