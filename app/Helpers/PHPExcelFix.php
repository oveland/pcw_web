<?php
namespace App\Helpers;

class PHPExcelFix
{
    public static function register()
    {
        // Este archivo corrige el problema con count() en PHPExcel
        require_once __DIR__ . '/CountPatch.php';
    }
}