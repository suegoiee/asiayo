<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function process(OrderRequest $request)
    {
        $validatedData = $request->validated();
        $processedData = $this->orderService->processOrder($validatedData);
        return response()->json($processedData, 200);
    }
}
