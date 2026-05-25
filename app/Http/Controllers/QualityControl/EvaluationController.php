<?php

namespace App\Http\Controllers\QualityControl;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TeacherEvaluation;
use App\Http\Requests\StoreEvaluationRequest;
use App\Services\EvaluationService;
use App\Repositories\EvaluationRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class EvaluationController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(EvaluationService $service, EvaluationRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function create(User $teacher)
    {
        // Only allow evaluating teachers
        if (!$teacher->isTeacher()) {
            abort(404);
        }

        $startOfWeek = now()->startOfWeek(Carbon::SATURDAY);

        // Check if already evaluated this week
        $existing = $this->repository->getExistingEvaluation($teacher->id, $startOfWeek->format('Y-m-d'));

        if ($existing && !auth()->user()->can('edit evaluations')) {
            return redirect()->route('qualitycontrol.reports.center')->with('error', 'Teacher already evaluated for this week and you do not have permission to edit.');
        }

        return view('qualitycontrol.evaluations.create', compact('teacher', 'startOfWeek', 'existing'));
    }

    public function store(StoreEvaluationRequest $request, User $teacher)
    {
        try {
            $this->service->storeEvaluation($teacher, $request->validated(), auth()->id());
            
            return redirect()->route('qualitycontrol.reports.center')->with('success', 'Teacher evaluation saved successfully.');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to save evaluation: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $evaluations = $this->repository->getEvaluationsQuery($request)->paginate(20);
        $teachers = User::roleTeacher()->get();

        return view('qualitycontrol.reports.index', compact('evaluations', 'teachers'));
    }

    public function teacherReport(User $teacher)
    {
        $data = $this->service->getTeacherReportData($teacher);

        return view('qualitycontrol.reports.teacher', array_merge([
            'teacher' => $teacher,
        ], $data));
    }

    public function performance(Request $request)
    {
        $teachers = User::roleTeacher()->get();
        $performanceData = $this->service->getPerformanceData($teachers);

        return view('qualitycontrol.reports.performance', compact('performanceData'));
    }

    public function center(Request $request)
    {
        $view = $request->get('view', 'weekly'); // default to weekly
        $teachers = User::roleTeacher()->get();
        
        $performanceData = $this->service->getPerformanceData($teachers);

        // 1. Weekly View: ONLY teachers NOT evaluated this week
        $pendingTeachers = $performanceData->filter(function($item) {
            return $item->has_eval_this_week === false;
        })->sortBy('name');

        // 2. Monthly View: All teachers with their monthly averages
        $monthlyRankings = $performanceData->sortByDesc('monthly_avg');

        // 3. Yearly View: All teachers with their yearly averages
        $yearlyRankings = $performanceData->sortByDesc('yearly_avg');

        // 4. Log View: Full history
        $evaluations = $this->repository->getEvaluationsQuery($request)->paginate(20)->withQueryString();

        return view('qualitycontrol.reports.center', compact(
            'view', 
            'pendingTeachers', 
            'monthlyRankings', 
            'yearlyRankings', 
            'evaluations', 
            'teachers'
        ));
    }

    public function schedules(Request $request)
    {
        // Get the requested week or default to current week
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : now();
        $startOfWeek = $date->copy()->startOfWeek(Carbon::SATURDAY);
        $endOfWeek = $startOfWeek->copy()->addDays(6)->endOfDay();

        $schedules = \App\Models\Schedule::with(['teacher', 'student', 'course'])
            ->whereBetween('starts_at', [$startOfWeek, $endOfWeek])
            ->orderBy('starts_at', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->starts_at->format('Y-m-d');
            });

        $prevWeek = $startOfWeek->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $startOfWeek->copy()->addWeek()->format('Y-m-d');

        return view('qualitycontrol.schedules', compact('schedules', 'startOfWeek', 'endOfWeek', 'prevWeek', 'nextWeek'));
    }
}
