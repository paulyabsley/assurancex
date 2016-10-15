<?php
require_once('../private/init.php');
$html = new Html('Assurance X');
echo $html->h;
echo $html->b;
echo '<a href="' . ROOT . '/quote/personal-details/">Quote online now</a> ';
echo '<a href="' . ROOT . '/quote/retrieve-quote/">Retrieve past quote</a>';
echo $html->f;