<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    MessageSquare,
    MessagesSquare,
    ShieldAlert,
    UserRound,
    UserRoundCog,
    HelpCircle,
    PhoneCall,
} from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import admin from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Admin Chatbot', href: admin.dashboard().url }],
    },
});

defineProps<{
    stats: {
        totalConversations: number;
        todayConversations: number;
        totalMessages: number;
        blockedMessages: number;
        handoffs: number;
        totalDoctors: number;
        totalFaqs: number;
    };
    messagesPerDay: { day: string; total: number }[];
}>();
</script>

<template>
    <Head title="Admin Chatbot" />

    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Dashboard Chatbot
            </h1>
            <p class="text-sm text-muted-foreground">
                Ringkasan aktivitas asisten virtual rumah sakit.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium"
                        >Percakapan</CardTitle
                    >
                    <MessageSquare class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.totalConversations }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        +{{ stats.todayConversations }} hari ini
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium">Pesan</CardTitle>
                    <MessagesSquare class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.totalMessages }}
                    </div>
                    <p class="text-xs text-muted-foreground">total semua pesan</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium"
                        >Diblokir / Sensitif</CardTitle
                    >
                    <ShieldAlert class="size-4 text-amber-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.blockedMessages }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        ditangani filter rule-based
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium"
                        >Handoff Petugas</CardTitle
                    >
                    <PhoneCall class="size-4 text-rose-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.handoffs }}</div>
                    <p class="text-xs text-muted-foreground">
                        diteruskan ke manusia / IGD
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium"
                        >Dokter Aktif</CardTitle
                    >
                    <UserRound class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.totalDoctors }}
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium">FAQ Aktif</CardTitle>
                    <HelpCircle class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.totalFaqs }}</div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Volume pesan 7 hari terakhir</CardTitle>
                <CardDescription
                    >Jumlah pesan masuk per hari (user + assistant).</CardDescription
                >
            </CardHeader>
            <CardContent>
                <div
                    v-if="messagesPerDay.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    Belum ada percakapan.
                </div>
                <div
                    v-else
                    class="flex h-40 items-end gap-3 border-l border-b border-border pt-4"
                >
                    <div
                        v-for="row in messagesPerDay"
                        :key="row.day"
                        class="flex flex-1 flex-col items-center gap-2"
                    >
                        <span class="text-xs text-muted-foreground">{{
                            row.total
                        }}</span>
                        <div
                            class="w-full rounded-t bg-emerald-500 transition-all"
                            :style="{
                                height:
                                    Math.max(
                                        4,
                                        (row.total /
                                            Math.max(
                                                ...messagesPerDay.map(
                                                    (r) => r.total,
                                                ),
                                            )) *
                                            120,
                                    ) + 'px',
                            }"
                        />
                        <span
                            class="text-[10px] uppercase text-muted-foreground"
                        >{{ row.day.slice(5) }}</span>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
