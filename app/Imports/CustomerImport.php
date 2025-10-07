<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;

class CustomerImport implements ToModel, WithHeadingRow, WithLimit
{
    protected $estate_id;

    public function __construct($estate_id)
    {
        $this->estate_id = $estate_id;
    }

    public function limit(): int
    {
        return 100; // Limit to 100 rows
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // This is a fallback for the old modal upload
        // Most uploads should use the new client-side preview system

        return new User([
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'password' => bcrypt('password123'), // Default password
            'role' => 2, // Customer role
            'estate_id' => $this->estate_id,
            'status' => 2, // Active
            'can_login' => 0, // Customers typically don't login to web
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'account_no' => $row['account_no'] ?? null,
            'hno' => $row['house_no'] ?? null,
        ]);
    }
}
