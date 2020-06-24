<?php


namespace App\Models\Apps\Rocket\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

interface PhotoInterface
{
    /**
     * @param $date
     * @return Carbon
     */
    public function getDateAttribute($date);

    /**
     * @return string
     */
    public function getDateFormat();

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle();

    /**
     * @return BelongsTo | DispatchRegister
     */
    public function dispatchRegister();

    /**
     * @param string $encodeImage
     * @return object
     */
    public function getAPIFields($encodeImage = 'url');

    /**
     * @param $data
     * @return void
     */
    public function setDataAttribute($data);

    /**
     * @param $effects
     * @return void
     */
    public function setEffectsAttribute($effects);

    /**
     * @param $data
     * @return object
     */
    function getDataAttribute($data);

    /**
     * @param $effects
     * @return object
     */
    function getEffectsAttribute($effects);
}