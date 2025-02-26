<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Corp;
use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Http\Request;

class CorpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Corp')->only(['store']);
        $this->middleware('can:update,App\Models\Corp')->only(['update']);
        $this->middleware('can:delete,App\Models\Corp')->only(['destroy']);
        $this->middleware('can:views,App\Models\Corp')->only(['index']);
        $this->middleware('can:view,App\Models\Corp')->only(['show']);
    }

    
    public function index()
    {
        //die();
        $Corps = Corp::where('status', 1)->get();
        return response()->json($Corps);
    }

   
    public function show($id)
    {
        // Aktif kayıtları getirin
        $Corp = Corp::where('id', $id)->where('status', 1)->first();
    
        if (!$Corp) {
            return response()->json(['message' => 'Corp not found or has been deleted'], 404);
        }
    
        return response()->json($Corp);
    }

  
  
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Corp = Corp::create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Corp created successfully"
        ]);
    }

 
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Corp = Corp::find($id);

        if (!$Corp) {
            return response()->json(['message' => 'Corp not found'], 404);
        }

        $Corp->name = $request->name;
        $Corp->save();
        return response()->json([
            "status" => true,
            "message" => "Corp updated successfully"
        ]);
    }

   
    public function destroy($id)
    {
        $corp = Corp::find($id);

        if (!$corp) {
            return response()->json(['message' => 'Korpus tapılmadı'], 404);
        }

        // Fakülteye bağlı diğer kayıtları kontrol et (status = 1 olanlar)
        $hasActiveRelations = Room::where('corp_id', $id)->where('status', 1)->exists() ||
            Schedule::where('corp_id', $id)->where('status', 1)->exists();

        if ($hasActiveRelations) {
            return response()->json(['message' => 'Bu Korpusa bağlı aktiv məlumatlar var. Silinə bilmir.'], 400);
        }

        // Fakültenin status'unu 0 yaparak soft delete işlemi uygula
        $corp->status = 0;
        $corp->save();

        return response()->json(['message' => 'Korpus uğurla silindi.']);
    }
}
