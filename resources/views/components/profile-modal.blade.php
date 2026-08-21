{{--
    Komponen: <x-profile-modal />
    Taruh SEKALI saja di layout utama (mis. resources/views/layouts/app.blade.php),
    diletakkan sebelum </body>. Dipicu dari mana saja lewat event Alpine:
        @click="$dispatch('open-profile-modal')"
    Contoh pemakaian di sidebar ada di file sidebar-trigger-snippet.blade.php
--}}
<div
    x-data="{ open: false, tab: 'info' }"
    x-show="open"
    x-on:open-profile-modal.window="open = true; tab = 'info'"
    x-on:keydown.escape.window="open = false"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="absolute inset-0 bg-black/60"
    ></div>

    {{-- Card --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-sm border rounded-2xl p-6 shadow-xl"
        style="background:#111827; border-color:rgba(255,255,255,0.08);"
        @click.outside="open = false"
    >
        {{-- Tombol tutup --}}
        <button
            @click="open = false"
            type="button"
            class="absolute top-4 right-4 w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-lg font-bold text-white mb-5">Profil Saya</h3>

        {{-- ====== TAB: INFO (foto, nama, email) ====== --}}
        <div x-show="tab === 'info'">

            {{-- Foto profile --}}
            <form
                action="{{ route('profile.photo.update') }}"
                method="POST"
                enctype="multipart/form-data"
                x-data="{ preview: null }"
                class="flex flex-col items-center gap-2 mb-6"
            >
                @csrf
                @method('PATCH')

                <label class="relative cursor-pointer group">
                    <div class="w-[72px] h-[72px] rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-xl overflow-hidden">
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-full object-cover" alt="Preview foto profil">
                        </template>
                        <template x-if="!preview">
                            <span>
                                @if(auth()->user()->photo_path)
                                    <img src="{{ asset('uploads/' . auth()->user()->photo_path) }}" class="w-full h-full object-cover" alt="Foto profil">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                @endif
                            </span>
                        </template>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-[#111827] border border-slate-700 flex items-center justify-center group-hover:border-indigo-500 transition">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <input
                        type="file"
                        name="photo"
                        accept="image/*"
                        class="hidden"
                        @change="
                            const file = $event.target.files[0];
                            if (file) {
                                preview = URL.createObjectURL(file);
                                $el.closest('form').requestSubmit();
                            }
                        "
                    >
                </label>
                <span class="text-xs text-indigo-400">Ganti foto</span>

                @error('photo')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </form>

            {{-- Nama & email --}}
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-3 mb-2">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs text-slate-400 mb-1">Nama</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        class="w-full bg-[#111827] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition"
                    >
                    @error('name')
                        <span class="text-xs text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        class="w-full bg-[#111827] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition"
                    >
                    @error('email')
                        <span class="text-xs text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full mt-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium py-2.5 rounded-xl transition cursor-pointer"
                >
                    Simpan perubahan
                </button>
            </form>

            {{-- Link ke tab password --}}
            <button
                @click="tab = 'password'"
                type="button"
                class="w-full flex items-center gap-2 text-sm text-slate-300 hover:text-indigo-400 border-t border-slate-800/80 pt-4 mt-2 transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Ubah password
            </button>
        </div>

        {{-- ====== TAB: PASSWORD ====== --}}
        <div x-show="tab === 'password'" x-cloak>
            <button
                @click="tab = 'info'"
                type="button"
                class="flex items-center gap-1 text-xs text-slate-400 hover:text-white mb-4 transition cursor-pointer"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali
            </button>

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs text-slate-400 mb-1">Password saat ini</label>
                    <input type="password" name="current_password" class="w-full bg-[#111827] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    @error('current_password')
                        <span class="text-xs text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">Password baru</label>
                    <input type="password" name="password" class="w-full bg-[#111827] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    @error('password')
                        <span class="text-xs text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">Konfirmasi password baru</label>
                    <input type="password" name="password_confirmation" class="w-full bg-[#111827] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                </div>

                <button
                    type="submit"
                    class="w-full mt-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium py-2.5 rounded-xl transition cursor-pointer"
                >
                    Update password
                </button>
            </form>
        </div>
    </div>
</div>