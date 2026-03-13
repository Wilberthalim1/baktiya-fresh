<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $fillable = ['po_number','supplier_id','purchase_request_id','created_by','order_date','expected_date','status','notes','subtotal','discount','tax','total'];
    protected $casts = ['order_date' => 'date', 'expected_date' => 'date'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function goodsReceipts() { return $this->hasMany(GoodsReceipt::class); }
    public function invoice() { return $this->hasOne(PurchaseInvoice::class); }

    public static function generateNumber(): string {
        $prefix = 'PO/' . date('Y') . '/' . date('m') . '/';
        $last = self::where('po_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (intval(substr($last->po_number, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function recalculate(): void {
        $subtotal = $this->items->sum('total');
        $tax = $subtotal * 0.11;
        $this->update(['subtotal'=>$subtotal,'tax'=>$tax,'total'=>$subtotal+$tax-$this->discount]);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'draft'=>'secondary','sent'=>'primary','partial'=>'warning','received'=>'success','cancelled'=>'danger',default=>'secondary'
        };
    }
}
