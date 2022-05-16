<?php

namespace NiclasVanEyk\TransactionalRoutes\Tests\Unit;

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\TransactionalRoutes\Tests\Fixtures\ExampleController;
use NiclasVanEyk\TransactionalRoutes\Tests\Support\AssertsWhetherTransactionHappen;
use NiclasVanEyk\TransactionalRoutes\Tests\TestCase;

class TransactionalRoutesTest extends TestCase
{
    use AssertsWhetherTransactionHappen;

    /** @test */
    public function it_runs_a_transaction_if_annotated()
    {
        Route::get('/test', [ExampleController::class, 'defaultTransactional']);

        $this->get('/test')->assertSuccessful();
        $this->assertTransactionsWereCommitted(amount: 1);
    }

    /** @test */
    public function it_opens_a_transaction_on_a_given_connection()
    {
        Route::get('/test', [ExampleController::class, 'otherTransactional']);

        $this->get('/test')->assertSuccessful();
        $this->assertTransactionsWereCommitted(amount: 0); // default connection
        $this->assertTransactionsWereCommitted(amount: 1, connection: 'other');
    }

    /** @test */
    public function it_does_not_open_a_transaction_for_failing_form_requests()
    {
        Route::get('/test', [ExampleController::class, 'formRequestTransactional']);

        $this
            ->assertNoTransactionsAreStarted()
            ->get('/test', headers: ['accept' => 'application/json'])
            ->assertStatus(422);
    }
}
