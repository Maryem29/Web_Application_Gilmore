<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'firebase.php';

try {
    // Initialize Firebase
    initialize_firebase_database();
    echo "Firebase connection established in register.php\n";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm-password']);

        // Validate password
        if ($password !== $confirm_password) {
            die("Passwords do not match.");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare data
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password
        ];

        // Check for existing users
        // Check for existing users
	$existing_users = get_data_from_firebase("/users");

	if (is_array($existing_users) || is_object($existing_users)) {
	    foreach ($existing_users as $user) {
		if ($user['username'] === $username || $user['email'] === $email) {
		    die("Username or email already exists.");
		}
	    }
	} else {
	    // If no users exist or something is wrong with the response
	    echo "No existing users found or invalid response from Firebase.";
	}


        // Write new user data
        write_to_firebase("/users", $user_data);
        echo "Registration successful!";
    }
} catch (Exception $e) {
    echo "Error in register.php: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #626AB2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 800px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            width: 250px; /* Increase logo size */
            margin-bottom: 20px;
        }

        .blue-box {
            background: #4C57A7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .register-fields {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two columns */
            gap: 20px; /* Space between input fields */
            margin-bottom: 20px;
        }

        .register-fields label {
            color: white;
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        .register-fields input {
            width: 100%;
            padding: 7px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            display:block;
        }

        .register-button {
            padding: 10px 20px;
            font-size: 16px;
            background: white;
            color: #4C57A7;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .register-button:hover {
            background: #E2E8F0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="white-box">
            <img src="images/sleep med.png" alt="Logo" class="logo">
            <div class="blue-box">
                <form action="" method="post">
                    <div class="register-fields">
                        <div>
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                        <div>
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div>
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <div>
                            <label for="confirm-password">Confirm Password</label>
                            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                        </div>
                    </div>
                    <button type="submit" class="register-button">Register</button>
                    <p class="login-text">
                        Already have an account? <a href="login.php">Login Here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
