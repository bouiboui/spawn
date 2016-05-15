<?php

file_put_contents('test.txt', var_export($argv, true).PHP_EOL, FILE_APPEND);

sleep(1);