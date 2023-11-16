<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListTransactionsTest extends TestCase
{
    use RefreshDatabase;

    private $authUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->authUser = User::factory()->create();

        $otherUser = User::factory()->create();

        Transaction::factory()->createMany([
            [
                'type' => 'paid',
                'amount' => 11000,
                'description' => 'Buying some stuff',
                'performed_at' => now()->subDay(),
                'user_id' => $otherUser->id,
            ],
            [
                'type' => 'paid',
                'amount' => 5000,
                'description' => 'Buying a bottle of milk',
                'performed_at' => now()->subDay(),
                'user_id' => $this->authUser->id,
            ],
            [
                'type' => 'transferred',
                'amount' => 10000,
                'description' => 'Get some cash from ATM',
                'performed_at' => now()->subWeek(),
                'user_id' => $this->authUser->id,
            ],
            [
                'type' => 'received',
                'amount' => 15000,
                'description' => 'Selling some of my stuff',
                'performed_at' => now(),
                'user_id' => $this->authUser->id,
            ],
            [
                'type' => 'transferred',
                'amount' => 10000,
                'description' => 'Put some cash back to my bank account',
                'performed_at' => now(),
                'user_id' => $this->authUser->id,
            ],
        ]);
    }

    #[Test]
    public function it_lists_authenticated_users_transactions_sorted_by_perform_time_in_descending_order()
    {
        $this->actingAs($this->authUser)
            ->getJson(route('transactions.index'))
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJson([
                'data'=> [
                    ['description' => 'Put some cash back to my bank account'],
                    ['description' => 'Selling some of my stuff'],
                    ['description' => 'Buying a bottle of milk'],
                    ['description' => 'Get some cash from ATM'],
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'amount',
                        'description',
                        'performed_at',
                    ],
                ],
            ]);
    }

    #[Test]
    #[DataProvider('searchPhrases')]
    public function it_filters_transactions_based_on_search_phrase($searchPhrase, $responseItems)
    {
        $this->actingAs($this->authUser)
            ->getJson(route('transactions.index', ['search' => $searchPhrase]))
            ->assertOk()
            ->assertJsonCount(count($responseItems['data']), 'data')
            ->assertJson($responseItems);
    }

    #[Test]
    #[DataProvider('performedAtRanges')]
    public function it_filters_transactions_based_on_perform_time_range($startOfPerformedAt, $endOfPerformedAt, $responseItems)
    {
        $this->actingAs($this->authUser)
            ->getJson(route('transactions.index', [
                'start_of_performed_at' => $startOfPerformedAt,
                'end_of_performed_at' => $endOfPerformedAt
            ]))
            ->assertOk()
            ->assertJsonCount(count($responseItems['data']), 'data')
            ->assertJson($responseItems);
    }

    #[Test]
    #[DataProvider('amountRanges')]
    public function it_filters_transactions_based_on_amount_range($minimumAmount, $maximumAmount, $responseItems)
    {
        $this->actingAs($this->authUser)
            ->getJson(route('transactions.index', [
                'min_amount' => $minimumAmount,
                'max_amount' => $maximumAmount
            ]))
            ->assertOk()
            ->assertJsonCount(count($responseItems['data']), 'data')
            ->assertJson($responseItems);
    }

    public static function searchPhrases(): array
    {
        return [
            'Single word search' => [
                'some',
                [
                    'data' => [
                        ['description' => 'Put some cash back to my bank account'],
                        ['description' => 'Selling some of my stuff'],
                        ['description' => 'Get some cash from ATM'],
                    ]
                ]
            ],
            'Multi word search' => [
                'cash some',
                [
                    'data' => [
                        ['description' => 'Put some cash back to my bank account'],
                        ['description' => 'Get some cash from ATM'],
                    ]
                ]
            ],
        ];
    }

    public static function performedAtRanges(): array
    {
        return [
            'Start of perform time only' => [
                now()->subDays(2)->startOfDay()->format('Y-m-d H:i:s'),
                null,
                [
                    'data' => [
                        ['description' => 'Put some cash back to my bank account'],
                        ['description' => 'Selling some of my stuff'],
                        ['description' => 'Buying a bottle of milk'],
                    ]
                ]
            ],
            'End of perform time only' => [
                null,
                now()->subDay()->endOfDay()->format('Y-m-d H:i:s'),
                [
                    'data' => [
                        ['description' => 'Buying a bottle of milk'],
                        ['description' => 'Get some cash from ATM'],
                    ]
                ]
            ],
            'Both start and end of perform time' => [
                now()->subDays(2)->startOfDay()->format('Y-m-d H:i:s'),
                now()->subDay()->endOfDay()->format('Y-m-d H:i:s'),
                [
                    'data' => [
                        ['description' => 'Buying a bottle of milk'],
                    ]
                ]
            ],
        ];
    }

    public static function amountRanges(): array
    {
        return [
            'Minimum amount only' => [
                9000,
                null,
                [
                    'data' => [
                        ['description' => 'Put some cash back to my bank account'],
                        ['description' => 'Selling some of my stuff'],
                        ['description' => 'Get some cash from ATM'],
                    ]
                ]
            ],
            'Maximum amount only' => [
                null,
                12000,
                [
                    'data' => [
                        ['description' => 'Put some cash back to my bank account'],
                        ['description' => 'Buying a bottle of milk'],
                        ['description' => 'Get some cash from ATM'],
                    ]
                ]
            ],
            'Both minimum and maximum amount' => [
                9000,
                12000,
                [
                    'data' => [
                        ['description' => 'Put some cash back to my bank account'],
                        ['description' => 'Get some cash from ATM'],
                    ]
                ]
            ],
        ];
    }
}
