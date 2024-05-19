<!DOCTYPE html>
<html>
<head>
    <title>Products PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Products</h1>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Variants</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->variants->pluck('name')->implode(', ') }}</td>
                    </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
