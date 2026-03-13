<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    protected $fillable = ['goods_receipt_id','product_id','purchase_order_item_id','qty_ordered','qty_received','condition','notes'];
    public function product() { return $this->belongsTo(Product::class); }
}
