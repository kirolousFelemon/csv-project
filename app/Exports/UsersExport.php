<?php

namespace App\Exports;

use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Log;

class UsersExport implements FromArray, WithHeadings
{
    protected $specialties;

    public function __construct($specialties)
    {
        $this->specialties = $specialties;
    }

    /**
     * Prepare the data for export as an array.
     *
     * @return array
     */
    // public function array(): array
    // {
    //     // Retrieve subscribers for the given specialty and convert to an array
    //     $subscribers = Subscriber::select(
    //             'id',
    //             DB::raw('LOWER(specialty) as specialty'),
    //             'name',
    //             'email',
    //             'license_id',
    //             'organization_name',
    //             'telephone',
    //             'national_id',
    //             'city'
    //         )
    //         ->where('specialty', 'LIKE', '%' . $this->specialties . '%')
    //         ->groupBy('specialty', 'id', 'name', 'email', 'license_id', 'organization_name', 'telephone', 'national_id', 'city')
    //         ->get()
    //         ->toArray();

    //     return $subscribers;
    // }

    // /**
    //  * Define the headings for the exported file.
    //  *
    //  * @return array
    //  */
    // public function headings(): array
    // {
    //     return [
    //         'ID',
    //         'Specialty',
    //         'Name',
    //         'Email',
    //         'License ID',
    //         'Organization Name',
    //         'Telephone',
    //         'National ID',
    //         'City',
    //     ];
    // }

    public function array(): array
    {
        // Retrieve subscribers for the given specialties and convert to an array
        $subscribers = Subscriber::select(
                'id',
                DB::raw('LOWER(specialty) as specialty'),
                'name',
                'email',
                'license_id',
                'organization_name',
                'telephone',
                'national_id',
                'city'
            )
            ->whereIn('specialty', $this->specialties)
            ->get()
            ->toArray();

        // Log the result of the subscriber query
        Log::info("Subscribers retrieved for export: " . json_encode($subscribers));

        return $subscribers;
    }

    /**
     * Define the headings for the exported file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Specialty',
            'Name',
            'Email',
            'License ID',
            'Organization Name',
            'Telephone',
            'National ID',
            'City',
        ];
    }
}
