<?php
namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoicingController extends Controller
{
    // Sales Invoice
    public function salesIndex()
    {
        $invoices = SalesInvoice::with('customer', 'salesOrder')->latest()->paginate(15);
        return view('invoicing.sales.index', compact('invoices'));
    }

    public function salesCreate(Request $request)
    {
        $so = SalesOrder::with('customer', 'items.product')->findOrFail($request->so_id);
        return view('invoicing.sales.create', compact('so'));
    }

    public function salesStore(Request $request)
    {
        DB::transaction(function () use ($request) {
            $so = SalesOrder::with('items.product')->findOrFail($request->sales_order_id);

            $invoice = SalesInvoice::create([
                'inv_number' => SalesInvoice::generateNumber(),
                'sales_order_id' => $so->id,
                'customer_id' => $so->customer_id,
                'created_by' => Auth::id(),
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $so->subtotal,
                'discount' => $so->discount,
                'tax_amount' => $so->tax,
                'total' => $so->total,
                'paid_amount' => 0,
                'payment_status' => 'unpaid',
                'status' => 'issued',
                'notes' => $request->notes,
            ]);

            foreach ($so->items as $item) {
                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'total' => $item->total,
                ]);
                $item->product->deductStock($item->quantity, 'SalesInvoice', $invoice->id, Auth::id());
            }

            $so->update(['status' => 'completed']);
        });

        return redirect()->route('invoicing.sales.index')->with('success', 'Invoice penjualan berhasil dibuat!');
    }

    public function salesShow(SalesInvoice $invoice)
    {
        $invoice->load('customer', 'salesOrder', 'items.product', 'creator');
        return view('invoicing.sales.show', compact('invoice'));
    }

    public function salesPayment(Request $request, SalesInvoice $invoice)
    {
        $request->validate(['amount' => 'required|numeric|min:1', 'method' => 'required']);

        DB::transaction(function () use ($request, $invoice) {
            Payment::create([
                'payment_number' => 'PAY/' . date('YmdHis'),
                'payment_type' => 'sales',
                'payable_type' => SalesInvoice::class,
                'payable_id' => $invoice->id,
                'created_by' => Auth::id(),
                'payment_date' => $request->payment_date ?? now(),
                'amount' => $request->amount,
                'method' => $request->method,
                'reference' => $request->reference,
                'notes' => $request->notes,
            ]);

            $newPaid = $invoice->paid_amount + $request->amount;
            $status = $newPaid >= $invoice->total ? 'paid' : 'partial';
            $invoice->update(['paid_amount' => $newPaid, 'payment_status' => $status]);
        });

        return back()->with('success', 'Pembayaran berhasil dicatat!');
    }

    // Purchase Invoice
    public function purchaseIndex()
    {
        $invoices = PurchaseInvoice::with('supplier', 'purchaseOrder')->latest()->paginate(15);
        return view('invoicing.purchase.index', compact('invoices'));
    }

    public function purchaseStore(Request $request)
    {
        DB::transaction(function () use ($request) {
            $po = PurchaseOrder::with('items.product', 'supplier')->findOrFail($request->purchase_order_id);

            $invoice = PurchaseInvoice::create([
                'inv_number' => PurchaseInvoice::generateNumber(),
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'purchase_order_id' => $po->id,
                'supplier_id' => $po->supplier_id,
                'created_by' => Auth::id(),
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $po->subtotal,
                'discount' => $po->discount,
                'tax_amount' => $po->tax,
                'total' => $po->total,
                'paid_amount' => 0,
                'payment_status' => 'unpaid',
                'status' => 'received',
                'notes' => $request->notes,
            ]);

            foreach ($po->items as $item) {
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => 0,
                    'total' => $item->total,
                ]);
            }
        });

        return redirect()->route('invoicing.purchase.index')->with('success', 'Invoice pembelian berhasil dibuat!');
    }
}
