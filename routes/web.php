<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\FeeInvoiceController;
use App\Http\Controllers\LibraryBookController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ParentProfileController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\NotificationController;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Notifications

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
Route::put('/notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
Route::post('/notifications/import', [NotificationController::class, 'import'])->name('notifications.import');
// Management/Adminstration

Route::get('/administration', [ManagementController::class, 'index'])->name('management');
Route::post('/management', [ManagementController::class, 'store'])->name('management.store');
Route::put('/management/{management}', [ManagementController::class, 'update'])->name('management.update');
Route::delete('/management/{management}', [ManagementController::class, 'destroy'])->name('management.destroy');
Route::post('/management/import', [ManagementController::class, 'import'])->name('management.import');
//transport

Route::get('/transport', [TransportController::class, 'index'])->name('transport.index');
Route::post('/transports', [TransportController::class, 'store'])->name('transport.store');
Route::put('/transports/{transport}', [TransportController::class, 'update'])->name('transport.update');
Route::delete('/transports/{transport}', [TransportController::class, 'destroy'])->name('transport.destroy');
Route::post('/transports/import', [TransportController::class, 'import'])->name('transport.import');Route::post('/transports/import', [TransportController::class, 'import'])->name('transport.import');
//Hostel 

Route::get('/hostel', [HostelController::class, 'index'])->name('hostels');
Route::post('/hostels', [HostelController::class, 'store'])->name('hostels.store');
Route::put('/hostels/{hostel}', [HostelController::class, 'update'])->name('hostels.update');
Route::delete('/hostels/{hostel}', [HostelController::class, 'destroy'])->name('hostels.destroy');
Route::post('/hostels/import', [HostelController::class, 'import'])->name('hostels.import');
// parent

Route::get('/parents', [ParentProfileController::class, 'index'])->name('parents');
Route::post('/parents', [ParentProfileController::class, 'store'])->name('parents.store');
Route::put('/parents/{parentProfile}', [ParentProfileController::class, 'update'])->name('parents.update');
Route::delete('/parents/{parentProfile}', [ParentProfileController::class, 'destroy'])->name('parents.destroy');
Route::post('/parents/import', [ParentProfileController::class, 'import'])->name('parents.import');
//staff

Route::post('/staffs/import', [StaffController::class, 'import'])->name('staffs.import');
Route::get('/staff', [StaffController::class, 'index'])->name('staffs');
Route::post('/staffs', [StaffController::class, 'store'])->name('staffs.store');
Route::put('/staffs/{staff}', [StaffController::class, 'update'])->name('staffs.update');
Route::delete('/staffs/{staff}', [StaffController::class, 'destroy'])->name('staffs.destroy');

//Activity routes

Route::get('/activities', [ActivityController::class, 'index'])->name('activities');
Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

//library issues
Route::post('/library/import', [LibraryBookController::class, 'import'])->name('library.import');
Route::post('/library/issues', [LibraryBookController::class, 'issueBook'])->name('library.issue');
Route::put('/library/issues/{issue}/return', [LibraryBookController::class, 'returnBook'])->name('library.return');
Route::delete('/library/issues/{issue}', [LibraryBookController::class, 'deleteIssue'])->name('library.issue.destroy');

//library
Route::get('/library', [LibraryBookController::class, 'index'])->name('library');
Route::post('/library', [LibraryBookController::class, 'store'])->name('library.store');
Route::put('/library/{libraryBook}', [LibraryBookController::class, 'update'])->name('library.update');
Route::delete('/library/{libraryBook}', [LibraryBookController::class, 'destroy'])->name('library.destroy');

//invoice
Route::get('/fees', [FeeInvoiceController::class, 'index'])->name('fees');
Route::post('/fees', [FeeInvoiceController::class, 'store'])->name('fees.store');
Route::put('/fees/{feeInvoice}', [FeeInvoiceController::class, 'update'])->name('fees.update');
Route::delete('/fees/{feeInvoice}', [FeeInvoiceController::class, 'destroy'])->name('fees.destroy');

Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects');
Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers');
Route::post('/teachers/store', [TeacherController::class, 'store'])->name('teachers.store');
Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
Route::get('/students', [StudentController::class, 'index'])->name('students');
Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
Route::post('/students/import', [StudentController::class, 'import'])
    ->name('students.import');
Route::put('/students/{student}', [StudentController::class, 'update'])
    ->name('students.update');
// SchoolClass
Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes');
Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
Route::put('/classes/{schoolClass}', [SchoolClassController::class, 'update'])->name('classes.update');
Route::delete('/classes/{schoolClass}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');
// });
//admission
Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions');
Route::post('/admissions', [AdmissionController::class, 'store'])->name('admissions.store');
Route::put('/admissions/{admission}', [AdmissionController::class, 'update'])->name('admissions.update');
// Public admission page
Route::get('/apply-admission', [AdmissionController::class, 'create'])->name('admissions.apply');
Route::post('/apply-admission', [AdmissionController::class, 'publicStore'])->name('admissions.public.store');

//attendance
Route::get('/attendance', function () {
    return Inertia::render('Attendance/Index');
})->name('attendance');

//Exams
Route::get('/exams', function () {
    return Inertia::render('Exams/Index');
})->name('exams');
//results
Route::get('/results', function () {
    return Inertia::render('Results/Index');
})->name('results');

Route::get('/student/dashboard', function () {
    return Inertia::render('User/StudentDashboard');
})->name('student.dashboard');

Route::get('/parent/dashboard', function () {
    return Inertia::render('User/ParentDashboard');
})->name('parent.dashboard');

//Teacher dashboard
Route::get('/teacher/dashboard', function () {
    return Inertia::render('User/TeacherDashboard');
})->name('teacher.dashboard');

//class teacher dashboard
Route::get('/class-teacher/dashboard', function () {
    return Inertia::render('User/ClassTeacherDashboard');
})->name('class-teacher.dashboard');

//staff dashboard
Route::get('/staff/dashboard', function () {
    return Inertia::render('User/StaffDashboard');
})->name('staff.dashboard');
//transportation
// Route::get('/transportation', function () {
//     return Inertia::render('Transport/Index');
// })->name('transportation');
//staff
// Route::get('/staff', function () {
//     return Inertia::render('Staffs/Index');
// })->name('staff');
//parents
// Route::get('/parents', function () {
//     return Inertia::render('Parents/Index');
// })->name('parents');
//Report
Route::get('/reports', function () {
    return Inertia::render('Reports/Index');
})->name('reports');
//Settings
Route::get('/settings', function () {
    return Inertia::render('Settings/Index');
})->name('settings');
//Administration
// Route::get('/administration', function () {
//     return Inertia::render('Administrations/Index');
// })->name('administration');
//Notifications
// Route::get('/notifications', function () {
//     return Inertia::render('Notifications/Index');
// })->name('notifications');
//hostels
// Route::get('/hostel', function () {
//     return Inertia::render('Hostels/Index');
// })->name('hostels');
//calender
Route::get('/calendar', function () {
    return Inertia::render('Calendar/Index');
})->name('calendar');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
