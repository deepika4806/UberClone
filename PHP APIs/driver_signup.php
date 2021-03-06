<?php require ('constants.php');
$con = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName);
$name = mysqli_escape_string($con, $_POST['name']);
$phone = mysqli_escape_string($con, $_POST['phone']);
$email = mysqli_escape_string($con, $_POST['email']);
$password = mysqli_escape_string($con, $_POST['password']);
$cab_no = mysqli_escape_string($con, $_POST['cab_no']);
if (!$con)
{
    $response = array(
        "status" => "0",
        "data" => "Error Connecting to Database!"
    );
    die(json_encode($response));
}
else
{
    if (isset($name) && !empty($name) && isset($phone) && !empty($phone) && isset($email) && !empty($email) && isset($password) && !empty($password) && isset($cab_no) && !empty($cab_no))
    {
        $if_already_exists_query = "SELECT * FROM drivers WHERE email='$email' or phone='$phone' or cab_no='$cab_no'";
        $result = mysqli_query($con, $if_already_exists_query);
        if (mysqli_num_rows($result) > 0)
        {
            $response = array(
                "status" => "0",
                "data" => "Email or phone or cab already registered"
            );
            die(json_encode($response));
        }
        else
        {
            $verification_code = password_hash(rand(100000, 999999) , PASSWORD_DEFAULT);
            $register_user_query = "INSERT INTO drivers (driver_name, phone, email, password, verification_code, cab_no) VALUES ('$name', '$phone', '$email', '$password', '$verification_code', '$cab_no')";
            $result = mysqli_query($con, $register_user_query);
            if ($result)
            {
                $to = $email;
                $subject = "Near Cabs Verification Email";
                $message = '

			<html>

			<head>

			<title>Near Cabs</title>

			</head>

			<body>

			<h2>Thank you for joining Near Cabs</h2>

			<p>Click the following link to verify your email. <br /></p>

			<a href='. $server_url . '/api/verify.php?token=' . $verification_code . '>'. $server_url . '/api/verify.php?token=' . $verification_code . '</a>

			<br />

			<br />

			<p>Near Cabs</p>

			</body>

			</html>

			';
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: <no-reply@nearcabs.com>' . "\r\n";
                $headers .= 'Bcc: 1anuppanwar@gmail.com' . "\r\n";
                mail($to, $subject, $message, $headers);
                $response = array(
                    "status" => "1",
                    "data" => "Registered successfully"
                );
            }
            else
            {
                $response = array(
                    "status" => "0",
                    "data" => "1 Unable to regsiter"
                );
            }
            die(json_encode($response));
        }
    }
    else
    {
        $response = array(
            "status" => "0",
            "data" => "2 Unable to register"
        );
        die(json_encode($response));
    }
} ?>
