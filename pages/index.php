<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Check user type authorization
$query = 'SELECT ID, t.TYPE
          FROM users u
          JOIN type t ON t.TYPE_ID=u.TYPE_ID WHERE ID = '.$_SESSION['MEMBER_ID'].'';
$result = mysqli_query($db, $query) or die(mysqli_error($db));

while ($row = mysqli_fetch_assoc($result)) {
    $Aa = $row['TYPE'];
    if ($Aa == 'User') {
        ?>
        <script type="text/javascript">
            alert("Restricted Page! You will be redirected to POS");
            window.location = "pos.php";
        </script>
        <?php
        exit;
    }
}

// -------------------------------------------------------------
// Database Queries for KPI Cards
// -------------------------------------------------------------

// 1. Total Stock Value: Sum of product prices
$stock_val_query = "SELECT SUM(COALESCE(NULLIF(SALE_PRICE, 0), PRICE)) AS total_val FROM product";
$stock_val_res = mysqli_query($db, $stock_val_query) or die(mysqli_error($db));
$stock_val_row = mysqli_fetch_assoc($stock_val_res);
$total_stock_value = floatval($stock_val_row['total_val'] ?? 0);

// 2. Low-Stock Item Count: Unique products where QTY < threshold
$low_stock_query = "SELECT COUNT(*) AS low_count FROM (
    SELECT PRODUCT_CODE, COUNT(QTY_STOCK) AS QTY_STOCK, MAX(REORDER_THRESHOLD) AS REORDER_THRESHOLD 
    FROM product 
    GROUP BY PRODUCT_CODE
) AS temp WHERE QTY_STOCK < REORDER_THRESHOLD";
$low_stock_res = mysqli_query($db, $low_stock_query) or die(mysqli_error($db));
$low_stock_row = mysqli_fetch_assoc($low_stock_res);
$low_stock_count = intval($low_stock_row['low_count'] ?? 0);

// 3. Pending Purchase Orders (Simulated / Mocked)
$pending_po_count = 3;

// 4. Today's Sales Total
$today_date = date("Y-m-d");
$sales_query = "SELECT GRANDTOTAL FROM transaction WHERE DATE LIKE '$today_date%'";
$sales_res = mysqli_query($db, $sales_query) or die(mysqli_error($db));
$today_sales_total = 0;
if ($sales_res) {
    while ($row = mysqli_fetch_assoc($sales_res)) {
        $grand_total = floatval(str_replace(',', '', $row['GRANDTOTAL']));
        $today_sales_total += $grand_total;
    }
}

// -------------------------------------------------------------
// Database Queries for Charts
// -------------------------------------------------------------

// Chart 1: Stock Value Trend Over Time
$trend_query = "SELECT DATE_STOCK_IN, SUM(COALESCE(NULLIF(SALE_PRICE, 0), PRICE)) AS val FROM product GROUP BY DATE_STOCK_IN ORDER BY DATE_STOCK_IN ASC LIMIT 10";
$trend_res = mysqli_query($db, $trend_query) or die(mysqli_error($db));
$trend_dates = [];
$trend_values = [];
if ($trend_res) {
    while ($row = mysqli_fetch_assoc($trend_res)) {
        $trend_dates[] = $row['DATE_STOCK_IN'];
        $trend_values[] = floatval($row['val']);
    }
}

// Chart 2: Top 5 Selling Items
$top_selling_query = "SELECT PRODUCTS, SUM(CAST(QTY AS SIGNED)) AS total_sold FROM transaction_details GROUP BY PRODUCTS ORDER BY total_sold DESC LIMIT 5";
$top_selling_res = mysqli_query($db, $top_selling_query) or die(mysqli_error($db));
$top_products = [];
$top_sales = [];
if ($top_selling_res) {
    while ($row = mysqli_fetch_assoc($top_selling_res)) {
        $top_products[] = $row['PRODUCTS'];
        $top_sales[] = intval($row['total_sold']);
    }
}

