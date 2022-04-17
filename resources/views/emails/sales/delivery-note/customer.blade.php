@extends('emails.template')

@section('content')
    <div class="title">{{ trans('sales/delivery-note.email_customer_subject', ['name' => $fromName]) }}</div>
    <br>
    <div class="body-text">
        {{ trans('sales/delivery-note.email_customer_salutation', ['name' => $deliveryNote->customer_name]) }}
        <br />
        {{ trans('sales/delivery-note.email_customer_first_line') }}
        <br />
        @if ($deliveryNoteMessage)
            <br />
            {{ $deliveryNoteMessage }}
            <br />
        @endif
        <br />
        <br />
        <br />
        <br />
        <br />
        {{ trans('sales/delivery-note.email_customer_closing') }}
        <br />
        <br />
        {{ $deliveryNote->form->createdBy->name }}
        <br />
        <img alt="PDF" height="100" src="{{ asset('svg/pdf.svg') }}" width="100" />
        <br />
        {{ $deliveryNote->form->number }}
    </div>
@stop
