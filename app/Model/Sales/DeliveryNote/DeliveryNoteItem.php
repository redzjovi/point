<?php

namespace App\Model\Sales\DeliveryNote;

use App\Model\Master\Allocation;
use App\Model\Master\Item;
use App\Model\TransactionModel;

/**
 * @property int $id
 * @property int $delivery_note_id
 * @property null|int $delivery_order_item_id
 * @property int $item_id
 * @property string $item_name
 * @property null|float $gross_weight
 * @property null|float $tare_weight
 * @property null|float $net_weight
 * @property float $quantity
 * @property null|string $expiry_date
 * @property null|string $production_number
 * @property float $price
 * @property null|float $discount_percent
 * @property float $discount_value
 * @property int $taxable
 * @property string $unit
 * @property float $converter
 * @property null|string $notes
 * @property null|int $allocation_id
 * 
 * @property null|Allocation $allocation
 * @property DeliveryNote $deliveryNote
 * @property Item $item
 */
class DeliveryNoteItem extends TransactionModel
{
    protected $connection = 'tenant';

    public static $alias = 'sales_delivery_note_item';
    
    public $timestamps = false;

    protected $fillable = [
        'delivery_order_item_id',
        'item_id',
        'item_name',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'quantity',
        'expiry_date',
        'production_number',
        'price',
        'discount_percent',
        'discount_value',
        'taxable',
        'unit',
        'converter',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'double',
        'converter' => 'double',
        'price' => 'double',
        'discount_percent' => 'double',
        'discount_value' => 'double',
        'gross_weight' => 'double',
        'tare_weight' => 'double',
        'net_weight' => 'double',
    ];

    public function setExpiryDateAttribute($value)
    {
        $this->attributes['expiry_date'] = convert_to_server_timezone($value);
    }

    public function getExpiryDateAttribute($value)
    {
        return convert_to_local_timezone($value);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }
}
