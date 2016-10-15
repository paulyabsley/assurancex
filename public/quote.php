<?php
require_once('../private/init.php');
$html = new Html('Assurance X Online Quote');
$quote = new Quote();
echo $html->h;
echo $html->b;
echo $quote->errors;
echo $quote->display_form;
// var_dump($_SESSION);
// var_dump($errors);
echo $html->f;