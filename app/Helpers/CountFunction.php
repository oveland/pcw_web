<?php
/**
 * Este archivo corrige problemas de compatibilidad con count() en PHP 7.2+
 */

// Definición original del namespace
namespace {
    // Guarda la función count() original
    if (!function_exists('__original_count')) {
        function __original_count($var) {
            return count($var);
        }
    }

    // Redefine la función count() global
    if (!function_exists('safe_count')) {
        function safe_count($var) {
            if (is_array($var) || $var instanceof \Countable) {
                return __original_count($var);
            }
            return 0;
        }
    }
}

// PHPExcel namespace específico (importante: sin llaves)
namespace PHPExcel\Writer\Excel2007 {
    // Redefine count() en este namespace
    function count($var) {
        if (is_array($var) || $var instanceof \Countable) {
            return \count($var);
        }
        return 0;
    }
}

// Otros namespaces de PHPExcel que pueden necesitar el parche
namespace PHPExcel\Writer\Excel5 {
    function count($var) {
        if (is_array($var) || $var instanceof \Countable) {
            return \count($var);
        }
        return 0;
    }
}

namespace PHPExcel\Calculation {
    function count($var) {
        if (is_array($var) || $var instanceof \Countable) {
            return \count($var);
        }
        return 0;
    }
}