<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\Attendance;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToCollection
{
    protected $selectedMonth;

    public function __construct($selectedMonth)
    {
        $this->selectedMonth = $selectedMonth;
    }

    public function collection(Collection $rows)
    {
        try {
            // Skip the header row
            $rows = $rows->slice(1);
            
            foreach ($rows as $row) {
                // Get staff code and name, with strict checking for empty values
                $staffCode = trim($row[0] ?? '');
                $staffName = trim($row[1] ?? '');

                // Skip truly empty rows (both code and name are empty)
                if (empty($staffCode) && empty($staffName)) {
                    continue;
                }

                // Create or update staff record even if only code exists
                if (!empty($staffCode)) {
                    Log::info('Processing staff record', [
                        'code' => $staffCode,
                        'name' => $staffName
                    ]);

                    $staff = Staff::updateOrCreate(
                        ['staff_code' => $staffCode],
                        [
                            'name' => $staffName ?: 'Unknown', // Default name if empty
                            'status' => 'active'
                        ]
                    );

                    // Process attendance data starting from column 2 (01-01, 01-02, etc.)
                    for ($day = 1; $day <= 31; $day++) {
                        $columnIndex = $day + 1;  // Adjust index based on your Excel structure
                        
                        // Get raw attendance data
                        $rawTimes = $row[$columnIndex] ?? null;
                        
                        if (!empty($rawTimes)) {
                            // Convert date
                            $date = Carbon::createFromFormat(
                                'Y-m-d', 
                                $this->selectedMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT)
                            );

                            // Update or create attendance record
                            Attendance::updateOrCreate(
                                [
                                    'staff_id' => $staff->id,
                                    'date' => $date->format('Y-m-d')
                                ],
                                [
                                    'raw_data' => $rawTimes,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]
                            );
                        }
                    }
                }
            }

            Log::info('Attendance import completed successfully');
            return true;

        } catch (\Exception $e) {
            Log::error('Error in attendance import: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}