<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowLeft,
    Bed,
    Calendar,
    CheckCircle2,
    ClipboardList,
    Clock,
    FileText,
    HeartPulse,
    Info,
    Loader2,
    MessageCircle,
    Phone,
    RotateCcw,
    Send,
    ShieldCheck,
    Sparkles,
    Volume2,
    VolumeX,
    Wallet,
    X,
} from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { home } from '@/routes';

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
    icon: string;
    label: string;
}

interface CategoryItem {
    name: string;
    description: string | null;
}

const props = defineProps<{
    welcomeMessage: string;
    hospitalName: string;
    categories: CategoryItem[];
    suggestions: Suggestion[];
    contact: {
        phone: string;
        igd: string;
        whatsapp: string;
    };
}>();

const STORAGE_KEY = 'cs-rs.chat.session';

const sessionId = ref<string | null>(null);
const messages = ref<ChatMessage[]>([]);
const input = ref('');
const sending = ref(false);
const muted = ref(false);
const infoSheetOpen = ref(false);
const scrollContainer = ref<HTMLDivElement | null>(null);
const inputRef = ref<HTMLTextAreaElement | null>(null);
let messageCounter = 0;

const SUGGESTION_ICON: Record<string, typeof Calendar> = {
    calendar: Calendar,
    clipboard: ClipboardList,
    shield: ShieldCheck,
    wallet: Wallet,
    bed: Bed,
    file: FileText,
};

const formatTime = (): string =>
    new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });

const pushMessage = (msg: Omit<ChatMessage, 'id' | 'timestamp'>) => {
    messages.value.push({
        ...msg,
        id: ++messageCounter,
        timestamp: formatTime(),
    });
};

const initialMessageCount = computed(() => messages.value.length);
const hasConversation = computed(() => messages.value.length > 1);
const visibleMessages = computed(() =>
    messages.value.length > 1
        ? messages.value.filter((message, index) => index !== 0)
        : messages.value,
);

const showWelcome = () => {
    messages.value = [];
    messageCounter = 0;
    pushMessage({ role: 'assistant', content: props.welcomeMessage });
};

const playBeep = () => {
    if (muted.value) {
        return;
    }

    try {
        const ctx = new (
            window.AudioContext ||
            (window as unknown as { webkitAudioContext: typeof AudioContext })
                .webkitAudioContext
        )();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.0001, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.05, ctx.currentTime + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.18);
        osc.start();
        osc.stop(ctx.currentTime + 0.2);
    } catch {
        // ignore audio errors
    }
};

const scrollToBottom = async (smooth = true) => {
    await nextTick();

    if (scrollContainer.value) {
        scrollContainer.value.scrollTo({
            top: scrollContainer.value.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto',
        });
    }
};

const csrfToken = (): string =>
    document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
        ?.content ?? '';

const autoResize = () => {
    const el = inputRef.value;

    if (!el) {
        return;
    }

    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 140) + 'px';
};

const onInputKeydown = (e: KeyboardEvent) => {
    // Di mobile (touch), Enter membuat baris baru. Di desktop, Enter mengirim.
    const isMobile = window.matchMedia('(pointer: coarse)').matches;

    if (e.key === 'Enter' && !e.shiftKey && !isMobile) {
        e.preventDefault();
        send();
    }
};

