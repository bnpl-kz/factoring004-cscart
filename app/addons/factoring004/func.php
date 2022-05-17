<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function getShippings()
{
    return fn_get_shippings(true);
}
