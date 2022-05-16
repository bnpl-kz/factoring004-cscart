<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * @return array
 */
function getShippings(): array
{
    return fn_get_shippings(true);
}
