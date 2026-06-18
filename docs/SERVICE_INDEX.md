# Service Index

| Service | Dependencies | Public Methods |
|---------|--------------|----------------|
| App\Services\AttendanceService | AttendanceRepository, AttendanceFilter, ScheduleService | getAttendances, getAttendanceDetails, getCreateData, storeAttendance |
| App\Services\AuthService |  | afterLogin |
| App\Services\BookService | BookRepository | getIndexData, storeBook, updateBook, deleteBook |
| App\Services\CourseService | CourseRepository, CourseFilter | getIndexData, storeCourse, updateCourse, deleteCourse |
| App\Services\DashboardService | DashboardRepository | getDashboardData |
| App\Services\EnrollmentService | EnrollmentRepository, EnrollmentFilter | getIndexData, storeEnrollments, updateEnrollment, deleteEnrollment |
| App\Services\EvaluationService | EvaluationRepository | getPerformanceData, storeEvaluation, getTeacherReportData |
| App\Services\InquiryService | InquiryRepository | processInquiry, getIndexData, updateStatus, deleteInquiry, convertToParent |
| App\Services\NewsService | NewsRepository, NewsFilter | getIndexData, storeNews, updateNews, deleteNews |
| App\Services\NotificationService | NotificationRepository | getIndexData, getUnreadData, markAsRead, markAllAsRead |
| App\Services\ParentDashboardService | ParentDashboardRepository | getDashboardData |
| App\Services\ParentService | ParentRepository, ParentFilter | getIndexData, storeParent, addChild, removeChild, updateParent, updatePassword, deleteParent |
| App\Services\PaymentService | PaymentRepository, ScheduleService | getIndexData, getAdminIndexData, updatePaymentStatus, generateMonthlyPayments, generatePaymentsForEnrollment, markAllPaid, markAllUnpaid, getAllowedCountries, hasAccessToCountry, applyRegionalFilter, canAccessPayroll |
| App\Services\PricingTierService | PricingTierRepository, PricingTierFilter | getIndexData, storePricingTier, updatePricingTier, deletePricingTier |
| App\Services\ProfileService | ProfileRepository | getProfileData, updateProfile, updatePassword, updateAvatar, deleteAvatar |
| App\Services\ReportService | ReportRepository, ReportFilter | getIndexData |
| App\Services\RolePermissionService | RolePermissionRepository | getIndexData, getManageRolesData, createRole, createPermission, updateRolePermissions, assignRole, storeUser, deleteUser |
| App\Services\ScheduleService | ScheduleRepository, ScheduleFilter | getCalendarData, getEnrollmentGroupedData, storeSchedule, updateSchedule, deleteSchedule, bulkCancel, bulkDelete, generateMonthlySchedules |
| App\Services\SchedulerDashboardService | SchedulerDashboardRepository | getDashboardData |
| App\Services\StudentCourseService | StudentCourseRepository | getCoursesData |
| App\Services\StudentDashboardService | StudentDashboardRepository | getDashboardData |
| App\Services\StudentEvaluationService | StudentEvaluationRepository, StudentEvaluationFilter | storeEvaluation, updateEvaluation, deleteEvaluation, getPendingEvaluations, getStudentMonthlyScores, getStudentMonthlyAverages, getStudentEvaluations, getAggregateScores, getEvaluationByMonth, getTeacherEvaluations, getIndexData |
| App\Services\StudentProfileService | StudentProfileRepository | updateProfile, updatePassword, updateAvatar, deleteAvatar, getProfileData |
| App\Services\StudentReportService | StudentReportRepository | getIndexData, getReport |
| App\Services\StudentResourceService | StudentResourceRepository | getIndexData, getResource |
| App\Services\StudentScheduleService | StudentScheduleRepository | getWeeklyData, getDailyData |
| App\Services\StudentService | StudentRepository, StudentFilter | getIndexData, storeStudent, updateStudent, updatePassword, deleteStudent |
| App\Services\TeacherApplicationService | TeacherApplicationRepository | processApplication, getIndexData, convertToTeacher, updateStatus, deleteApplication |
| App\Services\TeacherAttendanceService | TeacherAttendanceRepository | storeAttendance, notifyParentWaiting |
| App\Services\TeacherDashboardService | TeacherDashboardRepository, StudentEvaluationService | getDashboardData |
| App\Services\TeacherHourService | TeacherHourRepository | getPayrollData, calculateAndSaveHours, markAsPaid, markAsUnpaid |
| App\Services\TeacherHoursService | TeacherHoursRepository | getHoursData |
| App\Services\TeacherProfileService | TeacherProfileRepository | getProfileData, updateProfile, updatePassword, updateAvatar, deleteAvatar |
| App\Services\TeacherReportService | TeacherReportRepository | getIndexData, getCreateData, getEditData, storeReport, updateReport, deleteReport |
| App\Services\TeacherResourceService | TeacherResourceRepository | getIndexData, getCreateData, getEditData, storeResource, updateResource, deleteResource, downloadResource |
| App\Services\TeacherScheduleService | TeacherScheduleRepository | getWeeklyData, getDailyData |
| App\Services\TeacherService | TeacherRepository, TeacherFilter | getTeachers, getTeacherDetails, storeTeacher, updateTeacher, updatePassword, updateRate, deleteTeacher |
