@extends('layout.app')

@section('title', 'Products')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <h1 class="text-center mb-4">Products</h1>
        <div class="text-end mb-3">
            <button id="add-product-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#product-modal">Add Product</button>
            <a href="{{ route('products.export', ['format' => 'excel']) }}" class="btn btn-success" target="_blank">Export to Excel</a>
            <a href="{{ route('products.export', ['format' => 'pdf']) }}" class="btn btn-danger" target="_blank">Export to PDF</a>
        </div>
        <div id="products-table" class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Variants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="product-modal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    @csrf
                    <input type="hidden" name="id" id="product-id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" id="product-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="product-description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="product-price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="variants" class="form-label">Variants (comma-separated)</label>
                        <input type="text" name="variants" id="product-variants" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function() {
        fetchProducts();

        $('#add-product-btn').click(function() {
            $('#productModalLabel').text('Add Product');
            $('#product-form')[0].reset();
            $('#product-id').val('');
            $('#product-modal').modal('show');
        });

        $('#product-form').submit(function(event) {
            event.preventDefault();
            let formData = $(this).serialize();
            let id = $('#product-id').val();
            let url = id ? '{{ url("products") }}/' + id : '{{ route("products.store") }}';
            let type = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: type,
                data: formData,
                success: function(response) {
                    $('#product-modal').modal('hide');
                    fetchProducts();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });

    function fetchProducts() {
        $.ajax({
            url: '{{ route("products.fetch") }}',
            type: 'GET',
            success: function(response) {
                displayProducts(response);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function displayProducts(products) {
        let tableBody = $('#products-table tbody');
        tableBody.empty();

        products.forEach(function(product) {
            let variants = product.variants.map(v => v.name).join(', ');

            let row = `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.description}</td>
                    <td>${product.price}</td>
                    <td>${variants}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editProduct(${product.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                </tr>
            `;

            tableBody.append(row);
        });
    }

    function editProduct(id) {
        $.ajax({
            url: '{{ url("products") }}/' + id + '/edit',
            type: 'GET',
            success: function(product) {
                $('#productModalLabel').text('Edit Product');
                $('#product-id').val(product.id);
                $('#product-name').val(product.name);
                $('#product-description').val(product.description);
                $('#product-price').val(product.price);
                $('#product-variants').val(product.variants.map(v => v.name).join(', '));
                $('#product-modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: '{{ url("products") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    fetchProducts();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    }
</script>
@endsection

