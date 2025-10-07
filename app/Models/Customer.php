<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip_code
 * @property string $country
 * @property string $status
 * @property string $notes
 */

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Scope para filtrar clientes ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para filtrar clientes inativos
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope para filtrar clientes suspensos
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }
}