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
$sql = "SELECT DISTINCT CNAME, CATEGORY_ID FROM category order by CNAME asc";
$result = mysqli_query($db, $sql) or die ("Bad SQL: $sql");

$aaa = "<select class='form-control' name='category' required>
        <option disabled selected hidden>Select Category</option>";
  while ($row = mysqli_fetch_assoc($result)) {
    $aaa .= "<option value='".$row['CATEGORY_ID']."'>".$row['CNAME']."</option>";
  }

$aaa .= "</select>";

$sql2 = "SELECT DISTINCT SUPPLIER_ID, COMPANY_NAME FROM supplier order by COMPANY_NAME asc";
$result2 = mysqli_query($db, $sql2) or die ("Bad SQL: $sql2");

$sup = "<select class='form-control' name='supplier' required>
        <option disabled selected hidden>Select Supplier</option>";
  while ($row = mysqli_fetch_assoc($result2)) {
    $sup .= "<option value='".$row['SUPPLIER_ID']."'>".$row['COMPANY_NAME']."</option>";
  }

$sup .= "</select>";
?>
            
            <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
              <h4 class="m-0 font-weight-bold text-primary">Product&nbsp;<a  href="#" data-toggle="modal" data-target="#aModal" type="button" class="btn btn-primary bg-gradient-primary" style="border-radius: 0px;"><i class="fas fa-fw fa-plus"></i></a></h4>
              <button type="button" onclick="exportTableToCSV('products.csv')" class="btn btn-success"><i class="fas fa-file-csv fa-fw"></i> Export CSV</button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"> 
               <thead>
                   <tr>
                     <th>Product Code</th>
                     <th>Name</th>
                     <th>Category</th>
                     <th>Reorder Threshold</th>
                     <th>Unit Cost</th>
                     <th>Sale Price</th>
                     <th>Location</th>
                     <th>Action</th>
                   </tr>
               </thead>
          <tbody>

<?php                  
    $query = 'SELECT PRODUCT_ID, PRODUCT_CODE, NAME, PRICE, CNAME, DATE_STOCK_IN, p.CATEGORY, p.REORDER_THRESHOLD, p.UNIT_COST, p.SALE_PRICE, p.LOCATION, COUNT(QTY_STOCK) AS QTY_STOCK FROM product p join category c on p.CATEGORY_ID=c.CATEGORY_ID GROUP BY PRODUCT_CODE';
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
                echo '<td>'. (!empty($row['CATEGORY']) ? $row['CATEGORY'] : $row['CNAME']) .'</td>';
                echo '<td class="number-text">'. $row['REORDER_THRESHOLD'].'</td>';
                echo '<td class="price-text">$ '. number_format($row['UNIT_COST'], 2).'</td>';
                echo '<td class="price-text">$ '. number_format($row['SALE_PRICE'] > 0 ? $row['SALE_PRICE'] : $row['PRICE'], 2).'</td>';
                echo '<td>'. $row['LOCATION'].'</td>';
                      echo '<td align="right">
                              <div class="btn-group">
                                <a type="button" class="btn btn-primary bg-gradient-primary" href="pro_searchfrm.php?action=edit & id='.$row['PRODUCT_CODE'] . '">
                                  <i class="fas fa-fw fa-list-alt"></i> Details
                                </a>
                                <a type="button" class="btn btn-warning bg-gradient-warning" href="pro_edit.php?action=edit & id='.$row['PRODUCT_ID']. '">
                                  <i class="fas fa-fw fa-edit"></i> Edit
                                </a>
                              </div>
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

  <!-- Product Modal-->
  <div class="modal fade" id="aModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Product</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form role="form" method="post" action="pro_transac.php?action=add">
           <div class="form-group">
             <input class="form-control" placeholder="Product Code" name="prodcode" required>
           </div>
           <div class="form-group">
             <input class="form-control" placeholder="Name" name="name" required>
           </div>
           <div class="form-group">
             <textarea rows="5" cols="50" class="form-control" placeholder="Description" name="description" required></textarea>
           </div>
           <div class="form-group">
             <input type="number" min="1" max="999999999" class="form-control" placeholder="Quantity" name="quantity" required>
           </div>
           <div class="form-group">
             <input type="number" min="1" max="999999999" class="form-control" placeholder="On Hand" name="onhand" required>
           </div>
           <div class="form-group">
             <input type="number" step="0.01" min="0" class="form-control" placeholder="Unit Cost" name="unit_cost" required>
           </div>
           <div class="form-group">
             <input type="number" step="0.01" min="0" class="form-control" placeholder="Sale Price" name="price" required>
           </div>
           <div class="form-group">
             <?php
               echo $aaa;
             ?>
           </div>
           <div class="form-group">
             <input class="form-control" placeholder="Category (Custom Text)" name="category_text">
           </div>
           <div class="form-group">
             <input type="number" min="0" class="form-control" placeholder="Reorder Threshold" name="reorder_threshold" required>
           </div>
           <div class="form-group">
             <input class="form-control" placeholder="Storage Location" name="location" required>
           </div>
           <div class="form-group">
             <?php
               echo $sup;
             ?>
           </div>
           <div class="form-group">
             <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" class="form-control" placeholder="Date Stock In" name="datestock" required>
           </div>
            <hr>
            <button type="submit" class="btn btn-success"><i class="fa fa-check fa-fw"></i>Save</button>
            <button type="reset" class="btn btn-danger"><i class="fa fa-times fa-fw"></i>Reset</button>
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>      
          </form>  
        </div>
      </div>
    </div>
  </div>