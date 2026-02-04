@php
    $layout = auth()->check() && auth()->user()->is_admin == 1
        ? 'layouts.admin'
        : 'layouts.main';
@endphp

@extends($layout)

@section('content')
<div class="container mx-auto px-0 md:px-4 py-0 md:py-8 max-w-4xl h-[95vh]">

    {{-- AREA CHAT SYSTEM --}}
    <div x-data="chatSystem(
            '{{ $order->order_id }}', 
            '{{ Auth::id() }}', 
            '{{ session('revision_file_id') }}', 
            '{{ session('revision_file_name') }}'
         )" 
         x-init="initChat()" 
         class="flex flex-col h-full bg-white md:border md:rounded-2xl shadow-xl overflow-hidden relative">
        
        {{-- CHAT HEADER --}}
        <div class="bg-white px-4 py-3 flex justify-between items-center shadow-sm z-10 border-b min-h-[60px]">
            
            {{-- HEADER NORMAL --}}
            <div x-show="!selectionMode" class="flex justify-between items-center w-full">
                <div class="flex items-center gap-3">
                    <div class="bg-white md:bg-transparent px-4 py-3 flex justify-between items-center border-b md:border-0">
                        {{-- Ubah href di sini --}}
                        <a href="{{ url()->previous() }}" class="text-slate-600 hover:text-slate-900 font-bold flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="font-bold text-slate-800 text-sm leading-tight flex items-center gap-2">
                            {{ $targetUser->name ?? 'Admin Support' }}
                            <span class="text-xs font-normal text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                #{{ $order->order_id }}
                            </span>
                        </h3>
                        <span class="text-[10px] text-slate-500 mt-0.5">
                            {{ $order->designType->nama_jenis }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- HEADER MODE SELEKSI --}}
            <div x-show="selectionMode" style="display: none;" class="flex justify-between items-center w-full bg-slate-50 text-slate-800">
                <div class="flex items-center gap-3">
                    <button @click="cancelSelection()" class="p-2 hover:bg-slate-200 rounded-full transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <span class="font-bold text-lg" x-text="selectedMessages.length"></span>
                </div>
                
                <button @click="deleteSelected()" 
                        x-show="selectedMessages.length > 0"
                        class="text-red-600 hover:bg-red-50 p-2 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- AREA PESAN (SCROLLABLE) --}}
        <div x-ref="chatBox" class="flex-1 overflow-y-auto overscroll-y-contain p-4 space-y-2 bg-[#f0f2f5]">
            
            <div x-show="isLoading" class="flex justify-center py-4">
                <svg class="animate-spin h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <template x-for="(chat, index) in messages" :key="chat.id || index">
                <div class="flex flex-col w-full group relative mb-2" 
                     :class="chat.user_id == currentUserId ? 'items-end' : 'items-start'">

                    {{-- PEMBATAS TANGGAL --}}
                    <template x-if="showDateSeparator(index)">
                        <div class="w-full flex justify-center py-4 relative z-0">
                            <span class="bg-[#e3e6e8] text-slate-600 text-[11px] font-medium px-3 py-1 rounded-lg shadow-sm border select-none"
                                  x-text="formatDateDivider(chat.created_at)"></span>
                        </div>
                    </template>

                    {{-- PEMBATAS REVISI --}}
                    <template x-if="chat.referenced_file">
                        <div class="flex items-center gap-2 my-4 w-full">
                            <div class="h-px bg-indigo-300 flex-1"></div>
                            
                            <span class="text-[10px] font-bold text-indigo-600 bg-white border border-indigo-200 px-2 py-1 rounded-lg uppercase tracking-wider">
                                {{-- Panggil fungsi helper untuk hitung urutan --}}
                                Revisi ke-<span x-text="getRevisionNumber(index)"></span>
                            </span>

                            <div class="h-px bg-indigo-300 flex-1"></div>
                        </div>
                    </template>

                    {{-- WRAPPER PESAN UNTUK SELEKSI --}}
                    <div class="flex items-center gap-2 w-full transition-all duration-200"
                         :class="chat.user_id == currentUserId ? 'justify-end' : 'justify-start'">
                        
                        {{-- CHECKBOX AREA --}}
                        <div x-show="selectionMode && chat.user_id == currentUserId && !chat.is_deleted" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-0"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             @click="toggleSelection(chat.id)"
                             class="cursor-pointer p-1 flex-shrink-0 min-w-[24px]">
                             
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                 :class="selectedMessages.includes(chat.id) ? 'bg-indigo-600 border-indigo-600' : 'border-slate-400 bg-white'">
                                <svg x-show="selectedMessages.includes(chat.id)" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>

                        {{-- BALON CHAT --}}
                        <div class="relative max-w-[85%]"
                             :class="[
                                selectionMode && chat.user_id == currentUserId && !chat.is_deleted ? 'cursor-pointer opacity-90 hover:opacity-100' : '',
                                activeMenuId === chat.id ? 'z-30' : 'z-0'
                             ]"
                             @click="selectionMode && chat.user_id == currentUserId && !chat.is_deleted ? toggleSelection(chat.id) : null">
                            
                            <div class="rounded-lg px-3 py-1.5 shadow-sm text-sm border border-black/5 relative transition-colors" 
                                 :class="[
                                    chat.user_id == currentUserId ? 'bg-[#d9fdd3] rounded-tr-none' : 'bg-white rounded-tl-none',
                                    selectionMode && selectedMessages.includes(chat.id) ? 'ring-2 ring-indigo-400 bg-indigo-50' : ''
                                 ]">

                                {{-- ================= TOMBOL TITIK TIGA (DROPDOWN) ================= --}}
                                <template x-if="chat.user_id == currentUserId && !chat.is_deleted && !selectionMode">
                                    <div class="absolute top-0 right-0 p-1">
                                        {{-- 
                                            PERBAIKAN SINGLE OPEN:
                                            - Menggunakan toggleMenu(chat.id) alih-alih local state.
                                        --}}
                                        <button @click.stop="toggleMenu(chat.id)" 
                                                class="text-slate-400 hover:text-slate-600 transition-colors p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 30 30" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>

                                        {{-- MENU DROPDOWN --}}
                                        <div x-show="activeMenuId === chat.id" 
                                             @click.away="activeMenuId = null"
                                             style="display: none;"
                                             class="absolute right-0 top-6 w-32 bg-white rounded-lg shadow-xl border border-slate-100 py-1 z-50 overflow-hidden">
                                            
                                            {{-- Opsi 1: Pilih --}}
                                            <button type="button" 
                                                    @click.stop="enableSelection(chat.id); activeMenuId = null" 
                                                    class="w-full text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 flex items-center gap-2 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Pilih Pesan
                                            </button>

                                            {{-- Opsi 2: Hapus --}}
                                            <button type="button"
                                                    @click.stop="deleteMessage(chat.id); activeMenuId = null" 
                                                    class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 flex items-center gap-2 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- TAMPILAN JIKA DIHAPUS --}}
                                <template x-if="chat.is_deleted">
                                    <p class="text-slate-400 italic text-xs flex items-center gap-1 py-1 select-none">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        Pesan ini telah dihapus
                                    </p>
                                </template>

                                {{-- TAMPILAN NORMAL (BELUM DIHAPUS) --}}
                                <template x-if="!chat.is_deleted">
                                    <div class="pr-6">
                                        {{-- Konteks Revisi (Updated Content, Old Style) --}}
                                    <template x-if="chat.referenced_file">
                                        <div class="mb-2 p-2 bg-black/5 rounded text-xs border-l-4 border-orange-500 flex justify-between items-center gap-2 relative z-0">
                                            
                                            {{-- Info File (Kiri) --}}
                                            <div>
                                                <p class="font-bold text-slate-700 text-[10px]">
                                                    {{-- Tipe File --}}
                                                    <span x-text="chat.referenced_file.tipe_file"></span>
                                                    
                                                    {{-- Hitung Urutan Revisi --}}
                                                    <template x-if="chat.referenced_file.tipe_file === 'Revisi'">
                                                        <span x-text="' #' + getRevisionNumber(index)"></span>
                                                    </template>
                                                </p>
                                            </div>

                                            {{-- Tombol Aksi (Kanan) --}}
                                            <div class="flex gap-2">
                                                {{-- Tombol Lihat (Hanya untuk Gambar) --}}
                                                <a href="#" 
                                                   @click.prevent="openImageModal(chat.referenced_file.path_file)" 
                                                   x-show="isImage(chat.referenced_file.path_file)"
                                                   class="text-blue-600 font-bold hover:underline text-[10px]">
                                                    Lihat
                                                </a>

                                                {{-- Tombol Unduh (Untuk Dokumen) --}}
                                                <a :href="'/storage/' + chat.referenced_file.path_file" 
                                                   target="_blank" 
                                                   x-show="!isImage(chat.referenced_file.path_file)"
                                                   class="text-blue-600 font-bold hover:underline text-[10px]">
                                                    Unduh
                                                </a>
                                            </div>
                                        </div>
                                    </template>

                                        {{-- Gambar Attachment --}}
                                        <template x-if="chat.attachment">
                                            <div class="mb-2 mt-1">
                                                <img :src="'/storage/' + chat.attachment" class="rounded-md max-h-48 border border-slate-200 cursor-pointer object-cover bg-slate-100" @click="openImageModal(chat.attachment)">
                                            </div>
                                        </template>

                                        {{-- Teks Pesan --}}
                                        <template x-if="chat.message">
                                             <p x-text="chat.message" class="whitespace-pre-wrap leading-snug"></p>
                                        </template>
                                    </div>
                                </template>

                                {{-- Waktu & Centang --}}
                                <div class="text-[10px] text-slate-500 text-right mt-1 flex justify-end items-center gap-1 select-none">
                                    <span x-text="formatTime(chat.created_at)"></span>
                                    
                                    {{-- Ikon Centang Realtime --}}
                                    <template x-if="chat.user_id == currentUserId && !chat.is_deleted">
                                        {{-- Container Flex untuk warna dinamis --}}
                                        <div class="flex items-center transition-colors duration-300"
                                            :class="chat.is_read ? 'text-blue-500' : 'text-slate-400'">
                                            
                                            {{-- Icon Check Circle Solid --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                            </svg> 
                                        </div>
                                    </template>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- AREA PREVIEW SEBELUM KIRIM --}}
        <div class="bg-slate-50 px-3 py-2 flex flex-col gap-2 border-t z-20" 
             x-show="(contextFileId || draftPreview) && !selectionMode" 
             style="display: none;">
            
            <template x-if="contextFileId">
                <div class="flex items-center justify-between bg-orange-50 border border-orange-200 text-orange-800 px-3 py-2 rounded-lg text-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        <span class="font-bold">Menanggapi: <span x-text="contextFileName || 'File Desain'"></span></span>
                    </div>
                    <button @click="contextFileId = null; contextFileName = null" class="text-orange-400 hover:text-orange-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </template>

            <template x-if="draftPreview">
                <div class="relative w-fit">
                    <img :src="draftPreview" class="h-24 w-auto rounded-lg border border-slate-300 shadow-sm object-cover">
                    <button @click="clearFileAttachment()" 
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </template>
        </div>

        {{-- INPUT AREA (HANYA MUNCUL SAAT REVISI) --}}
        @if($order->status_pesanan === 'Revisi')
            <div class="bg-white px-3 py-2 flex items-center gap-2 z-20 border-t" x-show="!selectionMode">
                <div class="relative">
                    <button type="button" @click="$refs.fileInput.click()" 
                            class="text-slate-500 hover:text-slate-700 p-2 rounded-full hover:bg-black/5 transition">
                        {{-- Ikon Gambar / Galeri --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </button>                
                    <input type="file" x-ref="fileInput" class="hidden" @change="handleFileSelect($event)" accept="image/*">
                </div>

                <div class="flex-1 bg-[#f0f2f5] rounded-lg flex items-center px-4 py-2 shadow-sm border border-slate-200">
                    <input type="text" x-model="newMessage" @keydown.enter="sendMessage()"
                           class="w-full bg-transparent border-0 focus:ring-0 text-sm text-slate-800 placeholder-slate-400 p-0 outline-none"
                           placeholder="Ketik pesan..." autocomplete="off">
                </div>
                
                <button type="button" @click="sendMessage()" 
                        :disabled="!newMessage.trim() && !fileAttachment"
                        :class="(newMessage.trim() || fileAttachment) ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-slate-300 cursor-not-allowed'"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-0.5 transform rotate-90" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </div>
        @else
            {{-- INFO JIKA STATUS BUKAN REVISI (READ ONLY) --}}
            <div class="bg-slate-50 px-4 py-4 z-20 border-t text-center" x-show="!selectionMode">
                <div class="flex flex-col items-center justify-center text-slate-500 gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p class="text-[11px] text-slate-400">Anda hanya dapat mengirim pesan saat <strong>Revisi</strong>.</p>
                </div>
            </div>
        @endif

        {{-- Modal Preview Fullscreen --}}
        <div x-show="previewImage" style="display: none;"
             class="absolute inset-0 z-50 bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm"
             x-transition.opacity>
            <button @click="previewImage = null" class="absolute top-4 right-4 text-white hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img :src="'/storage/' + previewImage" class="max-w-full max-h-full rounded shadow-2xl">
        </div>
    </div>
</div>

<script>
    function chatSystem(orderId, userId, initialContextId, initialContextName) {
        return {
            messages: [],
            newMessage: '',
            contextFileId: initialContextId || null, 
            contextFileName: initialContextName || null,
            fileAttachment: null,
            draftPreview: null,
            currentUserId: userId,
            isLoading: true,
            previewImage: null,
            
            // Logika Seleksi
            selectionMode: false,
            selectedMessages: [],
            
            // LOGIKA MENU (SINGLE OPEN)
            activeMenuId: null,

            initChat() {
                this.fetchMessages(); // Ambil pesan lama
                
                // Panggil ini saat awal buka biar notif pesan lama hilang
                this.markMessagesRead(); 

                if (window.Echo) {
                    Echo.private(`order.${orderId}`)
                        
                        // 1. SAAT PESAN MASUK
                        .listen('MessageSent', (e) => {
                            this.messages.push({
                                id: Date.now(),
                                user_id: e.user_id,
                                message: e.message,
                                attachment: e.attachment,
                                referenced_file: e.referenced_file,
                                created_at: new Date().toISOString(),
                                user: { name: e.user_name },
                                is_deleted: false,
                                is_read: false 
                            });
                            this.scrollToBottom();

                            // [KUNCI REALTIME TANPA REFRESH]
                            // Jika pesan yang masuk BUKAN dari saya (berarti dari lawan bicara),
                            // dan saya sedang membuka halaman ini, maka LANGSUNG tandai sebagai dibaca.
                            if (e.user_id != this.currentUserId) {
                                this.markMessagesRead();
                            }
                        })

                        // 2. SAAT PESAN SAYA DIBACA OLEH LAWAN BICARA
                        .listen('MessageRead', (e) => {
                            if (e.readerId != this.currentUserId) {
                                // Ubah status semua pesan saya jadi biru
                                this.messages.forEach(msg => {
                                    if (msg.user_id == this.currentUserId) {
                                        msg.is_read = true;
                                    }
                                });
                                // Pancing AlpineJS update tampilan
                                this.messages = [...this.messages]; 
                            }
                        });
                }
            },

            markMessagesRead() {
                axios.post(`/orders/${orderId}/mark-read`)
                    .then(response => {
                        console.log('Semua pesan ditandai terbaca.');
                    })
                    .catch(err => console.error(err));
            },

            fetchMessages() {
                axios.get(`/orders/${orderId}/messages`)
                    .then(response => {
                        this.messages = response.data;
                        this.scrollToBottom();
                        this.isLoading = false;
                    })
                    .catch(e => {
                        console.error(e);
                        this.isLoading = false;
                    });
            },

            // --- FUNGSI MENU ---
            toggleMenu(id) {
                // Jika ID yang diklik sama dengan yang aktif, tutup. Jika beda, buka yang baru.
                this.activeMenuId = (this.activeMenuId === id) ? null : id;
            },

            // --- LOGIKA SELEKSI & HAPUS BANYAK ---
            enableSelection(initialId) {
                console.log('Mengaktifkan mode seleksi untuk ID:', initialId);
                this.selectionMode = true;
                this.selectedMessages = [initialId];
                this.activeMenuId = null; // Tutup menu setelah klik pilih
            },

            toggleSelection(id) {
                if (this.selectedMessages.includes(id)) {
                    this.selectedMessages = this.selectedMessages.filter(msgId => msgId !== id);
                    if (this.selectedMessages.length === 0) {
                        this.selectionMode = false;
                    }
                } else {
                    this.selectedMessages.push(id);
                }
            },

            cancelSelection() {
                this.selectionMode = false;
                this.selectedMessages = [];
            },

            deleteSelected() {
                const count = this.selectedMessages.length;
                if(!confirm(`Hapus ${count} pesan terpilih?`)) return;

                const promises = this.selectedMessages.map(id => axios.delete(`/chats/${id}`));

                Promise.all(promises)
                    .then(() => {
                        this.selectedMessages.forEach(id => {
                            const index = this.messages.findIndex(m => m.id === id);
                            if(index !== -1) {
                                this.messages[index].is_deleted = true;
                                this.messages[index].message = null; 
                                this.messages[index].attachment = null;
                            }
                        });
                        this.cancelSelection();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal menghapus beberapa pesan.');
                        this.cancelSelection();
                    });
            },

            deleteMessage(chatId) {
                if(!confirm('Hapus pesan ini?')) return;

                axios.delete(`/chats/${chatId}`)
                    .then(response => {
                        if(response.data.status === 'success') {
                            const index = this.messages.findIndex(m => m.id === chatId);
                            if(index !== -1) {
                                this.messages[index].is_deleted = true;
                                this.messages[index].message = null; 
                                this.messages[index].attachment = null;
                            }
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal menghapus pesan.');
                    });
            },
            // -------------------------------------

            showDateSeparator(index) {
                if (index === 0) return true;
                const currentDate = new Date(this.messages[index].created_at).toDateString();
                const prevDate = new Date(this.messages[index - 1].created_at).toDateString();
                return currentDate !== prevDate;
            },

            formatDateDivider(dateString) {
                if (!dateString) return '';
                return new Date(dateString).toLocaleDateString('id-ID', { 
                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
                });
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.fileAttachment = file;
                    if (this.isImage(file.name)) {
                        this.draftPreview = URL.createObjectURL(file);
                    } else {
                        this.draftPreview = null;
                    }
                }
            },

            clearFileAttachment() {
                this.fileAttachment = null;
                this.draftPreview = null;
                if(this.$refs.fileInput) this.$refs.fileInput.value = '';
            },

            sendMessage() {
                if (!this.newMessage.trim() && !this.fileAttachment) return;

                let formData = new FormData();
                formData.append('message', this.newMessage);
                if (this.fileAttachment) formData.append('attachment', this.fileAttachment);
                if (this.contextFileId) formData.append('referenced_file_id', this.contextFileId);

                const tempMsg = this.newMessage;
                
                this.newMessage = '';
                this.clearFileAttachment();
                this.contextFileId = null;

                axios.post(`/orders/${orderId}/messages`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                .then(response => {
                    if(response.data.status === 'success') {
                        this.messages.push(response.data.chat);
                        this.scrollToBottom();
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Gagal mengirim pesan.');
                    this.newMessage = tempMsg;
                });
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    if(this.$refs.chatBox) {
                        this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
                    }
                });
            },

            formatTime(dateString) {
                if (!dateString) return '';
                return new Date(dateString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            },

            isImage(path) {
                if(!path) return false;
                const extension = path.split('.').pop().toLowerCase();
                return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
            },

            getRevisionNumber(currentIndex) {
                let count = 0;
                // Loop dari pesan pertama (0) sampai pesan saat ini (currentIndex)
                for (let i = 0; i <= currentIndex; i++) {
                    // Jika pesan pada indeks tersebut memiliki referenced_file, hitung +1
                    if (this.messages[i].referenced_file) {
                        count++;
                    }
                }
                return count;
            },

            openImageModal(path) {
                this.previewImage = path;
            }
        }
    }
</script>
@endsection