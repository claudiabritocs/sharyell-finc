<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Cviebrock\EloquentSluggable\Sluggable;

class Categories extends Model
{
    use Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'titulo',
                'on_update' => true
            ]
        ];
    }

    protected $table = 'categories';

    protected $fillable = [
        'user_id', 'titulo', 'slug',
    ];

   
    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class, 'category_id', 'id');
        
    }

}
