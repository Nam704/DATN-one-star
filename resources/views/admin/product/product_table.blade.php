@foreach ($products as $product)
<tr>
    <td>{{ $product->name }}</td>
    <td><img src="{{ asset($product->image_primary) }}" alt="err" height="60px"></td>
    <td>{{ $product->brand->name }}</td>
    <td>{{ $product->category->name }}</td>
    <td>{{ $product->total_quantity }}</td>
    <td>{{ $product->min_price }}-{{ $product->max_price }}</td>
    <td>
        <a href="{{ route('admin.products.edit', $product->id) }}">
            <button type="button" class="btn btn-secondary btn-warning">Sửa</button>
        </a>
        <form action="{{ route('admin.products.lock', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có muốn ngừng bán sản phẩm này không?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary btn-danger">Ngừng bán</button>
        </form>
        <a href="{{ route('admin.products.detail', $product->id) }}"><button class="btn btn-info">Chi tiết</button></a>
        <a href="{{ route('admin.products.stas', $product->id) }}"><button class="btn btn-primary">Thống kê</button></a>
    </td>
</tr>
@endforeach
