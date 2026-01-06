<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CssController;
use App\Http\Controllers\BasicUiController;
use App\Http\Controllers\AdvanceUiController;
use App\Http\Controllers\ExtraComponentsController;
use App\Http\Controllers\BasicTableController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ChartController;
/** new controllers **/
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\BuyerUserController;
use App\Http\Controllers\DeceaseInventoryController;

use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuyerTypeChannelController;
use App\Http\Controllers\ProductVariationTypeController;
use App\Http\Controllers\DeceaseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DeceaseMappingController;
use App\Http\Controllers\PaitentController;
use App\Http\Controllers\supplierController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\PatientdiseaseController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientmedicineController;
use App\Http\Controllers\PatientscheduleController;
use App\Http\Controllers\PatientcarermapController;
use App\Http\Controllers\MedicineStockManagement;





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

Auth::routes(['verify' => true]);

//Route::prefix('superadmin')->group(function () { 
 /****Separate route for superadmin login************* */
   Route::get('/superadmin-login', [LoginController::class, 'showLoginFormSuperadmin']);
   Route::post('/superadmin-login', [LoginController::class, 'postLoginFormSuperadmin'])->name('superadmin-login');
  /****Separate route for superadmin login************* */  
//});





// Route::get('/superadminlogin', [LoginController::class,'showLoginFormSuperadmin'])->name('superadmin.login');
// Route::post('/superadminlogin', [LoginController::class,'loginSuperadmin'])->name('submit');
//Route::post('/superadminlogout', [LoginController::class,'superadminlogout'])->name('superadmin.logout');

