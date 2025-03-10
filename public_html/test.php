<?php
$memcached = new Memcached();
$memcached->addServer(getenv('MEMCACHED_HOST'), getenv('MEMCACHED_PORT'));

$result = $memcached->set('test_key', 'test_value', 3600); // 3600 seconds = 1 hour

if ($result) {
    echo "Memcached set successful!\n";
} else {
    echo "Memcached set failed!\n";
}

$value = $memcached->get('test_key');

if ($value) {
    echo "Retrieved value: " . $value . "\n";
} else {
    echo "Failed to retrieve value.\n";
}
?>
