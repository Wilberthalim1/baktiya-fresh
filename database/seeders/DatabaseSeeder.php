<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $users = [
            ['name'=>'Administrator','email'=>'admin@baktiya.com','password'=>Hash::make('password'),'role'=>'admin','is_active'=>true],
            ['name'=>'Sales Team','email'=>'sales@baktiya.com','password'=>Hash::make('password'),'role'=>'sales','is_active'=>true],
            ['name'=>'Purchasing Team','email'=>'purchasing@baktiya.com','password'=>Hash::make('password'),'role'=>'purchasing','is_active'=>true],
            ['name'=>'Invoicing Team','email'=>'invoicing@baktiya.com','password'=>Hash::make('password'),'role'=>'invoicing','is_active'=>true],
            ['name'=>'Warehouse Team','email'=>'warehouse@baktiya.com','password'=>Hash::make('password'),'role'=>'warehouse','is_active'=>true],
        ];
        foreach ($users as $u) User::create($u);

        // Categories
        $categories = [
            ['code'=>'ELC','name'=>'Elektronik'],
            ['code'=>'MEC','name'=>'Mekanikal'],
            ['code'=>'CHM','name'=>'Kimia'],
            ['code'=>'INS','name'=>'Instrumen'],
        ];
        foreach ($categories as $cat) Category::create($cat);

        // Customers
        $customers = [
            ['code'=>'CUST0001','name'=>'PT. Maju Bersama','company'=>'PT. Maju Bersama','email'=>'info@majubersama.com','phone'=>'021-5551234','city'=>'Jakarta','status'=>'active','credit_limit'=>100000000],
            ['code'=>'CUST0002','name'=>'CV. Sukses Jaya','company'=>'CV. Sukses Jaya','email'=>'cs@suksesjaya.com','phone'=>'022-5559876','city'=>'Bandung','status'=>'active','credit_limit'=>50000000],
            ['code'=>'CUST0003','name'=>'PT. Indo Teknologi','company'=>'PT. Indo Teknologi','email'=>'purchase@indotek.co.id','phone'=>'031-5554321','city'=>'Surabaya','status'=>'active','credit_limit'=>200000000],
        ];
        foreach ($customers as $c) Customer::create($c);

        // Suppliers
        $suppliers = [
            ['code'=>'SUPP0001','name'=>'PT. Sumber Elektronik','company'=>'PT. Sumber Elektronik','email'=>'sales@sumberelektronik.com','phone'=>'021-7771234','city'=>'Jakarta','payment_term'=>30,'status'=>'active'],
            ['code'=>'SUPP0002','name'=>'CV. Teknik Jaya','company'=>'CV. Teknik Jaya','email'=>'order@teknikjaya.com','phone'=>'022-7779876','city'=>'Bandung','payment_term'=>14,'status'=>'active'],
        ];
        foreach ($suppliers as $s) Supplier::create($s);

        // Products
        $elc = Category::where('code','ELC')->first()->id;
        $mec = Category::where('code','MEC')->first()->id;
        $ins = Category::where('code','INS')->first()->id;

        $products = [
            ['code'=>'PRD00001','name'=>'Sensor Suhu PT100','category_id'=>$ins,'unit'=>'pcs','cost_price'=>250000,'selling_price'=>350000,'stock_quantity'=>50,'min_stock'=>10,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00002','name'=>'PLC Siemens S7-1200','category_id'=>$elc,'unit'=>'unit','cost_price'=>8500000,'selling_price'=>11000000,'stock_quantity'=>5,'min_stock'=>2,'max_stock'=>20,'is_active'=>true],
            ['code'=>'PRD00003','name'=>'Kabel Control 4x1.5mm','category_id'=>$elc,'unit'=>'meter','cost_price'=>15000,'selling_price'=>22000,'stock_quantity'=>500,'min_stock'=>100,'max_stock'=>1000,'is_active'=>true],
            ['code'=>'PRD00004','name'=>'Bearing SKF 6205','category_id'=>$mec,'unit'=>'pcs','cost_price'=>85000,'selling_price'=>120000,'stock_quantity'=>30,'min_stock'=>10,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00005','name'=>'Panel Listrik 60x80','category_id'=>$elc,'unit'=>'unit','cost_price'=>1200000,'selling_price'=>1800000,'stock_quantity'=>8,'min_stock'=>3,'max_stock'=>30,'is_active'=>true],
        ];
        foreach ($products as $p) Product::create($p);

        echo "Seeding selesai!\n";
    }
}
