<?php

use SmePlug\SmePlug;

require_once './vendor/autoload.php';

$smeplug = new SmePlug(
    '66ccccb703dcded72327ae94a90e2c4c80cde3a939ee46f03a8ff8ce3eec0b89'
);

$plans = $smeplug->purchaseDataPlan(
    phone: '09061668519',
    network_id: 1,
    plan_id: 1,
);
var_dump($plans);
