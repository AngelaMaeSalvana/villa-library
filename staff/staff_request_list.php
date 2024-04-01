<?php
include '../auth.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillaReadHub - Books</title>
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
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2> 
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon"/>
        </a><!--header container--> 
        <div class="user-header mt-4 d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <img src="https://github.com/mdo.png" alt="" width="50" height="50" class="rounded-circle me-2">
            <strong><span><?php echo $_SESSION["staff_name"] ."<br/>"; echo $_SESSION["role"]; ?></span> </strong> 
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./staff_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active"> <a href="./staff_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./staff_borrow_dash.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Borrow</a> </li>
            <li class="nav-item"> <a href="./staff_return.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Return</a> </li>
            <li class="nav-item"> <a href="./staff_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./staff_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <hr>
            <li class="nav-item"> <a href="./staff_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="../logout.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
         
    </div>
    
    <div class="board container"><!--board container-->
    <div class="header1">
            <div class="text">
                <div class="title">
                    <h2>Request List</h2>
                </div>
            </div>
            <div class="searchbar">
                <form action="">
                <input type="search" id="searchInput" placeholder="Search..." required>
                <i class='bx bx-search' id="search-icon"></i>
            </form>
            </div>
    </div>

    <div class="books container">
    <table class="table table-hover table-sm">
    <thead>
            <tr>
            <th>Request ID</th>
            <th>User ID</th>                 
            <th>Book Title</th>
            <th>Authors ID</th>
            <th>Publisher ID</th>
            <th>Price</th>
            <th>Edition</th>
            <th>Year Published</th>
            <th>Quantity</th>
            <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $conn =  mysqli_connect("localhost","root","","db_library_2", 3308); 
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                 // SQL query
                 $sql = "SELECT Request_ID, User_ID, Book_Title, Authors_ID, Publisher_ID, price, tb_edition, Year_Published, Quantity, tb_status FROM tbl_requestbooks";
                 $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>".$row["Request_ID"]."</td>
                                <td>".$row["User_ID"]."</td>
                                <td>".$row["Book_Title"]."</td>
                                <td>".$row["Authors_ID"]."</td>
                                <td>".$row["Publisher_ID"]."</td>
                                <td>".$row["price"]."</td>
                                <td>".$row["tb_edition"]."</td>
                                <td>".$row["Year_Published"]."</td>
                                <td>".$row["Quantity"]."</td>
                                <td>".$row["tb_status"]."</td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "0 results";
                }


            // Close connection
            $conn->close();
            ?>
        </tbody> 
    </table>

    </div>

    <div class="container mt-3">
        <button class="btn" id="requestButton">Request New Book</button>
    </div>
</div>



<script>
    // JavaScript code for search functionality
    document.getElementById("searchInput").addEventListener("input", function() {
        let searchValue = this.value.toLowerCase();
        let rows = document.querySelectorAll("tbody tr");
        rows.forEach(row => {
            let cells = row.querySelectorAll("td");
            let found = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchValue)) {
                    found = true;
                }
            });
            if (found) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });


    document.getElementById("requestButton").addEventListener("click", function() {
        window.location.href = "staff_request_form.php";
    });


    let navItems = document.querySelectorAll(".nav-item");  //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', ()=> { 
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');
                
                
            })
            
        })
     
</script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
</body>
</html>