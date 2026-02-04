<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Order;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageRead;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function chatPage(Order $order)
    {
        if (Auth::id() !== $order->user_id && !Auth::user()->is_admin) {
            abort(403);
        }
        
        $targetUser = Auth::user()->is_admin 
            ? $order->user 
            : User::where('is_admin', 1)->first();

        // Kita kirim data order files juga untuk dipilih sebagai "Konteks" (opsional, untuk dropdown/list)
        $designFiles = $order->orderFiles; 

        return view('orders.chat', compact('order', 'targetUser', 'designFiles'));
    }

    public function fetchMessages(Order $order)
    {
        // 1. Update database (seperti sebelumnya)
        $updated = Chat::where('order_id', $order->order_id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // 2. [BARU] Jika ada pesan yang diupdate, beri tahu lawan bicara via Pusher
        if ($updated > 0) {
            broadcast(new MessageRead($order->order_id, Auth::id()))->toOthers();
        }

        // 3. Return data
        return $order->chats()
            ->with(['user', 'referencedFile'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage(Request $request, Order $order)
    {
        // Validasi: Pesan wajib ada KECUALI ada file attachment
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|image|max:5120', // Max 5MB, khusus gambar chat
            'referenced_file_id' => 'nullable|exists:order_files,file_id'
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['status' => 'error', 'message' => 'Pesan atau file tidak boleh kosong'], 422);
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store("chat-attachments/{$order->order_id}", 'public');
        }

        $chat = Chat::create([
            'order_id' => $order->order_id,
            'user_id' => Auth::id(),
            'message' => $request->message ?? '', // Bisa kosong jika cuma kirim gambar
            'attachment' => $path,
            'referenced_file_id' => $request->referenced_file_id // ID dari order_files yang dibahas
        ]);

        // Load relasi agar lengkap saat dikirim balik ke JS / Pusher
        $chat->load(['user', 'referencedFile']);

        broadcast(new MessageSent($chat))->toOthers();

        return response()->json([
            'status' => 'success', 
            'chat' => $chat
        ]);
    }

    public function destroy(Chat $chat)
    {
        // 1. Validasi: Hanya pemilik pesan yang boleh menghapus
        if (Auth::id() != $chat->user_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // 2. Soft Delete: Update flag is_deleted jadi true
        $chat->update([
            'is_deleted' => true
        ]);

        return response()->json(['status' => 'success']);
    }

    public function markAsRead(Order $order)
    {
        // Update semua pesan 'is_read = 0' milik lawan bicara menjadi '1'
        $updated = Chat::where('order_id', $order->order_id)
            ->where('user_id', '!=', Auth::id()) // Pesan dari orang lain
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Jika ada yang diupdate, kirim sinyal 'MessageRead' agar centang pengirim jadi biru
        if ($updated > 0) {
            broadcast(new MessageRead($order->order_id, Auth::id()))->toOthers();
        }

        return response()->json(['status' => 'success']);
    }
}