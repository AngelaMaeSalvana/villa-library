<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

if (isset($_POST['submit'])) {
    try {
        // Retrieve form data
        $bookTitle = $_POST['bookTitle'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $edition = $_POST['edition'];
        $year = $_POST['year'];
        $quantity = $_POST['quantity'];
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $country = $_POST['country'];

        
     // Additional data from form fields
    $name = $_POST['add_name'];
    $address = $_POST['add_address'];
    $email = $_POST['add_email'];
    $contact = $_POST['add_contact'];
        $type = $_POST['type']; 

        // Retrieve selected section and shelf number from hidden input fields
        $selectedSection = $_POST['selectedSection'];
        $selectedShelf = $_POST['selectedShelf'];

        $isbn = 1;
        $bib = "N/A";

        // Retrieve the custom Accession Code
        $customAccessionCode = $_POST['accessionCode'];

        // Check if custom Accession Code is provided
        if (!empty($customAccessionCode)) {
            // Use the provided custom Accession Code
            $customAccessionCode = floatval($customAccessionCode);
        } else {
            // Generate a new random 6-digit value
            $randomValue = rand(100000, 999999); // Generate random value between 100000 and 999999
            $customAccessionCode = floatval($randomValue . '.2');
        }

        // Handle author input
        if ($_POST['author'] === 'Other') {
            // Use the value from the "New Author" input field
            $author = $_POST['newAuthor'];
        } else {
            // Use the selected author from the dropdown
            $author = $_POST['author'];
        }

        if (empty($bookTitle) || empty($author) || empty($publisher) || empty($quantity) || empty($edition) || empty($year) || empty($price) || !is_numeric($year) || $year < 0 || !is_numeric($price) || $price < 0 || !is_numeric($quantity) || $quantity < 0 || empty($selectedSection) || empty($selectedShelf)) {
            $errorMessage = "Please fill in all required fields with valid data.";
        
            // Log validation failure
            error_log("Validation failed: $errorMessage");
        
            // Log individual data for debugging
            error_log("Book Title: " . $bookTitle);
            error_log("Author: " . $author);
            error_log("Publisher: " . $publisher);
            error_log("Quantity: " . $quantity);
            error_log("Edition: " . $edition);
            error_log("Year: " . $year);
            error_log("Price: " . $price);
            error_log("Country: " . $country);
            error_log("Selected Section: " . $selectedSection);
            error_log("Selected Shelf: " . $selectedShelf);
        } else {
            // Handle "Other Edition" input
            if ($edition === "Other") {
                // Check if the 'otherEdition' input is set and not empty
                if (isset($_POST['otherEdition']) && !empty($_POST['otherEdition'])) {
                    $edition = $_POST['otherEdition']; // Use the input value for edition
                } else {
                    $errorMessage = "Please provide a value for Other Edition.";
                }
            }

            // Check if the book already exists based on title and edition
            $checkDuplicateBookSql = "SELECT * FROM tbl_books WHERE Book_Title = '$bookTitle' AND tb_edition = '$edition'";
            $result = $conn->query($checkDuplicateBookSql);

            if ($result->num_rows > 0) {
                // Book already exists, update the quantity
                $row = $result->fetch_assoc();
                $existingQty = $row['Quantity'];
                $newQty = $existingQty + $quantity;

                $updateQuantitySql = "UPDATE tbl_books SET Quantity = '$newQty' WHERE Book_Title = '$bookTitle' AND tb_edition = '$edition'";
                if ($conn->query($updateQuantitySql) !== TRUE) {
                    throw new Exception("Error updating quantity: " . $conn->error);
                }
            } else {
                // Check if the author already exists in tbl_authors
                $checkAuthorSql = "SELECT Authors_ID FROM tbl_authors WHERE Authors_Name = '$author'";
                $authorResult = $conn->query($checkAuthorSql);

                if ($authorResult->num_rows > 0) {
                    // Author already exists, retrieve their ID
                    $authorRow = $authorResult->fetch_assoc();
                    $authorsID = $authorRow['Authors_ID'];
                } else {
                    // Author doesn't exist, insert the new author into tbl_authors
                    $authorsID = substr(uniqid('A_', true), -6); // Generate Authors_ID
                    $insertAuthorSql = "INSERT INTO tbl_authors (Authors_ID, Authors_Name, Nationality) 
                                        VALUES ('$authorsID', '$author', '$country')";

                    if ($conn->query($insertAuthorSql) !== TRUE) {
                        throw new Exception("Error inserting author: " . $conn->error);
                    }
                }

                // Proceed with insertion
                $insertBookSql = "INSERT INTO tbl_books (Accession_Code, Book_Title, Authors_ID, Publisher_Name, Section_Code, shelf, tb_edition, Year_Published, ISBN, Bibliography, cl_type, Quantity, Price, tb_status) 
                VALUES ('$customAccessionCode','$bookTitle', '$authorsID', '$publisher', '$selectedSection', '$selectedShelf', '$edition', '$year', '$isbn', '$bib', '$type','$quantity', '$price', 'Available')";
              
                if ($conn->query($insertBookSql) !== TRUE) {
                    throw new Exception("Error inserting book: " . $conn->error);
                }else{
                    $insertContributor = "INSERT INTO tbl_contributor (Accession_Code, Name, Address, Email, Contact_Number)
                    VALUES ('$customAccessionCode','$name','$address','$email','$contact')";
                     
                     if ($conn->query($insertContributor) === TRUE) {
                        echo '<script>console.log("Update Success");</script>';
                        
                    } else {
                        echo "Error updating request status: " . $conn->error;
                    }

                }
            }

            // Display success message
            echo '<script>alert("Book Added Successfully!");</script>';
            echo '<script>window.location.href = "admin_books.php";</script>';
        }
    } catch (Exception $e) {
        $errorMessage = "An error occurred: " . $e->getMessage();
        
        // Log error message
        error_log($errorMessage);
    }
}



?>


<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding of Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head> 
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <?php
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
            $userID = $_SESSION["User_ID"];
            $sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, image_data 
                        FROM tbl_employee 
                        WHERE User_ID = $userID";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                echo "Error: " . mysqli_error($conn);
            } else {
                $userData = mysqli_fetch_assoc($result);
                // Fetch the First_Name from $userData
                $firstName = $userData['First_Name'];
                $role = $userData['tb_role'];
            }
            ?>
            <?php if (!empty($userData['image_data'])) : ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo  $firstName . "<br/>" .  $role; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-report'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bx-log-out'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board1 container-fluid"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="back-btn">
                    <a href="./admin_books.php"><i class='bx bx-arrow-back'></i></a>
                </div>
                <div class="title">
                    <h2>Add Book</h2>
                </div>
            </div>
        </div>
        <div class="books container-fluid">
            <!-- Display success or error message -->
            <div class="container">
                <?php if (!empty($successMessage)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form id="bookForm" method="POST" action="">
                <div class="mb-3">
                    <label for="accessionCode" class="form-label">Accession Code</label>
                    <input type="text" class="form-control" id="accessionCode" name="accessionCode" 
                        placeholder="You can leave this field empty to generate unqiue Accession Code">
                </div>

                <input type="hidden" name="userID" value="<?php echo $_SESSION['User_ID']; ?>">

                <div class="mb-3">
                    <label for="bookTitle" class="form-label">Book Title</label>
                    <input type="text" class="form-control" id="bookTitle" name="bookTitle" required>
                </div>


                <?php
                // Database connection
                $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $query = "SELECT Authors_Name, Authors_ID FROM tbl_authors";
                $result = mysqli_query($conn, $query);

                $existingAuthors = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $existingAuthors[] = $row['Authors_Name'];
                }

                // Add an "Other" option to the existing authors
                $existingAuthors[] = "Other";
                ?>

                <div class="mb-3">
                    <label for="authorSelect" class="form-label">Author</label>
                    <select class="form-select" id="author" name="author" required>
                        <option value="" disabled selected>Select an author</option>
                        <?php foreach ($existingAuthors as $authorOption) : ?>
                            <option value="<?php echo $authorOption; ?>"><?php echo $authorOption; ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="mb-3" id="newAuthorInput" style="display: none;">
                    <label for="newAuthor" class="form-label">New Author</label>
                    <input type="text" class="form-control" id="newAuthor" name="newAuthor">

                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="country" name="country">
                </div>

                <div class="mb-3">
                    <label for="publisher" class="form-label">Publisher</label>
                    <input type="text" class="form-control" id="publisher" name="publisher" required>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year Published</label>
                    <input type="text" class="form-control" id="year" name="year" required>
                </div>
                <div class="mb-3">

                    <label for="edition" class="form-label">Edition</label>
                    <select class="form-select" id="edition" name="edition" onchange="toggleOtherEdition()" required>
                        <option value="First Edition">First Edition</option>
                        <option value="Second Edition">Second Edition</option>
                        <option value="Third Edition">Third Edition</option>
                        <option value="Fourth Edition">Fourth Edition</option>
                        <option value="Other">Other</option>
                    </select>

                    <div id="otherEditionContainer" class="mb-3" style="display: none;">
                        <label for="otherEdition" class="form-label">Other Edition</label>
                        <input type="text" class="form-control" id="otherEdition" name="otherEdition">
                    </div>

                </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Price</label>
                        <input type="text" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>

                    <?php
                    $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

                    // Check connection
                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }

                    // Fetch sections from tbl_section
                    $sql_sections = "SELECT Section_Code, Section_Name FROM tbl_section";
                    $result_sections = mysqli_query($conn, $sql_sections);

                    // Check if sections were fetched successfully
                    if ($result_sections && mysqli_num_rows($result_sections) > 0) {
                        echo '<div class="form-group">';
                        echo '<label for="section" class="form-label">Section:</label>';
                        echo '<select id="section" name="section" class="form-select" required>';
                        echo '<option value="">Select Section</option>';

                        // Display options for each section
                        while ($row = mysqli_fetch_assoc($result_sections)) {
                            echo '<option value="' . $row['Section_Code'] . '">' . $row['Section_Name'] . '</option>';
                        }

                        echo '</select>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-warning" role="alert">No sections found</div>';
                    }
                    // Close connection
                    mysqli_close($conn);
                    ?> 
                    <br>
                    <div class="form-group">
                        <label for='shelf' class="form-label">Shelf</label>
                        <div id="shelfContainer" class="input-group"></div>

                        <input type="hidden" id="selectedSection" name="selectedSection">
                        <input type="hidden" id="selectedShelf" name="selectedShelf">
                    </div>
                    <br>

                    <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="Procured" selected>Supplier Procured</option>
                        <option value="Donated">Donated</option>
                    </select>
                </div>

                    <div class="mb-3">
                <label for="add_name" class="form-label">Name</label>
                <input type="text" class="form-control" id="add_name" name="add_name" required>
            </div>

            <div class="mb-3">
                <label for="add_address" class="form-label">Address</label>
                <input type="text" class="form-control" id="add_address" name="add_address" required>
            </div>

            <div class="mb-3">
                <label for="add_email" class="form-label">Email</label>
                <input type="text" class="form-control" id="add_email" name="add_email" required>
            </div>

            <div class="mb-3">
                <label for="add_contact" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="add_contact" name="add_contact" required>
            </div>

         
            <a href="admin_books.php" class="btn btn-primary">Cancel</a>


                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
            </form>

        </div>

    </div>

    <!--Logout Modal -->
    <div class="modal fade" id="logOut" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Event listener for section dropdown change
            $('#section').change(function() {
                var sectionCode = $(this).val();
                console.log('Selected Section Code:', sectionCode); // Log the selected section code

                // AJAX request to fetch shelf numbers
                $.ajax({
                    url: 'queries/shelfs.php', // Update the URL to your PHP script
                    method: 'POST',
                    data: {
                        sectionCode: sectionCode
                    },
                    dataType: 'html',
                    success: function(response) {
                        console.log('Shelf Numbers Response:', response); // Log the response from the server
                        // Update shelf container with fetched shelf numbers
                        $('#shelfContainer').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching shelf numbers:', error);
                    }
                });
            });

            // Event listener for form submission
            $('form').submit(function() {
                // Get selected section and shelf number
                var selectedSection = $('#section').val();
                var selectedShelf = $('#shelf').val();

                console.log('Selected Section:', selectedSection); // Log the selected section
                console.log('Selected Shelf:', selectedShelf); // Log the selected shelf number

                // Set hidden input values
                $('#selectedSection').val(selectedSection);
                $('#selectedShelf').val(selectedShelf);
            });
        });
    </script>

    <!-- JavaScript to toggle input field -->
    <script>
        document.getElementById("author").addEventListener("change", function() {
            const newAuthorInput = document.getElementById("newAuthorInput");
            newAuthorInput.style.display = (this.value === "Other") ? "block" : "none";
        });
    </script>


    <script>
        function toggleOtherEdition() {
            const editionSelect = document.getElementById('edition');
            const otherEditionContainer = document.getElementById('otherEditionContainer');
            const otherEditionInput = document.getElementById('otherEdition');

            if (editionSelect.value === 'Other') {
                otherEditionContainer.style.display = 'block'; // Show the input field
                otherEditionInput.required = true; // Make the input field required
            } else {
                otherEditionContainer.style.display = 'none'; // Hide the input field
                otherEditionInput.required = false; // Make the input field optional
                otherEditionInput.value = ''; // Clear the input field value
            }
        }
    </script>


</body>

</html>