Route::group(['middleware' => ['auth']], function () { 
    

    Route::get('/logout', [LoginController::class, 'logout']);
    /** country state city **/
    Route::post('api/fetch-states', [CompanyController::class, 'fetchState']);
    Route::post('api/fetch-cities', [CompanyController::class, 'fetchCity']);
    Route::get('image/{filename}', [DashboardController::class,'displayImage'])->name('image.displayImage');
    /** new routes start **/

   // Route::get('/admin', [DashboardController::class, 'dashboardadminModern'])->name('admin.dashboard');
    Route::prefix('admin')->middleware(['admin'])->group(function () { 

        Route::get('/patient-schedule-list',[PatientscheduleController::class,'patientscheduleList'])->name('admin.patient-schedule-list');
        Route::get('/admin-patient-schedule-create', [PatientscheduleController::class, 'createPatientschedule'])->name('admin-patient-schedule-create');
        Route::post('/admin-patient-schedule-create', [PatientscheduleController::class, 'storePatientschedule'])->name('admin-patient-schedule-create');
        Route::get('/admin-patient-schedule-edit/{id}', [PatientscheduleController::class, 'createPatientschedule'])->name('admin-patient-schedule-edit');
        Route::post('/admin-patient-schedule-update/{id}', [PatientscheduleController::class, 'updatePatientschedule'])->name('admin-patient-schedule-update');
        Route::get('/admin-patient-schedule-delete/{id}', [PatientscheduleController::class, 'destroyPatientschedule'])->name('admin-patient-schedule-delete');

        Route::get('/patient-medicine-list',[PatientmedicineController::class,'patientmedicineList'])->name('admin.patient-medicine-list');
        Route::get('/admin-patient-medicine-create', [PatientmedicineController::class, 'createPatientmedicine'])->name('admin-patient-medicine-create');
        Route::post('/admin-patient-medicine-create', [PatientmedicineController::class, 'storePatientmedicine'])->name('admin-patient-medicine-create');
        Route::get('/admin-patient-medicine-edit/{id}', [PatientmedicineController::class, 'createPatientmedicine'])->name('admin-patient-medicine-edit');
        Route::post('/admin-patient-medicine-update/{id}', [PatientmedicineController::class, 'updatePatientmedicine'])->name('admin-patient-medicine-update');
        Route::get('/admin-patient-medicine-delete/{id}', [PatientmedicineController::class, 'destroyPatientmedicine'])->name('admin-patient-medicine-delete');

        Route::get('/patient-disease-list',[PatientdiseaseController::class,'patientdiseaseList'])->name('admin.patient-disease-list');
        Route::get('/admin-patient-disease-create', [PatientdiseaseController::class, 'createPatientdisease'])->name('admin-patient-disease-create');
        Route::post('/admin-patient-disease-create', [PatientdiseaseController::class, 'storePatientdisease'])->name('admin-patient-disease-create');
        Route::get('/admin-patient-disease-edit/{id}', [PatientdiseaseController::class, 'createPatientdisease'])->name('admin-patient-disease-edit');
        Route::post('/admin-patient-disease-update/{id}', [PatientdiseaseController::class, 'updatePatientdisease'])->name('admin-patient-disease-update');
        Route::get('/admin-patient-disease-delete/{id}', [PatientdiseaseController::class, 'destroyPatientdisease'])->name('admin-patient-disease-delete');

        Route::get('/patient-list',[UserController::class,'patientList'])->name('admin.paitent-list');
        Route::get('/admin-patient-create', [UserController::class, 'createPatient'])->name('admin-patient-create');
        Route::post('/admin-patient-create', [UserController::class, 'storePatient'])->name('admin-patient-create');
        Route::get('/admin-patient-edit/{id}', [UserController::class, 'createPatient'])->name('admin-patient-edit');
        Route::post('/admin-patient-update/{id}', [UserController::class, 'updatePatient'])->name('admin-patient-update');
        Route::get('/admin-patient-delete/{id}', [UserController::class, 'destroyPatient'])->name('admin-patient-delete');

        /** new route for carer **/
        Route::get('/carer-list',[UserController::class,'carerList'])->name('admin.carer-list');
        Route::get('/admin-carer-create', [UserController::class, 'createCarer'])->name('admin-carer-create');
        Route::post('/admin-carer-create', [UserController::class, 'storeCarer'])->name('admin-carer-create');
        Route::get('/admin-carer-edit/{id}', [UserController::class, 'createCarer'])->name('admin-carer-edit');
        Route::post('/admin-carer-update/{id}', [UserController::class, 'updateCarer'])->name('admin-carer-update');
        Route::get('/admin-carer-delete/{id}', [UserController::class, 'destroyCarer'])->name('admin-carer-delete');

        /** new route for manager added by admin **/
        Route::get('/manager-list',[UserController::class,'managerList'])->name('admin.manager-list');
        Route::get('/admin-manager-create', [UserController::class, 'createManager'])->name('admin-manager-create');
        Route::post('/admin-manager-create', [UserController::class, 'storeManager'])->name('admin-manager-create');
        Route::get('/admin-manager-edit/{id}', [UserController::class, 'createManager'])->name('admin-manager-edit');
        Route::post('/admin-manager-update/{id}', [UserController::class, 'updateManager'])->name('admin-manager-update');
        Route::get('/admin-manager-delete/{id}', [UserController::class, 'destroyManager'])->name('admin-manager-delete');
        Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('admin.profile-edit');
        Route::match(['post', 'patch'], '/profile-update/{id}', [ProfileController::class, 'update'])->name('admin.profile-update');

        /** new route for patient carer mapping on admin side */
         Route::get('/admin-patient-carer-map-list', [PatientcarermapController::class, 'index'])->name('admin-patient-carer-map-list');
         Route::get('/admin-patient-carer-map-create', [PatientcarermapController::class, 'create'])->name('admin-patient-carer-map-create');
         Route::post('/admin-patient-carer-map-create', [PatientcarermapController::class, 'store'])->name('admin-patient-carer-map-create');
         Route::get('/admin-patient-carer-map-edit/{id?}', [PatientcarermapController::class, 'create'])->name('admin-patient-carer-map-edit');
         Route::post('/admin-patient-carer-map-update/{id?}', [PatientcarermapController::class, 'update'])->name('admin-patient-carer-map-update');
         Route::get('/admin-patient-carer-map-delete/{id}', [PatientcarermapController::class, 'destroy'])->name('admin-patient-carer-map-delete');
    
         Route::get('/admin-decease-list', [DeceaseController::class, 'index'])->name('admin-decease-list');
         Route::get('/admin-decease-create', [DeceaseController::class, 'create'])->name('admin-decease-create');
         Route::post('/admin-decease-create', [DeceaseController::class, 'store'])->name('admin-decease-create');
         Route::get('/admin-decease-edit/{id?}', [DeceaseController::class, 'create'])->name('admin-decease-edit');
         Route::post('/admin-decease-update/{id?}', [DeceaseController::class, 'update'])->name('admin-decease-update');
         Route::get('/admin-decease-delete/{id}', [DeceaseController::class, 'destroy'])->name('admin-decease-delete');
    
    
        Route::get('/admin-inventory-list', [InventoryController::class, 'index'])->name('admin-inventory-list');
        Route::get('/admin-inventory-create', [InventoryController::class, 'create'])->name('admin-inventory-create');
        Route::post('/admin-inventory-create', [InventoryController::class, 'store'])->name('admin-inventory-create');
        Route::get('/admin-inventory-edit/{id?}', [InventoryController::class, 'create'])->name('admin-inventory-edit');
        Route::post('/admin-inventory-update/{id?}', [InventoryController::class, 'update'])->name('admin-inventory-update');
        Route::get('/admin-inventory-delete/{id}', [InventoryController::class, 'destroy'])->name('admin-inventory-delete');
    
        Route::get('/admin-supplier-list', [supplierController::class, 'index'])->name('admin-supplier-list');
        Route::get('/admin-supplier-create', [supplierController::class, 'create'])->name('admin-supplier-create');
        Route::post('/admin-supplier-create', [supplierController::class, 'store'])->name('admin-supplier-create');
        Route::get('/admin-supplier-edit/{id}', [supplierController::class, 'create'])->name('admin-supplier-edit');
        Route::post('/admin-supplier-update/{id}', [supplierController::class, 'update'])->name('admin-supplier-update');
        Route::get('/admin-supplier-delete/{id}', [supplierController::class, 'destroy'])->name('admin-supplier-delete');
        
        Route::get('/admin-stockin-list', [StockInController::class, 'index'])->name('admin-stockin-list');
        Route::get('/admin-stockin-create', [StockInController::class, 'create'])->name('admin-stockin-create');
        Route::post('/admin-stockin-create', [StockInController::class, 'store'])->name('admin-stockin-create');
        Route::get('/admin-stockin-edit/{id?}',[StockInController::class, 'create'])->name('admin-stockin-edit');
        Route::post('/admin-stockin-update/{id?}',[StockInController::class, 'update'])->name('admin-stockin-update');
        //Route::get('/admin-stockin-delete/{id}', [StockInController::class, 'destroy'])->name('admin-stockin-delete');
        
        Route::get('/admin-stockout-list', [StockOutController::class, 'index'])->name('admin-stockout-list');
        Route::get('/admin-stockout-create', [StockOutController::class, 'create'])->name('admin-stockout-create');
        Route::post('/admin-stockout-create', [StockOutController::class, 'store'])->name('admin-stockout-create');
        Route::get('/admin-stockout-edit/{id?}',[StockOutController::class, 'create'])->name('admin-stockout-edit');
        Route::post('/admin-stockout-update/{id?}',[StockOutController::class, 'update'])->name('admin-stockout-update');
        
        Route::get('/admin-medicine-list', [MedicineController::class, 'index'])->name('admin-medicine-list');
        Route::get('/admin-medicine-create', [MedicineController::class, 'create'])->name('admin-medicine-create');
        Route::post('/admin-medicine-create', [MedicineController::class, 'store'])->name('admin-medicine-create');
        Route::get('/admin-medicine-edit/{id?}', [MedicineController::class, 'create'])->name('admin-medicine-edit');
        Route::post('/admin-medicine-update/{id?}', [MedicineController::class, 'update'])->name('admin-medicine-update');
        Route::get('/admin-medicine-delete/{id?}',[MedicineController::class, 'destroy'])->name('admin-medicine-delete');
    
        Route::resource('/inventory-mapping',DeceaseInventoryController::class);
        Route::get('/inventory-mapping/destroy/{id}',[DeceaseInventoryController::class,'destroy'])->name('inventory-mapping.delete');
        
        Route::get('/admin-inventory-mapping-list', [DeceaseInventoryController::class, 'index'])->name('admin-inventory-mapping-list');
        Route::get('/admin-inventory-mapping-create', [DeceaseInventoryController::class, 'create'])->name('admin-inventory-mapping-create');
        Route::post('/admin-inventory-mapping-create', [DeceaseInventoryController::class, 'store'])->name('admin-inventory-mapping-create');
        Route::get('/admin-inventory-mapping-edit/{id?}', [DeceaseInventoryController::class, 'edit'])->name('admin-inventory-mapping-edit');
        Route::post('/admin-inventory-mapping-update/{id?}', [DeceaseInventoryController::class, 'update'])->name('admin-inventory-mapping-update');
        Route::get('/admin-inventory-mapping-delete/{id?}',[DeceaseInventoryController::class, 'destroy'])->name('admin-inventory-mapping-delete');
        
        Route::get('medicine-stock-management/create',[MedicineStockManagement::class,'create'])->name('admin.medicine-stock-management.create');
        Route::post('/medicine-stock-management', [MedicineStockManagement::class, 'store'])->name('admin.medicine-stock-management.store');
        Route::get('medicine-stock-management',[MedicineStockManagement::class,'index'])->name('admin.medicine-stock-management.list');
        Route::post('getMedicine', [MedicineStockManagement::class, 'getMedicine'])->name('admin.getMedicine');
        Route::get('medicine-stock-management-edit/{id}',[MedicineStockManagement::class,'create'])->name('admin.medicine-stock-management.edit');
        Route::post('medicine-stock-management-update/{id?}', [MedicineStockManagement::class, 'update'])->name('admin.medicine-stock-management.update');
        Route::get('medicine-stock-management-delete/{id}', [MedicineStockManagement::class, 'destroy'])->name('admin.medicine-stock-management.delete');
        });

    Route::prefix('manager')->middleware(['manager'])->group(function () { 
        Route::get('/manager-patient-list',[UserController::class,'managerpatientList'])->name('manager.paitent-list');
        Route::get('/manager-patient-create', [UserController::class, 'managercreatePatient'])->name('manager-patient-create');
        Route::post('/manager-patient-create', [UserController::class, 'managerstorePatient'])->name('manager-patient-create');
        Route::get('/manager-patient-edit/{id}', [UserController::class, 'managercreatePatient'])->name('manager-patient-edit');
        Route::post('/manager-patient-update/{id}', [UserController::class, 'managerupdatePatient'])->name('manager-patient-update');
        Route::get('/manager-patient-delete/{id}', [UserController::class, 'managerdestroyPatient'])->name('manager-patient-delete');

        /** new route for carer **/
        Route::get('/manager-carer-list',[UserController::class,'managercarerList'])->name('manager.carer-list');
        Route::get('/manager-carer-create', [UserController::class, 'managercreateCarer'])->name('manager-carer-create');
        Route::post('/manager-carer-create', [UserController::class, 'managerstoreCarer'])->name('manager-carer-create');
        Route::get('/manager-carer-edit/{id}', [UserController::class, 'managercreateCarer'])->name('manager-carer-edit');
        Route::post('/manager-carer-update/{id}', [UserController::class, 'managerupdateCarer'])->name('manager-carer-update');
        Route::get('/manager-carer-delete/{id}', [UserController::class, 'managerdestroyCarer'])->name('manager-carer-delete');

         Route::get('/manager-patient-carer-map-list', [PatientcarermapController::class, 'index'])->name('manager-patient-carer-map-list');
         Route::get('/manager-patient-carer-map-create', [PatientcarermapController::class, 'create'])->name('manager-patient-carer-map-create');
         Route::post('/manager-patient-carer-map-create', [PatientcarermapController::class, 'store'])->name('manager-patient-carer-map-create');
         Route::get('/manager-patient-carer-map-edit/{id?}', [PatientcarermapController::class, 'create'])->name('manager-patient-carer-map-edit');
         Route::post('/manager-patient-carer-map-update/{id?}', [PatientcarermapController::class, 'update'])->name('manager-patient-carer-map-update');
         Route::get('/manager-patient-carer-map-delete/{id}', [PatientcarermapController::class, 'destroy'])->name('manager-patient-carer-map-delete');
        
        Route::get('/manager-medicine-list', [MedicineController::class, 'index'])->name('manager-medicine-list');
        Route::get('/manager-medicine-create', [MedicineController::class, 'create'])->name('manager-medicine-create');
        Route::post('/manager-medicine-create', [MedicineController::class, 'store'])->name('manager-medicine-create');
        Route::get('/manager-medicine-edit/{id?}', [MedicineController::class, 'create'])->name('manager-medicine-edit');
        Route::post('/manager-medicine-update/{id?}', [MedicineController::class, 'update'])->name('manager-medicine-update');
        Route::get('/manager-medicine-delete/{id?}',[MedicineController::class, 'destroy'])->name('manager-medicine-delete');
        
        Route::get('medicine-stock-management/create',[MedicineStockManagement::class,'create'])->name('manager.medicine-stock-management.create');
        Route::post('/medicine-stock-management', [MedicineStockManagement::class, 'store'])->name('manager.medicine-stock-management.store');
        Route::get('medicine-stock-management',[MedicineStockManagement::class,'index'])->name('manager.medicine-stock-management.list');
        Route::post('getMedicine', [MedicineStockManagement::class, 'getMedicine'])->name('manager.getMedicine');
        Route::get('medicine-stock-management-edit/{id}',[MedicineStockManagement::class,'create'])->name('manager.medicine-stock-management.edit');
        Route::post('medicine-stock-management-update/{id?}', [MedicineStockManagement::class, 'update'])->name('manager.medicine-stock-management.update');
        Route::get('medicine-stock-management-delete/{id}', [MedicineStockManagement::class, 'destroy'])->name('manager.medicine-stock-management.delete');

     });
      
     /****************************************************************************/
     
     /****************************************************************************/
    
  
    Route::prefix('superadmin')->middleware(['superadmin'])->group(function () { 
        Route::get('/', [DashboardController::class, 'dashboardSuperadminModern'])->name('superadmin.dashboard');
        Route::get('/login', [LoginController::class, 'showLoginForm']);
        

    /** medicine-stock-management **/
        Route::get('medicine-stock-management/create',[MedicineStockManagement::class,'create'])->name('superadmin.medicine-stock-management.create');
        Route::post('/medicine-stock-management', [MedicineStockManagement::class, 'store'])->name('superadmin.medicine-stock-management.store');
        Route::get('medicine-stock-management',[MedicineStockManagement::class,'index'])->name('superadmin.medicine-stock-management.list');
        Route::post('getMedicine', [MedicineStockManagement::class, 'getMedicine'])->name('superadmin.getMedicine');
        Route::get('medicine-stock-management-edit/{id}',[MedicineStockManagement::class,'create'])->name('superadmin.medicine-stock-management.edit');
        Route::post('medicine-stock-management-update/{id?}', [MedicineStockManagement::class, 'update'])->name('superadmin.medicine-stock-management.update');
        Route::get('medicine-stock-management-delete/{id}', [MedicineStockManagement::class, 'destroy'])->name('superadmin.medicine-stock-management.delete');
        
        /** company route */

        Route::resource('/company', CompanyController::class);
        // Route::get('/company/{id}/edit', [CompanyController::class,'edit'])->index('superadmin');
        Route::post('/company-import', [CompanyController::class,'companyImport'])->name('company-import');
        Route::get('/company-export', [CompanyController::class,'companyExport'])->name('company-export');
        Route::get('/company/delete/{id}', [CompanyController::class,'destroy'])->name('company.destroy');
        
        /** company admin **/
        Route::get('/company-admin-create', [UserController::class, 'create'])->name('company-admin-create');
        Route::get('/company-admin-edit/{id}', [UserController::class, 'create'])->name('superadmin.company-admin-edit');
        Route::post('/company-admin-update/{id}', [UserController::class, 'update'])->name('company-admin-update');
        Route::get('/company-admin-list', [UserController::class, 'index'])->name('company-admin-list');
        Route::post('/company-admin-create', [UserController::class, 'store'])->name('company-admin-create');
        Route::get('/company-admin-delete/{id}', [UserController::class, 'destroy'])->name('superadmin.company-admin-delete');

        Route::get('/company-user-create', [UserController::class, 'usersCreate'])->name('superadmin.company-user-create');
        Route::get('/company-user-edit/{id}', [UserController::class, 'usersCreate'])->name('superadmin.company-user-edit');
        Route::post('/company-user-update/{id}', [UserController::class, 'usersUpdate'])->name('superadmin.company-user-update');
        Route::get('/company-user-list', [UserController::class, 'usersList'])->name('superadmin.company-user-list');
        Route::post('/company-user-create', [UserController::class, 'userStore'])->name('superadmin.company-user-create');
        Route::get('/company-user-delete/{id}', [UserController::class, 'destroyUser'])->name('superadmin.company-user-delete');
        
        /* new route for supplier master*/
        Route::get('/supplier-list', [supplierController::class, 'index'])->name('supplier-list');
        Route::get('/supplier-create', [supplierController::class, 'create'])->name('supplier-create');
        Route::post('/supplier-create', [supplierController::class, 'store'])->name('supplier-create');
        Route::get('/supplier-edit/{id}', [supplierController::class, 'create'])->name('supplier-edit');
        Route::post('/supplier-update/{id}', [supplierController::class, 'update'])->name('supplier-update');
        Route::get('/supplier-delete/{id}', [supplierController::class, 'destroy'])->name('supplier-delete');


        Route::get('/admin-list',[UserController::class,'adminList'])->name('superadmin.admin-list');
        /** new route for patient **/
        Route::get('/patient-list',[UserController::class,'patientList'])->name('superadmin.patient-list');

        /** new route for carer **/
        Route::get('/carer-list',[UserController::class,'carerList'])->name('superadmin.carer-list');

        /** new route for manager **/
        Route::get('/manager-list',[UserController::class,'managerList'])->name('superadmin.manager-list');

        /* new route for decease*/
        Route::get('/decease-list', [DeceaseController::class, 'index'])->name('decease-list');
        Route::get('/decease-create', [DeceaseController::class, 'create'])->name('decease-create');
        Route::post('/decease-create', [DeceaseController::class, 'store'])->name('decease-create');
        Route::get('/decease-edit/{id?}', [DeceaseController::class, 'create'])->name('decease-edit');
        Route::post('/decease-update/{id?}', [DeceaseController::class, 'update'])->name('decease-update');
        Route::get('/decease-delete/{id}', [DeceaseController::class, 'destroy'])->name('decease-delete');

        /* new route for inventory */
        Route::get('/inventory-list', [InventoryController::class, 'index'])->name('inventory-list');
        Route::get('/inventory-create', [InventoryController::class, 'create'])->name('inventory-create');
        Route::post('/inventory-create', [InventoryController::class, 'store'])->name('inventory-create');
        Route::get('/inventory-edit/{id?}', [InventoryController::class, 'create'])->name('inventory-edit');
        Route::post('/inventory-update/{id?}', [InventoryController::class, 'update'])->name('inventory-update');
        Route::get('/inventory-delete/{id}', [InventoryController::class, 'destroy'])->name('inventory-delete');

         /* new route for medicine */
         Route::get('/medicine-list', [MedicineController::class, 'index'])->name('medicine-list');
         Route::get('/medicine-create', [MedicineController::class, 'create'])->name('medicine-create');
         Route::post('/medicine-create', [MedicineController::class, 'store'])->name('medicine-create');
         Route::get('/medicine-edit/{id?}', [MedicineController::class, 'create'])->name('medicine-edit');
         Route::post('/medicine-update/{id?}', [MedicineController::class, 'update'])->name('medicine-update');
         Route::get('/medicine-delete/{id}', [MedicineController::class, 'destroy'])->name('medicine-delete');

        /* new route for decease inventory mapping */

        Route::resource('/inventory-mapping',DeceaseInventoryController::class);
        Route::get('/inventory-mapping/destroy/{id}',[DeceaseInventoryController::class,'destroy'])->name('inventory-mapping.delete');
        
        /* new route for patient carer mapping */
        Route::get('/patient-carer-map-list', [PatientcarermapController::class, 'index'])->name('patient-carer-map-list');
         Route::get('/patient-carer-map-create', [PatientcarermapController::class, 'create'])->name('patient-carer-map-create');
         Route::post('/patient-carer-map-create', [PatientcarermapController::class, 'store'])->name('patient-carer-map-create');
         Route::get('/patient-carer-map-edit/{id?}', [PatientcarermapController::class, 'create'])->name('patient-carer-map-edit');
         Route::post('/patient-carer-map-update/{id?}', [PatientcarermapController::class, 'update'])->name('patient-carer-map-update');
         Route::get('/patient-carer-map-delete/{id}', [PatientcarermapController::class, 'destroy'])->name('patient-carer-map-delete');

        /* new route for patient schedule*/
        Route::get('/patient-schedule-list', [PaitentController::class, 'index'])->name('patient-schedule-list');
        Route::get('/patient-schedule-create', [PaitentController::class, 'create'])->name('patient-schedule-create');
        Route::post('/patient-schedule-create', [PaitentController::class, 'store'])->name('patient-schedule-create');
        Route::get('/patient-schedule-edit/{id?}',[PaitentController::class, 'create'])->name('patient-schedule-edit');
        Route::post('/patient-schedule-update/{id?}',[PaitentController::class, 'update'])->name('patient-schedule-update');
        
        /* new route for stock in*/
        Route::get('/stockin-list', [StockInController::class, 'index'])->name('stockin-list');
        Route::get('/stockin-create', [StockInController::class, 'create'])->name('stockin-create');
        Route::post('/stockin-create', [StockInController::class, 'store'])->name('stockin-create');
        Route::get('/stockin-edit/{id?}',[StockInController::class, 'create'])->name('stockin-edit');
        Route::post('/stockin-update/{id?}',[StockInController::class, 'update'])->name('stockin-update');

        /* new route for stock out*/
        Route::get('/stockout-list', [StockOutController::class, 'index'])->name('stockout-list');
        Route::get('/stockout-create', [StockOutController::class, 'create'])->name('stockout-create');
        Route::post('/stockout-create', [StockOutController::class, 'store'])->name('stockout-create');
        Route::get('/stockout-edit/{id?}',[StockOutController::class, 'create'])->name('stockout-edit');
        Route::post('/stockout-update/{id?}',[StockOutController::class, 'update'])->name('stockout-update');
        


        

        /** new buyer routes start **/
        Route::resource('/buyer-type', BuyerController::class);
        Route::get('/buyer-type/delete/{id}', [BuyerController::class,'destroy'])->name('buyer-type.destroy');

        /** Decease routes  */
        Route::get('/decease-user-create', [DeceaseController::class, 'decease'])->name('superadmin.decease-user-create');

        /** Inventory routes  */
        //Route::get('/inventory-create', [InventoryController::class, 'inventory'])->name('superadmin.inventory-create');

        /** Mapping routes  */
        //Route::get('/mapping-create', [DeceaseMappingController::class, 'mapping'])->name('superadmin.mapping-create');

        /** Paitent routes  */
       // Route::get('/paitent-create', [PaitentController::class, 'paitent'])->name('superadmin.paitent-create');

        /** Supplier routes  */
        //Route::get('/supplier-create', [supplierController::class, 'supplier'])->name('superadmin.supplier-create');

         /** StockIn routes  */
         //Route::get('/stockin-create', [StockInController::class, 'stockin'])->name('superadmin.stockin-create');
        
        /** StockOut routes  */
       // Route::get('/stockout-create', [StockOutController::class, 'stockout'])->name('superadmin.stockout-create');
               
        /** new products routes **/
        Route::resource('/product',ProductsController::class);
        Route::get('/product',[ProductsController::class,'index'])->name('superadmin.product.index');
        Route::get('/product/{$id}/edit',[ProductsController::class,'edit'])->name('superadmin.product.edit');
        Route::post('/product',[ProductsController::class,'store'])->name('superadmin.product.store');
        Route::patch('/product/{$id}',[ProductsController::class,'update'])->name('superadmin.product.update');
        Route::get('/product/destroy/{id}',[ProductsController::class,'destroy'])->name('superadmin.product.delete');
        Route::get('/product/imagedelete/{id}',[ProductsController::class,'imagedelete'])->name('superadmin.product.imagedelete');
        
        /** product category admin side **/
        Route::resource('/product-category',ProductCategoryController::class);
        Route::get('/product-category',[ProductCategoryController::class,'index'])->name('superadmin.product-category.index');
        Route::get('/product-category/{$id}/edit',[ProductCategoryController::class,'index'])->name('superadmin.product-category.edit');
        Route::post('/product-category',[ProductCategoryController::class,'store'])->name('superadmin.product-category.store');
        Route::put('/product-category/{$id}',[ProductCategoryController::class,'update'])->name('superadmin.product-category.update');
        Route::get('/product-category/destroy/{id}',[ProductCategoryController::class,'destroy'])->name('superadmin.product-category.delete');
        Route::post('/product-category-import',[ProductCategoryController::class,'productcategoryimport'])->name('superadmin.product-category-import');
        Route::get('/product-category-export/{type?}',[ProductCategoryController::class,'productcategoryexport'])->name('superadmin.product-category-export');

        /** product company mapping **/
        Route::resource('/product-mapping',ProductCompanyMapping::class);
        Route::get('/product-mapping/destroy/{id}',[ProductCompanyMapping::class,'destroy'])->name('product-mapping.delete');

        /** sub category admin side **/

        Route::resource('/product-subcategory',SubCategoryController::class);
        Route::get('/product-subcategory',[SubCategoryController::class,'index'])->name('superadmin.product-subcategory.index');
        Route::get('/product-subcategory/{$id}/edit',[SubCategoryController::class,'index'])->name('superadmin.product-subcategory.edit');
        Route::post('/product-subcategory',[SubCategoryController::class,'store'])->name('superadmin.product-subcategory.store');
        Route::put('/product-subcategory/{$id}',[SubCategoryController::class,'update'])->name('superadmin.product-subcategory.update');
        Route::get('/product-subcategory/destroy/{id}',[SubCategoryController::class,'destroy'])->name('superadmin.product-subcategory.delete');
        Route::post('/sub-category-import',[SubCategoryController::class,'subcategoryimport'])->name('superadmin.sub-category-import');
          
        /** new buyer registration routes start **/
        Route::resource('/buyer', BuyerUserController::class);
        Route::get('/buyer',[BuyerUserController::class,'index'])->name('superadmin.buyer.index');
        Route::get('/buyer/create',[BuyerUserController::class,'create'])->name('superadmin.buyer.create');
        Route::get('/buyer/{id}/edit',[BuyerUserController::class,'edit'])->name('superadmin.buyer.edit');
        Route::post('/buyer',[BuyerUserController::class,'store'])->name('superadmin.buyer.store');
        Route::patch('/buyer/{id}',[BuyerUserController::class,'update'])->name('superadmin.buyer.update');
        Route::get('/buyer/destroy/{id}',[BuyerUserController::class,'destroy'])->name('superadmin.buyer.delete');
        /** buyer import and export **/
        Route::post('/buyer/import', [BuyerUserController::class,'buyerImport'])->name('superadmin.buyer.import');
        Route::get('/buyer/export/{type}', [BuyerUserController::class,'buyerExport'])->name('superadmin.buyer.export');


        /** profile superadmin side **/

        Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('superadmin.profile-edit');

        Route::match(['post', 'patch'], '/profile-update/{id}', [ProfileController::class, 'update'])->name('superadmin.profile-update');

        
        /**  admin side Buyer Type Channel**/

        Route::resource('/buyer-type-channel',BuyerTypeChannelController::class);
        Route::get('/buyer-type-channel',[BuyerTypeChannelController::class,'index'])->name('superadmin.buyer-type-channel.index');
        Route::post('/buyer-type-channel',[BuyerTypeChannelController::class,'store'])->name('superadmin.buyer-type-channel.store');
        Route::get('/buyer-type-channel/{$id}/edit',[BuyerTypeChannelController::class,'index'])->name('superadmin.buyer-type-channel.edit');
        Route::get('/buyer-type-channel/destroy/{id}',[BuyerTypeChannelController::class,'destroy'])->name('superadmin.buyer-type-channel.delete');
        Route::put('/buyer-type-channel/{$id}',[BuyerTypeChannelController::class,'update'])->name('superadmin.buyer-type-channel.update');
        Route::post('/buyer-type-channel-import',[BuyerTypeChannelController::class,'buyertypechannelimport'])->name('superadmin.buyer-type-channel-import');

         /**  admin side Product Variation Type**/

         Route::resource('/product-variation-type',ProductVariationTypeController::class);
         Route::get('/product-variation-type',[ProductVariationTypeController::class,'index'])->name('superadmin.product-variation-type.index');
         Route::post('/product-variation-type',[ProductVariationTypeController::class,'store'])->name('superadmin.product-variation-type.store');
         Route::get('/product-variation-type/{$id}/edit',[ProductVariationTypeController::class,'index'])->name('superadmin.product-variation-type.edit');
         Route::get('/product-variation-type/destroy/{id}',[ProductVariationTypeController::class,'destroy'])->name('superadmin.product-variation-type.delete');
         Route::put('/product-variation-type/{$id}',[ProductVariationTypeController::class,'update'])->name('superadmin.product-variation-type.update');

         Route::post('/product-variation-type-import',[ProductVariationTypeController::class,'productvariationtypeimport'])->name('superadmin.product-variation-type-import');

    });

    Route::middleware(['companyadmin'])->group(function () {
        Route::get('/',[DashboardController::class, 'dashboardModern'])->name('dashboard');
        /** company user **/
        Route::get('/company-user-create', [UserController::class, 'usersCreate'])->name('company-user-create');
        Route::get('/company-user-edit/{id}', [UserController::class, 'usersCreate'])->name('company-user-edit');
        Route::post('/company-user-update/{id}', [UserController::class, 'usersUpdate'])->name('company-user-update');
        Route::get('/company-user-list', [UserController::class, 'usersList'])->name('company-user-list');
        Route::post('/company-user-create', [UserController::class, 'userStore'])->name('company-user-create');
        Route::get('/company-user-delete/{id}', [UserController::class, 'destroyUser'])->name('company-user-delete');

        Route::get('/permision-edit', [UserController::class, 'usersPermission']);
        /** new products routes **/
        
         /** product category user side **/
         Route::resource('/product-category',ProductCategoryController::class);
         Route::get('/product-category/destroy/{id}',[ProductCategoryController::class,'destroy'])->name('product-category.delete');

        /** new products routes **/
        Route::resource('/product',ProductsController::class);
        Route::get('/product/destroy/{id}',[ProductsController::class,'destroy'])->name('product.delete');
        Route::get('/product/imagedelete/{id}',[ProductsController::class,'imagedelete'])->name('product.imagedelete');

        /** product category **/
        Route::resource('/product-category',ProductCategoryController::class);
        Route::get('/product-category/destroy/{id}',[ProductCategoryController::class,'destroy'])->name('product-category.delete');
        Route::post('/product-category-import',[ProductCategoryController::class,'productcategoryimport'])->name('product-category-import');
       

        /** sub category user side **/
        Route::resource('/product-subcategory',SubCategoryController::class);
        Route::get('/product-subcategory/destroy/{id}',[SubCategoryController::class,'destroy'])->name('product-subcategory.delete');

        
        Route::post('/sub-category-import',[SubCategoryController::class,'subcategoryimport'])->name('sub-category-import');
        // Route::get('/sub-category-export/{type?}',[SubCategoryController::class,'subCategoryexportFile'])->name('sub-category-export');


        /** user side Buyer Type Channel**/

        Route::resource('/buyer-type-channel',BuyerTypeChannelController::class);
        Route::get('/buyer-type-channel/destroy/{id}',[BuyerTypeChannelController::class,'destroy'])->name('buyer-type-channel.delete');
        Route::post('/buyer-type-channel-import',[BuyerTypeChannelController::class,'buyertypechannelimport'])->name('buyer-type-channel-import');

        Route::resource('/product-variation-type',ProductVariationTypeController::class);
        Route::get('/product-variation-type/destroy/{id}',[ProductVariationTypeController::class,'destroy'])->name('product-variation-type.delete');
        Route::post('/product-variation-type-import',[ProductVariationTypeController::class,'productvariationtypeimport'])->name('product-variation-type-import');
    
        
        /** new buyer registration routes start **/
        Route::resource('/buyer', BuyerUserController::class);

        
        /** profile user side **/

        Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('profile-edit');
    
        Route::match(['post', 'patch'], '/profile-update/{id}', [ProfileController::class, 'update'])->name('profile-update');
        
        Route::get('/buyer/destroy/{id}',[BuyerUserController::class,'destroy'])->name('buyer.delete');
        Route::post('/buyer/import', [BuyerUserController::class,'buyerImport'])->name('buyer.import');
        Route::get('/buyer/export/{type}', [BuyerUserController::class,'buyerExport'])->name('buyer.export');

    });


    /** product category import and export **/
    Route::get('/sub-category-export/{type?}',[SubCategoryController::class,'subcategoryexport'])->name('sub-category-export');

    Route::get('/product-category-export/{type?}',[ProductCategoryController::class,'productCategoryexport'])->name('product-category-export');

    Route::get('/product-variation-type-export/{type?}',[ProductVariationTypeController::class,'productvariationtypeexport'])->name('product-variation-type-export');

    Route::get('/buyer-type-channelexport/{type?}',[BuyerTypeChannelController::class,'buyertypechannel_export'])->name('buyer-type-channelexport');
     
    /** product import and export **/
    Route::post('/product-import', [ProductsController::class,'productImport']);
    Route::get('/product-export/{type?}', [ProductsController::class,'productExport'])->name('product-export');
    
    Route::post('/company-user-import', [UserController::class,'companyUserImport']);
    Route::get('/company-user-export/{type?}', [UserController::class,'companyUserExport'])->name('company-user-export');
    Route::get('/company-user-import', [UserController::class,'companyUserExport'])->name('company-user-import');

    
    /** state and city **/
    Route::post('api/user-fetch-states', [DashboardController::class, 'user_fetchState']);
    Route::post('api/user-fetch-cities', [DashboardController::class, 'user_fetchCity']);
    Route::post('api/fetch-subcategory', [DashboardController::class, 'product_fetchSubcategory']);
    

    /** new routes end **/



    // Dashboard Route
    // Route::get('/', [DashboardController::class, 'dashboardModern'])->middleware('verified');
    Route::get('/', [DashboardController::class, 'dashboardModern'])->name('dashboardModern');

    Route::get('/modern', [DashboardController::class, 'dashboardModern']);
    Route::get('/ecommerce', [DashboardController::class, 'dashboardEcommerce']);
    Route::get('/analytics', [DashboardController::class, 'dashboardAnalytics']);

    // Application Route
    Route::get('/app-email', [ApplicationController::class, 'emailApp']);
    Route::get('/app-email/content', [ApplicationController::class, 'emailContentApp']);
    Route::get('/app-chat', [ApplicationController::class, 'chatApp']);
    Route::get('/app-todo', [ApplicationController::class, 'todoApp']);
    Route::get('/app-kanban', [ApplicationController::class, 'kanbanApp']);
    Route::get('/app-file-manager', [ApplicationController::class, 'fileManagerApp']);
    Route::get('/app-contacts', [ApplicationController::class, 'contactApp']);
    Route::get('/app-calendar', [ApplicationController::class, 'calendarApp']);
    Route::get('/app-invoice-list', [ApplicationController::class, 'invoiceList']);
    Route::get('/app-invoice-view', [ApplicationController::class, 'invoiceView']);
    Route::get('/app-invoice-edit', [ApplicationController::class, 'invoiceEdit']);
    Route::get('/app-invoice-add', [ApplicationController::class, 'invoiceAdd']);
    Route::get('/eCommerce-products-page', [ApplicationController::class, 'ecommerceProduct']);
    Route::get('/eCommerce-pricing', [ApplicationController::class, 'eCommercePricing']);

    // User profile Route
    Route::get('/user-profile-page', [UserProfileController::class, 'userProfile']);

    // Page Route
    Route::get('/page-contact', [PageController::class, 'contactPage']);
    Route::get('/page-blog-list', [PageController::class, 'pageBlogList']);
    Route::get('/page-search', [PageController::class, 'searchPage']);
    Route::get('/page-knowledge', [PageController::class, 'knowledgePage']);
    Route::get('/page-knowledge/licensing', [PageController::class, 'knowledgeLicensingPage']);
    Route::get('/page-knowledge/licensing/detail', [PageController::class, 'knowledgeLicensingPageDetails']);
    Route::get('/page-timeline', [PageController::class, 'timelinePage']);
    Route::get('/page-faq', [PageController::class, 'faqPage']);
    Route::get('/page-faq-detail', [PageController::class, 'faqDetailsPage']);
    Route::get('/page-account-settings', [PageController::class, 'accountSetting']);
    Route::get('/page-blank', [PageController::class, 'blankPage']);
    Route::get('/page-collapse', [PageController::class, 'collapsePage']);

    // Media Route
    Route::get('/media-gallery-page', [MediaController::class, 'mediaGallery']);
    Route::get('/media-hover-effects', [MediaController::class, 'hoverEffect']);

    // User Route
    Route::get('/page-users-list', [UserController::class, 'usersList']);
    Route::get('/page-users-view', [UserController::class, 'usersView']);
    Route::get('/page-users-edit', [UserController::class, 'usersEdit']);



    // Card Route
    Route::get('/cards-basic', [CardController::class, 'cardBasic']);
    Route::get('/cards-advance', [CardController::class, 'cardAdvance']);
    Route::get('/cards-extended', [CardController::class, 'cardsExtended']);

    // Css Route
    Route::get('/css-typography', [CssController::class, 'typographyCss']);
    Route::get('/css-color', [CssController::class, 'colorCss']);
    Route::get('/css-grid', [CssController::class, 'gridCss']);
    Route::get('/css-helpers', [CssController::class, 'helpersCss']);
    Route::get('/css-media', [CssController::class, 'mediaCss']);
    Route::get('/css-pulse', [CssController::class, 'pulseCss']);
    Route::get('/css-sass', [CssController::class, 'sassCss']);
    Route::get('/css-shadow', [CssController::class, 'shadowCss']);
    Route::get('/css-animations', [CssController::class, 'animationCss']);
    Route::get('/css-transitions', [CssController::class, 'transitionCss']);

    // Basic Ui Route
    Route::get('/ui-basic-buttons', [BasicUiController::class, 'basicButtons']);
    Route::get('/ui-extended-buttons', [BasicUiController::class, 'extendedButtons']);
    Route::get('/ui-icons', [BasicUiController::class, 'iconsUI']);
    Route::get('/ui-alerts', [BasicUiController::class, 'alertsUI']);
    Route::get('/ui-badges', [BasicUiController::class, 'badgesUI']);
    Route::get('/ui-breadcrumbs', [BasicUiController::class, 'breadcrumbsUI']);
    Route::get('/ui-chips', [BasicUiController::class, 'chipsUI']);
    Route::get('/ui-chips', [BasicUiController::class, 'chipsUI']);
    Route::get('/ui-collections', [BasicUiController::class, 'collectionsUI']);
    Route::get('/ui-navbar', [BasicUiController::class, 'navbarUI']);
    Route::get('/ui-pagination', [BasicUiController::class, 'paginationUI']);
    Route::get('/ui-preloader', [BasicUiController::class, 'preloaderUI']);

    // Advance UI Route
    Route::get('/advance-ui-carousel', [AdvanceUiController::class, 'carouselUI']);
    Route::get('/advance-ui-collapsibles', [AdvanceUiController::class, 'collapsibleUI']);
    Route::get('/advance-ui-toasts', [AdvanceUiController::class, 'toastUI']);
    Route::get('/advance-ui-tooltip', [AdvanceUiController::class, 'tooltipUI']);
    Route::get('/advance-ui-dropdown', [AdvanceUiController::class, 'dropdownUI']);
    Route::get('/advance-ui-feature-discovery', [AdvanceUiController::class, 'discoveryFeature']);
    Route::get('/advance-ui-media', [AdvanceUiController::class, 'mediaUI']);
    Route::get('/advance-ui-modals', [AdvanceUiController::class, 'modalUI']);
    Route::get('/advance-ui-scrollspy', [AdvanceUiController::class, 'scrollspyUI']);
    Route::get('/advance-ui-tabs', [AdvanceUiController::class, 'tabsUI']);
    Route::get('/advance-ui-waves', [AdvanceUiController::class, 'wavesUI']);
    Route::get('/fullscreen-slider-demo', [AdvanceUiController::class, 'fullscreenSlider']);

    // Extra components Route
    Route::get('/extra-components-range-slider', [ExtraComponentsController::class, 'rangeSlider']);
    Route::get('/extra-components-sweetalert', [ExtraComponentsController::class, 'sweetAlert']);
    Route::get('/extra-components-nestable', [ExtraComponentsController::class, 'nestAble']);
    Route::get('/extra-components-treeview', [ExtraComponentsController::class, 'treeView']);
    Route::get('/extra-components-ratings', [ExtraComponentsController::class, 'ratings']);
    Route::get('/extra-components-tour', [ExtraComponentsController::class, 'tour']);
    Route::get('/extra-components-i18n', [ExtraComponentsController::class, 'i18n']);
    Route::get('/extra-components-highlight', [ExtraComponentsController::class, 'highlight']);

    // Basic Tables Route
    Route::get('/table-basic', [BasicTableController::class, 'tableBasic']);

    // Data Table Route
    Route::get('/table-data-table', [DataTableController::class, 'dataTable']);

    // Form Route
    Route::get('/form-elements', [FormController::class, 'formElement']);
    Route::get('/form-select2', [FormController::class, 'formSelect2']);
    Route::get('/form-validation', [FormController::class, 'formValidation']);
    Route::get('/form-masks', [FormController::class, 'masksForm']);
    Route::get('/form-editor', [FormController::class, 'formEditor']);
    Route::get('/form-file-uploads', [FormController::class, 'fileUploads']);
    Route::get('/form-layouts', [FormController::class, 'formLayouts']);
    Route::get('/form-wizard', [FormController::class, 'formWizard']);

    // Charts Route
    Route::get('/charts-chartjs', [ChartController::class, 'chartJs']);
    Route::get('/charts-chartist', [ChartController::class, 'chartist']);
    Route::get('/charts-sparklines', [ChartController::class, 'sparklines']);


    // locale route
    Route::get('lang/{locale}', [LanguageController::class, 'swap']);
});

// Authentication Route
Route::get('/user-login', [AuthenticationController::class, 'userLogin']);
Route::get('/user-register', [AuthenticationController::class, 'userRegister']);
Route::get('/user-forgot-password', [AuthenticationController::class, 'forgotPassword']);
Route::get('/user-lock-screen', [AuthenticationController::class, 'lockScreen']);

// Misc Route
Route::get('/page-404', [MiscController::class, 'page404']);
Route::get('/page-maintenance', [MiscController::class, 'maintenancePage']);
Route::get('/page-500', [MiscController::class, 'page500']);