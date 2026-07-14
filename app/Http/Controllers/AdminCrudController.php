<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\KalenderAkademik;
use App\Models\Berita;
use App\Models\Banner;
use App\Models\Course;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminCrudController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::check() && Auth::user()->isAdmin(), 403, 'Akses khusus Administrator.');
            return $next($request);
        });
    }

    // ==========================================
    // FAKULTAS CRUD
    // ==========================================
    public function fakultasIndex()
    {
        $fakultas = Fakultas::withCount('prodis')->orderBy('nama')->get();
        return view('admin.fakultas', compact('fakultas'));
    }

    public function fakultasStore(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:fakultas,kode',
        ]);

        Fakultas::create($data);
        Auth::user()->logActivity("Membuat Fakultas baru: {$data['nama']}");

        return back()->with('success', __('Fakultas berhasil ditambahkan.'));
    }

    public function fakultasUpdate(Request $request, Fakultas $fakultas)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:fakultas,kode,' . $fakultas->id,
        ]);

        $fakultas->update($data);
        Auth::user()->logActivity("Memperbarui Fakultas ID {$fakultas->id}: {$data['nama']}");

        return back()->with('success', __('Fakultas berhasil diperbarui.'));
    }

    public function fakultasDestroy(Fakultas $fakultas)
    {
        Auth::user()->logActivity("Menghapus Fakultas: {$fakultas->nama}");
        $fakultas->delete();
        return back()->with('success', __('Fakultas berhasil dihapus.'));
    }

    // ==========================================
    // PRODI CRUD
    // ==========================================
    public function prodiIndex()
    {
        $prodis = Prodi::with('fakultas')->orderBy('nama')->get();
        $fakultas = Fakultas::orderBy('nama')->get();
        return view('admin.prodi', compact('prodis', 'fakultas'));
    }

    public function prodiStore(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:prodis,kode',
            'fakultas_id' => 'required|exists:fakultas,id',
        ]);

        Prodi::create($data);
        Auth::user()->logActivity("Membuat Prodi baru: {$data['nama']}");

        return back()->with('success', __('Program Studi berhasil ditambahkan.'));
    }

    public function prodiUpdate(Request $request, Prodi $prodi)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:prodis,kode,' . $prodi->id,
            'fakultas_id' => 'required|exists:fakultas,id',
        ]);

        $prodi->update($data);
        Auth::user()->logActivity("Memperbarui Prodi ID {$prodi->id}: {$data['nama']}");

        return back()->with('success', __('Program Studi berhasil diperbarui.'));
    }

    public function prodiDestroy(Prodi $prodi)
    {
        Auth::user()->logActivity("Menghapus Prodi: {$prodi->nama}");
        $prodi->delete();
        return back()->with('success', __('Program Studi berhasil dihapus.'));
    }

    // ==========================================
    // SEMESTER CRUD
    // ==========================================
    public function semesterIndex()
    {
        $semesters = Semester::orderByDesc('id')->get();
        return view('admin.semesters', compact('semesters'));
    }

    public function semesterStore(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Semester::create($data);
        Auth::user()->logActivity("Membuat Semester baru: {$data['nama']}");

        return back()->with('success', __('Semester berhasil ditambahkan.'));
    }

    public function semesterUpdate(Request $request, Semester $semester)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $semester->update($data);
        Auth::user()->logActivity("Memperbarui Semester ID {$semester->id}: {$data['nama']}");

        return back()->with('success', __('Semester berhasil diperbarui.'));
    }

    public function semesterToggle(Semester $semester)
    {
        // Deactivate all others if activating this one
        if (!$semester->is_active) {
            Semester::where('id', '!=', $semester->id)->update(['is_active' => false]);
            $semester->update(['is_active' => true]);
            Auth::user()->logActivity("Mengaktifkan Semester: {$semester->nama}");
        } else {
            $semester->update(['is_active' => false]);
            Auth::user()->logActivity("Menonaktifkan Semester: {$semester->nama}");
        }

        return back()->with('success', __('Status semester berhasil diubah.'));
    }

    public function semesterDestroy(Semester $semester)
    {
        Auth::user()->logActivity("Menghapus Semester: {$semester->nama}");
        $semester->delete();
        return back()->with('success', __('Semester berhasil dihapus.'));
    }

    // ==========================================
    // TAHUN AKADEMIK CRUD
    // ==========================================
    public function tahunAkademikIndex()
    {
        $tahuns = TahunAkademik::orderByDesc('id')->get();
        return view('admin.tahun_akademik', compact('tahuns'));
    }

    public function tahunAkademikStore(Request $request)
    {
        $data = $request->validate([
            'tahun' => 'required|string|max:255',
        ]);

        TahunAkademik::create($data);
        Auth::user()->logActivity("Membuat Tahun Akademik baru: {$data['tahun']}");

        return back()->with('success', __('Tahun Akademik berhasil ditambahkan.'));
    }

    public function tahunAkademikUpdate(Request $request, TahunAkademik $tahun)
    {
        $data = $request->validate([
            'tahun' => 'required|string|max:255',
        ]);

        $tahun->update($data);
        Auth::user()->logActivity("Memperbarui Tahun Akademik ID {$tahun->id}: {$data['tahun']}");

        return back()->with('success', __('Tahun Akademik berhasil diperbarui.'));
    }

    public function tahunAkademikToggle(TahunAkademik $tahun)
    {
        if (!$tahun->is_active) {
            TahunAkademik::where('id', '!=', $tahun->id)->update(['is_active' => false]);
            $tahun->update(['is_active' => true]);
            Auth::user()->logActivity("Mengaktifkan Tahun Akademik: {$tahun->tahun}");
        } else {
            $tahun->update(['is_active' => false]);
            Auth::user()->logActivity("Menonaktifkan Tahun Akademik: {$tahun->tahun}");
        }

        return back()->with('success', __('Status Tahun Akademik berhasil diubah.'));
    }

    public function tahunAkademikDestroy(TahunAkademik $tahun)
    {
        Auth::user()->logActivity("Menghapus Tahun Akademik: {$tahun->tahun}");
        $tahun->delete();
        return back()->with('success', __('Tahun Akademik berhasil dihapus.'));
    }

    // ==========================================
    // KELAS CRUD
    // ==========================================
    public function kelasIndex()
    {
        $kelas = Kelas::with('prodi')->orderBy('nama')->get();
        $prodis = Prodi::orderBy('nama')->get();
        return view('admin.kelas', compact('kelas', 'prodis'));
    }

    public function kelasStore(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id',
        ]);

        Kelas::create($data);
        Auth::user()->logActivity("Membuat Kelas baru: {$data['nama']}");

        return back()->with('success', __('Kelas berhasil ditambahkan.'));
    }

    public function kelasUpdate(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id',
        ]);

        $kelas->update($data);
        Auth::user()->logActivity("Memperbarui Kelas ID {$kelas->id}: {$data['nama']}");

        return back()->with('success', __('Kelas berhasil diperbarui.'));
    }

    public function kelasDestroy(Kelas $kelas)
    {
        Auth::user()->logActivity("Menghapus Kelas: {$kelas->nama}");
        $kelas->delete();
        return back()->with('success', __('Kelas berhasil dihapus.'));
    }

    // ==========================================
    // PENGUMUMAN CRUD
    // ==========================================
    public function pengumumanIndex()
    {
        $pengumumans = Pengumuman::with('user')->latest()->get();
        return view('admin.pengumuman', compact('pengumumans'));
    }

    public function pengumumanStore(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'target_role' => 'required|in:all,dosen,mahasiswa',
        ]);

        $data['user_id'] = Auth::id();
        Pengumuman::create($data);
        Auth::user()->logActivity("Membuat Pengumuman baru: {$data['judul']}");

        return back()->with('success', __('Pengumuman berhasil dipublikasikan.'));
    }

    public function pengumumanUpdate(Request $request, Pengumuman $pengumuman)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'target_role' => 'required|in:all,dosen,mahasiswa',
        ]);

        $pengumuman->update($data);
        Auth::user()->logActivity("Memperbarui Pengumuman ID {$pengumuman->id}: {$data['judul']}");

        return back()->with('success', __('Pengumuman berhasil diperbarui.'));
    }

    public function pengumumanDestroy(Pengumuman $pengumuman)
    {
        Auth::user()->logActivity("Menghapus Pengumuman: {$pengumuman->judul}");
        $pengumuman->delete();
        return back()->with('success', __('Pengumuman berhasil dihapus.'));
    }

    // ==========================================
    // KALENDER AKADEMIK CRUD
    // ==========================================
    public function kalenderIndex()
    {
        $kalender = KalenderAkademik::orderBy('tanggal_mulai')->get();
        return view('admin.kalender', compact('kalender'));
    }

    public function kalenderStore(Request $request)
    {
        $data = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        KalenderAkademik::create($data);
        Auth::user()->logActivity("Membuat Kalender baru: {$data['kegiatan']}");

        return back()->with('success', __('Kalender akademik berhasil ditambahkan.'));
    }

    public function kalenderUpdate(Request $request, KalenderAkademik $kalender)
    {
        $data = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $kalender->update($data);
        Auth::user()->logActivity("Memperbarui Kalender ID {$kalender->id}: {$data['kegiatan']}");

        return back()->with('success', __('Kalender akademik berhasil diperbarui.'));
    }

    public function kalenderDestroy(KalenderAkademik $kalender)
    {
        Auth::user()->logActivity("Menghapus Kalender: {$kalender->kegiatan}");
        $kalender->delete();
        return back()->with('success', __('Kalender akademik berhasil dihapus.'));
    }

    // ==========================================
    // BERITA CRUD
    // ==========================================
    public function beritaIndex()
    {
        $berita = Berita::with('user')->latest()->get();
        return view('admin.berita', compact('berita'));
    }

    public function beritaStore(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('news', 'public');
        }

        $data['user_id'] = Auth::id();
        Berita::create($data);
        Auth::user()->logActivity("Membuat Berita baru: {$data['judul']}");

        return back()->with('success', __('Berita berhasil ditambahkan.'));
    }

    public function beritaUpdate(Request $request, Berita $berita)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('news', 'public');
        }

        $berita->update($data);
        Auth::user()->logActivity("Memperbarui Berita ID {$berita->id}: {$data['judul']}");

        return back()->with('success', __('Berita berhasil diperbarui.'));
    }

    public function beritaDestroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }
        Auth::user()->logActivity("Menghapus Berita: {$berita->judul}");
        $berita->delete();
        return back()->with('success', __('Berita berhasil dihapus.'));
    }

    // ==========================================
    // BANNER CRUD
    // ==========================================
    public function bannerIndex()
    {
        $banners = Banner::orderBy('order')->get();
        return view('admin.banners', compact('banners'));
    }

    public function bannerStore(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'required|image|max:2048',
            'link' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $data['gambar'] = $request->file('gambar')->store('banners', 'public');
        $data['order'] = $data['order'] ?? 0;

        Banner::create($data);
        Auth::user()->logActivity("Membuat Banner baru: {$data['judul']}");

        return back()->with('success', __('Banner berhasil ditambahkan.'));
    }

    public function bannerUpdate(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
            'link' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($banner->gambar);
            $data['gambar'] = $request->file('gambar')->store('banners', 'public');
        }

        $banner->update($data);
        Auth::user()->logActivity("Memperbarui Banner ID {$banner->id}: {$data['judul']}");

        return back()->with('success', __('Banner berhasil diperbarui.'));
    }

    public function bannerToggle(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        Auth::user()->logActivity("Mengubah status aktif Banner ID {$banner->id}");

        return back()->with('success', __('Status banner berhasil diubah.'));
    }

    public function bannerDestroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->gambar);
        Auth::user()->logActivity("Menghapus Banner: {$banner->judul}");
        $banner->delete();
        return back()->with('success', __('Banner berhasil dihapus.'));
    }

    // ==========================================
    // COURSE CRUD (Mata Kuliah)
    // ==========================================
    public function courseIndex()
    {
        $courses = Course::with('dosen', 'prodi', 'semester', 'kelas')->orderBy('nama_matkul')->get();
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $prodis = Prodi::orderBy('name')->get();
        $semesters = Semester::orderByDesc('id')->get();
        $kelas = Kelas::orderBy('nama')->get();

        return view('admin.courses', compact('courses', 'dosens', 'prodis', 'semesters', 'kelas'));
    }

    public function courseStore(Request $request)
    {
        $data = $request->validate([
            'nama_matkul' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:courses,kode',
            'dosen_id' => 'required|exists:users,id',
            'prodi_id' => 'nullable|exists:prodis,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'deskripsi' => 'nullable|string',
        ]);

        $course = Course::create($data);

        // Auto enroll students in that class if specified
        if ($course->kelas_id) {
            $studentIds = User::where('role', 'mahasiswa')
                ->where('kelas_id', $course->kelas_id)
                ->pluck('id');
            $course->students()->sync($studentIds);
        } else {
            // Fallback: enroll all students
            $course->students()->sync(User::where('role', 'mahasiswa')->pluck('id'));
        }

        Auth::user()->logActivity("Membuat Mata Kuliah baru: {$data['nama_matkul']}");

        return back()->with('success', __('Mata kuliah berhasil ditambahkan.'));
    }

    public function courseUpdate(Request $request, Course $course)
    {
        $data = $request->validate([
            'nama_matkul' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:courses,kode,' . $course->id,
            'dosen_id' => 'required|exists:users,id',
            'prodi_id' => 'nullable|exists:prodis,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'deskripsi' => 'nullable|string',
        ]);

        $course->update($data);

        // Sync students if class changed
        if ($course->kelas_id) {
            $studentIds = User::where('role', 'mahasiswa')
                ->where('kelas_id', $course->kelas_id)
                ->pluck('id');
            $course->students()->sync($studentIds);
        }

        Auth::user()->logActivity("Memperbarui Mata Kuliah ID {$course->id}: {$data['nama_matkul']}");

        return back()->with('success', __('Mata kuliah berhasil diperbarui.'));
    }

    public function courseDestroy(Course $course)
    {
        Auth::user()->logActivity("Menghapus Mata Kuliah: {$course->nama_matkul}");
        $course->delete();
        return back()->with('success', __('Mata kuliah berhasil dihapus.'));
    }

    // ==========================================
    // COURSE ENROLLMENTS
    // ==========================================
    public function courseEnrollments(Request $request, Course $course)
    {
        $enrolledIds = $course->students()->pluck('users.id')->toArray();

        $query = User::where('role', 'mahasiswa')->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $students = $query->orderBy('name')->paginate(50);
        $kelas = Kelas::orderBy('nama')->get();

        return view('admin.enrollments', compact('course', 'students', 'enrolledIds', 'kelas'));
    }

    public function courseEnrollmentsStore(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $studentIds = $request->input('student_ids', []);
        $course->students()->sync($studentIds);

        Auth::user()->logActivity("Memperbarui enrollment mahasiswa untuk course: {$course->nama_matkul}");

        return redirect()->route('admin.courses.index')->with('success', __('Daftar mahasiswa terdaftar berhasil diperbarui.'));
    }

    // ==========================================
    // LOGS INDEX
    // ==========================================
    public function logsIndex(Request $request)
    {
        $query = UserActivity::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            })->orWhere('activity', 'like', "%{$search}%");
        }

        if ($request->filled('role')) {
            $role = $request->role;
            $query->whereHas('user', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $logs = $query->latest()->paginate(30);

        return view('admin.logs', compact('logs'));
    }
}
