<?php

use SmePlug\SmePlug;

require_once '../vendor/autoload.php';

$key = file_get_contents('../key.txt');
$smeplug = new SmePlug($key);

//$plans = $smeplug->getDataPlans();
//var_dump($plans);

//$networks = $smeplug->getNetworks();
//var_dump($networks);

//$purchase_data = $smeplug->purchaseDataPlan('1', '500', 'XXXXXXXXX');
//var_dump($purchase_data);

//$purchase_airtime = $smeplug->purchaseAirtime('1', 50, 'XXXXXXXXX');
//var_dump($purchase_airtime);

//$banks = $smeplug->getTransferBanksList();
//var_dump($banks);

//$details = $smeplug->resolveAccountDetails('000007', 'XXXXXXXXX');
//var_dump($details);

//$transfer = $smeplug->bankTransfer('000007', 'XXXXX', 100, 'Test123');
//var_dump($transfer);