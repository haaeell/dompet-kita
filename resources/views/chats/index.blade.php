@extends('layouts.app')
@section('title', 'Chat Pasangan')

@section('content')
    @php
        $initialMessages = $messages->map(fn($message) => [
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
            'is_me' => $message->user_id === auth()->id(),
            'read_at' => optional($message->read_at)->toIso8601String(),
            'edited_at' => optional($message->edited_at)->toIso8601String(),
            'is_edited' => filled($message->edited_at),
            'created_at' => $message->created_at->toIso8601String(),
            'time' => $message->created_at->format('H:i'),
            'day' => $message->created_at->isoFormat('D MMM Y'),
        ])->values();
    @endphp

    <script>
        document.documentElement.classList.add('chat-page');
    </script>

    <style>
        html.chat-page,
        html.chat-page body {
            height: 100%;
            overflow: hidden;
        }

        html.chat-page .layout-wrapper {
            height: 100dvh;
            overflow: hidden;
        }

        html.chat-page .main-content {
            height: 100%;
            overflow: hidden;
            padding: 24px;
        }

        .chat-shell {
            --chat-bubble-color: #db2777;
            --chat-bubble-color-dark: #be185d;
            --chat-bg-base: #fbfdff;
            --chat-bg-pattern-a: rgba(251, 207, 232, .22);
            --chat-bg-pattern-b: rgba(6, 182, 212, .13);
            height: calc(100dvh - 140px);
            min-height: 0;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 18px 54px rgba(15, 23, 42, .07);
        }

        .chat-header {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(135deg, #fff 0%, #fdf2f8 55%, #ecfeff 100%);
        }

        .chat-avatar-stack {
            display: flex;
            align-items: center;
            min-width: 74px;
        }

        .chat-avatar {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            overflow: hidden;
            border: 3px solid #fff;
            border-radius: 999px;
            background: #fce7f3;
            color: #be185d;
            font-weight: 800;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .12);
        }

        .chat-avatar + .chat-avatar {
            margin-left: -12px;
        }

        .chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-thread {
            min-height: 0;
            overflow-y: auto;
            padding: 18px 16px 22px;
            background:
                linear-gradient(90deg, var(--chat-bg-pattern-a) 1px, transparent 1px),
                linear-gradient(var(--chat-bg-pattern-b) 1px, transparent 1px),
                var(--chat-bg-base);
            background-size: 34px 34px;
        }

        .chat-day {
            display: flex;
            justify-content: center;
            margin: 8px 0 14px;
        }

        .chat-day span {
            border-radius: 999px;
            background: rgba(255, 255, 255, .92);
            padding: 6px 10px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .chat-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            margin: 8px 0;
        }

        .chat-row.me {
            justify-content: flex-end;
        }

        .chat-bubble {
            max-width: min(620px, 78%);
            border-radius: 20px 20px 20px 6px;
            padding: 10px 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        }

        .chat-row.me .chat-bubble {
            background: var(--chat-bubble-color);
            border-color: var(--chat-bubble-color);
            color: #fff;
            border-radius: 20px 20px 6px 20px;
        }

        .chat-name {
            margin-bottom: 3px;
            font-size: 11px;
            font-weight: 800;
            color: #db2777;
        }

        .chat-row.me .chat-name {
            color: rgba(255, 255, 255, .82);
        }

        .chat-body {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            font-size: 14px;
            line-height: 1.55;
        }

        .chat-media {
            margin-bottom: 8px;
        }

        .chat-image {
            display: block;
            width: min(320px, 70vw);
            max-height: 360px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, .38);
            background: rgba(255, 255, 255, .24);
        }

        .chat-audio {
            width: min(320px, 70vw);
            height: 42px;
        }

        .chat-meta {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
            font-size: 10px;
            color: #94a3b8;
        }

        .chat-row.me .chat-meta {
            color: rgba(255, 255, 255, .72);
        }

        .chat-composer {
            display: grid;
            grid-template-columns: 42px 42px minmax(0, 1fr) 46px;
            gap: 10px;
            margin: 0 14px 14px;
            padding: 10px;
            border: 1px solid #f1f5f9;
            border-radius: 22px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 16px 42px rgba(15, 23, 42, .1);
            position: sticky;
            bottom: 0;
            z-index: 5;
        }

        .chat-input {
            min-height: 46px;
            max-height: 128px;
            resize: none;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 12px 14px;
            color: #0f172a;
            outline: none;
        }

        .chat-input:focus {
            border-color: #f9a8d4;
            box-shadow: 0 0 0 4px rgba(249, 168, 212, .18);
        }

        .chat-send {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: var(--chat-bubble-color);
            color: #fff;
            transition: .2s ease;
        }

        .chat-send:hover {
            background: var(--chat-bubble-color-dark);
            transform: translateY(-1px);
        }

        .chat-tool {
            width: 42px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            transition: .2s ease;
        }

        .chat-tool:hover,
        .chat-tool.recording {
            border-color: #f9a8d4;
            background: #fdf2f8;
            color: #db2777;
        }

        .chat-attachment-preview {
            grid-column: 1 / -1;
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-radius: 8px;
            border: 1px solid #fbcfe8;
            background: #fdf2f8;
            padding: 9px 10px;
            color: #9d174d;
            font-size: 12px;
            font-weight: 700;
        }

        .chat-attachment-preview.active {
            display: flex;
        }

        .chat-preview-content {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .chat-preview-image {
            width: 58px;
            height: 58px;
            flex: 0 0 auto;
            display: none;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .12);
        }

        .chat-preview-image.active {
            display: block;
        }

        .chat-recording-chip {
            grid-column: 1 / -1;
            display: none;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            background: #fff1f2;
            padding: 9px 10px;
            color: #be123c;
            font-size: 12px;
            font-weight: 800;
        }

        .chat-recording-chip.active {
            display: flex;
        }

        .chat-theme-button {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: #fff;
            color: var(--chat-bubble-color);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .1);
        }

        .chat-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .chat-theme-panel {
            position: absolute;
            right: 16px;
            top: 74px;
            z-index: 20;
            width: min(320px, calc(100vw - 32px));
            display: none;
            border-radius: 18px;
            border: 1px solid #f1f5f9;
            background: rgba(255, 255, 255, .96);
            padding: 14px;
            box-shadow: 0 22px 60px rgba(15, 23, 42, .18);
            backdrop-filter: blur(14px);
        }

        .chat-theme-panel.active {
            display: block;
        }

        .chat-swatch,
        .chat-bg-option {
            height: 34px;
            border-radius: 999px;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #e2e8f0;
        }

        .chat-bg-option {
            height: 42px;
            border-radius: 14px;
            background-size: 28px 28px;
        }

        .chat-message-actions {
            display: none;
            gap: 6px;
            margin-top: 7px;
            justify-content: flex-end;
        }

        .chat-row.me .chat-bubble:hover .chat-message-actions,
        .chat-row.me .chat-bubble:focus-within .chat-message-actions {
            display: flex;
        }

        .chat-action-button {
            border-radius: 999px;
            background: rgba(255, 255, 255, .18);
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 800;
            color: rgba(255, 255, 255, .9);
        }

        .chat-row.pending .chat-bubble {
            opacity: .78;
        }

        @media (max-width: 768px) {
            html.chat-page .layout-wrapper {
                height: 100dvh;
                min-height: 100dvh;
                overflow: hidden;
            }

            html.chat-page .main-content {
                height: 100dvh;
                min-height: 100dvh;
                overflow: hidden;
                padding: 0;
                padding-top: 68px;
                padding-bottom: 0;
            }

            .chat-page-title {
                display: none !important;
            }

            .chat-shell {
                height: calc(100dvh - 68px);
                min-height: 0;
                border-left: 0;
                border-right: 0;
                border-radius: 0;
                margin: 0 -16px;
            }

            .chat-bubble {
                max-width: 82%;
            }

            .chat-composer {
                grid-template-columns: 40px 40px minmax(0, 1fr) 44px;
                margin: 0 10px calc(env(safe-area-inset-bottom, 0px) + 86px);
            }
        }
    </style>

    <div class="chat-page-title flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="page-title">Chat Pasangan</h1>
            <p class="page-subtitle">Ruang kecil buat ngobrol cepat berdua.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-ghost w-full sm:w-auto justify-center">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <section class="chat-shell">
        <header class="chat-header">
            <div class="flex items-center gap-3 min-w-0">
                <div class="chat-avatar-stack">
                    @foreach($coupleMembers->take(3) as $member)
                        <div class="chat-avatar">
                            @if($member->profile_photo_url)
                                <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}">
                            @else
                                {{ $member->avatar_display }}
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900 truncate">{{ auth()->user()->couple->couple_name }}</div>
                    <div id="chatStatus" class="text-xs text-slate-500 mt-0.5">Realtime ringan aktif</div>
                </div>
            </div>
            <div class="chat-header-actions">
                <button id="chatNotifyButton" type="button" class="chat-theme-button" title="Aktifkan notifikasi">
                    <i class="fa-solid fa-bell"></i>
                </button>
                <button id="chatThemeButton" type="button" class="chat-theme-button" title="Tema chat">
                    <i class="fa-solid fa-palette"></i>
                </button>
            </div>
            <div id="chatThemePanel" class="chat-theme-panel">
                <div class="text-xs font-extrabold text-slate-500 mb-2">Warna bubble</div>
                <div class="grid grid-cols-6 gap-2 mb-4">
                    <button type="button" class="chat-swatch" data-chat-color="#db2777" data-chat-color-dark="#be185d" style="background:#db2777"></button>
                    <button type="button" class="chat-swatch" data-chat-color="#7c3aed" data-chat-color-dark="#6d28d9" style="background:#7c3aed"></button>
                    <button type="button" class="chat-swatch" data-chat-color="#0891b2" data-chat-color-dark="#0e7490" style="background:#0891b2"></button>
                    <button type="button" class="chat-swatch" data-chat-color="#16a34a" data-chat-color-dark="#15803d" style="background:#16a34a"></button>
                    <button type="button" class="chat-swatch" data-chat-color="#ea580c" data-chat-color-dark="#c2410c" style="background:#ea580c"></button>
                    <button type="button" class="chat-swatch" data-chat-color="#334155" data-chat-color-dark="#1e293b" style="background:#334155"></button>
                </div>

                <div class="text-xs font-extrabold text-slate-500 mb-2">Background</div>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" class="chat-bg-option" data-chat-bg="grid" style="background-color:#fbfdff;background-image:linear-gradient(90deg, rgba(251, 207, 232, .35) 1px, transparent 1px),linear-gradient(rgba(6, 182, 212, .2) 1px, transparent 1px);"></button>
                    <button type="button" class="chat-bg-option" data-chat-bg="soft" style="background:linear-gradient(135deg,#fff7fb,#ecfeff);"></button>
                    <button type="button" class="chat-bg-option" data-chat-bg="mint" style="background-color:#f0fdfa;background-image:radial-gradient(circle at 8px 8px, rgba(20,184,166,.18) 2px, transparent 3px);"></button>
                </div>
                <input id="chatBgImageInput" type="file" accept="image/*" class="hidden">
                <button id="chatBgImageButton" type="button" class="mt-3 w-full rounded-xl border border-pink-100 bg-pink-50 px-3 py-2 text-xs font-bold text-pink-700">
                    <i class="fa-solid fa-image mr-1"></i> Pakai gambar sendiri
                </button>
            </div>
        </header>

        <div id="chatThread" class="chat-thread" aria-live="polite"></div>

        <form id="chatForm" class="chat-composer" data-skip-page-loader>
            <div id="chatAttachmentPreview" class="chat-attachment-preview">
                <div class="chat-preview-content">
                    <img id="chatPreviewImage" class="chat-preview-image" alt="Preview foto">
                    <span id="chatAttachmentText"></span>
                </div>
                <button id="chatClearAttachment" type="button" class="text-pink-700" title="Hapus lampiran">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="chatRecordingChip" class="chat-recording-chip">
                <i class="fa-solid fa-circle text-[8px]"></i>
                <span id="chatRecordingText">Merekam suara...</span>
            </div>
            <input id="chatImageInput" type="file" accept="image/*" class="hidden">
            <button id="chatImageButton" type="button" class="chat-tool" title="Kirim foto">
                <i class="fa-solid fa-image"></i>
            </button>
            <button id="chatVoiceButton" type="button" class="chat-tool" title="Rekam voice note">
                <i class="fa-solid fa-microphone"></i>
            </button>
            <textarea id="chatInput" class="chat-input" rows="1" maxlength="1000" placeholder="Tulis pesan..."></textarea>
            <button id="chatSendButton" type="submit" class="chat-send" title="Kirim">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        const chatInitialMessages = @json($initialMessages);
        const chatMessagesUrl = @json(route('chats.messages'));
        const chatStoreUrl = @json(route('chats.store'));
        const chatUpdateUrlTemplate = @json(route('chats.update', ['chatMessage' => '__ID__']));
        const chatDeleteUrlTemplate = @json(route('chats.destroy', ['chatMessage' => '__ID__']));
        const chatFirebaseConfig = @json(config('firebase.web'));
        const chatFirebaseReady = Boolean(chatFirebaseConfig.apiKey && chatFirebaseConfig.projectId && chatFirebaseConfig.appId);
        const chatCoupleId = @json((string) auth()->user()->couple_id);
        const chatCurrentUserId = @json(auth()->id());
        const chatCsrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const chatThread = document.getElementById('chatThread');
        const chatForm = document.getElementById('chatForm');
        const chatInput = document.getElementById('chatInput');
        const chatSendButton = document.getElementById('chatSendButton');
        const chatStatus = document.getElementById('chatStatus');
        const chatImageInput = document.getElementById('chatImageInput');
        const chatImageButton = document.getElementById('chatImageButton');
        const chatVoiceButton = document.getElementById('chatVoiceButton');
        const chatAttachmentPreview = document.getElementById('chatAttachmentPreview');
        const chatAttachmentText = document.getElementById('chatAttachmentText');
        const chatPreviewImage = document.getElementById('chatPreviewImage');
        const chatClearAttachment = document.getElementById('chatClearAttachment');
        const chatRecordingChip = document.getElementById('chatRecordingChip');
        const chatRecordingText = document.getElementById('chatRecordingText');
        const chatShell = document.querySelector('.chat-shell');
        const chatThemeButton = document.getElementById('chatThemeButton');
        const chatThemePanel = document.getElementById('chatThemePanel');
        const chatNotifyButton = document.getElementById('chatNotifyButton');
        const chatBgImageButton = document.getElementById('chatBgImageButton');
        const chatBgImageInput = document.getElementById('chatBgImageInput');
        const renderedMessages = new Map();
        let lastMessageId = 0;
        let lastRenderedDay = null;
        let polling = false;
        let pendingAttachment = null;
        let mediaRecorder = null;
        let audioChunks = [];
        let recordingStartedAt = 0;
        let recordingTimer = null;
        let firebaseEnabled = false;
        let firestoreAddDoc = null;
        let firestoreCollection = null;
        let firestoreDb = null;
        let pollingTimer = null;

        function chatDebug(message, detail = null) {
            if (window.DompetKitaChatDebug) {
                console.log(`[DompetKita Chat] ${message}`, detail || '');
            }
        }

        function setChatStatus(message, detail = null) {
            chatStatus.textContent = message;
            if (detail) chatStatus.title = String(detail);
            chatDebug(message, detail);
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));
        }

        function isNearBottom() {
            return chatThread.scrollHeight - chatThread.scrollTop - chatThread.clientHeight < 140;
        }

        function scrollToBottom() {
            chatThread.scrollTop = chatThread.scrollHeight;
        }

        function normalizeMessage(message) {
            const normalized = { ...message };
            normalized.is_me = Number(normalized.user_id) === Number(chatCurrentUserId);
            normalized.is_pending = Boolean(normalized.is_pending);
            normalized.is_edited = Boolean(normalized.is_edited);
            return normalized;
        }

        function avatarMarkup(message) {
            if (message.is_me) return '';
            if (message.photo) {
                return `<div class="chat-avatar !h-8 !w-8 !border-2"><img src="${escapeHtml(message.photo)}" alt="${escapeHtml(message.name)}"></div>`;
            }
            return `<div class="chat-avatar !h-8 !w-8 !border-2">${escapeHtml(message.avatar || '')}</div>`;
        }

        function attachmentMarkup(message) {
            if (!message.attachment_url) return '';

            if (message.attachment_type === 'image') {
                return `
                    <a class="chat-media block" href="${escapeHtml(message.attachment_url)}" target="_blank" rel="noopener">
                        <img class="chat-image" src="${escapeHtml(message.attachment_url)}" alt="Foto chat" loading="lazy">
                    </a>
                `;
            }

            if (message.attachment_type === 'audio') {
                return `
                    <div class="chat-media">
                        <audio class="chat-audio" controls preload="metadata" src="${escapeHtml(message.attachment_url)}"></audio>
                    </div>
                `;
            }

            return '';
        }

        function messageStatusMarkup(message) {
            if (!message.is_me) return '';

            if (message.is_pending) {
                return '<i class="fa-solid fa-spinner fa-spin"></i>';
            }

            return '<i class="fa-solid fa-check-double"></i>';
        }

        function messageActionsMarkup(message) {
            if (!message.is_me || message.is_pending) return '';

            return `
                <div class="chat-message-actions">
                    ${message.attachment_url ? '' : `<button type="button" class="chat-action-button" data-edit-message="${message.id}">Edit</button>`}
                    <button type="button" class="chat-action-button" data-delete-message="${message.id}">Hapus</button>
                </div>
            `;
        }

        function renderMessage(message) {
            message = normalizeMessage(message);
            if (renderedMessages.has(message.id)) return;

            const shouldScroll = isNearBottom();
            if (message.day !== lastRenderedDay) {
                chatThread.insertAdjacentHTML('beforeend', `<div class="chat-day"><span>${escapeHtml(message.day)}</span></div>`);
                lastRenderedDay = message.day;
            }

            chatThread.insertAdjacentHTML('beforeend', `
                <div class="chat-row ${message.is_me ? 'me' : ''} ${message.is_pending ? 'pending' : ''}" data-message-id="${message.id}">
                    ${avatarMarkup(message)}
                    <div class="chat-bubble">
                        <div class="chat-name">${message.is_me ? 'Kamu' : escapeHtml(message.name)}</div>
                        ${attachmentMarkup(message)}
                        ${message.body ? `<div class="chat-body">${escapeHtml(message.body)}</div>` : ''}
                        <div class="chat-meta">
                            <span>${escapeHtml(message.time)}</span>
                            ${message.is_edited ? '<span>diedit</span>' : ''}
                            ${messageStatusMarkup(message)}
                        </div>
                        ${messageActionsMarkup(message)}
                    </div>
                </div>
            `);

            renderedMessages.set(message.id, message);
            const numericMessageId = Number(message.id);
            if (Number.isFinite(numericMessageId)) {
                lastMessageId = Math.max(lastMessageId, numericMessageId);
            }

            if (shouldScroll || message.is_me) scrollToBottom();

            if (!message.is_me && (document.hidden || !document.hasFocus())) {
                showIncomingNotification(message);
            }
        }

        function removeMessage(id) {
            const row = chatThread.querySelector(`[data-message-id="${CSS.escape(String(id))}"]`);
            if (row) row.remove();
            renderedMessages.delete(id);
        }

        function replaceRenderedMessage(oldId, message) {
            removeMessage(oldId);
            renderedMessages.delete(message.id);
            renderMessage(message);
        }

        function chatMessageUrl(template, id) {
            return template.replace('__ID__', encodeURIComponent(id));
        }

        async function readChatError(response, fallbackMessage) {
            try {
                const errorData = await response.json();
                return errorData.message || Object.values(errorData.errors || {}).flat()[0] || fallbackMessage;
            } catch (error) {
                return fallbackMessage;
            }
        }

        async function editMessage(id) {
            const message = renderedMessages.get(Number(id)) || renderedMessages.get(String(id));
            if (!message) return;

            const result = await Swal.fire({
                title: 'Edit pesan',
                input: 'textarea',
                inputValue: message.body || '',
                inputAttributes: { maxlength: 1000 },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#db2777',
                background: '#fff',
                color: '#1a1a2e',
                inputValidator: value => !value.trim() ? 'Pesannya tidak boleh kosong.' : undefined,
            });

            if (!result.isConfirmed) return;

            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('body', result.value.trim());

                const response = await fetch(chatMessageUrl(chatUpdateUrlTemplate, id), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': chatCsrfToken,
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error(await readChatError(response, 'Pesan belum bisa diedit.'));

                const data = await response.json();
                replaceRenderedMessage(id, data.message);
                publishFirebaseMessage(data.message, 'updated');
                Toast.fire({ icon: 'success', title: 'Pesan diperbarui.' });
            } catch (error) {
                Toast.fire({ icon: 'error', title: error.message });
            }
        }

        async function deleteMessage(id) {
            const result = await Swal.fire({
                title: 'Hapus pesan?',
                text: 'Pesan ini akan hilang dari chat.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                background: '#fff',
                color: '#1a1a2e',
            });

            if (!result.isConfirmed) return;

            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');

                const response = await fetch(chatMessageUrl(chatDeleteUrlTemplate, id), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': chatCsrfToken,
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error(await readChatError(response, 'Pesan belum bisa dihapus.'));

                removeMessage(id);
                await publishFirebaseMessage({ id, user_id: chatCurrentUserId, name: 'Kamu' }, 'deleted');
                Toast.fire({ icon: 'success', title: 'Pesan dihapus.' });
            } catch (error) {
                Toast.fire({ icon: 'error', title: error.message });
            }
        }

        function renderMessages(messages) {
            messages.forEach(renderMessage);
            if (renderedMessages.size === messages.length) scrollToBottom();
        }

        function replaceMessages(messages) {
            chatThread.innerHTML = '';
            renderedMessages.clear();
            lastMessageId = 0;
            lastRenderedDay = null;
            messages.forEach(renderMessage);
            scrollToBottom();
        }

        function applyChatTheme(theme = {}) {
            if (!chatShell) return;

            chatShell.style.setProperty('--chat-bubble-color', theme.color || '#db2777');
            chatShell.style.setProperty('--chat-bubble-color-dark', theme.colorDark || '#be185d');

            const bg = theme.background || 'grid';
            if (bg === 'soft') {
                chatShell.style.setProperty('--chat-bg-base', '#fff7fb');
                chatShell.style.setProperty('--chat-bg-pattern-a', 'rgba(255,255,255,0)');
                chatShell.style.setProperty('--chat-bg-pattern-b', 'rgba(255,255,255,0)');
                chatThread.style.backgroundImage = 'linear-gradient(135deg,#fff7fb,#ecfeff)';
                chatThread.style.backgroundSize = 'auto';
            } else if (bg === 'mint') {
                chatShell.style.setProperty('--chat-bg-base', '#f0fdfa');
                chatShell.style.setProperty('--chat-bg-pattern-a', 'rgba(20,184,166,.18)');
                chatShell.style.setProperty('--chat-bg-pattern-b', 'rgba(20,184,166,.08)');
                chatThread.style.backgroundImage = 'radial-gradient(circle at 8px 8px, rgba(20,184,166,.18) 2px, transparent 3px)';
                chatThread.style.backgroundSize = '28px 28px';
            } else if (bg === 'image' && theme.backgroundImage) {
                chatShell.style.setProperty('--chat-bg-base', '#fbfdff');
                chatThread.style.backgroundImage = `linear-gradient(rgba(255,255,255,.78), rgba(255,255,255,.78)), url("${theme.backgroundImage}")`;
                chatThread.style.backgroundSize = 'cover';
                chatThread.style.backgroundPosition = 'center';
            } else {
                chatShell.style.setProperty('--chat-bg-base', '#fbfdff');
                chatShell.style.setProperty('--chat-bg-pattern-a', 'rgba(251, 207, 232, .22)');
                chatShell.style.setProperty('--chat-bg-pattern-b', 'rgba(6, 182, 212, .13)');
                chatThread.style.backgroundImage = '';
                chatThread.style.backgroundSize = '';
                chatThread.style.backgroundPosition = '';
            }
        }

        function saveChatTheme(theme) {
            localStorage.setItem('dompetkita.chat.theme', JSON.stringify(theme));
            applyChatTheme(theme);
        }

        function loadChatTheme() {
            try {
                return JSON.parse(localStorage.getItem('dompetkita.chat.theme') || '{}');
            } catch (error) {
                return {};
            }
        }

        async function fetchMessages() {
            if (polling) return;
            polling = true;

            try {
                const pollUrl = `${chatMessagesUrl}?after_id=${lastMessageId}&_=${Date.now()}`;
                const response = await fetch(pollUrl, {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache',
                    },
                });

                if (!response.ok) throw new Error('Gagal memuat pesan.');

                const data = await response.json();
                renderMessages(data.messages || []);
                chatStatus.textContent = 'Realtime ringan aktif';
            } catch (error) {
                chatStatus.textContent = 'Mencoba menyambungkan lagi...';
            } finally {
                polling = false;
            }
        }

        async function syncRecentMessages() {
            try {
                const response = await fetch(`${chatMessagesUrl}?_=${Date.now()}`, {
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache',
                    },
                });

                if (!response.ok) throw new Error('Gagal sinkron pesan.');

                const data = await response.json();
                replaceMessages(data.messages || []);
            } catch (error) {
                chatStatus.textContent = 'Mencoba menyambungkan lagi...';
            }
        }

        function canNotify() {
            return 'Notification' in window && Notification.permission === 'granted';
        }

        async function showIncomingNotification(message) {
            if (!canNotify()) return;

            const preview = message.attachment_type === 'image'
                ? 'Mengirim foto'
                : message.attachment_type === 'audio'
                    ? 'Mengirim voice note'
                    : message.body;

            const options = {
                body: preview || 'Pesan baru',
                icon: '/images/pwa-icon-dompetkita-192.png',
                badge: '/images/pwa-icon-dompetkita-192.png',
                tag: `chat-${message.id}`,
                renotify: true,
            };

            try {
                if ('serviceWorker' in navigator) {
                    const registration = await navigator.serviceWorker.ready;
                    await registration.showNotification(`Pesan dari ${message.name}`, options);
                    return;
                }
            } catch (error) {
                // Fallback to the page notification below.
            }

            new Notification(`Pesan dari ${message.name}`, options);
        }

        async function requestChatNotifications() {
            if (!('Notification' in window)) {
                Toast.fire({ icon: 'error', title: 'Browser belum mendukung notifikasi.' });
                return;
            }

            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                Toast.fire({ icon: 'success', title: 'Notifikasi chat aktif.' });
                chatNotifyButton.classList.add('text-pink-600');
            } else {
                Toast.fire({ icon: 'info', title: 'Notifikasi belum diizinkan.' });
            }
        }

        async function initFirebaseRealtime() {
            chatDebug('Firebase config check', {
                ready: chatFirebaseReady,
                projectId: chatFirebaseConfig.projectId || null,
                authDomain: chatFirebaseConfig.authDomain || null,
                appId: chatFirebaseConfig.appId ? 'set' : null,
                path: `couples/${chatCoupleId}/chatMessages`,
            });

            if (!chatFirebaseReady) {
                setChatStatus('Firebase config belum lengkap, fallback aktif');
                pollingTimer = window.setInterval(fetchMessages, 2000);
                return;
            }

            try {
                setChatStatus('Menghubungkan Firebase...');
                const firebaseApp = await import('https://www.gstatic.com/firebasejs/10.14.1/firebase-app.js');
                const firestore = await import('https://www.gstatic.com/firebasejs/10.14.1/firebase-firestore.js');
                const app = firebaseApp.initializeApp(chatFirebaseConfig);
                firestoreDb = firestore.getFirestore(app);
                firestoreAddDoc = firestore.addDoc;
                firestoreCollection = firestore.collection;

                const messagesRef = firestore.collection(firestoreDb, 'couples', chatCoupleId, 'chatMessages');
                const messagesQuery = firestore.query(messagesRef, firestore.orderBy('id', 'asc'));
                chatDebug('Firestore listener attach', { path: `couples/${chatCoupleId}/chatMessages` });

                firestore.onSnapshot(messagesQuery, snapshot => {
                    chatDebug('Firestore snapshot', {
                        size: snapshot.size,
                        changes: snapshot.docChanges().length,
                        fromCache: snapshot.metadata.fromCache,
                    });
                    snapshot.docChanges().forEach(change => {
                        if (change.type !== 'added') return;
                        const message = change.doc.data();
                        if (!message?.id) return;
                        chatDebug('Pesan masuk dari Firestore', message);
                        if (['updated', 'deleted'].includes(message.action)) {
                            syncRecentMessages();
                            return;
                        }
                        fetchMessages();
                    });
                }, error => {
                    setChatStatus('Firebase listener gagal, fallback aktif', error.message);
                    if (!pollingTimer) pollingTimer = window.setInterval(fetchMessages, 2000);
                });

                firebaseEnabled = true;
                setChatStatus('Realtime Firebase aktif');
                pollingTimer = window.setInterval(fetchMessages, 2000);
            } catch (error) {
                setChatStatus('Firebase gagal init, fallback aktif', error.message);
                if (!pollingTimer) pollingTimer = window.setInterval(fetchMessages, 2000);
            }
        }

        async function publishFirebaseMessage(message, action = 'created') {
            if (!firebaseEnabled || !firestoreAddDoc || !firestoreCollection || !firestoreDb) return;

            try {
                chatDebug('Firestore publish mulai', {
                    path: `couples/${chatCoupleId}/chatMessages`,
                    messageId: message.id,
                });
                await firestoreAddDoc(
                    firestoreCollection(firestoreDb, 'couples', chatCoupleId, 'chatMessages'),
                    {
                        id: Number(message.id),
                        user_id: Number(message.user_id),
                        name: message.name,
                        type: 'message-created',
                        action,
                        signalOnly: true,
                        createdAt: Date.now(),
                    }
                );
                chatDebug('Pesan dipublish ke Firestore', message);
            } catch (error) {
                setChatStatus('Firebase publish gagal, fallback tetap aktif', error);
            }
        }

        async function sendMessage(body) {
            chatSendButton.disabled = true;
            chatSendButton.classList.add('opacity-70');

            const tempId = `temp-${Date.now()}`;
            const pendingMessage = {
                id: tempId,
                user_id: chatCurrentUserId,
                name: 'Kamu',
                avatar: '',
                photo: null,
                body,
                attachment_type: pendingAttachment?.type || null,
                attachment_url: pendingAttachment?.type === 'image' ? URL.createObjectURL(pendingAttachment.file) : null,
                attachment_mime: pendingAttachment?.file?.type || null,
                attachment_size: pendingAttachment?.file?.size || null,
                audio_duration: pendingAttachment?.duration || null,
                is_me: true,
                is_pending: true,
                is_edited: false,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                day: new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date()),
            };

            renderMessage(pendingMessage);

            try {
                const formData = new FormData();
                formData.append('body', body);
                if (pendingAttachment) {
                    formData.append('attachment', pendingAttachment.file, pendingAttachment.name);
                    formData.append('attachment_type', pendingAttachment.type);
                    if (pendingAttachment.duration) {
                        formData.append('audio_duration', pendingAttachment.duration);
                    }
                }

                const response = await fetch(chatStoreUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': chatCsrfToken,
                    },
                    body: formData,
                });

                if (!response.ok) {
                    let errorMessage = 'Pesan belum terkirim.';
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || Object.values(errorData.errors || {}).flat()[0] || errorMessage;
                    } catch (parseError) {
                        // Keep default message.
                    }
                    throw new Error(errorMessage);
                }

                const data = await response.json();
                replaceRenderedMessage(tempId, data.message);
                publishFirebaseMessage(data.message, 'created');
                chatInput.value = '';
                chatInput.style.height = '46px';
                clearAttachment();
            } catch (error) {
                removeMessage(tempId);
                Toast.fire({ icon: 'error', title: error.message });
            } finally {
                if (pendingMessage.attachment_url?.startsWith('blob:')) {
                    URL.revokeObjectURL(pendingMessage.attachment_url);
                }
                chatSendButton.disabled = false;
                chatSendButton.classList.remove('opacity-70');
                chatInput.focus();
            }
        }

        chatForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const body = chatInput.value.trim();
            if (!body && !pendingAttachment) return;
            sendMessage(body);
        });

        chatThread.addEventListener('click', function (event) {
            const editButton = event.target.closest('[data-edit-message]');
            if (editButton) {
                editMessage(editButton.dataset.editMessage);
                return;
            }

            const deleteButton = event.target.closest('[data-delete-message]');
            if (deleteButton) {
                deleteMessage(deleteButton.dataset.deleteMessage);
            }
        });

        chatInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                chatForm.requestSubmit();
            }
        });

        chatInput.addEventListener('input', function () {
            this.style.height = '46px';
            this.style.height = Math.min(this.scrollHeight, 128) + 'px';
        });

        function setAttachment(file, type, duration = null) {
            pendingAttachment = {
                file,
                type,
                duration,
                name: file.name || (type === 'audio' ? `voice-note-${Date.now()}.webm` : `photo-${Date.now()}`),
            };

            chatAttachmentText.textContent = type === 'audio'
                ? `Voice note siap dikirim${duration ? ` (${duration}s)` : ''}`
                : `Foto siap dikirim: ${file.name}`;

            if (type === 'image') {
                chatPreviewImage.src = URL.createObjectURL(file);
                chatPreviewImage.classList.add('active');
            } else {
                chatPreviewImage.removeAttribute('src');
                chatPreviewImage.classList.remove('active');
            }

            chatAttachmentPreview.classList.add('active');
        }

        function clearAttachment() {
            if (chatPreviewImage.src) {
                URL.revokeObjectURL(chatPreviewImage.src);
            }
            pendingAttachment = null;
            chatImageInput.value = '';
            chatAttachmentPreview.classList.remove('active');
            chatAttachmentText.textContent = '';
            chatPreviewImage.removeAttribute('src');
            chatPreviewImage.classList.remove('active');
        }

        function recordingSeconds() {
            return Math.max(1, Math.round((Date.now() - recordingStartedAt) / 1000));
        }

        function preferredAudioMimeType() {
            const candidates = [
                'audio/webm;codecs=opus',
                'audio/webm',
                'audio/mp4',
                'audio/aac',
                'audio/ogg;codecs=opus',
            ];

            if (!window.MediaRecorder?.isTypeSupported) return '';
            return candidates.find(type => MediaRecorder.isTypeSupported(type)) || '';
        }

        function audioExtensionFromMime(mimeType) {
            if (mimeType.includes('mp4')) return 'm4a';
            if (mimeType.includes('aac')) return 'aac';
            if (mimeType.includes('ogg')) return 'ogg';
            return 'webm';
        }

        async function startRecording() {
            if (!window.isSecureContext) {
                Toast.fire({ icon: 'error', title: 'Voice note di production wajib pakai HTTPS.' });
                return;
            }

            if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
                Toast.fire({ icon: 'error', title: 'Browser belum mendukung voice note.' });
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const mimeType = preferredAudioMimeType();
                audioChunks = [];
                mediaRecorder = new MediaRecorder(stream, mimeType ? { mimeType } : undefined);
                recordingStartedAt = Date.now();

                mediaRecorder.addEventListener('dataavailable', event => {
                    if (event.data.size > 0) audioChunks.push(event.data);
                });

                mediaRecorder.addEventListener('stop', () => {
                    stream.getTracks().forEach(track => track.stop());
                    const recordedMimeType = mediaRecorder.mimeType || mimeType || 'audio/webm';
                    const duration = recordingSeconds();
                    const blob = new Blob(audioChunks, { type: recordedMimeType });

                    if (!blob.size) {
                        Toast.fire({ icon: 'error', title: 'Voice note kosong, coba rekam lagi.' });
                    } else {
                        const extension = audioExtensionFromMime(recordedMimeType);
                        const file = new File([blob], `voice-note-${Date.now()}.${extension}`, { type: recordedMimeType });
                        setAttachment(file, 'audio', duration);
                    }

                    mediaRecorder = null;
                    chatVoiceButton.classList.remove('recording');
                    chatVoiceButton.innerHTML = '<i class="fa-solid fa-microphone"></i>';
                    chatRecordingChip.classList.remove('active');
                    window.clearInterval(recordingTimer);
                });

                mediaRecorder.start();
                chatVoiceButton.classList.add('recording');
                chatVoiceButton.innerHTML = '<i class="fa-solid fa-stop"></i>';
                chatRecordingChip.classList.add('active');
                chatRecordingText.textContent = 'Merekam suara... 0s';
                recordingTimer = window.setInterval(() => {
                    chatRecordingText.textContent = `Merekam suara... ${recordingSeconds()}s`;
                }, 500);
            } catch (error) {
                Toast.fire({ icon: 'error', title: 'Izin mikrofon belum diberikan.' });
            }
        }

        function stopRecording() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
        }

        chatImageButton.addEventListener('click', () => chatImageInput.click());
        chatImageInput.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) return;
            setAttachment(file, 'image');
        });
        chatClearAttachment.addEventListener('click', clearAttachment);
        chatVoiceButton.addEventListener('click', function () {
            if (mediaRecorder) {
                stopRecording();
                return;
            }
            startRecording();
        });
        chatNotifyButton.addEventListener('click', requestChatNotifications);
        chatThemeButton.addEventListener('click', function () {
            chatThemePanel.classList.toggle('active');
        });
        document.addEventListener('click', function (event) {
            if (!chatThemePanel.contains(event.target) && !chatThemeButton.contains(event.target)) {
                chatThemePanel.classList.remove('active');
            }
        });
        document.querySelectorAll('[data-chat-color]').forEach(button => {
            button.addEventListener('click', function () {
                const current = loadChatTheme();
                saveChatTheme({
                    ...current,
                    color: this.dataset.chatColor,
                    colorDark: this.dataset.chatColorDark,
                });
            });
        });
        document.querySelectorAll('[data-chat-bg]').forEach(button => {
            button.addEventListener('click', function () {
                const current = loadChatTheme();
                saveChatTheme({
                    ...current,
                    background: this.dataset.chatBg,
                });
            });
        });
        chatBgImageButton.addEventListener('click', () => chatBgImageInput.click());
        chatBgImageInput.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function () {
                try {
                    const current = loadChatTheme();
                    saveChatTheme({
                        ...current,
                        background: 'image',
                        backgroundImage: reader.result,
                    });
                } catch (error) {
                    Toast.fire({ icon: 'error', title: 'Gambar terlalu besar untuk disimpan.' });
                }
            };
            reader.readAsDataURL(file);
        });

        document.addEventListener('DOMContentLoaded', function () {
            applyChatTheme(loadChatTheme());
            renderMessages(chatInitialMessages);
            initFirebaseRealtime();
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) fetchMessages();
            });
        });
    </script>
@endpush
