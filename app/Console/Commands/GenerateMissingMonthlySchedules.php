<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateMissingMonthlySchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedules:generate-missing {--month= : The month to generate schedules for (Y-m format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly schedules for all active enrollments that do not have them yet';

    protected $scheduleService;

    /**
     * Execute the console command.
     */
    public function handle(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
        
        $monthInput = $this->option('month');
        $targetMonths = [];
        
        if ($monthInput) {
            $targetMonths[] = Carbon::parse($monthInput)->startOfMonth();
        } else {
            // Always check current month
            $targetMonths[] = now()->startOfMonth();
            
            // If it's near the end of the month, ALSO check next month
            if (now()->day >= 25) {
                $targetMonths[] = now()->addMonth()->startOfMonth();
            }
        }

        $monthNames = collect($targetMonths)->map->format('F Y')->join(' and ');
        $this->info("Generating missing schedules for {$monthNames}...");

        $totalEnrollments = 0;
        $totalSchedulesCreated = 0;
        $totalConflicts = 0;
        $totalSkipped = 0;
        $errors = 0;

        Enrollment::where('status', 'active')->chunk(100, function ($enrollments) use ($targetMonths, &$totalEnrollments, &$totalSchedulesCreated, &$totalConflicts, &$totalSkipped, &$errors) {
            foreach ($enrollments as $enrollment) {
                $totalEnrollments++;
                
                $enrollmentSkippedAll = true;

                foreach ($targetMonths as $month) {
                    try {
                        $result = $this->scheduleService->generateMonthlySchedules($enrollment, $month);
                        
                        if ($result['success']) {
                            if ($result['count'] > 0) {
                                $totalSchedulesCreated += $result['count'];
                                $enrollmentSkippedAll = false;
                                if (isset($result['conflicts']) && $result['conflicts'] > 0) {
                                    $totalConflicts += $result['conflicts'];
                                }
                            }
                        } else {
                            $this->error("Failed for enrollment #{$enrollment->id} ({$month->format('F Y')}): " . $result['message']);
                            $errors++;
                            $enrollmentSkippedAll = false;
                        }
                    } catch (\Exception $e) {
                        $this->error("Exception for enrollment #{$enrollment->id} ({$month->format('F Y')}): " . $e->getMessage());
                        Log::error("Schedule generation command failed for enrollment #{$enrollment->id} ({$month->format('F Y')}): " . $e->getMessage());
                        $errors++;
                        $enrollmentSkippedAll = false;
                    }
                }
                
                if ($enrollmentSkippedAll) {
                    $totalSkipped++;
                }
            }
        });

        $this->newLine();
        $this->info("=== Report for {$monthNames} ===");
        $this->info("Total Active Enrollments Processed: {$totalEnrollments}");
        $this->info("Total New Schedules Created: {$totalSchedulesCreated}");
        $this->info("Total Enrollments Skipped (already had schedules): {$totalSkipped}");
        
        if ($totalConflicts > 0) {
            $this->warn("Total Conflicts Skipped: {$totalConflicts}");
        }
        
        if ($errors > 0) {
            $this->error("Total Errors Encountered: {$errors}");
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
