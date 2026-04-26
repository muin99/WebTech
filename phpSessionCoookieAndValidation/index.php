<?php
session_start();

function test_input($data) {
    return trim($data);
}

function clear_login_data() {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    setcookie("student_data", '', time() - 3600, "/");
    session_destroy();
}

if (isset($_POST["logout"])) {
    clear_login_data();
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}

$fullName = $email = $username = $password = $confirmPassword = $age = $gender = $course = "";
$terms = "";

$fullNameErr = $emailErr = $usernameErr = $passwordErr = $confirmPasswordErr = "";
$ageErr = $genderErr = $courseErr = $termsErr = "";
$success = "";
$submittedDetails = [];

$loggedIn = isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;

if (!$loggedIn && isset($_COOKIE["student_data"])) {
    $cookieData = json_decode($_COOKIE["student_data"], true);
    if (is_array($cookieData) && !empty($cookieData["Full Name"])) {
        $_SESSION["logged_in"] = true;
        $_SESSION["student_details"] = $cookieData;
        $loggedIn = true;
    }
}

if ($loggedIn && isset($_SESSION["student_details"])) {
    $submittedDetails = $_SESSION["student_details"];
    $success = "Logged in successfully!";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["logout"]) && !$loggedIn) {
    if (empty($_POST["full_name"])) {
        $fullNameErr = "Full Name is required";
    } else {
        $fullName = test_input($_POST["full_name"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $fullName)) {
            $fullNameErr = "Full Name must contain only letters and spaces";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email Address is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Please enter a valid email address";
        }
    }

    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
        if (strlen($username) < 5) {
            $usernameErr = "Username must be at least 5 characters long";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
        if (strlen($password) < 6) {
            $passwordErr = "Password must be at least 6 characters long";
        }
    }

    if (empty($_POST["confirm_password"])) {
        $confirmPasswordErr = "Confirm Password is required";
    } else {
        $confirmPassword = $_POST["confirm_password"];
        if (!empty($password) && $password !== $confirmPassword) {
            $confirmPasswordErr = "Password and Confirm Password must match";
        }
    }

    if (empty($_POST["age"])) {
        $ageErr = "Age is required";
    } else {
        $age = test_input($_POST["age"]);
        if (!is_numeric($age)) {
            $ageErr = "Age must be a number";
        } elseif ($age < 18) {
            $ageErr = "Age must be 18 or above";
        }
    }

    if (!isset($_POST["gender"])) {
        $genderErr = "Gender must be selected";
    } else {
        $gender = test_input($_POST["gender"]);
    }

    if (empty($_POST["course"])) {
        $courseErr = "Course must be selected";
    } else {
        $course = test_input($_POST["course"]);
    }

    if (!isset($_POST["terms"])) {
        $termsErr = "You must accept the Terms & Conditions";
    } else {
        $terms = "checked";
    }

    if (empty($fullNameErr) && empty($emailErr) && empty($usernameErr) && empty($passwordErr) && empty($confirmPasswordErr) && empty($ageErr) && empty($genderErr) && empty($courseErr) && empty($termsErr)) {
        $submittedDetails = [
            "Full Name" => $fullName,
            "Email Address" => $email,
            "Username" => $username,
            "Age" => $age,
            "Gender" => $gender,
            "Course" => $course
        ];

        $_SESSION["logged_in"] = true;
        $_SESSION["student_details"] = $submittedDetails;
        setcookie("student_data", json_encode($submittedDetails), time() + 3600, "/");

        $loggedIn = true;
        $success = "Registration Successful!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        input {
            margin: 8px 0;
            padding: 8px;
            width: 40%;
        }
        label {
            font-weight: bold;
            font-size: 15px;
        }
        select, .radio-group, .checkbox-group {
            margin: 8px 0;
        }
        .m-auto{
            width: auto;
        }
        .details {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }
        .logout-btn {
            padding: 10px 18px;
            font-size: 14px;
            margin: 10px 0 20px 0;
        }
    </style>
</head>
<body>

    <h2>Student Registration Form</h2>

    <?php if ($loggedIn) : ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input class="logout-btn" type="submit" name="logout" value="Logout">
        </form>

        <div class="details">
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($submittedDetails["Full Name"] ?? ''); ?></p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($submittedDetails["Email Address"] ?? ''); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($submittedDetails["Username"] ?? ''); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($submittedDetails["Age"] ?? ''); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($submittedDetails["Gender"] ?? ''); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($submittedDetails["Course"] ?? ''); ?></p>
        </div>
    <?php else : ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <label>Full Name:</label><br>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>">
            <span class="error">* <?php echo $fullNameErr; ?></span><br><br>

            <label>Email Address:</label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <span class="error">* <?php echo $emailErr; ?></span><br><br>

            <label>Username:</label><br>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
            <span class="error">* <?php echo $usernameErr; ?></span><br><br>

            <label>Password:</label><br>
            <input type="password" name="password">
            <span class="error">* <?php echo $passwordErr; ?></span><br><br>

            <label>Confirm Password:</label><br>
            <input type="password" name="confirm_password">
            <span class="error">* <?php echo $confirmPasswordErr; ?></span><br><br>

            <label>Age:</label><br>
            <input type="number" name="age" value="<?php echo htmlspecialchars($age); ?>" min="18">
            <span class="error">* <?php echo $ageErr; ?></span><br><br>

            <label>Gender:</label><br>
            <div class="radio-group">
                <label><input class="m-auto" type="radio" name="gender" value="Male" <?php if ($gender === "Male") echo "checked"; ?>> Male</label>
                <label><input class="m-auto" type="radio" name="gender" value="Female" <?php if ($gender === "Female") echo "checked"; ?>> Female</label>
                <span class="error">* <?php echo $genderErr; ?></span><br><br>
            </div>

            <label>Course Selection:</label><br>
            <select name="course">
                <option value="">-- Select Course --</option>
                <option value="Computer Science" <?php if ($course === "Computer Science") echo "selected"; ?>>Computer Science</option>
                <option value="Information Technology" <?php if ($course === "Information Technology") echo "selected"; ?>>Information Technology</option>
                <option value="Business Administration" <?php if ($course === "Business Administration") echo "selected"; ?>>Business Administration</option>
                <option value="Engineering" <?php if ($course === "Engineering") echo "selected"; ?>>Engineering</option>
            </select>
            <span class="error">* <?php echo $courseErr; ?></span><br><br>

            <div class="checkbox-group">
                <label><input class="m-auto" type="checkbox" name="terms" <?php echo $terms; ?>> I accept the Terms and Conditions</label>
                <span class="error">* <?php echo $termsErr; ?></span><br><br>
            </div>

            <input type="submit" value="Register" style="padding: 12px 25px; font-size: 16px;">
        </form>
    <?php endif; ?>

</body>
</html>
 