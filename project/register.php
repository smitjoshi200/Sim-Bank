<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (isset($_POST["register"])) {
    $username = null;
    $email = null;
    $password = null;
    $confirm = null;
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
    }
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    if (isset($_POST["confirm"])) {
        $confirm = $_POST["confirm"];
    }
    $isValid = true;
    //check if passwords match on the server side
    if ($password == $confirm) {
        echo "Passwords match <br>";
    }
    else {
        echo "Passwords don't match<br>";
        $isValid = false;
    }
    if (!isset($email) || !isset($password) || !isset($confirm) || !isset($username)) {
        $isValid = false;
    }
    //TODO other validation as desired, remember this is the last line of defense
    if ($isValid) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $db = getDB();
        if (isset($db)) {
            //here we'll use placeholders to let PDO map and sanitize our data
            $stmt = $db->prepare("INSERT INTO Users(username, email, password) VALUES(:username, :email, :password)");
            //here's the data map for the parameter to data
            $params = array(":username" => $username, ":email" => $email, ":password" => $hash);
            $r = $stmt->execute($params);
            //let's just see what's returned
            echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                echo "<br>Welcome! You successfully registered, please login.";
            }
            else {
                if ($e[0] == "23000") {
                    echo "<br>Either username or email is already registered, please try again";
                }
                else {
                    echo "uh oh something went wrong: " . var_export($e, true);
                }
                
            }
        }
    }
    else {
        echo "There was a validation issue";
    }
}
?>
<form method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" maxlength="60" required/>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required/>
    <label for="p1">Password:</label>
    <input type="password" id="p1" name="password" required/>
    <label for="p2">Confirm Password:</label>
    <input type="password" id="p2" name="confirm" required/>
    <input type="submit" name="register" value="Register"/>
</form>