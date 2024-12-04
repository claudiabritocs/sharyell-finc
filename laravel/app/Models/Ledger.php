<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    protected $table = 'ledger';

    protected $fillable = [
        'user_id', 'category_id', 'descricao', 'value', 'remember',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'id', 'category_id');
    }

}
