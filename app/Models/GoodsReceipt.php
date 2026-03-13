<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $fillable = ['gr_number','purchase_order_id','received_by','receipt_date','status','notes'];
    protected $casts = ['receipt_date' => 'date'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function receiver() { return $this->belongsTo(User::class, 'received_by'); }
    public function items() { return $this->hasMany(GoodsReceiptItem::class); }

    public static function generateNumber(): string {
        $prefix = 'GR/' . date('Y') . '/' . date('m') . '/';
        $last = self::where('gr_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (intval(substr($last->gr_number, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
