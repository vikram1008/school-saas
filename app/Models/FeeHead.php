<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeHead extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'fee_heads';

    protected $fillable = [
        'name', 'name_hi',
        'type', 'frequency',
        'is_active', 'is_optional',
        'description', 'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_optional' => 'boolean',
    ];

    public function structures()
    {
        return $this->hasMany(FeeStructure::class, 'fee_head_id');
    }

    public function demands()
    {
        return $this->hasMany(FeeDemand::class, 'fee_head_id');
    }

    public static function frequencyLabels(): array
    {
        return [
            'monthly'     => 'Monthly / मासिक',
            'quarterly'   => 'Quarterly / त्रैमासिक',
            'half_yearly' => 'Half-Yearly / अर्धवार्षिक',
            'yearly'      => 'Yearly / वार्षिक',
            'one_time'    => 'One Time / एकमुश्त',
        ];
    }
}