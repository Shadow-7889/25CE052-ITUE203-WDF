<?php
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name == "" || $email == "" || $mobile == "" || $message == "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $error = "Mobile number must contain exactly 10 digits.";
    } else {

        $file = fopen("registrations.csv", "a");

        if ($file !== false) {
        
            if (filesize("registrations.csv") == 0) {
                fputcsv($file, ["Name", "Email", "Mobile", "Message"]);
            }

            fputcsv($file, [$name, $email, $mobile, $message]);
            fclose($file);

            $success = "Registration submitted successfully!";
        } else {
            $error = "Unable to save the data. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 450px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #cccccc;
        }

        h2 {
            text-align: center;
            color: #243b55;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #bbbbbb;
            border-radius: 5px;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: #2463eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .success {
            color: #176b36;
            background: #e4f7e9;
            padding: 10px;
        }

        .error {
            color: #a61b1b;
            background: #fde8e8;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Registration Form</h2>

    <?php if ($success != ""): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>

        <label for="mobile">Mobile Number</label>
        <input type="tel" id="mobile" name="mobile"
               pattern="[0-9]{10}" maxlength="10" required>

        <label for="message">Message</label>
        <textarea id="message" name="message"
                  rows="4" required></textarea>

        <button type="submit">Submit Registration</button>
    </form>
</div>

</body>
</html>
