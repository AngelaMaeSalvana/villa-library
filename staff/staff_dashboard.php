<?php

include "../auth.php";  

if(!isset($_SESSION["staff_name"])) {
    header("location: ../auth.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./staff.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" ><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly "><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong> 
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item active"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_borrow_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Borrow</a> </li>
            <li class="nav-item"> <a href="./staff_return.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Return</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" class="nav-link link-body-emphasis" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
        
        
    </div>
    <div class="board container"><!--board container--> 
        <div class="header">
            <div class="text">
                <div class="title">
                    <h2>Dashboard</h2>
                </div>
                <div class="datetime">
                    <p id = "currentDate"></p>
                    <p id = "currentTime"></p>
                </div>
            </div>
          

        </div>
        <div class="content">
            <div class="overview">
                <h3>Overview</h3> 
                <div class="ovw-con">
                    <div class="totalbooks">  
                        <?php
                                 $conn =  mysqli_connect("localhost","root","","db_library_2", 3308); //database connection
    
                                $totalBooks = "Select * from tbl_books";
                                $totalBooks_run = mysqli_query($conn, $totalBooks); 
                                if($totalBooks_count = mysqli_num_rows($totalBooks_run))
                                {
                                    echo "<h3>"  .$totalBooks_count." </h3>";
                                }
                                else
                                {
                                    echo "<h3>0</h3>";
                                }
                            ?> 
                        <div class="cap">
                            <i class='bx bxs-book'></i>
                            <h5>Total Books</h5>
                        </div>
                        
                    </div>
                    <div class="line"></div>
                    <div class="totalvisits">
                         <?php
                                $totalVisits = "Select * from tbl_log";
                                $totalVisits_run = mysqli_query($conn, $totalVisits); 
                                $currenDate = date("YYYY-mm-dd");
                                
                                if($totalVisits_count = mysqli_num_rows($totalVisits_run)) {
                                    echo "<h3>"  .$totalVisits_count." </h3>";
                                } else {
                                    echo "<h3>0</h3>";
                                }
                            ?>
                        <div class="cap">
                            <i class='bx bxs-book-reader'></i>
                            <h5>Total Visits</h5>
                        </div>
                        
                    </div>
                </div> 
            </div>
            
            <div class="duebooks">
                <h3>Due Today</h3>
                
                <div class="duebooks-con">

                <?php
                             
$totalVisits = "SELECT
bd.BorrowDetails_ID, 
b.User_ID, 
b.Accession_Code, 
bk.Book_Title, 
bd.Quantity, 
b.Date_Borrowed, 
b.Due_Date, 
br.Borrower_ID, 
bd.tb_status, 
br.First_Name, 
br.Last_Name
FROM
tbl_borrowdetails AS bd
INNER JOIN
tbl_borrow AS b
ON 
    bd.Borrower_ID = b.Borrower_ID
INNER JOIN
tbl_books AS bk
ON 
    b.Accession_Code = bk.Accession_Code
INNER JOIN
tbl_borrower AS br
ON 
    bd.Borrower_ID = br.Borrower_ID
WHERE
b.Due_Date = CURDATE();
";

$totalVisits_run = mysqli_query($conn, $totalVisits);

if ($totalVisits_run) {
$totalVisits_count = mysqli_num_rows($totalVisits_run);
// Display each visit's details
echo '<div style="overflow-x: auto;">'; // Container for scrollable effect
echo '<table>';
while ($row = mysqli_fetch_assoc($totalVisits_run)) {
    
    echo '<tr><td></td><td><strong>' . $row['Book_Title'] . '</strong></td></tr>';
    echo '<tr><td><strong></strong></td><td>' . $row['First_Name']. ' ' . $row['Last_Name'] . '</td></tr>';
    
}

echo '</table>';
echo '</div>'; // Close the container


} else {
echo "<h4>No Due date today</h4>";
}
                            ?>
                </div>
            </div>
            <div class="stats">
                <h3>Statistics</h3>
                <div class="stats-con"></div>
            </div>
            <div class="newbooks">
                <h3>What's New?</h3>
                <div class="newbooks-con"></div>
            </div>

                
        </div>
        

    </div>

    <!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Logging Out...</h1>
      </div>
      <div class="modal-body">
        Do you want to log out?
      </div>
      <div class="modal-footer d-flex flex-row justify-content-center">
        <a href="javascript:history.go(0)"><button type="button" class="btn" data-bs-dismiss="modal">Cancel</button></a>
        <a href="../logout.php"><button type="button" class="btn">Log Out</button></a>
      </div>
    </div>
  </div>
</div>
        

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script> 
        let date = new Date().toLocaleDateString('en-US', {  
            day:   'numeric',
            month: 'long',
            year:  'numeric' ,  
            weekday: 'long', 
        });   
        document.getElementById("currentDate").innerText = date; 

        setInterval( () => {
            let time = new Date().toLocaleTimeString('en-US',{ 
            hour: 'numeric',
            minute: 'numeric', 
            second: 'numeric',
            hour12: 'true',
        })  
        document.getElementById("currentTime").innerText = time; 

        }, 1000)
        

        let navItems = document.querySelectorAll(".nav-item");  //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', ()=> { 
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');
                
                
            })
            
        })
     


    </script>
</body>
</html>