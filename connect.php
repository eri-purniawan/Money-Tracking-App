<?php

$dir = __DIR__;

$db = new PDO("sqlite:$dir/money_tracking.db");
$host = gethostname();

$split = str_split($host);
foreach ($split as $str) {
  if ($str != '-' && $str != ' ') {
    $new_name[] = $str;
  }
}
$table_name = 'keuangan_' . implode('', $new_name);
