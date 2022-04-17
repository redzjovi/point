<?php

namespace App\Mail\Sales\DeliveryNote;

use App\Model\Sales\DeliveryNote\DeliveryNote;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $fromName;
    
    /**
     * @var DeliveryNote
     */
    public $deliveryNote;

    /**
     * @var string
     */
    public $deliveryNoteMessage;

    /**
     * @param string $fromName
     * @param DeliveryNote $deliveryNote
     * @param string $message
     * @return void
     */
    public function __construct($fromName, $deliveryNote, $message)
    {
        $this->fromName = $fromName;
        $this->deliveryNote = $deliveryNote;
        $this->deliveryNoteMessage = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = PDF::loadView('exports/sales/delivery-note', [
            'deliveryNote' => $this->deliveryNote,
        ]);

        return $this->subject(trans('sales/delivery-note.email_customer_subject', ['name' => $this->fromName]))
            ->view('emails/sales/delivery-note/customer')
            ->attachData($pdf->output(), sprintf('%s.pdf', $this->deliveryNote->form->number));
    }
}
