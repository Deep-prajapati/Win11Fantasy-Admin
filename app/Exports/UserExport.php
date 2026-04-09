<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select('id', 'name', 'email', 'mobile_number')->where('role' , 2)->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Mobile Number'
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->email,
            $row->mobile_number
        ];
    }
}