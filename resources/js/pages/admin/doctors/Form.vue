<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

interface Schedule {
    day_of_week: number;
    start_time: string;
    end_time: string;
    room: string | null;
    is_active: boolean;
}
interface Doctor {
    id: number;
    name: string;
    specialization: string;
    polyclinic: string | null;
    bio: string | null;
    photo_url: string | null;
    is_active: boolean;
    leave_start_date: string | null;
    leave_end_date: string | null;
    leave_reason: string | null;
    schedules: Schedule[];
}

const props = defineProps<{
    doctor: Doctor | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.dashboard().url },
            { title: 'Dokter', href: admin.doctors.index().url },
            { title: 'Form', href: '#' },
        ],
    },
});

const DAYS = [
    { value: 0, label: 'Minggu' },
    { value: 1, label: 'Senin' },
    { value: 2, label: 'Selasa' },
    { value: 3, label: 'Rabu' },
    { value: 4, label: 'Kamis' },
    { value: 5, label: 'Jumat' },
    { value: 6, label: 'Sabtu' },
];

const form = useForm({
    name: props.doctor?.name ?? '',
    specialization: props.doctor?.specialization ?? '',
    polyclinic: props.doctor?.polyclinic ?? '',
    bio: props.doctor?.bio ?? '',
    photo_url: props.doctor?.photo_url ?? '',
    is_active: props.doctor?.is_active ?? true,
    leave_start_date: props.doctor?.leave_start_date ?? '',
    leave_end_date: props.doctor?.leave_end_date ?? '',
    leave_reason: props.doctor?.leave_reason ?? '',
    schedules: (props.doctor?.schedules ?? []).map((s) => ({
        day_of_week: s.day_of_week,
        start_time: s.start_time.slice(0, 5),
        end_time: s.end_time.slice(0, 5),
        room: s.room ?? '',
        is_active: s.is_active ?? true,
    })) as Schedule[],
});

const addSchedule = () =>
    form.schedules.push({
        day_of_week: 1,
        start_time: '08:00',
        end_time: '12:00',
        room: '',
        is_active: true,
    });

const removeSchedule = (idx: number) => form.schedules.splice(idx, 1);

const submit = () => {
    if (props.doctor) {
        form.put(`/admin/doctors/${props.doctor.id}`);
    } else {
        form.post('/admin/doctors');
    }
};
</script>

<template>
    <Head :title="doctor ? 'Edit Dokter' : 'Tambah Dokter'" />

    <form class="flex flex-col gap-4 p-6" @submit.prevent="submit">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon-sm" as-child>
                <Link :href="admin.doctors.index().url"
                    ><ArrowLeft class="size-4"
                /></Link>
            </Button>
            <h1 class="text-2xl font-semibold">
                {{ doctor ? 'Edit Dokter' : 'Tambah Dokter' }}
            </h1>
        </div>

        <div class="grid gap-4 rounded-lg border bg-card p-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name">Nama</Label>
                    <Input id="name" v-model="form.name" required />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="specialization">Spesialisasi</Label>
                    <Input
                        id="specialization"
                        v-model="form.specialization"
                        required
                    />
                    <InputError :message="form.errors.specialization" />
                </div>
                <div class="grid gap-2">
                    <Label for="polyclinic">Poliklinik</Label>
                    <Input id="polyclinic" v-model="form.polyclinic" />
                </div>
                <div class="grid gap-2">
                    <Label for="photo_url">URL Foto</Label>
                    <Input id="photo_url" v-model="form.photo_url" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="bio">Bio</Label>
                <textarea
                    id="bio"
                    v-model="form.bio"
                    rows="3"
                    class="rounded-md border bg-background px-3 py-2 text-sm"
                />
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="size-4 rounded border"
                />
                <Label for="is_active">Aktif</Label>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-6">
            <div class="mb-4">
                <h2 class="font-medium">Cuti / Tidak Praktik</h2>
                <p class="text-xs text-muted-foreground">
                    Isi periode cuti agar chatbot otomatis menjawab "dokter sedang cuti" pada tanggal tersebut.
                </p>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="leave_start_date" class="text-xs"
                        >Tanggal mulai</Label
                    >
                    <Input
                        id="leave_start_date"
                        v-model="form.leave_start_date"
                        type="date"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="leave_end_date" class="text-xs"
                        >Tanggal selesai</Label
                    >
                    <Input
                        id="leave_end_date"
                        v-model="form.leave_end_date"
                        type="date"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="leave_reason" class="text-xs">Alasan</Label>
                    <Input
                        id="leave_reason"
                        v-model="form.leave_reason"
                        placeholder="contoh: Cuti seminar"
                    />
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-6">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="font-medium">Jadwal Praktik</h2>
                    <p class="text-xs text-muted-foreground">
                        Tambahkan jadwal mingguan praktik dokter.
                    </p>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addSchedule"
                >
                    <Plus class="size-4" /> Tambah jadwal
                </Button>
            </div>

            <div class="flex flex-col gap-3">
                <div
                    v-for="(s, idx) in form.schedules"
                    :key="idx"
                    class="grid grid-cols-12 items-end gap-2"
                >
                    <div class="col-span-3">
                        <Label class="text-xs">Hari</Label>
                        <select
                            v-model.number="s.day_of_week"
                            class="h-9 w-full rounded-md border bg-background px-2 text-sm"
                        >
                            <option
                                v-for="d in DAYS"
                                :key="d.value"
                                :value="d.value"
                            >
                                {{ d.label }}
                            </option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <Label class="text-xs">Mulai</Label>
                        <Input v-model="s.start_time" type="time" />
                    </div>
                    <div class="col-span-3">
                        <Label class="text-xs">Selesai</Label>
                        <Input v-model="s.end_time" type="time" />
                    </div>
                    <div class="col-span-2">
                        <Label class="text-xs">Ruang</Label>
                        <Input v-model="s.room" />
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon-sm"
                        class="col-span-1"
                        @click="removeSchedule(idx)"
                    >
                        <Trash2 class="size-4 text-rose-600" />
                    </Button>
                </div>
                <div
                    v-if="form.schedules.length === 0"
                    class="rounded-md border border-dashed py-6 text-center text-sm text-muted-foreground"
                >
                    Belum ada jadwal.
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <Button variant="outline" as-child>
                <Link :href="admin.doctors.index().url">Batal</Link>
            </Button>
            <Button type="submit" :disabled="form.processing">Simpan</Button>
        </div>
    </form>
</template>
