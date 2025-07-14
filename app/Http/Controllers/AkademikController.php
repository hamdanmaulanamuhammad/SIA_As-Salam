<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Semester;
use Illuminate\Http\Request;

class AkademikController extends Controller
{
    public function index()
    {
        $kelas = Kelas::paginate(10);
        $mapels = Mapel::paginate(10);
        $semesters = Semester::paginate(10);

        return view('admin.menu-akademik', compact('kelas', 'mapels', 'semesters'));
    }
}
