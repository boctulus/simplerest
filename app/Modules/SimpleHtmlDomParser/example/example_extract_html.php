<?php
include_once('../SimpleHtmlDomParser.php');

echo file_get_html('http://www.google.com/')->plaintext;
?>