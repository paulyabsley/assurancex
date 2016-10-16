<?php
require_once('../private/init.php');
$html = new Html('Assurance X');
echo $html->h;
echo $html->b;
// Create db table
// Quote::create_quote_table();
echo '<a href="' . ROOT . '/quote/personal-details/">Quote online now</a> ';
echo '<a href="' . ROOT . '/quote/retrieve-quote/">Retrieve quote</a>';
echo $html->f;