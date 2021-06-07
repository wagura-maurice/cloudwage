<?php

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

use App\Http\Controllers\EmployeeContractsController;
use App\Http\Controllers\ReportsController;

Auth::routes();

Route::get('/', ['as' => 'welcome', 'uses' => 'PagesController@welcome', 'middleware' => 'guest']);
Route::get('/registered', ['uses' => 'Auth\RegisterController@postRegister', 'middleware' => 'guest']);
Route::get('/activate/{id}', [
    'uses' => 'Auth\RegisterController@activate',
    'middleware' => 'guest',
])->name('activate');

Route::group(['middleware' => ['auth', 'connection']], function () {
    Route::get('/dashboard', ['as' => 'dashboard', 'uses' => 'PagesController@dashboard']);
//    Route::get('logout', ['as' => 'logout', 'uses' => 'Authentication\AuthController@logout']);
    Route::get('user-profile', ['as' => 'user.profile', 'uses' => 'UsersController@profile']);
    Route::post('user-profile', ['as' => 'user.profile.update', 'uses' => 'UsersController@postProfile']);
    Route::resource('profile', 'CompanyProfileController');
    Route::resource('departments', 'DepartmentsController');
    Route::get('department-print/{department}', ['as' => 'departments.generate', 'uses' => 'DepartmentsController@generate']);
    Route::post('department-print', ['as' => 'departments.pdfs', 'uses' => 'DepartmentsController@getPDF']);
    Route::resource('allowances', 'AllowancesController');
    Route::resource('employee-allowances', 'EmployeeAllowancesController');
    Route::get('allowances-print/{id}', ['as' => 'allowances.generate', 'uses' => 'AllowancesController@generate']);
    Route::post('allowances-print', ['as' => 'allowances.document', 'uses' => 'AllowancesController@getDocument']);
    Route::resource('deductions', 'DeductionsController');
    Route::resource('employee-deductions', 'EmployeeDeductionsController');
    Route::get('deductions-report/{id}', ['as' => 'deductions.report', 'uses' => 'DeductionsController@report']);
    Route::post('deductions-print/{id}', ['as' => 'deductions.generate', 'uses' => 'DeductionsController@generate']);
    Route::post('deductions-print', ['as' => 'deductions.document', 'uses' => 'DeductionsController@getDocument']);
    Route::resource('reliefs', 'ReliefController');
    Route::resource('employee-types', 'EmployeeTypesController');
    Route::resource('payment-methods', 'PaymentMethodsController');
    Route::resource('payment-structures', 'PaymentStructureController');
    Route::resource('shifts', 'ShiftsController');
    Route::resource('work-plans', 'WorkPlanController');
    Route::resource('grades', 'PayGradesController');
    Route::resource('holidays', 'HolidaysController');
    Route::resource('employees', 'EmployeesController');
    Route::resource('contracts', 'EmployeeContractsController');
    Route::resource('assignments', 'EmployeeAssignmentsController');
    Route::resource('employee-payment-methods', 'EmployeePaymentMethodsController');
    Route::resource('payroll', 'PayrollController');
    Route::get('finalize-payroll', 'PayrollController@finalize');
    Route::get('delete-payroll', 'PayrollController@deletePayrolls');
    Route::get('payroll-pdf/{id}', ['as' => 'payroll.pdf', 'uses' => 'PayrollController@getPDF']);
    Route::get('payroll-pdfs', ['as' => 'payroll.pdfs', 'uses' => 'PayrollController@getAllPDFs']);
    Route::get('payroll-view/{month}', ['as' => 'payroll.show.all', 'uses' => 'PayrollController@viewAll']);
    Route::get('payroll-report', ['as' => 'payroll.report', 'uses' => 'PayrollController@report']);
    Route::post('payroll-generate', ['as' => 'payroll.generate', 'uses' => 'PayrollController@generate']);
    Route::post('payroll-print', ['as' => 'payroll.document', 'uses' => 'PayrollController@getDocument']);
    Route::resource('advances', 'AdvancesController');
    Route::get('advances-bulk', ['as' => 'advances.bulk', 'uses' => 'AdvancesController@bulkAssign']);
    Route::post('advances-bulk', ['as' => 'advances.process', 'uses' => 'AdvancesController@bulkProcess']);
    Route::get('loans/details/{loanId}', ['as' => 'loans.details', 'uses' => 'LoansController@details']);
    Route::resource('loans', 'LoansController');
    Route::resource('policies', 'PoliciesController');
//    Route::resource('tax', 'TaxReportsController');
    Route::get('tax', ['as' => 'tax.index', 'uses' => 'TaxReportsController@index']);
    Route::get('tax-report', 'TaxReportsController@getYear');
    Route::post('tax/{type}', ['as' => 'tax.show', 'uses' => 'TaxReportsController@getReport']);
    Route::resource('template', 'ReportTemplateController');
    Route::resource('worked', 'DaysWorkedController');
    Route::resource('hours-worked', 'HoursWorkedController');
    Route::resource('units-made', 'UnitsMadeController');
    Route::resource('coinage', 'CoinageController');
    Route::resource('users', 'UsersController');
    Route::resource('api', 'APIController');
    Route::get('allowance-report', 'ReportsController@allowances')->name('allowance-report');
    Route::get('employee-sample', 'EmployeesController@importSample')->name('employee-sample');
    Route::post('employee-import', 'EmployeesController@importExcel')->name('employee-import');
    Route::post('worked-import', 'DaysWorkedController@importExcel')->name('worked-import');

    Route::get('contract/{contract_id}/renew', 'EmployeeContractsController@renew')->name('renew-contract');
    Route::get('employee-days-worked', 'DaysWorkedController@downloadExcel')->name('employee-worked');
    Route::resource('leave', 'LeaveController');
    Route::get('statutoryFiles', 'ReportsController@statutoryFiles')->name('statutory-files');
    Route::post('export-statutory', 'ReportsController@exportDocument')->name('statutory-export');

    Route::get('calculator', 'CalculatorController@index')->name('calculator');
});

//
//Route::get('login', ['as' => 'login.index', 'uses' => 'Auth\LoginController@getLogin']);
//Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@getLogin']);
//
//Route::post('login', ['as' => 'login.store', 'uses' => 'Auth\LoginController@postLogin']);
//Route::get('forgot', ['as' => 'forgot.index', 'uses' => 'Auth\LoginController@index']);
//Route::post('forgot', ['as' => 'forgot.store', 'uses' => 'Auth\LoginController@index']);
