<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Variant extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'variants';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
