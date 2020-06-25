<?php
Route::get('/', function () {
    return view('welcome');
});
Auth::routes();
Route::get('/', 'HomeController@index')->name('home');
Route::get('/mypdf', 'HomeController@mypdf')->name('mypdf');
Route::resource('users', 'UserController');
Route::resource('roles', 'RoleController');
Route::resource('permissions', 'PermissionController');
Route::resource('posts', 'PostController');
Route::resource('stocks', 'StockController');
Route::resource('purchase', 'PurchaseController');
Route::resource('vendors', 'VendorController');
Route::resource('operationsHealth', 'OperationHealthController');


// v1 : LeadManagement

Route::get('/client-v1-list','ClientController@clientv1')->name('clientv1'); //v2
Route::get('/ajtrans','ClientController@ajtrans')->name('ajtrans'); //v2

Route::get('/payments-recieved-list','ClientController@paymentRecievedLIST')->name('paymentRecievedLIST'); //v2

Route::get('/order-approval-list','ClientController@orderApprovalList')->name('orderApprovalList'); //v2


Route::post('/getOrderApprovalListData','OrderController@getOrderApprovalListData')->name('getOrderApprovalListData'); //v2






Route::get('/clientv1/{clietid}','ClientController@viewClientv1')->name('viewClientv1'); //v2


Route::get('/Client-v1-leads','ClientController@clientv1Leads')->name('clientv1Leads'); //v2
Route::get('/support-ticket','UserController@supportTicket')->name('supportTicket'); //v2



Route::post('/getLeadNotesData','ClientController@getLeadNotesData')->name('getLeadNotesData'); //v2
Route::post('/saveNewLead','ClientController@saveNewLead')->name('saveNewLead'); //v2



Route::post('/setPaymentRecCommnet','ClientController@setPaymentRecCommnet')->name('setPaymentRecCommnet'); //v2
Route::post('/setPaymentRecOrder','ClientController@setPaymentRecOrder')->name('setPaymentRecOrder'); //v2





// Route::get('get-leads', 'UserController@getLeadsAcceessList')->name('getLeadsAcceessList');//v2





// v1 : LeadMangement

//v2
Route::resource('client', 'ClientController');
Route::resource('orders', 'OrderController');
Route::post('/getClientsList','ClientController@getClientsList')->name('getClientsList'); //v2
Route::post('/getLeadList','UserController@getLeadList')->name('getLeadList'); //v2
Route::post('/getLeadListViewAll','UserController@getLeadListViewAll')->name('getLeadListViewAll'); //v2


Route::post('/getLeadList_LMLayout','UserController@getLeadList_LMLayout')->name('getLeadList_LMLayout'); //v2




Route::post('/getLeadList_SALES_END','UserController@getLeadList_SALES_END')->name('getLeadList_SALES_END'); //v2




Route::post('/getLeadListSalesOwn','UserController@getLeadListSalesOwn')->name('getLeadListSalesOwn'); //v2



Route::get('/getLeadReports','UserController@getLeadReports')->name('getLeadReports'); //v2


Route::get('/login-activity','UserController@loginActivity')->name('loginActivity'); //v2
Route::get('/login-activity/{id}','UserController@viewLoginActivityData')->name('viewLoginActivityData'); //v2


Route::post('/getLoginActivityUser','UserController@getLoginActivityUser')->name('getLoginActivityUser'); //v2
Route::post('/getLoginActivityDetails','UserController@getLoginActivityDetails')->name('getLoginActivityDetails'); //v2





Route::get('/getLeadStagesGraph','UserController@getLeadStagesGrapgh')->name('getLeadStagesGrapgh'); //v2


Route::get('/lead-distribution','UserController@getLeadReports_Dist')->name('getLeadReports_Dist'); //v2







Route::post('/getClientsListTodayFUP','ClientController@getClientsListTodayFUP')->name('getClientsListTodayFUP'); //v2
Route::post('/getClientsListYestardayFUP','ClientController@getClientsListYestardayFUP')->name('getClientsListYestardayFUP'); //v2
Route::post('/getClientsListDelayFUP','ClientController@getClientsListDelayFUP')->name('getClientsListDelayFUP'); //v2


Route::post('/getClientsNotesList','ClientController@getClientsNotesList')->name('getClientsNotesList'); //v2
Route::post('/getSamplesList','SampleController@getSamplesList')->name('getSamplesList'); //v2
Route::post('/getSamplesListNew','SampleController@getSamplesListNew')->name('getSamplesListNew'); //v2
Route::post('/getSamplesListUserWise','SampleController@getSamplesListUserWise')->name('getSamplesListUserWise'); //v2

Route::post('/getSampleDetails','SampleController@getSampleDetails')->name('getSampleDetails'); //v2
Route::post('/softdeleteClient','ClientController@softdeleteClient')->name('softdeleteClient');
Route::post('/getClientDetails','ClientController@getClientDetails')->name('getClientDetails');
Route::post('/edit/client','ClientController@edit_client')->name('edit_client');
Route::resource('sample', 'SampleController'); //v2


Route::get('add_stage_sample/{leadid}', 'SampleController@add_stage_sample')->name('add_stage_sample'); //v2
Route::get('add-mylead-sample/{leadid}', 'SampleController@add_myLead_sample')->name('add_myLead_sample'); //v2


Route::post('sample.storeLead', 'SampleController@storeLead')->name('sample.storeLead'); //v2

Route::get('sample-list', 'SampleController@sampleListSales')->name('sampleListSales'); //v2
Route::post('/getSamplesListSalesDash','SampleController@getSamplesListSalesDash')->name('getSamplesListSalesDash');




