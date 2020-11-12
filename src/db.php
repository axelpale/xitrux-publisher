<?php
// Database adapter

include_once("config.php");

// Yhdistetään MySQL-tietokantaan
function korg_connect() {
  // MySQL-yhteys
  try {
    $dbh = new PDO('mysql:host=localhost;dbname='.DBNAME, DB_USER, DB_PASS);
  } catch (PDOException $e) {
    print "Could not connect: " . $e->getMessage() . "<br/>";
    die();
  }

  return $dbh;
}

function korg_get_row($sql, $con) {
  $item = $con->query($sql)->fetch();
  return $item;
}

function korg_get_rows($sql, $con) {
  $items = $con->query($sql)->fetchAll();
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
