# Repository Index

| Repository | Public Methods |
|------------|----------------|
| App\Repositories\AttendanceRepository | getAttendancesQuery, getAttendanceWithRelations, updateOrCreate, getStats |
| App\Repositories\BookRepository | getBooksQuery, getActiveBooksQuery, findOrFail, create, update, delete |
| App\Repositories\CourseRepository | getCoursesQuery, findOrFail, create, update, delete |
| App\Repositories\DashboardRepository | getUserCountsByRole, getPendingInquiriesCount, getRecentEnrollments, getRecentInquiries, getRevenueForMonth, getMonthlyEnrollmentTrends, getMonthlyRevenueTrends, getAttendanceSummary, getEvaluationsSummary, getInquiryConversionRate, getTopCoursesPerformance, getTeacherPerformanceRanking, getMonthlyComparisonData |
| App\Repositories\EnrollmentRepository | getEnrollmentsQuery, findOrFail, create, update, delete |
| App\Repositories\EvaluationRepository | getEvaluationsQuery, getTeacherEvaluations, getMonthlyAverages, getYearlyAverages, updateOrCreate, getExistingEvaluation, getPerformanceStats |
| App\Repositories\InquiryRepository | createInquiry |
| App\Repositories\NewsRepository | getNewsQuery, findOrFail, create, update, delete |
| App\Repositories\NotificationRepository | getPaginatedNotifications, getUnreadNotifications, getUnreadCount, findNotification |
| App\Repositories\ParentDashboardRepository | getChildren, getSchedulesForChildren, getTodaySchedules, getUpcomingSchedules, getAttendancesForChildren, getLatestReports, getUnpaidPayments, getPendingReportsCount |
| App\Repositories\ParentRepository | getParentsQuery, findOrFail, create, update, delete |
| App\Repositories\PaymentRepository | getEnrollmentsQuery, getStudentsQuery, getStatsQuery, getCourses |
| App\Repositories\PricingTierRepository | getPricingTiersQuery, findOrFail, create, update, delete, findDuplicate |
| App\Repositories\ProfileRepository | update |
| App\Repositories\ReportRepository | getReportsQuery, findOrFail |
| App\Repositories\RolePermissionRepository | getUsersPaginated, getAllRolesWithPermissions, getAllPermissions, createRole, createPermission, updateRole, updateUser, createUser, deleteUser |
| App\Repositories\ScheduleRepository | getSchedulesQuery, getScheduleWithRelations, create, update, delete, bulkCancel, bulkDelete |
| App\Repositories\SchedulerDashboardRepository | getTodaySchedules, getUpcomingSchedules, getStudentsCount, getTeachersCount, getPendingAttendanceCount, getMonthlyRevenue, getReportsCount, searchUsers, getStudentsQuery, getTeachersQuery |
| App\Repositories\StudentCourseRepository | getEnrollments, getCourseStats, getMasteryScores, getNextSessions |
| App\Repositories\StudentDashboardRepository | getEnrollments, getCourseProgressStats, getTodaySchedules, getWeekSchedules, getRecentReports, getRecentResources, getUnpaidPayments, getCompletedSchedulesCount, getTotalSchedulesCount, getCompletedThisWeekCount |
| App\Repositories\StudentEvaluationRepository | create, update, delete, find, getPendingEvaluationsForTeacher, getStudentMonthlyScores, getStudentEvaluations, getTeacherEvaluations, getAggregateScores, getEvaluationByMonth, getEvaluationsQuery |
| App\Repositories\StudentProfileRepository | updateProfile, updatePassword, updateAvatar, getStats |
| App\Repositories\StudentReportRepository | getReportsQuery, getCoursesWithReports, getTeachersWithReports, getReport |
| App\Repositories\StudentRepository | getStudentsQuery, findOrFail, create, update, delete |
| App\Repositories\StudentResourceRepository | getResourcesQuery, getCoursesForStudent, getResource |
| App\Repositories\StudentScheduleRepository | getSchedulesForRange, getSchedulesForDate |
| App\Repositories\TeacherApplicationRepository | createApplication |
| App\Repositories\TeacherAttendanceRepository | getScheduleForTeacher, updateOrCreateAttendance |
| App\Repositories\TeacherDashboardRepository | getWeekSchedules, getMyStudents, getStudentsNeedingReports, getRecentReports, getRecentResources, getThisMonthHours |
| App\Repositories\TeacherHourRepository | getTeachers, getPayrollRecords, updateOrCreate |
| App\Repositories\TeacherHoursRepository | getAttendancesQueryForMonth |
| App\Repositories\TeacherProfileRepository | getTeacherStats, updateUser |
| App\Repositories\TeacherReportRepository | getPaginatedReports, getTeacherStudents, getTeacherCourses, createReport, updateReport, deleteReport |
| App\Repositories\TeacherRepository | getTeachersQuery, getTeacherWithRelations, create, update, delete |
| App\Repositories\TeacherResourceRepository | getPaginatedResources, getTeacherStudents, getTeacherCourses, createResource, updateResource, deleteResource |
| App\Repositories\TeacherScheduleRepository | getSchedulesForRange |
