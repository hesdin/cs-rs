<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ShieldCheck, Sparkles } from 'lucide-vue-next';
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
    type: 'text' | 'textarea' | 'password';
    value: string;
    sensitive?: boolean;
}

defineProps<{
    settings: SettingItem[];
    modelInfo: { model: string; temperature: string; configured: boolean };
}>();

const openrouterSettings = (items: SettingItem[]) =>
    items.filter((i) => i.group === 'openrouter');

const nonOpenrouterSettings = (items: SettingItem[]) =>
    items.filter((i) => i.group !== 'openrouter');

function maskApiKey(value: string): string {
    if (!value || value.length < 12) {
        return value;
    }
    return value.slice(0, 12) + '••••••';
}
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

        <!-- LLM Config Card -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-base">
                    <Sparkles class="size-4" /> Konfigurasi LLM (OpenRouter)
                </CardTitle>
                <CardDescription>
                    {{ modelInfo.configured ? 'Menggunakan API key dari database atau .env.' : 'API key belum dikonfigurasi.' }}
                </CardDescription>
            </CardHeader>
            <CardContent class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs text-muted-foreground">Model aktif</p>
                    <p class="font-mono">{{ modelInfo.model || '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Temperature</p>
                    <p>{{ modelInfo.temperature || '-' }}</p>
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
                    <!-- Password input for sensitive fields (API key) -->
                    <Input
                        v-if="item.type === 'password'"
                        :name="`settings[${item.key}]`"
                        type="password"
                        autocomplete="off"
                        :placeholder="item.value ? '•••••••• (kosongkan jika tidak ingin mengubah)' : 'Masukkan API key'"
                    />
                    <!-- Text input -->
                    <Input
                        v-else-if="item.type === 'text'"
                        :name="`settings[${item.key}]`"
                        type="text"
                        :placeholder="`Masukkan ${item.label}`"
                        :default-value="item.value"
                    />
                    <!-- Textarea -->
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
