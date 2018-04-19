<?php
  session_start();
  require "../functions.php";
  require "../conf.inc.php";

  if (count($_POST) === 4
    && !empty($_POST["name"])
    && !empty($_POST["username"])
    && !empty($_POST["birthday"])
    && !empty($_POST["email"])
  ) {
    $error = false;
    $listOfErrors = [];

    // Cleaning string values

    $_POST["name"]         = trim($_POST["name"]);
    $_POST["username"]     = strtolower(trim($_POST["username"]));
    $_POST["email"]        = strtolower(trim($_POST["email"]));

    // Check values one by one

    // name length: min 3 max 60

    if (strlen($_POST["name"]) < 2 || strlen($_POST["name"]) > 60) {
      $error = true;
      $listOfErrors[] = 1;
    }

    // username length: min 3 max 60

    if (strlen($_POST["username"]) < 2 || strlen($_POST["username"]) > 20) {
      $error = true;
      $listOfErrors[] = 2;
    }

    // email : valid format

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
      $error = true;
      $listOfErrors[] = 6;
    } else {
      // Check if this email address already exists

      // Database connection
      $connection = connectDB();

      // Query that returns 1 every time it founds this email
      $query = $connection->prepare("SELECT 1 FROM MEMBER WHERE email= :email");

      // Execute
      $query->execute(["email" => $_POST["email"]]);

      // Fetch data with the query
      $result = $query->fetch();

      if (!empty($result)) {
        $error = true;
        $listOfErrors[] = 7;
      }
    }

    // Check date format: american (YYYY-MM-DD) or european (DD/MM/YYYY)

    $dateFormat = false;

    if (strpos($_POST["birthday"], "/")) {
      list($day, $month, $year) = explode("/", $_POST["birthday"]);
      $dateFormat = true;
    } else if (strpos($_POST["birthday"], "-")) {
      list($year, $month, $day) = explode("-", $_POST["birthday"]);
      $dateFormat = true;
    } else {
      $error = true;
      $listOfErrors[] = 3;
    }

    // Check valid date

    if (!is_numeric($month)
      || !is_numeric($day)
      || !is_numeric($year)
      || !checkdate($month, $day, $year)
    ) {
      $error = true;
      $listOfErrors[] = 4;
    } else {
      // Check if allowed to signup (13 <= age <= 150)
      $today        = time();
      $time13years  = $today - 13*3600*24*365;
      $time150years = $today - 150*3600*24*365;

      // Returns UNIX timestamp with corresponding to the arguments given
      $birthday = mktime(0, 0, 0, $month, $day, $year);

      if ($time13years < $birthday || $time150years > $birthday) {
        $error = true;
        $listOfErrors[] = 5;
      }
    }

    if ($error) {
      $_SESSION["errorForm"] = $listOfErrors;
      $_SESSION["postForm"] = $_POST;
      header("Location: ../account.php");
    }

    // Else => insertion in database

    else {

      // Query that inserts the new member
      $updateQuery = $connection->prepare(
        "UPDATE MEMBER
        SET email=:titi,name=:tata,username=:tutu,birthday=:tete
        WHERE id=:toto AND token=:tyty"
      );

      // Execute the query
      $updateQuery->execute([
        "titi" => $_POST["email"],
        "tata" => $_POST["name"],
        "tutu" => $_POST["username"],
        "tete" => $year . "-" . $month . "-" . $day,
        "toto" => $_SESSION["id"],
        "tyty" => $_SESSION["token"]
      ]);

      header("Location: ../account.php");
    }

  } else {
    die("Error: invalid form submission.");
  }