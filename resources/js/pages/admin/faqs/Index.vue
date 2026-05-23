<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import FaqController from '@/actions/App/Http/Controllers/Admin/FaqController';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import admin from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'FAQ', href: admin.faqs.index().url },
        ],
    },
});

interface Faq {
    id: number;
    category: string;
    question: string;
    answer: string;
    is_active: boolean;
    priority: number;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    faqs: Paginated<Faq>;
    filters: { q: string };
}>();

const search = ref(props.filters.q ?? '');

const submitSearch = () => {
    router.get(
        admin.faqs.index().url,
        { q: search.value },
        { preserveState: true, replace: true },
    );
};

const remove = (faq: Faq) => {
    if (!confirm(`Hapus FAQ "${faq.question}"?`)) return;
    router.delete(FaqController.destroy.url({ faq: faq.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="FAQ" />

    <div class="flex flex-col gap-4 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">FAQ Rumah Sakit</h1>
                <p class="text-sm text-muted-foreground">
                    Sumber jawaban yang dibaca chatbot.
                </p>
            </div>
            <Button as-child>
                <Link :href="admin.faqs.create().url">
                    <Plus class="size-4" /> Tambah FAQ
                </Link>
            </Button>
        </div>

        <form class="flex gap-2" @submit.prevent="submitSearch">
            <Input
                v-model="search"
                placeholder="Cari pertanyaan, kategori..."
                class="max-w-sm"
            />
            <Button variant="outline" type="submit">Cari</Button>
        </form>

        <Card>
            <CardContent class="p-0">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">Kategori</th>
                            <th class="px-4 py-3 font-medium">Pertanyaan</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Prioritas</th>
                            <th class="px-4 py-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="faq in faqs.data"
                            :key="faq.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 align-top">
                                <span
                                    class="inline-block rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                >{{ faq.category }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ faq.question }}</p>
                                <p
                                    class="mt-1 line-clamp-2 text-xs text-muted-foreground"
                                >
                                    {{ faq.answer }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs"
                                    :class="
                                        faq.is_active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-slate-100 text-slate-500'
                                    "
                                >
                                    {{ faq.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                {{ faq.priority }}
                            </td>
                            <td class="px-4 py-3 text-right align-top">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                admin.faqs.edit({
                                                    faq: faq.id,
                                                }).url
                                            "
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        @click="remove(faq)"
                                    >
                                        <Trash2 class="size-4 text-rose-600" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="faqs.data.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Belum ada FAQ.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>

        <div
            v-if="faqs.last_page > 1"
            class="flex items-center justify-between text-sm"
        >
            <span class="text-muted-foreground"
                >Halaman {{ faqs.current_page }} dari {{ faqs.last_page }} ({{
                    faqs.total
                }}
                total)</span
            >
            <div class="flex gap-1">
                <template v-for="link in faqs.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded border px-3 py-1 text-xs"
                        :class="
                            link.active ? 'bg-primary text-primary-foreground' : ''
                        "
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="rounded border px-3 py-1 text-xs text-muted-foreground"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
