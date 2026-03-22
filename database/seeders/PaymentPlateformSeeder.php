<?php

namespace Database\Seeders;

use App\Models\PaymentPlateform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentPlateformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plateforms = [
            ['name' => 'BankTransfer', 'image' => 'assets/payments/bank-transfer.png','enabled'=>true],
        ];
        
        foreach ($plateforms as $key => $value) 
        {
            PaymentPlateform::updateOrCreate($value,$value);
        }
    }
}
