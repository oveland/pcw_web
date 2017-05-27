<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 26/05/2017
 * Time: 8:14 PM
 */

namespace app;


trait Mapping
{
    public function getAttribute($field)
    {
        $field = (collect($this->mapping)->keys()->contains($field))?$this->mapping[$field]:$field;
        return parent::getAttribute($field);
    }

    public function setAttribute($field,$value)
    {
        $field = (collect($this->mapping)->keys()->contains($field))?$this->mapping[$field]:$field;
        return parent::setAttribute($field,$value);
    }
}