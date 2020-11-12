<?php
// Database adapter

include_once("config.php");

// Yhdistetään MySQL-tietokantaan
function korg_connect() {
  // MySQL-yhteys
  try {
    $dbh = new PDO('mysql:host=localhost;dbname='.DB_NAME, DB_USER, DB_PASS);
  } catch (PDOException $e) {
    print "Could not connect: " . $e->getMessage() . "<br/>";
    die();
  }

  return $dbh;
}

function korg_get_row($sql, $con) {
  // Returns false if row does not exist
  $item = $con->query($sql)->fetch(PDO::FETCH_ASSOC);
  return $item;
}

function korg_get_rows($sql, $con) {
  $items = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  return $items;
}

function korg_insert($sql, $con) {
  // Return number of rows affected.
  $result = $con->query($sql);
  if (!$result) {
    return 0;
  }
  return $result->rowCount();
}

function korg_insert_id($con) {
  return $con->lastInsertId();
}

function korg_update($sql, $con) {
  // Return number of rows affected.
  $result = $con->query($sql);
  if (!$result) {
    return 0;
  }
  return $result->rowCount();
}

function korg_delete($sql, $con) {
  // Return number of rows affected.
  $result = $con->query($sql);
  if (!$result) {
    return 0;
  }
  return $result->rowCount();
}
