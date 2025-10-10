<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\notificationModel;
use App\Models\landlord\landlordModel;
use App\Models\tenant\tenantModel;
use App\Models\landlord\paymentVerifiedModel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


class paymentlandlordController extends Controller
{
     public function paymentLandlordindex($landlord_id)
    {
        $sessionLandlordId = session('landlord_id');
        $notifications = notificationModel::where('receiverID', $sessionLandlordId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $unreadCount = notificationModel::where('receiverID', $landlord_id)
            ->where('isRead', false)
            ->count();
        if (!$sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Please log in as a landlord.');
        }
    
        if ($landlord_id !== $sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Unauthorized access.');
        }
    
        $landlord = landlordModel::find($landlord_id);
        if (!$landlord) {
            return redirect()->route('loginLandlord')->with('error', 'Landlord not found.');
        }
    
        return view('landlord.auth.paymentLandlord',[
        "title" => "Landlord Payment Account",
        "headerName" => "Landlord Payment Account Verification",
        'landlord' => $landlord,
        'landlord_id' => $landlord_id,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
    ]); 
}
public function verifyPaymentLandlord(Request $request)
{
    $landlord_id = $request->route('landlord_id');
    $paymentPageUrl = url()->previous();

    // Validate input
    $request->validate([
            'paymentEmail' => 'required|email',
            'amount' => 'required|numeric|min:1',
        ]);
  try {
            $amountInCents = $request->amount * 100; // PayMongo requires amount in centavos

            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->post('https://api.paymongo.com/v1/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'line_items' => [[
                                'name' => 'Dormhub Account Upgrade',
                                'quantity' => 1,
                                'currency' => 'PHP',
                                'amount' => $amountInCents,
                            ]],
                            'payment_method_types' => ['gcash'],
                            'success_url' => url('/landlord/payment-success'),
                            'cancel_url' => url('/landlord/payment-cancel'),
                            'customer_email' => $request->paymentEmail,
                        ],
                    ],
                ]);

            if ($response->failed()) {
                return response()->json(['error' => $response->json()], 400);
            }

            $checkoutUrl = $response->json()['data']['attributes']['checkout_url'];

            return response()->json([
                'checkout_url' => $checkoutUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    
}
public function paymentSuccess(Request $request)
{
    $landlord_id = session('landlord_id');
    $landlord = landlordModel::find($landlord_id);
    $landlord->isVerified = 1;
    $landlord->save();
    paymentVerifiedModel::create([
        'fklandlordID' => $landlord_id,
        'email' => $landlord->email,
        'amount' => '500',
        'paymongo_id' => null,
        'status' => 'success',
        'paymentMethod' => 'gcash',
        'referenceNumber' => null,
        'responsePayload' => json_encode($request->all()),
    ]);
    return redirect()->route('payment.landlord', ['landlord_id' => $landlord_id, 'status' => 'success']);

}
public function paymentCancel(Request $request)
{
$landlord_id = session('landlord_id');
    return redirect()->route('payment.landlord', ['landlord_id' => $landlord_id, 'status' => 'cancelled']);

}


}
