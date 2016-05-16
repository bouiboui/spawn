<?php

echo is_file($argv[1]) ? file_get_contents($argv[1]) : var_export($argv, true);

sleep(mt_rand(0, 2));