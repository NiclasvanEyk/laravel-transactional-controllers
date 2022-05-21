<?php

namespace NiclasVanEyk\TransactionalControllers\Tests\Unit;

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\TransactionalControllers\Tests\Fixtures\DynamicExampleController;
use NiclasVanEyk\TransactionalControllers\Tests\Fixtures\ExampleController;
use NiclasVanEyk\TransactionalControllers\Tests\Support\AssertsWhetherTransactionHappen;
use NiclasVanEyk\TransactionalControllers\Tests\TestCase;

class TransactionalControllersTest extends TestCase
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

    /** @test */
    public function it_does_not_open_a_transaction_without_the_annotation()
    {
        Route::get('/test', [ExampleController::class, 'nonTransactional']);

        $this
            ->assertNoTransactionsAreStarted()
            ->get('/test')
            ->assertStatus(200);
    }

    /** @test */
    public function it_still_honors_the_call_action_conventional_method()
    {
        Route::get('/test', [DynamicExampleController::class, 'test']);

        $this
            ->assertNoTransactionsAreStarted()
            ->get('/test')
            ->assertStatus(200);
    }

    /** @test */
    public function it_still_works_with_normal_closure_routes()
    {
        Route::get('/test', fn () => 'success!');

        $this
            ->assertNoTransactionsAreStarted()
            ->get('/test')
            ->assertStatus(200);
    }
}
