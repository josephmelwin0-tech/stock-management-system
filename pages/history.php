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
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h4 class="m-0 font-weight-bold text-primary">Inventory Audit & Stock History</h4>
        <button onclick="exportTableToCSV('stock-history.csv')" class="btn btn-sm btn-success"><i class="fas fa-file-csv fa-fw"></i> Export to CSV</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"> 
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Action</th>
                        <th>Qty Changed</th>
                        <th>Executed By</th>
                    </tr>
                </thead>
                <tbody>
                <?php                  
                $query = 'SELECT * FROM stock_history ORDER BY id DESC';
                $result = mysqli_query($db, $query) or die (mysqli_error($db));
                while ($row = mysqli_fetch_assoc($result)) {
                    $badge_class = 'badge-primary';
                    if ($row['action_type'] == 'SALE') {
                        $badge_class = 'badge-danger';
                    } elseif ($row['action_type'] == 'DAMAGE') {
                        $badge_class = 'badge-warning';
                    } elseif ($row['action_type'] == 'ADD') {
                        $badge_class = 'badge-success';
                    } elseif ($row['action_type'] == 'RECEIVE') {
                        $badge_class = 'badge-info';
                    }
                    
                    echo '<tr>';
                    echo '<td>'. $row['created_at'].'</td>';
                    echo '<td class="sku-text">'. $row['product_code'].'</td>';
                    echo '<td>'. $row['product_name'].'</td>';
                    echo '<td><span class="badge '. $badge_class .'">'. $row['action_type'] .'</span></td>';
                    echo '<td class="number-text">'. $row['quantity'].'</td>';
                    echo '<td>'. $row['user'].'</td>';
                    echo '</tr>';
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
