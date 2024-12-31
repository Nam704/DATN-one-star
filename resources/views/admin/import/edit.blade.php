<form action="{{ route('imports.store') }}" method="POST">
    @csrf
    <!-- Chọn nhà cung cấp -->
    <div class="mb-3">
        <label for="supplier" class="form-label">Supplier</label>
        <select id="supplier" name="supplier_id" class="form-control" required>
            @foreach ($suppliers as $supplier)
            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Tên nhập hàng -->
    <div class="mb-3">
        <label for="import-name" class="form-label">Import Name</label>
        <input type="text" id="import-name" name="name" class="form-control" required>
    </div>

    <!-- Ngày nhập -->
    <div class="mb-3">
        <label for="import-date" class="form-label">Import Date</label>
        <input type="date" id="import-date" name="import_date" class="form-control" required>
    </div>

    <!-- Tổng số tiền -->
    <div class="mb-3">
        <label for="total-amount" class="form-label">Total Amount</label>
        <input type="number" id="total-amount" name="total_amount" class="form-control" step="0.01" required>
    </div>

    <!-- Ghi chú -->
    <div class="mb-3">
        <label for="note" class="form-label">Note</label>
        <textarea id="note" name="note" class="form-control"></textarea>
    </div>

    <!-- Thêm chi tiết nhập hàng -->
    <div id="import-details-container">
        <h5>Import Details</h5>
        <div class="import-detail">
            <div class="mb-3">
                <label for="product-variant" class="form-label">Product Variant</label>
                <select name="import_details[0][product_variant_id]" class="form-control" required>
                    <!-- List product variants here -->
                    @foreach ($productVariants as $variant)
                    <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" name="import_details[0][quantity]" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="price_per_unit" class="form-label">Price per Unit</label>
                <input type="number" name="import_details[0][price_per_unit]" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="total-price" class="form-label">Total Price</label>
                <input type="number" name="import_details[0][total_price]" class="form-control" step="0.01" required>
            </div>
        </div>
        <!-- More rows can be added dynamically -->
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>