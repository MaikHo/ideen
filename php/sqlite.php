<?php 
// Pfad zur Datenbank 
$datenbank = "db/datenbank.sqt"; 

// Datenbank-Datei erstellen 
if (!file_exists($datenbank)) { 
  $db = new PDO('sqlite:' . $datenbank); 
  $db->exec("CREATE TABLE nachrichten( 
    id INTEGER PRIMARY KEY, 
    titel CHAR(255), 
    autor CHAR(255), 
    nachricht TEXT, 
    datum DATE)"); 
} 
else { 
  // Verbindung 
  $db = new PDO('sqlite:' . $datenbank); 
} 

// Schreibrechte überprüfen 
if (!is_writable($datenbank)) { 
  // Schreibrechte setzen 
  chmod($datenbank, 0777); 
} 
?> 
