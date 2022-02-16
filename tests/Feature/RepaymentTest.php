<?php

use App\Models\Repayment;
use Illuminate\Support\Carbon;

it('can return a principal amount', function (Repayment $repayment, float $principal) {
    expect($repayment->principal)->toEqual(round($principal, 2));
})->with([
    [fn() => Repayment::factory()->create(['due' => 100, 'interest' => 5]), 100 - 5],
    [fn() => Repayment::factory()->create(['due' => 123442.2212, 'interest' => 21.3223]), 123442.2212 - 21.3223],
    [fn() => Repayment::factory()->create(['due' => 87532.287, 'interest' => 43.2873]), 87532.287 - 43.2873],
]);

test('is_paid', function (?Carbon $paidOn, bool $isPaid) {
    $repayment = Repayment::factory(['paid_on' => $paidOn])->create()->refresh();
    expect($repayment->is_paid)->toBe($isPaid);
})->with([
    'paid_on = now()' => [now(), true],
    'paid_on = now()->subMonth()' => [now()->subMonth(), true],
    'paid_on = now()->addYear()' => [now()->addYear(), true],
    'paid_on = null' => [null, false]
]);
