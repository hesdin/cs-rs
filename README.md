# Hospital Customer Service Chatbot

Chatbot customer service rumah sakit berbasis **Laravel 13 + Vue 3 (Inertia) + PostgreSQL + OpenRouter**.

Bot hanya menjawab informasi umum, jadwal dokter, dan administrasi. Pertanyaan medis (gejala, diagnosis, obat, hasil pemeriksaan) dan kondisi darurat **tidak pernah** diteruskan ke LLM — sistem menangani lewat rule-based filter dengan respons aman + handoff ke petugas/IGD.

## Arsitektur Pipeline

```
POST /api/chat
   │
   ▼
ChatService.handle
   ├─ DetectSensitiveQuestion (rule-based, BUKAN LLM)
   │     ├─ EMERGENCY        → balas template IGD/119, handoff=true
   │     └─ MEDICAL_ADVICE   → disclaimer + saran konsultasi, handoff=true
   │
   ├─ RetrieveKnowledge      (FAQ + Doctors dari Postgres, RAG sederhana)
   ├─ BuildPrompt            (system prompt + context + history)
   ├─ OpenRouterService      (Http::post chat/completions)
   └─ ChatMessage::create    (audit log lengkap)
```

## Stack & Versi

- PHP 8.4 · Laravel 13 · Pest 4
- Vue 3 + Inertia v3 + TailwindCSS v4 + shadcn-vue
- PostgreSQL · OpenRouter (model configurable via env)
- Wayfinder (typed routes), Laravel Boost MCP

## Setup

```bash
# 1. Dependensi
composer install
npm install

# 2. App key + DB
cp .env.example .env
php artisan key:generate

# 3. Isi DB Postgres + API key OpenRouter di .env
#    OPENROUTER_API_KEY=sk-or-v1-...
#    OPENROUTER_MODEL=openai/gpt-4o-mini   # atau anthropic/claude-3.5-sonnet, dll.

# 4. Migrate + seed
php artisan migrate --seed

# 5. Build assets
npm run build           # production
# atau
composer run dev        # serve + queue + vite dev
```

Login admin default: `admin@cs-rs.test` / `password`.

URL:
- `/chat`            — chatbot publik (tanpa login)
- `/admin`           — dashboard chatbot
- `/admin/faqs`      — kelola FAQ
- `/admin/doctors`   — kelola dokter & jadwal
- `/admin/conversations` — log percakapan
- `/admin/chatbot-settings` — disclaimer, fallback, dll.

## Konfigurasi OpenRouter

Semua via `config/openrouter.php` ← `.env`. Tidak ada hardcoded key/model.

```env
OPENROUTER_API_KEY=sk-or-v1-...
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
OPENROUTER_MODEL=openai/gpt-4o-mini
OPENROUTER_TEMPERATURE=0.3
OPENROUTER_MAX_TOKENS=800
OPENROUTER_TIMEOUT=30

CHATBOT_HISTORY_WINDOW=6
CHATBOT_MAX_FAQ_CONTEXT=5
CHATBOT_MAX_SCHEDULE_CONTEXT=10
```

Ganti model dengan apa pun yang didukung OpenRouter (GPT-4o, Claude, Gemini, Llama, dll.) tanpa mengubah kode.

## API

### `POST /api/chat`

Rate limit: 30 req/menit per IP.

Request body:

```json
{
  "session_id": "550e8400-e29b-41d4-a716-446655440000",
  "message": "Apakah RS menerima BPJS?",
  "channel": "web"
}
```

`session_id` opsional — kalau kosong server akan generate UUID baru dan kembalikan di response.

Response 200:

```json
{
  "data": {
    "session_id": "550e8400-e29b-41d4-a716-446655440000",
    "reply": "Ya, RS menerima BPJS dengan rujukan faskes 1...",
    "intent": "general",
    "handoff": false,
    "used_llm": true,
    "model": "openai/gpt-4o-mini",
    "conversation_id": 42
  }
}
```

Contoh skenario:

| Pertanyaan | intent | handoff | used_llm | reply |
|---|---|---|---|---|
| `"Saya tidak sadarkan diri, sesak napas"` | `emergency` | `true` | `false` | template IGD + 119 |
| `"Obat apa untuk diare?"` | `medical_advice` | `true` | `false` | disclaimer + arah konsultasi |
| `"Jadwal dr. anak hari Senin"` | `general` | `false` | `true` | jawaban LLM grounded ke DB |
| `"Bagaimana daftar BPJS?"` | `general` | `false` | `true` | jawaban LLM dari FAQ |

