<?php

namespace App\Model\Sales\HistoryDeliveryNote;

use App\Model\Master\Item;
use App\Model\PointModel;

/**
 * @property int $id
 * @property int $history_delivery_note_id
 * @property int $item_id
 * @property string $item_name
 * @property float $quantity_remaining
 * @property float $quantity
 * @property string $unit
 * @property float $converter
 * @property null|string $created_at
 * @property null|string $updated_at
 * 
 * @property HistoryDeliveryNote $historyDeliveryNote
 * @property Item $item
 */
class HistoryDeliveryNoteItem extends PointModel
{
    protected $connection = 'tenant';

    protected $fillable = [
        'id',
        'history_delivery_note_id',
        'item_id',
        'item_name',
        'quantity_remaining',
        'quantity',
        'unit',
        'converter',
    ];

    protected $casts = [
        'quantity_remaining' => 'double',
        'quantity' => 'double',
        'converter' => 'double',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function historyDeliveryNote()
    {
        return $this->belongsTo(HistoryDeliveryNote::class, 'history_delivery_note_id');
    }
}
