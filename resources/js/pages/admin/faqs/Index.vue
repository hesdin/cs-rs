<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
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
    category_id: number;
    category: { id: number; name: string } | null;
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

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    faqs: Paginated<Faq>;
    categories: Category[];
    filters: { q: string; category_id: number | null; status: string };
}>();

const search = ref(props.filters.q ?? '');
const categoryId = ref<number | ''>(props.filters.category_id ?? '');
const status = ref(props.filters.status ?? 'all');

const applyFilters = () => {
    router.get(
        admin.faqs.index().url,
        {
            q: search.value || undefined,
            category_id: categoryId.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
        },
        { preserveState: true, replace: true },
    );
};

watch([categoryId, status], applyFilters);

const hasActiveFilters = computed(
    () =>
        !!search.value ||
        !!categoryId.value ||
        (status.value && status.value !== 'all'),
);

const resetFilters = () => {
    search.value = '';
    categoryId.value = '';
    status.value = 'all';
    router.get(admin.faqs.index().url, {}, { preserveState: true, replace: true });
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

        <Card>
            <CardContent class="p-4">
                <form
                    class="flex flex-wrap items-end gap-3"
                    @submit.prevent="applyFilters"
                >
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-muted-foreground"
                            >Pencarian</label
                        >
                        <Input
                            v-model="search"
                            placeholder="Pertanyaan, jawaban, kategori..."
                            class="w-72"
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-muted-foreground"
                            >Kategori</label
                        >
                        <select
                            v-model="categoryId"
                            class="h-9 w-44 rounded-md border bg-background px-2 text-sm"
                        >
                            <option value="">Semua kategori</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-muted-foreground"
                            >Status</label
                        >
                        <select
                            v-model="status"
                            class="h-9 w-36 rounded-md border bg-background px-2 text-sm"
                        >
                            <option value="all">Semua</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>

                    <Button type="submit" variant="outline">Terapkan</Button>
                    <Button
                        v-if="hasActiveFilters"
                        type="button"
                        variant="ghost"
                        @click="resetFilters"
                    >
                        <X class="size-4" /> Reset
                    </Button>

                    <span class="ml-auto text-xs text-muted-foreground">
                        Menampilkan {{ faqs.data.length }} dari
                        {{ faqs.total }} FAQ
                    </span>
                </form>
            </CardContent>
        </Card>

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
                                >{{ faq.category?.name ?? '—' }}</span>
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
                                Tidak ada FAQ yang cocok dengan filter.
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
