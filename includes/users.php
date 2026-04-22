<?php

// Responsible for login and registration logic
// Checks login details against tbl_users and verifies the stored password hash
function authenticate_user($db, $email, $password)
{
    if (!$db) {
        return ["success" => false, "message" => "Database connection is unavailable.", "user" => null];
    }

    $email = strtolower(trim($email));

    if ($email === "" || $password === "") {
        return ["success" => false, "message" => "Please enter both email and password.", "user" => null];
    }

    $statement = mysqli_prepare(
        $db,
        "SELECT user_id, user_name, user_email, user_pass
         FROM tbl_users
         WHERE user_email = ?
         LIMIT 1"
    );

    if (!$statement) {
        return ["success" => false, "message" => "Unable to check login details.", "user" => null];
    }

    mysqli_stmt_bind_param($statement, "s", $email);

    if (!mysqli_stmt_execute($statement)) {
        mysqli_stmt_close($statement);
        return ["success" => false, "message" => "Unable to check login details.", "user" => null];
    }

    $result = mysqli_stmt_get_result($statement);
    $row = null;

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }

    mysqli_stmt_close($statement);

    if (!$row) {
        return ["success" => false, "message" => "Invalid email or password.", "user" => null];
    }

    $storedPassword = $row["user_pass"];

    if (!password_verify($password, $storedPassword)) {
        return ["success" => false, "message" => "Invalid email or password.", "user" => null];
    }

    return [
        "success" => true,
        "message" => "Login successful.",
        "user" => [
            "id" => $row["user_id"],
            "name" => $row["user_name"],
            "email" => $row["user_email"],
        ],
    ];
}

// Validates registration data and creates a new user account
function register_user($db, $name, $email, $password, $address)
{
    if (!$db) {
        return ["success" => false, "message" => "Database connection is unavailable."];
    }

    $name = trim($name);
    $email = strtolower(trim($email));
    $address = trim($address);

    if ($name === "" || $email === "" || $password === "" || $address === "") {
        return ["success" => false, "message" => "All fields are required."];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ["success" => false, "message" => "Please enter a valid email address."];
    }

    if (strlen($password) < 6) {
        return ["success" => false, "message" => "Password must be at least 6 characters."];
    }

    $check = mysqli_prepare($db, "SELECT user_id FROM tbl_users WHERE user_email = ? LIMIT 1");

    if (!$check) {
        return ["success" => false, "message" => "Unable to check existing users."];
    }

    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);
    $exists = $result && mysqli_num_rows($result) > 0;

    if ($result) {
        mysqli_free_result($result);
    }

    mysqli_stmt_close($check);

    if ($exists) {
        return ["success" => false, "message" => "An account with this email already exists."];
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $insert = mysqli_prepare($db, "INSERT INTO tbl_users (user_name, user_email, user_pass, user_address) VALUES (?, ?, ?, ?)");

    if (!$insert) {
        return ["success" => false, "message" => "Unable to create the account."];
    }

    mysqli_stmt_bind_param($insert, "ssss", $name, $email, $hashedPassword, $address);
    $success = mysqli_stmt_execute($insert);
    mysqli_stmt_close($insert);

    if (!$success) {
        return ["success" => false, "message" => "Unable to create the account."];
    }

    return ["success" => true, "message" => "Registration successful. Please log in."];
}

