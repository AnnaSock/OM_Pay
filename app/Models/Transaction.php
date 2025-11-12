<?php

namespace App\Models;

use App\Enums\TypeTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'montant',
        'type_transaction',
        'compte_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'montant' => 'decimal:2',
        'compte_id' => 'string',
        'type_transaction' => TypeTransaction::class,
    ];

    /**
     * Get the compte that owns the transaction.
     */
    public function compte(): BelongsTo
    {
        return $this->belongsTo(Compte::class);
    }
}