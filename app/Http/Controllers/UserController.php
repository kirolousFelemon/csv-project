<?php
namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\Subscriber;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{


    public function tests()
    {
        $specialties = Subscriber::select(DB::raw('LOWER(SUBSTRING(specialty, 1, 3)) as specialty_prefix'))
            ->groupBy(DB::raw('LOWER(SUBSTRING(specialty, 1, 3))'))
            ->count();
        dd($specialties);

    }
    public function export()
    {
        // Remove the time limit for large exports
        set_time_limit(0);
    
        // Get distinct specialties, grouping by the first three characters, case-insensitive
        $specialties = Subscriber::select(DB::raw('LOWER(specialty) as specialty'))
            ->get()
            ->groupBy(function ($item) {
                // Group by the first three characters of the specialty, case-insensitive
                return strtolower(substr($item->specialty, 0, 3));
            });
    
        $downloadLinks = [];
        $exportCount = 0; // Count the number of files generated
    
        foreach ($specialties as $prefix => $group) {
            // Use the name of the first specialty in the group as the file name
            $firstSpecialty = $group->first()->specialty;
    
            // Clean the specialty name
            $fileName = preg_replace('/[^a-zA-Z0-9]/', '', $firstSpecialty);
    
            // Check if the specialty is only symbols, spaces, or underscores
            if (empty($fileName)) {
                $fileName = 'unknown'; // Set as 'unknown' if no alphanumeric characters are present
            }
    
            $fileName .= ".xlsx"; // Append file extension
    
            // Log the specialties being grouped and processed
            \Log::info("Processing group for prefix: " . $prefix . " with specialties: " . json_encode($group->pluck('specialty')));
    
            // Determine directory based on file name
            $directoryPath = storage_path('app/public/csv');
            if ($fileName === 'unknown.xlsx') {
                $directoryPath .= '/unknown'; // Place in 'unknown' folder
            }
    
            // Ensure the storage directory exists
            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0777, true, true);
            }
    
            try {
                // Get the specialties list for the current group
                $specialtyList = $group->pluck('specialty')->toArray();
    
                // Log the list of specialties being exported
                \Log::info("Specialties for export: " . json_encode($specialtyList));
    
                // Store the Excel file in the public storage disk
                Excel::store(new UsersExport($specialtyList), 'csv/' . ($fileName === 'unknown.xlsx' ? 'unknown/' : '') . $fileName);
                $exportCount++; // Increment file counter
    
                // Generate the URL for the download
                $fileUrl = Storage::url('csv/' . ($fileName === 'unknown.xlsx' ? 'unknown/' : '') . $fileName);
                $downloadLinks[] = $fileUrl;
    
                \Log::info("File generated and stored: " . $fileUrl);
            } catch (\Exception $e) {
                // Log any errors for debugging
                \Log::error("Error storing the file for specialty group starting with: " . $prefix . ". Error: " . $e->getMessage());
            }
        }
    
        // Return the download links and count of generated files as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Files generated successfully.',
            'data' => $downloadLinks
        ]);
    }
    
    
    // public function exportWork()
    // {
    //     // Remove the time limit for large exports
    //     set_time_limit(0);
    
    //     // Get distinct specialties, grouping by the first three characters, case-insensitive
    //     $specialties = Subscriber::select(DB::raw('LOWER(specialty) as specialty'))
    //         ->get()
    //         ->groupBy(function ($item) {
    //             // Group by the first three characters of the specialty, case-insensitive
    //             return strtolower(substr($item->specialty, 0, 3));
    //         });
    
    //     // Log the grouping result to verify the specialty groups
    //     \Log::info("Grouped specialties: " . json_encode($specialties->toArray()));
    
    //     $downloadLinks = [];
    //     $exportCount = 0; // Count the number of files generated
    
    //     foreach ($specialties as $prefix => $group) {
    //         // Use the name of the first specialty in the group as the file name
    //         $firstSpecialty = $group->first()->specialty;
            
    //         $fileName = preg_replace('/[^a-zA-Z0-9]/', '', $firstSpecialty) . ".xlsx";
    

    //         // Log the specialties being grouped and processed
    //         \Log::info("Processing group for prefix: " . $prefix . " with specialties: " . json_encode($group->pluck('specialty')));
    
    //         // Ensure the storage/public directory exists
    //         $directoryPath = storage_path('app/public');
    //         if (!File::exists($directoryPath)) {
    //             File::makeDirectory($directoryPath, 0777, true, true);
    //         }
    
    //         try {
    //             // Get the specialties list for the current group
    //             $specialtyList = $group->pluck('specialty')->toArray();
    
    //             // Log the list of specialties being exported
    //             \Log::info("Specialties for export: " . json_encode($specialtyList));
    
    //             // Store the Excel file in the public storage disk
    //             Excel::store(new UsersExport($specialtyList), 'csv/' . $fileName);
    //             $exportCount++; // Increment file counter
    
    //             // Generate the URL for the download
    //             $fileUrl = Storage::url('csv/' . $fileName);
    //             $downloadLinks[] = $fileUrl;
    
    //             \Log::info("File generated and stored: " . $fileUrl);
    //         } catch (\Exception $e) {
    //             // Log any errors for debugging
    //             \Log::error("Error storing the file for specialty group starting with: " . $prefix . ". Error: " . $e->getMessage());
    //         }
    //     }
    
    //     // Return the download links and count of generated files as a JSON response
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Files generated successfully.',
    //         'data' => $downloadLinks
    //     ]);
    // }
    
    
    public function exportNew()
    {
        set_time_limit(0);

        // Get distinct specialties, grouping by the full specialty name (case-insensitive)
        $specialties = Subscriber::select(DB::raw('LOWER(specialty) as specialty'))
            ->groupBy(DB::raw('LOWER(specialty)'))
            ->get();

        // Prepare the data to be exported (create the export instance)
        $export = new UsersExport($specialties);

        // Define the file path in the public storage folder
        $fileName = "specialties.xlsx";
        $filePath = storage_path('app/public/csvNew/' . $fileName);

        // Ensure the storage directory exists
        $directoryPath = storage_path('app/public/csvNew');
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0777, true, true);
        }

        try {
            // Store the Excel file
            Excel::store($export, 'csvNew/' . $fileName);

            // Generate the URL for download
            $fileUrl = Storage::url('csvNew/' . $fileName);

            return response()->json([
                'success' => true,
                'message' => 'File generated successfully.',
                'data' => $fileUrl // Provide download link
            ]);
        } catch (\Exception $e) {
            \Log::error("Error storing the file: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating file.',
            ]);
        }
    
    }
    
    
}

