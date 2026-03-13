<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use SoftDeletes;
    protected $fillable = ['pr_number','sales_order_id','requested_by','request_date','required_date','status','notes'];
    protected $casts = ['request_date' => 'date', 'required_date' => 'date'];

    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function items() { return $this->hasMany(PurchaseRequestItem::class); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }

    public static function generateNumber(): string {
        $prefix = 'PR/' . date('Y') . '/' . date('m') . '/';
        $last = self::where('pr_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (intval(substr($last->pr_number, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'draft'=>'secondary','pending'=>'warning','approved'=>'success','rejected'=>'danger','ordered'=>'info',default=>'secondary'
        };
    }
}
