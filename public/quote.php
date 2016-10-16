<?php
require_once('../private/init.php');
$html = new Html('Assurance X Online Quote');
$quote = new Quote();
echo $html->h;
echo $html->b;
echo $quote->errors;
echo $quote->display_form;
echo $quote->display_premium;
echo $quote->restart;
echo $html->f;