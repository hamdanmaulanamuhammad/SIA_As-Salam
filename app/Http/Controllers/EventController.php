<?php  
  
namespace App\Http\Controllers;  
  
use App\Models\Event;  
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Validator;  
use Carbon\Carbon;  
  
class EventController extends Controller  
{  
    public function index()  
    {  
        // Ambil acara hari ini  
        $todayEvents = Event::whereDate('start_time', now()->format('Y-m-d'))->get();  
  
        // Ambil acara mendatang  
        $upcomingEvents = Event::where('start_time', '>', now())->get();  
  
        return view('admin.events-admin', compact('todayEvents', 'upcomingEvents'));  
    }  
  
    public function create()  
    {  
        // Menampilkan form untuk membuat acara baru  
        return view('events.create');  
    }  
  
    public function store(Request $request)  
    {  
        $request->validate([  
            'name' => 'required|string|max:255',  
            'start_time' => 'required|date|after:now',  
            'end_time' => 'required|date|after:start_time',  
            'late_limit' => 'required|integer|min:0',  
        ]);  
  
        // Validasi tidak ada acara lain di hari yang sama  
        $date = Carbon::parse($request->start_time)->format('Y-m-d');  
        $existingEvent = Event::whereDate('start_time', $date)->first();  
  
        if ($existingEvent) {  
            return response()->json(['error' => 'Sudah ada acara di hari ini.'], 400);  
        }  
  
        Event::create($request->all());  
  
        return response()->json(['message' => 'Acara berhasil dibuat.'], 201);  
    }  
  
    public function show(Event $event)  
    {  
        // Menampilkan detail acara  
        return view('events.show', compact('event'));  
    }  
  
    public function edit(Event $event)  
    {  
        // Menampilkan form untuk mengedit acara  
        return view('events.edit', compact('event'));  
    }  
  
    public function update(Request $request, Event $event)  
    {  
        $request->validate([  
            'name' => 'required|string|max:255',  
            'start_time' => 'required|date|after:now',  
            'end_time' => 'required|date|after:start_time',  
            'late_limit' => 'required|integer|min:0',  
        ]);  
  
        // Validasi tidak ada acara lain di hari yang sama  
        $date = Carbon::parse($request->start_time)->format('Y-m-d');  
        $existingEvent = Event::whereDate('start_time', $date)  
            ->where('id', '!=', $event->id) // Pastikan tidak memeriksa acara yang sama  
            ->first();  
  
        if ($existingEvent) {  
            return response()->json(['error' => 'Sudah ada acara di hari ini.'], 400);  
        }  
  
        $event->update($request->all());  
  
        return response()->json(['message' => 'Acara berhasil diperbarui.'], 200);  
    }  
  
    public function destroy(Event $event)  
    {  
        $event->delete();  
  
        return response()->json(['message' => 'Acara berhasil dihapus.'], 200);  
    }  
}  
