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
        <h4 class="m-0 font-weight-bold text-primary">Damaged & Written-Off Goods</h4>
        <div>
            <button class="btn btn-warning text-white" data-toggle="modal" data-target="#dgModal"><i class="fas fa-exclamation-triangle fa-fw"></i> Report Damaged Goods</button>
            <button onclick="exportTableToCSV('damaged-goods.csv')" class="btn btn-success"><i class="fas fa-file-csv fa-fw"></i> Export CSV</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"> 
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Qty Damaged</th>
                        <th>Notes / Reason</th>
                        <th>Reported By</th>
                    </tr>
                </thead>
                <tbody>
                <?php                  
                $query = 'SELECT * FROM damaged_goods ORDER BY id DESC';
                $result = mysqli_query($db, $query) or die (mysqli_error($db));
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>'. $row['created_at'].'</td>';
                    echo '<td class="sku-text">'. $row['product_code'].'</td>';
                    echo '<td>'. $row['product_name'].'</td>';
                    echo '<td class="number-text text-danger">'. $row['quantity'].'</td>';
                    echo '<td>'. $row['notes'].'</td>';
                    echo '<td>'. $row['reported_by'].'</td>';
                    echo '</tr>';
                }
                ?> 
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Damaged Goods Modal -->
<div class="modal fade" id="dgModal" tabindex="-1" role="dialog" aria-labelledby="dgModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="dgModalLabel"><i class="fas fa-exclamation-triangle"></i> Report Damaged Goods</h5>
        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form role="form" method="post" action="dg_transac.php?action=add">
          <div class="form-group">
            <label>Select Product</label>
            <select class="form-control" name="product_code" required>
              <option value="" disabled selected hidden>Choose Product</option>
              <?php echo $prod_options; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Quantity Damaged</label>
            <input type="number" min="1" max="9999" class="form-control" placeholder="Qty" name="quantity" required>
          </div>
          <div class="form-group">
            <label>Reason / Notes</label>
            <textarea rows="3" class="form-control" placeholder="Explain the reason for damage/write-off (e.g. Expired, broken in delivery)" name="notes" required></textarea>
          </div>
          <hr>
          <button type="submit" class="btn btn-danger"><i class="fa fa-check fa-fw"></i> Submit Report</button>
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>      
        </form>  
      </div>
    </div>
  </div>
</div>

<?php
include'../includes/footer.php';
?>