// Chart 3: Stock Breakdown by Category (Custom fallback to CNAME)
$cat_breakdown_query = "SELECT COALESCE(NULLIF(p.CATEGORY, ''), c.CNAME) AS cat_name, COUNT(PRODUCT_ID) AS qty FROM product p LEFT JOIN category c ON p.CATEGORY_ID=c.CATEGORY_ID GROUP BY cat_name";
$cat_breakdown_res = mysqli_query($db, $cat_breakdown_query) or die(mysqli_error($db));
$cat_names = [];
$cat_quantities = [];
if ($cat_breakdown_res) {
    while ($row = mysqli_fetch_assoc($cat_breakdown_res)) {
        $cat_names[] = $row['cat_name'];
        $cat_quantities[] = intval($row['qty']);
    }
}

// Chart 4: PO vs Received vs Back Orders (Simulated based on actual product count)
$total_products_query = "SELECT COUNT(*) AS total FROM product";
$total_products_res = mysqli_query($db, $total_products_query) or die(mysqli_error($db));
$total_products_row = mysqli_fetch_assoc($total_products_res);
$received_count = intval($total_products_row['total'] ?? 0);
$po_count = $received_count + 8; // Simulate some ordered but not received
$back_order_count = 8;

?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Redesigned Dashboard Title -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Dashboard Overview</h1>
    <span class="badge bg-warning text-white py-2 px-3 mono-text"><?php echo date("F d, Y"); ?></span>
</div>

<!-- KPI Summary Cards Row -->
<div class="row">

    <!-- Total Stock Value -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #C9762C;">Total Stock Value</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 price-text">$ <?php echo number_format($total_stock_value, 2); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low-Stock Item Count -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Low-Stock Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 number-text"><?php echo $low_stock_count; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Purchase Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Purchase Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 number-text"><?php echo $pending_po_count; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Sales Total -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Sales Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 price-text">$ <?php echo number_format($today_sales_total, 2); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Charts Grid - Row 1 -->
<div class="row">
    <!-- Stock Value Trend -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Stock Value Trend Over Time</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="position: relative; height:320px; width:100%">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Category Stock Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie" style="position: relative; height:320px; width:100%">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid - Row 2 -->
