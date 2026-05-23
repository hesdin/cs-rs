<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import DoctorController from '@/actions/App/Http/Controllers/Admin/DoctorController';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import admin from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'Dokter', href: admin.doctors.index().url },
        ],
    },
});

interface Schedule {
    id: number;
    day_of_week: number;
    start_time: string;
    end_time: string;
    room: string | null;
}
interface Doctor {
    id: number;
    name: string;
    specialization: string;
    polyclinic: string | null;
    is_active: boolean;
    schedules: Schedule[];
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    doctors: Paginated<Doctor>;
    filters: { q: string };
}>();

const search = ref(props.filters.q ?? '');
const DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

const submitSearch = () => {
    router.get(
        admin.doctors.index().url,
        { q: search.value },
        { preserveState: true, replace: true },
    );
};

const remove = (doctor: Doctor) => {
    if (!confirm(`Hapus dokter ${doctor.name}?`)) return;
    router.delete(DoctorController.destroy.url({ doctor: doctor.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Dokter" />

    <div class="flex flex-col gap-4 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Dokter & Jadwal</h1>
                <p class="text-sm text-muted-foreground">
                    Data ini menjadi sumber jadwal yang dijawab chatbot.
                </p>
            </div>
            <Button as-child>
                <Link :href="admin.doctors.create().url">
                    <Plus class="size-4" /> Tambah Dokter
                </Link>
            </Button>
        </div>

        <form class="flex gap-2" @submit.prevent="submitSearch">
            <Input
                v-model="search"
                placeholder="Nama, spesialisasi, poli..."
                class="max-w-sm"
            />
            <Button variant="outline" type="submit">Cari</Button>
        </form>

        <Card>
            <CardContent class="p-0">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">Dokter</th>
                            <th class="px-4 py-3 font-medium">Spesialisasi</th>
                            <th class="px-4 py-3 font-medium">Jadwal</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="d in doctors.data"
                            :key="d.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ d.name }}</p>
                                <p
                                    v-if="d.polyclinic"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ d.polyclinic }}
                                </p>
                            </td>
                            <td class="px-4 py-3">{{ d.specialization }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="s in d.schedules"
                                        :key="s.id"
                                        class="rounded bg-slate-100 px-2 py-0.5 text-xs dark:bg-slate-800"
                                    >
                                        {{ DAYS[s.day_of_week] }}
                                        {{ s.start_time.slice(0, 5) }}–{{
                                            s.end_time.slice(0, 5)
                                        }}
                                    </span>
                                    <span
                                        v-if="d.schedules.length === 0"
                                        class="text-xs text-muted-foreground"
                                    >Belum ada jadwal</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs"
                                    :class="
                                        d.is_active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-slate-100 text-slate-500'
                                    "
                                >
                                    {{ d.is_active ? 'Aktif' : 'Nonaktif' }}
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
                                                admin.doctors.edit({
                                                    doctor: d.id,
                                                }).url
                                            "
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        @click="remove(d)"
                                    >
                                        <Trash2 class="size-4 text-rose-600" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="doctors.data.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Belum ada dokter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
