<script>
  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('.settings-menu-toggle');
    if (!toggle) return;

    var item = toggle.closest('.menu-item');
    if (!item) return;

    setTimeout(function () {
      if (item.classList.contains('open')) {
        $('.menu-inner').animate({
          scrollTop: $('.menu-inner')[0].scrollHeight
        }, 500);
      }
    }, 350);
  }, true);
</script>
@if (Auth::guard('admin')->user()->user_type == 1)
  <ul class="menu-inner py-1">
    <!-- Dashboards -->

    <li class="menu-item {{ (request()->routeIs('admin.dashboard')) ?'active':'' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.leads.list')) || (request()->routeIs('admin.leads.show')) ?'active':'' }}">
        <a href="{{ route('admin.leads.list') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-target"></i>
            <div>Leads</div>
        </a>
    </li>

    <li class="menu-item {{
        (request()->routeIs('admin.allcontact.list')) ||
        (request()->routeIs('admin.contact_export_history')) ||
        (request()->routeIs('admin.email_qamr_portal.allcontact.history')) ||
        (request()->routeIs('admin.allcontact.show')) ?'active':'' }}">
        <a href="{{ route('admin.allcontact.list') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-address-book"></i>
            <div>Contacts</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.todo.list')) ?'active':'' }}">
        <a href="{{ route('admin.todo.list') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-square-check"></i>
            <div>Task</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.dealPipeline.list')) ? 'active' : '' }}">
        <a href="{{ route('admin.dealPipeline.list') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-chart-line"></i>
            <!-- <i class="menu-icon tf-icons ti ti-intercom"></i> -->
            <div>Deal Pipeline</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.booking')) || (request()->routeIs('admin.booking.view'))  ?'active':'' }}">
        <a href="{{ route('admin.booking') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
            <div>Orders</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.employer')) || (request()->routeIs('admin.employer.visaDetshow')) ?'active':'' }}">
        <a href="{{ route('admin.employer') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-briefcase"></i>
            <div>Employer</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.employer.listp')) || (request()->routeIs('admin.employer.visaDetshowp')) ?'active':'' }}">
      <a href="{{ route('admin.employer.listp') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-building"></i>
          <div>Employer plus</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.candidate')) || (request()->routeIs('admin.candidate.show')) || (request()->routeIs('admin.candidate.publish')) ?'active':'' }}">
      <a href="{{ route('admin.candidate') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>Candidate</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.testimonial')) || (request()->routeIs('admin.testimonial.show')) || (request()->routeIs('admin.testimonial.publish')) || (request()->routeIs('admin.google_review')) ?'active':'' }}">
      <a href="{{ route('admin.testimonial') }}" class="menu-link">
      <i class="menu-icon tf-icons ti ti-quote"></i>
      <div>Testimonial</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.associate')) ||
        (request()->routeIs('admin.associate.show')) ?'active':'' }}">
      <a href="{{ route('admin.associate') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user-plus"></i>
        <div>Associate</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.client')) ?'active':'' }}">
      <a href="{{ route('admin.client') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-user"></i>
          <div>Customer</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.contact.list')) || (request()->routeIs('admin.contact.show')) || (request()->routeIs('admin.contact_plus_export_history')) || (request()->routeIs('admin.email_qamr_portal.history')) ?'active':'' }}">
      <a href="{{ route('admin.contact.list') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-id"></i>
        <div>Contact Plus</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.partner')) || (request()->routeIs('admin.partner.show')) ?'active':'' }}">
        <a href="{{ route('admin.partner') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-heart-handshake"></i>
          <div>Partner</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.file_manager.list')) ?'active':'' }}">
        <a href="{{ route('admin.file_manager.list') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-folder"></i>
            <div>File Manager</div>
        </a>
    </li>

    <!-- Staff -->

    <!-- Finance Management -->
    <li class="menu-item {{
        (request()->routeIs('admin.candidate.transactionList')) ||
        (request()->routeIs('admin.invoice.list')) ||
        (request()->routeIs('admin.invoice.show')) ||
        (request()->routeIs('admin.salary.dashboard')) ||
        (request()->routeIs('admin.attendance.list')) ||
        (request()->routeIs('admin.salary.index')) ||
        (request()->routeIs('admin.salary.settings.page')) ||
        (request()->routeIs('admin.payment.list')) ||
        (request()->routeIs('admin.expense.list')) ||
        (request()->routeIs('admin.fund_advance.*')) ? 'active open' : '' }}">

        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-currency-rupee"></i>
            <div>Finance</div>
        </a>

        <ul class="menu-sub">

            <!-- Expense -->
            <li class="menu-item {{ (request()->routeIs('admin.expense.list')) ? 'active' : '' }}">
                <a href="{{ route('admin.expense.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-receipt-2"></i>
                    <div>Expense</div>
                </a>
            </li>

            <!-- Candidate -->
            <li class="menu-item {{ (request()->routeIs('admin.candidate.transactionList')) ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <div>Candidate</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ (request()->routeIs('admin.candidate.transactionList')) ? 'active' : '' }}">
                        <a href="{{ route('admin.candidate.transactionList') }}" class="menu-link">
                            <div>Transaction</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Client -->
            <li class="menu-item {{
                (request()->routeIs('admin.invoice.list')) ||
                (request()->routeIs('admin.payment.list')) ||
                (request()->routeIs('admin.invoice.show')) ? 'active open' : ''
            }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <div>Client</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{
                        (request()->routeIs('admin.invoice.list')) ||
                        (request()->routeIs('admin.invoice.show')) ? 'active' : ''
                    }}">
                        <a href="{{ route('admin.invoice.list') }}" class="menu-link">
                            <div>Sale Invoices</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.payment.list')) ? 'active' : '' }}">
                        <a href="{{ route('admin.payment.list') }}" class="menu-link">
                            <div>Payments</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- HR Management -->
            <li class="menu-item {{
                (request()->routeIs('admin.salary.dashboard')) ||
                (request()->routeIs('admin.attendance.list')) ||
                (request()->routeIs('admin.salary.index')) ||
                (request()->routeIs('admin.salary.settings.page')) ? 'active open' : ''
            }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-id-badge-2"></i>
                    <div>HR Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ (request()->routeIs('admin.salary.dashboard')) ? 'active' : '' }}">
                        <a href="{{ route('admin.salary.dashboard') }}" class="menu-link">
                            <div>Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.attendance.list')) ? 'active' : '' }}">
                        <a href="{{ route('admin.attendance.list') }}" class="menu-link">
                            <div>Attendance</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.salary.index')) ? 'active' : '' }}">
                        <a href="{{ route('admin.salary.index') }}" class="menu-link">
                            <div>Payroll</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.salary.settings.page')) ? 'active' : '' }}">
                        <a href="{{ route('admin.salary.settings.page') }}" class="menu-link">
                            <div>Settings</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Fund & Advance Management -->
            <li class="menu-item {{
                (request()->routeIs('admin.fund_advance.dashboard')) ||
                (request()->routeIs('admin.fund_advance.transactions.list')) ||
                (request()->routeIs('admin.fund_advance.transactions.view')) ||
                (request()->routeIs('admin.fund_advance.settlements.pending')) ||
                (request()->routeIs('admin.fund_advance.settlements.history')) ||
                (request()->routeIs('admin.fund_advance.ledgers.party')) ||
                (request()->routeIs('admin.fund_advance.ledgers.employee')) ||
                (request()->routeIs('admin.fund_advance.ledgers.fund')) ||
                (request()->routeIs('admin.fund_advance.reports.outstanding')) ||
                (request()->routeIs('admin.fund_advance.reports.advances')) ||
                (request()->routeIs('admin.fund_advance.reports.loans')) ||
                (request()->routeIs('admin.fund_advance.reports.funds')) ||
                (request()->routeIs('admin.fund_advance.reports.settlements')) ||
                (request()->routeIs('admin.fund_advance.reports.transactions')) ? 'active open' : ''
            }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-report-money"></i>
                    <div>Fund & Advance Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ (request()->routeIs('admin.fund_advance.dashboard')) ? 'active' : '' }}">
                        <a href="{{ route('admin.fund_advance.dashboard') }}" class="menu-link">
                            <div>Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.fund_advance.transactions.list')) || (request()->routeIs('admin.fund_advance.transactions.view')) ? 'active' : '' }}">
                        <a href="{{ route('admin.fund_advance.transactions.list') }}" class="menu-link">
                            <div>Transactions</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.pending')) || (request()->routeIs('admin.fund_advance.settlements.history')) ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div>Settlements</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.pending')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.settlements.pending') }}" class="menu-link"><div>Pending Settlements</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.history')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.settlements.history') }}" class="menu-link"><div>Settlement History</div></a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item {{
                        (request()->routeIs('admin.fund_advance.ledgers.party')) ||
                        (request()->routeIs('admin.fund_advance.ledgers.employee')) ||
                        (request()->routeIs('admin.fund_advance.ledgers.fund')) ? 'active open' : ''
                    }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div>Ledgers</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.party')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.ledgers.party') }}" class="menu-link"><div>Party Ledger</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.employee')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.ledgers.employee') }}" class="menu-link"><div>Employee Ledger</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.fund')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.ledgers.fund') }}" class="menu-link"><div>Fund Ledger</div></a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item {{
                        (request()->routeIs('admin.fund_advance.reports.outstanding')) ||
                        (request()->routeIs('admin.fund_advance.reports.advances')) ||
                        (request()->routeIs('admin.fund_advance.reports.loans')) ||
                        (request()->routeIs('admin.fund_advance.reports.funds')) ||
                        (request()->routeIs('admin.fund_advance.reports.settlements')) ||
                        (request()->routeIs('admin.fund_advance.reports.transactions')) ? 'active open' : ''
                    }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div>Reports</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.outstanding')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.reports.outstanding') }}" class="menu-link"><div>Outstanding</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.advances')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.reports.advances') }}" class="menu-link"><div>Advances</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.loans')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.reports.loans') }}" class="menu-link"><div>Loans</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.funds')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.reports.funds') }}" class="menu-link"><div>Funds</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.settlements')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.reports.settlements') }}" class="menu-link"><div>Settlements</div></a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.transactions')) ? 'active' : '' }}">
                                <a href="{{ route('admin.fund_advance.reports.transactions') }}" class="menu-link"><div>Transactions</div></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

        </ul>
    </li>

    <!-- Whatsapp Business -->
    <li class="menu-item {{
        (request()->routeIs('admin.whatsapp.campaign')) ||
        (request()->routeIs('admin.whatsapp.templateList')) ||
        (request()->routeIs('admin.whatsapp.api')) ||
        (request()->routeIs('admin.whatsapp.campaign.report')) ?'active open':'' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-brand-whatsapp"></i>
            <div>Whatsapp Business</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item {{
                (request()->routeIs('admin.whatsapp.campaign')) ||
                (request()->routeIs('admin.whatsapp.campaign.report')) ?'active':'' }}">
                <a href="{{ route('admin.whatsapp.campaign') }}" class="menu-link"><div>Campaign</div></a>
            </li>
            <li class="menu-item {{ (request()->routeIs('admin.whatsapp.templateList')) ?'active':'' }}">
                <a href="{{ route('admin.whatsapp.templateList') }}" class="menu-link"><div>Template</div></a>
            </li>
            <li class="menu-item {{ (request()->routeIs('admin.whatsapp.api')) ?'active':'' }}">
                <a href="{{ route('admin.whatsapp.api') }}" class="menu-link"><div>API Setup</div></a>
            </li>
        </ul>
    </li>

    <!-- Meta Whatsapp -->
    <li class="menu-item {{
        (request()->routeIs('admin.metawhatsapp.campaign')) ||
        (request()->routeIs('admin.metawhatsapp.campaignShow')) ||
        (request()->routeIs('admin.whatsapp.metatemplateList')) ||
        (request()->routeIs('admin.imagehost.list')) ||
        (request()->routeIs('admin.metawhatsapp.api')) ||
        (request()->routeIs('admin.whatsapp.chatredirecturllist')) ||
        (request()->is('admin/meta-automation/*')) ||
        (request()->is('admin/team-member-page/*')) ||
        (request()->is('admin/dynamic-image-url/*')) ||
        (request()->routeIs('admin.autometanotification.list')) ?'active open':'' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons ti ti-brand-meta"></i>
          <div>Whatsapp Meta</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item {{ (request()->routeIs('admin.metawhatsapp.campaign')) || (request()->routeIs('admin.metawhatsapp.campaignShow')) ?'active':'' }}">
                <a href="{{ route('admin.metawhatsapp.campaign') }}" class="menu-link">
                <div>Campaign</div>
                </a>
            </li>
            <li class="menu-item {{ (request()->routeIs('admin.whatsapp.metatemplateList')) ?'active':'' }}">
                <a href="{{ route('admin.whatsapp.metatemplateList') }}" class="menu-link"> <div>Template</div></a>
            </li>
            <!-- <li class="menu-item {{ (request()->routeIs('admin.autometanotification.list')) ?'active':'' }}">
                <a href="{{ route('admin.autometanotification.list') }}" class="menu-link">
                    <div>Emp Meta Automation</div>
                </a>
            </li> -->
            <li class="menu-item {{ (request()->is('admin/meta-automation/*')) ?'active':'' }}">
                <a href="{{ route('admin.metaautomation.list') }}" class="menu-link">
                    <div>Meta Automation</div>
                </a>
            </li>
            <li class="menu-item {{ (request()->routeIs('admin.metawhatsapp.api')) ?'active':'' }}">
                <a href="{{ route('admin.metawhatsapp.api') }}" class="menu-link"> <div>Meta API Setup</div></a>
            </li>
            <li class="menu-item {{ (request()->routeIs('admin.imagehost.list')) ?'active':'' }}">
                <a href="{{ route('admin.imagehost.list') }}" class="menu-link">Image Host</a>
            </li>

            <li class="menu-item {{ (request()->routeIs('admin.whatsapp.chatredirecturllist')) ?'active':'' }}">
                <a href="{{ route('admin.whatsapp.chatredirecturllist') }}" class="menu-link">Whatsapp URL</a>
            </li>

            <li class="menu-item {{ (request()->routeIs('admin.team-member-page.list')) ? 'active':'' }}">
                <a href="{{ route('admin.team-member-page.list') }}" class="menu-link">Teammember Pages</a>
            </li>

            <li class="menu-item {{ (request()->routeIs('admin.dynamic-image-url.list')) ? 'active':'' }}">
                <a href="{{ route('admin.dynamic-image-url.list') }}" class="menu-link">Image URL</a>
            </li>
        </ul>
    </li> 
    
    <!-- sms Campaign -->
    <li class="menu-item {{
        (request()->routeIs('admin.sms.api')) ||
        (request()->routeIs('admin.sms.smstemplateList')) ||       
        (request()->routeIs('admin.smsCampaign.campaignShow')) ||       
        (request()->routeIs('admin.smsCampaign.list')) ?'active open':'' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-message"></i>
            <div>SMS Campaign</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item {{ (request()->routeIs('admin.smsCampaign.list')) ||  (request()->routeIs('admin.smsCampaign.campaignShow')) || (request()->routeIs('admin.smsCampaign.getsendList')) ?'active':'' }}">
                <a href="{{ route('admin.smsCampaign.list') }}" class="menu-link">
                <div>Campaign</div>
                </a>
            </li>
            <li class="menu-item {{ (request()->routeIs('admin.sms.smstemplateList')) ?'active':'' }}">
                <a href="{{ route('admin.sms.smstemplateList') }}" class="menu-link"> <div>Template</div></a>
            </li>
            <li class="menu-item {{ (request()->routeIs('admin.sms.api')) ?'active':'' }}">
                <a href="{{ route('admin.sms.api') }}" class="menu-link"> <div>SMS API Setup</div></a>
            </li>

        </ul>
    </li>

    <!-- Email Campaign -->
    <li class="menu-item {{
        (request()->is('admin/email-automation/*')) ||
        (request()->routeIs('admin.email.smtp')) ||
        (request()->routeIs('admin.email.templateList')) ||
        (request()->routeIs('admin.emailCampaign.list')) ||
        (request()->routeIs('admin.emailCampaign.campaignShow')) ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-mail"></i>
            <div>Email Campaign</div>
        </a>

        <ul class="menu-sub">
            <!-- Campaign -->
            <li class="menu-item {{ 
                (request()->routeIs('admin.emailCampaign.list')) || 
                (request()->routeIs('admin.emailCampaign.campaignShow')) ? 'active' : '' 
            }}">
                <a href="{{ route('admin.emailCampaign.list') }}" class="menu-link">
                    <div>Campaign</div>
                </a>
            </li>

            <!-- Template -->
            <li class="menu-item {{ (request()->routeIs('admin.email.templateList')) ? 'active' : '' }}">
                <a href="{{ route('admin.email.templateList') }}" class="menu-link">
                    <div>Template</div>
                </a>
            </li>

            <!-- Email Automation -->
            <li class="menu-item {{ (request()->is('admin/email-automation/*')) ?'active':'' }}">
                <a href="{{ route('admin.emailautomation.list') }}" class="menu-link">
                    <div>Email Automation</div>
                </a>
            </li>

            <!-- SMTP Setup -->
            <li class="menu-item {{ (request()->routeIs('admin.email.smtp')) ? 'active' : '' }}">
                <a href="{{ route('admin.email.smtp') }}" class="menu-link">
                    <div>SMTP Setup</div>
                </a>
            </li>
        </ul>
    </li>

    <!-- Layouts -->
    <li class="menu-item {{

      (request()->routeIs('admin.settings.file_manager.index')) ||
      (request()->routeIs('admin.settings.storage_usage.index')) ||
      (request()->routeIs('admin.settings.db_backup.index')) ||
      (request()->routeIs('admin.webconfig')) ||
      (request()->routeIs('admin.mail.setup.index')) ||
      (request()->routeIs('admin.permission')) ||
      (request()->routeIs('admin.personaliseclass')) ||
      (request()->routeIs('admin.template.index')) ||
      (request()->routeIs('admin.order.received.panel')) ||
      (request()->routeIs('admin.cv.setting')) ||
      (request()->routeIs('admin.customer.costlist')) ||
      (request()->routeIs('admin.booking.requirement')) ||
      (request()->routeIs('admin.frontwebsiteconfig')) ||
      (request()->routeIs('admin.metanotification.list')) ||
      (request()->routeIs('admin.ip-tracker')) ||

      (request()->routeIs('admin.staticmetanotification.list')) ||
      (request()->routeIs('admin.todolabel.list')) ||
      (request()->routeIs('admin.department.list')) ||
      (request()->routeIs('admin.contact.unsubscribereport')) ||
      (request()->routeIs('admin.businesstype.list')) ||
      (request()->routeIs('admin.groupm.list')) ||
      (request()->routeIs('admin.orderStatus')) ||
      (request()->routeIs('admin.branch')) ||
      (request()->routeIs('admin.staff')) ||
      (request()->routeIs('admin.staff.view')) ||
      (request()->routeIs('admin.profession')) ||
      (request()->routeIs('admin.placeofissue')) ||
      (request()->routeIs('admin.country')) ||
      (request()->routeIs('admin.region')) ||
      (request()->routeIs('admin.city')) ||
      (request()->routeIs('admin.carknown.list')) ||
      (request()->routeIs('admin.education')) ||
      (request()->routeIs('admin.religion')) ||
      (request()->routeIs('admin.source-management')) ||
      (request()->routeIs('admin.expworklocation')) ||
      (request()->routeIs('admin.lifecycle.list')) ||
      (request()->routeIs('admin.leadstage.list')) ||
      (request()->routeIs('admin.mailtest')) ||
      (request()->routeIs('admin.industries.list')) ||
      (request()->routeIs('admin.flight.vendor_list')) ||
      (request()->routeIs('admin.staff')) ||
      (request()->routeIs('admin.allcontactgroup.list')) ||
      (request()->routeIs('admin.account_details.list')) ||
      (request()->routeIs('admin.expense.category.list')) ||
      (request()->routeIs('admin.expense.expensefor.list')) ||
      (request()->routeIs('admin.device.index')) ||
      (request()->routeIs('admin.unauthorizedlogin.index')) ||
      (request()->routeIs('admin.business.list')) ||
      (request()->routeIs('admin.dealStage.list')) ||
      (request()->routeIs('admin.recruitStatus.list')) ||
      (request()->routeIs('admin.frontwebsiteconfig')) ||
      (request()->routeIs('admin.webconfig')) ||
      (request()->routeIs('admin.facebook.accounts')) ||
      (request()->routeIs('admin.domains')) ||
      (request()->routeIs('admin.allcontact.sync-with-google')) ||
      (request()->routeIs('admin.leads.auto_assign.change')) ||
        (request()->routeIs('admin.jobTitle.list'))
       ? 'active open':'' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle settings-menu-toggle">
        <i class="menu-icon tf-icons ti ti-settings"></i>
        <div>Settings</div>
      </a>

      <ul class="menu-sub">

      <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.staff.view')) ||  (request()->routeIs('admin.domains')) || (request()->routeIs('admin.permission')) || (request()->routeIs('admin.device.index')) ||
      (request()->routeIs('admin.unauthorizedlogin.index')) ? 'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div>Access</div>
            </a>
            <ul class="menu-sub">


                <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.staff.view')) ?'active':'' }}">
                    <a href="{{ route('admin.staff') }}" class="menu-link">
                        <div>Team Member</div>
                    </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.permission')) ?'active':'' }}">
                    <a href="{{ route('admin.permission') }}" class="menu-link">
                        <div>Permission</div>
                    </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.device.index')) ?'active':'' }}">
                    <a href="{{ route('admin.device.index') }}" class="menu-link">
                        <div>Allowed IP</div>
                    </a>
                </li>

            </ul>
        </li>



        <li class="menu-item {{
            (request()->routeIs('admin.leads.auto_assign.change')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div>Leads</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="" class="menu-link">
                        <div>Facebook Lead</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="" class="menu-link">
                        <div>Google Lead</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.leads.auto_assign.change')) ?'active':'' }}">
                    <a href="{{ route('admin.leads.auto_assign.change') }}" class="menu-link">
                        <div>Lead Assign</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.todolabel.list')) ||
            (request()->routeIs('admin.department.list')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Todo">Todo</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.todolabel.list')) ?'active':'' }}">
                    <a href="{{ route('admin.todolabel.list') }}" class="menu-link">
                        <div data-i18n="Todo Label">Todo Label</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.department.list')) ?'active':'' }}">
                    <a href="{{ route('admin.department.list') }}" class="menu-link">
                        <div data-i18n="Department">Department</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{
            request()->routeIs('admin.business.list') ||
            request()->routeIs('admin.dealStage.list') ||
            request()->routeIs('admin.recruitStatus.list') ||
            request()->routeIs('admin.jobTitle.list')
             ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Deal Pipeline">Deal Pipeline</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.business.list') ? 'active' : '' }}">
                    <a href="{{ route('admin.business.list') }}" class="menu-link">
                        <div data-i18n="Business Type">Business Type</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.dealStage.list') ? 'active' : '' }}">
                    <a href="{{ route('admin.dealStage.list') }}" class="menu-link">
                        <div data-i18n="Deal Stage">Deal Stage</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.recruitStatus.list') ? 'active' : '' }}">
                    <a href="{{ route('admin.recruitStatus.list') }}" class="menu-link">
                        <div data-i18n="Recruit Status">Recruit Status</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.jobTitle.list') ? 'active' : '' }}">
                    <a href="{{ route('admin.jobTitle.list') }}" class="menu-link">
                        <div data-i18n="Job Title">Job Title</div>
                    </a>
                </li>
            </ul>
        </li>


        <li class="menu-item {{
            (request()->routeIs('admin.contact.unsubscribereport')) ||
            (request()->routeIs('admin.businesstype.list')) ||
            (request()->routeIs('admin.groupm.list')) ||
            (request()->routeIs('admin.lifecycle.list')) ||
            (request()->routeIs('admin.leadstage.list')) ||
            (request()->routeIs('admin.contact_plus_export_history')) ||
            (request()->routeIs('admin.email_qamr_portal.history')) ||
            (request()->routeIs('admin.industries.list')) ?'active open':'' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div>Contact Plus</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.contact.unsubscribereport')) ?'active':'' }}">
                    <a href="{{ route('admin.contact.unsubscribereport') }}" class="menu-link">
                        <div data-i18n="Unsubscribe Report">Unsubscribe Report</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.industries.list')) ?'active':'' }}">
                    <a href="{{ route('admin.industries.list') }}" class="menu-link">
                        <div>Industries</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.businesstype.list')) ?'active':'' }}">
                    <a href="{{ route('admin.businesstype.list') }}" class="menu-link">
                      <div>Business Type</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.groupm.list')) ?'active':'' }}">
                    <a href="{{ route('admin.groupm.list') }}" class="menu-link">
                      <div>Group</div>
                    </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.lifecycle.list')) ?'active':'' }}">
                    <a href="{{ route('admin.lifecycle.list') }}" class="menu-link">
                        <div>Lifecycle Status</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.leadstage.list')) ?'active':'' }}">
                    <a href="{{ route('admin.leadstage.list') }}" class="menu-link">
                        <div>Lead Stage</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.allcontact.sync-with-google')) || (request()->routeIs('admin.allcontactgroup.list')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Contacts</div></a>
            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.allcontactgroup.list')) ?'active':'' }}">
                    <a href="{{ route('admin.allcontactgroup.list') }}" class="menu-link"><div>Group</div></a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.allcontact.sync-with-google')) ?'active':'' }}">
                    <a href="{{ route('admin.allcontact.sync-with-google') }}" class="menu-link"><div>Google Contact</div></a>
                </li>
            </ul>
        </li>

         <li class="menu-item {{ (request()->routeIs('admin.settings.file_manager.index')) ?'active':'' }}">
                <a href="{{ route('admin.settings.file_manager.index') }}" class="menu-link">
                    <div>File Manager</div>
                </a>
        </li>

         <li class="menu-item {{ (request()->routeIs('admin.settings.storage_usage.index')) ?'active':'' }}">
                <a href="{{ route('admin.settings.storage_usage.index') }}" class="menu-link">
                    <div>Storage Usage</div>
                </a>
        </li>

         <li class="menu-item {{ (request()->routeIs('admin.settings.db_backup.index')) ?'active':'' }}">
                <a href="{{ route('admin.settings.db_backup.index') }}" class="menu-link">
                    <div>DB Backup</div>
                </a>
        </li>


        <li class="menu-item {{
            (request()->routeIs('admin.flight.vendor_list')) ||
            (request()->routeIs('admin.account_details.list')) ||
            (request()->routeIs('admin.expense.category.list')) ||
            (request()->routeIs('admin.expense.expensefor.list')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div>Finance</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.flight.vendor_list')) ?'active':'' }}">
                    <a href="{{ route('admin.flight.vendor_list') }}" class="menu-link"><div>Flight Vendor</div></a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.account_details.list')) ?'active':'' }}">
                    <a href="{{ route('admin.account_details.list') }}" class="menu-link"><div>Account Details</div></a>
                </li>
                <li class="menu-item">
                    <a href="" class="menu-link">
                        <div>Received In</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.expense.category.list')) ?'active':'' }}">
                    <a href="{{ route('admin.expense.category.list') }}" class="menu-link"><div>Expense Category</div></a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.expense.expensefor.list')) ?'active':'' }}">
                    <a href="{{ route('admin.expense.expensefor.list') }}" class="menu-link"><div>Expense For</div></a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.orderStatus')) ||
            (request()->routeIs('admin.branch')) ||

            (request()->routeIs('admin.profession')) ||
            (request()->routeIs('admin.placeofissue')) ||
            (request()->routeIs('admin.country')) ||
            (request()->routeIs('admin.region')) ||
            (request()->routeIs('admin.city')) ||
            (request()->routeIs('admin.carknown.list')) ||
            (request()->routeIs('admin.education')) ||
            (request()->routeIs('admin.religion')) ||
            (request()->routeIs('admin.source-management')) ||
            (request()->routeIs('admin.expworklocation')) ? 'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons ti ti-adjustments"></i>
              <div>Dynamic</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item {{ (request()->routeIs('admin.branch')) ? 'active':'' }}">
                <a href="{{ route('admin.branch') }}" class="menu-link">
                  <div>Branch</div>
                </a>
              </li>

              <li class="menu-item {{ (request()->routeIs('admin.orderStatus')) ? 'active':'' }}">
                <a href="{{ route('admin.orderStatus') }}" class="menu-link">
                  <div>Order Status</div>
                </a>
              </li>

              <li class="menu-item {{ (request()->routeIs('admin.profession')) ?'active':'' }}">
                <a href="{{ route('admin.profession') }}" class="menu-link">
                  <div>Profession</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.placeofissue')) ?'active':'' }}">
                <a href="{{ route('admin.placeofissue') }}" class="menu-link">
                  <div>Place of Issue</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.expworklocation')) ?'active':'' }}">
                <a href="{{ route('admin.expworklocation') }}" class="menu-link">
                  <div>Expected Work Location</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.country')) ?'active':'' }}">
                <a href="{{ route('admin.country') }}" class="menu-link">
                  <div>Country</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.region')) ?'active':'' }}">
                <a href="{{ route('admin.region') }}" class="menu-link">
                  <div>Region</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.city')) ?'active':'' }}">
                <a href="{{ route('admin.city') }}" class="menu-link">
                  <div>City</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.carknown.list')) ?'active':'' }}">
                <a href="{{ route('admin.carknown.list') }}" class="menu-link">
                  <div>Car Known</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.education')) ?'active':'' }}">
                <a href="{{ route('admin.education') }}" class="menu-link">
                  <div>Education</div>
                </a>
              </li>
              <li class="menu-item {{ (request()->routeIs('admin.religion')) ?'active':'' }}">
                <a href="{{ route('admin.religion') }}" class="menu-link">
                  <div>Religion</div>
                </a>
              </li>

              <li class="menu-item {{ (request()->routeIs('admin.source-management')) ?'active':'' }}">
                <a href="{{ route('admin.source-management') }}" class="menu-link">
                  <div>Source</div>
                </a>
              </li>

            </ul>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.webconfig')) ||
            (request()->routeIs('admin.frontwebsiteconfig')) ||
            (request()->routeIs('admin.facebook.accounts')) ||
            (request()->routeIs('admin.ip-tracker')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div>Website</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.frontwebsiteconfig')) ?'active':'' }}">
                    <a href="{{ route('admin.frontwebsiteconfig') }}" class="menu-link">
                      <div>Front End Qamarhire</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.webconfig')) ?'active':'' }}">
                    <a href="{{ route('admin.webconfig') }}" class="menu-link">
                      <div>Qamarhire Configuration</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.facebook.accounts') ? 'active' : '' }}">
                    <a href="{{ route('admin.facebook.accounts') }}" class="menu-link">
                        <div>QamarJob FB Meta Configuration</div>
                    </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.ip-tracker')) ?'active':'' }}">
                    <a href="{{ route('admin.ip-tracker') }}" class="menu-link">
                        <div>IP Tracker</div>
                    </a>
                </li>
               
            </ul>
        </li>







        {{-- <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
          <a href="{{ route('admin.mail.setup.index') }}" class="menu-link">
            <div>Mail Setup</div>
          </a>
        </li> --}}



        <li class="menu-item {{ (request()->routeIs('admin.personaliseclass')) ?'active':'' }}">
          <a href="{{ route('admin.personaliseclass') }}" class="menu-link">
            <div>Personalise Class</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.template.index')) ?'active':'' }}">
          <a href="{{ route('admin.template.index') }}" class="menu-link">
            <div>Notifications</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.metanotification.list')) ?'active':'' }}">
          <a href="{{ route('admin.metanotification.list') }}" class="menu-link">
            <div>Emp Meta Automation</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.metaautomation.list')) ?'active':'' }}">
          <a href="{{ route('admin.metaautomation.list') }}" class="menu-link">
            <div>Meta Automation</div>
          </a>
        </li>

        {{-- <li class="menu-item {{ (request()->routeIs('admin.staticmetanotification.list')) ?'active':'' }}">
          <a href="{{ route('admin.staticmetanotification.list') }}" class="menu-link">
            <div>Static Meta OTP</div>
          </a>
        </li> --}}

        {{-- <li class="menu-item {{ (request()->routeIs('admin.order.received.panel')) ?'active':'' }}">
          <a href="{{ route('admin.order.received.panel') }}" class="menu-link">
            <div>Order Receiver Panel</div>
          </a>
        </li> --}}



        <li class="menu-item {{ (request()->routeIs('admin.cv.setting')) ?'active':'' }}">
          <a href="{{ route('admin.cv.setting') }}" class="menu-link">
            <div>CV Setting</div>
          </a>
        </li>

        {{-- <li class="menu-item {{ (request()->routeIs('admin.whatsapp.api')) ?'active':'' }}">
          <a href="{{ route('admin.whatsapp.api') }}" class="menu-link">
            <div>Whatsapp API</div>
          </a>
        </li> --}}

        {{-- <li class="menu-item {{ (request()->routeIs('admin.metawhatsapp.api')) ?'active':'' }}">
          <a href="{{ route('admin.metawhatsapp.api') }}" class="menu-link">
            <div>Whatsapp API+</div>
          </a>
        </li> --}}

        <li class="menu-item {{ (request()->routeIs('admin.customer.costlist')) ?'active':'' }}">
          <a href="{{ route('admin.customer.costlist') }}" class="menu-link">
            <div>Customer Cost</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.booking.requirement')) ?'active':'' }}">
          <a href="{{ route('admin.booking.requirement') }}" class="menu-link">
            <div>Requirement</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.mailtest')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons ti ti-flask"></i>
              <div>Testing</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item {{ (request()->routeIs('admin.mailtest')) ?'active':'' }}">
                <a href="{{ route('admin.mailtest') }}" class="menu-link">
                  <div>Mail</div>
                </a>
              </li>
            </ul>

        </li>

        <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active open':''}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div>Setup</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
                    <a href="{{ route('admin.mail.setup.index') }}" class="menu-link"><div>Mail Setup</div></a>
                </li>
                <li class="menu-item">
                    <a href="" class="menu-link">
                        <div>Facebool API Lead</div>
                    </a>
                </li>
            </ul>
        </li>

      </ul>
    </li>

  </ul>
@else
  @php
    $permission = DB::table('adminpermissions')->where('staff_id','=',Auth::guard('admin')->user()->id)->first();
  @endphp
  @if (isset($permission) && $permission->full_access == 1)
    <ul class="menu-inner py-1">
        <!-- Dashboards -->

        <li class="menu-item {{ (request()->routeIs('admin.dashboard')) ?'active':'' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.leads.list')) || (request()->routeIs('admin.leads.show')) ?'active':'' }}">
            <a href="{{ route('admin.leads.list') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-target"></i>
                <div>Leads</div>
            </a>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.allcontact.list')) ||
            (request()->routeIs('admin.contact_export_history')) ||
            (request()->routeIs('admin.email_qamr_portal.allcontact.history')) ||
            (request()->routeIs('admin.allcontact.show')) ?'active':'' }}">
            <a href="{{ route('admin.allcontact.list') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-address-book"></i>
                <div>Contacts</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.todo.list')) ?'active':'' }}">
            <a href="{{ route('admin.todo.list') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-square-check"></i>
                <div>Task</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.dealPipeline.list')) ? 'active' : '' }}">
            <a href="{{ route('admin.dealPipeline.list') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-chart-line"></i>
                <div>Deal Pipeline</div>
            </a>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.booking')) ||
            (request()->routeIs('admin.booking.view'))  ?'active':'' }}">
            <a href="{{ route('admin.booking') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                <div>Orders</div>
            </a>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.employer')) ||
            (request()->routeIs('admin.employer.visaDetshow')) ?'active':'' }}">
            <a href="{{ route('admin.employer') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-briefcase"></i>
                <div>Employer</div>
            </a>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.employer.listp')) ||
            (request()->routeIs('admin.employer.visaDetshowp')) ?'active':'' }}">
            <a href="{{ route('admin.employer.listp') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building"></i>
                <div>Employer plus</div>
            </a>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.candidate')) ||
            (request()->routeIs('admin.candidate.show')) ||
            (request()->routeIs('admin.candidate.publish')) ?'active':'' }}">
            <a href="{{ route('admin.candidate') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div>Candidate</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.testimonial')) || (request()->routeIs('admin.testimonial.show')) || (request()->routeIs('admin.testimonial.publish')) ?'active':'' }}">
            <a href="{{ route('admin.testimonial') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-quote"></i>
            <div>Testimonial</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.associate')) ||
            (request()->routeIs('admin.associate.show')) ?'active':'' }}">
            <a href="{{ route('admin.associate') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user-plus"></i>
                <div>Associate</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.attendance.list')) ?'active':'' }}">
            <a href="{{ route('admin.attendance.list') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-calendar-stats"></i>
                <div>Attendance</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.client')) ?'active':'' }}">
            <a href="{{ route('admin.client') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div>Customer</div>
            </a>
        </li>

        <li class="menu-item {{
            (request()->routeIs('admin.contact.list')) ||
            (request()->routeIs('admin.contact_plus_export_history')) ||
            (request()->routeIs('admin.email_qamr_portal.history')) ||
            (request()->routeIs('admin.contact.show')) ?'active':'' }}">
            <a href="{{ route('admin.contact.list') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-id"></i>
                <div>Contact Plus</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.partner')) || (request()->routeIs('admin.partner.show')) ?'active':'' }}">
            <a href="{{ route('admin.partner') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-heart-handshake"></i>
                <div>Partner</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.file_manager.list')) ?'active':'' }}">
            <a href="{{ route('admin.file_manager.list') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-folder"></i>
                <div>File Manager</div>
            </a>
        </li>


        <!-- Finance Management -->
        <li class="menu-item {{
            (request()->routeIs('admin.candidate.transactionList')) ||
        (request()->routeIs('admin.salary.dashboard')) ||
        (request()->routeIs('admin.attendance.list')) ||
        (request()->routeIs('admin.salary.settings.page')) ||
        (request()->routeIs('admin.fund_advance.*'))  ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-currency-rupee"></i>
                <div>Finance</div>
            </a>

            <ul class="menu-sub">

                <!-- Expense -->
                <li class="menu-item {{ (request()->routeIs('admin.expense.list')) ? 'active' : '' }}">
                    <a href="{{ route('admin.expense.list') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-receipt-2"></i>
                        <div>Expense</div>
                    </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.candidate.transactionList')) ?'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Candidate</div></a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->routeIs('admin.candidate.transactionList')) ?'active':'' }}">
                            <a href="{{ route('admin.candidate.transactionList') }}" class="menu-link">
                                <div>Transaction</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">Client</a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="" class="menu-link">
                                <div>Sale Invoices</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="" class="menu-link">
                                <div>Payments</div>
                            </a>
                        </li>
                    </ul>
                </li>
                 <li class="menu-item {{ (request()->routeIs('admin.salary.dashboard')) || (request()->routeIs('admin.attendance.list')) || (request()->routeIs('admin.salary.settings.page')) ?'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons ti ti-id-badge-2"></i>
                        <div>HR Management</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->routeIs('admin.salary.dashboard')) ?'active':'' }}">
                            <a href="{{ route('admin.salary.dashboard') }}" class="menu-link">
                                <div>Dashboard</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.attendance.list')) ?'active':'' }}">
                            <a href="{{ route('admin.attendance.list') }}" class="menu-link">
                                <div>Attendance</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.salary.settings.page')) ?'active':'' }}">
                            <a href="{{ route('admin.salary.settings.page') }}" class="menu-link">
                                <div>Settings</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Fund & Advance Management -->
                <li class="menu-item {{
                    (request()->routeIs('admin.fund_advance.dashboard')) ||
                    (request()->routeIs('admin.fund_advance.transactions.list')) ||
                    (request()->routeIs('admin.fund_advance.transactions.view')) ||
                    (request()->routeIs('admin.fund_advance.settlements.pending')) ||
                    (request()->routeIs('admin.fund_advance.settlements.history')) ||
                    (request()->routeIs('admin.fund_advance.ledgers.party')) ||
                    (request()->routeIs('admin.fund_advance.ledgers.employee')) ||
                    (request()->routeIs('admin.fund_advance.ledgers.fund')) ||
                    (request()->routeIs('admin.fund_advance.reports.outstanding')) ||
                    (request()->routeIs('admin.fund_advance.reports.advances')) ||
                    (request()->routeIs('admin.fund_advance.reports.loans')) ||
                    (request()->routeIs('admin.fund_advance.reports.funds')) ||
                    (request()->routeIs('admin.fund_advance.reports.settlements')) ||
                    (request()->routeIs('admin.fund_advance.reports.transactions')) ?'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons ti ti-report-money"></i>
                        <div>Fund & Advance Management</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.dashboard')) ?'active':'' }}">
                            <a href="{{ route('admin.fund_advance.dashboard') }}" class="menu-link">
                                <div>Dashboard</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.transactions.list')) || (request()->routeIs('admin.fund_advance.transactions.view')) ?'active':'' }}">
                            <a href="{{ route('admin.fund_advance.transactions.list') }}" class="menu-link">
                                <div>Transactions</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.pending')) || (request()->routeIs('admin.fund_advance.settlements.history')) ?'active open':'' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Settlements</div></a>
                            <ul class="menu-sub">
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.pending')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.settlements.pending') }}" class="menu-link"><div>Pending Settlements</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.history')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.settlements.history') }}" class="menu-link"><div>Settlement History</div></a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item {{
                            (request()->routeIs('admin.fund_advance.ledgers.party')) ||
                            (request()->routeIs('admin.fund_advance.ledgers.employee')) ||
                            (request()->routeIs('admin.fund_advance.ledgers.fund')) ?'active open':'' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Ledgers</div></a>
                            <ul class="menu-sub">
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.party')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.ledgers.party') }}" class="menu-link"><div>Party Ledger</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.employee')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.ledgers.employee') }}" class="menu-link"><div>Employee Ledger</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.fund')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.ledgers.fund') }}" class="menu-link"><div>Fund Ledger</div></a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item {{
                            (request()->routeIs('admin.fund_advance.reports.outstanding')) ||
                            (request()->routeIs('admin.fund_advance.reports.advances')) ||
                            (request()->routeIs('admin.fund_advance.reports.loans')) ||
                            (request()->routeIs('admin.fund_advance.reports.funds')) ||
                            (request()->routeIs('admin.fund_advance.reports.settlements')) ||
                            (request()->routeIs('admin.fund_advance.reports.transactions')) ?'active open':'' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Reports</div></a>
                            <ul class="menu-sub">
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.outstanding')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.reports.outstanding') }}" class="menu-link"><div>Outstanding</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.advances')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.reports.advances') }}" class="menu-link"><div>Advances</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.loans')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.reports.loans') }}" class="menu-link"><div>Loans</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.funds')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.reports.funds') }}" class="menu-link"><div>Funds</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.settlements')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.reports.settlements') }}" class="menu-link"><div>Settlements</div></a>
                                </li>
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.transactions')) ?'active':'' }}">
                                    <a href="{{ route('admin.fund_advance.reports.transactions') }}" class="menu-link"><div>Transactions</div></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        <!-- Whatsapp -->
        <li class="menu-item {{
            (request()->routeIs('admin.whatsapp.campaign')) ||
            (request()->routeIs('admin.whatsapp.templateList')) ||
            (request()->routeIs('admin.whatsapp.api')) ||
            (request()->routeIs('admin.whatsapp.campaign.report')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-brand-whatsapp"></i>
                <div>Whatsapp Business</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{
                    (request()->routeIs('admin.whatsapp.campaign')) ||
                    (request()->routeIs('admin.whatsapp.campaign.report'))  ?'active':'' }}">
                    <a href="{{ route('admin.whatsapp.campaign') }}" class="menu-link"><div>Campaign</div></a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.whatsapp.templateList')) ?'active':'' }}">
                    <a href="{{ route('admin.whatsapp.templateList') }}" class="menu-link"> <div>Template</div></a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.whatsapp.api')) ?'active':'' }}">
                    <a href="{{ route('admin.whatsapp.api') }}" class="menu-link"><div>API Setup</div></a>
                </li>
            </ul>
        </li>

        <!-- Meta Whatsapp -->
        <li class="menu-item {{
            (request()->routeIs('admin.metawhatsapp.campaign')) ||
            (request()->routeIs('admin.metawhatsapp.campaignShow')) ||
            (request()->is('admin/team-member-page/*')) ||
            (request()->is('admin/dynamic-image-url/*')) ||
            (request()->routeIs('admin.metawhatsapp.api')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-brand-meta"></i>
                <div>Whatsapp Meta</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{
                    (request()->routeIs('admin.metawhatsapp.campaign')) ||
                    (request()->routeIs('admin.metawhatsapp.campaignShow')) ?'active':'' }}">
                    <a href="{{ route('admin.metawhatsapp.campaign') }}" class="menu-link">
                        <div>Campaign</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="" class="menu-link">Template</a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.metawhatsapp.api')) ?'active':'' }}">
                    <a href="{{ route('admin.metawhatsapp.api') }}" class="menu-link"><div>Meta API Setup</div></a>
                </li>
            </ul>
        </li>

        <!-- sms Campaign -->
        <li class="menu-item {{
            (request()->routeIs('admin.sms.api')) ||
            (request()->routeIs('admin.sms.smstemplateList')) ||       
            (request()->routeIs('admin.smsCampaign.campaignShow')) ||       
            (request()->routeIs('admin.smsCampaign.list')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-message"></i>
                <div>SMS Campaign</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.smsCampaign.list')) ||  (request()->routeIs('admin.smsCampaign.campaignShow')) || (request()->routeIs('admin.smsCampaign.getsendList')) ?'active':'' }}">
                    <a href="{{ route('admin.smsCampaign.list') }}" class="menu-link">
                    <div>Campaign</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.sms.smstemplateList')) ?'active':'' }}">
                    <a href="{{ route('admin.sms.smstemplateList') }}" class="menu-link"> <div>Template</div></a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.sms.api')) ?'active':'' }}">
                    <a href="{{ route('admin.sms.api') }}" class="menu-link"> <div>SMS API Setup</div></a>
                </li>

            </ul>
        </li>

         <!-- sms Campaign -->
        <li class="menu-item {{
            (request()->routeIs('admin.sms.api')) ||
            (request()->routeIs('admin.sms.smstemplateList')) ||       
            (request()->routeIs('admin.smsCampaign.campaignShow')) ||       
            (request()->routeIs('admin.smsCampaign.list')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-message"></i>
                <div>SMS Campaign</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ (request()->routeIs('admin.smsCampaign.list')) ||  (request()->routeIs('admin.smsCampaign.campaignShow')) || (request()->routeIs('admin.smsCampaign.getsendList')) ?'active':'' }}">
                    <a href="{{ route('admin.smsCampaign.list') }}" class="menu-link">
                    <div>Campaign</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.sms.smstemplateList')) ?'active':'' }}">
                    <a href="{{ route('admin.sms.smstemplateList') }}" class="menu-link"> <div>Template</div></a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.sms.api')) ?'active':'' }}">
                    <a href="{{ route('admin.sms.api') }}" class="menu-link"> <div>SMS API Setup</div></a>
                </li>

            </ul>
        </li>

        <!-- Layouts -->
        <li class="menu-item {{
            (request()->routeIs('admin.settings.file_manager.index')) ||
            (request()->routeIs('admin.settings.storage_usage.index')) ||
            (request()->routeIs('admin.settings.db_backup.index')) ||
            (request()->routeIs('admin.webconfig')) ||
            (request()->routeIs('admin.mail.setup.index')) ||
            (request()->routeIs('admin.permission')) ||
            (request()->routeIs('admin.personaliseclass')) ||
            (request()->routeIs('admin.template.index')) ||
            (request()->routeIs('admin.order.received.panel')) ||
            (request()->routeIs('admin.cv.setting')) ||
            (request()->routeIs('admin.customer.costlist')) ||
            (request()->routeIs('admin.booking.requirement')) ||
            (request()->routeIs('admin.frontwebsiteconfig')) ||
            (request()->routeIs('admin.metanotification.list')) ||
            (request()->routeIs('admin.staticmetanotification.list')) ||
            (request()->routeIs('admin.todolabel.list')) ||
            (request()->routeIs('admin.department.list')) ||
            (request()->routeIs('admin.businesstype.list')) ||
            (request()->routeIs('admin.groupm.list')) ||
            (request()->routeIs('admin.orderStatus')) ||
            (request()->routeIs('admin.branch')) ||
            (request()->routeIs('admin.staff')) ||
            (request()->routeIs('admin.staff.view')) ||
            (request()->routeIs('admin.profession')) ||
            (request()->routeIs('admin.placeofissue')) ||
            (request()->routeIs('admin.country')) ||
            (request()->routeIs('admin.region')) ||
            (request()->routeIs('admin.city')) ||
            (request()->routeIs('admin.carknown.list')) ||
            (request()->routeIs('admin.education')) ||
            (request()->routeIs('admin.religion')) ||
            (request()->routeIs('admin.expworklocation')) ||
            (request()->routeIs('admin.leadstage.list')) ||
            (request()->routeIs('admin.lifecycle.list')) ||
            (request()->routeIs('admin.mailtest')) ||
            (request()->routeIs('admin.domains')) ||
            (request()->routeIs('admin.industries.list')) ? 'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle settings-menu-toggle">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div>Settings</div>
            </a>

            <ul class="menu-sub">

            {{-- @if ($permission->access_setting == 1) --}}
            {{-- <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.staff.view')) || (request()->routeIs('admin.domains')) || (request()->routeIs('admin.permission')) || (request()->routeIs('admin.device.index')) ||
            (request()->routeIs('admin.unauthorizedlogin.index')) ? 'active open':'' }}">
                        <div>Access</div>
                    </a>
                    <ul class="menu-sub">


                        <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.staff.view')) ? 'active':'' }}">
                            <a href="{{ route('admin.staff') }}" class="menu-link">
                                <div>Team Member</div>
                            </a>
                        </li>

                        <li class="menu-item {{ (request()->routeIs('admin.permission')) ?'active':'' }}">
                            <a href="{{ route('admin.permission') }}" class="menu-link">
                                <div>Permission</div>
                            </a>
                        </li>

                        <li class="menu-item {{ (request()->routeIs('admin.device.index')) ?'active':'' }}">
                            <a href="{{ route('admin.device.index') }}" class="menu-link">
                                <div>Allowed IP</div>
                            </a>
                        </li>

                    </ul>
                </li> --}}
                {{-- @endif --}}



                <li class="menu-item {{
                    (request()->routeIs('admin.todolabel.list')) ||
                    (request()->routeIs('admin.department.list')) ?'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Todo">Todo</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->routeIs('admin.todolabel.list')) ?'active':'' }}">
                            <a href="{{ route('admin.todolabel.list') }}" class="menu-link">
                                <div data-i18n="Todo Label">Todo Label</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.department.list')) ?'active':'' }}">
                            <a href="{{ route('admin.department.list') }}" class="menu-link">
                                <div data-i18n="Department">Department</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-item {{
                    (request()->routeIs('admin.businesstype.list')) ||
                    (request()->routeIs('admin.groupm.list')) ||
                    (request()->routeIs('admin.lifecycle.list')) ||
                    (request()->routeIs('admin.leadstage.list')) ||
                    (request()->routeIs('admin.contact_plus_export_history')) ||
                    (request()->routeIs('admin.email_qamr_portal.history')) ||
                    (request()->routeIs('admin.industries.list')) ?'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div>Contact Plus</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->routeIs('admin.industries.list')) ?'active':'' }}">
                            <a href="{{ route('admin.industries.list') }}" class="menu-link">
                                <div>Industries</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.businesstype.list')) ?'active':'' }}">
                            <a href="{{ route('admin.businesstype.list') }}" class="menu-link">
                                <div>Business Type</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.groupm.list')) ?'active':'' }}">
                            <a href="{{ route('admin.groupm.list') }}" class="menu-link">
                                <div>Group</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.lifecycle.list')) ?'active':'' }}">
                            <a href="{{ route('admin.lifecycle.list') }}" class="menu-link">
                                <div>Lifecycle Status</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.leadstage.list')) ?'active':'' }}">
                            <a href="{{ route('admin.leadstage.list') }}" class="menu-link">
                                <div>Lead Stage</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-item {{
                    (request()->routeIs('admin.orderStatus')) ||
                    (request()->routeIs('admin.branch')) ||
                    (request()->routeIs('admin.profession')) ||
                    (request()->routeIs('admin.placeofissue')) ||
                    (request()->routeIs('admin.country')) ||
                    (request()->routeIs('admin.region')) ||
                    (request()->routeIs('admin.city')) ||
                    (request()->routeIs('admin.carknown.list')) ||
                    (request()->routeIs('admin.education')) ||
                    (request()->routeIs('admin.religion')) ||
                    (request()->routeIs('admin.expworklocation')) ? 'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-adjustments"></i>
                    <div>Dynamic</div>
                    </a>

                    <ul class="menu-sub">
                    <li class="menu-item {{ (request()->routeIs('admin.branch')) ? 'active':'' }}">
                        <a href="{{ route('admin.branch') }}" class="menu-link">
                        <div>Branch</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.orderStatus')) ? 'active':'' }}">
                        <a href="{{ route('admin.orderStatus') }}" class="menu-link">
                        <div>Order Status</div>
                        </a>
                    </li>

                    <li class="menu-item {{ (request()->routeIs('admin.profession')) ?'active':'' }}">
                        <a href="{{ route('admin.profession') }}" class="menu-link">
                        <div>Profession</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.placeofissue')) ?'active':'' }}">
                        <a href="{{ route('admin.placeofissue') }}" class="menu-link">
                        <div>Place of Issue</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.expworklocation')) ?'active':'' }}">
                        <a href="{{ route('admin.expworklocation') }}" class="menu-link">
                        <div>Expected Work Location</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.country')) ?'active':'' }}">
                        <a href="{{ route('admin.country') }}" class="menu-link">
                        <div>Country</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.region')) ?'active':'' }}">
                        <a href="{{ route('admin.region') }}" class="menu-link">
                        <div>Region</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.city')) ?'active':'' }}">
                        <a href="{{ route('admin.city') }}" class="menu-link">
                        <div>City</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.carknown.list')) ?'active':'' }}">
                        <a href="{{ route('admin.carknown.list') }}" class="menu-link">
                        <div>Car Known</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.education')) ?'active':'' }}">
                        <a href="{{ route('admin.education') }}" class="menu-link">
                        <div>Education</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->routeIs('admin.religion')) ?'active':'' }}">
                        <a href="{{ route('admin.religion') }}" class="menu-link">
                        <div>Religion</div>
                        </a>
                    </li>



                    </ul>
                </li>

                <li class="menu-item {{
                    (request()->routeIs('admin.webconfig')) ||
                    (request()->routeIs('admin.frontwebsiteconfig')) ?'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div>Website</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->routeIs('admin.webconfig')) ?'active':'' }}">
                            <a href="{{ route('admin.webconfig') }}" class="menu-link">
                                <div>Website Configuration</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->routeIs('admin.frontwebsiteconfig')) ?'active':'' }}">
                            <a href="{{ route('admin.frontwebsiteconfig') }}" class="menu-link">
                                <div>Front End Website</div>
                            </a>
                        </li>
                    </ul>
                </li>







                {{-- <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
                <a href="{{ route('admin.mail.setup.index') }}" class="menu-link">
                    <div>Mail Setup</div>
                </a>
                </li> --}}

                <li class="menu-item {{ (request()->routeIs('admin.personaliseclass')) ?'active':'' }}">
                <a href="{{ route('admin.personaliseclass') }}" class="menu-link">
                    <div>Personalise Class</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.settings.storage_usage.index')) ?'active':'' }}">
                <a href="{{ route('admin.settings.storage_usage.index') }}" class="menu-link">
                    <div>Storage Usage</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.settings.db_backup.index')) ?'active':'' }}">
                <a href="{{ route('admin.settings.db_backup.index') }}" class="menu-link">
                    <div>DB Backup</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.template.index')) ?'active':'' }}">
                <a href="{{ route('admin.template.index') }}" class="menu-link">
                    <div>Notifications</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.metanotification.list')) ?'active':'' }}">
                <a href="{{ route('admin.metanotification.list') }}" class="menu-link">
                    <div>Emp Meta Automation</div>
                </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('admin.metaautomation.list')) ?'active':'' }}">
                <a href="{{ route('admin.metaautomation.list') }}" class="menu-link">
                    <div>Meta Automation</div>
                </a>
                </li>

                {{-- <li class="menu-item {{ (request()->routeIs('admin.staticmetanotification.list')) ?'active':'' }}">
                <a href="{{ route('admin.staticmetanotification.list') }}" class="menu-link">
                    <div>Static Meta OTP</div>
                </a>
                </li> --}}

                {{-- <li class="menu-item {{ (request()->routeIs('admin.order.received.panel')) ?'active':'' }}">
                <a href="{{ route('admin.order.received.panel') }}" class="menu-link">
                    <div>Order Receiver Panel</div>
                </a>
                </li> --}}



                <li class="menu-item {{ (request()->routeIs('admin.cv.setting')) ?'active':'' }}">
                <a href="{{ route('admin.cv.setting') }}" class="menu-link">
                    <div>CV Setting</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.whatsapp.api')) ?'active':'' }}">
                <a href="{{ route('admin.whatsapp.api') }}" class="menu-link">
                    <div>Whatsapp API</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.metawhatsapp.api')) ?'active':'' }}">
                <a href="{{ route('admin.metawhatsapp.api') }}" class="menu-link">
                    <div>Whatsapp API+</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.customer.costlist')) ?'active':'' }}">
                <a href="{{ route('admin.customer.costlist') }}" class="menu-link">
                    <div>Customer Cost</div>
                </a>
                </li>

                <li class="menu-item {{ (request()->routeIs('admin.booking.requirement')) ?'active':'' }}">
                <a href="{{ route('admin.booking.requirement') }}" class="menu-link">
                    <div>Requirement</div>
                </a>
                </li>


                <li class="menu-item {{ (request()->routeIs('admin.mailtest')) ?'active open':'' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-flask"></i>
                    <div>Testing</div>
                    </a>

                    <ul class="menu-sub">
                    <li class="menu-item {{ (request()->routeIs('admin.mailtest')) ?'active':'' }}">
                        <a href="{{ route('admin.mailtest') }}" class="menu-link">
                        <div>Mail</div>
                        </a>
                    </li>
                    </ul>

                </li>

                <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active open':''}}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div>Setup</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
                            <a href="{{ route('admin.mail.setup.index') }}" class="menu-link"><div>Mail Setup</div></a>
                        </li>
                        <li class="menu-item">
                            <a href="" class="menu-link">
                                <div>Facebool API Lead</div>
                            </a>
                        </li>
                    </ul>
                </li>


            </ul>
        </li>

    </ul>
  @elseif(isset($permission) && $permission->full_access == 0)
    <ul class="menu-inner py-1">
        <!-- Dashboards -->

        <li class="menu-item {{ (request()->routeIs('admin.dashboard')) ?'active':'' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        @if ($permission->leads == 1)
            <li class="menu-item {{ (request()->routeIs('admin.leads.list')) || (request()->routeIs('admin.leads.show')) ?'active':'' }}">
                <a href="{{ route('admin.leads.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-target"></i>
                    <div>Leads</div>
                </a>
            </li>
        @endif

        @if ($permission->allcontact == 1)
            <li class="menu-item {{
                (request()->routeIs('admin.allcontact.list')) ||
                (request()->routeIs('admin.contact_export_history')) ||
                (request()->routeIs('admin.email_qamr_portal.allcontact.history')) ||
                (request()->routeIs('admin.allcontact.show')) ?'active':'' }}">
                <a href="{{ route('admin.allcontact.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-address-book"></i>
                    <div>Contacts</div>
                </a>
            </li>
        @endif


        @if ($permission->todo == 1)
            <li class="menu-item {{ (request()->routeIs('admin.todo.list')) ?'active':'' }}">
                <a href="{{ route('admin.todo.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-square-check"></i>
                    <div>Task</div>
                </a>
            </li>
        @endif

        @if ($permission->deal_pipeline == 1)
            <li class="menu-item {{ (request()->routeIs('admin.dealPipeline.list')) ? 'active' : '' }}">
                <a href="{{ route('admin.dealPipeline.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-chart-line"></i>
                    <!-- <i class="menu-icon tf-icons ti ti-intercom"></i> -->
                    <div>Deal Pipeline</div>
                </a>
            </li>
        @endif


        @if ($permission->bookings == 1)
        <li class="menu-item {{ (request()->routeIs('admin.booking')) || (request()->routeIs('admin.booking.view'))  ?'active':'' }}">
            <a href="{{ route('admin.booking') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                <div>Orders</div>
            </a>
        </li>
        @endif

        @if ($permission->employer == 1)
        <li class="menu-item {{ (request()->routeIs('admin.employer')) || (request()->routeIs('admin.employer.visaDetshow')) ?'active':'' }}">
            <a href="{{ route('admin.employer') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-briefcase"></i>
                <div>Employer</div>
            </a>
        </li>
        @endif

        @if ($permission->employerplus == 1)
            <li class="menu-item {{ (request()->routeIs('admin.employer.listp')) || (request()->routeIs('admin.employer.visaDetshowp')) ?'active':'' }}">
                <a href="{{ route('admin.employer.listp') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building"></i>
                    <div>Employer plus</div>
                </a>
            </li>
        @endif

        @if ($permission->candidate == 1)
        <li class="menu-item {{ (request()->routeIs('admin.candidate')) || (request()->routeIs('admin.candidate.show')) || (request()->routeIs('admin.candidate.publish')) ?'active':'' }}">
            <a href="{{ route('admin.candidate') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-users"></i>
            <div>Candidate</div>
            </a>
        </li>
        @endif

        @if ($permission->testimonial == 1)
        <li class="menu-item {{ (request()->routeIs('admin.testimonial')) || (request()->routeIs('admin.testimonial.show')) || (request()->routeIs('admin.testimonial.publish')) ?'active':'' }}">
            <a href="{{ route('admin.testimonial') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-quote"></i>
            <div>Testimonial</div>
            </a>
        </li>
        @endif


        @if ($permission->associate == 1)
        <li class="menu-item {{ (request()->routeIs('admin.associate')) ||
            (request()->routeIs('admin.associate.show')) ?'active':'' }}">
            <a href="{{ route('admin.associate') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-user-plus"></i>
            <div>Associate</div>
            </a>
        </li>
        @endif

        
        <li class="menu-item {{ (request()->routeIs('admin.attendance.top')) ?'active':'' }}">
            <a href="{{ route('admin.attendance.top') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-calendar-stats"></i>
            <div>Attendance</div>
            </a>
        </li>

        @if ($permission->client == 1)
        <li class="menu-item {{ (request()->routeIs('admin.client')) ?'active':'' }}">
            <a href="{{ route('admin.client') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div>Customer</div>
            </a>
        </li>
        @endif

        @if ($permission->contactp == 1)
        <li class="menu-item {{ (request()->routeIs('admin.contact.list')) || (request()->routeIs('admin.contact.show')) || (request()->routeIs('admin.contact_plus_export_history')) || (request()->routeIs('admin.email_qamr_portal.history')) ?'active':'' }}">
            <a href="{{ route('admin.contact.list') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-id"></i>
            <div>Contact Plus</div>
            </a>
        </li>
        @endif


        @if ($permission->partner == 1)
        <li class="menu-item {{ (request()->routeIs('admin.partner')) || (request()->routeIs('admin.partner.show')) ?'active':'' }}">
            <a href="{{ route('admin.partner') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-heart-handshake"></i>
            <div>Partner</div>
            </a>
        </li>
        @endif

        @if ($permission->file_manager == 1)
            <li class="menu-item {{ (request()->routeIs('admin.file_manager.list')) ?'active':'' }}">
                <a href="{{ route('admin.file_manager.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-folder"></i>
                    <div>File Manager</div>
                </a>
            </li>
        @endif


        <!-- Finance Management -->

        @if ($permission->finance == 1)
        <li class="menu-item {{
            (request()->routeIs('admin.expense.*')) ||
            (request()->routeIs('admin.salary.*')) ||
            (request()->routeIs('admin.attendance.*')) ||
            (request()->routeIs('admin.candidate.transactionList')) ||
            (request()->routeIs('admin.invoice.list')) ||
            (request()->routeIs('admin.invoice.show')) ||
            (request()->routeIs('admin.payment.list')) ||
            (request()->routeIs('admin.fund_advance.*')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-currency-rupee"></i>
            <div>Finance</div>
            </a>
            <ul class="menu-sub">

                @if ($permission->expense)
                    <li class="menu-item {{ (request()->routeIs('admin.expense.list')) ?'active':'' }}">
                        <a href="{{ route('admin.expense.list') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-receipt-2"></i>
                            <div>Expense</div>
                        </a>
                    </li>
                @endif

                @if ($permission->candidate_finance == 1)
                    <li class="menu-item {{ (request()->routeIs('admin.candidate.transactionList')) ?'active open':'' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Candidate</div></a>
                        <ul class="menu-sub">
                        @if ($permission->transaction_finance == 1)
                            <li class="menu-item {{ (request()->routeIs('admin.candidate.transactionList')) ?'active':'' }}">
                                <a href="{{ route('admin.candidate.transactionList') }}" class="menu-link">
                                    <div>Transaction</div>
                                </a>
                            </li>
                        @endif

                        </ul>
                    </li>
                @endif

                @if ($permission->transaction_client == 1)
                    <li class="menu-item {{
                        (request()->routeIs('admin.invoice.list')) ||
                        (request()->routeIs('admin.payment.list')) ||
                        (request()->routeIs('admin.invoice.show')) ?'active open':'' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">Client</a>
                        <ul class="menu-sub">
                            @if ($permission->finance_sale_invoices == 1)
                                <li class="menu-item {{
                                    (request()->routeIs('admin.invoice.list')) ||
                                    (request()->routeIs('admin.invoice.show')) ?'active':'' }}">
                                    <a href="{{ route('admin.invoice.list') }}" class="menu-link">
                                        <div>Sale Invoices</div>
                                    </a>
                                </li>
                            @endif

                            @if ($permission->finance_payment == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.payment.list')) ?'active':'' }}">
                                    <a href="{{ route('admin.payment.list') }}" class="menu-link">
                                        <div>Payments</div>
                                    </a>
                                </li>
                            @endif


                        </ul>
                    </li>
                @endif

                @if ($permission->hr_management == 1)
                    <li class="menu-item {{
                        (request()->routeIs('admin.salary.dashboard')) ||
                        (request()->routeIs('admin.attendance.list')) ||
                        (request()->routeIs('admin.salary.index')) ||
                        (request()->routeIs('admin.salary.settings.page')) ?'active open':'' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-id-badge-2"></i>
                            <div>HR Management</div>
                        </a>
                        <ul class="menu-sub">
                            @if ($permission->hr_dashboard == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.salary.dashboard')) ?'active':'' }}">
                                    <a href="{{ route('admin.salary.dashboard') }}" class="menu-link">
                                        <div>Dashboard</div>
                                    </a>
                                </li>
                            @endif
                            @if ($permission->hr_attendance == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.attendance.list')) ?'active':'' }}">
                                    <a href="{{ route('admin.attendance.list') }}" class="menu-link">
                                        <div>Attendance</div>
                                    </a>
                                </li>
                            @endif
                            @if ($permission->hr_payroll == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.salary.index')) ?'active':'' }}">
                                    <a href="{{ route('admin.salary.index') }}" class="menu-link">
                                        <div>Payroll</div>
                                    </a>
                                </li>
                            @endif
                            @if ($permission->hr_settings == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.salary.settings.page')) ?'active':'' }}">
                                    <a href="{{ route('admin.salary.settings.page') }}" class="menu-link">
                                        <div>Settings</div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if ($permission->fund_advance == 1)
                    <li class="menu-item {{
                        (request()->routeIs('admin.fund_advance.dashboard')) ||
                        (request()->routeIs('admin.fund_advance.transactions.list')) ||
                        (request()->routeIs('admin.fund_advance.transactions.view')) ||
                        (request()->routeIs('admin.fund_advance.settlements.pending')) ||
                        (request()->routeIs('admin.fund_advance.settlements.history')) ||
                        (request()->routeIs('admin.fund_advance.ledgers.party')) ||
                        (request()->routeIs('admin.fund_advance.ledgers.employee')) ||
                        (request()->routeIs('admin.fund_advance.ledgers.fund')) ||
                        (request()->routeIs('admin.fund_advance.reports.outstanding')) ||
                        (request()->routeIs('admin.fund_advance.reports.advances')) ||
                        (request()->routeIs('admin.fund_advance.reports.loans')) ||
                        (request()->routeIs('admin.fund_advance.reports.funds')) ||
                        (request()->routeIs('admin.fund_advance.reports.settlements')) ||
                        (request()->routeIs('admin.fund_advance.reports.transactions')) ?'active open':'' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-report-money"></i>
                            <div>Fund & Advance Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.dashboard')) ?'active':'' }}">
                                <a href="{{ route('admin.fund_advance.dashboard') }}" class="menu-link">
                                    <div>Dashboard</div>
                                </a>
                            </li>
                            <li class="menu-item {{ (request()->routeIs('admin.fund_advance.transactions.list')) || (request()->routeIs('admin.fund_advance.transactions.view')) ?'active':'' }}">
                                <a href="{{ route('admin.fund_advance.transactions.list') }}" class="menu-link">
                                    <div>Transactions</div>
                                </a>
                            </li>

                            @if ($permission->fund_advance_settlement_view == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.pending')) || (request()->routeIs('admin.fund_advance.settlements.history')) ?'active open':'' }}">
                                    <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Settlements</div></a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.pending')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.settlements.pending') }}" class="menu-link"><div>Pending Settlements</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.settlements.history')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.settlements.history') }}" class="menu-link"><div>Settlement History</div></a>
                                        </li>
                                    </ul>
                                </li>
                            @endif

                            @if ($permission->fund_advance_ledger_view == 1)
                                <li class="menu-item {{
                                    (request()->routeIs('admin.fund_advance.ledgers.party')) ||
                                    (request()->routeIs('admin.fund_advance.ledgers.employee')) ||
                                    (request()->routeIs('admin.fund_advance.ledgers.fund')) ?'active open':'' }}">
                                    <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Ledgers</div></a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.party')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.ledgers.party') }}" class="menu-link"><div>Party Ledger</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.employee')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.ledgers.employee') }}" class="menu-link"><div>Employee Ledger</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.ledgers.fund')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.ledgers.fund') }}" class="menu-link"><div>Fund Ledger</div></a>
                                        </li>
                                    </ul>
                                </li>
                            @endif

                            @if ($permission->fund_advance_reports_view == 1)
                                <li class="menu-item {{
                                    (request()->routeIs('admin.fund_advance.reports.outstanding')) ||
                                    (request()->routeIs('admin.fund_advance.reports.advances')) ||
                                    (request()->routeIs('admin.fund_advance.reports.loans')) ||
                                    (request()->routeIs('admin.fund_advance.reports.funds')) ||
                                    (request()->routeIs('admin.fund_advance.reports.settlements')) ||
                                    (request()->routeIs('admin.fund_advance.reports.transactions')) ?'active open':'' }}">
                                    <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Reports</div></a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.outstanding')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.reports.outstanding') }}" class="menu-link"><div>Outstanding</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.advances')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.reports.advances') }}" class="menu-link"><div>Advances</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.loans')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.reports.loans') }}" class="menu-link"><div>Loans</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.funds')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.reports.funds') }}" class="menu-link"><div>Funds</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.settlements')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.reports.settlements') }}" class="menu-link"><div>Settlements</div></a>
                                        </li>
                                        <li class="menu-item {{ (request()->routeIs('admin.fund_advance.reports.transactions')) ?'active':'' }}">
                                            <a href="{{ route('admin.fund_advance.reports.transactions') }}" class="menu-link"><div>Transactions</div></a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            </ul>
        </li>
        @endif



        <!-- Whatsapp -->
        @if ($permission->whatsapp_nromal == 1)
            <li class="menu-item {{
                (request()->routeIs('admin.whatsapp.campaign')) ||
                (request()->routeIs('admin.whatsapp.templateList')) ||
                (request()->routeIs('admin.whatsapp.api')) ||
                (request()->routeIs('admin.whatsapp.campaign.report')) ?'active open':'' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-brand-whatsapp"></i>
                    <div>Whatsapp Business</div>
                </a>

                <ul class="menu-sub">
                    @if ($permission->whatsapp_campaign == 1)
                        <li class="menu-item {{
                        (request()->routeIs('admin.whatsapp.campaign')) ||
                        (request()->routeIs('admin.whatsapp.campaign.report'))  ?'active':'' }}">
                        <a href="{{ route('admin.whatsapp.campaign') }}" class="menu-link">
                            <div>Campaign</div>
                        </a>
                        </li>
                    @endif

                    @if ($permission->whatsapp_template == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.whatsapp.templateList')) ?'active':'' }}">
                        <a href="{{ route('admin.whatsapp.templateList') }}" class="menu-link">Template</a>
                        </li>
                    @endif

                    @if ($permission->whatsapp_api == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.whatsapp.api')) ?'active':'' }}">
                            <a href="{{ route('admin.whatsapp.api') }}" class="menu-link"><div>API Setup</div></a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <!-- Meta Whatsapp -->
        @if ($permission->whatsapp_plus == 1)
            <li class="menu-item {{
                (request()->routeIs('admin.metawhatsapp.campaign')) ||
                (request()->routeIs('admin.metawhatsapp.campaignShow')) ||
                (request()->routeIs('admin.whatsapp.metatemplateList')) ||
                (request()->routeIs('admin.imagehost.list')) ||
                (request()->routeIs('admin.whatsapp.chatredirecturllist')) ||
                (request()->is('admin/team-member-page/*')) ||
                (request()->is('admin/dynamic-image-url/*')) ||
                (request()->routeIs('admin.metawhatsapp.api')) ?'active open':'' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-brand-meta"></i>
                <div>Whatsapp Meta</div>
                </a>
                <ul class="menu-sub">
                @if ($permission->meta_whatsapp_campaign == 1)
                    <li class="menu-item {{ (request()->routeIs('admin.metawhatsapp.campaign')) || (request()->routeIs('admin.metawhatsapp.campaignShow')) ?'active':'' }}">
                    <a href="{{ route('admin.metawhatsapp.campaign') }}" class="menu-link">
                        <div>Campaign</div>
                    </a>
                    </li>
                @endif

                @if ($permission->meta_whatsapp_template == 1)
                    <li class="menu-item {{ (request()->routeIs('admin.whatsapp.metatemplateList')) ?'active':'' }}">
                    <a href="{{ route('admin.whatsapp.metatemplateList') }}" class="menu-link"> Template</a>
                    </li>
                @endif

                @if ($permission->image_host == 1)
                    <li class="menu-item {{ (request()->routeIs('admin.imagehost.list')) ?'active':'' }}">
                    <a href="{{ route('admin.imagehost.list') }}" class="menu-link">
                        <div>Image Host</div>
                    </a>
                    </li>
                @endif

                @if ($permission->whatsapp_meta_api == 1)
                    <li class="menu-item {{ (request()->routeIs('admin.metawhatsapp.api')) ?'active':'' }}">
                        <a href="{{ route('admin.metawhatsapp.api') }}" class="menu-link"><div>Meta API Setup</div></a>
                    </li>
                @endif

                @if ($permission->whatsapp_url == 1)
                <li class="menu-item {{ (request()->routeIs('admin.whatsapp.chatredirecturllist')) ?'active':'' }}">
                    <a href="{{ route('admin.whatsapp.chatredirecturllist') }}" class="menu-link">Whatsapp URL</a>
                </li>
                @endif

                @if ($permission->whatsapp_url == 1)
                <li class="menu-item {{ (request()->routeIs('admin.team-member-page.list')) ? 'active':'' }}">
                    <a href="{{ route('admin.team-member-page.list') }}" class="menu-link">Teammember Pages</a>
                </li>
                @endif

                @if ($permission->whatsapp_url == 1)
                <li class="menu-item {{ (request()->routeIs('admin.dynamic-image-url.list')) ? 'active':'' }}">
                    <a href="{{ route('admin.dynamic-image-url.list') }}" class="menu-link">Image URL</a>
                </li>
                @endif
                </ul>
            </li>
        @endif

         <!-- sms Campaign -->
        @if ($permission->sms_campaign_module == 1)
         <li class="menu-item {{
            (request()->routeIs('admin.sms.api')) ||
            (request()->routeIs('admin.sms.smstemplateList')) ||       
            (request()->routeIs('admin.smsCampaign.campaignShow')) ||       
            (request()->routeIs('admin.smsCampaign.list')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-message"></i>
                <div>SMS Campaign</div>
            </a>

            <ul class="menu-sub">
            
                @if ($permission->sms_campaign == 1)
                <li class="menu-item {{ (request()->routeIs('admin.smsCampaign.list')) ||  (request()->routeIs('admin.smsCampaign.campaignShow')) || (request()->routeIs('admin.smsCampaign.getsendList')) ?'active':'' }}">
                    <a href="{{ route('admin.smsCampaign.list') }}" class="menu-link">
                    <div>Campaign</div>
                    </a>
                </li>
                @endif

                @if ($permission->sms_template == 1)
                <li class="menu-item {{ (request()->routeIs('admin.sms.smstemplateList')) ?'active':'' }}">
                    <a href="{{ route('admin.sms.smstemplateList') }}" class="menu-link"> <div>Template</div></a>
                </li>
                @endif

                @if ($permission->sms_api == 1)
                <li class="menu-item {{ (request()->routeIs('admin.sms.api')) ?'active':'' }}">
                    <a href="{{ route('admin.sms.api') }}" class="menu-link"> <div>SMS API Setup</div></a>
                </li>
                @endif

            </ul>
        </li>
        @endif

        <!-- Email Campaign -->
        @if(isset($permission->email_campaign_module))
        @if ($permission->email_campaign_module == 1)
            <li class="menu-item {{
                (request()->routeIs('admin.email.smtp')) ||
                (request()->routeIs('admin.email.templateList')) ||
                (request()->routeIs('admin.emailCampaign.list')) ||
                (request()->routeIs('admin.emailCampaign.show')) ? 'active open' : ''
            }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-mail"></i>
                    <div>Email Campaign</div>
                </a>

                <ul class="menu-sub">
                    
                    {{-- Campaign --}}
                    @if ($permission->email_campaign == 1)
                        <li class="menu-item {{
                            (request()->routeIs('admin.emailCampaign.list')) ||
                            (request()->routeIs('admin.emailCampaign.show')) ? 'active' : ''
                        }}">
                            <a href="{{ route('admin.emailCampaign.list') }}" class="menu-link">
                                <div>Campaign</div>
                            </a>
                        </li>
                    @endif

                    {{-- Template --}}
                    @if ($permission->email_template == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.email.templateList')) ? 'active' : '' }}">
                            <a href="{{ route('admin.email.templateList') }}" class="menu-link">
                                <div>Template</div>
                            </a>
                        </li>
                    @endif

                    {{-- SMTP Setup --}}
                    @if ($permission->email_smtp == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.email.smtp')) ? 'active' : '' }}">
                            <a href="{{ route('admin.email.smtp') }}" class="menu-link">
                                <div>SMTP Setup</div>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif
        @endif


        <!-- Layouts -->
        @if ($permission->settings == 1)
            <li class="menu-item {{
                (request()->routeIs('admin.settings.file_manager.index')) ||
                (request()->routeIs('admin.settings.storage_usage.index')) ||
                (request()->routeIs('admin.settings.db_backup.index')) ||
                (request()->routeIs('admin.webconfig')) ||
                (request()->routeIs('admin.mail.setup.index')) ||
                (request()->routeIs('admin.permission')) ||
                (request()->routeIs('admin.personaliseclass')) ||
                (request()->routeIs('admin.template.index')) ||
                (request()->routeIs('admin.order.received.panel')) ||
                (request()->routeIs('admin.cv.setting')) ||
                (request()->routeIs('admin.customer.costlist')) ||
                (request()->routeIs('admin.booking.requirement')) ||
                (request()->routeIs('admin.frontwebsiteconfig')) ||
                (request()->routeIs('admin.metanotification.list')) ||
                (request()->routeIs('admin.staticmetanotification.list')) ||
                (request()->routeIs('admin.todolabel.list')) ||
                (request()->routeIs('admin.department.list')) ||
                (request()->routeIs('admin.industries.list')) ||
                (request()->routeIs('admin.staff')) ||
                (request()->routeIs('admin.staff.view')) ||
                (request()->routeIs('admin.businesstype.list')) ||
                (request()->routeIs('admin.domains')) ||
                (request()->routeIs('admin.groupm.list')) ? 'active open':'' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle settings-menu-toggle">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div>Settings</div>
                </a>
                <ul class="menu-sub">

                    @if ($permission->access_setting == 1)
                    <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.staff.view')) || (request()->routeIs('admin.domains')) || (request()->routeIs('admin.permission')) || (request()->routeIs('admin.device.index')) ||
                    (request()->routeIs('admin.unauthorizedlogin.index')) ? 'active open':'' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div>Access</div>
                        </a>
                        <ul class="menu-sub">

                            @if ($permission->staff == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.staff.view')) ? 'active':'' }}">
                                    <a href="{{ route('admin.staff') }}" class="menu-link">
                                        <div>Team Member</div>
                                    </a>
                                </li>
                            @endif

                            @if ($permission->access_allowed_ip == 1)
                            <li class="menu-item {{ (request()->routeIs('admin.device.index')) ?'active':'' }}">
                                <a href="{{ route('admin.device.index') }}" class="menu-link">
                                    <div>Allowed IP</div>
                                </a>
                            </li>
                            @endif
                            <!-- <li class="menu-item {{ (request()->routeIs('admin.unauthorizedlogin.index')) ?'active':'' }}">
                                <a href="{{ route('admin.unauthorizedlogin.index') }}" class="menu-link">
                                    <div>Unauthorize Login</div>
                                </a>
                            </li> -->
                        </ul>
                    </li>
                    @endif


                    @if ($permission->permission_setting == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.permission')) ?'active':'' }}">
                            <a href="{{ route('admin.permission') }}" class="menu-link">
                                <div>Permission</div>
                            </a>
                        </li>
                    @endif



                    @if ($permission->todo_setting == 1)
                        <li class="menu-item {{
                            (request()->routeIs('admin.todolabel.list')) ||
                            (request()->routeIs('admin.department.list')) ?'active open':'' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <div data-i18n="Todo">Todo</div>
                            </a>
                            <ul class="menu-sub">
                                @if ($permission->todo_label == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.todolabel.list')) ?'active':'' }}">
                                        <a href="{{ route('admin.todolabel.list') }}" class="menu-link">
                                            <div data-i18n="Todo Label">Todo Label</div>
                                        </a>
                                    </li>
                                @endif
                                @if ($permission->department == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.department.list')) ?'active':'' }}">
                                        <a href="{{ route('admin.department.list') }}" class="menu-link">
                                            <div data-i18n="Department">Department</div>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    @endif

                    @if ($permission->contact_plus_setting == 1)
                        <li class="menu-item {{
                            (request()->routeIs('admin.businesstype.list')) ||
                            (request()->routeIs('admin.groupm.list')) ||
                            (request()->routeIs('admin.contact_plus_export_history')) ||
                            (request()->routeIs('admin.email_qamr_portal.history')) ||
                            (request()->routeIs('admin.industries.list')) ?'active open':'' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <div>Contact Plus</div>
                            </a>
                            <ul class="menu-sub">
                                @if ($permission->industries == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.industries.list')) ?'active':'' }}">
                                        <a href="{{ route('admin.industries.list') }}" class="menu-link">
                                            <div>Industries</div>
                                        </a>
                                    </li>
                                @endif
                                @if ($permission->businesstype == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.businesstype.list')) ?'active':'' }}">
                                        <a href="{{ route('admin.businesstype.list') }}" class="menu-link">
                                            <div>Business Type</div>
                                        </a>
                                    </li>
                                @endif
                                @if ($permission->groupcp == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.groupm.list')) ?'active':'' }}">
                                        <a href="{{ route('admin.groupm.list') }}" class="menu-link">
                                            <div>Group</div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($permission->dynamic == 1)
                        <li class="menu-item {{
                            (request()->routeIs('admin.orderStatus')) ||
                            (request()->routeIs('admin.branch')) ||
                            (request()->routeIs('admin.staff')) ||
                            (request()->routeIs('admin.staff.view')) ||
                            (request()->routeIs('admin.profession')) ||
                            (request()->routeIs('admin.placeofissue')) ||
                            (request()->routeIs('admin.country')) ||
                            (request()->routeIs('admin.region')) ||
                            (request()->routeIs('admin.city')) ||
                            (request()->routeIs('admin.carknown.list')) ||
                            (request()->routeIs('admin.education')) ||
                            (request()->routeIs('admin.religion')) ||
                            (request()->routeIs('admin.expworklocation')) ? 'active open':'' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons ti ti-adjustments"></i>
                                <div>Dynamic</div>
                            </a>

                            <ul class="menu-sub">

                                @if ($permission->branch == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.branch')) ?'active':'' }}">
                                        <a href="{{ route('admin.branch') }}" class="menu-link">
                                            <div>Branch</div>
                                        </a>
                                    </li>
                                @endif

                                @if ($permission->profession == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.profession')) ?'active':'' }}">
                                        <a href="{{ route('admin.profession') }}" class="menu-link">
                                            <div>Profession</div>
                                        </a>
                                    </li>
                                @endif

                                @if ($permission->placeofissue == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.placeofissue')) ?'active':'' }}">
                                        <a href="{{ route('admin.placeofissue') }}" class="menu-link">
                                            <div>Place of Issue</div>
                                        </a>
                                    </li>
                                @endif


                                @if ($permission->expworklocation == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.expworklocation')) ?'active':'' }}">
                                        <a href="{{ route('admin.expworklocation') }}" class="menu-link">
                                            <div>Expected Work Location</div>
                                        </a>
                                    </li>
                                @endif


                                @if ($permission->country == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.country')) ?'active':'' }}">
                                        <a href="{{ route('admin.country') }}" class="menu-link">
                                            <div>Country</div>
                                        </a>
                                    </li>
                                @endif

                                @if ($permission->region == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.region')) ?'active':'' }}">
                                        <a href="{{ route('admin.region') }}" class="menu-link">
                                            <div>Region</div>
                                        </a>
                                    </li>
                                @endif

                                @if ($permission->city == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.city')) ?'active':'' }}">
                                        <a href="{{ route('admin.city') }}" class="menu-link">
                                            <div>City</div>
                                        </a>
                                    </li>
                                @endif


                                @if ($permission->carknown == 1)
                                    <li class="menu-item {{ (request()->routeIs('admin.carknown.list')) ?'active':'' }}">
                                        <a href="{{ route('admin.carknown.list') }}" class="menu-link">
                                            <div>Car Known</div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <li class="menu-item {{ (request()->routeIs('admin.webconfig')) ?'active open':'' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle"><div>Website</div></a>
                        <ul class="menu-sub">
                            @if ($permission->websiteconfig == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.webconfig')) ?'active':'' }}">
                                    <a href="{{ route('admin.webconfig') }}" class="menu-link">
                                        <div>Website Configuration</div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    @if ($permission->personalise_class == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.personaliseclass')) ?'active':'' }}">
                            <a href="{{ route('admin.personaliseclass') }}" class="menu-link">
                                <div>Personalise Class</div>
                            </a>
                        </li>
                    @endif

                    @if ($permission->storage_usage_setting == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.settings.storage_usage.index')) ?'active':'' }}">
                            <a href="{{ route('admin.settings.storage_usage.index') }}" class="menu-link">
                                <div>Storage Usage</div>
                            </a>
                        </li>
                    @endif

                    @if ($permission->db_backup_setting == 1)
                        <li class="menu-item {{ (request()->routeIs('admin.settings.db_backup.index')) ?'active':'' }}">
                            <a href="{{ route('admin.settings.db_backup.index') }}" class="menu-link">
                                <div>DB Backup</div>
                            </a>
                        </li>
                    @endif


                    @if ($permission->setup == 1)

                    <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active open':''}}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div>Setup</div>
                        </a>
                        <ul class="menu-sub">
                            @if ($permission->mailsetup == 1)
                                <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
                                    <a href="{{ route('admin.mail.setup.index') }}" class="menu-link"><div>Mail Setup</div></a>
                                </li>
                            @endif

                            <li class="menu-item">
                                <a href="" class="menu-link">
                                    <div>Facebool API Lead</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                @endif

                </ul>
            </li>
        @endif



    </ul>
  @else
    <div><p>Permission are not granted</p></div>
  @endif
@endif