Route::post('/deleteSample','SampleController@deleteSample')->name('deleteSample'); //v2
Route::post('/deletePaymentRequest','ClientController@deletePaymentRequest')->name('deletePaymentRequest'); //v2

Route::resource('rawclientdata', 'RawClientDataController'); //v2

Route::post('import', 'RawClientDataController@import')->name('import'); //v2
Route::post('importAttendance', 'RawClientDataController@importAttendance')->name('importAttendance'); //v2

Route::post('importOrder', 'RawClientDataController@importOrder')->name('importOrder'); //v2


Route::get('export', 'RawClientDataController@export')->name('export'); //v2
Route::get('export_sample', 'RawClientDataController@export_sample')->name('export_sample');//v2
Route::get('export_sample_attendace', 'RawClientDataController@export_sample_attendace')->name('export_sample_attendace');//v2
Route::post('getMasterAttenDance', 'UserController@getMasterAttenDance')->name('getMasterAttenDance');//v2


Route::post('getMyMasterAttenDance', 'UserController@getMyMasterAttenDance')->name('getMyMasterAttenDance');//v2
Route::post('getIndividualAttendance', 'UserController@getIndividualAttendance')->name('getIndividualAttendance');//v2

Route::get('myAttendance', 'UserController@myAttendance')->name('myAttendance');//v2
Route::get('getIndData', 'UserController@getINDMartData')->name('getINDMartData');//v2
Route::get('get-leads-data', 'UserController@getINDMartDataNEW')->name('getINDMartDataNEW');//v2

Route::get('get-leads', 'UserController@getLeadsAcceessList')->name('getLeadsAcceessList');//v2
Route::get('my-leads', 'UserController@getLeadsAcceessListOwn')->name('getLeadsAcceessListOwn');//v2
Route::get('add-new-leads', 'UserController@AddNewLead')->name('AddNewLead');//v2



Route::get('printLabel/{sampleID}/{newsample}', 'UserController@printLabel')->name('printLabel');//v2
Route::get('send/quatation/{sampleID}', 'UserController@sendQuationView')->name('sendQuationView');//v2





Route::get('add-new-lead', 'UserController@add_lead_data')->name('add_lead_data');//v2
Route::post('updateLeadData', 'UserController@updateLeadData')->name('updateLeadData');//v2

Route::post('saveLeadData', 'UserController@saveLeadData')->name('saveLeadData');//v2



Route::get('users/lead/{leadid}/edit', 'UserController@editLead')->name('editLead');//v2


Route::post('setClientUpdation', 'UserController@setClientUpdation')->name('setClientUpdation');//v2




Route::get('sample/print/{id}', 'SampleController@print')->name('print_sample');//v2
Route::get('sample.print.all', 'SampleController@print_all')->name('sample.print.all');//v2
Route::get('client.notes', 'ClientController@getClient_notes_view')->name('client.notes');//v2

Route::post('add.notes', 'ClientController@add_Note')->name('add.notes'); //v2
Route::post('addNotesONLead', 'ClientController@addNotesONLead')->name('addNotesONLead'); //v2

Route::post('myLeadTranfer', 'ClientController@myLeadTranfer')->name('myLeadTranfer'); //v2
Route::post('deleteMyLead', 'ClientController@deleteMyLead')->name('deleteMyLead'); //v2
Route::post('saveQuationDataAsDraft', 'ClientController@saveQuationDataAsDraft')->name('saveQuationDataAsDraft'); //v2
Route::post('getCID_Quation_data', 'ClientController@getCID_Quation_data')->name('getCID_Quation_data'); //v2




Route::post('delete.note', 'ClientController@deleteNote')->name('delete.note'); //v2
Route::post('upload.dropzone', 'HomeController@UploadDropzone')->name('upload.dropzone'); //v2
Route::get('user/profile', 'UserController@userProfile')->name('user.profile'); //v2
Route::post('getOrdersList', 'OrderController@getOrdersList')->name('getOrdersList'); //v2
Route::post('getOrderData', 'OrderController@getOrderData')->name('getOrderData'); //v2

Route::post('getOrderMainList', 'OrderController@getOrderMainList')->name('getOrderMainList'); //v2
Route::post('getRawClientData', 'RawClientDataController@getRawClientData')->name('getRawClientData'); //v2
Route::post('getOrderItemsList', 'OrderController@getOrderItemsList')->name('getOrderItemsList'); //v2
Route::post('saveMaterialItem', 'OrderController@saveMaterialItem')->name('saveMaterialItem'); //v2
Route::post('getOrderMaterialItemAddedList', 'OrderController@getOrderMaterialItemAddedList')->name('getOrderMaterialItemAddedList'); //v2
Route::get('getMaterialAttribue', 'OrderController@getMaterialAttribue')->name('getMaterialAttribue'); //v2
Route::get('setMaterialAttribue', 'OrderController@setMaterialAttribue')->name('setMaterialAttribue'); //v2
Route::get('orders-info/{id}', 'OrderController@getOrderInfo')->name('getOrderInfo'); //v2
Route::get('orders-add-material/{id}', 'OrderController@orderAddMaterial')->name('orderAddMaterial'); //v2



Route::get('getCatItems', 'OrderController@getCatItems')->name('getCatItems'); //v2
Route::post('getSalesInvoiceReqestList', 'OrderController@getSalesInvoiceReqestList')->name('getSalesInvoiceReqestList'); //v2

Route::post('deleteReqInvoice', 'OrderController@deleteReqInvoice')->name('deleteReqInvoice'); //v2



Route::post('getPaymentReqestList', 'OrderController@getPaymentReqestList')->name('getPaymentReqestList'); //v2

Route::post('getPaymentRequestDataAdmin', 'OrderController@getPaymentRequestDataAdmin')->name('getPaymentRequestDataAdmin'); //v2







