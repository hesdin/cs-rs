<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import FaqCategoryController from '@/actions/App/Http/Controllers/Admin/FaqCategoryController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    color: string | null;
    icon: string | null;
    sort_order: number;
    is_active: boolean;
}

const props = defineProps<{
    category: Category | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'Kategori', href: admin.categories.index().url },
            { title: 'Form', href: '#' },
        ],
    },
});

const action = props.category
    ? FaqCategoryController.update.form({ category: props.category.id })
    : FaqCategoryController.store.form();

const initial = {
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
    color: props.category?.color ?? '#64748b',
    icon: props.category?.icon ?? '',
    sort_order: props.category?.sort_order ?? 0,
    is_active: props.category?.is_active ?? true,
};
</script>

<template>
    <Head :title="category ? 'Edit Kategori' : 'Tambah Kategori'" />

    <div class="flex flex-col gap-4 p-6">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon-sm" as-child>
                <Link :href="admin.categories.index().url"
                    ><ArrowLeft class="size-4"
                /></Link>
            </Button>
            <h1 class="text-2xl font-semibold">
                {{ category ? 'Edit Kategori' : 'Tambah Kategori' }}
            </h1>
        </div>

        <Form
            v-bind="action"
            :transform="(data: any) => ({
                ...data,
                is_active: !!data.is_active,
                sort_order: Number(data.sort_order || 0),
            })"
            class="space-y-4 rounded-lg border bg-card p-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Nama Kategori</Label>
                <Input
                    id="name"
                    name="name"
                    :default-value="initial.name"
                    placeholder="contoh: Pendaftaran"
                    required
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Deskripsi</Label>
                <Input
                    id="description"
                    name="description"
                    :default-value="initial.description"
                    placeholder="Singkat saja, ditampilkan di list kategori"
                />
                <InputError :message="errors.description" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="grid gap-2">
                    <Label for="color">Warna</Label>
                    <Input
                        id="color"
                        name="color"
                        type="color"
                        :default-value="initial.color"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="icon"
                        >Icon
                        <span class="text-xs text-muted-foreground"
                            >(opsional, nama lucide)</span
                        ></Label
                    >
                    <Input
                        id="icon"
                        name="icon"
                        :default-value="initial.icon"
                        placeholder="info"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="sort_order">Urutan</Label>
                    <Input
                        id="sort_order"
                        name="sort_order"
                        type="number"
                        :default-value="initial.sort_order"
                        min="0"
                        max="1000"
                    />
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="is_active"
                    name="is_active"
                    type="checkbox"
                    :checked="initial.is_active"
                    value="1"
                    class="size-4 rounded border"
                />
                <Label for="is_active">Aktif</Label>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <Button variant="outline" as-child>
                    <Link :href="admin.categories.index().url">Batal</Link>
                </Button>
                <Button :disabled="processing">Simpan</Button>
            </div>
        </Form>
    </div>
</template>
