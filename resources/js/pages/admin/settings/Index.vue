<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';
import ChatbotSettingController from '@/actions/App/Http/Controllers/Admin/ChatbotSettingController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'Pengaturan Chatbot', href: admin.chatbotSettings.edit().url },
        ],
    },
});

interface SettingItem {
    key: string;
    label: string;
    group: string;
    description: string;
    type: 'text' | 'textarea';
    value: string;
}

defineProps<{
    settings: SettingItem[];
    modelInfo: { model: string; temperature: number; configured: boolean };
}>();
</script>

<template>
    <Head title="Pengaturan Chatbot" />

    <div class="flex flex-col gap-4 p-6">
        <div>
            <h1 class="text-2xl font-semibold">Pengaturan Chatbot</h1>
            <p class="text-sm text-muted-foreground">
                Kelola disclaimer, fallback, dan pesan keamanan tanpa deploy ulang.
            </p>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-base">
                    <ShieldCheck class="size-4" /> Konfigurasi LLM
                </CardTitle>
                <CardDescription
                    >Diatur dari <code>config/openrouter.php</code> & .env.</CardDescription>
            </CardHeader>
            <CardContent class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs text-muted-foreground">Model aktif</p>
                    <p class="font-mono">{{ modelInfo.model }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Temperature</p>
                    <p>{{ modelInfo.temperature }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">API Key</p>
                    <p
                        :class="
                            modelInfo.configured
                                ? 'text-emerald-600'
                                : 'text-rose-600'
                        "
                    >
                        {{ modelInfo.configured ? 'Terkonfigurasi' : 'Belum diisi' }}
                    </p>
                </div>
            </CardContent>
        </Card>

        <Form
            v-bind="ChatbotSettingController.update.form()"
            class="space-y-4"
            v-slot="{ errors, processing }"
        >
            <Card v-for="item in settings" :key="item.key">
                <CardHeader>
                    <CardTitle class="text-base">{{ item.label }}</CardTitle>
                    <CardDescription>{{ item.description }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <input
                        v-if="item.type === 'text'"
                        :name="`settings[${item.key}]`"
                        :default-value="item.value"
                        class="h-9 w-full rounded-md border bg-background px-3 text-sm"
                    />
                    <textarea
                        v-else
                        :name="`settings[${item.key}]`"
                        rows="4"
                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                    >{{ item.value }}</textarea>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button :disabled="processing">Simpan Pengaturan</Button>
            </div>
        </Form>
    </div>
</template>
