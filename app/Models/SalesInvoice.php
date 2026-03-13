<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use SoftDeletes;
    protected $fillable = ['inv_number','sales_order_id','customer_id','created_by','invoice_date','due_date','subtotal','discount','tax_amount','total','paid_amount','payment_status','status','notes'];
    protected $casts = ['invoice_date' => 'date', 'due_date' => 'date'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(SalesInvoiceItem::class); }

    public static function generateNumber(): string {
        $prefix = 'INV/' . date('Y') . '/' . date('m') . '/';
        $last = self::where('inv_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (intval(substr($last->inv_number, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getRemainingAmountAttribute(): float { return $this->total - $this->paid_amount; }
}
