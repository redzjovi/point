<?php

namespace App\Model\Sales\HistoryDeliveryNote;

use App\Model\Master\Warehouse;
use App\Model\PointModel;
use App\Model\Sales\DeliveryOrder\DeliveryOrder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property int $delivery_note_id
 * @property int $delivery_order_id
 * @property int $warehouse_id
 * @property string $driver
 * @property string $license_plate
 * @property null|string $notes
 * @property int $request_by
 * @property int $approval_by
 * @property int $activity_type
 * @property null|string $created_at
 * @property null|string $updated_at
 * 
 * @property DeliveryNote $deliveryNote
 * @property DeliveryOrder $deliveryOrder
 * @property Collection<HistoryDeliveryNoteItem> $items
 * @property Warehouse $warehouse
 */
class HistoryDeliveryNote extends PointModel
{
    const ACTVITY_TYPE_APPROVE = 'approve';
    const ACTVITY_TYPE_CREATE = 'create';
    const ACTVITY_TYPE_DELETE = 'delete';
    const ACTVITY_TYPE_REJECT = 'reject';
    const ACTVITY_TYPE_SEND_EMAIL = 'send_email';
    const ACTVITY_TYPE_UPDATE = 'update';

    protected $connection = 'tenant';

    protected $table = 'history_delivery_notes';

    protected $fillable = [
        'delivery_note_id',
        'delivery_order_id',
        'warehouse_id',
        'driver',
        'license_plate',
        'notes',
        'request_by',
        'approval_by',
        'activity_type',
    ];

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class, 'delivery_note_id');
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id');
    }

    public function items()
    {
        return $this->hasMany(HistoryDeliveryNoteItem::class, 'history_delivery_note_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
