<?php

namespace App\Model\Purchase\PurchaseInvoice;

use App\Model\Master\Allocation;
use App\Model\Master\Item;
use App\Model\Purchase\PurchaseReceive\PurchaseReceive;
use App\Model\Purchase\PurchaseReceive\PurchaseReceiveItem;
use App\Model\TransactionModel;

/**
 * @property int $id
 * @property int $purchase_invoice_id
 * @property int $purchase_receive_id
 * @property int $purchase_receive_item_id
 * @property int $item_id
 * @property null|string $expiry_date
 * @property null|string $production_number
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
 * @property PurchaseReceive $purchaseReceive
 */
class PurchaseInvoiceItem extends TransactionModel
{
    protected $connection = 'tenant';

    public static $alias = 'purchase_invoice_item';

    public $timestamps = false;

    protected $fillable = [
        'purchase_receive_id',
        'purchase_receive_item_id',
        'item_id',
        'item_name',
        'quantity',
        'unit',
        'converter',
        'price',
        'discount_percent',
        'discount_value',
        'taxable',
        'notes',
        'allocation_id',
    ];

    protected $casts = [
        'quantity' => 'double',
        'converter' => 'double',
        'price' => 'double',
        'discount_percent' => 'double',
        'discount_value' => 'double',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function purchaseReceive()
    {
        return $this->belongsTo(PurchaseReceive::class);
    }

    public function purchaseReceiveItem()
    {
        return $this->belongsTo(PurchaseReceiveItem::class);
    }
}
