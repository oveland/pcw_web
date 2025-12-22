<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FixPhotoPaths extends Command
{
    protected $signature = 'photos:fix-paths {dispatch_id}';

    protected $description = 'Corrige paths de app_photos en AWS cambiando side E por 1';

    public function handle()
    {
        $dispatchId = $this->argument('dispatch_id');

        $photos = DB::table('app_photos')
            ->where('dispatch_register_id', $dispatchId)
            ->where('side', '!=', 'E')
            ->get();

        $this->info("Se encontraron {$photos->count()} fotos para corregir.");

        $disk = Storage::disk('s3');

        foreach ($photos as $photo) {
            $oldPath = $photo->path;
            $newPath = preg_replace('/-E\.jpeg$/', '-1.jpeg', $oldPath);

            try {
                if (!$disk->exists($newPath)) {
                    $disk->move($oldPath, $newPath);

                    DB::table('app_photos')
                        ->where('id', $photo->id)
                        ->update([
                            'side' => '1',
                            'path' => $newPath,
                            'updated_at' => now(),
                        ]);

                    $this->info("✔ Foto {$photo->id} corregida: {$newPath}");
                } else {
                    $this->warn("⚠ Foto {$photo->id} ya existe en destino: {$newPath}");
                }
            } catch (\Exception $e) {
                $this->error("Error con foto {$photo->id}: " . $e->getMessage());
            }
        }
    }
}
