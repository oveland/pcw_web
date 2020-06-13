<?php

namespace App\Models\Apps\Rocket;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use File;
use Image;
use Storage;

/**
 * App\Models\Apps\Rocket\Photo
 *
 * @method static Builder|Photo newModelQuery()
 * @method static Builder|Photo newQuery()
 * @method static Builder|Photo query()
 * @mixin Eloquent
 * @property int $id
 * @property Carbon $date
 * @property int $vehicle_id
 * @property int $dispatch_register_id
 * @property int $location_id
 * @property string $path
 * @property string $side
 * @property string $type
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Photo whereCreatedAt($value)
 * @method static Builder|Photo whereData($value)
 * @method static Builder|Photo whereDispatchRegisterId($value)
 * @method static Builder|Photo whereId($value)
 * @method static Builder|Photo whereLocationId($value)
 * @method static Builder|Photo wherePath($value)
 * @method static Builder|Photo whereSide($value)
 * @method static Builder|Photo whereType($value)
 * @method static Builder|Photo whereUpdatedAt($value)
 * @method static Builder|Photo whereVehicleId($value)
 * @property-read Vehicle $vehicle
 * @method static Builder|Photo whereDate($value)
 * @property int|null $persons
 * @property-read DispatchRegister|null $dispatchRegister
 * @method static Builder|Photo wherePersons($value)
 * @property object $effects
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\Photo whereEffects($value)
 */
class Photo extends Model
{
    protected $table = 'app_photos';

    use PhotoRekognition;

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Photo $photo) {
            $photo->processRekognition();
        });
    }

    public function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return $date = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        }

        return Carbon::createFromFormat($this->getDateFormat(), explode('.', $date)[0]);
    }

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    protected $fillable = ['date', 'vehicle_id', 'dispatch_register_id', 'location_id', 'path', 'type', 'data', 'side'];

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo | DispatchRegister
     */
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    /**
     * @param string $encodeImage
     * @return object
     * @throws FileNotFoundException
     */
    public function getAPIFields($encodeImage = 'url')
    {
        $dispatchRegister = $this->dispatchRegister;

        return (object)[
            'id' => $this->id,
            'url' => $this->encode($encodeImage),
            'date' => $this->date->toDateTimeString(),
            'side' => Str::ucfirst(__($this->side)),
            'type' => Str::ucfirst(__($this->type)),
            'vehicle_id' => $this->vehicle_id,
            'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getAPIFields() : null,
            'persons' => $this->data,
        ];
    }

    /**
     * @param string $encode
     * @return \Intervention\Image\Image|string
     * @throws FileNotFoundException
     */
    public function encode($encode = "webp")
    {
        if ($encode == "url") {
            return config('app.url') . "/api/v2/files/rocket/get-photo?id=$this->id" . ($this->effects ? "&with-effect=true&t=" . date('H.i.s.u') : "");
        }

        if ($this->vehicle && Storage::exists($this->path)) {
            return Image::make(Storage::get($this->path))->encode($encode);
        } else {
            return Image::make(File::get('img/image-404.jpg'))->resize(300, 300)->encode($encode);
        }
    }

    public function setDataAttribute($data)
    {
        $this->attributes['data'] = collect($data)->toJson();
    }

    public function setEffectsAttribute($effects)
    {
        $this->attributes['effects'] = collect($effects)->toJson();
    }

    /**
     * @param $data
     * @return object
     */
    function getDataAttribute($data)
    {
        return $data && Str::of($data)->startsWith('{') && Str::of($data)->endsWith('}') ? (object)json_decode($data, true) : null;
    }

    /**
     * @param $effects
     * @return object
     */
    function getEffectsAttribute($effects)
    {
        return $effects && Str::of($effects)->startsWith('{') && Str::of($effects)->endsWith('}') ? (object)json_decode($effects, true) : null;
    }

    /**
     * @param null $encode
     * @param bool $withEffects
     * @return \Intervention\Image\Image
     * @throws FileNotFoundException
     */
    public function getImage($encode = null, $withEffects = false)
    {
        if (Storage::exists($this->path)) {
            $image = Image::make(Storage::disk('local')->get($this->path));
            if ($withEffects && $this->effects) {
                $image->contrast(intval($this->effects->contrast))
                    ->gamma($this->effects->gamma)
                    ->brightness(10)
                    ->sharpen(intval($this->effects->sharpen));

                $image->text('Contrast ' . intval($this->effects->contrast), 5, 430, function ($font) {
                    $font->color('#00ff00');
                })->text('Gamma ' . intval($this->effects->gamma), 5, 440, function ($font) {
                    $font->color('#00ff00');
                })->text('Sharpen ' . intval($this->effects->sharpen), 5, 460, function ($font) {
                    $font->color('#00ff00');
                });
            }
        } else {
            $image = Image::make(File::get('img/image-404.jpg'))->resize(300, 300);
        }

        $luminance = $this->getAvgLuminance($image);

        $image->text('Brightness ' . ($this->effects->brightness ?? '') . '| Av: ' . $luminance, 5, 450, function ($font) {
            $font->color('#00ff00');
        });

        $image->text('PCW @ ' . Carbon::now()->format('Y'), 5, 475, function ($font) {
            $font->color('#00ff00');
        });

        return $encode ? $image->encode($encode) : $image;
    }

    /**
     * @param \Intervention\Image\Image $image
     * @param int $num_samples
     * @return int
     */
    function getAvgLuminance($image, $num_samples = 2)
    {
        $img = imagecreatefromjpeg($image->encode('data-url'));

        $width = imagesx($img);
        $height = imagesy($img);

        $x_step = intval($width / $num_samples);
        $y_step = intval($height / $num_samples);

        $total_lum = 0;
        $sample_no = 1;

        for ($x = 0; $x < $width; $x += $x_step) {
            for ($y = 0; $y < $height; $y += $y_step) {

                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // choose a simple luminance formula from here
                // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
                $lum = ($r + $r + $b + $g + $g + $g) / 6;

                $total_lum += $lum;
                $sample_no++;
            }
        }

        // work out the average
        $avg_lum = $total_lum / $sample_no;

        return intval(($avg_lum / 255) * 100 * 2);
    }
}