<div class="row">
    <!-- Top Selling Items -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Selling Items</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar" style="position: relative; height:320px; width:100%">
                    <canvas id="sellingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- PO vs Received vs Back Orders -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">PO vs Received vs Back Orders Comparison</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar" style="position: relative; height:320px; width:100%">
                    <canvas id="poChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards for Recent Activity & Quick Links -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Added Products</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php
                    $recent_query = "SELECT NAME, PRODUCT_CODE, DATE_STOCK_IN FROM product GROUP BY PRODUCT_CODE ORDER BY PRODUCT_ID DESC LIMIT 5";
                    $recent_res = mysqli_query($db, $recent_query) or die(mysqli_error($db));
                    while ($row = mysqli_fetch_array($recent_res)) {
                        echo "<div class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                <div>
                                    <h6 class='mb-0 font-weight-bold'>{$row['NAME']}</h6>
                                    <small class='text-muted sku-text'>{$row['PRODUCT_CODE']}</small>
                                </div>
                                <span class='badge bg-light text-dark mono-text'>{$row['DATE_STOCK_IN']}</span>
                              </div>";
                    }
                    ?>
                </div>
                <a href="product.php" class="btn btn-primary btn-block mt-3">View All Products</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Statistics Quick Info</h6>
            </div>
            <div class="card-body py-4">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Customers</div>
                        <?php
                        $c_q = mysqli_query($db, "SELECT COUNT(*) FROM customer");
                        $c_r = mysqli_fetch_array($c_q);
                        ?>
                        <div class="h3 font-weight-bold text-gray-800 number-text"><?php echo $c_r[0]; ?></div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Suppliers</div>
                        <?php
                        $s_q = mysqli_query($db, "SELECT COUNT(*) FROM supplier");
                        $s_r = mysqli_fetch_array($s_q);
                        ?>
                        <div class="h3 font-weight-bold text-gray-800 number-text"><?php echo $s_r[0]; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Employees</div>
                        <?php
                        $e_q = mysqli_query($db, "SELECT COUNT(*) FROM employee");
                        $e_r = mysqli_fetch_array($e_q);
                        ?>
                        <div class="h3 font-weight-bold text-gray-800 number-text"><?php echo $e_r[0]; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Registered Accounts</div>
                        <?php
                        $u_q = mysqli_query($db, "SELECT COUNT(*) FROM users WHERE TYPE_ID=2");
                        $u_r = mysqli_fetch_array($u_q);
                        ?>
                        <div class="h3 font-weight-bold text-gray-800 number-text"><?php echo $u_r[0]; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS Code to initialize Chart.js charts with DB data -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // -------------------------------------------------------------
    // Chart 1: Stock Value Trend Over Time
    // -------------------------------------------------------------
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendLabels = <?php echo json_encode($trend_dates); ?>;
    const trendData = <?php echo json_encode($trend_values); ?>;
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels.length ? trendLabels : ["No Data"],
            datasets: [{
                label: 'Stock Value ($)',
                data: trendData.length ? trendData : [0],
                backgroundColor: 'rgba(201, 118, 44, 0.05)',
                borderColor: '#C9762C',
                borderWidth: 3,
                pointBackgroundColor: '#C9762C',
                pointBorderColor: '#ffffff',
                pointRadius: 4,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    ticks: {
                        font: { family: 'JetBrains Mono' }
                    },
                    grid: { color: '#E3E3E0' }
                },
                x: {
                    ticks: {
                        font: { family: 'Inter' }
                    },
                    grid: { display: false }
                }
            }
        }
    });

    // -------------------------------------------------------------
    // Chart 2: Category Breakdown
    // -------------------------------------------------------------
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    const catLabels = <?php echo json_encode($cat_names); ?>;
    const catData = <?php echo json_encode($cat_quantities); ?>;
    
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: catLabels.length ? catLabels : ["No Categories"],
            datasets: [{
                data: catData.length ? catData : [0],
                backgroundColor: [
                    '#1B2430', '#C9762C', '#2F9E44', '#D64545', 
                    '#4D96FF', '#6BCB77', '#FFD93D', '#FF6B6B', '#9B5DE5'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { family: 'Inter', size: 11 }
                    }
                }
            }
        }
    });

    // -------------------------------------------------------------
    // Chart 3: Top Selling Items
    // -------------------------------------------------------------
    const sellingCtx = document.getElementById('sellingChart').getContext('2d');
    const sellingLabels = <?php echo json_encode($top_products); ?>;
    const sellingData = <?php echo json_encode($top_sales); ?>;
    
    new Chart(sellingCtx, {
        type: 'bar',
        data: {
            labels: sellingLabels.length ? sellingLabels : ["No Sales"],
            datasets: [{
                label: 'Units Sold',
                data: sellingData.length ? sellingData : [0],
                backgroundColor: '#1B2430',
                borderRadius: 4,
                maxBarThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    ticks: {
                        font: { family: 'JetBrains Mono' }
                    },
                    grid: { color: '#E3E3E0' }
                },
                x: {
                    ticks: {
                        font: { family: 'Inter' }
                    },
                    grid: { display: false }
                }
            }
        }
    });

    // -------------------------------------------------------------
    // Chart 4: PO vs Received vs Back Orders
    // -------------------------------------------------------------
    const poCtx = document.getElementById('poChart').getContext('2d');
    
    new Chart(poCtx, {
        type: 'bar',
        data: {
            labels: ['Purchase Orders', 'Received Items', 'Back Orders'],
            datasets: [{
                label: 'Quantity',
                data: [<?php echo $po_count; ?>, <?php echo $received_count; ?>, <?php echo $back_order_count; ?>],
                backgroundColor: ['#C9762C', '#2F9E44', '#D64545'],
                borderRadius: 4,
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    ticks: {
                        font: { family: 'JetBrains Mono' }
                    },
                    grid: { color: '#E3E3E0' }
                },
                x: {
                    ticks: {
                        font: { family: 'Inter' }
                    },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<?php
include '../includes/footer.php';
?>