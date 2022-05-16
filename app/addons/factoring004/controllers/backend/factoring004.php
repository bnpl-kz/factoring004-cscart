<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/** @var string $mode */

header('Content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($mode === 'upload') {

        $rebuilt = fn_rebuild_files('file');

        $upload_dir = DIR_ROOT . '/images/';

        $name = basename($rebuilt['name']);

        $upload_file = $upload_dir . $name;

        if (move_uploaded_file($rebuilt['tmp_name'], $upload_file)) {
            echo json_encode(
                ['success' => true, 'message'=>__('payments.factoring004.upload_file_success'),'fileName' => $name]
            );
        } else {
            echo json_encode(
                ['success' => false, 'message'=>__('payments.factoring004.upload_file_failed')]
            );
        }

    } else if ($mode === 'remove') {
        $file =  DIR_ROOT . '/images/' . $_POST['filename'];

        if (!unlink($file)) {
            echo json_encode(
                ['success' => false, 'message'=>__('payments.factoring004.offer_file_remove')]
            );
        }
        else {
            echo json_encode(
                ['success' => true, 'message'=>__('payments.factoring004.offer_file_remove')]
            );
        }
    }

    exit;
}
