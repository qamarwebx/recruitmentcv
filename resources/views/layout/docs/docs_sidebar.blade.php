<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- Logo -->
    <div class="app-brand demo">

        <a href="{{ route('docs.index') }}" class="app-brand-link">

            <span class="app-brand-text fw-bold fs-4">
                📚 QAMAR Docs
            </span>

        </a>

    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboard -->

        <li class="menu-item {{ request()->routeIs('docs.index') ? 'active' : '' }}">

            <a href="{{ route('docs.index') }}" class="menu-link">

                <i class="menu-icon tf-icons ti ti-home"></i>

                <div>Dashboard</div>

            </a>

        </li>


        <!-- ========================= -->
        <!-- Lead Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.lead-module') || request()->routeIs('docs.lead-module.*') || request()->routeIs('docs.lead-assignment') || request()->routeIs('docs.meta-api') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-users"></i>

                <div>Lead Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.lead-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.database') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.database') }}" class="menu-link">
                        <div>Database Structure</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.creation') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.creation') }}" class="menu-link">
                        <div>Lead Creation</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.duplicate') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.duplicate') }}" class="menu-link">
                        <div>Duplicate Check</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-assignment') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-assignment') }}" class="menu-link">
                        <div>Lead Assignment</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('docs.lead-assignment') }}#reassignment" class="menu-link">
                        <div>Lead Reassignment</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.followup') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.followup') }}" class="menu-link">
                        <div>Follow-up</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.notes') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.notes') }}" class="menu-link">
                        <div>Lead Notes</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.qualification') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.qualification') }}" class="menu-link">
                        <div>Qualification</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.activity') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.activity') }}" class="menu-link">
                        <div>Activity Logs</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.meta-api') ? 'active' : '' }}">
                    <a href="{{ route('docs.meta-api') }}" class="menu-link">
                        <div>Meta Conversion API</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.lead-module.whatsapp') ? 'active' : '' }}">
                    <a href="{{ route('docs.lead-module.whatsapp') }}" class="menu-link">
                        <div>WhatsApp Integration</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.cron-jobs') ? 'active' : '' }}">
                    <a href="{{ route('docs.cron-jobs') }}" class="menu-link">
                        <div>Cron Jobs</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Contacts Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.contacts-module') || request()->routeIs('docs.contacts-module.*') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-address-book"></i>

                <div>Contacts Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.contacts-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.database') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.database') }}" class="menu-link">
                        <div>Database Structure</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.creation') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.creation') }}" class="menu-link">
                        <div>Contact Creation</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.google-sync') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.google-sync') }}" class="menu-link">
                        <div>Google Contacts Sync</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.groups') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.groups') }}" class="menu-link">
                        <div>Groups</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.lead-sync') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.lead-sync') }}" class="menu-link">
                        <div>Lead Sync</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.export') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.export') }}" class="menu-link">
                        <div>Export &amp; History</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.filters') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.filters') }}" class="menu-link">
                        <div>Filters &amp; Saved Views</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.contacts-module.lifecycle') ? 'active' : '' }}">
                    <a href="{{ route('docs.contacts-module.lifecycle') }}" class="menu-link">
                        <div>Lifecycle Status</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Task Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.task-module') || request()->routeIs('docs.task-module.*') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-square-check"></i>

                <div>Task Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.task-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.database') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.database') }}" class="menu-link">
                        <div>Database Structure</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.creation') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.creation') }}" class="menu-link">
                        <div>Task Creation &amp; Status</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.recurring') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.recurring') }}" class="menu-link">
                        <div>Recurring Tasks</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.reminders') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.reminders') }}" class="menu-link">
                        <div>Reminder Delivery</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.shared-sink') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.shared-sink') }}" class="menu-link">
                        <div>Shared Reminder Sink</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.assignment') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.assignment') }}" class="menu-link">
                        <div>Assignment &amp; Permissions</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.labels') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.labels') }}" class="menu-link">
                        <div>Labels &amp; Departments</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.task-module.filters') ? 'active' : '' }}">
                    <a href="{{ route('docs.task-module.filters') }}" class="menu-link">
                        <div>Filters &amp; Saved Views</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Deal Pipeline Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module') || request()->routeIs('docs.deal-pipeline-module.*') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-chart-line"></i>

                <div>Deal Pipeline Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.database') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.database') }}" class="menu-link">
                        <div>Database Structure</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.stages') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.stages') }}" class="menu-link">
                        <div>Pipeline Stages &amp; Kanban</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.creation') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.creation') }}" class="menu-link">
                        <div>Deal Creation</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.settings') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.settings') }}" class="menu-link">
                        <div>Settings &amp; Lookups</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.notes-files') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.notes-files') }}" class="menu-link">
                        <div>Notes &amp; Files</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.reminders') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.reminders') }}" class="menu-link">
                        <div>Reminders &amp; Cron Jobs</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.relationships') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.relationships') }}" class="menu-link">
                        <div>Relationship to Leads &amp; Candidates</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.deal-pipeline-module.filters') ? 'active' : '' }}">
                    <a href="{{ route('docs.deal-pipeline-module.filters') }}" class="menu-link">
                        <div>Filters &amp; Saved Views</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Orders Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.orders-module') || request()->routeIs('docs.orders-module.*') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>

                <div>Orders Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.orders-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.database') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.database') }}" class="menu-link">
                        <div>Database Structure</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.creation') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.creation') }}" class="menu-link">
                        <div>Order Creation</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.status') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.status') }}" class="menu-link">
                        <div>Order Status &amp; Lifecycle</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.visa-payment') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.visa-payment') }}" class="menu-link">
                        <div>Visa, Payment &amp; Employer</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.employer-assignment') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.employer-assignment') }}" class="menu-link">
                        <div>Employer &amp; Candidate Assignment</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.receiver-panel') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.receiver-panel') }}" class="menu-link">
                        <div>Order Receiver Panel</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.requirement') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.requirement') }}" class="menu-link">
                        <div>Requirement Snippets</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.orders-module.cancellation-notifications') ? 'active' : '' }}">
                    <a href="{{ route('docs.orders-module.cancellation-notifications') }}" class="menu-link">
                        <div>Cancellation &amp; Notifications</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Employer Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.employer-module') || request()->routeIs('docs.employer-module.*') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-briefcase"></i>

                <div>Employer Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.employer-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.database') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.database') }}" class="menu-link">
                        <div>Database Structure</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.split') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.split') }}" class="menu-link">
                        <div>Employer vs. Employer Plus</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.creation') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.creation') }}" class="menu-link">
                        <div>Creation &amp; Editing</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.visa-views') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.visa-views') }}" class="menu-link">
                        <div>Visa Details Views</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.assignment') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.assignment') }}" class="menu-link">
                        <div>Candidate Assignment</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.payment') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.payment') }}" class="menu-link">
                        <div>Payment Status &amp; Invoicing</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.work-agreement') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.work-agreement') }}" class="menu-link">
                        <div>Work Agreement PDF</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.employer-module.filters') ? 'active' : '' }}">
                    <a href="{{ route('docs.employer-module.filters') }}" class="menu-link">
                        <div>Filters, Permissions &amp; Routes</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Testimonial Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.testimonial-module') ? 'active' : '' }}">
            <a href="{{ route('docs.testimonial-module') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-message"></i>
                <div>Testimonial Module</div>
            </a>
        </li>


        <!-- ========================= -->
        <!-- Associate Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.associate-module') || request()->routeIs('docs.associate-module.*') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-user-plus"></i>

                <div>Associate Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.associate-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.associate-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.associate-module.verification') ? 'active' : '' }}">
                    <a href="{{ route('docs.associate-module.verification') }}" class="menu-link">
                        <div>Verification &amp; Cross-Module Linkage</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Candidate Module -->
        <!-- ========================= -->

        <li class="menu-item {{ request()->routeIs('docs.candidate-module') || request()->routeIs('docs.candidate-module.*') ? 'open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-user"></i>

                <div>Candidate Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('docs.candidate-module') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module') }}" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.database') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.database') }}" class="menu-link">
                        <div>Database Structure</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.creation') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.creation') }}" class="menu-link">
                        <div>Candidate Creation</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.publish') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.publish') }}" class="menu-link">
                        <div>Publish Wizard</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.documents') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.documents') }}" class="menu-link">
                        <div>Documents</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.status') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.status') }}" class="menu-link">
                        <div>Status &amp; Deployment Pipeline</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.transactions') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.transactions') }}" class="menu-link">
                        <div>Transactions &amp; Finance</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.activity') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.activity') }}" class="menu-link">
                        <div>Activity Log</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('docs.candidate-module.leads-deals') ? 'active' : '' }}">
                    <a href="{{ route('docs.candidate-module.leads-deals') }}" class="menu-link">
                        <div>Relationship to Leads &amp; Deals</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Client Module -->
        <!-- ========================= -->

        <li class="menu-item">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-building"></i>

                <div>Client Module</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Finance -->
        <!-- ========================= -->

        <li class="menu-item">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-currency-rupee"></i>

                <div>Finance</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div>Overview</div>
                    </a>
                </li>

            </ul>

        </li>


        <!-- ========================= -->
        <!-- Settings -->
        <!-- ========================= -->

        <li class="menu-item">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons ti ti-settings"></i>

                <div>Settings</div>

            </a>

            <ul class="menu-sub">

                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div>General Settings</div>
                    </a>
                </li>

            </ul>

        </li>

    </ul>

</aside>