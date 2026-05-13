<?php
$c = new mysqli('127.0.0.1', 'root', '');
if ($c->connect_error) die("MySQL error: " . $c->connect_error);
$c->query('CREATE DATABASE IF NOT EXISTS habita_dsw_ut3_albn');
echo "BD habita_dsw_ut3_albn: OK\n";
$c->close();