Route::post('saveOrderItem', 'OrderController@saveOrderItem')->name('saveOrderItem'); //v2
Route::post('getOrderMItemsAddedList', 'OrderController@getOrderMItemsAddedList')->name('getOrderMItemsAddedList'); //v2
Route::post('deleteItemOrder', 'OrderController@deleteItemOrder')->name('deleteItemOrder'); //v2
Route::post('getStock_AddedList', 'OrderController@getStock_AddedList')->name('getStock_AddedList'); //v2
Route::post('reserveItemfromStock', 'OrderController@reserveItemfromStock')->name('reserveItemfromStock'); //v2
Route::post('reserveItemfromStock', 'OrderController@reserveItemfromStock')->name('reserveItemfromStock'); //v2
Route::post('purchaseItemforStock', 'OrderController@purchaseItemforStock')->name('purchaseItemforStock'); //v2
Route::post('saveCateory', 'OrderController@saveCateory')->name('saveCateory'); //v2
Route::post('saveItemName', 'OrderController@saveItemName')->name('saveItemName'); //v2
Route::post('deleteOrderNow', 'OrderController@deleteOrderNow')->name('deleteOrderNow'); //v2
Route::get('purchase-request-list', 'PurchaseController@purchaseReqAlert')->name('purchase.req.alert'); //v2
Route::get('stock-request-list', 'StockController@stockReqAlert')->name('stock.req.alert'); //v2
Route::get('sample/add/{id}', 'SampleController@addSamplebyID')->name('sample/add'); //v2
Route::post('getPurchaseRequestAlert', 'PurchaseController@getPurchaseRequestAlert')->name('getPurchaseRequestAlert'); //v2

Route::post('getPurchaseRequestGroupTotal', 'PurchaseController@getPurchaseRequestGroupTotal')->name('getPurchaseRequestGroupTotal'); //v2
Route::get('purchase-order/{id}', 'PurchaseController@createPurchaseOrder')->name('createPurchaseOrder'); //v2
Route::post('savePurchaseOrder', 'PurchaseController@savePurchaseOrder')->name('savePurchaseOrder'); //v2
Route::post('getRequestedItems', 'StockController@getRequestedItems')->name('getRequestedItems'); //v2
Route::post('BOMConfirmation', 'OrderController@BOMConfirmation')->name('BOMConfirmation'); //v2
Route::post('deleteBOMItems', 'OrderController@deleteBOMItems')->name('deleteBOMItems'); //v2
Route::get('pending-reserve', 'OrderController@purchaseReserved')->name('purchase.reserved'); //v2
Route::post('getPurchaseReserved', 'OrderController@getPurchaseReservedList')->name('getPurchaseReserved'); //v2
Route::post('purchase-request-entry', 'OrderController@purchaseItemforStock')->name('purchaseRequestEntry'); //v2
Route::get('purchased-orders-list', 'PurchaseController@purchasedOrdersList')->name('purchasedOrdersList'); //v2
Route::post('getPurchasedOrderedlist', 'PurchaseController@getPurchasedOrderedlist')->name('getPurchasedOrderedlist'); //v2


//recieved orders
Route::get('recieved-orders', 'StockController@recievedOrders')->name('recievedOrders'); //v2
Route::post('getRecievedOrders', 'StockController@getRecievedOrders')->name('getRecievedOrders'); //v2
Route::get('orders-recieved', 'StockController@ordersRecieved')->name('ordersRecieved'); //v2
Route::post('getPurchaseOrderData', 'PurchaseController@getPurchaseOrderData')->name('getPurchaseOrderData'); //v2
Route::post('saveRecievedPurchaseOrder', 'StockController@saveRecievedPurchaseOrder')->name('saveRecievedPurchaseOrder'); //v2
Route::get('purchase-list', 'PurchaseController@purchaseList')->name('purchaseList'); //v2
Route::get('purchase-list-printed-box', 'PurchaseController@purchaseListPrintedBOx')->name('purchaseListPrintedBOx'); //v2

Route::post('getRecievedOrdersListNew', 'StockController@getRecievedOrdersListNew')->name('getRecievedOrdersListNew'); //v2
Route::get('recieved-orders/{id}', 'StockController@recievedPendingOrders')->name('recievedPendingOrders'); //v2
Route::post('reservedNowItems', 'StockController@reservedNowItems')->name('reservedNowItems'); //v2
Route::post('IssueNowItems', 'StockController@IssueNowItems')->name('IssueNowItems'); //v2
Route::get('stocks-entry', 'StockController@StockEntry')->name('stocks.entry'); //v2
Route::post('saveStockItems', 'StockController@saveStockItems')->name('saveStockItems'); //v2
Route::post('getStocks', 'StockController@getStocks')->name('getStocks'); //v2
Route::get('import-export', 'HomeController@ImportExport')->name('import-export'); //v2
Route::post('import-data', 'RawClientDataController@importData')->name('import_data'); //v2
Route::post('delete.items', 'StockController@deleteItems')->name('delete.items'); //v2
Route::post('getVendorList', 'VendorController@getVendorList')->name('getVendorList'); //v2
Route::post('userAccess', 'UserController@userAccess')->name('userAccess'); //v2
Route::post('userAccessRemove', 'UserController@userAccessRemove')->name('userAccessRemove'); //v2
Route::post('saveOrderItemsAddmore', 'OrderController@saveOrderItemsAddmore')->name('saveOrderItemsAddmore'); //v2
Route::get('client-leads', 'ClientController@clientLeads')->name('client.leads'); //v2
Route::get('today-client-follow-up', 'ClientController@todayClientFollow')->name('today.clientFollow'); //v2
Route::get('yestarday-client-follow-up', 'ClientController@yestardayClientFollow')->name('yestarday.clientFollow'); //v2
Route::get('delayed-client-follow-up', 'ClientController@delayedClientFollow')->name('delayed.clientFollow'); //v2
Route::get('new-sample', 'SampleController@sampleNew')->name('sample.new'); //v2

