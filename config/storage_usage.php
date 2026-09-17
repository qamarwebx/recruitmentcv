<?php

use App\Services\StorageUsage\Adapters\DbFileColumnAdapter;
use App\Services\StorageUsage\Adapters\DiskPrefixAdapter;
use App\Services\StorageUsage\Adapters\FileManagerUsageAdapter;
use App\Services\StorageUsage\Adapters\NullAdapter;

return [

    /*
     * Module -> storage mapping. To wire up a new module: add an entry here
     * with a label, an adapter class, and that adapter's options - nothing
     * else in the Storage Usage module needs to change.
     *
     * Adapters:
     *  - NullAdapter: module has no file storage today, always reports zero.
     *  - DbFileColumnAdapter: usage is derived from filename/path columns on
     *    one or more tables, resolved against a physical directory and
     *    stat()'d (these legacy tables don't store file size). Each entry
     *    under "sources" also feeds the "uncategorized" scan below so files
     *    on disk that no DB row references are detected.
     *  - DiskPrefixAdapter: module already uses Laravel's Storage facade -
     *    usage is a full, authoritative directory listing under a disk
     *    prefix (no DB join, no uncategorized ambiguity for that prefix).
     *  - FileManagerUsageAdapter: reuses FileManagerQuotaService, which
     *    already tracks size per file in the database - no filesystem scan.
     */
    'modules' => [

        'leads' => [
            'label' => 'Leads',
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'contacts' => [
            'label' => 'Contacts',
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'allcontact_files',
                        'column' => 'file_path',
                        // Column already stores "uploads/contacts/xxx.ext" relative to /public.
                        'root' => 'public',
                        'scan_dir' => 'uploads/contacts',
                    ],
                ],
            ],
        ],

        'tasks' => [
            'label' => 'Tasks',
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'todos',
                        'column' => 'file_attachment',
                        // Column stores just the filename; lives under a legacy root
                        // that toggles between /public and /public_html.
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/todo',
                    ],
                ],
            ],
        ],

        'deal_pipeline' => [
            'label' => 'Deal Pipeline',
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'deal_files',
                        'column' => 'file_path',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/deal_files',
                    ],
                ],
            ],
        ],

        'booking' => [
            'label' => 'Booking',
            // Audited: bookings/bookingpayments/bookingnotifications/bookingorderstatuses/
            // bookingfilters/partnerbookingfilters/replacebookingcands/candidatebookinglimits
            // have no file columns, BookingController has no upload code, and no Booking
            // view has a file input. Its one Storage::url('pdf/'.$cv_name) call just
            // re-links to the Candidate module's own cv_file (already tracked under
            // "candidate" above) and is dead code besides - storage/app/public/pdf
            // doesn't exist and public/storage isn't symlinked.
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'orders' => [
            'label' => 'Orders',
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'employer' => [
            'label' => 'Employer',
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'employer_plus' => [
            'label' => 'Employer plus',
            // Distinct sidebar item/data pool from "Employer" (employerpluses table,
            // ~363 live rows) but audited the same way: employerpluses has no file
            // columns, EmployerController (shared by both) has no upload code, and
            // no Employer/Employer-plus view has a file input. The Work Agreement PDF
            // (PdfGeneratorController::generateworkagreement/downloadWorkAgreement) is
            // streamed straight to the browser ('D' = direct download) and never
            // written to disk, so it isn't a storage location either.
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'associate' => [
            'label' => 'Associate',
            // associates.logo is the only file-shaped column, but it has no
            // upload code anywhere (AssociateController never sets it), no view
            // has a file input, and it's 0/84 populated - dead/never-built field.
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'contact' => [
            'label' => 'Contact',
            // Contactplus/contactpluses (admin/contact-list) - distinct from
            // AllContact/"Contacts" above. contactpluses.office_logo is dead (no
            // upload code, hardcoded empty <img src=""> in show.blade.php, 0
            // populated rows). Its CSV bulk-export feature (ContactpController::
            // bulkExport) writes real CSVs under the legacy exports/ dir, but the
            // export_contact_plus_histories table it needs doesn't exist in the
            // DB, so the feature currently 500s before writing anything - not a
            // reliable source to track, and that directory is also shared with
            // AllContact's own export CSVs (would need filename-pattern splitting
            // to attribute correctly). Revisit if either export feature is fixed.
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'client' => [
            'label' => 'Client',
            // "Client" (admin/client) is backed by the users table, not a
            // separate customers table. CustomerController itself has no upload
            // code; the only real file column is users.photo, uploaded via the
            // front-end self-service "My Profile" page (DashboardController::
            // updateMyprofile). avatar_url is excluded - always an external
            // Google OAuth CDN link, never a local file.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'users',
                        'column' => 'photo',
                        'root' => 'legacy',
                        'scan_dir' => 'user/img/avatars',
                    ],
                ],
            ],
        ],

        'partner' => [
            'label' => 'Partner',
            // partners.profile (self-service portal photo, PartnerController::
            // accountUpdate2) is deliberately NOT a source here - it uploads into
            // the shared admin/assets/img/avatars bucket also used by Staff/Admin,
            // Candidate, Todo, and TeamMember, so claiming it under Partner would
            // double-count against whichever module ends up owning that folder.
            // Currently 0 populated rows anyway, so nothing is lost today.
            //
            // admin/assets/images/partner (below) is also written by the
            // unrelated Domains module (DomainController::websitelogoupdt ->
            // domains.website_logo/website_logo_ar) and admin/assets/images/
            // cv_setting is also written by the global CV-template settings
            // table (cvsettings.filename, PdfGeneratorController) - neither is
            // tracked as its own module yet, so their files correctly fall under
            // "Uncategorized" rather than being claimed by Partner, since these
            // sources only claim what partners/partnercvsettings actually
            // reference.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'partners',
                        'column' => 'logo',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/partner',
                    ],
                    [
                        'table' => 'partners',
                        'column' => 'website_logo',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/partner',
                    ],
                    [
                        'table' => 'partnercvsettings',
                        'column' => 'filename',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/cv_setting',
                    ],
                ],
            ],
        ],

        'candidate' => [
            'label' => 'Candidate',
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'candidatefiles',
                        'column' => 'filename',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/candidate',
                    ],
                    [
                        'table' => 'candidates',
                        'column' => 'cv_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/candidate',
                    ],
                    [
                        'table' => 'candidates',
                        'column' => 'photo_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/candidate',
                    ],
                    // Uploaded in the same CandidateController flows as cv_file/photo_file
                    // above, into the same directory - just never added as a source.
                    [
                        'table' => 'candidates',
                        'column' => 'pass_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/candidate',
                    ],
                    [
                        'table' => 'candidates',
                        'column' => 'lic_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/candidate',
                    ],
                    [
                        'table' => 'candidates',
                        'column' => 'pass_back_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/candidate',
                    ],
                    [
                        'table' => 'candidates',
                        'column' => 'musaned_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/candidate',
                    ],
                    // System-generated CV PDFs (PdfGeneratorController "CV ready for
                    // download" flow, PDF::Output(..., 'F') = saved to disk) - a
                    // completely separate, previously untracked directory.
                    [
                        'table' => 'candidates',
                        'column' => 'cv_execute_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/pdf',
                    ],
                    // Same generator, partner-branded variant (companycvexecutes.cv_file),
                    // saved under its own "partner" subfolder.
                    [
                        'table' => 'companycvexecutes',
                        'column' => 'cv_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/pdf/partner',
                    ],
                    // Candidate payment receipts (admin/candidate/transaction/list,
                    // a submenu under Candidate, not its own module) -
                    // CandidateController::candamtStr, another previously
                    // untracked directory.
                    [
                        'table' => 'paymentcands',
                        'column' => 'payment_slip',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/payment/candidate',
                    ],
                ],
            ],
        ],

        'testimonial' => [
            'label' => 'Testimonial',
            'adapter' => DiskPrefixAdapter::class,
            'options' => [
                'disk' => 'public',
                'prefix' => 'testimonials',
            ],
        ],

        'fund_advance' => [
            'label' => 'Fund & Advance',
            // fund_advance_transactions.attachment and fund_advance_settlements.
            // attachment both use the modern Storage::disk('public') pattern
            // (storeAs 'fund_advance/transactions' / 'fund_advance/settlements')
            // - no legacy base_path toggle involved at all. One recursive prefix
            // covers both subfolders, same technique as Testimonial above.
            // Currently 0 bytes (no attachments uploaded yet) but live, wired-up
            // upload functionality - tracked from day one so it doesn't become
            // another invisible bucket once staff start using it.
            'adapter' => DiskPrefixAdapter::class,
            'options' => [
                'disk' => 'public',
                'prefix' => 'fund_advance',
            ],
        ],

        'whatsapp_business' => [
            'label' => 'Whatsapp Business',
            // Campaign (campaignlists.temp_file) and Template (whatsappcamptemplates.
            // file) both save into admin/assets/images/template. Campaign copies the
            // template's own filename when built from an existing template (same
            // physical bytes, two DB rows) - safe to combine as multiple sources on
            // ONE module, since DbFileColumnAdapter dedupes by physical path within
            // a single compute() call, same as Candidate's multiple sources.
            // API Setup (whatsappapis) has no file column.
            //
            // NOT tracked: WhatsappApiController::sendTest()'s "test send" attachment
            // upload saves into admin/assets/images/whatsapp with NO DB column ever
            // referencing the filename - neither DbFileColumnAdapter (no DB row to
            // join) nor DiskPrefixAdapter (this directory isn't Storage-facade based)
            // can track a raw legacy directory with zero DB traceability. Small today
            // (~4MB) - revisit with a dedicated adapter if it grows.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'campaignlists',
                        'column' => 'temp_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/template',
                    ],
                    [
                        'table' => 'whatsappcamptemplates',
                        'column' => 'file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/template',
                    ],
                ],
            ],
        ],

        'whatsapp_meta' => [
            'label' => 'Whatsapp Meta',
            // Template (metawhatsapptemplates.whatsapp_file) - same physical
            // directory as Whatsapp Business but a separate sidebar module/table,
            // no filename overlap found with the Business sources. Meta Campaign
            // (metawhatsappcampaigns) has no file column - its header_image/video/
            // document fields are plain text URL inputs, not uploads. Meta API
            // (metawhatsappapis) has no file column either.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'metawhatsapptemplates',
                        'column' => 'whatsapp_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/template',
                    ],
                ],
            ],
        ],

        'sms_campaign' => [
            'label' => 'SMS Campaign',
            // sms_templates.sms_file shares admin/assets/images/template with
            // Whatsapp Business/Meta/Notifications/Emp Meta Automation below -
            // safe, distinct table/column, currently 0 populated rows but
            // correctly wired for when a template is uploaded. sms_campaigns/
            // sms_campaign_responses/sms_apis have no file columns.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'sms_templates',
                        'column' => 'sms_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/template',
                    ],
                ],
            ],
        ],

        'email_campaign' => [
            'label' => 'Email Campaign',
            // email_templates.attachment - dedicated directory, not shared with
            // anything else. Fixed EmailTemplateController's inverted
            // base_path_status check in templateStore/templateUpdate (it read
            // "==0 -> public" while its own deleteAttachment() correctly reads
            // "==1 -> public") so uploads/deletes/dashboard now all agree.
            // email_campaigns.attachment is NOT a separate upload - it just
            // copies the chosen template's URL at compose time, GenericCampaignMail
            // re-references the template's own file, never writes a new one.
            // email_smtps/autoemailnotifications/scheduled_send_email_automation
            // have no file columns.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'email_templates',
                        'column' => 'attachment',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/email-template',
                    ],
                ],
            ],
        ],

        'front_website_config' => [
            'label' => 'Front End Website Config',
            // frontendwebsiteconfigs.english_logo/arabic_logo (Settings > Website >
            // Front End Qamarhire) - a new, previously untracked directory
            // (user/img/logo), sole writer confirmed via grep. Qamarhire
            // Configuration, QamarJob FB Meta Configuration, and IP Tracker (the
            // other 3 Website sub-pages) have no file columns.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'frontendwebsiteconfigs',
                        'column' => 'english_logo',
                        'root' => 'legacy',
                        'scan_dir' => 'user/img/logo',
                    ],
                    [
                        'table' => 'frontendwebsiteconfigs',
                        'column' => 'arabic_logo',
                        'root' => 'legacy',
                        'scan_dir' => 'user/img/logo',
                    ],
                ],
            ],
        ],

        'domains' => [
            'label' => 'Domains',
            // domains.website_logo/website_logo_ar (Settings > Domains) - shares
            // admin/assets/images/partner with the "partner" module's
            // partners.logo/website_logo (different table, no filename overlap
            // found; both sets of sources independently claim only what they
            // themselves reference, so no double-count).
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'domains',
                        'column' => 'website_logo',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/partner',
                    ],
                    [
                        'table' => 'domains',
                        'column' => 'website_logo_ar',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/partner',
                    ],
                ],
            ],
        ],

        'cv_setting' => [
            'label' => 'CV Setting',
            // cvsettings.filename (Settings > CV Setting, global CV-template
            // image slots) - shares admin/assets/images/cv_setting with the
            // "partner" module's partnercvsettings.filename (different table).
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'cvsettings',
                        'column' => 'filename',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/cv_setting',
                    ],
                ],
            ],
        ],

        'notifications' => [
            'label' => 'Notifications',
            // templatenotification.file (Settings > Notifications) - shares
            // admin/assets/images/template with several other modules above.
            // Fixed a filename-sanitization mismatch in BackEndController::
            // templateStore/templateUpdt: the sanitized name was written to
            // disk but the ORIGINAL (unsanitized) name was saved to the DB
            // column, so any filename containing a space would never resolve
            // back to its real file. Now both store the same sanitized name.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'templatenotification',
                        'column' => 'file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/template',
                    ],
                ],
            ],
        ],

        'emp_meta_automation' => [
            'label' => 'Emp Meta Automation',
            // metanotifications.whatsapp_file (Settings > Emp Meta Automation) -
            // shares admin/assets/images/template too; correctly wired (sanitized
            // filename stored), currently 0 populated rows. Meta Automation
            // (autometanotifications) has no file column - not tracked separately.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'metanotifications',
                        'column' => 'whatsapp_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/template',
                    ],
                ],
            ],
        ],

        'file_manager' => [
            'label' => 'File Manager',
            'adapter' => FileManagerUsageAdapter::class,
            'options' => [],
        ],

        'expense' => [
            'label' => 'Expense',
            // receipt_file stores a JSON array of filenames (multi-file upload,
            // ExpenseController::store/update always json_encode()s), which
            // DbFileColumnAdapter now decodes - see its own doc comment. Also
            // fixed ExpenseController's inverted base_path_status check (it read
            // "==0 -> public" where every other legacy-rooted controller reads
            // "==1 -> public") so new uploads land in the same root this "legacy"
            // resolver expects; one pre-existing stray file from before that fix
            // sits in the other root and won't be counted.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'expenses',
                        'column' => 'receipt_file',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/payment/expense',
                    ],
                ],
            ],
        ],

        'payment' => [
            'label' => 'Payment',
            // payments.payment_slip (admin/payment/list, partner-invoice payments)
            // - genuinely distinct from paymentcands.payment_slip already tracked
            // under "candidate" (different table/FK/directory, same column name
            // by coincidence). Correct, non-inverted base_path_status check.
            'adapter' => DbFileColumnAdapter::class,
            'options' => [
                'sources' => [
                    [
                        'table' => 'payments',
                        'column' => 'payment_slip',
                        'root' => 'legacy',
                        'scan_dir' => 'admin/assets/images/payment/slip',
                    ],
                ],
            ],
        ],

        'invoice' => [
            'label' => 'Sales Invoice',
            // invoices/invoice_payment/payment_invoice/invoiceaccountdets have no
            // file columns, and the invoice PDF (InvoiceController and the
            // partner-portal PartnerInvoiceController) is generated with DomPDF's
            // ->stream() - rendered straight to the browser, never written to
            // disk. No file input exists in any Sales Invoice view either.
            'adapter' => NullAdapter::class,
            'options' => [],
        ],

        'hr_management' => [
            'label' => 'HR Management',
            // Covers all 4 HR Management sidebar sub-pages (Dashboard, Attendance,
            // Payroll, Settings). Audited: attendance_logs/attendance_approval_logs/
            // attendance_save_filters, payrolls/salary_settings/admins.monthly_salary,
            // and holidays have no file-shaped columns; AttendanceController/
            // SalaryController/HolidayController have no upload code; no view in
            // any of the 4 sub-pages has a file input. Attendance Slip and Salary
            // Slip PDFs (Pdf::loadView()->download()/->stream()) are streamed
            // straight to the browser and never written to disk.
            'adapter' => NullAdapter::class,
            'options' => [],
        ],
    ],

    /*
     * Directories that belong to a DbFileColumnAdapter module above (by its
     * "scan_dir" entries) are diffed against the files those DB rows
     * reference; anything left over is reported under "Uncategorized".
     * Directories owned outright by a DiskPrefixAdapter/FileManagerUsageAdapter
     * are already fully accounted for and are not scanned again here.
     */
    'uncategorized_label' => 'Uncategorized',

    /*
     * Cache key used to guard against overlapping recalculations triggered
     * by the scheduler and a manual "Recalculate" click at the same time.
     */
    'lock_key' => 'storage-usage:recalculating',
    'lock_seconds' => 300,
];
