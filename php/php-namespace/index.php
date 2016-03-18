<?php
include "class/animals/animals.class.php";
include "class/toys/toys.class.php";

use Animals\Dogs as AnimalsDogs;
use Toys\Dogs as ToysDogs;

$logi = new AnimalsDogs();
print "logi,a dog,says:";
$logi->say();


$cuteDoggy = new ToysDogs();
print "cute doggy is a toy, and he says:";
$cuteDoggy->say();


/*
$lucky = new \Animals\Dogs();
print "luck, a dog, says:";
$lucky->say();


$cuteDoggy = new \Toys\Dogs();
print "cute doggy is a toy, and he says:";
$cuteDoggy->say();
*/
