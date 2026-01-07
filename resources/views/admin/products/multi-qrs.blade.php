
<!DOCTYPE html>
<html>
<head>
   <style>
    .text-center {
        text-align: center;
    }


    .table {
        /* width: 700px; */
        font-family: 'Verdana';
        /* border: 0.1px solid #cccccc; */
    }

    table td,
    th {
        padding: 0;
        font-size: 4px;
        border: 0.1px solid;
    }

    .size {
        /* height: 129.21px; */
        /* width: 237.76px; */
        height: 45px;
        width: 80px;
        box-sizing: border-box;
    }

    .font-big {
        font-size: 9px;
        font-weight: bold;
    }

    .red {
        color: red;
    }
</style>
</head>
<body>

<div class="size text-center">

   @foreach ($qrData as $product)
    <table class="table size" cellspacing="0" cellpadding="0">
        <tr class="text-enter">
            <td rowspan="3" cellspacing="0" cellpadding="0" style="width: 50%;border:0.1px solid red;border-right:0;">
                <div class="text-center">
                   <img src="data:image/png;base64,{{ $product['qr']  }}"
             width="35"
             style="border:1px solid #000; padding:6px;">
                </div>
                 
            </td>
            @php
                    $numbers = preg_replace('/[^0-9]/', '', $product['name']);
                    $letters = preg_replace('/[^a-zA-Z]/', '', $product['name']);
                @endphp
            <td cellspacing="0" cellpadding="0" class="text-center" style="border-bottom: 0">
                <span class="font-big" style="color: black">{{ strtoupper($letters) }}</span>
                <span class="font-big red">{{ $numbers }}</span>
            </td>
        </tr>
        <tr>
            <td cellspacing="0" cellpadding="0" style="border-bottom: 0">
                <span>Wt:<br></span>
                <span class="font-big red"
                    style="text-align: right;display:block">{{ $product['weight'] }}
                </span>
            </td>
        </tr>
        <tr>
            <td cellspacing="0" cellpadding="0">
                <span>Size:<br></span>
                <div style="text-align: right">
                    <span class="font-big red">{{ $product['size']}}</span>
                    <span><b>mm</b></span>
                </div>
            </td>
        </tr>
    </table>

</div>
@endforeach
   <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>
</html>


