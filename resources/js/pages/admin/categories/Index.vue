<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import FaqCategoryController from '@/actions/App/Http/Controllers/Admin/FaqCategoryController';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import admin from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'Kategori FAQ', href: admin.categories.index().url },
        ],
    },
});

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    color: string | null;
    icon: string | null;
    sort_order: number;
    is_active: boolean;
    faqs_count: number;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    categories: Paginated<Category>;
    filters: { q: string };
}>();

const search = ref(props.filters.q ?? '');

const submitSearch = () => {
    router.get(
        admin.categories.index().url,
        { q: search.value || undefined },
        { preserveState: true, replace: true },
    );
};

const remove = (cat: Category) => {
    if (cat.faqs_count > 0) {
        alert(
            `Kategori "${cat.name}" tidak dapat dihapus karena masih digunakan oleh ${cat.faqs_count} FAQ. Pindahkan FAQ ke kategori lain terlebih dahulu.`,
        );
        return;
    }
    if (!confirm(`Hapus kategori "${cat.name}"?`)) return;
    router.delete(FaqCategoryController.destroy.url({ category: cat.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Kategori FAQ" />

    <div class="flex flex-col gap-4 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Kategori FAQ</h1>
                <p class="text-sm text-muted-foreground">
                    Sumber kebenaran tunggal untuk pengelompokan FAQ. Mencegah
                    typo dan memudahkan rename.
                </p>
            </div>
            <Button as-child>
                <Link :href="admin.categories.create().url">
                    <Plus class="size-4" /> Tambah Kategori
                </Link>
            </Button>
        </div>

        <form class="flex gap-2" @submit.prevent="submitSearch">
            <Input
                v-model="search"
                placeholder="Cari nama kategori..."
                class="max-w-sm"
            />
            <Button variant="outline" type="submit">Cari</Button>
        </form>

        <Card>
            <CardContent class="p-0">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium w-16">Urutan</th>
                            <th class="px-4 py-3 font-medium">Kategori</th>
                            <th class="px-4 py-3 font-medium">Deskripsi</th>
                            <th class="px-4 py-3 font-medium">Jumlah FAQ</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="c in categories.data"
                            :key="c.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ c.sort_order }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-block size-3 rounded-full border"
                                        :style="{
                                            background: c.color ?? '#cbd5e1',
                                        }"
                                    />
                                    <div>
                                        <p class="font-medium">{{ c.name }}</p>
                                        <p
                                            class="font-mono text-xs text-muted-foreground"
                                        >
                                            {{ c.slug }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="px-4 py-3 text-xs text-muted-foreground"
                            >
                                {{ c.description ?? '—' }}
                            </td>
                            <td class="px-4 py-3">{{ c.faqs_count }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs"
                                    :class="
                                        c.is_active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-slate-100 text-slate-500'
                                    "
                                >
                                    {{ c.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                admin.categories.edit({
                                                    category: c.id,
                                                }).url
                                            "
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        :disabled="c.faqs_count > 0"
                                        :title="
                                            c.faqs_count > 0
                                                ? 'Masih dipakai oleh FAQ'
                                                : 'Hapus'
                                        "
                                        @click="remove(c)"
                                    >
                                        <Trash2 class="size-4 text-rose-600" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categories.data.length === 0">
                            <td
                                colspan="6"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Belum ada kategori.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