Route::get('/getClientsListd','ClientController@getClientsListApi')->name('getClientsListApi');





Route::get('/sendMail','ClientController@sendMail')->name('sendMail');
Route::post('/saveFeedback','SampleController@saveFeedback')->name('saveFeedback');
Route::post('/UserResetPassword','UserController@UserResetPassword')->name('UserResetPassword');
Route::get('/sendSMS/{phone_number}', 'SMSController@sendSMS');
Route::post('/customLogin', 'Auth\LoginController@customLogin')->name('customLogin');
Route::post('/LoginOTPVerify', 'Auth\LoginController@LoginOTPVerify')->name('LoginOTPVerify');
Route::get('/pending-feedback-sample', 'SampleController@samplePendingFeedback')->name('sample.pending.feedback');
Route::get('/users/permission/{user_id}', 'UserController@userPermissions')->name('userPermissions');
Route::post('/setUserPermission', 'UserController@setUserPermission')->name('setUserPermission');
Route::get('/add-permission-users', 'UserController@addPermissionUsers')->name('add.permission.users');
Route::post('/saveuserPermission', 'UserController@saveuserPermission')->name('saveuserPermission');
Route::get('/reports-sales-graph', 'UserController@reportSalesGraph')->name('reportSalesGraph');

Route::post('/sentTicketRequest', 'UserController@sentTicketRequest')->name('sentTicketRequest');
Route::post('/getTicketListData', 'UserController@getTicketListData')->name('getTicketListData');


Route::post('/getTicketListDataInfo', 'UserController@getTicketListDataInfo')->name('getTicketListDataInfo');

Route::post('/sendEmailQuatation', 'ClientController@sendEmailQuatation')->name('sendEmailQuatation');
Route::post('/downloadQuatation', 'ClientController@downloadQuatation')->name('downloadQuatation');





Route::get('/view-tickets', 'UserController@view_ticket_data')->name('view_ticket_data');




Route::get('/print/qcform/{id}', 'OrderController@print_QCFORM')->name('print.qcform');
Route::get('/view_preview_quatation/{id}', 'ClientController@view_preview_quatation')->name('view_preview_quatation');
Route::get('quatations', 'ClientController@getQutatationList')->name('getQutatationList');
Route::post('getAjaxQuatationList', 'ClientController@getAjaxQuatationList')->name('getAjaxQuatationList');
Route::get('/quatation/preview/{id}', 'ClientController@quationPreview')->name('quationPreview');
Route::get('/addNew_Quotation', 'ClientController@addNew_Quotation')->name('addNew_Quotation');



Route::get('/print/qcform-bulk/{id}', 'OrderController@print_QCFORM_BULK')->name('print.qcform_bulk');

Route::get('/qcform/creates', 'OrderController@qcformStore')->name('qcform.creates');


Route::get('missed-cronjob', 'OrderController@viewMissedCronJob')->name('viewMissedCronJob');

Route::post('/save-qc-from', 'OrderController@saveQCdata')->name('saveQCdata');
Route::post('/save-qc-from-copy', 'OrderController@saveQC_Copy')->name('saveQC_Copy');








Route::post('/qcform.getList', 'OrderController@qcFormList')->name('qcform.getList');
Route::post('/qcform.getList_v1', 'OrderController@qcFormListV1')->name('qcform.getList_v1');
Route::post('/getPayOrderApprovalList', 'OrderController@getPayOrderApprovalList')->name('getPayOrderApprovalList');
Route::post('/mark_as_row_material', 'ClientController@markAsRawMaterial')->name('mark_as_row_material');


Route::post('/setTicketResponseSELF', 'UserController@setTicketResponseSELF')->name('setTicketResponseSELF');



Route::post('/getOrderQty', 'OrderController@getOrderQty')->name('getOrderQty');

Route::get('/payment-confirmation-request', 'OrderController@PaymentRequestConfirmation')->name('PaymentRequestConfirmation');

Route::get('/sales-invoice-Request', 'OrderController@SaleInvoiceRequest')->name('SaleInvoiceRequest');

Route::get('/lead-manager-report', 'UserController@getLeadManagerReport')->name('getLeadManagerReport');









Route::post('/qcformGetList_OrderLIst', 'OrderController@qcformGetList_OrderLIst')->name('qcformGetList_OrderLIst');

Route::post('/qcformgetListBulk', 'OrderController@qcformgetListBulk')->name('qcformgetListBulk');



Route::post('/qcform_getList_dispatched', 'OrderController@qcform_getList_dispatched')->name('qcform_getList_dispatched');


Route::post('/getQcOrderStagePendingList', 'OrderController@getQcOrderStagePendingList')->name('getQcOrderStagePendingList');
Route::post('/setSaveProcessAction', 'OrderController@setSaveProcessAction')->name('setSaveProcessAction');




Route::get('/qcform/list', 'OrderController@qcFormListView')->name('qcform.list');
Route::get('/qcform/orders/dispatched', 'OrderController@qcFormListViewDispatched')->name('qcFormListViewDispatched');
Route::get('/qc-bulk-orders', 'OrderController@qcform_getList_BulkList')->name('qcform_getList_BulkList');


Route::get('/feedback/pie/graph', 'SampleController@feedbackSampleGraph')->name('feedbackSampleGraph');
Route::post('/getOrderDataInfo', 'SampleController@getOrderDataInfo')->name('getOrderDataInfo');
Route::get('/back-order-upload', 'OrderController@backOrderUpload')->name('backOrderUpload');
Route::get('/qcform/{form_id}/edit', 'OrderController@qceditForm')->name('qceditForm');
Route::get('/qcform/bulk/{form_id}/edit', 'OrderController@qceditBULKForm')->name('qceditForm');

