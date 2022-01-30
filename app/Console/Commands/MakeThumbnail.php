<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Storage;

use Illuminate\Console\Command;
use App\Services\ImageService;
use App\Models\Atec;

class MakeThumbnail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumb:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store Thumbnail Images';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $atecs = Atec::get();
        foreach($atecs as $atec) {
            if ($atec->thumbnail == null && $atec->image != null) {
                $targetName = 'thmb_'.$atec->image;
                printf($targetName.PHP_EOL);
                ImageService::resizeImage(
                    storage_path('app/public/atec_image/'.$atec->image),
                    storage_path('app/public/atec_image/'.$targetName),
                    240,
                    180
                );
                $atec->thumbnail = asset(Storage::url('atec_image/').$targetName);
                $atec->save();
            }
        }
    }
}
