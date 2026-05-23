<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import FaqController from '@/actions/App/Http/Controllers/Admin/FaqController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

interface Faq {
    id: number;
    category: string;
    question: string;
    answer: string;
    is_active: boolean;
    priority: number;
    keywords: string[] | null;
}

const props = defineProps<{
    faq: Faq | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'FAQ', href: admin.faqs.index().url },
            { title: 'Form', href: '#' },
        ],
    },
});

const action = props.faq
    ? FaqController.update.form({ faq: props.faq.id })
    : FaqController.store.form();

const initial = {
    category: props.faq?.category ?? '',
    question: props.faq?.question ?? '',
    answer: props.faq?.answer ?? '',
    priority: props.faq?.priority ?? 0,
    is_active: props.faq?.is_active ?? true,
    keywords: (props.faq?.keywords ?? []).join(', '),
};
</script>

<template>
    <Head :title="faq ? 'Edit FAQ' : 'Tambah FAQ'" />

    <div class="flex flex-col gap-4 p-6">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon-sm" as-child>
                <Link :href="admin.faqs.index().url"
                    ><ArrowLeft class="size-4"
                /></Link>
            </Button>
            <h1 class="text-2xl font-semibold">
                {{ faq ? 'Edit FAQ' : 'Tambah FAQ' }}
            </h1>
        </div>

        <Form
            v-bind="action"
            :transform="(data: any) => ({
                ...data,
                is_active: !!data.is_active,
                priority: Number(data.priority || 0),
                keywords: typeof data.keywords === 'string'
                    ? data.keywords.split(',').map((k: string) => k.trim()).filter(Boolean)
                    : data.keywords,
            })"
            class="space-y-4 rounded-lg border bg-card p-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="category">Kategori</Label>
                <Input
                    id="category"
                    name="category"
                    :default-value="initial.category"
                    placeholder="contoh: Pendaftaran, Administrasi"
                    required
                />
                <InputError :message="errors.category" />
            </div>

            <div class="grid gap-2">
                <Label for="question">Pertanyaan</Label>
                <Input
                    id="question"
                    name="question"
                    :default-value="initial.question"
                    required
                />
                <InputError :message="errors.question" />
            </div>

            <div class="grid gap-2">
                <Label for="answer">Jawaban</Label>
                <textarea
                    id="answer"
                    name="answer"
                    rows="5"
                    :value="initial.answer"
                    required
                    class="rounded-md border bg-background px-3 py-2 text-sm"
                />
                <InputError :message="errors.answer" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="priority">Prioritas</Label>
                    <Input
                        id="priority"
                        name="priority"
                        type="number"
                        :default-value="initial.priority"
                        min="0"
                        max="1000"
                    />
                    <InputError :message="errors.priority" />
                </div>
                <div class="grid gap-2">
                    <Label for="keywords"
                        >Keywords <span class="text-muted-foreground"
                            >(pisah dengan koma)</span
                        ></Label
                    >
                    <Input
                        id="keywords"
                        name="keywords"
                        :default-value="initial.keywords"
                        placeholder="bpjs, asuransi, jaminan"
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
                    <Link :href="admin.faqs.index().url">Batal</Link>
                </Button>
                <Button :disabled="processing">Simpan</Button>
            </div>
        </Form>
    </div>
</template>
