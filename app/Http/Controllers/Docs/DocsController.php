<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;

class DocsController extends Controller
{
    public function index()
    {
        return view('docs.index');
    }

    public function leadModule()
    {
        return view('docs.lead-module');
    }

    public function leadModuleDatabase()
    {
        return view('docs.lead-module.database-structure');
    }

    public function leadModuleCreation()
    {
        return view('docs.lead-module.lead-creation');
    }

    public function leadModuleDuplicate()
    {
        return view('docs.lead-module.duplicate-check');
    }

    public function leadModuleFollowup()
    {
        return view('docs.lead-module.follow-up');
    }

    public function leadModuleNotes()
    {
        return view('docs.lead-module.lead-notes');
    }

    public function leadModuleQualification()
    {
        return view('docs.lead-module.qualification');
    }

    public function leadModuleActivity()
    {
        return view('docs.lead-module.activity-logs');
    }

    public function leadModuleWhatsapp()
    {
        return view('docs.lead-module.whatsapp-integration');
    }

    public function leadAssignment()
    {
        return view('docs.lead-assignment');
    }

    public function candidateModule()
    {
        return view('docs.candidate-module');
    }

    public function candidateModuleDatabase()
    {
        return view('docs.candidate-module.database-structure');
    }

    public function candidateModuleCreation()
    {
        return view('docs.candidate-module.candidate-creation');
    }

    public function candidateModulePublish()
    {
        return view('docs.candidate-module.publish-wizard');
    }

    public function candidateModuleDocuments()
    {
        return view('docs.candidate-module.documents');
    }

    public function candidateModuleStatus()
    {
        return view('docs.candidate-module.status-pipeline');
    }

    public function candidateModuleTransactions()
    {
        return view('docs.candidate-module.transactions');
    }

    public function candidateModuleActivity()
    {
        return view('docs.candidate-module.activity-log');
    }

    public function candidateModuleLeadsDeals()
    {
        return view('docs.candidate-module.leads-and-deals');
    }

    public function contactsModule()
    {
        return view('docs.contacts-module');
    }

    public function contactsModuleDatabase()
    {
        return view('docs.contacts-module.database-structure');
    }

    public function contactsModuleCreation()
    {
        return view('docs.contacts-module.contact-creation');
    }

    public function contactsModuleGoogleSync()
    {
        return view('docs.contacts-module.google-contacts-sync');
    }

    public function contactsModuleGroups()
    {
        return view('docs.contacts-module.groups');
    }

    public function contactsModuleLeadSync()
    {
        return view('docs.contacts-module.lead-sync');
    }

    public function contactsModuleExport()
    {
        return view('docs.contacts-module.export-and-history');
    }

    public function contactsModuleFilters()
    {
        return view('docs.contacts-module.filters-and-saved-views');
    }

    public function contactsModuleLifecycle()
    {
        return view('docs.contacts-module.lifecycle-status');
    }

    public function taskModule()
    {
        return view('docs.task-module');
    }

    public function taskModuleDatabase()
    {
        return view('docs.task-module.database-structure');
    }

    public function taskModuleCreation()
    {
        return view('docs.task-module.task-creation-and-status');
    }

    public function taskModuleRecurring()
    {
        return view('docs.task-module.recurring-tasks');
    }

    public function taskModuleReminders()
    {
        return view('docs.task-module.reminder-delivery');
    }

    public function taskModuleSharedSink()
    {
        return view('docs.task-module.shared-reminder-sink');
    }

    public function taskModuleAssignment()
    {
        return view('docs.task-module.assignment-and-permissions');
    }

    public function taskModuleLabels()
    {
        return view('docs.task-module.labels-and-departments');
    }

    public function taskModuleFilters()
    {
        return view('docs.task-module.filters-and-saved-views');
    }

    public function dealPipelineModule()
    {
        return view('docs.deal-pipeline-module');
    }

    public function dealPipelineModuleDatabase()
    {
        return view('docs.deal-pipeline-module.database-structure');
    }

    public function dealPipelineModuleStages()
    {
        return view('docs.deal-pipeline-module.pipeline-stages-and-kanban');
    }

    public function dealPipelineModuleCreation()
    {
        return view('docs.deal-pipeline-module.deal-creation');
    }

    public function dealPipelineModuleSettings()
    {
        return view('docs.deal-pipeline-module.settings-lookups');
    }

    public function dealPipelineModuleNotesFiles()
    {
        return view('docs.deal-pipeline-module.notes-and-files');
    }

    public function dealPipelineModuleReminders()
    {
        return view('docs.deal-pipeline-module.reminders-and-cron-jobs');
    }

    public function dealPipelineModuleRelationships()
    {
        return view('docs.deal-pipeline-module.relationship-to-leads-and-candidates');
    }

    public function dealPipelineModuleFilters()
    {
        return view('docs.deal-pipeline-module.filters-and-saved-views');
    }

    public function ordersModule()
    {
        return view('docs.orders-module');
    }

    public function ordersModuleDatabase()
    {
        return view('docs.orders-module.database-structure');
    }

    public function ordersModuleCreation()
    {
        return view('docs.orders-module.order-creation');
    }

    public function ordersModuleStatus()
    {
        return view('docs.orders-module.order-status-and-lifecycle');
    }

    public function ordersModuleVisaPayment()
    {
        return view('docs.orders-module.visa-payment-and-employer-creation');
    }

    public function ordersModuleEmployerAssignment()
    {
        return view('docs.orders-module.employer-and-candidate-assignment');
    }

    public function ordersModuleReceiverPanel()
    {
        return view('docs.orders-module.order-receiver-panel');
    }

    public function ordersModuleRequirement()
    {
        return view('docs.orders-module.requirement-snippets');
    }

    public function ordersModuleCancellation()
    {
        return view('docs.orders-module.cancellation-and-notifications');
    }

    public function employerModule()
    {
        return view('docs.employer-module');
    }

    public function employerModuleDatabase()
    {
        return view('docs.employer-module.database-structure');
    }

    public function employerModuleSplit()
    {
        return view('docs.employer-module.employer-vs-employer-plus');
    }

    public function employerModuleCreation()
    {
        return view('docs.employer-module.employer-creation-and-editing');
    }

    public function employerModuleVisaViews()
    {
        return view('docs.employer-module.visa-details-views');
    }

    public function employerModuleAssignment()
    {
        return view('docs.employer-module.candidate-assignment');
    }

    public function employerModulePayment()
    {
        return view('docs.employer-module.payment-status-and-invoicing');
    }

    public function employerModuleWorkAgreement()
    {
        return view('docs.employer-module.work-agreement-pdf');
    }

    public function employerModuleFilters()
    {
        return view('docs.employer-module.filters-permissions-and-routes');
    }

    public function testimonialModule()
    {
        return view('docs.testimonial-module');
    }

    public function associateModule()
    {
        return view('docs.associate-module');
    }

    public function associateModuleVerification()
    {
        return view('docs.associate-module.verification-and-linkage');
    }

    public function cronJobs()
    {
        return view('docs.cron-jobs');
    }

    public function metaApi()
    {
        return view('docs.meta-api');
    }
}