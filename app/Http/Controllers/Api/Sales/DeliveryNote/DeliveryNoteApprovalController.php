<?php

namespace App\Http\Controllers\Api\Sales\DeliveryNote;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Model\Sales\DeliveryNote\DeliveryNote;
use Illuminate\Http\Request;

class DeliveryNoteApprovalController extends Controller
{
    /**
     * @param $id
     * @return ApiResource
     */
    public function approve($id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);
        $deliveryNote->form->approval_by = auth()->user()->id;
        $deliveryNote->form->approval_at = now();
        $deliveryNote->form->approval_status = 1;
        $deliveryNote->form->save();

        return new ApiResource($deliveryNote);
    }

    /**
     * @param Request $request
     * @param $id
     * @return ApiResource
     */
    public function reject(Request $request, $id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);
        $deliveryNote->form->approval_by = auth()->user()->id;
        $deliveryNote->form->approval_at = now();
        $deliveryNote->form->approval_reason = $request->get('reason');
        $deliveryNote->form->approval_status = -1;
        $deliveryNote->form->save();

        return new ApiResource($deliveryNote);
    }
}
