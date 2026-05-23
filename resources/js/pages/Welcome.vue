<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    CalendarClock,
    HelpCircle,
    MessageCircle,
    ShieldCheck,
    UserRound,
} from 'lucide-vue-next';
import { dashboard, login } from '@/routes';

defineProps<{
    hospitalName?: string;
}>();

const features = [
    {
        icon: HelpCircle,
        title: 'FAQ Rumah Sakit',
        desc: 'Informasi pendaftaran, BPJS, jam besuk, prosedur rawat inap, dan layanan administrasi.',
    },
    {
        icon: CalendarClock,
        title: 'Jadwal Dokter',
        desc: 'Cek jadwal praktik dokter spesialis dan poliklinik dalam hitungan detik.',
    },
    {
        icon: ShieldCheck,
        title: 'Aman & Bertanggung Jawab',
        desc: 'Tidak memberikan diagnosis atau saran obat. Pertanyaan darurat otomatis diarahkan ke IGD.',
    },
];
</script>

<template>
    <Head title="Customer Service Virtual" />

    <div
        class="min-h-screen bg-gradient-to-b from-emerald-50 via-white to-white text-slate-900 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 dark:text-slate-100"
    >
        <header
            class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5"
        >
            <div class="flex items-center gap-2">
                <div
                    class="flex size-9 items-center justify-center rounded-lg bg-emerald-600 text-white"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="size-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-semibold">
                        {{ hospitalName ?? 'RS Ibnu Sina YW-UMI Makassar' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        Asisten Customer Service
                    </p>
                </div>
            </div>

            <nav class="flex items-center gap-3 text-sm">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-md px-3 py-1.5 text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Dashboard
                </Link>
                <Link
                    v-else
                    :href="login()"
                    class="rounded-md px-3 py-1.5 text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Login Admin
                </Link>
            </nav>
        </header>

        <main class="mx-auto max-w-6xl px-6 pt-12 pb-24">
            <section class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                    >
                        <span class="size-1.5 rounded-full bg-emerald-500" />
                        Online 24 jam
                    </span>

                    <h1
                        class="mt-4 text-4xl font-semibold leading-tight tracking-tight md:text-5xl"
                    >
                        Tanya apa saja tentang
                        <span class="text-emerald-600">layanan rumah sakit</span>
                        kami.
                    </h1>

                    <p
                        class="mt-4 max-w-lg text-base text-slate-600 dark:text-slate-400"
                    >
                        Jadwal dokter, pendaftaran, BPJS, hingga prosedur rawat
                        inap — semua bisa Anda tanyakan ke asisten virtual kami
                        dalam Bahasa Indonesia.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            href="/chat"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
                        >
                            <MessageCircle class="size-4" />
                            Mulai Chat Sekarang
                        </Link>
                        <a
                            href="#fitur"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            Pelajari Lebih Lanjut
                        </a>
                    </div>

                    <div
                        class="mt-10 flex items-center gap-6 text-xs text-slate-500"
                    >
                        <div class="flex items-center gap-2">
                            <UserRound class="size-4" />
                            6+ Dokter Spesialis
                        </div>
                        <div class="flex items-center gap-2">
                            <HelpCircle class="size-4" />
                            8+ Topik FAQ
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div
                        class="absolute -inset-4 rounded-3xl bg-emerald-200/40 blur-3xl dark:bg-emerald-900/30"
                    />
                    <div
                        class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-xl dark:border-slate-800 dark:bg-slate-900"
                    >
                        <div class="flex items-center gap-2 border-b pb-3 dark:border-slate-800">
                            <div
                                class="flex size-8 items-center justify-center rounded-full bg-emerald-600 text-xs font-medium text-white"
                            >AI</div>
                            <div class="text-xs">
                                <p class="font-medium">Asisten Virtual</p>
                                <p class="text-slate-500">Online sekarang</p>
                            </div>
                        </div>
                        <div class="space-y-3 pt-4 text-sm">
                            <div
                                class="max-w-[80%] rounded-2xl bg-slate-100 px-3 py-2 dark:bg-slate-800"
                            >
                                Halo, saya asisten virtual RS Ibnu Sina Makassar. Ada
                                yang bisa saya bantu?
                            </div>
                            <div
                                class="ml-auto max-w-[80%] rounded-2xl bg-emerald-600 px-3 py-2 text-right text-white"
                            >
                                Jadwal dokter anak hari Senin?
                            </div>
                            <div
                                class="max-w-[85%] rounded-2xl bg-slate-100 px-3 py-2 dark:bg-slate-800"
                            >
                                Tentu. Untuk poli anak hari Senin:
                                <br />• dr. Bunga Sari, Sp.A — 09:00–13:00 (Ruang
                                A-2)
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="mt-24 grid gap-6 md:grid-cols-3">
                <div
                    v-for="f in features"
                    :key="f.title"
                    class="rounded-xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
                >
                    <component
                        :is="f.icon"
                        class="size-6 text-emerald-600"
                    />
                    <h3 class="mt-4 font-semibold">{{ f.title }}</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        {{ f.desc }}
                    </p>
                </div>
            </section>

            <section
                class="mt-16 rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200"
            >
                <p class="font-medium">Catatan penting</p>
                <p class="mt-1">
                    Asisten virtual ini tidak menggantikan konsultasi medis. Bot
                    tidak memberikan diagnosis, saran obat, maupun tindakan
                    medis. Untuk kondisi darurat, segera hubungi
                    <strong>IGD (0411) 452-917 ext. 119</strong> atau
                    <strong>119</strong>.
                </p>
            </section>
        </main>

        <footer
            class="border-t border-slate-200 py-6 text-center text-xs text-slate-500 dark:border-slate-800"
        >
            © {{ new Date().getFullYear() }}
            {{ hospitalName ?? 'RS Ibnu Sina YW-UMI Makassar' }} — Customer Service Virtual.
        </footer>
    </div>
</template>