Route::get('/qcform/{form_id}/copy-order', 'OrderController@qcFormCopy')->name('qcFormCopy');



Route::get('/order-wizard/{orderid}', 'OrderController@orderWizard')->name('orderWizard');
Route::post('updateQCdata', 'OrderController@updateQCdata')->name('updateQCdata');
Route::post('updateQCdataNewWays', 'OrderController@updateQCdataNewWays')->name('updateQCdataNewWays');
Route::post('updateQCdataNewWaysBULK', 'OrderController@updateQCdataNewWaysBULK')->name('updateQCdataNewWaysBULK');
Route::post('getMyQCData', 'OrderController@getMyQCData')->name('getMyQCData');

Route::post('saveSalesInvoiceRequest', 'OrderController@saveSalesInvoiceRequest')->name('saveSalesInvoiceRequest');
Route::post('saveSalesInvoiceRequestAccessed', 'OrderController@saveSalesInvoiceRequestAccessed')->name('saveSalesInvoiceRequestAccessed');



Route::post('savePaymentRecivedClient', 'OrderController@savePaymentRecivedClient')->name('savePaymentRecivedClient');







Route::post('saveOrderProcessDays', 'OrderController@saveOrderProcessDays')->name('saveOrderProcessDays');
Route::get('orders-list', 'OrderController@orderList')->name('orderList');
Route::post('deleteQcForm', 'OrderController@deleteQcForm')->name('deleteQcForm');
Route::post('getOrderWizardList', 'OrderController@getOrderWizardList')->name('getOrderWizardList');
Route::post('save_order_process', 'OrderController@save_order_process')->name('save_order_process');
Route::post('getOrderProcessSteps', 'OrderController@getOrderProcessSteps')->name('getOrderProcessSteps');
Route::post('printSamplewithFilter', 'SampleController@printSamplewithFilter')->name('printSamplewithFilter');
Route::get('sample-pending-list', 'SampleController@viewSamplePendingList')->name('viewSamplePendingList');
Route::get('getClientData', 'HomeController@getClientInfo')->name('getClientInfo');
Route::get('purchase-list', 'OrderController@qcFROMPurchaseList')->name('qcform.purchaselist');
Route::get('purchase-list-printed-label', 'OrderController@qcFROMPurchaseListPrintedLabel')->name('qcFROMPurchaseListPrintedLabel');

Route::post('getPurchaseListQCFROM', 'OrderController@getPurchaseListQCFROM')->name('getPurchaseListQCFROM');
Route::post('getPurchaseListQCFROM_V1', 'OrderController@getPurchaseListQCFROM_V1')->name('getPurchaseListQCFROM_V1');
Route::post('getPurchaseListQCFROM_V1_LABEL_BOX', 'OrderController@getPurchaseListQCFROM_V1_LABEL_BOX')->name('getPurchaseListQCFROM_V1_LABEL_BOX');

Route::post('getPurchaseListQCFROM_V1_MODFIED', 'OrderController@getPurchaseListQCFROM_V1_MODFIED')->name('getPurchaseListQCFROM_V1_MODFIED');





Route::post('getPurchaseListQCFROMArtWork', 'OrderController@getPurchaseListQCFROMArtWork')->name('getPurchaseListQCFROMArtWork');
Route::post('getPurchaseListQCFROMArtWorkAllOther', 'OrderController@getPurchaseListQCFROMArtWorkAllOther')->name('getPurchaseListQCFROMArtWorkAllOther');


Route::post('getPurchaseListHistory', 'OrderController@getPurchaseListHistory')->name('getPurchaseListHistory');

Route::post('setQCPurchaseStatus', 'OrderController@setQCPurchaseStatus')->name('setQCPurchaseStatus');
Route::post('setQCProductionStatus', 'OrderController@setQCProductionStatus')->name('setQCProductionStatus');


//20
//production list
Route::get('production-list', 'OrderController@qcFROMProductionList')->name('qcform.qcFROMProductionList');
Route::post('getQCFromProduction', 'OrderController@getQCFromProduction')->name('getQCFromProduction');
Route::post('setgetQCFromProductionStage', 'OrderController@setgetQCFromProductionStage')->name('setgetQCFromProductionStage');


Route::post('getQCFormOrderData', 'OrderController@getQCFormOrderData')->name('getQCFormOrderData');
Route::post('UpdateOrderDispatch', 'OrderController@UpdateOrderDispatch')->name('UpdateOrderDispatch');
Route::post('UpdateOrderDispatch_v1', 'OrderController@UpdateOrderDispatch_v1')->name('UpdateOrderDispatch_v1');



Route::get('orders-statges-reports', 'OrderController@orderStagesReport')->name('orderStagesReport');
Route::post('getOrderStatgesReport', 'OrderController@getOrderStatgesReport')->name('getOrderStatgesReport');

Route::get('getOrderList/{step_code}/{my_color}', 'OrderController@getOrderList')->name('getOrderList');


Route::post('getCurrentOrderStagesData', 'OrderController@getCurrentOrderStagesData')->name('getCurrentOrderStagesData');

Route::get('get-stages-report', 'OrderController@getStagesReportbyteam')->name('getStagesReportbyteam');
Route::post('getStagesByTeamWithFilter', 'OrderController@getStagesByTeamWithFilter')->name('getStagesByTeamWithFilter');

Route::get('monthly-sales-report', 'OrderController@getMonthlySalesReport')->name('getMonthlySalesReport');


Route::post('getOperationHealthData', 'OperationHealthController@getOperationHealthData')->name('getOperationHealthData');


