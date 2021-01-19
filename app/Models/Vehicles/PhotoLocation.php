<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class PhotoLocation extends Model
{
    protected $table = "app_photos";

    public function toArray()
    {
        return [
          'id' => $this->id,
        ];
    }
}
