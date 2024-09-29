<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mockery\Exception;

class CreateProducts implements ShouldQueue, ShouldBeUnique
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
        $products = [];

        for ($i = 0; $i <= $this->count; $i++) {
            $productResource = [
                'title' => 'Product title ' . $i,
                'descriptionHtml' => 'Product description ' . $i,
            ];

            $result = $this->shop->api()->graph(
                'mutation createProduct($productInput: ProductInput!) {
            	productCreate(input: $productInput) {
                    product {
                      id
                      title
                      description
                    }
                    userErrors {
                      field
                      message
                    }
                }
            }',
                ['productInput' => $productResource]
            );
//            var_dump($result);

            if (!empty($result['errors'])) {
                throw new Exception("Shopify error");
            }
//            var_dump($result['body']['data']);
            $id = $result['body']['data']['productCreate']['product']['id'];

            $products[] = ['gid' => $id];
        }

        $this->shop->fakeProducts()->createMany($products);
    }
}
