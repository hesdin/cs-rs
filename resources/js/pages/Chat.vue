<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { home } from '@/routes';

interface ChatMessage {
    role: 'user' | 'assistant' | 'system';
    content: string;
    intent?: string;
    handoff?: boolean;
    used_llm?: boolean;
    error?: boolean;
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

const props = defineProps<{
    welcomeMessage?: string;
    hospitalName?: string;
}>();

const sessionId = ref<string | null>(null);
const messages = ref<ChatMessage[]>([]);
const input = ref('');
const sending = ref(false);
const scrollContainer = ref<HTMLDivElement | null>(null);

const SUGGESTIONS = [
    'Bagaimana cara mendaftar?',
    'Apakah RS menerima BPJS?',
    'Jadwal dokter spesialis anak',
    'Jam besuk pasien rawat inap',
];

onMounted(() => {
    messages.value.push({
        role: 'assistant',
        content:
            props.welcomeMessage ??
            `Halo, saya asisten virtual ${props.hospitalName ?? 'RS Sehat Sentosa'}. Saya bisa bantu informasi jadwal dokter, pendaftaran, BPJS, dan administrasi rumah sakit. Ada yang bisa saya bantu?`,
    });
});

const scrollToBottom = async () => {
    await nextTick();
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight;
    }
};

const csrfToken = (): string => {
    return (
        document
            .querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? ''
    );
};

const send = async (text?: string) => {
    const content = (text ?? input.value).trim();
    if (!content || sending.value) {
        return;
    }

    messages.value.push({ role: 'user', content });
    input.value = '';
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

        messages.value.push({
            role: 'assistant',
            content: json.data.reply,
            intent: json.data.intent,
            handoff: json.data.handoff,
            used_llm: json.data.used_llm,
        });
    } catch (e) {
        messages.value.push({
            role: 'assistant',
            content:
                'Maaf, terjadi gangguan koneksi ke layanan. Silakan coba lagi.',
            error: true,
        });
    } finally {
        sending.value = false;
        await scrollToBottom();
    }
};
</script>

<template>
    <Head title="Chat" />

    <div
        class="flex min-h-screen flex-col bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900"
    >
        <header
            class="flex items-center justify-between border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80"
        >
            <Link :href="home()" class="flex items-center gap-2">
                <div
                    class="flex size-9 items-center justify-center rounded-lg bg-emerald-600 text-white"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="size-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold">
                        {{ hospitalName ?? 'RS Sehat Sentosa' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        Asisten Customer Service
                    </p>
                </div>
            </Link>
            <span
                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
            >
                <span
                    class="size-1.5 rounded-full bg-emerald-500 animate-pulse"
                />
                Online
            </span>
        </header>

        <main
            ref="scrollContainer"
            class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-4 overflow-y-auto p-4"
        >
            <div
                v-for="(m, idx) in messages"
                :key="idx"
                class="flex gap-3"
                :class="m.role === 'user' ? 'justify-end' : 'justify-start'"
            >
                <div
                    v-if="m.role === 'assistant'"
                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-medium text-white"
                >
                    AI
                </div>
                <div
                    class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed whitespace-pre-wrap shadow-sm"
                    :class="
                        m.role === 'user'
                            ? 'bg-emerald-600 text-white'
                            : m.error
                              ? 'bg-red-50 text-red-900 dark:bg-red-950 dark:text-red-100'
                              : 'bg-white text-slate-900 dark:bg-slate-800 dark:text-slate-100'
                    "
                >
                    {{ m.content }}
                    <div
                        v-if="m.handoff"
                        class="mt-2 flex flex-col gap-2 rounded-lg border border-amber-300 bg-amber-50 p-2 text-xs text-amber-900"
                    >
                        <span class="font-medium"
                            >➡ Pertanyaan ini diarahkan ke petugas
                            customer service / IGD.</span
                        >
                    </div>
                </div>
            </div>

            <div v-if="sending" class="flex gap-3">
                <div
                    class="flex size-8 items-center justify-center rounded-full bg-emerald-600 text-xs font-medium text-white"
                >
                    AI
                </div>
                <div
                    class="flex items-center gap-1 rounded-2xl bg-white px-4 py-3 dark:bg-slate-800"
                >
                    <span
                        class="size-1.5 animate-bounce rounded-full bg-slate-400 [animation-delay:-0.3s]"
                    />
                    <span
                        class="size-1.5 animate-bounce rounded-full bg-slate-400 [animation-delay:-0.15s]"
                    />
                    <span
                        class="size-1.5 animate-bounce rounded-full bg-slate-400"
                    />
                </div>
            </div>

            <div
                v-if="messages.length <= 1 && !sending"
                class="flex flex-wrap gap-2 pt-2"
            >
                <button
                    v-for="s in SUGGESTIONS"
                    :key="s"
                    type="button"
                    class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                    @click="send(s)"
                >
                    {{ s }}
                </button>
            </div>
        </main>

        <footer
            class="border-t border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900"
        >
            <form
                class="mx-auto flex w-full max-w-3xl items-center gap-2"
                @submit.prevent="send()"
            >
                <Input
                    v-model="input"
                    type="text"
                    placeholder="Ketik pertanyaan Anda..."
                    :disabled="sending"
                    class="flex-1"
                />
                <Button type="submit" :disabled="sending || !input.trim()">
                    Kirim
                </Button>
            </form>
            <p
                class="mx-auto mt-2 max-w-3xl text-center text-[11px] text-slate-500"
            >
                Bot ini tidak menggantikan konsultasi medis. Untuk darurat
                hubungi IGD / 119.
            </p>
        </footer>
    </div>
</template>
