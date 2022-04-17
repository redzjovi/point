<?php

namespace App\Http\Controllers\Api\Sales\DeliveryNote;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\DeliveryNote\DeliveryNoteSendEmail\StoreRequest;
use App\Mail\Sales\DeliveryNote\CustomerMail;
use App\Model\Sales\DeliveryNote\DeliveryNote;
use App\User;
use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class DeliveryNoteSendEmailController extends Controller
{
    /**
     * @param StoreRequest $request
     * @throws ModelNotFoundException
     */
    public function store(StoreRequest $request)
    {
        /** @var User  */
        $user = $request->user();

        $id = $request->get('id');
        $message = $request->get('message');

        /** @var DeliveryNote */
        $deliveryNote = DeliveryNote::query()->findOrFail($id);

        Mail::to($deliveryNote->customer->email)->send(new CustomerMail(
            $user->name,
            $deliveryNote,
            $message
        ));

        return response()->noContent();
    }
}
