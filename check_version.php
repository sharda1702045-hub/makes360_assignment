<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bulk_email_portal', 'root', '');
echo $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
