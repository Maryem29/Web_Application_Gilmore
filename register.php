<?php
include 'firebase.php';
session_start(); // Start session to store the success message

// Initialize message variable
$message = '';
$message_class = '';
$error_fields = []; // Array to store fields that have errors

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm-password']);

        // Check if any required fields are empty
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $message = "Please fill in all fields.";
            $message_class = "error";
            if (empty($username)) $error_fields[] = 'username';
            if (empty($email)) $error_fields[] = 'email';
            if (empty($password)) $error_fields[] = 'password';
            if (empty($confirm_password)) $error_fields[] = 'confirm-password';
        } else {
            // Validate password
            if ($password !== $confirm_password) {
                $message = "Passwords do not match.";
                $message_class = "error";
                $error_fields[] = 'password';
                $error_fields[] = 'confirm-password';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Prepare data
                $user_data = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed_password
                ];

                // Check for existing users
                $existing_users = get_data_from_firebase("/users");

                if (is_array($existing_users) || is_object($existing_users)) {
                    foreach ($existing_users as $user) {
                        if ($user['username'] === $username || $user['email'] === $email) {
                            $message = "Username or email already exists.";
                            $message_class = "error";
                            $error_fields[] = 'username';
                            $error_fields[] = 'email';
                            break;
                        }
                    }
                }

                if (empty($message)) {
                    // Write new user data
                    write_to_firebase("/users", $user_data);
                    $_SESSION['success_message'] = "Registration successful! Now please log in.";
                    // Redirect to login page
                    header('Location: login.php');
                    exit; // Ensure script stops here after redirection
                }
            }
        }
    }
} catch (Exception $e) {
    $message = "Error in register.php: " . $e->getMessage();
    $message_class = "error";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Display floating message if set -->
        <?php if ($message): ?>
            <div class="floating-message <?php echo $message_class; ?>" id="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="white-box">
            <img src="images/sleep med.png" alt="Logo" class="logo">
            <div class="blue-box">
                <form action="" method="post">
                    <div class="register-fields">
                        <div>
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="Enter your username" 
                                class="<?php echo in_array('username', $error_fields) ? 'error-border' : ''; ?>" 
                                value="<?php echo isset($username) ? $username : ''; ?>" required>
                        </div>
                        <div>
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" 
                                class="<?php echo in_array('email', $error_fields) ? 'error-border' : ''; ?>" 
                                value="<?php echo isset($email) ? $email : ''; ?>" required>
                        </div>
                        <div>
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" 
                                class="<?php echo in_array('password', $error_fields) ? 'error-border' : ''; ?>" 
                                value="<?php echo isset($password) ? $password : ''; ?>" required>
                        </div>
                        <div>
                            <label for="confirm-password">Confirm Password</label>
                            <input type="password" id="confirm-password" name="confirm-password" 
                                placeholder="Confirm your password" 
                                class="<?php echo in_array('confirm-password', $error_fields) ? 'error-border' : ''; ?>" 
                                value="<?php echo isset($confirm_password) ? $confirm_password : ''; ?>" required>
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
    
	<div class="bookmark-nav">
	    <div class="bookmark" onclick="toggleNav()">
		<img src="images/sleep.png" alt="Logo" class="bookmark-logo">
	    </div>
	    <div class="nav-options" id="nav-options">
		<ul>
		    <li><a href="index.php">Home</a></li>
		    <li><a href="register.php">Register</a></li>
		    <li><a href="login.php">Login</a></li>
		    <li><a href="about.php">About</a></li>
		    <li><a href="contact.php">Contact</a></li>
		</ul>
	    </div>
	</div>
	
	<script>
	    function toggleNav() {
		const navOptions = document.getElementById('nav-options');

		if (navOptions.classList.contains('active')) {
		    // Slide up
		    navOptions.classList.remove('active');
		    navOptions.classList.add('inactive');

		    // Wait for the animation to finish, then hide the element
		    setTimeout(() => {
		        navOptions.style.display = 'none';
		    }, 500); // Match the transition duration
		} else {
		    // Slide down
		    navOptions.style.display = 'flex'; // Ensure it's visible
		    navOptions.classList.remove('inactive');
		    navOptions.classList.add('active');
		}
	    }

	    // Attach event listener to the logo
	    document.getElementById('bookmark-logo').addEventListener('click', toggleNav);
	</script>


    

    <script>
        // Automatically hide the message after 3 seconds
        window.onload = function() {
            var message = document.getElementById('message');
            if (message) {
                message.style.visibility = 'visible';
                message.style.opacity = 1;
                setTimeout(function() {
                    message.style.opacity = 0;
                    message.style.visibility = 'hidden';
                }, 3000); // Hide after 3 seconds
            }
        };
    </script>
</body>
</html>
