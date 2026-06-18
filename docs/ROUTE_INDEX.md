# Route Index

| Method | URI | Action | Middleware |
|--------|-----|--------|------------|
| GET|HEAD | up | Closure |  |
| GET|HEAD | / | App\Http\Controllers\Pages\HomeController@index | web, throttle:60,1 |
| GET|HEAD | our-programs | App\Http\Controllers\Pages\OurProgramsController@index | web, throttle:60,1 |
| GET|HEAD | our-teachers | App\Http\Controllers\Pages\OurTeachersController@index | web, throttle:60,1 |
| GET|HEAD | courses | App\Http\Controllers\Pages\CoursesController@index | web, throttle:60,1 |
| GET|HEAD | contact | App\Http\Controllers\Pages\ContactController@index | web, throttle:60,1 |
| GET|HEAD | news | App\Http\Controllers\Pages\NewsController@index | web, throttle:60,1 |
| GET|HEAD | news/{slug} | App\Http\Controllers\Pages\NewsController@show | web, throttle:60,1 |
| POST | inquiry | App\Http\Controllers\InquiryController@store | web, throttle:60,1 |
| GET|HEAD | get-started | App\Http\Controllers\InquiryController@getStarted | web, throttle:60,1 |
| GET|HEAD | register | Closure | web, throttle:60,1 |
| GET|HEAD | teacher-application | App\Http\Controllers\TeacherApplicationController@create | web, throttle:60,1 |
| POST | teacher-application | App\Http\Controllers\TeacherApplicationController@store | web, throttle:60,1 |
| GET|HEAD | teacher-application/success | App\Http\Controllers\TeacherApplicationController@success | web, throttle:60,1 |
| GET|HEAD | login | App\Http\Controllers\Auth\LoginController@create | web, throttle:60,1, guest |
| POST | login | App\Http\Controllers\Auth\LoginController@store | web, throttle:60,1, guest |
| GET|HEAD | refresh-csrf | Closure | web, throttle:60,1 |
| POST | logout | Closure | web, throttle:60,1, auth |
| GET|HEAD | dashboard | Closure | web, throttle:60,1, auth |
| GET|HEAD | dashboard/books | App\Http\Controllers\Dashboard\BookController@index | web, throttle:60,1, auth |
| GET|HEAD | dashboard/books/create | App\Http\Controllers\Dashboard\BookController@create | web, throttle:60,1, auth |
| POST | dashboard/books | App\Http\Controllers\Dashboard\BookController@store | web, throttle:60,1, auth |
| POST | dashboard/books/upload-pdf-chunk | App\Http\Controllers\Dashboard\BookController@uploadPdfChunk | web, throttle:60,1, auth |
| GET|HEAD | dashboard/books/{book} | App\Http\Controllers\Dashboard\BookController@show | web, throttle:60,1, auth |
| GET|HEAD | dashboard/books/{book}/edit | App\Http\Controllers\Dashboard\BookController@edit | web, throttle:60,1, auth |
| PATCH | dashboard/books/{book} | App\Http\Controllers\Dashboard\BookController@update | web, throttle:60,1, auth |
| DELETE | dashboard/books/{book} | App\Http\Controllers\Dashboard\BookController@destroy | web, throttle:60,1, auth |
| GET|HEAD | dashboard/books/{book}/stream | App\Http\Controllers\Dashboard\BookController@stream | web, throttle:60,1, auth |
| GET|HEAD | dashboard/books/{book}/download | App\Http\Controllers\Dashboard\BookController@download | web, throttle:60,1, auth |
| GET|HEAD | accountant | App\Http\Controllers\Accountant\DashboardController@index | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| GET|HEAD | accountant/payments | App\Http\Controllers\Accountant\PaymentController@index | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| GET|HEAD | accountant/payments/enrollment/{enrollment} | App\Http\Controllers\Accountant\PaymentController@show | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| PATCH | accountant/payments/enrollment/{enrollment} | App\Http\Controllers\Accountant\PaymentController@updateEnrollment | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| DELETE | accountant/payments/enrollment/{enrollment} | App\Http\Controllers\Accountant\PaymentController@destroyEnrollment | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| PATCH | accountant/payments/{payment}/status | App\Http\Controllers\Accountant\PaymentController@updateStatus | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| GET|HEAD | accountant/teacher-hours | App\Http\Controllers\Accountant\TeacherHourController@index | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| POST | accountant/teacher-hours/mark-paid | App\Http\Controllers\Accountant\TeacherHourController@markAsPaid | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| POST | accountant/teacher-hours/mark-unpaid | App\Http\Controllers\Accountant\TeacherHourController@markAsUnpaid | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| PATCH | accountant/teacher-hours/{teacher}/update-rate | App\Http\Controllers\Accountant\TeacherHourController@updateRate | web, auth, role_or_permission:Accountant|SuperAdmin|Admin|manage accounting |
| GET|HEAD | storage/{path} | Closure |  |
| GET|HEAD | admin/superadmin | App\Http\Controllers\SuperAdmin\RolePermissionController@index | web, auth, role:SuperAdmin |
| GET|HEAD | admin/superadmin/roles | App\Http\Controllers\SuperAdmin\RolePermissionController@manageRoles | web, auth, role:SuperAdmin |
| POST | admin/superadmin/roles | App\Http\Controllers\SuperAdmin\RolePermissionController@storeRole | web, auth, role:SuperAdmin |
| POST | admin/superadmin/roles/{role}/permissions | App\Http\Controllers\SuperAdmin\RolePermissionController@updateRolePermissions | web, auth, role:SuperAdmin |
| POST | admin/superadmin/assign-role/{user} | App\Http\Controllers\SuperAdmin\RolePermissionController@assignRole | web, auth, role:SuperAdmin |
| POST | admin/superadmin/permissions | App\Http\Controllers\SuperAdmin\RolePermissionController@storePermission | web, auth, role:SuperAdmin |
| POST | admin/superadmin/users | App\Http\Controllers\SuperAdmin\RolePermissionController@storeUser | web, auth, role:SuperAdmin |
| DELETE | admin/superadmin/users/{user} | App\Http\Controllers\SuperAdmin\RolePermissionController@destroyUser | web, auth, role:SuperAdmin |
| GET|HEAD | admin/dashboard | App\Http\Controllers\Admin\DashboardController@index | web, auth |
| GET|HEAD | admin/inquiries | App\Http\Controllers\Admin\InquiryController@index | web, auth |
| GET|HEAD | admin/inquiries/{inquiry} | App\Http\Controllers\Admin\InquiryController@show | web, auth |
| POST | admin/inquiries/{inquiry}/convert | App\Http\Controllers\Admin\InquiryController@convertToParent | web, auth |
| PATCH | admin/inquiries/{inquiry}/status | App\Http\Controllers\Admin\InquiryController@updateStatus | web, auth |
| DELETE | admin/inquiries/{inquiry} | App\Http\Controllers\Admin\InquiryController@destroy | web, auth |
| GET|HEAD | admin/parents | App\Http\Controllers\Admin\ParentController@index | web, auth |
| GET|HEAD | admin/parents/create | App\Http\Controllers\Admin\ParentController@create | web, auth |
| POST | admin/parents | App\Http\Controllers\Admin\ParentController@store | web, auth |
| GET|HEAD | admin/parents/{parent} | App\Http\Controllers\Admin\ParentController@show | web, auth |
| PATCH | admin/parents/{parent} | App\Http\Controllers\Admin\ParentController@update | web, auth |
| PATCH | admin/parents/{parent}/password | App\Http\Controllers\Admin\ParentController@updatePassword | web, auth |
| DELETE | admin/parents/{parent} | App\Http\Controllers\Admin\ParentController@destroy | web, auth |
| POST | admin/parents/{parent}/children | App\Http\Controllers\Admin\ParentController@addChild | web, auth |
| DELETE | admin/parents/{parent}/children/{child} | App\Http\Controllers\Admin\ParentController@removeChild | web, auth |
| GET|HEAD | admin/teacher-applications | App\Http\Controllers\Admin\TeacherApplicationController@index | web, auth |
| GET|HEAD | admin/teacher-applications/{application} | App\Http\Controllers\Admin\TeacherApplicationController@show | web, auth |
| POST | admin/teacher-applications/{application}/convert | App\Http\Controllers\Admin\TeacherApplicationController@convertToTeacher | web, auth |
| PATCH | admin/teacher-applications/{application}/status | App\Http\Controllers\Admin\TeacherApplicationController@updateStatus | web, auth |
| DELETE | admin/teacher-applications/{application} | App\Http\Controllers\Admin\TeacherApplicationController@destroy | web, auth |
| GET|HEAD | admin/students | App\Http\Controllers\Admin\StudentController@index | web, auth |
| GET|HEAD | admin/students/{student} | App\Http\Controllers\Admin\StudentController@show | web, auth |
| POST | admin/students | App\Http\Controllers\Admin\StudentController@store | web, auth |
| PATCH | admin/students/{student} | App\Http\Controllers\Admin\StudentController@update | web, auth |
| DELETE | admin/students/{student} | App\Http\Controllers\Admin\StudentController@destroy | web, auth |
| PATCH | admin/students/{student}/password | App\Http\Controllers\Admin\StudentController@updatePassword | web, auth |
| GET|HEAD | admin/teachers | App\Http\Controllers\Admin\TeacherController@index | web, auth |
| GET|HEAD | admin/teachers/create | App\Http\Controllers\Admin\TeacherController@create | web, auth |
| POST | admin/teachers | App\Http\Controllers\Admin\TeacherController@store | web, auth |
| GET|HEAD | admin/teachers/{teacher} | App\Http\Controllers\Admin\TeacherController@show | web, auth |
| GET|HEAD | admin/teachers/{teacher}/edit | App\Http\Controllers\Admin\TeacherController@edit | web, auth |
| PATCH | admin/teachers/{teacher} | App\Http\Controllers\Admin\TeacherController@update | web, auth |
| PATCH | admin/teachers/{teacher}/password | App\Http\Controllers\Admin\TeacherController@updatePassword | web, auth |
| DELETE | admin/teachers/{teacher} | App\Http\Controllers\Admin\TeacherController@destroy | web, auth |
| GET|HEAD | admin/courses | App\Http\Controllers\Admin\CourseController@index | web, auth |
| GET|HEAD | admin/courses/create | App\Http\Controllers\Admin\CourseController@create | web, auth |
| POST | admin/courses | App\Http\Controllers\Admin\CourseController@store | web, auth |
| GET|HEAD | admin/courses/{course} | App\Http\Controllers\Admin\CourseController@show | web, auth |
| GET|HEAD | admin/courses/{course}/edit | App\Http\Controllers\Admin\CourseController@edit | web, auth |
| PATCH | admin/courses/{course} | App\Http\Controllers\Admin\CourseController@update | web, auth |
| DELETE | admin/courses/{course} | App\Http\Controllers\Admin\CourseController@destroy | web, auth |
| GET|HEAD | admin/enrollments | App\Http\Controllers\Admin\EnrollmentController@index | web, auth |
| GET|HEAD | admin/enrollments/create | App\Http\Controllers\Admin\EnrollmentController@create | web, auth |
| POST | admin/enrollments | App\Http\Controllers\Admin\EnrollmentController@store | web, auth |
| GET|HEAD | admin/enrollments/{enrollment} | App\Http\Controllers\Admin\EnrollmentController@show | web, auth |
| GET|HEAD | admin/enrollments/{enrollment}/edit | App\Http\Controllers\Admin\EnrollmentController@edit | web, auth |
| PUT|PATCH | admin/enrollments/{enrollment} | App\Http\Controllers\Admin\EnrollmentController@update | web, auth |
| DELETE | admin/enrollments/{enrollment} | App\Http\Controllers\Admin\EnrollmentController@destroy | web, auth |
| PATCH | admin/enrollments/{enrollment}/payment-status | App\Http\Controllers\Admin\EnrollmentController@updatePaymentStatus | web, auth |
| GET|HEAD | admin/schedules | App\Http\Controllers\Admin\ScheduleController@index | web, auth |
| GET|HEAD | admin/schedules/create | App\Http\Controllers\Admin\ScheduleController@create | web, auth |
| POST | admin/schedules | App\Http\Controllers\Admin\ScheduleController@store | web, auth |
| GET|HEAD | admin/schedules/{schedule} | App\Http\Controllers\Admin\ScheduleController@show | web, auth |
| GET|HEAD | admin/schedules/{schedule}/edit | App\Http\Controllers\Admin\ScheduleController@edit | web, auth |
| PUT|PATCH | admin/schedules/{schedule} | App\Http\Controllers\Admin\ScheduleController@update | web, auth |
| DELETE | admin/schedules/{schedule} | App\Http\Controllers\Admin\ScheduleController@destroy | web, auth |
| POST | admin/schedules/bulk-cancel/{enrollment} | App\Http\Controllers\Admin\ScheduleController@bulkCancel | web, auth |
| DELETE | admin/schedules/bulk-delete/{enrollment} | App\Http\Controllers\Admin\ScheduleController@bulkDelete | web, auth |
| PATCH | admin/teacher-hours/{teacher}/update-rate | App\Http\Controllers\Admin\TeacherController@updateRate | web, auth |
| GET|HEAD | admin/attendances | App\Http\Controllers\Admin\AttendanceController@index | web, auth |
| GET|HEAD | admin/attendances/{attendance} | App\Http\Controllers\Admin\AttendanceController@show | web, auth |
| GET|HEAD | admin/reports | App\Http\Controllers\Admin\ReportController@index | web, auth |
| GET|HEAD | admin/reports/{report} | App\Http\Controllers\Admin\ReportController@show | web, auth |
| GET|HEAD | admin/student-evaluations | App\Http\Controllers\Admin\StudentEvaluationController@index | web, auth |
| GET|HEAD | admin/student-evaluations/{studentEvaluation} | App\Http\Controllers\Admin\StudentEvaluationController@show | web, auth |
| GET|HEAD | admin/profile | App\Http\Controllers\Admin\ProfileController@show | web, auth |
| GET|HEAD | admin/profile/edit | App\Http\Controllers\Admin\ProfileController@edit | web, auth |
| PATCH | admin/profile | App\Http\Controllers\Admin\ProfileController@update | web, auth |
| PATCH | admin/profile/password | App\Http\Controllers\Admin\ProfileController@updatePassword | web, auth |
| POST | admin/profile/avatar | App\Http\Controllers\Admin\ProfileController@updateAvatar | web, auth |
| DELETE | admin/profile/avatar | App\Http\Controllers\Admin\ProfileController@deleteAvatar | web, auth |
| GET|HEAD | admin/notifications | App\Http\Controllers\NotificationController@index | web, auth |
| POST | admin/notifications/{id}/read | App\Http\Controllers\NotificationController@markAsRead | web, auth |
| POST | admin/notifications/read-all | App\Http\Controllers\NotificationController@markAllAsRead | web, auth |
| GET|HEAD | admin/notifications/unread | App\Http\Controllers\NotificationController@getUnread | web, auth |
| GET|HEAD | admin/pricing-tiers | App\Http\Controllers\Admin\PricingTierController@index | web, auth |
| GET|HEAD | admin/pricing-tiers/create | App\Http\Controllers\Admin\PricingTierController@create | web, auth |
| POST | admin/pricing-tiers | App\Http\Controllers\Admin\PricingTierController@store | web, auth |
| GET|HEAD | admin/pricing-tiers/{pricing_tier} | App\Http\Controllers\Admin\PricingTierController@show | web, auth |
| GET|HEAD | admin/pricing-tiers/{pricing_tier}/edit | App\Http\Controllers\Admin\PricingTierController@edit | web, auth |
| PUT|PATCH | admin/pricing-tiers/{pricing_tier} | App\Http\Controllers\Admin\PricingTierController@update | web, auth |
| DELETE | admin/pricing-tiers/{pricing_tier} | App\Http\Controllers\Admin\PricingTierController@destroy | web, auth |
| GET|HEAD | admin/news | App\Http\Controllers\Admin\NewsController@index | web, auth, permission:manage news |
| GET|HEAD | admin/news/create | App\Http\Controllers\Admin\NewsController@create | web, auth, permission:manage news |
| POST | admin/news | App\Http\Controllers\Admin\NewsController@store | web, auth, permission:manage news |
| GET|HEAD | admin/news/{news} | App\Http\Controllers\Admin\NewsController@show | web, auth, permission:manage news |
| GET|HEAD | admin/news/{news}/edit | App\Http\Controllers\Admin\NewsController@edit | web, auth, permission:manage news |
| PUT|PATCH | admin/news/{news} | App\Http\Controllers\Admin\NewsController@update | web, auth, permission:manage news |
| DELETE | admin/news/{news} | App\Http\Controllers\Admin\NewsController@destroy | web, auth, permission:manage news |
| GET|HEAD | scheduler/dashboard | App\Http\Controllers\Scheduler\DashboardController@index | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/dashboard/search | App\Http\Controllers\Scheduler\DashboardController@ajaxSearch | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/students | App\Http\Controllers\Scheduler\DashboardController@students | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/students/search | App\Http\Controllers\Scheduler\DashboardController@ajaxSearchStudents | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/students/{student} | App\Http\Controllers\Scheduler\DashboardController@showStudent | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/teachers | App\Http\Controllers\Scheduler\DashboardController@teachers | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/teachers/search | App\Http\Controllers\Scheduler\DashboardController@ajaxSearchTeachers | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/teachers/{teacher} | App\Http\Controllers\Scheduler\DashboardController@showTeacher | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/availability/{user} | App\Http\Controllers\Scheduler\DashboardController@availability | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| POST | scheduler/availability/{user} | App\Http\Controllers\Scheduler\DashboardController@saveAvailability | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/schedules | App\Http\Controllers\Admin\ScheduleController@index | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/schedules/create | App\Http\Controllers\Admin\ScheduleController@create | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| POST | scheduler/schedules | App\Http\Controllers\Admin\ScheduleController@store | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/schedules/{schedule} | App\Http\Controllers\Admin\ScheduleController@show | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/schedules/{schedule}/edit | App\Http\Controllers\Admin\ScheduleController@edit | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| PUT|PATCH | scheduler/schedules/{schedule} | App\Http\Controllers\Admin\ScheduleController@update | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| DELETE | scheduler/schedules/{schedule} | App\Http\Controllers\Admin\ScheduleController@destroy | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/attendance | App\Http\Controllers\Admin\AttendanceController@index | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/attendance/create | App\Http\Controllers\Admin\AttendanceController@create | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/attendance/verify/{schedule} | App\Http\Controllers\Admin\AttendanceController@verify | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| POST | scheduler/attendance | App\Http\Controllers\Admin\AttendanceController@store | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/attendance/{attendance} | App\Http\Controllers\Admin\AttendanceController@show | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| GET|HEAD | scheduler/reports | App\Http\Controllers\Admin\ReportController@index | web, auth, role_or_permission:SchedulerManager|SuperAdmin|Admin|QualityControl|manage quality|view evaluations |
| POST | teacher/availability/save | App\Http\Controllers\Scheduler\DashboardController@saveAvailability | web, auth |
| GET|HEAD | teacher/dashboard | App\Http\Controllers\Teacher\DashboardController@index | web, auth |
| GET|HEAD | teacher/reports/student-courses/{student} | App\Http\Controllers\Teacher\ReportController@getStudentCourses | web, auth |
| GET|HEAD | teacher/reports/quick-create/{schedule} | App\Http\Controllers\Teacher\ReportController@quickCreate | web, auth |
| GET|HEAD | teacher/reports | App\Http\Controllers\Teacher\ReportController@index | web, auth |
| GET|HEAD | teacher/reports/create | App\Http\Controllers\Teacher\ReportController@create | web, auth |
| POST | teacher/reports | App\Http\Controllers\Teacher\ReportController@store | web, auth |
| GET|HEAD | teacher/reports/{report} | App\Http\Controllers\Teacher\ReportController@show | web, auth |
| GET|HEAD | teacher/reports/{report}/edit | App\Http\Controllers\Teacher\ReportController@edit | web, auth |
| PUT|PATCH | teacher/reports/{report} | App\Http\Controllers\Teacher\ReportController@update | web, auth |
| DELETE | teacher/reports/{report} | App\Http\Controllers\Teacher\ReportController@destroy | web, auth |
| GET|HEAD | teacher/student-evaluations | App\Http\Controllers\Teacher\StudentEvaluationController@index | web, auth |
| GET|HEAD | teacher/student-evaluations/create | App\Http\Controllers\Teacher\StudentEvaluationController@create | web, auth |
| GET|HEAD | teacher/student-evaluations/pending | App\Http\Controllers\Teacher\StudentEvaluationController@pending | web, auth |
| GET|HEAD | teacher/student-evaluations/summary | App\Http\Controllers\Teacher\StudentEvaluationController@summary | web, auth |
| POST | teacher/student-evaluations | App\Http\Controllers\Teacher\StudentEvaluationController@store | web, auth |
| GET|HEAD | teacher/student-evaluations/{studentEvaluation} | App\Http\Controllers\Teacher\StudentEvaluationController@show | web, auth |
| GET|HEAD | teacher/resources | App\Http\Controllers\Teacher\ResourceController@index | web, auth |
| GET|HEAD | teacher/resources/create | App\Http\Controllers\Teacher\ResourceController@create | web, auth |
| POST | teacher/resources | App\Http\Controllers\Teacher\ResourceController@store | web, auth |
| GET|HEAD | teacher/resources/{resource} | App\Http\Controllers\Teacher\ResourceController@show | web, auth |
| GET|HEAD | teacher/resources/{resource}/edit | App\Http\Controllers\Teacher\ResourceController@edit | web, auth |
| PATCH | teacher/resources/{resource} | App\Http\Controllers\Teacher\ResourceController@update | web, auth |
| DELETE | teacher/resources/{resource} | App\Http\Controllers\Teacher\ResourceController@destroy | web, auth |
| GET|HEAD | teacher/resources/{resource}/download | App\Http\Controllers\Teacher\ResourceController@download | web, auth |
| GET|HEAD | teacher/hours | App\Http\Controllers\Teacher\HoursController@index | web, auth |
| GET|HEAD | teacher/my-students | Closure | web, auth |
| GET|HEAD | teacher/schedule | App\Http\Controllers\Teacher\ScheduleController@index | web, auth |
| GET|HEAD | teacher/schedule/daily | App\Http\Controllers\Teacher\ScheduleController@daily | web, auth |
| POST | teacher/attendance | App\Http\Controllers\Teacher\AttendanceController@store | web, auth |
| POST | teacher/attendance/waiting | App\Http\Controllers\Teacher\AttendanceController@notifyWaiting | web, auth |
| GET|HEAD | teacher/attendance/{schedule} | App\Http\Controllers\Teacher\AttendanceController@show | web, auth |
| GET|HEAD | teacher/profile | App\Http\Controllers\Teacher\ProfileController@show | web, auth |
| GET|HEAD | teacher/profile/edit | App\Http\Controllers\Teacher\ProfileController@edit | web, auth |
| PATCH | teacher/profile | App\Http\Controllers\Teacher\ProfileController@update | web, auth |
| PATCH | teacher/profile/password | App\Http\Controllers\Teacher\ProfileController@updatePassword | web, auth |
| POST | teacher/profile/avatar | App\Http\Controllers\Teacher\ProfileController@updateAvatar | web, auth |
| DELETE | teacher/profile/avatar | App\Http\Controllers\Teacher\ProfileController@deleteAvatar | web, auth |
| GET|HEAD | teacher/notifications | App\Http\Controllers\NotificationController@index | web, auth |
| POST | teacher/notifications/{id}/read | App\Http\Controllers\NotificationController@markAsRead | web, auth |
| POST | teacher/notifications/read-all | App\Http\Controllers\NotificationController@markAllAsRead | web, auth |
| GET|HEAD | teacher/notifications/unread | App\Http\Controllers\NotificationController@getUnread | web, auth |
| GET|HEAD | student/dashboard | App\Http\Controllers\Student\DashboardController@index | web, auth |
| GET|HEAD | student/schedule/weekly | App\Http\Controllers\Student\ScheduleController@weekly | web, auth |
| GET|HEAD | student/schedule/daily | App\Http\Controllers\Student\ScheduleController@daily | web, auth |
| GET|HEAD | student/courses | App\Http\Controllers\Student\CourseController@index | web, auth |
| GET|HEAD | student/resources | App\Http\Controllers\Student\ResourceController@index | web, auth |
| GET|HEAD | student/resources/{resource} | App\Http\Controllers\Student\ResourceController@show | web, auth |
| GET|HEAD | student/resources/{resource}/download | App\Http\Controllers\Student\ResourceController@download | web, auth |
| GET|HEAD | student/reports | App\Http\Controllers\Student\ReportController@index | web, auth |
| GET|HEAD | student/reports/{report} | App\Http\Controllers\Student\ReportController@show | web, auth |
| GET|HEAD | student/profile | App\Http\Controllers\Student\ProfileController@show | web, auth |
| GET|HEAD | student/profile/edit | App\Http\Controllers\Student\ProfileController@edit | web, auth |
| PATCH | student/profile | App\Http\Controllers\Student\ProfileController@update | web, auth |
| PATCH | student/profile/password | App\Http\Controllers\Student\ProfileController@updatePassword | web, auth |
| POST | student/profile/avatar | App\Http\Controllers\Student\ProfileController@updateAvatar | web, auth |
| DELETE | student/profile/avatar | App\Http\Controllers\Student\ProfileController@deleteAvatar | web, auth |
| GET|HEAD | student/notifications | App\Http\Controllers\NotificationController@index | web, auth |
| POST | student/notifications/{id}/read | App\Http\Controllers\NotificationController@markAsRead | web, auth |
| POST | student/notifications/read-all | App\Http\Controllers\NotificationController@markAllAsRead | web, auth |
| GET|HEAD | student/notifications/unread | App\Http\Controllers\NotificationController@getUnread | web, auth |
| GET|HEAD | parent/dashboard | App\Http\Controllers\ParentUser\DashboardController@index | web, auth |
| GET|HEAD | parent/children | App\Http\Controllers\ParentUser\ChildrenController@index | web, auth |
| GET|HEAD | parent/children/{child} | App\Http\Controllers\ParentUser\ChildrenController@show | web, auth |
| GET|HEAD | parent/schedule/weekly | App\Http\Controllers\ParentUser\ScheduleController@weekly | web, auth |
| GET|HEAD | parent/schedule/daily | App\Http\Controllers\ParentUser\ScheduleController@daily | web, auth |
| GET|HEAD | parent/reports | App\Http\Controllers\ParentUser\ReportController@index | web, auth |
| GET|HEAD | parent/reports/{report} | App\Http\Controllers\ParentUser\ReportController@show | web, auth |
| GET|HEAD | parent/children/{child}/evaluations | App\Http\Controllers\ParentUser\EvaluationController@show | web, auth |
| GET|HEAD | parent/attendance | App\Http\Controllers\ParentUser\AttendanceController@index | web, auth |
| GET|HEAD | parent/profile | App\Http\Controllers\ParentUser\ProfileController@show | web, auth |
| GET|HEAD | parent/profile/edit | App\Http\Controllers\ParentUser\ProfileController@edit | web, auth |
| PATCH | parent/profile | App\Http\Controllers\ParentUser\ProfileController@update | web, auth |
| PATCH | parent/profile/password | App\Http\Controllers\ParentUser\ProfileController@updatePassword | web, auth |
| POST | parent/profile/avatar | App\Http\Controllers\ParentUser\ProfileController@updateAvatar | web, auth |
| DELETE | parent/profile/avatar | App\Http\Controllers\ParentUser\ProfileController@deleteAvatar | web, auth |
| GET|HEAD | parent/notifications | App\Http\Controllers\NotificationController@index | web, auth |
| POST | parent/notifications/{id}/read | App\Http\Controllers\NotificationController@markAsRead | web, auth |
| POST | parent/notifications/read-all | App\Http\Controllers\NotificationController@markAllAsRead | web, auth |
| GET|HEAD | parent/notifications/unread | App\Http\Controllers\NotificationController@getUnread | web, auth |
| GET|HEAD | quality-control | App\Http\Controllers\QualityControl\EvaluationController@center | web, auth, role_or_permission:SuperAdmin|QualityControl|manage quality|view evaluations |
| GET|HEAD | quality-control/evaluation-center | App\Http\Controllers\QualityControl\EvaluationController@center | web, auth, role_or_permission:SuperAdmin|QualityControl|manage quality|view evaluations |
| GET|HEAD | quality-control/evaluate/{teacher} | App\Http\Controllers\QualityControl\EvaluationController@create | web, auth, role_or_permission:SuperAdmin|QualityControl|manage quality|view evaluations |
| POST | quality-control/evaluate/{teacher} | App\Http\Controllers\QualityControl\EvaluationController@store | web, auth, role_or_permission:SuperAdmin|QualityControl|manage quality|view evaluations |
| GET|HEAD | quality-control/reports/teacher/{teacher} | App\Http\Controllers\QualityControl\EvaluationController@teacherReport | web, auth, role_or_permission:SuperAdmin|QualityControl|manage quality|view evaluations |
