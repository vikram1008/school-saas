<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeHead extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'fee_heads';

    protected $fillable = [
        'name', 'name_hi', 'frequency', 'is_preset', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_preset'  => 'boolean',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // frequency: monthly | quarterly | half_yearly | yearly | one_time
    public static array $frequencies = [
        'monthly'     => 'Monthly',
        'quarterly'   => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'yearly'      => 'Yearly',
        'one_time'    => 'One Time',
    ];

    public function structures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeeStructure::class, 'fee_head_id');
    }
}