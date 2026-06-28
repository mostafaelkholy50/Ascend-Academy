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
        $targetMonth = $monthInput 
            ? Carbon::parse($monthInput)->startOfMonth() 
            : (now()->day >= 25 ? now()->addMonth()->startOfMonth() : now()->startOfMonth());

        $this->info("Generating missing schedules for {$targetMonth->format('F Y')}...");

        $totalEnrollments = 0;
        $totalSchedulesCreated = 0;
        $totalConflicts = 0;
        $totalSkipped = 0;
        $errors = 0;

        Enrollment::where('status', 'active')->chunk(100, function ($enrollments) use ($targetMonth, &$totalEnrollments, &$totalSchedulesCreated, &$totalConflicts, &$totalSkipped, &$errors) {
            foreach ($enrollments as $enrollment) {
                $totalEnrollments++;
                try {
                    $result = $this->scheduleService->generateMonthlySchedules($enrollment, $targetMonth);
                    
                    if ($result['success']) {
                        if ($result['count'] == 0) {
                            $totalSkipped++;
                        } else {
                            $totalSchedulesCreated += $result['count'];
                            if (isset($result['conflicts']) && $result['conflicts'] > 0) {
                                $totalConflicts += $result['conflicts'];
                            }
                        }
                    } else {
                        $this->error("Failed for enrollment #{$enrollment->id}: " . $result['message']);
                        $errors++;
                    }
                } catch (\Exception $e) {
                    $this->error("Exception for enrollment #{$enrollment->id}: " . $e->getMessage());
                    Log::error("Schedule generation command failed for enrollment #{$enrollment->id}: " . $e->getMessage());
                    $errors++;
                }
            }
        });

        $this->newLine();
        $this->info("=== Report for {$targetMonth->format('F Y')} ===");
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
