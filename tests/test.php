<?php

if (isset($argv)) {

    // For direct/process tests
    echo is_file($argv[1]) ? file_get_contents($argv[1]) : var_export($argv, true);

} else {

    // For curl/http tests
    echo var_export($_REQUEST, true);

}

// Simulate latency
sleep(mt_rand(0, 2));
