<?php

namespace App\Traits\Model\Sales;

use App\Model\Form;
use App\Model\Master\Customer;
use App\Model\Master\Warehouse;
use App\Model\Sales\DeliveryNote\DeliveryNote;
use App\Model\Sales\DeliveryNote\DeliveryNoteItem;
use App\Model\Sales\DeliveryOrder\DeliveryOrder;
use App\Model\Sales\HistoryDeliveryNote\HistoryDeliveryNote;
use App\Model\Sales\SalesInvoice\SalesInvoice;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait DeliveryNoteRelation
{
    /**
     * @return HasMany
     */
    public function histories()
    {
        return $this->hasMany(HistoryDeliveryNote::class, 'delivery_note_id');
    }

    /* Invoice needs DeliveryNotes that is done and has pendingDeliveryNotes*/
    public function pendingDeliveryNotes()
    {
        return $this->deliveryNotes()->notDone();
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(deliveryOrder::class, 'delivery_order_id');
    }

    /**
     * @return MorphOne
     */
    public function form()
    {
        return $this->morphOne(Form::class, 'formable');
    }

    /**
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class)->active();
    }

    public function salesInvoices()
    {
        return $this->belongsToMany(SalesInvoice::class, 'sales_invoice_items')->active();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
