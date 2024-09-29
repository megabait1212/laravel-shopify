<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFakeDataRequest;
use App\Jobs\CreateProducts;
use App\Jobs\DeleteProducts;
use App\Jobs\CreateCustomers;
use App\Jobs\DeleteCustomers;
use App\Models\FakeProduct;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\Exception;

class FakerController extends Controller
{
    public function __construct(private readonly ResponseFactory $responseFactory)
    {

    }

    public function store(CreateFakeDataRequest $request): Response
    {
        $data = $request->validated();
        $productsCount = $data['productsCount'] ?? 0;
        $customerCount = $data['customersCount'] ?? 0;
        $user = $request->user();

        if ($productsCount > 0) {
            CreateProducts::dispatch($productsCount, $user);
        }

        if ($customerCount > 0) {
            CreateCustomers::dispatch($customerCount, $user);
        }

        return $this->responseFactory->noContent();
    }

    public function destroy(Request $request): Response
    {
        $user = $request->user();
        DeleteProducts::dispatch($user);
        DeleteCustomers::dispatch($user);

        return $this->responseFactory->noContent();
    }
}