Route::get('sapCheckList', 'OrderController@sapCheckList')->name('sapCheckList');
Route::post('getSAPCheckListData', 'OrderController@getSAPCheckListData')->name('getSAPCheckListData');

Route::post('setProcessSAPChecklist', 'OrderController@setProcessSAPChecklist')->name('setProcessSAPChecklist');


Route::get('operation-plan', 'OperationHealthController@operationPlan')->name('operationPlan');
Route::post('getOperationOrderPlan', 'OperationHealthController@getOperationOrderPlan')->name('getOperationOrderPlan');

Route::post('save_plan_wizard', 'OperationHealthController@save_plan_wizard')->name('save_plan_wizard');

Route::post('savePlanDay3QTY', 'OperationHealthController@savePlanDay3QTY')->name('savePlanDay3QTY');

Route::post('getPlanedOrderDataDay2', 'OperationHealthController@getPlanedOrderDataDay2')->name('getPlanedOrderDataDay2');

Route::get('order-stages-daywise', 'OrderController@getOrderStageDaysWise')->name('getOrderStageDaysWise');
Route::get('order-stages-daywise-v1', 'OrderController@getOrderStageDaysWisev1')->name('getOrderStageDaysWisev1');

Route::post('getFilteruserWiseStageCompleted', 'OrderController@getFilteruserWiseStageCompleted')->name('getFilteruserWiseStageCompleted');
Route::post('getFilterLeadStagesCompleted', 'OrderController@getFilterLeadStagesCompleted')->name('getFilterLeadStagesCompleted');
Route::post('getFilterLeadLMReportCompleted', 'OrderController@getFilterLeadLMReportCompleted')->name('getFilterLeadLMReportCompleted');




Route::get('BoReports', 'OrderController@BoReports')->name('BoReports');
Route::get('sap-check-list-graph', 'OrderController@sap_chklistGraph')->name('sap_chklistGraph');

Route::get('stage-completed-filter', 'OrderController@stageCompletdFilter')->name('stageCompletdFilter');
Route::get('stage-completed-filter-v1', 'OrderController@stageCompletdFilterV1')->name('stageCompletdFilterV1');


Route::post('getOrderListOfstageCompleted', 'OrderController@getOrderListOfstageCompleted')->name('getOrderListOfstageCompleted');
Route::post('getPaymentDataDETAILSHOW', 'OrderController@getPaymentDataDETAILSHOW')->name('getPaymentDataDETAILSHOW');

Route::post('getPaymentDataDETAILSHOW_HIST', 'OrderController@getPaymentDataDETAILSHOW_HIST')->name('getPaymentDataDETAILSHOW_HIST');
Route::post('getPaymentDataDETAILSHOW_HIST_ORDER', 'OrderController@getPaymentDataDETAILSHOW_HIST_ORDER')->name('getPaymentDataDETAILSHOW_HIST_ORDER');





Route::get('pending-process', 'OrderController@pendingProcessReport')->name('pendingProcessReport');

Route::post('getOperatonsInfo', 'OperationHealthController@getOperatonsInfo')->name('getOperatonsInfo');
Route::post('getOperatonsPlanOrderDetails', 'OperationHealthController@getOperatonsPlanOrderDetails')->name('getOperatonsPlanOrderDetails');


Route::post('save_OPHPlan_Day4', 'OperationHealthController@save_OPHPlan_Day4')->name('save_OPHPlan_Day4');
Route::post('getPlanedOrderDay4Data', 'OperationHealthController@getPlanedOrderDay4Data')->name('getPlanedOrderDay4Data');
Route::get('operions-plan-lists', 'OperationHealthController@getOperationHealthPlanList')->name('getOperationHealthPlanList');



Route::post('getOHPlanList', 'OperationHealthController@getOHPlanList')->name('getOHPlanList');
Route::get('dispatched-report', 'OrderController@dispatchedReport')->name('dispatchedReport');



Route::get('plan-view-print/{planid}', 'OperationHealthController@planViewPrint')->name('planViewPrint');

Route::get('add-plan-achieve/{planid}', 'OperationHealthController@addPlanAchieve')->name('addPlanAchieve');

Route::post('SavePlanAchievedData', 'OperationHealthController@SavePlanAchievedData')->name('SavePlanAchievedData');

Route::post('savePlanAchieveData', 'OperationHealthController@savePlanAchieveData')->name('savePlanAchieveData');

Route::get('packing-options-catalog', 'OrderController@packagingOptionCategLog')->name('packagingOptionCategLog');
Route::get('packing-options-catalog-list', 'OrderController@packagingOptionCategLogList')->name('packagingOptionCategLogList');

Route::post('saveOPCDataOnly', 'OrderController@saveOPCDataOnly')->name('saveOPCDataOnly')->middleware('optimizeImages');

Route::post('getPOCDataAll', 'OrderController@getPOCDataAll')->name('getPOCDataAll');

Route::get('edit-poc/{poc_id}', 'OrderController@editPOC')->name('editPOC');

Route::get('add-view-report/{plan_id}', 'OrderController@viewReportOPlan')->name('viewReportOPlan');

Route::post('getPOCImges', 'OrderController@getPOCImges')->name('getPOCImges');

Route::post('getPartialOrderQty', 'OrderController@getPartialOrderQty')->name('getPartialOrderQty');

Route::post('getPOCFilter', 'OrderController@getPOCFilter')->name('getPOCFilter');
Route::get('getPOCInfinite', 'OrderController@getPOCInfinite')->name('getPOCInfinite');

Route::post('deletePOC', 'OrderController@deletePOC')->name('deletePOC');
Route::post('getClientOrderReportList', 'OrderController@getClientOrderReportList')->name('getClientOrderReportList');
Route::post('getClientOrderReportListFilter', 'OrderController@getClientOrderReportListFilter')->name('getClientOrderReportListFilter');

