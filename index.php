<?php
require_once 'dnsApi.php';

try {
    $api = new dnsApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}