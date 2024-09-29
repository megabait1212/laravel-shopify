<?php

namespace App\Jobs;

use App\Models\FakeCustomer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mockery\Exception;

class DeleteCustomers implements ShouldQueue, ShouldBeUnique
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
        $this->shop->fakeCustomers()->each(function (FakeCustomer $customer) {
            $customerDeleteInput = [
                'id' => $customer->gid
            ];
            $result = $this->shop->api()->graph('
                mutation deleteCustomer($input: CustomerDeleteInput!) {
                    customerDelete(input: $input) {
                        deletedCustomerId
                    }
                }
            ', ['input' => $customerDeleteInput]);
//            var_dump($result);
            if (empty($result['errors']) || $result['status'] == '404') {
                $customer->delete();
            } else {
                report(new Exception("Shopify error"));
            }
        });
    }
}