Route::post('setLeadAssign', 'UserController@setLeadAssign')->name('setLeadAssign');



Route::get('client-orders-report', 'OrderController@client_order_report')->name('client_order_report');

Route::get('payment-recieved-report', 'OrderController@client_paymentRecieved_report')->name('client_paymentRecieved_report');




Route::post('getPaymentRecievedReportListFilter', 'OrderController@getPaymentRecievedReportListFilter')->name('getPaymentRecievedReportListFilter');





Route::get('view-order-details/{client_id}', 'OrderController@viewOrderClient')->name('viewOrderClient');



 //ajcode for new stage
 Route::get('v1_getOrderslist', 'OrderController@v1_getOrderslist')->name('v1_getOrderslist');
 Route::get('v1Admin_getOrderslist', 'OrderController@v1Admin_getOrderslist')->name('v1Admin_getOrderslist');


 Route::get('get-sales-invoice', 'OrderController@getSalesInoviceRequest')->name('getSalesInoviceRequest');

 Route::post('getSalesInvoiceData', 'OrderController@getSalesInvoiceData')->name('getSalesInvoiceData');
 Route::post('saveAccountResponseOnSInvoiceRequest', 'OrderController@saveAccountResponseOnSInvoiceRequest')->name('saveAccountResponseOnSInvoiceRequest');





 Route::get('QCAccess', 'OrderController@QCAccess')->name('QCAccess');


 Route::post('getAllOrderStagev1', 'OrderController@getAllOrderStagev1')->name('getAllOrderStagev1');
 Route::post('getAllOrderStagev1_rnd', 'OrderController@getAllOrderStagev1_rnd')->name('getAllOrderStagev1_rnd');
 Route::post('getAllOrderStagev1_lead', 'OrderController@getAllOrderStagev1_lead')->name('getAllOrderStagev1_lead');
 Route::post('getAllOrderStagev1_MY_lead', 'OrderController@getAllOrderStagev1_MY_lead')->name('getAllOrderStagev1_MY_lead');






 Route::get('boPurchaseList', 'OrderController@boPurchaseList')->name('boPurchaseList');
 Route::get('boPurchaseListLB', 'OrderController@boPurchaseListLB')->name('boPurchaseListLB');

 Route::post('getAllPurchaseStagev1', 'OrderController@getAllPurchaseStagev1')->name('getAllPurchaseStagev1');





 //HRMS
 Route::get('hr-dashboard', 'UserController@HrDashbaord')->name('hrms_dashboard');
 Route::get('employee', 'UserController@employee')->name('employee');
 Route::get('job_role', 'UserController@jobRole')->name('jobRole');

 Route::post('saveEmployee', 'UserController@saveEmployee')->name('saveEmployee');
 Route::post('getEmpListData', 'UserController@getEmpListData')->name('getEmpListData');
 Route::post('deleteEMP', 'UserController@deleteEMP')->name('deleteEMP');
 Route::post('deleteFromPurchaseListwithID', 'OrderController@deleteFromPurchaseListwithID')->name('deleteFromPurchaseListwithID');


 Route::post('/getLocation','UserController@getLocation')->name('getLocation');
 Route::post('/updateEmpdata','UserController@updateEmpdata')->name('updateEmpdata');
 Route::post('/saveKPIData','UserController@saveKPIData')->name('saveKPIData');
 Route::post('/saveKPIReportSubmit','UserController@saveKPIReportSubmit')->name('saveKPIReportSubmit');


 Route::post('getKPIData', 'UserController@getKPIData')->name('getKPIData');
 //Route::post('getEmpListDailyReport', 'UserController@getEmpListDailyReport')->name('getEmpListDailyReport');

 Route::post('getKIPDetailsByUserDay', 'UserController@getKIPDetailsByUserDay')->name('getKIPDetailsByUserDay');


 Route::get('/emp-view/{emp_id}','UserController@empView')->name('empView');
 Route::get('/kpi-details-history','UserController@kpiDetailHistory')->name('kpiDetailHistory');


 Route::get('/kpi-details/{emp_id}','UserController@kpiDetails')->name('kpiDetails');
 Route::post('/kpiupdateData','UserController@kpiupdateData')->name('kpiupdateData');

 Route::post('getKPIDataReportHistory','UserController@getKPIDataReportHistory')->name('getKPIDataReportHistory');

 Route::post('/kpiDetailHistory_all','UserController@kpiDetailHistory_all')->name('kpiDetailHistory_all');
 Route::get('/kpi-details-history-all/{id}','UserController@kpiDetailHistoryEMP')->name('kpiDetailHistoryEMP');

 Route::get('upload-attendance','UserController@upload_epm_attendance')->name('upload_epm_attendance');
 Route::post('setSaveVendorOrder','OrderController@setSaveVendorOrder')->name('setSaveVendorOrder');

 Route::post('setSaveVendorOrderRecieved','OrderController@setSaveVendorOrderRecieved')->name('setSaveVendorOrderRecieved');
 Route::get('ingredient-list','RNDController@IngredentList')->name('rnd.ingrednetList');
 Route::get('ingredient-category-list','RNDController@ingrednetCategoryList')->name('rnd.ingrednetCategoryList');

 Route::post('getFPData','RNDController@getFPData')->name('getFPData');



 Route::get('finish-product-list','RNDController@finishProduct')->name('rnd.finishProduct');
 Route::post('getFinishProductDataList','RNDController@getFinishProductDataList')->name('getFinishProductDataList');



 Route::get('ingredient-brand-list','RNDController@IngredentBrandList')->name('rnd.ingrednetBrandList');


