<?php

/**
 * Escape HTML
 */
function e($string)
{
    return htmlspecialchars(
        $string,
        ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * Redirect
 */
function redirect($url)
{
    header("Location: ".APP_URL."/".$url);
    exit;
}

/**
 * Old Input
 */
function old($key)
{
    return $_POST[$key] ?? '';
}

/**
 * Current DateTime
 */
function now()
{
    return date("Y-m-d H:i:s");
}

/**
 * Generate Ticket Number
 */
function generateTicketNumber()
{
    return "UMMS-".
        date("Y").
        "-".
        strtoupper(substr(md5(uniqid()),0,6));
}

/**
 * Upload File
 */
function uploadFile($file)
{
    if($file['error'] !== 0){
        return null;
    }

    $folder = "../assets/uploads/";

    if(!is_dir($folder)){
        mkdir($folder,0777,true);
    }

    $filename = uniqid()."_".basename($file['name']);

    move_uploaded_file(
        $file['tmp_name'],
        $folder.$filename
    );

    return $filename;
}