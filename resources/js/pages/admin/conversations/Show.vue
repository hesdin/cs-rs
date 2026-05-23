<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import admin from '@/routes/admin';

interface Message {
    id: number;
    role: string;
    content: string;
    intent: string | null;
    was_blocked: boolean;
    used_llm: boolean;
    model: string | null;
    created_at: string;
}
interface Conversation {
    id: number;
    session_id: string;
    channel: string;
    user_identifier: string | null;
    started_at: string;
    handed_off: boolean;
    messages: Message[];
}

defineProps<{ conversation: Conversation }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'Percakapan', href: admin.conversations.index().url },
            { title: 'Detail', href: '#' },
        ],
    },
});

const intentColor = (i: string | null) => {
    switch (i) {
        case 'emergency':
            return 'bg-rose-100 text-rose-700';
        case 'medical_advice':
            return 'bg-amber-100 text-amber-800';
        case 'blocked':
            return 'bg-slate-200 text-slate-700';
        default:
            return 'bg-emerald-100 text-emerald-700';
    }
};
</script>

<template>
    <Head :title="`Percakapan #${conversation.id}`" />

    <div class="flex flex-col gap-4 p-6">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon-sm" as-child>
                <Link :href="admin.conversations.index().url"
                    ><ArrowLeft class="size-4"
                /></Link>
            </Button>
            <div>
                <h1 class="text-xl font-semibold">
                    Percakapan #{{ conversation.id }}
                </h1>
                <p class="font-mono text-xs text-muted-foreground">
                    {{ conversation.session_id }}
                </p>
            </div>
        </div>

        <Card>
            <CardContent class="grid grid-cols-2 gap-4 py-4 text-sm md:grid-cols-4">
                <div>
                    <p class="text-xs text-muted-foreground">Channel</p>
                    <p class="font-medium">{{ conversation.channel }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">User</p>
                    <p class="font-medium">
                        {{ conversation.user_identifier ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Mulai</p>
                    <p class="font-medium">
                        {{
                            new Date(conversation.started_at).toLocaleString(
                                'id-ID',
                            )
                        }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Handoff</p>
                    <p class="font-medium">
                        {{ conversation.handed_off ? 'Ya' : 'Tidak' }}
                    </p>
                </div>
            </CardContent>
        </Card>

        <div class="flex flex-col gap-3">
            <div
                v-for="m in conversation.messages"
                :key="m.id"
                class="flex gap-3"
                :class="m.role === 'user' ? 'justify-end' : 'justify-start'"
            >
                <div
                    class="max-w-[75%] rounded-2xl border px-4 py-2.5 text-sm whitespace-pre-wrap"
                    :class="
                        m.role === 'user'
                            ? 'bg-emerald-600 text-white'
                            : 'bg-card'
                    "
                >
                    <div class="flex flex-wrap items-center gap-2 text-[10px] opacity-80 mb-1">
                        <span class="font-medium uppercase">{{ m.role }}</span>
                        <span
                            v-if="m.intent"
                            class="rounded px-1.5 py-0.5"
                            :class="intentColor(m.intent)"
                        >{{ m.intent }}</span>
                        <span v-if="m.was_blocked" class="rounded bg-amber-200 px-1.5 py-0.5 text-amber-900">blocked</span>
                        <span v-if="m.used_llm && m.model" class="rounded bg-indigo-100 px-1.5 py-0.5 text-indigo-700">{{ m.model }}</span>
                        <span class="opacity-70">{{
                            new Date(m.created_at).toLocaleTimeString('id-ID')
                        }}</span>
                    </div>
                    {{ m.content }}
                </div>
            </div>
        </div>
    </div>
</template>
