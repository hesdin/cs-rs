<script setup lang="ts">
import {
    AlertTriangle,
    Clock,
    HeartPulse,
    Loader2,
    MessageCircle,
    Phone,
    Send,
    Sparkles,
    X,
} from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { chat as apiChat } from '@/routes/api';

interface ChatMessage {
    id: number;
    role: 'user' | 'assistant';
    content: string;
    intent?: string;
    handoff?: boolean;
    used_llm?: boolean;
    error?: boolean;
    timestamp: string;
}

interface ChatResponse {
    data: {
        session_id: string;
        reply: string;
        intent: string;
        handoff: boolean;
        used_llm: boolean;
        model: string | null;
        conversation_id: number;
    };
}

interface Suggestion {
    label: string;
}

interface CategoryItem {
    name: string;
    description: string | null;
}

interface StoredConversation {
    sessionId: string | null;
    messages: ChatMessage[];
    messageCounter: number;
}

const props = defineProps<{
    hospitalName: string;
    welcomeMessage: string;
    categories: CategoryItem[];
    suggestions: Suggestion[];
    contact: {
        phone: string;
        igd: string;
        whatsapp: string;
    };
}>();

const emit = defineEmits<{
    close: [];
}>();

const STORAGE_KEY = 'cs-rs.chat.session';
const CONVERSATION_KEY = 'cs-rs.chat.conversation';
const LEGACY_WELCOME_MESSAGE =
    'Assalamualaikum, saya asisten virtual RS Ibnu Sina Makassar. Saya bisa bantu informasi jadwal dokter, pendaftaran, BPJS, dan administrasi rumah sakit. Ada yang bisa saya bantu?';
const SHORT_WELCOME_MESSAGE =
    'Assalamualaikum, saya asisten virtual RS Ibnu Sina. Saya bisa bantu jadwal dokter, pendaftaran, BPJS, dan info layanan. Ada yang bisa saya bantu?';

const sessionId = ref<string | null>(null);
const messages = ref<ChatMessage[]>([]);
const input = ref('');
const sending = ref(false);
const scrollContainer = ref<HTMLDivElement | null>(null);
const inputRef = ref<HTMLTextAreaElement | null>(null);
let messageCounter = 0;

const formatTime = (): string =>
    new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });

const pushMessage = (message: Omit<ChatMessage, 'id' | 'timestamp'>): void => {
    messages.value.push({
        ...message,
        id: ++messageCounter,
        timestamp: formatTime(),
    });
};

const initialMessageCount = computed(() => messages.value.length);
const visibleMessages = computed(() =>
    messages.value.length > 1
        ? messages.value.filter((message, index) => index !== 0)
        : messages.value,
);
const cleanWa = computed(() => props.contact.whatsapp.replace(/[^0-9]/g, ''));
const cleanPhone = computed(() => props.contact.phone.replace(/[^0-9+]/g, ''));
const quickSuggestions = computed(() => props.suggestions.slice(0, 3));
const normalizedWelcomeMessage = computed(() =>
    props.welcomeMessage.trim() === LEGACY_WELCOME_MESSAGE
        ? SHORT_WELCOME_MESSAGE
        : props.welcomeMessage,
);

const saveConversation = (): void => {
    try {
        const payload: StoredConversation = {
            sessionId: sessionId.value,
            messages: messages.value,
            messageCounter,
        };

        localStorage.setItem(CONVERSATION_KEY, JSON.stringify(payload));
    } catch {
        // Ignore storage errors.
    }
};

const showWelcome = (): void => {
    messages.value = [];
    messageCounter = 0;
    pushMessage({ role: 'assistant', content: normalizedWelcomeMessage.value });
};

const restoreConversation = (): boolean => {
    try {
        const raw = localStorage.getItem(CONVERSATION_KEY);

        if (!raw) {
            return false;
        }

        const stored = JSON.parse(raw) as Partial<StoredConversation>;

        if (!Array.isArray(stored.messages) || stored.messages.length === 0) {
            return false;
        }

        messages.value = stored.messages;
        const storedCounter =
            typeof stored.messageCounter === 'number'
                ? stored.messageCounter
                : 0;

        messageCounter = Math.max(
            storedCounter,
            stored.messages.reduce(
                (max, message) => Math.max(max, message.id),
                0,
            ),
        );
        sessionId.value =
            typeof stored.sessionId === 'string' ? stored.sessionId : null;

        return true;
    } catch {
        return false;
    }
};

const closePanel = (): void => {
    saveConversation();
    emit('close');
};