Route::post('getIngredentList','RNDController@getIngredentList')->name('getIngredentList');
Route::post('getIngredentBrandList','RNDController@getIngredentBrandList')->name('getIngredentBrandList');
Route::post('getIngredentCategoryList','RNDController@getIngredentCategoryList')->name('getIngredentCategoryList');


Route::get('ingredent-add-supplier','RNDController@IngredentAddNew')->name('IngredentAddNew');
Route::get('ingredents-add-brand','RNDController@IngredentBrandAddNew')->name('IngredentBrandAddNew');
Route::get('add-ingredient','RNDController@addIngredetnView')->name('addIngredetnView');

Route::get('add-ingredient-category','RNDController@IngredentAddIngCat')->name('IngredentAddIngCat');


Route::post('getFinishProductList','RNDController@getFinishProductList')->name('getFinishProductList');
Route::post('saveFinishProduct','RNDController@saveFinishProduct')->name('saveFinishProduct');
Route::post('EditFinishProduct','RNDController@EditFinishProduct')->name('EditFinishProduct');


Route::get('add-finish-product','RNDController@addFinishProduct')->name('addFinishProduct');
Route::get('new-product-development','RNDController@NewProductProductDevlopment')->name('NewProductProductDevlopment');
Route::post('saveNewProductDevelopment','RNDController@saveNewProductDevelopment')->name('saveNewProductDevelopment');

Route::get('new-product-list','RNDController@NewProductProductDevlopmentList')->name('NewProductProductDevlopmentList');





Route::get('Ingredients','RNDController@Ingredients')->name('rnd.ingredients');
Route::post('getIngredients','RNDController@getIngredients')->name('getIngredients');
Route::post('deleteIngredient','RNDController@deleteIngredient')->name('deleteIngredient');


Route::post('getNewProductDevelopementList','RNDController@getNewProductDevelopementList')->name('getNewProductDevelopementList');



Route::post('ingredentsaveINGdata','RNDController@saveINGdata')->name('saveINGdata');
Route::post('saveINGBranddata','RNDController@saveINGBranddata')->name('saveINGBranddata');
Route::post('saveINGBrand','RNDController@saveINGBrand')->name('saveINGBrand');
Route::post('saveINGCategorydata','RNDController@saveINGCategorydata')->name('saveINGCategorydata');

Route::post('UpdateINGCategorydata','RNDController@UpdateINGCategorydata')->name('UpdateINGCategorydata');


Route::post('updateINGBrand','RNDController@updateINGBrand')->name('updateINGBrand');




Route::post('updateINGdata','RNDController@updateINGdata')->name('updateINGdata');
Route::post('EditNewProductDevelopment','RNDController@EditNewProductDevelopment')->name('EditNewProductDevelopment');



Route::post('updateIngredientdata','RNDController@updateIngredientdata')->name('updateIngredientdata');

Route::post('getIngredentListID','RNDController@getIngredentListID')->name('getIngredentListID');
Route::post('getIngredentBrandListID','RNDController@getIngredentBrandListID')->name('getIngredentBrandListID');
Route::post('saveFinishCatSubCat','RNDController@saveFinishCatSubCat')->name('saveFinishCatSubCat');
Route::post('getFinishProductCAT','RNDController@getFinishProductCAT')->name('getFinishProductCAT');
Route::post('getFinishProductcatSubListData','RNDController@getFinishProductcatSubListData')->name('getFinishProductcatSubListData');



Route::get('edit-ing/{ingid}','RNDController@editING')->name('editING');
Route::get('edit-ing-category/{ingid}','RNDController@editINGCategory')->name('editINGCategory');

Route::get('edit-ingrednts/{ingid}','RNDController@editIngredent')->name('editIngredent');

Route::get('edit-ing-brand/{ingid}','RNDController@editBrandING')->name('editBrandING');

Route::get('edit-finish-product/{ingid}','RNDController@editINGFinishProduct')->name('editINGFinishProduct');
Route::get('edit-new-product/{ingid}','RNDController@editnNewProductList')->name('editnNewProductList');




Route::get('finishProductCategory','RNDController@finishProductCategory')->name('finishProductCategory');
Route::get('finishProductSubCategory','RNDController@finishProductSubCategory')->name('finishProductSubCategory');

Route::post('getAllLeadData','UserController@getAllLeadData')->name('getAllLeadData');

Route::post('getAllLeadData_OWNLEAD','UserController@getAllLeadData_OWNLEAD')->name('getAllLeadData_OWNLEAD');




Route::post('setReplayToTicket','UserController@setReplayToTicket')->name('setReplayToTicket');






Route::get('add-finish-product-cat-sub','RNDController@add_finish_product_cat')->name('add_finish_product_cat');
Route::get('add-finish-product-sub-category','RNDController@add_finish_product_subcat')->name('add_finish_product_subcat');










// Route::post('getEMPPic','UserController@getEMPPic')->name('getEMPPic');
Route::post('deleteING','RNDController@deleteING')->name('deleteING');
Route::post('deleteNewProductDev','RNDController@deleteNewProductDev')->name('deleteNewProductDev');



Route::post('deleteINGCategory','RNDController@deleteINGCategory')->name('deleteINGCategory');

Route::post('deleteINGBrand','RNDController@deleteINGBrand')->name('deleteINGBrand');
Route::post('deleteFinishProduct','RNDController@deleteFinishProduct')->name('deleteFinishProduct');

Route::post('setSPRange','RNDController@setSPRange')->name('setSPRange');
Route::post('getSampleFeedbackPIE','SampleController@getSampleFeedbackPIE')->name('getSampleFeedbackPIE');

Route::get('pagination/fetch_data', 'UserController@fetch_data');




















 //ajcode for new stage
