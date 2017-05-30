<?php 
// https://werner-zenk.de/scripte/sqlite_datenbank.php

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




// nächster Teil, bedenke das alles aus dem Zusammenhang gerissen wurde


// Überprüfen ob das Formular versendet wurde. 
if ($_SERVER["REQUEST_METHOD"] == "POST") { 

 // Verbindung zur Datenbank-Datei herstellen 
 include "verbindung.php"; 

 // Die Formulareingaben müssen hier überprüft werden ... 

 // prepare() bereitet die Anweisung für die Ausführung vor. 
 // Die Platzhalter werden hier anstatt den POST-Variablen eingesetzt. 
 $insert = $db -> prepare("INSERT INTO nachrichten  
         (`titel`, `autor`, `nachricht`, `datum`) 
  VALUES (:titel, :autor, :nachricht, :datum)"); 

 // Die Platzhalter werden mit $insert->bindValue() durch den 
 // Inhalt der POST-Variablen ersetzt und maskiert. 
 $insert->bindValue(':titel', $_POST["titel"]); 
 $insert->bindValue(':autor', $_POST["autor"]); 
 $insert->bindValue(':nachricht', $_POST["nachricht"]); 
 $insert->bindValue(':datum', date("Y-m-d")); 

 // $insert->execute() führt die vorbereitete Anweisung aus. 
 // Bei einem erfolgreichen Eintrag wird 'true' zurück gegeben. 
 if ($insert->execute()) { 
  echo '<p>Die Nachricht wurde eingetragen.</p>'; 
 } 
 else { 
  // SQL-Fehlermeldung anzeigen. 
  print_r($insert->errorInfo()); 
 } 
} 






// Nachricht bearbeiten 
if (isset($_GET["id"])) { 

 // Eine Nachricht auslesen 
 // prepare() bereitet die Anweisung für die Ausführung vor 
 $select = $db->prepare("SELECT `id`, `titel`, `autor`, `nachricht`, `datum` 
                                   FROM `nachrichten` 
                                   WHERE `id` = :id"); 

 // $select->bindValue() bindet einen Wert an den angegebenen Variablennamen 
 // (die Platzhalter werden mit den GET-Variablen ersetzt). 
 $select->bindValue(':id', $_GET["id"]); 

 // $select->execute() führt die vorbereitete Anweisung aus. 
 $select->execute(); 

 // $select->fetch() holt die Zeile aus dem Ergebnis. 
 $nachricht = $select->fetch(); 

 // Die gespeicherte ID vergleichen 
 if ($nachricht["id"] == $_GET["id"]) { 

  // Formular zum bearbeiten der Nachricht ausgeben 
  echo '<form action="bearbeiten.php" method="post"> 
   <p> 
    <label>Titel:  
     <input type="text" name="titel" value="' . $nachricht["titel"] . '" size="45" maxlength="80" required="required"> 
    </label> 
   </p> 
   <p> 
    <label>Autor:  
     <input type="text" name="autor" value="' . $nachricht["autor"] . '" size="25" maxlength="30" required="required"> 
    </label> 
   </p> 
   <p> 
    <label>Nachricht: <br> 
     <textarea rows="10" cols="40" name="nachricht" required="required">' . $nachricht["nachricht"] . '</textarea> 
    </label> 
   </p> 
   <p> 
    <label><input type="radio" name="option" value="edit" checked="checked"> Ändern</label> 
    <label><input type="radio" name="option" value="delete" required="required"> Löschen</label> 
    <input type="hidden" name="id" value="' . $nachricht["id"] . '"> 
   </p> 
    <input type="submit" value="Absenden"> 
  </form>'; 
 } 
} 

// Überprüfen ob das Formular versendet wurde. 
if ($_SERVER["REQUEST_METHOD"] == "POST") { 

 // Nachricht ändern 
 if ($_POST["option"] == 'edit') { 

  // Die Formulareingaben müssen hier überprüft werden ... 

  // prepare() bereitet die Anweisung für die Ausführung vor. 
  $update = $db -> prepare("UPDATE `nachrichten` 
                        SET  
                          `titel` = :titel, 
                          `autor` = :autor, 
                          `nachricht` = :nachricht 
                        WHERE 
                            `id` = :id"); 

  // $update->bindValue() bindet einen Wert an den angegebenen Variablennamen 
  // (die Platzhalter werden mit den POST-Variablen ersetzt). 
  $update->bindValue(':titel', $_POST["titel"]); 
  $update->bindValue(':autor', $_POST["autor"]); 
  $update->bindValue(':nachricht', $_POST["nachricht"]); 
  $update->bindValue(':id', $_POST["id"]); 

  // $update->execute() führt die vorbereitete Anweisung aus. 
  if ($update->execute()) { 
   echo '<p>Die Nachricht wurde überschrieben.</p>'; 
  } 
  else { 
   // SQL-Fehlermeldung anzeigen. 
   print_r($update->errorInfo()); 
  } 
 } 

 // Nachricht löschen 
 if ($_POST["option"] == 'delete') { 

  // prepare() bereitet die Anweisung für die Ausführung vor. 
  $delete = $db->prepare("DELETE FROM `nachrichten` 
                                    WHERE `id` = :id"); 

  // $delete->bindValue() bindet einen Wert an den angegebenen Variablennamen 
  // (die Platzhalter werden mit den POST-Variablen ersetzt). 
  $delete->bindValue(':id', $_POST["id"]); 

  // $delete->execute() führt die vorbereitete Anweisung aus. 
  if ($delete->execute()) { 
   echo '<p>Die Nachricht wurde gelöscht</p>'; 
  } 
  else { 
   // SQL-Fehlermeldung anzeigen. 
   print_r($delete->errorInfo()); 
  } 
 } 
} 

// Nachrichten auslesen 
// $select->query() führt die SQL-Anweisung aus, 
// die eine Ergebnismenge als PDOStatement Objekt zurück gibt. 
$select = $db->query("SELECT `id`, `titel`, `autor`, `nachricht`, `datum` 
                                FROM `nachrichten` 
                                ORDER BY `datum` DESC"); 

// $select->fetchAll(...) gibt ein Array mit allen Datensätzen zurück. 
// PDO::FETCH_ASSOC gibt ein Objekt mit Eigenschaftennamen zurück, 
// diese entsprechen den Spaltennamen. 
$nachrichten = $select->fetchAll(PDO::FETCH_ASSOC); 

// Anzahl der Nachrichten mit count($nachrichten) ausgeben. 
echo '<h5>' . count($nachrichten) . ' Nachrichten</h5>'; 

// Ausgabe über eine Foreach-Schleife 
foreach ($nachrichten as $nachricht) { 
 extract($nachricht); 
 sscanf($datum, "%4s-%2s-%2s", $jahr, $monat, $tag); 
 echo '<p><small>' . $tag . '.' . $monat . '.' . $jahr . 
  '</small> - <b>' . $titel . '</b><br>' . 
  ' Autor: <em>' . $autor . '</em><br>' . 
  nl2br($nachricht) . '<br>' . 
  '<a href="?id=' . $id . '"><small>Nachricht bearbeiten</small></a></p>'; 
} 



?> 
