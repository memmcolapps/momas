<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::latest()->get()->map(function ($user) {
            return [
                'First Name'  => $user->first_name,
                'Last Name'   => $user->last_name,
                'Meter No'    => $user->meterNo,
                'Phone'       => $user->phone,
                'Email'       => $user->email,
                'Address'     => $user->address,
                'State'       => $user->state,
                'Created At'  => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Meter No',
            'Phone',
            'Email',
            'Address',
            'State',
            'Created At',
        ];
    }
}
