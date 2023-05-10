<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Internal\Debug;
use byteShard\Internal\QueueHandler;

file_put_contents('php://stderr', json_encode(['channel' => 'byteShard', 'context' => ['file' => __FILE__, 'line' => 3], 'extra' => [], 'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), new \DateTimeZone('UTC')), 'level' => 100, 'level_name' => 'DEBUG', 'message' => '[Queue] init'])."\n");

if (php_sapi_name() === 'cli') {
    $queueId = $argv[1] ?? 'all';
} else {
    $queueId = $_POST['QueueId'] ?? 'all';
}

$setup = true;
require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

require_once BS_FILE_BOOTSTRAP_APP;

// Run the queue
$queueHandler = new QueueHandler($queueId);
try {
    $queueHandler->run();
} catch (Exception $e) {
    Debug::error('Exception thrown in Queue: '.$e->getMessage());
}
