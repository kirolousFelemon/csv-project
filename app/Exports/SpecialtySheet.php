<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class SpecialtySheet implements FromCollection, ShouldAutoSize
{
    protected $specialties;
    protected $specialtyName;

    public function __construct(Collection $specialties, $specialtyName)
    {
        $this->specialties = $specialties;
        $this->specialtyName = $specialtyName;
    }

    public function collection()
    {
        // Return the specialties data for the current sheet
        return $this->specialties;
    }

    public function title(): string
    {
        // Use the specialty name as the sheet title
        return strtoupper($this->specialtyName);
    }
}
