
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
              <span>Copyright © 2022. All Rights Reserved.</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body"><?php echo  $_SESSION['FIRST_NAME']; ?> are you sure do you want to logout?</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="../js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="../js/demo/datatables-demo.js"></script>
  <script src="../js/city.js"></script> 
  

<!-- PROFILE OVERLAY NA MODAL -->
<div id="overlay" onclick="off()">
  <div id="text">I'm <?php echo  $_SESSION['FIRST_NAME']. ' '.$_SESSION['LAST_NAME'] ;?><BR>
    From <?php echo  $_SESSION['PROVINCE']. ' '.$_SESSION['CITY'] ;?></div>
</div>
<script>
function on() {
  document.getElementById("overlay").style.display = "block";
}

function off() {
  document.getElementById("overlay").style.display = "none";
}

//used in pos sa number only na textfields
function isNumberKey(evt)
      {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 
        && (charCode < 48 || charCode > 57))
        return false;
        return true;
      }  
//end of used in pos sa number only na textfields

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('darkModeToggle');
    const toggleIcon = document.getElementById('darkModeIcon');

    function updateIcon(isDark) {
        if (toggleIcon) {
            if (isDark) {
                toggleIcon.className = 'fas fa-sun text-warning';
                toggleIcon.style.color = 'var(--color-warning)';
            } else {
                toggleIcon.className = 'fas fa-moon text-gray-600';
                toggleIcon.style.color = '';
            }
        }
    }

    const isDark = document.body.classList.contains('dark-mode') || localStorage.getItem('dark-mode') === 'true';
    if (isDark) {
        document.body.classList.add('dark-mode');
        updateIcon(true);
    } else {
        updateIcon(false);
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const wasDark = document.body.classList.contains('dark-mode');
            const nowDark = !wasDark;
            
            if (nowDark) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('dark-mode', 'true');
                updateIcon(true);
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('dark-mode', 'false');
                updateIcon(false);
            }
        });
    }
});

function downloadCSV(csv, filename) {
    var csvFile = new Blob([csv], {type: "text/csv;charset=utf-8;"});
    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveOrOpenBlob(csvFile, filename);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) {
            var url = URL.createObjectURL(csvFile);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            // Fallback to data URI for older browsers
            var encodedUri = encodeURI("data:text/csv;charset=utf-8," + csv);
            window.open(encodedUri);
        }
    }
}

