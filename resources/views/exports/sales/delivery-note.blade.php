<table border="1" cellspacing="0" width="50%">
    <tbody>
        <tr>
            <td>{{ strtoupper(trans('sales/delivery-note.form_number')) }}</td>
            <td>{{ $deliveryNote->form->number }}</td>
        </tr>
        <tr>
            <td>{{ strtoupper(trans('sales/delivery-note.date')) }}</td>
            <td>{{ (new DateTime($deliveryNote->form->date))->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>{{ strtoupper(trans('sales/delivery-note.warehouse')) }}</td>
            <td>{{ $deliveryNote->warehouse->name }}</td>
        </tr>
        <tr>
            <td>{{ strtoupper(trans('sales/delivery-note.driver')) }}</td>
            <td>{{ $deliveryNote->driver }}</td>
        </tr>
        <tr>
            <td>{{ strtoupper(trans('sales/delivery-note.license_plate')) }}</td>
            <td>{{ $deliveryNote->license_plate }}</td>
        </tr>
    </tbody>
</table>
<br />
<table border="1" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th align="left">{{ strtoupper(trans('sales/delivery-note.item')) }}</th>
            <th align="right">{{ strtoupper(trans('sales/delivery-note.quantity')) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($deliveryNote->items as $item)
        <tr>
            <td align="center">{{ $loop->iteration }}</td>
            <td>{{ $item->item_name }}</td>
            <td align="right">
                {{ number_format($item->quantity, 0, ',', '.') }}
                {{ $item->unit }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<br />
<table>
    <thead>
        <tr>
            <th>{{ strtoupper(trans('sales/delivery-note.notes')) }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                @if ($deliveryNote->form->note)
                    {{ $deliveryNote->form->note }}
                @else
                    -
                @endif
            </td>
        </tr>
    </tbody>
</table>
