<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 26/03/2018
 * Time: 10:49 AM
 */

namespace App\Http\Controllers\Utils;


class CSV
{

    /**
     * @param string $filename
     * @param string $delimiter
     * @return array|bool
     */
    static function toArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }
}