<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compte extends Model
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
        'user_id',
        'user_type',
        'numero_compte',
        'numero_user',
        'code_marchand',
        'login',
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'string',
        'user_type' => 'string',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'solde',
    ];

    /**
     * Get the user that owns the compte.
     */
    public function user()
    {
        return $this->morphTo('user', 'user_type', 'user_id');
    }

    /**
     * Get the transactions for the compte.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope to find compte by numero_user.
     */
    public function scopeByNumero($query, string $numero)
    {
        return $query->where('numero_user', $numero);
    }

    /**
     * Get the solde attribute.
     */
    public function getSoldeAttribute(): float
    {
        $depot = $this->transactions()->where('type_transaction', 'Depot')->sum('montant');
        $retrait = $this->transactions()->where('type_transaction', 'Retrait')->sum('montant');
        $payement = $this->transactions()->where('type_transaction', 'Payement')->sum('montant');

        return $depot - $retrait - $payement;
    }

    /**
     * Get the current balance of the compte.
     */
    public function getSolde(): float
    {
        return $this->solde;
    }
}