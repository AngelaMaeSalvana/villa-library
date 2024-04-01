<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="../styles.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>
<body>
    <div class="main-wrap container-fluid">
        <div class="main-con row ">
            <div class="img-sec col-7">
                <img src="https://villanuevamisor.gov.ph/wp-content/uploads/2022/11/Villanueva-Municipal-Government-Association_LGU_Region-10-1024x692.jpg" alt="Library">
            </div>
            <div class="form-sec col-5">
                <div class="title">
                    <h1><strong>Villa<span>Read</span>Hub</strong></h1>
                <img src="../images/lib-icon.png" alt="lib-icon"/>
                </div>

                <div class="error-con">
                <?php
                    // Check if an error message is passed in the URL
                    if (isset($_GET['error'])) {
                        $error = $_GET['error'];
                        echo "<p class='error-message'>$error</p>";
                    }
                    ?>
                </div>
                <div class="form-con">
                    <p>A verification code has been sent to your email address.</p>
                    <form method="POST">    
                    <div class="input-con">
                        <input type="number" >
                        <input type="number" disabled >
                        <input type="number" disabled >
                        <input type="number" disabled >
                        <input type="number" disabled >
                        <input type="number" disabled >
                    </div>
                    <br/> 
                    <div class="btn-container row">
                        <button class="vrf-btn active" name="sendCode"  type="submit" >Verify</button> 
                        <a href="../index.php"  >Cancel</a>
                        
                    </div>
                    

                    
                    </form>
                </div>
                
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
<script>

</script>
 
</body>
</html> 