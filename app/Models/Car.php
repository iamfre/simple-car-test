<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasAdditionalField;

class Car extends Model
{
    use HasFactory;
    use HasAdditionalField;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cars';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $casts = [
        'additional' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
