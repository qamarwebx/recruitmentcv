<?php

namespace Tests\Feature\Salary;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\Payroll;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uses DatabaseTransactions (not RefreshDatabase) - same reasoning as
 * FileManagerTest: this app's production database is also its only
 * configured connection, so these tests run against real payroll rows and
 * roll back afterwards rather than needing a separate test database.
 */
class PayrollMarkPaidTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Storage::fake('public');
    }

    /**
     * Requests dispatched via route() resolve against APP_URL
     * (qamarhire.com), but RedirectIfNotAdminDomain only lets /admin/*
     * through on the crm.qamarhire.com host - so the admin routes have to
     * be hit with that host explicitly, same as the browser does in
     * production.
     */
    private function adminUrl(string $routeName, ...$params): string
    {
        return 'https://crm.qamarhire.com' . parse_url(route($routeName, ...$params), PHP_URL_PATH);
    }

    /**
     * Reproduces the reported bug: "The selected payroll id is invalid."
     * happened because a row's id can go stale in an already-loaded table
     * (deleted, or replaced by "Generate Payroll" with a fresh id) without
     * a page refresh. That must now fail as a clear, recoverable 404 -
     * never the generic exists:payrolls,id validation message.
     */
    public function test_stale_or_deleted_payroll_id_fails_with_a_clear_recoverable_error()
    {
        $admin = Admin::where('user_type', 1)->first();
        $this->assertNotNull($admin, 'expected at least one super admin to exist');

        $staleId = ((int) Payroll::max('id')) + 1000;
        $this->assertNull(Payroll::find($staleId));

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.payroll.mark_paid'), [
            'payroll_id' => $staleId,
            'payment_slip' => UploadedFile::fake()->image('slip.jpg', 100, 100),
        ]);

        $response->assertStatus(404);
        $response->assertJsonMissingPath('errors.payroll_id');
        $this->assertStringContainsString('no longer available', $response->json('res'));
    }

    public function test_pending_payroll_can_be_marked_paid_with_a_slip()
    {
        $admin = Admin::where('user_type', 1)->first();
        $payroll = Payroll::where('payment_status', Payroll::PAYMENT_PENDING)->first();

        $this->assertNotNull($admin);
        $this->assertNotNull($payroll, 'expected at least one Pending payroll row to test with');

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.payroll.mark_paid'), [
            'payroll_id' => $payroll->id,
            'payment_slip' => UploadedFile::fake()->image('slip.jpg', 100, 100),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['payment_status' => Payroll::PAYMENT_PAID]);

        $payroll->refresh();
        $this->assertSame(Payroll::PAYMENT_PAID, $payroll->payment_status);
        $this->assertSame('locked', $payroll->status, 'marking Paid should also lock the payroll');
        $this->assertNotNull($payroll->payment_slip);
        $this->assertNotNull($payroll->paid_by);
        $this->assertNotNull($payroll->paid_at);
        Storage::disk('public')->assertExists('payroll/payment_slips/' . $payroll->payment_slip);
    }

    public function test_salary_slip_upload_is_still_required()
    {
        $admin = Admin::where('user_type', 1)->first();
        $payroll = Payroll::where('payment_status', Payroll::PAYMENT_PENDING)->first();

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.payroll.mark_paid'), [
            'payroll_id' => $payroll->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['payment_slip']);

        $payroll->refresh();
        $this->assertSame(Payroll::PAYMENT_PENDING, $payroll->payment_status);
    }

    public function test_already_paid_payroll_is_rejected_and_not_reset()
    {
        $admin = Admin::where('user_type', 1)->first();
        $payroll = Payroll::where('payment_status', Payroll::PAYMENT_PENDING)->first();

        $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.payroll.mark_paid'), [
            'payroll_id' => $payroll->id,
            'payment_slip' => UploadedFile::fake()->image('slip1.jpg', 100, 100),
        ]);

        $payroll->refresh();
        $originalSlip = $payroll->payment_slip;
        $originalPaidAt = $payroll->paid_at;

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.payroll.mark_paid'), [
            'payroll_id' => $payroll->id,
            'payment_slip' => UploadedFile::fake()->image('slip2.jpg', 100, 100),
        ]);

        $response->assertStatus(422);

        $payroll->refresh();
        $this->assertSame($originalSlip, $payroll->payment_slip, 'an already-paid slip must not be overwritten');
        $this->assertEquals($originalPaidAt, $payroll->paid_at, 'paid_at must not be reset by a repeat call');
    }

    public function test_payroll_listing_and_filters_are_unaffected()
    {
        $admin = Admin::where('user_type', 1)->first();

        $response = $this->actingAs($admin, 'admin')->get($this->adminUrl('admin.salary.payroll.json'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'draw', 'recordsTotal', 'recordsFiltered']);
    }
}
