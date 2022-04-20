<?php

namespace App\Model\Sales\DeliveryOrder;

use App\Model\Master\Allocation;
use App\Model\Master\Item;
use App\Model\Sales\DeliveryNote\DeliveryNoteItem;
use App\Model\TransactionModel;

/**
 * @property int $id
 * @property int $delivery_order_id
 * @property null|int $sales_order_item_id
 * @property int $item_id
 * @property string $item_name
 * @property float $quantity
 * @property float $price
 * @property null|float $discount_percent
 * @property float $discount_value
 * @property int $taxable
 * @property string $unit
 * @property float $converter
 * @property null|string $notes
 * @property null|int $allocation_id
 * 
 * @property Item $item
 */
class DeliveryOrderItem extends TransactionModel
{
    protected $connection = 'tenant';

    public static $alias = 'sales_delivery_order_item';

    public $timestamps = false;

    protected $fillable = [
        'sales_order_item_id',
        'item_id',
        'item_name',
        'quantity',
        'price',
        'discount_percent',
        'discount_value',
        'taxable',
        'unit',
        'converter',
        'notes',
        'allocation_id',
    ];

    protected $casts = [
        'price' => 'double',
        'discount_percent' => 'double',
        'discount_value' => 'double',
        'quantity' => 'double',
        'converter' => 'double',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function deliveryNoteItems()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }
}
