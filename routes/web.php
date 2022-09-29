<?php
use App\Http\Controllers\UpdateUserProfileController;
use App\Http\Controllers\MailTicketController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('php-info', function () {
    echo phpinfo();
});
Route::get('/login', 'LoginController@showLoginForm')->name('login');
//Route::post('/login', 'LoginController@login')->name('login');
Route::get('/logout', 'Auth\LoginController@logout');


Auth::routes();
// Route::get('/', function () {
//     return view('welcome');
// });


Route::group(['middleware' => ['auth', 'checking_user_active_or_not', 'force_to_change_password']], function(){

    Route::get('/mail',[MailTicketController::class,'mail'])->name('ticket.mail');
    Route::get('/mailsend',[MailTicketController::class,'sendmail'])->name('ticket.sendmail');
   // Route::get('/header',[UpdateUserProfileController::class,'index'])->name('header.index');
    Route::get('/user/edit/{id}',[UpdateUserProfileController::class,'edit'])->name('user.edit');
    Route::post('/user/update/{id}',[UpdateUserProfileController::class,'update'])->name('user.update');

    Route::get('/circle_test', 'TicketController@circle_test')->name('circle_test');
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/sendmail', 'DashboardController@sendEmail')->name('sendmail');
    Route::post('/audit_company_post', 'UserController@makeaudit');
    Route::post('/category_admin', 'UserController@makedpadmin');
    Route::get('/backup_db', 'DashboardController@bdBackup')->name('bdBackup');
    Route::get('/only_db', 'DashboardController@todayBackup')->name('todayBackup');
    Route::get('/db_file', 'DashboardController@withFileBackup')->name('withFileBackup');
    Route::get('/backupDelete/{folder}/{id}', 'DashboardController@backupDelete')->name('backupDelete');
    Route::get('/home', 'DashboardController@index')->name('home');
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/audit_report', 'DashboardController@AuditFullReport')->name('audit-report');
    Route::get('/report_search', 'DashboardController@ViewReport')->name('report-search');
    Route::get('/initiat_report_search', 'DashboardController@InitiatViewReport')->name('initiat-report-search');
    Route::resource('/category', 'CategoryController');
    Route::resource('/sub-category', 'SubCategoryController');
    Route::resource('/users', 'UserController');
    //Route::post('/users', 'UserController@index');
    Route::get('/users/makeadmin/{id}/{status}', 'UserController@makeadmin');
    Route::POST('/user_company_list', 'UserController@user_company_list');
    Route::POST('/category_list', 'UserController@category_list');
    Route::get('/user_department_list/{id}', 'UserController@getUserDeparmentList')->name('user_department_list');
    Route::get('/user_company_list/{id}', 'UserController@getUserCompanyList')->name('user_company_list');
    Route::post('/get_sub_cat', 'CommonController@subCatList');
    Route::get('/get_all_data', ['uses' => 'UserController@ldapInfo']);
    Route::get('/request/inbox', 'TicketController@inbox');
    Route::get('/request/new', 'TicketController@create');
    Route::post('/request_new', 'TicketController@store');
    Route::get('/request/pending', ['uses' => 'TicketController@ticket_list', 'tStatus' => 2]);
    Route::get('/request/drafts', ['uses' => 'TicketController@drafts', 'tStatus' => 1]);
    Route::get('/request/request_info', ['uses' => 'TicketController@requestInfo', 'tStatus' => 6]);
    Route::get('/request/rejected', ['uses' => 'TicketController@rejected', 'tStatus' => 5]);
    Route::get('/request/approved', ['uses' => 'TicketController@approved', 'tStatus' => 4]);
    Route::get('/request/details/{Id}', ['uses' => 'TicketController@ticket_details']);
    Route::POST('/request/update_status', ['uses' => 'TicketController@update_status']);
    Route::get('/request/draft-edit/{id}', ["as" => "draft_edit", 'uses' => 'TicketController@DraftEdit']);
    Route::get('/request/draft-delete/{id}', ["as" => "draft_edit", 'uses' => 'TicketController@DraftDelete']);
    Route::POST('/request/deleteOldFile', ['uses' => 'TicketController@deleteOldFile']);
    Route::POST('/request/update_draft_request', ['uses' => 'TicketController@UpdateDraftRequest']);

    Route::POST('/request/request_status_update', ['uses' => 'TicketController@RequestStatusUpdate']);

    Route::get('/reassign', ['uses' => 'TicketController@reassign']);
    Route::POST('/search_assignment', ['uses' => 'TicketController@searchAssignment']);
    Route::POST('/update_assignment', ['uses' => 'TicketController@updateAssignment']);

    Route::get('/server-dns', 'ServerDnsNameController@index')->name('server-dns.index');
    Route::put('/server-dns/{id}', 'ServerDnsNameController@update')->name('server-dns.update');

    Route::resource('/vacations', 'VacationController');

    Route::get('/ldap_info', ['uses' => 'UserController@ldapInfo']);

    Route::POST('/filterAduser', ['uses' => 'TicketController@filterAduser']);
    Route::POST('/search_ad_user', ['uses' => 'TicketController@searchAdUser']);
    Route::POST('/search_ad_user_modal', ['uses' => 'TicketController@searchAdUserInModal']);
    Route::POST('/search_ad_user_modal_advancedSearch', ['uses' => 'TicketController@advancedSearchAdUserInModal']);
    Route::get('/company/list', ['uses' => 'CompanyName@index']);
    Route::get('/company/create', ['uses' => 'CompanyName@create']);
    Route::Post('/company/store', ['uses' => 'CompanyName@store']);
    Route::get('/company/edit/{id}', ['uses' => 'CompanyName@edit']);
    Route::POST('company/update/{id}', ['uses' => 'CompanyName@update']);
    Route::POST('report_search', ['uses' => 'ReportController@search']);
    Route::get('acknowledgement/{id}', ['uses' => 'ReportController@acknowledgementView']);
    Route::get('pdf/{id}', ['uses' => 'ReportController@CreatePDF']);
    Route::get('acknowledgement_list', ['uses' => 'ReportController@acknowledgement_list']);
    Route::get('/department_report', 'ReportController@departmentReport')->name('department-report');
    Route::POST('/save_archive', ['uses' => 'ArchiveCopntroller@save_archive']);
    Route::get('/archive/index', ['uses' => 'ArchiveCopntroller@index']);
    Route::get('/archive/archive/{id}', ['uses' => 'ArchiveCopntroller@archive']);
    Route::get('/archive/archiveBack/{id}', ['uses' => 'ArchiveCopntroller@archiveBack']);
    Route::get('/archive/archive_list', ['uses' => 'ArchiveCopntroller@archive_list']);
    Route::get('/archive/archive_search', ['uses' => 'ArchiveCopntroller@archiveSearch']);
    Route::POST('archive_report_search', ['uses' => 'ArchiveCopntroller@archiveReportSearch']);

    /** Start Manage Ticket Routes */

    Route::get('/get_manage_tickets', 'TicketManageController@index')->name('get_manage_tickets');
    Route::put('/update_view_status/{id}', 'TicketManageController@updateViewStatus')->name('update_view_status');
    Route::get('/manage_ticket_view/{id}', 'TicketManageController@ticketView')->name('manage_ticket_view');
    Route::get('/manage_ticket_edit/{id}', 'TicketManageController@ticketView')->name('manage_ticket_edit');
    Route::post('/manage_ticket_update/{id}', 'TicketManageController@ticketUpdate')->name('manage_ticket_update');
    Route::get('/ticket_edit_view/{id}', 'TicketManageController@ticketEditView')->name('ticket_edit_view');

    /** End Manage Ticket Routes */

    Route::get('archive_acknowledgement/{id}', ['uses' => 'ArchiveCopntroller@archiveAcknowledgement']);
    Route::get('/archive/pdf/{id}', ['uses' => 'ArchiveCopntroller@archivePDF']);
    Route::get('/contact_us', ['uses' => 'DashboardController@mail']);

    Route::POST('live_report_search', ['uses' => 'ArchiveCopntroller@liveReportSearch']);
    Route::get('/archive/live_acknowledgement_view/{id}', ['uses' => 'ArchiveCopntroller@liveAcknowledgement']);


    # TimyMce file upload
    Route::POST('/file_upload_tinymce', ['uses' => 'TicketController@fileUploadTinyMce']);


    /*================================ Audit Logs Routes ================================*/
    Route::get('/audit_logs', 'V2\AuditLogController@index')->name('auditLogs.index');
    Route::get('/audit_logs/{id}', 'V2\AuditLogController@show')->name('auditLogs.show');

    /*================================ Department Admin Routes ================================*/
    Route::get('subordinate-users', 'DepartmentAdmin\SubordinateUsersController@index')->name('subordinate.users.index');
    Route::get('search-subordinate-users', 'DepartmentAdmin\SubordinateUsersController@searchSubordinateUser')->name('subordinate.users.search');
    Route::post('subordinate-users/store', 'DepartmentAdmin\SubordinateUsersController@addSubordinateUser')->name('subordinate.users.store');
    Route::get('subordinate-users-list/{user_id}', 'DepartmentAdmin\SubordinateUsersController@userSubordinateLists')->name('subordinate.users.list');
    Route::get('subordinate-user-tickets', 'DepartmentAdmin\SubordinateUserTicketReportController@index')->name('subordinate.users.tickets');

    /*================================ Settings ================================*/
    Route::get('setting', "Setting\SettingController@index")->name('setting.index');
    Route::post('setting', "Setting\SettingController@store")->name('setting.store');

    /*================================ Change Password ================================*/
    Route::get('change-password', 'Password\ChangePasswordController@index')->name('change.password.index');
    Route::post('change-password', 'Password\ChangePasswordController@store')->name('change.password.store');
});
/*================================ Force Password Change ================================*/
Route::get('force/password_change', 'Password\ForceChangePasswordController@index')->name('force.password_change.index')->middleware('auth');
Route::post('force/password_change', 'Password\ForceChangePasswordController@store')->name('force.password_change.store')->middleware('auth');
// Route::get('/home', 'HomeController@index')->name('home');
