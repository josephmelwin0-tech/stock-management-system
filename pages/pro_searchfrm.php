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
          <center><div class="card shadow mb-4 col-xs-12 col-md-8 border-bottom-primary">
            <div class="card-header py-3">
              <h4 class="m-2 font-weight-bold text-primary">Product's Detail</h4>
            </div>
            <a href="product.php?action=add" type="button" class="btn btn-primary bg-gradient-primary btn-block"> <i class="fas fa-flip-horizontal fa-fw fa-share"></i> Back</a>
            <div class="card-body">
          <?php             $query = 'SELECT PRODUCT_ID, PRODUCT_CODE, NAME, DESCRIPTION, COUNT(`QTY_STOCK`) AS "QTY_STOCK", COUNT(`ON_HAND`) AS "ON_HAND", PRICE, c.CNAME, p.CATEGORY, p.REORDER_THRESHOLD, p.UNIT_COST, p.SALE_PRICE, p.LOCATION FROM product p join category c on p.CATEGORY_ID=c.CATEGORY_ID WHERE PRODUCT_CODE ='.$_GET['id'];
             $result = mysqli_query($db, $query) or die(mysqli_error($db));
               while($row = mysqli_fetch_array($result))
               {   
                 $zz= $row['PRODUCT_ID'];
                 $zzz= $row['PRODUCT_CODE'];
                 $i= $row['NAME'];
                 $a=$row['DESCRIPTION'];
                 $c=$row['PRICE'];
                 $d=$row['CNAME'];
                 $cat_text = $row['CATEGORY'];
                 $reorder = $row['REORDER_THRESHOLD'];
                 $cost = $row['UNIT_COST'];
                 $saleprice = $row['SALE_PRICE'];
                 $location = $row['LOCATION'];
               }
               $id = $_GET['id'];
          ?>

                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Product Code<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5>
                          : <?php echo $zzz; ?><br>
                        </h5>
                      </div>
                    </div>
                    <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Product Name<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5>
                          : <?php echo $i; ?> <br>
                        </h5>
                      </div>
                    </div>
                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Description<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5>
                          : <?php echo $a; ?><br>
                        </h5>
                      </div>
                    </div>
                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Unit Cost<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5 class="price-text">
                          : $ <?php echo number_format($cost, 2); ?><br>
                        </h5>
                      </div>
                    </div>
                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Sale Price<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5 class="price-text">
                          : $ <?php echo number_format($saleprice > 0 ? $saleprice : $c, 2); ?><br>
                        </h5>
                      </div>
                    </div>
                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Category (Dropdown)<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5>
                          : <?php echo $d; ?><br>
                        </h5>
                      </div>
                    </div>
                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Category (Custom Text)<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5>
                          : <?php echo $cat_text; ?><br>
                        </h5>
                      </div>
                    </div>
                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Reorder Threshold<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5 class="number-text">
                          : <?php echo $reorder; ?><br>
                        </h5>
                      </div>
                    </div>
                  <div class="form-group row text-left">
                      <div class="col-sm-3 text-primary">
                        <h5>
                          Storage Location<br>
                        </h5>
                      </div>
                      <div class="col-sm-9">
                        <h5>
                          : <?php echo $location; ?><br>
                        </h5>
                      </div>
                    </div>
                </div>
          </div></center>

          <div class="card shadow mb-4 col-xs-12 col-md-15 border-bottom-primary">
            <div class="card-header py-3">
              <h4 class="m-2 font-weight-bold text-primary">Inventory</h4>
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
                     <th>Supplier</th>
                     <th>Date Stock In</th>
                   </tr>
               </thead>
          <tbody>

<?php                  
    $query = 'SELECT PRODUCT_ID, PRODUCT_CODE, NAME, COUNT("QTY_STOCK") AS QTY_STOCK, COUNT("ON_HAND") AS ON_HAND, CNAME, p.CATEGORY, COMPANY_NAME, p.SUPPLIER_ID, DATE_STOCK_IN FROM product p join category c on p.CATEGORY_ID=c.CATEGORY_ID JOIN supplier s ON p.SUPPLIER_ID=s.SUPPLIER_ID where PRODUCT_CODE ='.$zzz.' GROUP BY `SUPPLIER_ID`, `DATE_STOCK_IN`';
        $result = mysqli_query($db, $query) or die (mysqli_error($db));
      
            while ($row = mysqli_fetch_assoc($result)) {
                                  
                echo '<tr>';
                echo '<td class="sku-text">'. $row['PRODUCT_CODE'].'</td>';
                echo '<td>'. $row['NAME'].'</td>';
                echo '<td class="number-text">'. $row['QTY_STOCK'].'</td>';
                echo '<td class="number-text">'. $row['ON_HAND'].'</td>';
                echo '<td>'. (!empty($row['CATEGORY']) ? $row['CATEGORY'] : $row['CNAME']) .'</td>';
                echo '<td>'. $row['COMPANY_NAME'].'</td>';
                echo '<td>'. $row['DATE_STOCK_IN'].'</td>';
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