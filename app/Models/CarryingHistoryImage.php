<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarryingHistoryImage extends Model
{
    protected $table = 't_carrying_history_image';

    protected $fillable = [
        'carrying_id', 'image', 'iamge_path', 'thumbnail'
    ];

    public static function get_image_by_carrying($carrying_id)
    {
        $images = CarryingHistoryImage::where('carrying_id', $carrying_id)
                    ->latest()
                    ->get();
        return $images;
    }
}
