<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class FeeCollectionItem extends Model
{
    protected $connection = 'tenant';
    protected $table = 'fee_collection_items';

    protected $fillable = [
        'fee_collection_id', 'fee_demand_id', 'amount_paid',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
    ];

    public function collection(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeeCollection::class, 'fee_collection_id');
    }

    public function demand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeeDemand::class, 'fee_demand_id');
    }
}