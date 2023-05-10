<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

class bs_error {
    public static function getHead(): string
    {
        $head = '<head>';
        $head .= '<meta content="text/html; charset=utf-8" http-equiv="Content-Type">';
        $head .= '<title>byteShard</title>';
        if (defined('BS_WEB_ROOT_DIR')) {
            $head .= '<link href="'.BS_WEB_ROOT_DIR.'/bs/css/error.css" type="text/css" rel="stylesheet">';
        } else {
            $head .= '<link href="bs/css/error.css" type="text/css" rel="stylesheet">';
        }
        $head .= '</head>';
        return $head;
    }

    public static function printByteShardNotFoundTemplate(string $directory): void
    {
        $html = '<html>';
        $html .= self::getHead();
        $html .= '<body>';
        $html .= '<div id="ContentFrame">';
        $html .= '<div id="MessageFrame">';
        $html .= '<div id="Gears"></div>';
        $html .= '<div class="no_framework"><p>';
        $html .= 'The private path to the byteShard framework has not been defined correctly<br>(File "'.$directory.'" does not exist)<br>Hint: This has to be corrected in the public config.php';
        $html .= '</p></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        print $html;
    }
}
