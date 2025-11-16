<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\BarangayInhabitant;
use App\Models\User;
use Illuminate\Http\Request;

class RBISearchController extends Controller
{
    /**
     * Search RBI and registered users for respondent verification
     */
    public function search(Request $request)
    {
        $barangay = auth()->user()->barangay;
        $searchTerm = $request->input('name');
        
        if (strlen($searchTerm) < 3) {
            return response()->json([
                'results' => [],
                'message' => 'Please enter at least 3 characters'
            ]);
        }
        
        $results = [];
        
        // Search registered residents (Users)
        $users = User::where('barangay_id', $barangay->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'resident');
            })
            ->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%");
            })
            ->limit(10)
            ->get();
        
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'type' => 'user',
                'name' => $user->full_name,
                'address' => $user->address ?? 'N/A',
                'contact' => $user->contact_number ?? 'N/A'
            ];
        }
        
        // Search RBI records
        $rbiRecords = BarangayInhabitant::where('barangay_id', $barangay->id)
            ->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
            })
            ->limit(10)
            ->get();
        
        foreach ($rbiRecords as $rbi) {
            $results[] = [
                'id' => $rbi->id,
                'type' => 'rbi',
                'name' => $rbi->full_name,
                'address' => $rbi->full_address ?? 'N/A',
                'contact' => $rbi->contact_number ?? 'N/A'
            ];
        }
        
        return response()->json([
            'results' => $results,
            'count' => count($results)
        ]);
    }
}