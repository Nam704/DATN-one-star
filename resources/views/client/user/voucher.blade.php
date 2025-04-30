<div class="tab-pane fade" id="voucher">
    <div class="container py-4">
        <h2 class="text-xl font-semibold mb-4">Voucher ưu đãi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($vouchers as $voucher)
                <div class="p-4 border rounded shadow bg-white">
                    <h3 class="text-lg font-bold">{{ $voucher->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $voucher->description }}</p>
                    <div class="mb-2">
                        <strong>Mã:</strong>
                        <span id="code-{{ $voucher->id }}">{{ $voucher->code }}</span>
                        <button onclick="copyCode('{{ $voucher->id }}')" class="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-sm">
                            Copy
                        </button>
                    </div>
                    <div class="text-sm text-gray-700 mb-2">
                        Áp dụng: {{ $voucher->start_date }} - {{ $voucher->end_date }}
                    </div>
                    <a href="{{ route('client.vouchers.show', $voucher->id) }}" class="inline-block px-3 py-1 bg-gray-800 text-white text-sm rounded">
                        Xem chi tiết
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function copyCode(id) {
            const code = document.getElementById('code-' + id).textContent;
            navigator.clipboard.writeText(code).then(() => {
                alert('Đã sao chép mã: ' + code);
            });
        }
    </script>
</div>