function exportTableToCSV(filename) {
    try {
        var csv = [];
        var table = document.getElementById("dataTable") || document.querySelector("table");
        if (!table) {
            alert("Error: No data table found on this page.");
            return;
        }
        var rows = table.querySelectorAll("tr");
        if (rows.length === 0) {
            alert("Error: The table is empty.");
            return;
        }
        
        var actionIndex = -1;
        var headerRow = table.querySelector("thead tr");
        if (!headerRow) {
            headerRow = rows[0];
        }
        
        if (headerRow) {
            var headers = headerRow.querySelectorAll("th, td");
            for (var h = 0; h < headers.length; h++) {
                var headerText = headers[h].textContent || headers[h].innerText;
                if (headerText.toLowerCase().trim().includes("action")) {
                    actionIndex = h;
                    break;
                }
            }
        }
        
        for (var i = 0; i < rows.length; i++) {
            var row = [];
            var cols = rows[i].querySelectorAll("td, th");
            
            if (cols.length === 0) continue;
            
            for (var j = 0; j < cols.length; j++) {
                if (j === actionIndex) continue;
                
                var text = (cols[j].textContent || cols[j].innerText).trim();
                text = text.replace(/(\r\n|\n|\r)/gm, " ").replace(/\s+/g, ' ');
                text = text.replace(/"/g, '""');
                row.push('"' + text + '"');
            }
            csv.push(row.join(","));
        }
        
        if (csv.length === 0) {
            alert("Error: No rows could be parsed.");
            return;
        }
        
        downloadCSV(csv.join("\n"), filename);
    } catch (e) {
        alert("Export Error: " + e.message);
    }
}
</script>

<!-- Floating Chatbot Widget -->
<div id="chat-bubble" onclick="toggleChatWindow()" style="position: fixed; bottom: 25px; right: 25px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #4e73df, #224abe); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 9999; transition: all 0.3s ease;">
  <i class="fas fa-robot" id="chat-icon"></i>
</div>

<div id="chat-window" style="position: fixed; bottom: 95px; right: 25px; width: 360px; height: 500px; background: white; border-radius: 15px; box-shadow: 0 5px 30px rgba(0,0,0,0.25); display: none; flex-direction: column; overflow: hidden; z-index: 9999; border: 1px solid rgba(0,0,0,0.1); font-family: 'Nunito', sans-serif;">
  <div style="background: linear-gradient(135deg, #4e73df, #224abe); color: white; padding: 15px; display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center;">
      <i class="fas fa-robot fa-lg mr-2"></i>
      <div>
        <h6 class="m-0 font-weight-bold" style="font-size: 14px;">Stock AI Assistant</h6>
        <small style="opacity: 0.8; font-size: 11px;">Active &bull; Connected to Database</small>
      </div>
    </div>
    <button onclick="toggleChatWindow()" style="background: none; border: none; color: white; cursor: pointer; font-size: 20px; outline: none;">&times;</button>
  </div>
  
  <div id="chat-messages" style="flex: 1; padding: 15px; overflow-y: auto; background: #f8f9fc; display: flex; flex-direction: column; gap: 10px;">
    <div class="bot-msg" style="align-self: flex-start; background: white; color: #333; padding: 10px 14px; border-radius: 15px 15px 15px 0px; max-width: 80%; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-size: 13px; line-height: 1.4;">
      Hello! I am your AI Inventory assistant. Ask me questions about stock levels, low items, total stock valuation, or supplier listings.
    </div>
  </div>

  <div id="chat-chips" style="padding: 8px 15px; background: #f8f9fc; display: flex; gap: 8px; overflow-x: auto; border-top: 1px solid rgba(0,0,0,0.05); scrollbar-width: none;">
    <span onclick="sendQuickMessage('What items are low on stock?')" style="background: white; border: 1px solid #4e73df; color: #4e73df; padding: 4px 10px; border-radius: 20px; font-size: 11px; cursor: pointer; white-space: nowrap; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">Low Stock</span>
    <span onclick="sendQuickMessage('What is our total stock value?')" style="background: white; border: 1px solid #4e73df; color: #4e73df; padding: 4px 10px; border-radius: 20px; font-size: 11px; cursor: pointer; white-space: nowrap; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">Stock Value</span>
    <span onclick="sendQuickMessage('How much money did we make today?')" style="background: white; border: 1px solid #4e73df; color: #4e73df; padding: 4px 10px; border-radius: 20px; font-size: 11px; cursor: pointer; white-space: nowrap; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">Today's Sales</span>
  </div>

  <form id="chat-form" onsubmit="submitChatMessage(event)" style="display: flex; border-top: 1px solid #e3e6f0; padding: 10px; background: white; margin-bottom: 0px;">
    <input id="chat-input" type="text" placeholder="Ask assistant..." style="flex: 1; border: 1px solid #d1d3e2; border-radius: 20px; padding: 8px 15px; font-size: 13px; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#4e73df'" onblur="this.style.borderColor='#d1d3e2'">
    <button type="submit" style="background: #4e73df; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; margin-left: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(78,115,223,0.3); transition: background 0.2s;"><i class="fas fa-paper-plane" style="font-size: 12px;"></i></button>
  </form>
</div>

<script>
function toggleChatWindow() {
    var win = document.getElementById("chat-window");
    var bubble = document.getElementById("chat-bubble");
    var icon = document.getElementById("chat-icon");
    if (win.style.display === "none" || win.style.display === "") {
        win.style.display = "flex";
        bubble.style.transform = "rotate(90deg)";
        icon.className = "fas fa-times";
        document.getElementById("chat-input").focus();
    } else {
        win.style.display = "none";
        bubble.style.transform = "rotate(0deg)";
        icon.className = "fas fa-robot";
    }
}

function sendQuickMessage(text) {
    document.getElementById("chat-input").value = text;
    document.getElementById("chat-form").dispatchEvent(new Event('submit'));
}

function submitChatMessage(e) {
    e.preventDefault();
    var input = document.getElementById("chat-input");
    var msg = input.value.trim();
    if (msg === "") return;

    appendMessage(msg, "user-msg");
    input.value = "";
    
    // Add typing indicator
    var indicator = appendMessage("Thinking...", "bot-msg-loading");

    $.ajax({
        url: "chat_bot.php",
        type: "POST",
        data: { message: msg },
        dataType: "json",
        success: function(data) {
            indicator.remove();
            appendMessage(data.response, "bot-msg");
        },
        error: function() {
            indicator.remove();
            appendMessage("Sorry, I encountered an error checking the database. Please make sure the server is online.", "bot-msg");
        }
    });
}

function appendMessage(text, className) {
    var logs = document.getElementById("chat-messages");
    var div = document.createElement("div");
    
    // Set styles dynamically
    if (className === "user-msg") {
        div.style.alignSelf = "flex-end";
        div.style.background = "#4e73df";
        div.style.color = "white";
        div.style.padding = "10px 14px";
        div.style.borderRadius = "15px 15px 0px 15px";
        div.style.boxShadow = "0 2px 5px rgba(78,115,223,0.1)";
    } else if (className === "bot-msg-loading") {
        div.style.alignSelf = "flex-start";
        div.style.background = "white";
        div.style.color = "#888";
        div.style.padding = "10px 14px";
        div.style.borderRadius = "15px 15px 15px 0px";
        div.style.boxShadow = "0 2px 5px rgba(0,0,0,0.05)";
        div.style.fontStyle = "italic";
    } else { // bot-msg
        div.style.alignSelf = "flex-start";
        div.style.background = "white";
        div.style.color = "#333";
        div.style.padding = "10px 14px";
        div.style.borderRadius = "15px 15px 15px 0px";
        div.style.boxShadow = "0 2px 5px rgba(0,0,0,0.05)";
    }
    
    div.style.maxWidth = "80%";
    div.style.fontSize = "13px";
    div.style.lineHeight = "1.4";
    div.innerHTML = text;
    
    logs.appendChild(div);
    logs.scrollTop = logs.scrollHeight;
    return div;
}
</script>

</body>

</html>

<?php
  include 'modal.php';
// JOB SELECT OPTION TAB
$sql = "SELECT DISTINCT TYPE, TYPE_ID FROM type";
$result = mysqli_query($db, $sql) or die ("Bad SQL: $sql");

$opt = "<select class='form-control' name='type'>";
  while ($row = mysqli_fetch_assoc($result)) {
    $opt .= "<option value='".$row['TYPE_ID']."'>".$row['TYPE']."</option>";
  }

$opt .= "</select>";

        $query = "SELECT ID, e.FIRST_NAME, e.LAST_NAME, e.GENDER, USERNAME, PASSWORD, e.EMAIL, PHONE_NUMBER, j.JOB_TITLE, e.HIRED_DATE, t.TYPE, l.PROVINCE, l.CITY
                      FROM users u
                      join employee e on u.EMPLOYEE_ID = e.EMPLOYEE_ID
                      join job j on e.JOB_ID=j.JOB_ID
                      join location l on e.LOCATION_ID=l.LOCATION_ID
                      join type t on u.TYPE_ID=t.TYPE_ID
                      WHERE ID =".$_SESSION['MEMBER_ID'];
        $result = mysqli_query($db, $query) or die(mysqli_error($db));
          while($row = mysqli_fetch_array($result))
          {  
                $zz= $row['ID'];
                $a= $row['FIRST_NAME'];
                $b=$row['LAST_NAME'];
                $c=$row['GENDER'];
                $d=$row['USERNAME'];
                $e=$row['PASSWORD'];
                $f=$row['EMAIL'];
                $g=$row['PHONE_NUMBER'];
                $h=$row['JOB_TITLE'];
                $i=$row['HIRED_DATE'];
                $j=$row['PROVINCE'];
                $k=$row['CITY'];
                $l=$row['TYPE'];
          }
      ?>

  <!-- User Edit Info Modal-->
  <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit User Info</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form role="form" method="post" action="settings_edit.php">
              <input type="hidden" name="id" value="<?php echo $zz; ?>" />

              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 First Name:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="First Name" name="firstname" value="<?php echo $a; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Last Name:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="Last Name" name="lastname" value="<?php echo $b; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Gender:
                </div>
                <div class="col-sm-9">
                  <select class='form-control' name='gender' required>
                    <option value="" disabled selected hidden>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Username:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="Username" name="username" value="<?php echo $d; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Password:
                </div>
                <div class="col-sm-9">
                  <input type="password" class="form-control" placeholder="Password" name="password" value="" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Email:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="Email" name="email" value="<?php echo $f; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Contact #:
                </div>
                <div class="col-sm-9">
                   <input class="form-control" placeholder="Contact #" name="phone" value="<?php echo $g; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Role:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="Role" name="role" value="<?php echo $h; ?>" readonly>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Hired Date:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="Hired Date" name="hireddate" value="<?php echo $i; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 Province:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="Province" name="province" value="<?php echo $j; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                 City / Municipality:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="City / Municipality" name="city" value="<?php echo $k; ?>" required>
                </div>
              </div>
              <div class="form-group row text-left text-primary">
                <div class="col-sm-3" style="padding-top: 5px;">
                  Account Type:
                </div>
                <div class="col-sm-9">
                  <input class="form-control" placeholder="Account Type" name="type" value="<?php echo $l; ?>" readonly>
                </div>
              </div>
              <hr>
            <button type="submit" class="btn btn-success"><i class="fa fa-check fa-fw"></i>Save</button>
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>      
          </form>  
        </div>
      </div>
    </div>
  </div>