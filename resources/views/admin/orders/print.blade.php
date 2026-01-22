<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Print</title>

    <style>
        .table {
            width: 700px;
            font-family: Verdana;
        }

        table td,
        th {
            padding: 7px 10px;
            font-size: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        p {
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .border-bottom {
            border-bottom: 1px solid #cccccc;
        }

        .border-0 {
            border: unset !important;
        }

        .container {
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="table" cellspacing="0">

            <tr>
                <td colspan="8" class="border-bottom text-center">
                    <h2 style="margin:0">Estimate</h2>
                </td>
            </tr>

            <tr>
                <td colspan="4" class="border-0">
                    <p>
                        <b>Date:</b>
                        {{ $order->created_at->format('d/m/Y') }} |
                        <b>Time:</b>
                        {{ $order->created_at->format('h:i') }}
                    </p>
                </td>
                <td colspan="4" class="border-0">
                    <p>
                        <b>Customer Name:</b>
                        {{ optional($order->customer)->name }}
                    </p>
                </td>
            </tr>

            <tr>
                <td colspan="8" class="border-0">
                    <p>
                        <b>Estimate Remark :</b> {{ $order->remarks }}
                    </p>
                </td>
            </tr>

            <tr>
                <th class="text-center">Sr</th>
                <th class="text-center">Image</th>
                <th class="text-center">Category</th>
                <th class="text-center">Product</th>
                <th class="text-center">Size</th>
                <th class="text-center">Weight</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Total Weight</th>
            </tr>

            @php $total_weight = 0; @endphp

            @foreach ($data as $i => $item)
            @if ($item->product)
            @php
            $gallery = $item->product->gallery ?? [];
            $image = $gallery[0] ?? null;

            if ($is_pdf) {
            // Absolute path for PDF
            $imgPath = $image
            ? public_path('uploads/products/' . $image)
            : null;
            } else {
            // Public URL for browser
            $imgPath = $image
            ? asset('uploads/products/' . $image)
            : null;
            }
            @endphp

            <tr>
                <td class="text-center">{{ $i + 1 }}</td>

                <td class="text-center">
                    @if ($image)
                    @if ($is_pdf)
                    @php
                    $imgSrc = null;
                    if ($imgPath && file_exists($imgPath)) {
                    $type = pathinfo($imgPath, PATHINFO_EXTENSION);
                    $dataImg = file_get_contents($imgPath);
                    $imgSrc = 'data:image/'.$type.';base64,'.base64_encode($dataImg);
                    }
                    @endphp
                    @if ($imgSrc)
                    <img src="{{ $imgSrc }}" width="90">
                    @else
                    No Image
                    @endif
                    @else
                    <img src="{{ $imgPath }}" width="90">
                    @endif
                    @else
                    No Image
                    @endif
                </td>

                <td class="text-center">
                    {{ optional($item->product->category)->name }}
                </td>
                <td class="text-center">{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->product->size }}</td>
                <td class="text-center">
                    {{ number_format((float)$item->product->weight, 3) }}
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">
                    {{ number_format($item->quantity * $item->product->weight, 3) }}
                </td>

                @php
                $total_weight += $item->quantity * $item->product->weight;
                @endphp
            </tr>
            @endif
            @endforeach

            <tr>
                <td colspan="6"><b>Approx Weight</b></td>
                <td class="text-center">{{ $total_quantity }}</td>
                <td class="text-center">{{ number_format($total_weight, 3) }}</td>
            </tr>

        </table>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => window.print(), 500);
        };
    </script>
</body>

</html>