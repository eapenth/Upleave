<!DOCTYPE html>
<html lang="en">
<?php
include 'conn.php';
if(isset($_SESSION['empid'])) {

$x=$_SESSION['name'];
$empid=$_SESSION['empid'];
$deptid=$_SESSION['deptid'];
$leaveSuccessMessage = '';
$leaveFailureMessage = '';
if(isset($_POST['submit'])){
    
    $lid=5;
    $fromd=$_POST['from_date'];
    $tod=$_POST['to_date'];
    if($tod){
        $date2 = new DateTime($tod);
        $date1 = new DateTime($fromd);
    $interval = $date1->diff($date2);
    $daysDiff = $interval->format('%a');
    if($daysDiff == 0){
        $daysDiff = 1;
    }
    }else{
        $daysDiff = 0.5;
        $tod = $fromd;
    }
    $currentDateTime = date("Y-m-d H:i:s");
    $session="Full Day";
    $reason=$_POST['assignment_duty'];
     $duty_order=$_FILES['duty_order'];
     $fileDestination="";
 if(isset($_FILES['duty_order']) && $_FILES['duty_order']['error'] === UPLOAD_ERR_OK) {
    $filename = $_FILES['duty_order']['name'];
    $tempFilePath = $_FILES['duty_order']['tmp_name'];
         $fileDestination = 'docu/' . $filename;


    // Move uploaded file to destination directory
    if(move_uploaded_file($tempFilePath, $fileDestination)) {
        // File uploaded successfully, proceed with form submission
        // Now you can use $fileDestination to store the file path in your database

        // Proceed with form submission
    } else {
        // Failed to move file, handle the error accordingly
        $leaveFailureMessage = 'Failed to upload file.';
    }
} 

    // Check if start date is Sunday
    if (date('D', strtotime($fromd)) === 'Sun') {
        $leaveFailureMessage = 'Leaves cannot be applied on Sundays. Please select another date.';
    } elseif(empty($lid) || empty($fromd) || empty($tod)) {
        $leaveFailureMessage = 'Leave type, start date, and end date cannot be empty.';
    } elseif(strtotime($fromd) < strtotime(date('Y-m-d'))) {
        $leaveFailureMessage = 'Cannot apply leave for previous dates.';
    } elseif(strtotime($tod) < strtotime($fromd)) {
        $leaveFailureMessage = 'End date cannot be earlier than start date.';
    } else {
        //$sql = "INSERT INTO tbl_leave (emp_id, l_id, st_date, to_date, document,reason,status) VALUES ('$empid','5', '$fromd', '$tod', '$fileDestination','$reason','0');";
                $sql = "INSERT INTO tbl_leave (emp_id, l_id,session,st_date, to_date, document,reason,status,appliedtime,daydiff) VALUES ('$empid', '$lid', '$session', '$fromd', '$tod', '$fileDestination','$reason','0','$currentDateTime','$daysDiff ');";
  
        if($conn->query($sql) == true){
            $lq="select max(req_id) as req_id from tbl_leave";
            $req=$conn->query($lq);
            $row =$req->fetch_assoc();
            $reqid = $row['req_id'];
            $idql = "insert into tbl_docs (req_id,emp_id,doc1,status)values('$reqid','$empid','$fileDestination','0');";
            if($conn->query($idql)){
            }
            $leaveSuccessMessage = 'Leave applied successfully';
        } else{
            $leaveFailureMessage = 'Leave application failed';
        }
    }
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Apply</title>
    
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    
    <script defer src="assets/fontawesome/js/all.min.js"></script>
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .custom-width {
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="app">
        <div id="sidebar" class='active'>
            <div class="sidebar-wrapper active">
                <div class="sidebar-header" style="height: 50px;margin-top: -30px">
                    <i class="fa fa-users text-success me-4"></i>
                    <span id="span01" style="font-size: smaller;">UPLeave</span>

                </div>
               <div class="sidebar-menu">
                  <ul class="menu">
                     <li class="sidebar-item ">
                        <a href="employee.php" class='sidebar-link'>
                        <i class="fa fa-home text-success"></i>
                        <span>Dashboard</span>
                        </a>
                     </li>
                     <li class="sidebar-item ">
                        <a href="apply_leave.php" class='sidebar-link'>
                        <i class="fa fa-plane text-success"></i>
                        <span>Apply Leave</span>
                        </a>
                     </li>
                     <li class="sidebar-item ">
                        <a href="cancel_leave.php" class='sidebar-link'>
                        <i class="fa fa-plane text-success"></i>
                        <span>Cancel Leave Request</span>
                        </a>
                     </li>
                     <li class="sidebar-item ">
                        <a href="leave_status.php" class='sidebar-link'>
                        <i class="fa fa-plane text-success"></i>
                        <span>Leave Status</span>
                        </a>
                     </li>
                     <li class="sidebar-item active">
                        <a href="leavelist.php" class='sidebar-link'>
                        <i class="fa fa-plane text-success"></i>
                        <span>Permissions</span>
                        </a>
                     </li>
                       <li class="sidebar-item ">
                        <a href="leavehistory.php" class='sidebar-link'>
                        <i class="fa fa-plane text-success"></i>
                        <span>Leave history</span>
                        </a>
                     </li>
                      <li class="sidebar-item ">
                        <a href="notifications.php" class='sidebar-link'>
                        <i class="fa fa-plane text-success"></i>
                        <span>Notifications <?php echo $_SESSION['not'];?></span>
                        </a>
                     </li>
                  </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main">
            <!-- 3 lines portion-->
            <nav class="navbar navbar-header navbar-expand navbar-light">
              <a class="sidebar-toggler" href="#"><span class="navbar-toggler-icon"></span></a>
                <button class="btn navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                 <!-- 3 lines portion ends here-->
                  <!-- notification-->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav d-flex align-items-center navbar-light ms-auto">
                        <li class="dropdown nav-icon">
                            <a href="#" data-bs-toggle="dropdown"
                                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                               
                            </a>
                          
                        </li>
                        <li class="dropdown">
                            <a href="#" data-bs-toggle="dropdown"
                                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                <div class="avatar me-1">
                                    <img src="assets/images/admin.png" alt="" srcset="" />
                                </div>
                                <div class="d-none d-md-block d-lg-inline-block">
                                <?php echo $x."<br>".$empid ?>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="evacc.php"><i data-feather="user"></i> Account</a>
                                <a class="dropdown-item" href="update_password.php"><i data-feather="settings"></i> Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php"><i data-feather="log-out"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>  
            </nav>
            
            <div class="main-content container-fluid">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Apply for Duty Leave</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class='breadcrumb-header'>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="employee.php" class="text-success">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Leave Application</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Basic multiple Column Form section start -->
                <section id="multiple-column-form">
                    <div class="row match-height">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
<form class="form" name="submit" action="#" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
    <!-- Number of Days -->
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group has-icon-left">
                <label for="no-of-days">Number of Days</label>
                <input type="number" class="form-control" name="no_of_days" id="no-of-days" oninput="updateDateFields()">
                <span id="noOfDaysValidation" style="color: red;"></span>
            </div>
        </div>
    </div>

    <!-- Date Fields -->
    <div class="row" id="dateFieldsRow" >
        <div class="col-md-6 col-6">
            <div class="form-group has-icon-left">
                <label for="from-date" id="from-date-label">Select Date</label>
                <input type="date" class="form-control" name="from_date" id="from-date" min="2024-02-19" onkeydown="return false" oninput="validateFromDate()">
                <span id="fromDateValidation" style="color: red;"></span>
            </div>
        </div>
        <div class="col-md-6 col-6" id="toDateCol" style="display: none;">
            <div class="form-group has-icon-left">
                <label for="to-date">To Date</label>
                <input type="date" class="form-control" name="to_date" id="to-date" onkeydown="return false" oninput="validateToDate()">
                <span id="toDateValidation" style="color: red;"></span>
            </div>
        </div>
    </div>

    <!-- Assignment Duty -->
    <div class="row">
        <div class="col-12">
            <div class="form-group has-icon-left">
                <label for="assignment-duty">Assignment Duty</label>
                <input type="text" class="form-control" name="assignment_duty" id="assignment-duty" style="width: 490px;"  oninput="validateAssignmentDuty()">
                <span id="assignmentDutyValidation" style="color: red;"></span>
            </div>
        </div>
    </div>

    <!-- Duty Order Upload -->
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group has-icon-left" id="fileUpload">
                <label for="file-upload">Upload Duty Order</label>
                <input type="file" class="form-control-file" name="duty_order" id="file-upload" onblur="validateFileUpload()">
            </div>
            <span id="fileValidation" style="color: red;"></span>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button type="submit" name="submit" class="btn btn-primary me-1 mb-1">Submit</button>
        </div>
    </div>
    </form>

    <!-- Leave Status Messages -->
    <span style="color: green;"><?php echo $leaveSuccessMessage; ?></span>
    <span style="color: red;"><?php echo $leaveFailureMessage; ?></span>

    <!-- JavaScript for Dynamic Behavior -->
    <script>
        function updateDateFields() {
            var noOfDays = document.getElementById('no-of-days').value;
            var dateFieldsRow = document.getElementById('dateFieldsRow');
            var toDateCol = document.getElementById('toDateCol');
            var fromDateLabel = document.getElementById('from-date-label');

            if (noOfDays > 1) {
                dateFieldsRow.style.display = 'flex';
                toDateCol.style.display = 'block';
                fromDateLabel.textContent = 'From Date';
            } else {
                // dateFieldsRow.style.display = 'none';
                toDateCol.style.display = 'none';
                fromDateLabel.textContent = ' Select Date';
            }
        }

        function validateFromDate() {
            var fromDate = document.getElementById('from-date').value;
            var fromDateValidation = document.getElementById('fromDateValidation');
            var today = new Date().toISOString().split('T')[0];
            if (fromDate < today) {
                fromDateValidation.textContent = 'From date cannot be in the past.';
            } else {
                fromDateValidation.textContent = '';
            }
        }

        function validateToDate() {
            var toDate = document.getElementById('to-date').value;
            var fromDate = document.getElementById('from-date').value;
            var toDateValidation = document.getElementById('toDateValidation');
            if (toDate < fromDate) {
            if(toDate == ''){
                toDateValidation.textContent = '';
            }else{
                toDateValidation.textContent = 'To date cannot be before from date.';

            }
            } else {
                  var date1 = new Date(fromDate);
                var date2 = new Date(toDate);
                var difference = Math.abs(date2 - date1);
                var daysDifference = Math.ceil(difference / (1000 * 60 * 60 * 24));
                document.getElementById('no-of-days').value = daysDifference;
                toDateValidation.textContent = '';
            }
        }

        function validateAssignmentDuty() {
            var assignmentDuty = document.getElementById('assignment-duty').value;
            var assignmentDutyValidation = document.getElementById('assignmentDutyValidation');
            if (assignmentDuty.trim() === '') {
                assignmentDutyValidation.textContent = 'Assignment duty cannot be empty.';
            } else {
                assignmentDutyValidation.textContent = '';
            }
        }

        function validateFileUpload() {
            var fileUpload = document.getElementById('file-upload').value;
            var fileValidation = document.getElementById('fileValidation');
            if (fileUpload === '') {
                fileValidation.textContent = 'Please upload a duty order.';
            } else {
                fileValidation.textContent = '';
            }
        }

        function validateForm() {
            updateDateFields();
            validateFromDate();
            validateToDate();
            validateAssignmentDuty();
            validateFileUpload();

            var noOfDaysValidation = document.getElementById('noOfDaysValidation').textContent;
            var fromDateValidation = document.getElementById('fromDateValidation').textContent;
            var toDateValidation = document.getElementById('toDateValidation').textContent;
            var assignmentDutyValidation = document.getElementById('assignmentDutyValidation').textContent;
            var fileValidation = document.getElementById('fileValidation').textContent;

            if (noOfDaysValidation !== '' || fromDateValidation !== '' || toDateValidation !== '' || assignmentDutyValidation !== '' || fileValidation !== '') {
                return false;
            }

            return true;
        }
    </script>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Basic multiple Column Form section end -->
            </div>
        </div>
    </div>
   
    <script src="assets/js/feather-icons/feather.min.js"></script>
    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php
}else{
    header('Location: login.php');
    exit();
}?>