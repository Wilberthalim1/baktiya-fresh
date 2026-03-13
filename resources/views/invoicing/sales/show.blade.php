@extends('layouts.app')
@section('title', 'Detail Invoice')
@section('page-title', 'Detail Invoice Penjualan')

@section('content')
<a href="{{ route('invoicing.sales.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i>Kembali</a>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $invoice->inv_number }}</strong>
                <span class="badge bg-{{ $invoice->payment_status === 'paid' ? 'success' : ($invoice->payment_status === 'partial' ? 'warning' : 'danger') }} fs-6">
                    {{ strtoupper($invoice->payment_status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Customer</small>
                        <div class="fw-bold">{{ $invoice->customer->name }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Tgl Invoice</small>
                        <div>{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Jatuh Tempo</small>
                        <div class="{{ $invoice->due_date < now() && $invoice->payment_status !== 'paid' ? 'text-danger fw-bold' : '' }}">{{ $invoice->due_date->format('d/m/Y') }}</div>
                    </div>
                </div>

                <table class="table table-sm">
                    <thead class="table-light"><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end">Total:</td><td class="fw-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="3" class="text-end text-success">Sudah Dibayar:</td><td class="text-success">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="3" class="text-end text-danger fw-bold">Sisa:</td><td class="text-danger fw-bold">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        @if($invoice->payment_status !== 'paid')
        <div class="card">
            <div class="card-header fw-bold">Catat Pembayaran</div>
            <div class="card-body">
                <form action="{{ route('invoicing.sales.payment', $invoice) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control" value="{{ $invoice->remaining_amount }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Metode</label>
                        <select name="method" class="form-select">
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai</option>
                            <option value="check">Cek</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Referensi</label>
                        <input type="text" name="reference" class="form-control" placeholder="No. Transfer/Cek">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan Pembayaran</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
