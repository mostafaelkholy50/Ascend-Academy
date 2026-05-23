<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\DashboardRepository;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Mockery;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(DashboardRepository::class);
        $this->service = new DashboardService($this->repository);

        // Setup permissions for Spatie
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage accounting']);
        Permission::create(['name' => 'manage quality']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'SuperAdmin']);
        Role::create(['name' => 'Accountant']);
        Role::create(['name' => 'QualityControl']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_get_dashboard_data_returns_full_data_for_admin()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $admin->assignRole('Admin');
        $this->actingAs($admin);

        $this->repository->shouldReceive('getUserCountsByRole')->once()->andReturn(collect(['Student' => 10]));
        $this->repository->shouldReceive('getPendingInquiriesCount')->once()->andReturn(5);
        $this->repository->shouldReceive('getRecentEnrollments')->once()->andReturn(collect([]));
        $this->repository->shouldReceive('getRecentInquiries')->once()->andReturn(collect([]));
        $this->repository->shouldReceive('getMonthlyEnrollmentTrends')->once()->andReturn(collect([]));

        // Add expectations for new KPI comparison and performance methods
        $this->repository->shouldReceive('getMonthlyComparisonData')->once()->andReturn([]);
        $this->repository->shouldReceive('getInquiryConversionRate')->once()->andReturn(75.0);
        $this->repository->shouldReceive('getTopCoursesPerformance')->once()->andReturn(collect([]));
        $this->repository->shouldReceive('getTeacherPerformanceRanking')->once()->andReturn(collect([]));

        $this->repository->shouldReceive('getRevenueForMonth')->twice()->andReturn(100.0);
        $this->repository->shouldReceive('getMonthlyRevenueTrends')->once()->andReturn(collect([]));

        $this->repository->shouldReceive('getEvaluationsSummary')->once()->andReturn(['total' => 1]);
        $this->repository->shouldReceive('getAttendanceSummary')->once()->andReturn(['total' => 1]);

        $data = $this->service->getDashboardData();

        $this->assertArrayHasKey('totalUsers', $data);
        $this->assertArrayHasKey('monthlyRevenue', $data);
        $this->assertArrayHasKey('attendanceSummary', $data);
    }

    public function test_get_dashboard_data_returns_only_accounting_for_accountant()
    {
        $accountant = User::factory()->create(['role' => 'Accountant']);
        $accountant->assignRole('Accountant');
        $this->actingAs($accountant);

        // Add expectation for new KPI comparison method
        $this->repository->shouldReceive('getMonthlyComparisonData')->once()->andReturn([]);

        $this->repository->shouldReceive('getRevenueForMonth')->twice()->andReturn(100.0);
        $this->repository->shouldReceive('getMonthlyRevenueTrends')->once()->andReturn(collect([]));

        // Should NOT call these
        $this->repository->shouldNotReceive('getUserCountsByRole');
        $this->repository->shouldNotReceive('getEvaluationsSummary');

        $data = $this->service->getDashboardData();

        $this->assertArrayHasKey('monthlyRevenue', $data);
        $this->assertArrayNotHasKey('totalUsers', $data);
        $this->assertArrayNotHasKey('attendanceSummary', $data);
    }

    public function test_get_dashboard_data_returns_only_quality_for_qc()
    {
        $qc = User::factory()->create(['role' => 'QualityControl']);
        $qc->assignRole('QualityControl');
        $this->actingAs($qc);

        // Add expectation for new KPI comparison method
        $this->repository->shouldReceive('getMonthlyComparisonData')->once()->andReturn([]);

        $this->repository->shouldReceive('getEvaluationsSummary')->once()->andReturn(['total' => 1]);
        $this->repository->shouldReceive('getAttendanceSummary')->once()->andReturn(['total' => 1]);

        // Should NOT call these
        $this->repository->shouldNotReceive('getRevenueForMonth');
        $this->repository->shouldNotReceive('getUserCountsByRole');

        $data = $this->service->getDashboardData();

        $this->assertArrayHasKey('attendanceSummary', $data);
        $this->assertArrayNotHasKey('monthlyRevenue', $data);
        $this->assertArrayNotHasKey('totalUsers', $data);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