const playBeep = (): void => {
    try {
        const ctx = new (
            window.AudioContext ||
            (window as unknown as { webkitAudioContext: typeof AudioContext })
                .webkitAudioContext
        )();
        const oscillator = ctx.createOscillator();
        const gain = ctx.createGain();

        oscillator.connect(gain);
        gain.connect(ctx.destination);
        oscillator.frequency.value = 880;

        gain.gain.setValueAtTime(0.0001, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.05, ctx.currentTime + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.18);

        oscillator.start();
        oscillator.stop(ctx.currentTime + 0.2);
    } catch {
        // Ignore audio errors in browsers that block autoplay/audio context.
    }
};

const scrollToBottom = async (smooth = true): Promise<void> => {
    await nextTick();

    if (!scrollContainer.value) {
        return;
    }

    scrollContainer.value.scrollTo({
        top: scrollContainer.value.scrollHeight,
        behavior: smooth ? 'smooth' : 'auto',
    });
};

const csrfToken = (): string =>
    document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
        ?.content ?? '';

const autoResize = (): void => {
    const element = inputRef.value;

    if (!element) {
        return;
    }

    element.style.height = 'auto';
    element.style.height = `${Math.min(element.scrollHeight, 140)}px`;
};

const focusComposer = async (): Promise<void> => {
    await nextTick();

    window.setTimeout(() => {
        inputRef.value?.focus();
        autoResize();
    }, 180);
};

const onInputKeydown = (event: KeyboardEvent): void => {
    const isMobile = window.matchMedia('(pointer: coarse)').matches;

    if (event.key === 'Enter' && !event.shiftKey && !isMobile) {
        event.preventDefault();
        void send();
    }
};

