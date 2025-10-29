<?php


namespace App\Http\Controllers\landlord\auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\bookingModel;
use App\Models\tenant\approvetenantsModel;
use App\Models\tenant\reservationModel;
use App\Models\notificationModel;
use Carbon\Carbon;
use App\Models\landlord\dormModel;
use Barryvdh\DomPDF\Facade\Pdf;
class dashboardController extends Controller
{
     public function landlordDashboard($landlord_id)
    {
          $sessionLandlordId = session('landlord_id');
            $notifications = notificationModel::where('receiverID', $sessionLandlordId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
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
        $unreadCount = notificationModel::where('receiverID', $landlord_id)
            ->where('isRead', false)
            ->count();
   
        return view ('landlord.auth.dashboard', ['title' => 'Landlord - Dashboard',
        'headerName' => 'Dashboard',
        'color' =>'primary'
        ,'landlord_id'=> $landlord_id,
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
}
public function getLandlord(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
$date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();
    $landlord = landlordModel::find($landlord_id);


    if (!$landlord) {
        return response()->json([
            'status' => 'error',
            'message' => 'Landlord not found.'
        ], 404);
    }


    return response()->json([
        'status' => 'success',
        'landlord' => $landlord
    ]);
}
public function getTotalTenants(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $dormID = $request->query('dorm_id'); // optional dorm filter
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();


    $totalTenants = approvetenantsModel::whereHas('room', function ($query) use ($landlord_id, $dormID) {
        $query->where('fklandlordID', $landlord_id);
        if ($dormID) {
            $query->where('fkdormID', $dormID);
        }
    })
    ->where('status','<>','moved_out')
    ->where('status','<>','pending')
    ->whereDate('created_at', '<=', $date)
    ->count();


    return response()->json([
        'status' => 'success',
        'total_tenants' => $totalTenants
    ]);
}
public function availableBeds(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $dormID = $request->query('dorm_id'); // optional dorm filter
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();
   
    // If you want to filter by month instead of specific date
    $startOfMonth = $date->copy()->startOfMonth();
    $endOfMonth = $date->copy()->endOfMonth();


    // All rooms of the landlord marked as Available
    $roomsQuery = roomModel::where('fklandlordID', $landlord_id)
        ->where('availability', 'Available');


    if ($dormID) {
        $roomsQuery->where('fkdormID', $dormID);
    }


    // Booked rooms during that month (overlapping with the month at all)
    $bookedRoomIds = approvetenantsModel::whereHas('room', function ($query) use ($landlord_id, $dormID) {
        $query->where('fklandlordID', $landlord_id);
        if ($dormID) {
            $query->where('fkdormID', $dormID);
        }
    })
    ->where(function($query) use ($startOfMonth, $endOfMonth) {
        $query->where(function($q) use ($startOfMonth, $endOfMonth) {
            // Booking starts before or during month and ends after or during month
            $q->where('moveInDate', '<=', $endOfMonth)
              ->where('moveOutDate', '>=', $startOfMonth);
        });
    })
    ->pluck('fkroomID')
    ->toArray();


    // Count available rooms not booked during that month
    $availableRoomsCount = $roomsQuery
        ->whereNotIn('roomID', $bookedRoomIds)
        ->count();


    return response()->json([
        'status' => 'success',
        'available_beds' => $availableRoomsCount,
        'debug' => [
            'date' => $date,
            'month_start' => $startOfMonth,
            'month_end' => $endOfMonth,
            'bookedRoomIds' => $bookedRoomIds,
            'allAvailableRooms' => $roomsQuery->pluck('roomID')->toArray(),
        ]
    ]);
}




public function getReservationList(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $dormID = $request->query('dorm_id'); // optional dorm filter
$date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();
    $reservations = reservationModel::with(['room']) // eager load room data
        ->whereHas('room', function ($query) use ($landlord_id, $dormID) {
            $query->where('fklandlordID', $landlord_id);
            if ($dormID) {
                $query->where('fkdormID', $dormID);
            }
        })
        ->whereDate('created_at', '<=', $date)
        ->orderBy('created_at', 'desc') // optional: show latest first
        ->get();


    return response()->json([
        'status' => 'success',
        'reservations' => $reservations
    ]);
}
public function getBookingList(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $dormID = $request->query('dorm_id'); // optional dorm filter
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();


    $bookings = bookingModel::with('room') // eager load room info
        ->whereHas('room', function ($query) use ($landlord_id, $dormID) {
            $query->where('fklandlordID', $landlord_id);
            if ($dormID) {
                $query->where('fkdormID', $dormID);
            }
        })
        ->orderBy('created_at', 'desc')
        ->get();


    return response()->json([
        'status' => 'success',
        'bookings' => $bookings
    ]);
}
public function getDormID($landlord_id)
{
    $dorms = dormModel::where('fklandlordID', $landlord_id)
                      ->get(['dormID', 'dormName']);
    return response()->json([
        'status' => 'success',
        'dorms' => $dorms
    ]);
}


public function getRoomProfits(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $dormID = $request->query('dorm_id'); // optional dorm filter
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();


    $query = roomModel::where('fklandlordID', $landlord_id)
        ->whereDate('created_at', '<=', $date);


    if ($dormID) {
        $query->where('fkdormID', $dormID);
    }
    $rooms = $query->get();
    // Group by roomNumber to avoid duplicates and sum their price
    $roomsData = $rooms->groupBy('roomNumber')->map(function ($groupedRooms, $roomNumber) {
        $totalPrice = $groupedRooms->where('availability', 'Occupied')->sum('price');


        return [
            'roomNumber' => $roomNumber,
            'profit' => $totalPrice,
            'availability' => $groupedRooms->first()->availability,
            'dormName' => $groupedRooms->first()->dorm->dormName ?? 'N/A',
            'created_at' => $groupedRooms->first()->created_at,
        ];
    })->sortByDesc('profit')->values();


    $totalProfit = $roomsData->sum('profit');


    return response()->json([
        'status' => 'success',
        'total_profit' => $totalProfit,
        'data' => $roomsData
    ]);
}
public function getGenderDistribution(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $dormID = $request->query('dorm_id'); // optional dorm filter
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();


    // Get all approved tenants under landlord (and optionally filter by dorm)
    $tenants = \App\Models\tenant\approvetenantsModel::with('room')
        ->whereHas('room', function ($q) use ($landlord_id, $dormID) {
            $q->where('fklandlordID', $landlord_id);
            if ($dormID) {
                $q->where('fkdormID', $dormID);
            }
        })
        ->whereDate('moveInDate', '<=', $date)
        ->get();


    // Group by gender
    $genderCounts = $tenants->groupBy('gender')
        ->map(fn($g, $gender) => [
            'gender' => $gender,
            'count' => $g->count()
        ])
        ->values()
        ->toArray();


    $total = $tenants->count();


    return response()->json([
        'status' => 'success',
        'total' => $total,
        'data' => $genderCounts
    ]);
}



public function generateFullReport($landlordID, Request $request)
{
    $selectedDate = $request->query('date');


    $reservations = reservationModel::with(['room.dorm','payment'])
        ->where('status', 'approved')
        ->whereHas('room', fn($q) => $q->where('fklandlordID', $landlordID))
        ->when($selectedDate, fn($q) => $q->whereDate('created_at', '<=', $selectedDate))
        ->get();


    $bookings = bookingModel::with(['room.dorm','payment'])
        ->where('status', 'approved')
        ->whereHas('room', fn($q) => $q->where('fklandlordID', $landlordID))
        ->when($selectedDate, fn($q) => $q->whereDate('created_at', '<=', $selectedDate))
        ->get();


    // Calculate total_amount per reservation/booking
    $bookings->each(fn($b) => $b->total_amount = $b->payment->sum('amount'));
    $reservations->each(function($r) {
    // Get only the latest approved payment
    $latestPayment = $r->payment()
        ->orderByDesc('created_at')
        ->first();


    $r->total_amount = $latestPayment->amount ?? 0; // fallback to 0 if none
});


    // Calculate combined income without double-counting payments
    $allPayments = $reservations->flatMap(fn($r) => $r->payment)
                    ->merge($bookings->flatMap(fn($b) => $b->payment))
                    ->unique('id');


    $totalIncome = $allPayments->sum('amount');


    $logoPath = public_path('images/Logo/logo.png');


    $reportData = [
        'reservations' => $reservations,
        'bookings'     => $bookings,
        'totalIncome'  => $totalIncome,
        'landlordID'   => $landlordID,
        'reportDate'   => Carbon::now('Asia/Manila')->format('F d, Y h:i A'),
        'logoPath'     => $logoPath,
    ];
    $pdf = PDF::loadView('landlord.reports.full-report', $reportData);
    return $pdf->stream("landlord-full-report-{$landlordID}.pdf");
}
}












