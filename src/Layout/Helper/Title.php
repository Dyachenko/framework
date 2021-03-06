<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Layout\Helper;

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

/**
 * Set or generate <title> code for <head>
 *
 * @param  string $title
 * @param  string $position
 * @param  string $separator
 *
 * @return string
 */
return
    function ($title = null, $position = Layout::POS_REPLACE, $separator = ' :: ') {
        // it's stack for <title> tag
        $oldTitle = Registry::get('layout:title');

        if (is_null($title)) {
            return $oldTitle;
        }

        // switch statement for text position
        switch ($position) {
            case Layout::POS_PREPEND:
                $result = $title . (!$oldTitle ?: $separator . $oldTitle);
                break;
            case Layout::POS_APPEND:
                $result = (!$oldTitle ?: $oldTitle . $separator) . $title;
                break;
            case Layout::POS_REPLACE:
            default:
                $result = $title;
                break;
        }
        Registry::set('layout:title', $result);
        return $result;
    };
