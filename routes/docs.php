<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Docs\DocsController;

Route::group([
    'middleware' => ['auth:admin', 'blockURL'],
    'prefix' => 'docs',
    'as' => 'docs.',
], function () {

    Route::get('/', [DocsController::class, 'index'])->name('index');

    Route::get('/lead-module', [DocsController::class, 'leadModule'])->name('lead-module');

    Route::prefix('lead-module')->name('lead-module.')->group(function () {

        Route::get('/database-structure', [DocsController::class, 'leadModuleDatabase'])->name('database');

        Route::get('/lead-creation', [DocsController::class, 'leadModuleCreation'])->name('creation');

        Route::get('/duplicate-check', [DocsController::class, 'leadModuleDuplicate'])->name('duplicate');

        Route::get('/follow-up', [DocsController::class, 'leadModuleFollowup'])->name('followup');

        Route::get('/lead-notes', [DocsController::class, 'leadModuleNotes'])->name('notes');

        Route::get('/qualification', [DocsController::class, 'leadModuleQualification'])->name('qualification');

        Route::get('/activity-logs', [DocsController::class, 'leadModuleActivity'])->name('activity');

        Route::get('/whatsapp-integration', [DocsController::class, 'leadModuleWhatsapp'])->name('whatsapp');

    });

    Route::get('/lead-assignment', [DocsController::class, 'leadAssignment'])->name('lead-assignment');

    Route::get('/candidate-module', [DocsController::class, 'candidateModule'])->name('candidate-module');

    Route::prefix('candidate-module')->name('candidate-module.')->group(function () {

        Route::get('/database-structure', [DocsController::class, 'candidateModuleDatabase'])->name('database');

        Route::get('/candidate-creation', [DocsController::class, 'candidateModuleCreation'])->name('creation');

        Route::get('/publish-wizard', [DocsController::class, 'candidateModulePublish'])->name('publish');

        Route::get('/documents', [DocsController::class, 'candidateModuleDocuments'])->name('documents');

        Route::get('/status-pipeline', [DocsController::class, 'candidateModuleStatus'])->name('status');

        Route::get('/transactions', [DocsController::class, 'candidateModuleTransactions'])->name('transactions');

        Route::get('/activity-log', [DocsController::class, 'candidateModuleActivity'])->name('activity');

        Route::get('/leads-and-deals', [DocsController::class, 'candidateModuleLeadsDeals'])->name('leads-deals');

    });

    Route::get('/contacts-module', [DocsController::class, 'contactsModule'])->name('contacts-module');

    Route::prefix('contacts-module')->name('contacts-module.')->group(function () {

        Route::get('/database-structure', [DocsController::class, 'contactsModuleDatabase'])->name('database');

        Route::get('/contact-creation', [DocsController::class, 'contactsModuleCreation'])->name('creation');

        Route::get('/google-contacts-sync', [DocsController::class, 'contactsModuleGoogleSync'])->name('google-sync');

        Route::get('/groups', [DocsController::class, 'contactsModuleGroups'])->name('groups');

        Route::get('/lead-sync', [DocsController::class, 'contactsModuleLeadSync'])->name('lead-sync');

        Route::get('/export-and-history', [DocsController::class, 'contactsModuleExport'])->name('export');

        Route::get('/filters-and-saved-views', [DocsController::class, 'contactsModuleFilters'])->name('filters');

        Route::get('/lifecycle-status', [DocsController::class, 'contactsModuleLifecycle'])->name('lifecycle');

    });

    Route::get('/task-module', [DocsController::class, 'taskModule'])->name('task-module');

    Route::prefix('task-module')->name('task-module.')->group(function () {

        Route::get('/database-structure', [DocsController::class, 'taskModuleDatabase'])->name('database');

        Route::get('/task-creation-and-status', [DocsController::class, 'taskModuleCreation'])->name('creation');

        Route::get('/recurring-tasks', [DocsController::class, 'taskModuleRecurring'])->name('recurring');

        Route::get('/reminder-delivery', [DocsController::class, 'taskModuleReminders'])->name('reminders');

        Route::get('/shared-reminder-sink', [DocsController::class, 'taskModuleSharedSink'])->name('shared-sink');

        Route::get('/assignment-and-permissions', [DocsController::class, 'taskModuleAssignment'])->name('assignment');

        Route::get('/labels-and-departments', [DocsController::class, 'taskModuleLabels'])->name('labels');

        Route::get('/filters-and-saved-views', [DocsController::class, 'taskModuleFilters'])->name('filters');

    });

    Route::get('/deal-pipeline-module', [DocsController::class, 'dealPipelineModule'])->name('deal-pipeline-module');

    Route::prefix('deal-pipeline-module')->name('deal-pipeline-module.')->group(function () {

        Route::get('/database-structure', [DocsController::class, 'dealPipelineModuleDatabase'])->name('database');

        Route::get('/pipeline-stages-and-kanban', [DocsController::class, 'dealPipelineModuleStages'])->name('stages');

        Route::get('/deal-creation', [DocsController::class, 'dealPipelineModuleCreation'])->name('creation');

        Route::get('/settings-lookups', [DocsController::class, 'dealPipelineModuleSettings'])->name('settings');

        Route::get('/notes-and-files', [DocsController::class, 'dealPipelineModuleNotesFiles'])->name('notes-files');

        Route::get('/reminders-and-cron-jobs', [DocsController::class, 'dealPipelineModuleReminders'])->name('reminders');

        Route::get('/relationship-to-leads-and-candidates', [DocsController::class, 'dealPipelineModuleRelationships'])->name('relationships');

        Route::get('/filters-and-saved-views', [DocsController::class, 'dealPipelineModuleFilters'])->name('filters');

    });

    Route::get('/orders-module', [DocsController::class, 'ordersModule'])->name('orders-module');

    Route::prefix('orders-module')->name('orders-module.')->group(function () {

        Route::get('/database-structure', [DocsController::class, 'ordersModuleDatabase'])->name('database');

        Route::get('/order-creation', [DocsController::class, 'ordersModuleCreation'])->name('creation');

        Route::get('/order-status-and-lifecycle', [DocsController::class, 'ordersModuleStatus'])->name('status');

        Route::get('/visa-payment-and-employer-creation', [DocsController::class, 'ordersModuleVisaPayment'])->name('visa-payment');

        Route::get('/employer-and-candidate-assignment', [DocsController::class, 'ordersModuleEmployerAssignment'])->name('employer-assignment');

        Route::get('/order-receiver-panel', [DocsController::class, 'ordersModuleReceiverPanel'])->name('receiver-panel');

        Route::get('/requirement-snippets', [DocsController::class, 'ordersModuleRequirement'])->name('requirement');

        Route::get('/cancellation-and-notifications', [DocsController::class, 'ordersModuleCancellation'])->name('cancellation-notifications');

    });

    Route::get('/employer-module', [DocsController::class, 'employerModule'])->name('employer-module');

    Route::prefix('employer-module')->name('employer-module.')->group(function () {

        Route::get('/database-structure', [DocsController::class, 'employerModuleDatabase'])->name('database');

        Route::get('/employer-vs-employer-plus', [DocsController::class, 'employerModuleSplit'])->name('split');

        Route::get('/employer-creation-and-editing', [DocsController::class, 'employerModuleCreation'])->name('creation');

        Route::get('/visa-details-views', [DocsController::class, 'employerModuleVisaViews'])->name('visa-views');

        Route::get('/candidate-assignment', [DocsController::class, 'employerModuleAssignment'])->name('assignment');

        Route::get('/payment-status-and-invoicing', [DocsController::class, 'employerModulePayment'])->name('payment');

        Route::get('/work-agreement-pdf', [DocsController::class, 'employerModuleWorkAgreement'])->name('work-agreement');

        Route::get('/filters-permissions-and-routes', [DocsController::class, 'employerModuleFilters'])->name('filters');

    });

    Route::get('/testimonial-module', [DocsController::class, 'testimonialModule'])->name('testimonial-module');

    Route::get('/associate-module', [DocsController::class, 'associateModule'])->name('associate-module');

    Route::get('/associate-module/verification-and-linkage', [DocsController::class, 'associateModuleVerification'])->name('associate-module.verification');

    Route::get('/cron-jobs', [DocsController::class, 'cronJobs'])->name('cron-jobs');

    Route::get('/meta-api', [DocsController::class, 'metaApi'])->name('meta-api');

});