const send = async (text?: string) => {
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
        const res = await fetch('/api/chat', {
            method: 'POST',
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

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const json: ChatResponse = await res.json();
        sessionId.value = json.data.session_id;

        try {
            localStorage.setItem(STORAGE_KEY, json.data.session_id);
        } catch {
            // ignore storage errors
        }

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

const startNewSession = () => {
    if (
        hasConversation.value &&
        !confirm('Mulai percakapan baru? Riwayat saat ini akan dihapus.')
    ) {
        return;
    }

    sessionId.value = null;

    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch {
        // ignore
    }

    showWelcome();
};

const cleanWa = computed(() => props.contact.whatsapp.replace(/[^0-9]/g, ''));
const cleanIgd = computed(() => props.contact.igd.replace(/[^0-9+]/g, ''));
const cleanPhone = computed(() => props.contact.phone.replace(/[^0-9+]/g, ''));

watch(input, autoResize);

onMounted(() => {
    try {
        sessionId.value = localStorage.getItem(STORAGE_KEY);
    } catch {
        // ignore
    }

    showWelcome();
});
</script>

<template>
    <Head :title="`Chat — ${hospitalName}`" />

    <!--
        h-[100dvh] (dynamic viewport) menangani address bar mobile browser
        dengan benar. h-screen / 100vh akan kepotong oleh address bar di iOS
        Safari & Chrome Android.
    -->
    <div
        class="flex h-[100dvh] flex-col bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- ========== HEADER (slim, fixed di top via flex parent) ========== -->
        <header
            class="flex-shrink-0 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95"
            style="padding-top: env(safe-area-inset-top)"
        >
            <div
                class="mx-auto flex w-full max-w-6xl items-center justify-between gap-2 px-3 py-2.5 sm:px-4 sm:py-3"
            >
                <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                    <Link
                        :href="home()"
                        class="flex size-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-500 transition active:bg-slate-100 dark:active:bg-slate-800"
                        aria-label="Kembali ke beranda"
                    >
                        <ArrowLeft class="size-5" />
                    </Link>

                    <div class="relative flex-shrink-0">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-sm"
                        >
                            <HeartPulse class="size-5" stroke-width="2.5" />
                        </div>
                        <span
                            class="absolute -right-0.5 -bottom-0.5 size-3 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-900"
                        />
                    </div>

                    <div class="min-w-0 leading-tight">
                        <p class="truncate text-sm font-semibold">
                            Asisten Virtual
                        </p>
                        <p class="truncate text-[11px] text-slate-500">
                            <span class="text-emerald-600">●</span> Online · RS
                            Ibnu Sina
                        </p>
                    </div>
                </div>

                <div class="flex flex-shrink-0 items-center gap-0.5 sm:gap-1">
                    <button
                        type="button"
                        :aria-label="muted ? 'Aktifkan suara' : 'Matikan suara'"
                        class="flex size-10 items-center justify-center rounded-lg text-slate-500 transition active:bg-slate-100 sm:hover:bg-slate-100 sm:hover:text-slate-900 dark:active:bg-slate-800 dark:sm:hover:bg-slate-800"
                        @click="muted = !muted"
                    >
                        <VolumeX v-if="muted" class="size-5" />
                        <Volume2 v-else class="size-5" />
                    </button>
                    <button
                        type="button"
                        aria-label="Mulai percakapan baru"
                        class="flex size-10 items-center justify-center rounded-lg text-slate-500 transition active:bg-slate-100 sm:hover:bg-slate-100 sm:hover:text-slate-900 dark:active:bg-slate-800 dark:sm:hover:bg-slate-800"
                        @click="startNewSession"
                    >
                        <RotateCcw class="size-5" />
                    </button>
                    <button
                        type="button"
                        aria-label="Info kontak & topik"
                        class="flex size-10 items-center justify-center rounded-lg text-slate-500 transition active:bg-slate-100 sm:hover:bg-slate-100 sm:hover:text-slate-900 lg:hidden dark:active:bg-slate-800 dark:sm:hover:bg-slate-800"
                        @click="infoSheetOpen = true"
                    >
                        <Info class="size-5" />
                    </button>
                </div>
            </div>
        </header>

        <!-- ========== BODY ========== -->
        <div
            class="mx-auto grid min-h-0 w-full max-w-6xl flex-1 grid-cols-1 overflow-hidden lg:grid-cols-[1fr_320px]"
        >
            <!-- ========== CHAT AREA ========== -->
            <div class="flex min-h-0 min-w-0 flex-col">
                <main
                    ref="scrollContainer"
                    class="flex-1 overflow-y-auto overscroll-contain px-3 py-4 sm:px-4 sm:py-5"
                >
                    <div class="mx-auto flex max-w-2xl flex-col gap-4">
                        <!-- Empty state suggestions -->
                        <div
                            v-if="initialMessageCount <= 1 && !sending"
                            class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-4 dark:border-emerald-900/30 dark:from-emerald-950/30 dark:to-slate-900"
                        >
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300"
                            >
                                <Sparkles class="size-3" />
                                Saran Pertanyaan
                            </span>
                            <p
                                class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-400"
                            >
                                Pilih salah satu, atau ketik sendiri di bawah.
                            </p>
                            <div class="mt-3 flex flex-col gap-2">
                                <button
                                    v-for="(s, i) in suggestions"
                                    :key="i"
                                    type="button"
                                    class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 text-left text-sm transition active:scale-[0.98] active:bg-emerald-50 sm:hover:-translate-y-0.5 sm:hover:border-emerald-300 sm:hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:active:bg-emerald-950/30"
                                    @click="send(s.label)"
                                >
                                    <span
                                        class="flex size-9 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 transition sm:group-hover:bg-emerald-600 sm:group-hover:text-white dark:bg-emerald-950/40 dark:text-emerald-400"
                                    >
                                        <component
                                            :is="
                                                SUGGESTION_ICON[s.icon] ||
                                                Sparkles
                                            "
                                            class="size-4"
                                        />
                                    </span>
                                    <span class="flex-1 leading-snug">{{
                                        s.label
                                    }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Messages -->
                        <TransitionGroup
                            tag="div"
                            class="flex flex-col gap-4"
                            enter-from-class="opacity-0 translate-y-2"
                            enter-active-class="transition duration-300 ease-out"
                            move-class="transition duration-300 ease-out"
                        >
                            <div
                                v-for="m in visibleMessages"
                                :key="m.id"
                                class="flex gap-2 sm:gap-3"
                                :class="
                                    m.role === 'user'
                                        ? 'justify-end'
                                        : 'justify-start'
                                "
                            >
                                <div
                                    v-if="m.role === 'assistant'"
                                    class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-xs font-semibold text-white shadow-sm sm:size-9"
                                >
                                    <HeartPulse class="size-4" />
                                </div>

                                <div class="max-w-[82%] min-w-0 sm:max-w-[78%]">
                                    <div
                                        class="rounded-2xl px-3.5 py-2.5 text-sm leading-relaxed whitespace-pre-wrap shadow-sm sm:px-4"
                                        :class="
                                            m.role === 'user'
                                                ? 'rounded-br-sm bg-emerald-600 text-white'
                                                : m.error
                                                  ? 'rounded-bl-sm border border-red-200 bg-red-50 text-red-900 dark:border-red-900/30 dark:bg-red-950/30 dark:text-red-100'
                                                  : 'rounded-bl-sm border border-slate-200 bg-white text-slate-900 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100'
                                        "
                                    >
                                        {{ m.content }}
                                    </div>

                                    <!-- Handoff banner -->
                                    <div
                                        v-if="m.handoff"
                                        class="mt-2 flex flex-col gap-2 rounded-xl border border-amber-300 bg-amber-50 p-3 text-xs text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-200"
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
                                                class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 font-medium hover:bg-amber-200 active:scale-95"
                                            >
                                                <Phone class="size-3" />
                                                CS
                                            </a>
                                            <a
                                                :href="`https://wa.me/${cleanWa}`"
                                                target="_blank"
                                                rel="noopener"
                                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 font-medium text-emerald-800 hover:bg-emerald-200 active:scale-95"
                                            >
                                                <MessageCircle class="size-3" />
                                                WhatsApp
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Meta -->
                                    <div
                                        class="mt-1 flex items-center gap-1.5 px-1 text-[10px] text-slate-400"
                                        :class="
                                            m.role === 'user'
                                                ? 'justify-end'
                                                : 'justify-start'
                                        "
                                    >
                                        <Clock class="size-3" />
                                        <span>{{ m.timestamp }}</span>
                                        <span
                                            v-if="
                                                m.role === 'assistant' &&
                                                m.used_llm === false &&
                                                !m.error &&
                                                !m.handoff
                                            "
                                            class="rounded-full bg-slate-100 px-1.5 py-0.5 text-[9px] font-medium text-slate-500 dark:bg-slate-800"
                                            >data RS</span
                                        >
                                        <span
                                            v-if="
                                                m.role === 'assistant' &&
                                                m.used_llm === true &&
                                                !m.error
                                            "
                                            class="rounded-full bg-emerald-50 px-1.5 py-0.5 text-[9px] font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400"
                                            >AI</span
                                        >
                                    </div>
                                </div>

                                <div
                                    v-if="m.role === 'user'"
                                    class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-slate-200 text-[10px] font-semibold text-slate-600 sm:size-9 dark:bg-slate-800 dark:text-slate-300"
                                >
                                    Anda
                                </div>
                            </div>
                        </TransitionGroup>

                        <!-- Typing indicator -->
                        <div v-if="sending" class="flex gap-2 sm:gap-3">
                            <div
                                class="flex size-8 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-sm sm:size-9"
                            >
                                <HeartPulse class="size-4" />
                            </div>
                            <div
                                class="flex items-center gap-1.5 rounded-2xl rounded-bl-sm border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900"
                            >
                                <span
                                    class="size-2 animate-bounce rounded-full bg-emerald-500 [animation-delay:-0.3s]"
                                />
                                <span
                                    class="size-2 animate-bounce rounded-full bg-emerald-500 [animation-delay:-0.15s]"
                                />
                                <span
                                    class="size-2 animate-bounce rounded-full bg-emerald-500"
                                />
                                <span class="ml-1 text-xs text-slate-500"
                                    >mengetik...</span
                                >
                            </div>
                        </div>
                    </div>
                </main>

                <!-- ========== INPUT BAR ========== -->
                <footer
                    class="flex-shrink-0 border-t border-slate-200 bg-white px-3 py-2.5 sm:px-4 sm:py-3 dark:border-slate-800 dark:bg-slate-900"
                    style="
                        padding-bottom: max(
                            0.5rem,
                            env(safe-area-inset-bottom)
                        );
                    "
                >
                    <form
                        class="mx-auto flex w-full max-w-2xl items-end gap-3 px-1"
                        @submit.prevent="send()"
                    >
                        <div
                            class="flex flex-1 items-end rounded-3xl border border-slate-200 bg-slate-50 px-3 py-1 transition focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-100 sm:px-4 sm:py-1.5 dark:border-slate-700 dark:bg-slate-800 dark:focus-within:border-emerald-500 dark:focus-within:ring-emerald-900/30"
                        >
                            <textarea
                                ref="inputRef"
                                v-model="input"
                                rows="1"
                                placeholder="Tulis pertanyaan Anda..."
                                :disabled="sending"
                                class="max-h-36 flex-1 resize-none border-0 bg-transparent py-1 text-base placeholder:text-slate-400 focus:outline-none disabled:opacity-60 sm:text-sm"
                                inputmode="text"
                                enterkeyhint="send"
                                @keydown="onInputKeydown"
                            />
                        </div>
                        <button
                            type="submit"
                            :disabled="sending || !input.trim()"
                            class="flex size-10 flex-shrink-0 items-center justify-center rounded-2xl bg-[#339966] text-white transition hover:bg-[#2b8055] active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
                            aria-label="Kirim pesan"
                        >
                            <Loader2
                                v-if="sending"
                                class="size-4 animate-spin"
                            />
                            <Send v-else class="size-4" />
                        </button>
                    </form>
                    <p
                        class="mx-auto mt-2 max-w-2xl text-center text-[10px] leading-snug text-slate-500 sm:text-[11px]"
                    >
                        Bot tidak menggantikan konsultasi medis. Darurat:
                        <a
                            :href="`tel:${cleanIgd}`"
                            class="font-medium text-rose-600 underline-offset-2 hover:underline"
                            >IGD {{ contact.igd }}</a
                        >
                        atau
                        <a
                            href="tel:119"
                            class="font-medium text-rose-600 underline-offset-2 hover:underline"
                            >119</a
                        >.
                    </p>
                </footer>
            </div>

            <!-- ========== SIDEBAR (desktop only) ========== -->
            <aside
                class="hidden overflow-y-auto border-l border-slate-200 bg-white px-5 py-6 lg:block dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="space-y-5">
                    <!-- Quick contact card -->
                    <div
                        class="overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 p-5 text-white"
                    >
                        <p
                            class="text-xs font-medium tracking-wide text-white/70 uppercase"
                        >
                            Kondisi Darurat?
                        </p>
                        <p class="mt-2 text-lg leading-snug font-semibold">
                            Hubungi IGD sekarang
                        </p>
                        <a
                            :href="`tel:${cleanIgd}`"
                            class="mt-4 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-rose-700 shadow-sm transition hover:shadow-md"
                        >
                            <Phone class="size-4" />
                            {{ contact.igd }}
                        </a>
                    </div>

                    <!-- Contact list -->
                    <div>
                        <p
                            class="mb-3 text-xs font-semibold tracking-wide text-slate-500 uppercase"
                        >
                            Hubungi Kami
                        </p>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a
                                    :href="`tel:${cleanPhone}`"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-slate-100 dark:hover:bg-slate-800"
                                >
                                    <span
                                        class="flex size-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400"
                                    >
                                        <Phone class="size-4" />
                                    </span>
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Customer Service
                                        </p>
                                        <p class="text-sm font-medium">
                                            {{ contact.phone }}
                                        </p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a
                                    :href="`https://wa.me/${cleanWa}`"
                                    target="_blank"
                                    rel="noopener"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-slate-100 dark:hover:bg-slate-800"
                                >
                                    <span
                                        class="flex size-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400"
                                    >
                                        <MessageCircle class="size-4" />
                                    </span>
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            WhatsApp
                                        </p>
                                        <p class="text-sm font-medium">
                                            {{ contact.whatsapp }}
                                        </p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Topics -->
                    <div v-if="categories.length > 0">
                        <p
                            class="mb-3 text-xs font-semibold tracking-wide text-slate-500 uppercase"
                        >
                            Topik yang Bisa Saya Bantu
                        </p>
                        <ul class="space-y-1.5 text-xs">
                            <li
                                v-for="cat in categories"
                                :key="cat.name"
                                class="flex items-start gap-2 text-slate-600 dark:text-slate-400"
                                :title="cat.description ?? ''"
                            >
                                <CheckCircle2
                                    class="mt-0.5 size-3.5 flex-shrink-0 text-emerald-500"
                                />
                                <span>{{ cat.name }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Disclaimer -->
                    <div
                        class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900 dark:border-amber-900/30 dark:bg-amber-950/30 dark:text-amber-200"
                    >
                        <p class="flex items-center gap-1.5 font-semibold">
                            <AlertTriangle class="size-3.5" />
                            Catatan
                        </p>
                        <p class="mt-1 leading-relaxed">
                            Asisten ini tidak memberikan diagnosis, saran obat,
                            atau interpretasi hasil pemeriksaan. Pertanyaan
                            medis akan otomatis diteruskan ke petugas.
                        </p>
                    </div>
                </div>
            </aside>
        </div>

        <!-- ========== MOBILE INFO BOTTOM SHEET ========== -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            leave-active-class="transition duration-150 ease-in"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div
                v-if="infoSheetOpen"
                class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm lg:hidden"
                @click="infoSheetOpen = false"
            />
        </Transition>
        <Transition
            enter-active-class="transition duration-300 ease-out"
            leave-active-class="transition duration-200 ease-in"
            enter-from-class="translate-y-full"
            leave-to-class="translate-y-full"
        >
            <div
                v-if="infoSheetOpen"
                class="fixed inset-x-0 bottom-0 z-50 max-h-[85vh] overflow-y-auto rounded-t-3xl bg-white shadow-2xl lg:hidden dark:bg-slate-900"
                style="padding-bottom: env(safe-area-inset-bottom)"
            >
                <div class="sticky top-0 bg-white pt-2 dark:bg-slate-900">
                    <div
                        class="mx-auto h-1.5 w-12 rounded-full bg-slate-300 dark:bg-slate-700"
                    />
                    <div
                        class="mt-2 flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-slate-800"
                    >
                        <h2 class="font-semibold">Info & Kontak</h2>
                        <button
                            type="button"
                            class="flex size-9 items-center justify-center rounded-lg text-slate-500 active:bg-slate-100 dark:active:bg-slate-800"
                            @click="infoSheetOpen = false"
                        >
                            <X class="size-5" />
                        </button>
                    </div>
                </div>

                <div class="space-y-5 px-5 py-5">
                    <!-- IGD card -->
                    <a
                        :href="`tel:${cleanIgd}`"
                        class="flex items-center justify-between rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 p-4 text-white shadow-md active:scale-[0.98]"
                    >
                        <div>
                            <p
                                class="text-[11px] font-medium tracking-wide text-white/70 uppercase"
                            >
                                Kondisi Darurat
                            </p>
                            <p class="text-base font-semibold">
                                Tap untuk hubungi IGD
                            </p>
                            <p class="mt-1 text-xs text-white/80">
                                {{ contact.igd }}
                            </p>
                        </div>
                        <span
                            class="flex size-12 items-center justify-center rounded-full bg-white text-rose-700"
                        >
                            <Phone class="size-5" />
                        </span>
                    </a>

                    <!-- Contact buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        <a
                            :href="`tel:${cleanPhone}`"
                            class="flex flex-col items-center gap-2 rounded-2xl border border-slate-200 bg-white p-4 text-center text-sm active:scale-95 dark:border-slate-800 dark:bg-slate-900"
                        >
                            <span
                                class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400"
                            >
                                <Phone class="size-5" />
                            </span>
                            <span class="font-medium">Telepon CS</span>
                            <span class="text-xs text-slate-500">{{
                                contact.phone
                            }}</span>
                        </a>
                        <a
                            :href="`https://wa.me/${cleanWa}`"
                            target="_blank"
                            rel="noopener"
                            class="flex flex-col items-center gap-2 rounded-2xl border border-slate-200 bg-white p-4 text-center text-sm active:scale-95 dark:border-slate-800 dark:bg-slate-900"
                        >
                            <span
                                class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400"
                            >
                                <MessageCircle class="size-5" />
                            </span>
                            <span class="font-medium">WhatsApp</span>
                            <span class="text-xs text-slate-500">{{
                                contact.whatsapp
                            }}</span>
                        </a>
                    </div>

                    <!-- Topics -->
                    <div v-if="categories.length > 0">
                        <p
                            class="mb-2 text-[11px] font-semibold tracking-wide text-slate-500 uppercase"
                        >
                            Topik yang Bisa Saya Bantu
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="cat in categories"
                                :key="cat.name"
                                class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs text-slate-700 dark:border-emerald-900/40 dark:bg-emerald-950/30 dark:text-slate-300"
                            >
                                <CheckCircle2 class="size-3 text-emerald-500" />
                                {{ cat.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Disclaimer -->
                    <div
                        class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900 dark:border-amber-900/30 dark:bg-amber-950/30 dark:text-amber-200"
                    >
                        <p class="flex items-center gap-1.5 font-semibold">
                            <AlertTriangle class="size-3.5" />
                            Catatan
                        </p>
                        <p class="mt-1 leading-relaxed">
                            Asisten ini tidak memberikan diagnosis, saran obat,
                            atau interpretasi hasil pemeriksaan.
                        </p>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
