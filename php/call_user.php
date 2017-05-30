<?php

class myclass {
    static function say_hello()
    {
        echo "Hello!\n";
    }
}

$classname = "myclass";

call_user_func(array($classname, 'say_hello'));

// dieser Aufruf ist interessant
call_user_func($classname .'::say_hello'); // As of 5.2.3

$myobject = new myclass();

call_user_func(array($myobject, 'say_hello'));

/*
Klassen normal mit autoload laden lassen und methode aufrufen (statische Methoden)
*/
call_user_func("myclass::say_hello");// testen

// ansonsten Objekt erzeugen
$myobject = new myclass();

call_user_func(array($myobject, 'say_hello'));


?>
