<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $count = 0;

    public function collection()
    {
        return User::orderBy('id', 'asc')->get();
    }

    public function map($user): array
    {
        return [
            ++$this->count,                         // ✅ Serial number
            $user->name,
            $user->email,
            $user->created_at->format('d-m-Y h:m:a'),    // ✅ dd-mm-yyyy
        ];
    }

    public function headings(): array
    {
        return [
            'Sr No',
            'Name',
            'Email',
            'Created Date',
        ];
    }
}
