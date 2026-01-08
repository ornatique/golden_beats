<style>
    .table {
        width: 700px;
        font-family: 'Verdana';
        /* border: 0.5px solid #cccccc; */
    }

    table td,
    th {
        padding: 7px 10px;
        font-size: 15px;
        /* border: 0.1px solid #cccccc; */
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    p {
        margin-bottom: 5px;
        margin-top: 0px;
        font-size: 14px
    }

    .border-bottom {
        border-bottom: 1px solid #cccccc
    }

    .border-left {
        border-left: 1px solid #cccccc
    }

    .border-0 {
        border: unset !important
    }

    .container {
        margin: 0 auto;
    }
</style>
<div class="container">
    <table class="table" cellspacing="0">
        {{-- <tr>
            <td colspan="8" class="border-bottom">
                <p style="margin: 0"><b>Order Remark :</b> {{ $order_id->remarks }}</p>
        </td>
        </tr> --}}
        <tr>
            <td colspan="8" class="border-bottom text-center">
                <h2 style="margin: 0">Estimate</h2>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="border-0">
                <p>
                    <b>Date:</b> {{ date('d/m/Y', strtotime($order_id->created_at)) }} |
                    <b>Time:</b> {{ date('h:i', strtotime($order_id->created_at)) }}
                </p>
            </td>
            <td colspan="4" class="border-0">
                {{-- <p>
                    <b>Order ID:</b> {{ $order_id->order_id }}
                </p> --}}
                <p>
                    <b>Customer Name:</b> {{ $order_id->user ? ucfirst($order_id->user->name) : '' }}
                </p>
                {{-- <p>
                <b>Distributor Name:</b> Harsh Shah
            </p> --}}

            </td>
        </tr>
        <tr>
            <td colspan="4" class="border-0">
                <b>Estimate Remark :</b> {{ $order_id->remarks }}</p>
            </td>


            {{-- <td colspan="3" class="border-0 text-right"><img
                    src="{{ asset('public/assets/admin/images/logo-dark.png') }}" width="250"></td> --}}
        </tr>
        <tr>
            <th class="text-center">Sr</th>
            <th class="text-center">Image</th>
            <th class="text-center">Category</th>
            <th class="text-center">Product</th>
            <th class="text-center">Size</th>
            {{-- <th class="text-center">Hole Size</th> --}}
            <th class="text-center">Weight</th>
            <th class="text-center">Qty.</th>
            <th class="text-center">Total <br> Weight</th>
        </tr>
        @php
        $total_weight = 0;

        @endphp
        @foreach ($data as $i => $key)
        @if ($key->product)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            {{-- <td class="text-center"><img width="100"
                    src="{{ asset('public/assets/admin/images/products/img-1.png') }}">
            </td> --}}
            @php
            $gallery = $key->product->gallery; // already array
            $image = $gallery[0] ?? null;

            if ($is_pdf ?? false) {
            $imgPath = $image
            ? public_path('assets/upload/products/' . $image)
            : null;
            } else {
            $imgPath = $image
            ? asset('public/assets/upload/products/' . $image)
            : null;
            }
            @endphp


            <td class="text-center">
                @if($image)
                @if($is_pdf)
                @php
                $imgSrc = null;

                if ($image && $is_pdf) {
                $path = public_path($image);

                if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = base64_encode($data);
                $imgSrc = 'data:image/'.$type.';base64,'.$base64;
                }
                }
                @endphp
                ?>
                {{-- DOMPDF: absolute file system path --}}
                 <img src="{{ $imgSrc }}" width="100">
                @else
                {{-- Browser print --}}
                <img src="{{ asset($image) }}" width="100">
                @endif
                @else
                <span>No Image</span>
                @endif
            </td>


            <td class="text-center">
                {{ $key->product ? ($key->product->category ? ucfirst($key->product->category->name) : '') : '' }}
            </td>
            <td class="text-center">{{ $key->product ? ucfirst($key->product->name) : '' }}</td>
            <td class="text-center">{{ $key->product ? $key->product->size : '' }}</td>
            {{-- <td class="text-center">{{ $key->product->hole_size }}</td> --}}
            <td class="text-center">
                {{ $key->product ? number_format((float) $key->product->weight, 3, '.', '') : '' }}
            </td>
            <td class="text-center">{{ $key->quantity }}</td>
            <td class="text-center">{{ $key->quantity * ($key->product ? $key->product->weight : 1) }}</td>
            @php
            $total_weight += $key->quantity * $key->product->weight;
            @endphp
        </tr>
        @endif
        @endforeach

        <tr>
            <td class="" colspan="6"><b>Approx Weight</b></td>
            <td class="text-center">{{ $total_quantity}}</td>
            <td class="text-center">{{ number_format((float) $total_weight, 3, '.', '') }}</td>
        </tr>
    </table>
</div>
<script src="{{ asset('public/assets/admin/libs/jquery/jquery.min.js') }}"></script>
<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 500);
    };
</script>