<?php

namespace NiclasVanEyk\TransactionalControllers\Tests\Support;

use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use NiclasVanEyk\TransactionalControllers\Tests\TestCase;

/** @mixin TestCase */
trait AssertsWhetherTransactionHappen
{
    /** @var array<string, int> */
    private array $transactionsCommitedPerConnection = [];

    public function setUp(): void
    {
        parent::setup();

        Event::listen(function (TransactionCommitted $event) {
            $connection = $event->connectionName;

            if (! array_key_exists($connection, $this->transactionsCommitedPerConnection)) {
                $this->transactionsCommitedPerConnection[$connection] = 1;
            } else {
                $this->transactionsCommitedPerConnection[$connection]++;
            }
        });
    }

    public function assertNoTransactionsAreStarted(): static
    {
        Event::listen(TransactionBeginning::class, function () {
            $this->fail('No transaction should have been started!');
        });

        return $this;
    }

    public function assertTransactionsWereCommitted(
        int $amount,
        ?string $connection = null
    ): static {
        $connection ??= config('database.default');
        $actual = Arr::get($this->transactionsCommitedPerConnection, $connection, 0);

        self::assertEquals($amount, $actual);

        return $this;
    }
}
