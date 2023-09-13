<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentType;
use App\Models\Banks;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    public function getPaymentOptions()
    {
        $paymentTypes = PaymentType::all();
        $banks = Banks::all();

        return response()->json([
            'paymentTypes' => $paymentTypes,
            'banks' => $banks
        ]);
    }
}
