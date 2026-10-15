<?php

namespace Tests\Feature;

use App\Models\Partner;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Partner Portal -> Orders: the "Ordered from QamarHire.com" badge in the card
 * and table views (worker/partner/orders/source-badge), shown only for orders
 * whose bookings.order_source is 'qamarhire' (written by qamarhire.com's
 * checkout). Search, filters, pagination and the AJAX reload keep working.
 *
 * Live MySQL inside DatabaseTransactions (rolled back); queue, mail and HTTP
 * faked. Run only this file:
 *   php artisan test tests/Feature/PartnerOrdersSourceBadgeTest.php
 */
class PartnerOrdersSourceBadgeTest extends TestCase
{
    use DatabaseTransactions;

    private const HOST = 'https://recruitmentcv.com';

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Mail::fake();
        Http::preventStrayRequests();
    }

    public function test_badge_only_on_qamarhire_orders_in_card_and_table_views()
    {
        $partner = $this->makePartner();
        $qh = $this->makeBooking($partner, ['order_source' => 'qamarhire']);
        $direct = $this->makeBooking($partner, ['order_source' => null]);

        $html = $this->actingAs($partner, 'partner')->get(self::HOST . '/partner/orders')->assertOk()->getContent();

        $this->assertStringContainsString('#' . $qh->reference_no, $html);
        $this->assertStringContainsString('#' . $direct->reference_no, $html);
        $this->assertStringContainsString('data-wp-view-panel="table"', $html);
        // One badge in the card view + one in the table view - for the qamarhire.com order only.
        $this->assertSame(2, substr_count($html, 'data-order-source="qamarhire"'));
        $this->assertStringContainsString('Ordered from QamarHire.com', $html);

        // Card: the badge sits under that order's number pill (and not under the other one).
        $this->assertMatchesRegularExpression('/#' . $qh->reference_no . '<\/span>\s*<span class="wp-source-badge"/', $html);
        $this->assertDoesNotMatchRegularExpression('/#' . $direct->reference_no . '<\/span>\s*<span class="wp-source-badge"/', $html);
        // Table: in that order's first cell.
        $this->assertMatchesRegularExpression('/wp-cell-title">#' . $qh->reference_no . '<\/span>\s*<span class="wp-source-badge"/', $html);
        $this->assertDoesNotMatchRegularExpression('/wp-cell-title">#' . $direct->reference_no . '<\/span>\s*<span class="wp-source-badge"/', $html);
    }

    public function test_search_filter_and_pagination_reload_keep_the_badge()
    {
        $partner = $this->makePartner();
        $qh = $this->makeBooking($partner, ['order_source' => 'qamarhire']);
        $direct = $this->makeBooking($partner, ['order_source' => null]);
        $this->actingAs($partner, 'partner');

        // AJAX reload (search / filters) returns the partial with the badge for the match only.
        $partial = $this->get(self::HOST . '/partner/orders?search=' . $qh->reference_no, ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->getContent();
        $this->assertStringContainsString('#' . $qh->reference_no, $partial);
        $this->assertStringNotContainsString('#' . $direct->reference_no, $partial);
        $this->assertSame(2, substr_count($partial, 'data-order-source="qamarhire"'));

        $partial = $this->get(self::HOST . '/partner/orders?search=' . $direct->reference_no, ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->getContent();
        $this->assertStringContainsString('#' . $direct->reference_no, $partial);
        $this->assertSame(0, substr_count($partial, 'data-order-source="qamarhire"'));

        $this->get(self::HOST . '/partner/orders?payment_status=0&page=1', ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $this->get(self::HOST . '/partner/orders?page=2')->assertOk();
        $this->get(self::HOST . '/partner/orders/' . $qh->id)->assertOk();
    }

    public function test_another_partner_never_sees_the_order()
    {
        $owner = $this->makePartner();
        $qh = $this->makeBooking($owner, ['order_source' => 'qamarhire']);

        $html = $this->actingAs($this->makePartner(), 'partner')->get(self::HOST . '/partner/orders')->assertOk()->getContent();
        $this->assertStringNotContainsString('#' . $qh->reference_no . '<', $html);
        $this->assertSame(0, substr_count($html, 'data-order-source="qamarhire"'));
    }

    // ------------------------------------------------------------------

    private function makePartner(): Partner
    {
        $p = new Partner();
        $p->forceFill([
            'rec_off_name' => 'Test Office ' . Str::random(5),
            'email' => 'office' . Str::lower(Str::random(10)) . '@example.test',
            'password' => bcrypt('secret'),
            'status' => 1,
            'portal_status' => 1,
            'registration_status' => 1,
            'mobile_verified_at' => now(),
            'email_verified_at' => now(),
        ])->save();
        $p->forceFill(['partner_id' => $p->id])->save();

        return $p->fresh();
    }

    private function makeBooking(Partner $partner, array $attrs = [])
    {
        $candId = DB::table('candidates')->insertGetId([
            'cand_name' => 'Test Candidate ' . Str::random(6),
            'pass_no' => 'T' . random_int(1000000, 9999999),
            'pass_type' => 'ECNR',
            'admin_id' => 1,
            'status' => 1,
            'publish' => 1,
            'cv_execute' => 0,
            'isdelete' => 0,
            'slug_text' => 'test-cand-' . Str::random(10),
            'reference_no' => random_int(900000, 999999),
            'cand_status' => DB::table('candidate_statuses')->min('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Customer ' . Str::random(5),
            'email' => 'cust' . Str::lower(Str::random(10)) . '@example.test',
            'password' => bcrypt('secret'),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $id = DB::table('bookings')->insertGetId(array_merge([
            'cand_id' => $candId,
            'user_id' => $userId,
            'partner_id' => $partner->id,
            'reference_no' => random_int(7000000, 7999999),
            'booking_date' => now(),
            'ord_status_id' => '1',
            'status' => 0,
            'booking_status' => 0,
            'visa_status' => 0,
            'payment_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ], $attrs));

        return DB::table('bookings')->find($id);
    }
}
