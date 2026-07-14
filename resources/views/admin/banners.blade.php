@extends('layouts.app')

@section('title', __('Manajemen Banner Dashboard'))

@section('content')
<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">LMS Kampus Malang</span>
        <h2 class="text-3xl font-extrabold mt-3 tracking-tight">Manajemen Banner Dashboard</h2>
        <p class="text-slate-300 text-sm mt-1.5 font-medium">Kelola slides promosi dan informasi penting di bagian atas dashboard</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ editingBanner: null }">
    <!-- Add Form -->
    <div>
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2 border-b pb-3">
                <i class="fas fa-image text-indigo-500"></i>
                <span>Tambah Banner</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Judul Banner</label>
                    <input name="judul" required value="{{ old('judul') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Pilih Gambar Banner</label>
                    <input type="file" name="gambar" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all border border-slate-200 rounded-xl p-1">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Link Tautan (Optional)</label>
                    <input name="link" value="{{ old('link') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="e.g. https://google.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Urutan Order (Optional)</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 rounded-xl transition-all shadow-md shadow-indigo-600/10">
                    Simpan Banner
                </button>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                            <th class="px-6 py-3.5">Gambar</th>
                            <th class="px-6 py-3.5">Judul</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($banners as $b)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <img src="{{ asset('storage/' . $b->gambar) }}" class="w-20 h-10 rounded-lg object-cover ring-1 ring-slate-200">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $b->judul }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1 font-semibold">Urutan: {{ $b->order }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.banners.toggle', $b) }}" class="m-0">
                                        @csrf
                                        <button class="inline-flex items-center gap-1.5 text-xs font-semibold transition-all rounded-full px-2.5 py-0.5 border
                                            {{ $b->is_active 
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' 
                                                : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200'
                                            }}">
                                            <span>{{ $b->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit"
                                            @click="editingBanner = {{ json_encode([
                                                'id' => $b->id,
                                                'judul' => $b->judul,
                                                'link' => $b->link,
                                                'order' => $b->order,
                                                'update_url' => route('admin.banners.update', $b)
                                            ]) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.banners.destroy', $b) }}" onsubmit="return confirm('Hapus banner ini?')" class="m-0">
                                            @csrf @method('DELETE')
                                            <button class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    Belum ada banner tersimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Edit Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 x-show="editingBanner !== null" x-cloak>
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-sm w-full p-8" @click.away="editingBanner = null">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Form Edit</span>
                            <h4 class="text-lg font-bold text-slate-800 mt-2" x-text="'Ubah: ' + editingBanner?.judul"></h4>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600" @click="editingBanner = null">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form :action="editingBanner?.update_url" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Judul Banner</label>
                            <input name="judul" required :value="editingBanner?.judul" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Ganti Gambar Banner</label>
                            <input type="file" name="gambar" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all border border-slate-200 rounded-xl p-1">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Link Tautan</label>
                            <input name="link" :value="editingBanner?.link" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Urutan Order</label>
                            <input type="number" name="order" :value="editingBanner?.order" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>

                        <div class="flex gap-3 justify-end pt-3">
                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-4 py-2.5 rounded-xl transition-all" @click="editingBanner = null">
                                Batal
                            </button>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-lg">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
