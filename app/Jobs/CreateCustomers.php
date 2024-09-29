<?php

namespace App\Jobs;

use App\Models\User;
use Faker\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mockery\Exception;

class CreateCustomers implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly int $count, private readonly User $shop)
    {
        //
    }

    public function uniqueId(): string
    {
        return $this->shop->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customers = [];
        $faker = Factory::create();

        for ($i = 0; $i <= $this->count; $i++) {
            $customerResource = [
                'firstName' => $faker->firstName,
                'lastName' => $faker->lastName,
            ];

            $result = $this->shop->api()->graph(
                'mutation createCustomer($input: CustomerInput!) {
            	customerCreate(input: $input) {
                    customer {
                      id
                      firstName
                      lastName
                    }
                    userErrors {
                      field
                      message
                    }
                }
            }',
                ['input' => $customerResource]
            );

            if (!empty($result['errors'])) {
                dump($result['errors']);
            }
//            var_dump($result['body']['data']);
            $id = $result['body']['data']['customerCreate']['product']['id'];

            $customers[] = ['gid' => $id];
        }

        $this->shop->fakeProducts()->createMany($customers);
    }
}
