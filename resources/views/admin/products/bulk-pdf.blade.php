<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Products List</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        td {
            vertical-align: middle;
            /* 🔥 KEY FIX */
            text-align: center;
        }

        .image-cell {
            margin-top: 50%;
            vertical-align: middle;
            text-align: center;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-top: 8%;
            display: inline-block;
        }


        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>

    <h3 style="text-align:center;margin-bottom:10px;">Products List</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Product Image</th>
            </tr>
        </thead>

        <tbody>
            @foreach($products as $index => $product)
            <tr>
                {{-- INDEX --}}
                <td>{{ $index + 1 }}</td>

                {{-- PRODUCT NAME --}}
                <td class="text-left">
                    {{ $product->name }}
                </td>

                {{-- CATEGORY --}}
                <td class="text-left">
                    {{ $product->category->name ?? '-' }}
                </td>

                {{-- SUBCATEGORY --}}
                <td class="text-left">
                    {{ $product->subcategory->name ?? '-' }}
                </td>


                {{-- PRODUCT IMAGES --}}
                <td class="image-cell">
                    @if($product->gallery && count($product->gallery))
                    @foreach($product->gallery as $img)
                    <img src="{{ public_path($img) }}" class="product-img">
                    @endforeach
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>