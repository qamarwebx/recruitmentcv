<?php

namespace Tests\Feature\Salary;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\AdvancePayment;
use App\Models\Payroll;
use App\Services\AdvancePaymentService;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uses DatabaseTransactions (not RefreshDatabase) - same reasoning as
 * PayrollMarkPaidTest: this app's production database is also its only
 * configured connection, so these tests run against it and roll back
 * afterwards. Staff members are created fresh per test via Admin::factory()
 * so nothing here depends on (or pollutes) real payroll/staff data.
 */
class AdvancePaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Storage::fake('public');
    }

    private function adminUrl(string $routeName, ...$params): string
    {
        return 'https://crm.qamarhire.com' . parse_url(route($routeName, ...$params), PHP_URL_PATH);
    }

    private function superAdmin(): Admin
    {
        $admin = Admin::where('user_type', 1)->first();
        $this->assertNotNull($admin, 'expected at least one super admin to exist');

        return $admin;
    }

    private function staff(array $overrides = []): Admin
    {
        return Admin::factory()->create(array_merge(['monthly_salary' => 30000], $overrides));
    }

    private function payrollService(): PayrollService
    {
        return app(PayrollService::class);
    }

    private function advanceService(): AdvancePaymentService
    {
        return app(AdvancePaymentService::class);
    }

    /** 1. Add advance for staff before payroll generation. */
    public function test_advance_created_before_payroll_is_pending_until_generated()
    {
        $staff = $this->staff();

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 1, 15, 10, 0), 3000, 'Test advance', $this->superAdmin());

        $this->assertNull($advance->payroll_id);
        $this->assertSame('Pending', $advance->status);
    }

    /** 2. Generate payroll -> advance automatically applied. */
    public function test_generating_payroll_automatically_applies_matching_advance()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 1, 15, 10, 0), 3000, null, $admin);

        $payroll = $this->payrollService()->generate($staff, 1, 2024, $admin);

        $advance->refresh();
        $this->assertSame($payroll->id, $advance->payroll_id);
        $this->assertSame('Applied', $advance->status);
        $this->assertEquals(3000.0, (float) $payroll->advance_deduction);
        $this->assertEquals(
            round((float) $payroll->net_payable + (float) $payroll->extra_paid - 3000, 2),
            $payroll->total_paid
        );
    }

    /** 3. Add advance after payroll exists -> same-month draft payroll updates. */
    public function test_advance_added_after_draft_payroll_exists_still_applies()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $payroll = $this->payrollService()->generate($staff, 2, 2024, $admin);
        $this->assertEquals(0.0, (float) $payroll->advance_deduction);

        $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 2, 10, 9, 0), 1500, null, $admin);

        $payroll->refresh();
        $this->assertEquals(1500.0, (float) $payroll->advance_deduction);
    }

    /** 4. Multiple advances -> correctly summed. */
    public function test_multiple_advances_for_same_period_are_summed()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 3, 5, 9, 0), 1000, null, $admin);
        $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 3, 20, 9, 0), 2000, null, $admin);

        $payroll = $this->payrollService()->generate($staff, 3, 2024, $admin);

        $this->assertEquals(3000.0, (float) $payroll->advance_deduction);
    }

    /** 5. Different staff -> no cross-impact. */
    public function test_advance_for_one_staff_never_affects_another()
    {
        $staffA = $this->staff();
        $staffB = $this->staff();
        $admin = $this->superAdmin();

        $this->advanceService()->create($staffA, \Carbon\Carbon::create(2024, 4, 5, 9, 0), 5000, null, $admin);

        $payrollB = $this->payrollService()->generate($staffB, 4, 2024, $admin);

        $this->assertEquals(0.0, (float) $payrollB->advance_deduction);
    }

    /** 6. Different month -> no cross-impact. */
    public function test_advance_for_one_month_never_affects_another_month()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 5, 5, 9, 0), 4000, null, $admin);

        $payrollJune = $this->payrollService()->generate($staff, 6, 2024, $admin);

        $this->assertEquals(0.0, (float) $payrollJune->advance_deduction);
    }

    /** 7. Existing "Any Extra Paid" -> formula remains Net Payable + Extra Paid - Advance. */
    public function test_total_paid_formula_combines_extra_paid_and_advance()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 7, 5, 9, 0), 3000, null, $admin);
        $this->payrollService()->generate($staff, 7, 2024, $admin);

        $payroll = Payroll::where('admin_id', $staff->id)->where('month', 7)->where('year', 2024)->first();

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.payroll.mark_paid'), [
            'payroll_id' => $payroll->id,
            'payment_slip' => UploadedFile::fake()->image('slip.jpg', 100, 100),
            'extra_paid' => 500,
        ]);

        $response->assertStatus(200);

        $payroll->refresh();
        $this->assertEquals(500.0, (float) $payroll->extra_paid);
        $this->assertEquals(3000.0, (float) $payroll->advance_deduction);
        $this->assertEquals(
            round((float) $payroll->net_payable + 500 - 3000, 2),
            $payroll->total_paid
        );
    }

    /** 11. Edit advance -> draft payroll recalculates. */
    public function test_editing_advance_amount_recalculates_draft_payroll()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 8, 5, 9, 0), 1000, null, $admin);
        $payroll = $this->payrollService()->generate($staff, 8, 2024, $admin);
        $this->assertEquals(1000.0, (float) $payroll->advance_deduction);

        $this->advanceService()->update($advance, $staff, \Carbon\Carbon::create(2024, 8, 5, 9, 0), 2500, 'updated', $admin);

        $payroll->refresh();
        $this->assertEquals(2500.0, (float) $payroll->advance_deduction);
    }

    /** 11. Delete advance -> draft payroll recalculates. */
    public function test_deleting_advance_recalculates_draft_payroll()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 9, 5, 9, 0), 1200, null, $admin);
        $payroll = $this->payrollService()->generate($staff, 9, 2024, $admin);
        $this->assertEquals(1200.0, (float) $payroll->advance_deduction);

        $this->advanceService()->delete($advance);

        $payroll->refresh();
        $this->assertEquals(0.0, (float) $payroll->advance_deduction);
        $this->assertDatabaseMissing('advance_payments', ['id' => $advance->id]);
    }

    /** 12. Locked/Final/Paid payroll protection - edit blocked. */
    public function test_editing_advance_linked_to_locked_payroll_is_blocked()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 10, 5, 9, 0), 1000, null, $admin);
        $payroll = $this->payrollService()->generate($staff, 10, 2024, $admin);
        $this->payrollService()->lock($payroll, $admin);

        $this->expectException(\RuntimeException::class);
        $this->advanceService()->update($advance, $staff, \Carbon\Carbon::create(2024, 10, 5, 9, 0), 2000, null, $admin);
    }

    /** 12. Locked/Final/Paid payroll protection - delete blocked. */
    public function test_deleting_advance_linked_to_locked_payroll_is_blocked()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 11, 5, 9, 0), 1000, null, $admin);
        $payroll = $this->payrollService()->generate($staff, 11, 2024, $admin);
        $this->payrollService()->lock($payroll, $admin);

        $this->expectException(\RuntimeException::class);
        $this->advanceService()->delete($advance);

        $payroll->refresh();
        $this->assertEquals(1000.0, (float) $payroll->advance_deduction, 'locked payroll figure must stay frozen');
    }

    /** 12. A new advance created AFTER lock must never silently modify the locked payroll. */
    public function test_advance_created_after_payroll_is_locked_is_not_auto_applied()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $payroll = $this->payrollService()->generate($staff, 12, 2024, $admin);
        $this->payrollService()->lock($payroll, $admin);

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 12, 20, 9, 0), 5000, null, $admin);

        $this->assertNull($advance->payroll_id);
        $this->assertSame('Pending', $advance->status);

        $payroll->refresh();
        $this->assertEquals(0.0, (float) $payroll->advance_deduction, 'locked payroll must not be silently modified');
    }

    /** Deleting the payroll unlinks its advances so a regeneration can pick them up again. */
    public function test_deleting_payroll_unlinks_advances_for_future_regeneration()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $advance = $this->advanceService()->create($staff, \Carbon\Carbon::create(2025, 1, 5, 9, 0), 900, null, $admin);
        $payroll = $this->payrollService()->generate($staff, 1, 2025, $admin);
        $this->assertSame($payroll->id, $advance->fresh()->payroll_id);

        $payroll->delete();

        $advance->refresh();
        $this->assertNull($advance->payroll_id);
        $this->assertSame('Pending', $advance->status);

        $regenerated = $this->payrollService()->generate($staff, 1, 2025, $admin);
        $this->assertEquals(900.0, (float) $regenerated->advance_deduction);
    }

    /** 15. Regenerating a draft payroll must never double-count an advance. */
    public function test_regenerating_draft_payroll_does_not_double_count_advance()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $this->advanceService()->create($staff, \Carbon\Carbon::create(2025, 2, 5, 9, 0), 700, null, $admin);

        $this->payrollService()->generate($staff, 2, 2025, $admin);
        $payroll = $this->payrollService()->generate($staff, 2, 2025, $admin);

        $this->assertEquals(700.0, (float) $payroll->advance_deduction);
        $this->assertSame(1, AdvancePayment::where('admin_id', $staff->id)->where('month', 2)->where('year', 2025)->count());
    }

    /** 13. December/January year boundary - each period is independent. */
    public function test_december_and_january_advances_stay_in_their_own_year_boundary()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 12, 31, 23, 0), 1000, null, $admin);
        $this->advanceService()->create($staff, \Carbon\Carbon::create(2025, 1, 1, 1, 0), 2000, null, $admin);

        $december = $this->payrollService()->generate($staff, 12, 2024, $admin);
        $january = $this->payrollService()->generate($staff, 1, 2025, $admin);

        $this->assertEquals(1000.0, (float) $december->advance_deduction);
        $this->assertEquals(2000.0, (float) $january->advance_deduction);
    }

    /** HTTP validation: staff, date, and a positive amount are mandatory. */
    public function test_store_endpoint_validates_required_fields_and_amount()
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.advance.store'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['admin_id', 'advance_date', 'amount']);

        $staff = $this->staff();
        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.advance.store'), [
            'admin_id' => $staff->id,
            'advance_date' => '2024-06-01 10:00',
            'amount' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }

    /** HTTP: a valid submission creates the advance and it shows up in the list. */
    public function test_store_endpoint_creates_advance_and_lists_it()
    {
        $admin = $this->superAdmin();
        $staff = $this->staff();

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.advance.store'), [
            'admin_id' => $staff->id,
            'advance_date' => '2024-06-01 10:00',
            'amount' => 2500,
            'remarks' => 'Emergency advance',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('advance_payments', [
            'admin_id' => $staff->id,
            'amount' => 2500,
            'month' => 6,
            'year' => 2024,
        ]);

        $listResponse = $this->actingAs($admin, 'admin')->get($this->adminUrl('admin.salary.advance.json'));
        $listResponse->assertStatus(200);
        $listResponse->assertJsonStructure(['data', 'draw', 'recordsTotal', 'recordsFiltered']);
    }

    /** Server-side authorization: staff without Payroll access cannot manage advances. */
    public function test_store_endpoint_rejects_staff_without_payroll_access()
    {
        $unauthorized = Admin::factory()->create();
        $staff = $this->staff();

        $response = $this->actingAs($unauthorized, 'admin')->post($this->adminUrl('admin.salary.advance.store'), [
            'admin_id' => $staff->id,
            'advance_date' => '2024-06-01 10:00',
            'amount' => 1000,
        ]);

        $response->assertStatus(403);
    }

    /** A staff id/amount must be trusted from the server, not the browser - an inactive staff id is rejected. */
    public function test_store_endpoint_rejects_inactive_staff()
    {
        $admin = $this->superAdmin();
        $inactiveStaff = $this->staff(['status' => 0]);

        $response = $this->actingAs($admin, 'admin')->post($this->adminUrl('admin.salary.advance.store'), [
            'admin_id' => $inactiveStaff->id,
            'advance_date' => '2024-06-01 10:00',
            'amount' => 1000,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['admin_id']);
    }

    /** Payment Details breakdown endpoint only returns advances linked to that exact payroll. */
    public function test_payroll_advances_endpoint_returns_only_linked_advances()
    {
        $staff = $this->staff();
        $admin = $this->superAdmin();

        $this->advanceService()->create($staff, \Carbon\Carbon::create(2024, 6, 5, 9, 0), 1800, 'note', $admin);
        $payroll = $this->payrollService()->generate($staff, 6, 2024, $admin);

        $response = $this->actingAs($admin, 'admin')->get($this->adminUrl('admin.salary.payroll.advances', $payroll->id));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'advances');
        $response->assertJson(['total' => 1800.0]);
    }
}
