<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $messages = $this->messageQuery()
            ->latest('id')
            ->take(60)
            ->get()
            ->reverse()
            ->values();

        $coupleMembers = $user->couple->users()->orderBy('name')->get();

        return view('chats.index', compact('messages', 'coupleMembers'));
    }

    public function messages(Request $request)
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = $afterId > 0
            ? $this->messageQuery()
                ->where('id', '>', $afterId)
                ->orderBy('id')
                ->take(80)
                ->get()
            : $this->messageQuery()
                ->latest('id')
                ->take(80)
                ->get()
                ->reverse()
                ->values();

        $this->markPartnerMessagesAsRead();

        return response()->json([
            'messages' => $messages->map(fn(ChatMessage $message) => $this->serializeMessage($message))->values(),
            'meta' => [
                'after_id' => $afterId,
                'latest_id' => $this->messageQuery()->max('id'),
                'couple_id' => Auth::user()->couple_id,
                'user_id' => Auth::id(),
            ],
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'body' => 'nullable|required_without:attachment|string|max:1000',
            'attachment' => [
                'nullable',
                'file',
                'max:12288',
            ],
            'attachment_type' => ['nullable', Rule::in(['image', 'audio'])],
            'audio_duration' => 'nullable|integer|min:0|max:3600',
        ]);

        $attachment = $this->storeAttachment($request);

        $message = ChatMessage::create(array_merge([
            'couple_id' => Auth::user()->couple_id,
            'user_id' => Auth::id(),
            'body' => trim($data['body'] ?? ''),
        ], $attachment))->load('user');

        return response()->json([
            'success' => true,
            'message' => $this->serializeMessage($message),
        ]);
    }

    public function attachment(ChatMessage $chatMessage)
    {
        abort_unless((int) $chatMessage->couple_id === (int) Auth::user()->couple_id, 403);
        abort_unless($chatMessage->attachment_path, 404);
        abort_unless(Storage::disk('public')->exists($chatMessage->attachment_path), 404);

        return Storage::disk('public')->response(
            $chatMessage->attachment_path,
            null,
            ['Cache-Control' => 'private, max-age=86400']
        );
    }

    public function update(Request $request, ChatMessage $chatMessage)
    {
        $this->authorizeOwnMessage($chatMessage);

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $chatMessage->update([
            'body' => trim($data['body']),
            'edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->serializeMessage($chatMessage->fresh('user')),
        ]);
    }

    public function destroy(ChatMessage $chatMessage)
    {
        $this->authorizeOwnMessage($chatMessage);

        if ($chatMessage->attachment_path) {
            Storage::disk('public')->delete($chatMessage->attachment_path);
        }

        $chatMessage->delete();

        return response()->json([
            'success' => true,
            'id' => $chatMessage->id,
        ]);
    }

    protected function messageQuery()
    {
        return ChatMessage::with('user')
            ->where('couple_id', Auth::user()->couple_id);
    }

    protected function markPartnerMessagesAsRead(): void
    {
        ChatMessage::where('couple_id', Auth::user()->couple_id)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    protected function serializeMessage(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'user_id' => $message->user_id,
            'name' => $message->user->name,
            'avatar' => $message->user->avatar_display,
            'photo' => $message->user->profile_photo_url,
            'body' => $message->body,
            'attachment_type' => $message->attachment_type,
            'attachment_url' => $message->attachment_path ? route('chats.attachment', $message, false) : null,
            'attachment_mime' => $message->attachment_mime,
            'attachment_size' => $message->attachment_size,
            'audio_duration' => $message->audio_duration,
            'is_me' => (int) $message->user_id === (int) Auth::id(),
            'read_at' => optional($message->read_at)->toIso8601String(),
            'edited_at' => optional($message->edited_at)->toIso8601String(),
            'is_edited' => filled($message->edited_at),
            'created_at' => $message->created_at->toIso8601String(),
            'time' => $message->created_at->format('H:i'),
            'day' => $message->created_at->isoFormat('D MMM Y'),
        ];
    }

    protected function storeAttachment(Request $request): array
    {
        if (! $request->hasFile('attachment')) {
            return [];
        }

        $file = $request->file('attachment');
        $mime = $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream';
        $requestedType = $request->input('attachment_type');
        $type = $requestedType ?: (str_starts_with($mime, 'image/') ? 'image' : 'audio');

        $imageMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $audioMimes = [
            'audio/webm',
            'video/webm',
            'audio/ogg',
            'application/ogg',
            'audio/mpeg',
            'audio/mp4',
            'video/mp4',
            'audio/aac',
            'audio/wav',
            'audio/x-wav',
            'audio/x-m4a',
            'application/octet-stream',
        ];

        if ($type === 'image' && ! in_array($mime, $imageMimes, true)) {
            $this->unsupportedAttachment($mime);
        }

        if ($type === 'audio' && ! in_array($mime, $audioMimes, true) && ! str_starts_with($mime, 'audio/')) {
            $this->unsupportedAttachment($mime);
        }

        return [
            'attachment_type' => $type,
            'attachment_path' => $file->store('chat-attachments/' . Auth::user()->couple_id, 'public'),
            'attachment_mime' => $mime,
            'attachment_size' => $file->getSize(),
            'audio_duration' => $type === 'audio' ? $request->integer('audio_duration') : null,
        ];
    }

    protected function unsupportedAttachment(string $mime): void
    {
        throw ValidationException::withMessages([
            'attachment' => "Format lampiran belum didukung di server ini. MIME terdeteksi: {$mime}.",
        ]);
    }

    protected function authorizeOwnMessage(ChatMessage $chatMessage): void
    {
        abort_unless((int) $chatMessage->couple_id === (int) Auth::user()->couple_id, 403);
        abort_unless((int) $chatMessage->user_id === (int) Auth::id(), 403);
    }
}