const send = async (text?: string): Promise<void> => {
    const content = (text ?? input.value).trim();

    if (!content || sending.value) {
        return;
    }

    pushMessage({ role: 'user', content });
    input.value = '';
    autoResize();
    sending.value = true;
    await scrollToBottom();

    try {
        const response = await fetch(apiChat.url(), {
            method: apiChat.definition.methods[0].toUpperCase(),
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({
                session_id: sessionId.value,
                message: content,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const json: ChatResponse = await response.json();

        sessionId.value = json.data.session_id;

        try {
            localStorage.setItem(STORAGE_KEY, json.data.session_id);
        } catch {
            // Ignore storage errors.
        }

        saveConversation();

        pushMessage({
            role: 'assistant',
            content: json.data.reply,
            intent: json.data.intent,
            handoff: json.data.handoff,
            used_llm: json.data.used_llm,
        });

        playBeep();
    } catch {
        pushMessage({
            role: 'assistant',
            content:
                'Maaf, terjadi gangguan koneksi. Silakan coba lagi atau hubungi customer service kami.',
            error: true,
        });
    } finally {
        sending.value = false;
        await scrollToBottom();
    }
};

watch(
    [messages, sessionId],
    () => {
        saveConversation();
    },
    { deep: true },
);
watch(input, autoResize);

onMounted(() => {
    try {
        sessionId.value = localStorage.getItem(STORAGE_KEY);
    } catch {
        // Ignore storage errors.
    }

    if (!restoreConversation()) {
        showWelcome();
    }

    void scrollToBottom(false);
    void focusComposer();
});
</script>

<template>
    <div class="flex h-full min-h-0 flex-col bg-white text-slate-900">
        <header class="border-b border-slate-200 bg-white px-4 py-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <HeartPulse class="size-4 shrink-0 text-emerald-700" />
                    <p class="truncate text-sm font-semibold tracking-tight">
                        Tanya Asisten
                    </p>
                </div>

                <button
                    type="button"
                    aria-label="Tutup panel"
                    class="flex size-8 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    @click="closePanel"
                >
                    <X class="size-4.5" />
                </button>
            </div>
        </header>

        <div
            ref="scrollContainer"
            class="min-h-0 flex-1 overflow-y-auto bg-white px-5 pt-4 pb-2"
        >
            <div class="flex flex-col gap-5">
                <div v-if="initialMessageCount <= 1 && !sending" class="px-1">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-600"
                    >
                        <Sparkles class="size-3" />
                        Saran Pertanyaan
                    </span>
                    <div class="mt-2 flex flex-col gap-0.5">
                        <button
                            v-for="(suggestion, index) in quickSuggestions"
                            :key="index"
                            type="button"
                            class="rounded-md px-2 py-1.5 text-left text-[13px] text-slate-600 transition hover:bg-white hover:text-slate-900"
                            @click="void send(suggestion.label)"
                        >
                            <span class="leading-5">{{
                                suggestion.label
                            }}</span>
                        </button>
                    </div>
                </div>

                <TransitionGroup
                    tag="div"
                    class="flex flex-col gap-3.5"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-active-class="transition duration-300 ease-out"
                    move-class="transition duration-300 ease-out"
                >
                    <div
                        v-for="message in visibleMessages"
                        :key="message.id"
                        class="flex"
                        :class="
                            message.role === 'user'
                                ? 'justify-end'
                                : 'justify-start'
                        "
                    >
                        <div class="max-w-[84%] min-w-0">
                            <div
                                v-if="message.role === 'assistant'"
                                class="mb-1.5 flex items-center gap-1.5 text-emerald-700"
                            >
                                <HeartPulse class="size-3.5" />
                                <span
                                    class="text-[11px] font-medium text-slate-500"
                                >
                                    AI Asisten
                                </span>
                            </div>

                            <div
                                class="text-[13px] leading-5.5 whitespace-pre-wrap"
                                :class="
                                    message.role === 'user'
                                        ? 'rounded-xl rounded-br-sm bg-[#f2f2f2] px-3.5 py-2.5 text-slate-900'
                                        : message.error
                                          ? 'rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-900'
                                          : 'px-0 py-0 text-slate-800'
                                "
                            >
                                {{ message.content }}
                            </div>

                            <div
                                v-if="message.handoff"
                                class="mt-1.5 flex flex-col gap-2 rounded-lg border border-amber-200 bg-amber-50 p-2.5 text-[11px] text-amber-900"
                            >
                                <div
                                    class="flex items-center gap-2 font-semibold"
                                >
                                    <AlertTriangle class="size-4" />
                                    Diteruskan ke Petugas
                                </div>
                                <p class="leading-relaxed">
                                    Hubungi langsung petugas kami:
                                </p>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <a
                                        :href="`tel:${cleanPhone}`"
                                        class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 font-medium ring-1 ring-amber-200"
                                    >
                                        <Phone class="size-3" />
                                        CS
                                    </a>
                                    <a
                                        :href="`https://wa.me/${cleanWa}`"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 font-medium text-emerald-800 ring-1 ring-emerald-200"
                                    >
                                        <MessageCircle class="size-3" />
                                        WhatsApp
                                    </a>
                                </div>
                            </div>

                            <div
                                class="mt-1 flex items-center gap-1 px-1 text-[9px] text-slate-400"
                                :class="
                                    message.role === 'user'
                                        ? 'justify-end'
                                        : 'justify-start'
                                "
                            >
                                <Clock class="size-3" />
                                <span>{{ message.timestamp }}</span>
                                <span
                                    v-if="
                                        message.role === 'assistant' &&
                                        message.used_llm === false &&
                                        !message.error &&
                                        !message.handoff
                                    "
                                    class="rounded-full bg-slate-100 px-1.5 py-0.5 text-[8px] font-medium text-slate-500"
                                >
                                    data RS
                                </span>
                                <span
                                    v-if="
                                        message.role === 'assistant' &&
                                        message.used_llm === true &&
                                        !message.error
                                    "
                                    class="rounded-full bg-emerald-50 px-1.5 py-0.5 text-[8px] font-medium text-emerald-700"
                                >
                                    AI
                                </span>
                            </div>
                        </div>
                    </div>
                </TransitionGroup>

                <div v-if="sending" class="flex gap-2">
                    <div class="min-w-0">
                        <div
                            class="mb-1.5 flex items-center gap-1.5 text-emerald-700"
                        >
                            <HeartPulse class="size-3.5" />
                            <span
                                class="text-[11px] font-medium text-slate-500"
                            >
                                AI Asisten
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 px-0 py-0">
                            <span
                                class="size-2 animate-bounce rounded-full bg-emerald-500 [animation-delay:-0.3s]"
                            />
                            <span
                                class="size-2 animate-bounce rounded-full bg-emerald-500 [animation-delay:-0.15s]"
                            />
                            <span
                                class="size-2 animate-bounce rounded-full bg-emerald-500"
                            />
                            <span class="ml-1 text-[11px] text-slate-500"
                                >Menganalisis permintaan Anda...</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside
            class="mx-5 mt-1.5 mb-3 rounded-2xl border-2 border-black bg-white"
        >
            <form
                class="flex items-end gap-3 px-2 py-2"
                @submit.prevent="void send()"
            >
                <div
                    class="flex flex-1 items-end rounded-2xl bg-white px-3 py-2"
                >
                    <textarea
                        ref="inputRef"
                        v-model="input"
                        rows="1"
                        placeholder="Tulis pertanyaan Anda..."
                        :disabled="sending"
                        class="max-h-36 min-h-14 flex-1 resize-none border-0 bg-transparent py-1 text-[13px] leading-5.5 placeholder:text-slate-400 focus:outline-none disabled:opacity-60"
                        inputmode="text"
                        enterkeyhint="send"
                        @keydown="onInputKeydown"
                    />
                </div>
                <button
                    type="submit"
                    :disabled="sending || !input.trim()"
                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#339966] text-white transition hover:bg-[#2b8055] disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label="Kirim pesan"
                >
                    <Loader2 v-if="sending" class="size-4 animate-spin" />
                    <Send v-else class="size-4" />
                </button>
            </form>
        </aside>
    </div>
</template>
