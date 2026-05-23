<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import admin from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'Percakapan', href: admin.conversations.index().url },
        ],
    },
});

interface Conversation {
    id: number;
    session_id: string;
    channel: string;
    user_identifier: string | null;
    started_at: string;
    last_activity_at: string | null;
    handed_off: boolean;
    messages_count: number;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    conversations: Paginated<Conversation>;
    filters: { q: string; handed_off: boolean };
}>();

const search = ref(props.filters.q ?? '');
const handedOff = ref(!!props.filters.handed_off);

const submitFilters = () => {
    router.get(
        admin.conversations.index().url,
        { q: search.value, handed_off: handedOff.value ? 1 : 0 },
        { preserveState: true, replace: true },
    );
};
</script>

<template>
    <Head title="Percakapan" />

    <div class="flex flex-col gap-4 p-6">
        <div>
            <h1 class="text-2xl font-semibold">Log Percakapan</h1>
            <p class="text-sm text-muted-foreground">
                Audit trail percakapan chatbot.
            </p>
        </div>

        <form class="flex flex-wrap items-center gap-3" @submit.prevent="submitFilters">
            <Input
                v-model="search"
                placeholder="Cari session id / IP..."
                class="max-w-sm"
            />
            <label class="flex items-center gap-2 text-sm">
                <input
                    v-model="handedOff"
                    type="checkbox"
                    class="size-4 rounded border"
                />
                Hanya yang handoff
            </label>
            <Button variant="outline" type="submit">Terapkan</Button>
        </form>

        <Card>
            <CardContent class="p-0">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">Session</th>
                            <th class="px-4 py-3 font-medium">Channel</th>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Pesan</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Mulai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="c in conversations.data"
                            :key="c.id"
                            class="cursor-pointer border-b last:border-0 hover:bg-muted/30"
                            @click="router.visit(admin.conversations.show({ conversation: c.id }).url)"
                        >
                            <td class="px-4 py-3 font-mono text-xs">
                                {{ c.session_id.slice(0, 8) }}…
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded bg-slate-100 px-2 py-0.5 text-xs dark:bg-slate-800"
                                >{{ c.channel }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                {{ c.user_identifier ?? '-' }}
                            </td>
                            <td class="px-4 py-3">{{ c.messages_count }}</td>
                            <td class="px-4 py-3">
                                <span
                                    v-if="c.handed_off"
                                    class="rounded-full bg-rose-100 px-2 py-0.5 text-xs text-rose-700"
                                >Handoff</span>
                                <span
                                    v-else
                                    class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700"
                                >Auto</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-muted-foreground">
                                {{
                                    new Date(c.started_at).toLocaleString(
                                        'id-ID',
                                    )
                                }}
                            </td>
                        </tr>
                        <tr v-if="conversations.data.length === 0">
                            <td
                                colspan="6"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Belum ada percakapan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>

        <div
            v-if="conversations.last_page > 1"
            class="flex items-center justify-between text-sm"
        >
            <span class="text-muted-foreground"
                >Halaman {{ conversations.current_page }} /
                {{ conversations.last_page }} ({{ conversations.total }})</span
            >
            <div class="flex gap-1">
                <template
                    v-for="link in conversations.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded border px-3 py-1 text-xs"
                        :class="
                            link.active ? 'bg-primary text-primary-foreground' : ''
                        "
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