## Aturan Bisnis (HARD-CODED, tidak di-bypass LLM)

1. JANGAN PERNAH memberi diagnosis, dosis, nama obat sebagai saran, atau interpretasi hasil pemeriksaan.
2. Pertanyaan darurat (kata kunci: pingsan, sesak napas, kejang, stroke, pendarahan, bunuh diri, kecelakaan, dll.) → respons IGD + 119, handoff.
3. Pertanyaan medis non-darurat (gejala, obat, dosis, hasil lab, kondisi spesifik) → disclaimer + arahkan ke dokter.
4. LLM hanya digunakan untuk **menyusun jawaban natural** dari konteks FAQ + jadwal dokter yang sudah diambil dari database.

## Testing

```bash
php artisan test --compact
# atau filter
php artisan test --compact --filter=ChatbotPipelineTest
```

`tests/Feature/ChatbotPipelineTest.php` mencakup:
- Emergency tanpa memanggil LLM (`Http::preventStrayRequests`)
- Medical advice tanpa memanggil LLM
- Pertanyaan aman → call LLM (di-fake) → reply natural
- LLM gagal → pakai fallback message dari settings
- Endpoint `POST /api/chat` smoke test

## Struktur File Utama

```
app/
├── Actions/Chatbot/
│   ├── DetectSensitiveQuestion.php   # rule-based filter
│   ├── RetrieveKnowledge.php         # RAG sederhana FAQ + Doctor
│   └── BuildPrompt.php               # system prompt builder
├── Enums/ChatbotIntent.php
├── Http/Controllers/
│   ├── Api/ChatController.php
│   └── Admin/{Dashboard,Faq,Doctor,Conversation,ChatbotSetting}Controller.php
├── Http/Requests/{ChatMessage,Faq,Doctor}Request.php
├── Models/{Faq,Doctor,DoctorSchedule,ChatConversation,ChatMessage,ChatbotSetting}.php
└── Services/
    ├── Chatbot/ChatService.php       # orkestrator pipeline
    └── OpenRouter/OpenRouterService.php

config/openrouter.php

database/migrations/2026_05_23_*  (faqs, doctors, doctor_schedules,
                                   chat_conversations, chat_messages, chatbot_settings)
database/seeders/{FaqSeeder, DoctorSeeder, ChatbotSettingSeeder}.php

resources/js/pages/
├── Chat.vue                          # widget chat publik
└── admin/
    ├── Index.vue                     # dashboard stats
    ├── faqs/{Index,Form}.vue
    ├── doctors/{Index,Form}.vue
    ├── conversations/{Index,Show}.vue
    └── settings/Index.vue            # disclaimer / fallback / dll.

routes/{web.php, api.php}
```

## Next Step: Integrasi WhatsApp

Sistem sudah siap WhatsApp. Tinggal tambah adapter:

1. **Pilih provider**: Twilio / Meta WhatsApp Cloud API / WAHA / fonnte / wablas.
2. **Webhook receiver**: buat `routes/api.php` route baru, mis. `POST /api/whatsapp/webhook`. Verifikasi signature provider.
3. **Adapter call**:
   ```php
   $result = app(\App\Services\Chatbot\ChatService::class)->handle([
       'session_id'      => $existingByPhoneOrNull,   // map phone → session_id
       'message'         => $request->input('text'),
       'channel'         => 'whatsapp',
       'user_identifier' => $request->input('from'),  // nomor WA
   ]);
   ```
4. **Reply**: kirim `$result['reply']` lewat HTTP API provider. Kalau `$result['handoff']` true, push notif ke CS dashboard / channel khusus.
5. **Mapping session**: simpan kolom tambahan `chat_conversations.user_identifier` (sudah ada) sebagai phone number, lookup conversation aktif dengan `where('channel', 'whatsapp')->where('user_identifier', $phone)->latest()`.
6. **Queue**: pindahkan call ke job (`SendWhatsappReply`) supaya webhook respond cepat.
7. **Template message**: WhatsApp Business butuh template untuk inisiasi >24h. Simpan di `chatbot_settings`.

Channel `web` dan `whatsapp` sudah didukung oleh validation request (`ChatMessageRequest`) dan kolom `chat_conversations.channel`.
