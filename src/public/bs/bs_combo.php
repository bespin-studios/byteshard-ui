<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Combo;
use byteShard\Enum\HttpResponseType;
use byteShard\ID;
use byteShard\Session;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
$setup = false;

require_once BS_FILE_BOOTSTRAP_APP;

$httpResponse = new \byteShard\Internal\HttpResponse(HttpResponseType::XML);

if (isset($_GET['i'])) {
    try {
        $payload = json_decode(Session::decrypt($_GET['i']));
    } catch (Exception) {
        $payload = null;
    }
    if ($payload instanceof stdClass) {
        $className = $payload?->{'!c'} ?? '';
        if ($className !== '' && class_exists($className)) {
            $combo = new $className();
            if ($combo instanceof Combo) {
                if (property_exists($payload, '!s')) {
                    $combo->setSelectedOption($payload->{'!s'});
                }
                if (property_exists($payload, '!p')) {
                    $parameter = unserialize($payload->{'!p'});
                    if (is_array($parameter)) {
                        $combo->setParameters($parameter);
                    }
                }
                $cell = Session::getCell(ID\ID::decryptFinalImplementation($payload->{'!i'}));
                if ($cell !== null) {
                    $containerClass = $cell->getContentClass();
                    $combo->setCellNonce($cell->getNonce());
                    $httpResponse->setResponseContent($combo->getComboContents($containerClass));
                }
            }
        }
    }
}
$httpResponse->printHTTPResponse();
