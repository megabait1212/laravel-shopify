<?php

namespace App\Jobs;

use App\Models\FakeProduct;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mockery\Exception;

class DeleteProducts implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly User $shop)
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
        $this->shop->fakeProducts()->each(function (FakeProduct $product) {
            $productDeleteInput = [
                'id' => $product->gid
            ];
            $result = $this->shop->api()->graph('
                mutation deleteProduct($input: ProductDeleteInput!) {
                    productDelete(input: $input) {
                        deletedProductId
                    }
                }
            ', ['input' => $productDeleteInput]);
//            var_dump($result);
            if (empty($result['errors']) || $result['status'] == '404') {
                $product->delete();
            } else {
                report(new Exception("Shopify error"));
            }
        });
    }
}
