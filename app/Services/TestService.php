<?php


namespace App\Services;

use App\Services\Apps\Rocket\Photos\ColorService;
use Illuminate\Support\Str;
use Image;
use App\Models\Apps\Rocket\Photo;
use Storage;

class TestService
{
    /**
     * @var ColorService
     */
    private $colorService;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;
    }

    public function test()
    {
        $photo = Photo::find(41897); // Free
//        $photo = Photo::find(41895); // Critical
//        $photo = Photo::find(41896); // Critical

//        $photo = Photo::find(41890); // Passenger similar color
//        $photo = Photo::find(41889); // Passenger Diff color
//        $photo = Photo::find(41902); // Passenger Diff color


        $storage = Storage::disk('s3');
        $imageFile = $storage->get($photo->path);

        $image = Image::make($imageFile);
        $imageOrig = Image::make($imageFile);


        $width = $image->width();
        $height = $image->height();

        $crop = (object)[
            'x' => 57.4,
            'y' => 80.9,
            'w' => 4.6,
            'h' => 5.1
        ];

        $zone = (object)[
            'w' => intval($crop->w * $width / 100),
            'h' => intval($crop->h * $height / 100),
            'x' => intval($crop->x * $width / 100),
            'y' => intval($crop->y * $height / 100)
        ];

        $imageOrig->rectangle($zone->x, $zone->y, $zone->x + $zone->w, $zone->y + $zone->h, function ($draw) {
            $draw->background('rgba(255, 255, 255, 0)');
            $draw->border(2, '#F34');
        });

        $image->crop($zone->w, $zone->h, $zone->x, $zone->y);

        $color = $this->getColorAverage($image);
        $colorHex = $this->getColorAverage($image, 'hex');

        $refColorHex = "#4b4f8e";
        $refColor = hexdec(Str::replaceFirst("#", "", $refColorHex));

        $equalsNumber = $this->compareColors($refColorHex, $colorHex);
        $equals = $equalsNumber <= 40;

        return view('admin.rocket.image', compact([
            'image', 'imageOrig',
            'color', 'colorHex',
            'refColor', 'refColorHex',
            'equals', 'equalsNumber',
            'zone', 'crop',
            'width', 'height'
        ]));

    }

    private function getColorAverage(\Intervention\Image\Image $image, $format = 'int')
    {
        $image = clone $image;
        // Reduce to single color and then sample
        $color = $image->limitColors(1)->pickColor(0, 0, $format);
        $image->destroy();

        return $color;
    }

    private function compareColors($col1, $col2, $tolerance = 68)
    {
        $col1 = Str::replaceFirst("#", "", $col1);
        $col2 = Str::replaceFirst("#", "", $col2);

        $col1Rgb = array(
            "r" => hexdec(substr($col1, 0, 2)),
            "g" => hexdec(substr($col1, 2, 2)),
            "b" => hexdec(substr($col1, 4, 2))
        );
        $col2Rgb = array(
            "r" => hexdec(substr($col2, 0, 2)),
            "g" => hexdec(substr($col2, 2, 2)),
            "b" => hexdec(substr($col2, 4, 2))
        );

        return number_format(0 +
            $this->colorService->deltaECIE2000(
                [$col1Rgb['r'], $col1Rgb['g'], $col1Rgb['b']],
                [$col2Rgb['r'], $col2Rgb['g'], $col2Rgb['b']]
            ),
            1
        );

        return number_format(sqrt(0 +
            pow(abs($col1Rgb['r'] - $col2Rgb['r']), 2) +
            pow(abs($col1Rgb['g'] - $col2Rgb['g']), 2) +
            pow(abs(abs($col1Rgb['b'] - $col2Rgb['b'])), 2)
        ), 1);
//        return ($col1Rgb['r'] >= $col2Rgb['r'] - $tolerance && $col1Rgb['r'] <= $col2Rgb['r'] + $tolerance) && ($col1Rgb['g'] >= $col2Rgb['g'] - $tolerance && $col1Rgb['g'] <= $col2Rgb['g'] + $tolerance) && ($col1Rgb['b'] >= $col2Rgb['b'] - $tolerance && $col1Rgb['b'] <= $col2Rgb['b'] + $tolerance);
    }
}