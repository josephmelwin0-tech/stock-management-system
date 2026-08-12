<?php
include'../includes/connection.php';
include'../includes/sidebar.php';

// Authorization Check
$query = 'SELECT ID, t.TYPE
          FROM users u
          JOIN type t ON t.TYPE_ID=u.TYPE_ID WHERE ID = '.$_SESSION['MEMBER_ID'].'';
$result = mysqli_query($db, $query) or die(mysqli_error($db));
while ($row = mysqli_fetch_assoc($result)) {
    $Aa = $row['TYPE'];
    if ($Aa=='User'){
        ?>
        <script type="text/javascript">
            alert("Restricted Page! You will be redirected to POS");
            window.location = "pos.php";
        </script>
        <?php
        exit;
    }           
}

// Fetch Suppliers for Dropdown
$sup_sql = "SELECT SUPPLIER_ID, COMPANY_NAME FROM supplier ORDER BY COMPANY_NAME ASC";
$sup_res = mysqli_query($db, $sup_sql);
$sup_options = "";
while ($row = mysqli_fetch_assoc($sup_res)) {
    $sup_options .= "<option value='{$row['SUPPLIER_ID']}'>{$row['COMPANY_NAME']}</option>";
}

// Fetch Unique Products for Dropdown
$prod_sql = "SELECT DISTINCT PRODUCT_CODE, NAME FROM product ORDER BY NAME ASC";
$prod_res = mysqli_query($db, $prod_sql);
$prod_options = "";
while ($row = mysqli_fetch_assoc($prod_res)) {
    $prod_options .= "<option value='{$row['PRODUCT_CODE']}'>{$row['NAME']} ({$row['PRODUCT_CODE']})</option>";
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h4 class="m-0 font-weight-bold text-primary">Purchase Orders (PO)</h4>
        <div>
            <button class="btn btn-primary" data-toggle="modal" data-target="#poModal"><i class="fas fa-plus fa-fw"></i> Create Purchase Order</button>
            <button onclick="exportTableToCSV('purchase-orders.csv')" class="btn btn-success"><i class="fas fa-file-csv fa-fw"></i> Export CSV</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"> 
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Product Info</th>
                        <th>Qty Ordered</th>
                        <th>Cost per Unit</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php                  
                $query = 'SELECT po.po_id, po.po_number, po.status, po.total_amount, po.created_at, 
                                 s.COMPANY_NAME, pod.product_name, pod.product_code, pod.quantity_ordered, pod.price 
                          FROM purchase_orders po 
                          JOIN supplier s ON po.supplier_id = s.SUPPLIER_ID
                          JOIN purchase_order_details pod ON po.po_id = pod.po_id
                          ORDER BY po.po_id DESC';
                $result = mysqli_query($db, $query) or die (mysqli_error($db));
                while ($row = mysqli_fetch_assoc($result)) {
                    $badge_class = 'badge-warning';
                    if ($row['status'] == 'RECEIVED') {
                        $badge_class = 'badge-success';
                    }
                    
                    echo '<tr>';
                    echo '<td>'. $row['po_number'].'</td>';
                    echo '<td>'. $row['COMPANY_NAME'].'</td>';
                    echo '<td>'. $row['product_name'] .' ('. $row['product_code'] .')</td>';
                    echo '<td class="number-text">'. $row['quantity_ordered'].'</td>';
                    echo '<td class="price-text">$ '. number_format($row['price'], 2).'</td>';
                    echo '<td class="price-text">$ '. number_format($row['total_amount'], 2).'</td>';
                    echo '<td><span class="badge '. $badge_class .'">'. $row['status'] .'</span></td>';
                    echo '<td align="center">';
                    if ($row['status'] == 'PENDING') {
                        echo '<a class="btn btn-sm btn-success bg-gradient-success" href="po_transac.php?action=receive&po_id='.$row['po_id'].'"><i class="fas fa-box-open fa-fw"></i> Receive Stock</a>';
                    } else {
                        echo '<span class="text-muted"><i class="fas fa-check-circle text-success"></i> Stock Added</span>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                ?> 
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PO Modal -->
<div class="modal fade" id="poModal" tabindex="-1" role="dialog" aria-labelledby="poModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="poModalLabel">New Purchase Order</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form role="form" method="post" action="po_transac.php?action=add">
          <div class="form-group">
            <label>PO Number</label>
            <input class="form-control" name="po_number" value="PO-<?php echo strtoupper(substr(md5(time()), 0, 8)); ?>" readonly required>
          </div>
          <div class="form-group">
            <label>Select Supplier</label>
            <select class="form-control" name="supplier" required>
              <option value="" disabled selected hidden>Choose Supplier</option>
              <?php echo $sup_options; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Select Product</label>
            <select class="form-control" name="product_code" required>
              <option value="" disabled selected hidden>Choose Product</option>
              <?php echo $prod_options; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Quantity Ordered</label>
            <input type="number" min="1" max="9999" class="form-control" placeholder="Qty" name="quantity" required>
          </div>
          <div class="form-group">
            <label>Cost per Unit ($)</label>
            <input type="number" step="0.01" min="0" class="form-control" placeholder="Cost Price" name="price" required>
          </div>
          <hr>
          <button type="submit" class="btn btn-success"><i class="fa fa-check fa-fw"></i> Save PO</button>
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>      
        </form>  
      </div>
    </div>
  </div>
</div>

<?php
include'../includes/footer.php';
?>
