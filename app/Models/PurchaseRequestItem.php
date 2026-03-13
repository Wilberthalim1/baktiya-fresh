<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    protected $fillable = ['purchase_request_id','product_id','quantity','estimated_price','notes'];
    public function product() { return $this->belongsTo(Product::class); }
}
