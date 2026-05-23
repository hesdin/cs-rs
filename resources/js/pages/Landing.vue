<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Award,
    BadgeCheck,
    Building2,
    Calendar,
    CheckCircle2,
    Clock,
    Heart,
    HelpCircle,
    Mail,
    MapPin,
    MessageCircle,
    Phone,
    Shield,
    ShieldCheck,
    Sparkles,
    Stethoscope,
    Users,
} from 'lucide-vue-next';
import { dashboard, login } from '@/routes';

interface DoctorSummary {
    id: number;
    name: string;
    specialization: string;
    polyclinic: string | null;
    on_leave: boolean;
    schedule_summary: string;
}

interface FeaturedFaq {
    id: number;
    category: string;
    question: string;
    answer_preview: string;
}

defineProps<{
    hospitalName: string;
    doctors: DoctorSummary[];
    specializations: string[];
    featuredFaqs: FeaturedFaq[];
    stats: {
        doctors: number;
        specializations: number;
        faqs: number;
        categories: number;
    };
    contact: {
        address: string;
        phone: string;
        igd: string;
        whatsapp: string;
        email: string;
        website: string;
    };
}>();

const services = [
    {
        icon: Stethoscope,
        title: 'Konsultasi Spesialis',
        desc: '12+ poliklinik spesialis dengan dokter berpengalaman dan jadwal fleksibel.',
    },
    {
        icon: Shield,
        title: 'IGD 24 Jam',
        desc: 'Layanan gawat darurat siap 24 jam dengan tim medis dan ambulans on-call.',
    },
    {
        icon: Heart,
        title: 'Medical Check-Up',
        desc: 'Paket MCU lengkap, termasuk khusus haji & umroh, pre-employment, dan pre-marital.',
    },
    {
        icon: Calendar,
        title: 'Pendaftaran Online',
        desc: 'Daftar via website, aplikasi mobile, atau WhatsApp. Antrean real-time.',
    },
    {
        icon: ShieldCheck,
        title: 'BPJS & Asuransi',
        desc: 'Faskes rujukan tipe B BPJS, terima 10+ asuransi swasta cashless.',
    },
    {
        icon: Building2,
        title: 'Rawat Inap Lengkap',
        desc: 'Dari kelas 3 hingga VVIP, ICU, NICU, PICU dengan fasilitas modern.',
    },
];

const advantages = [
    'Tim dokter spesialis berpengalaman',
    'Pelayanan 24/7 untuk gawat darurat',
    'Faskes rujukan tipe B BPJS',
    'Apotek 24 jam',
    'Lab & radiologi lengkap (CT, MRI, USG 4D)',
    'Asisten virtual cerdas berbahasa Indonesia',
];
</script>

<template>
    <Head :title="`${hospitalName} — Customer Service Virtual`" />

    <div class="min-h-screen bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100">
        <!-- ============ TOP BAR ============ -->
        <div
            class="border-b border-emerald-100 bg-emerald-50/60 py-2 text-xs dark:border-emerald-950 dark:bg-emerald-950/30"
        >
            <div
                class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 px-4"
            >
                <div class="flex items-center gap-4 text-emerald-900 dark:text-emerald-200">
                    <span class="inline-flex items-center gap-1.5">
                        <Phone class="size-3" /> CS {{ contact.phone }}
                    </span>
                    <span
                        class="hidden items-center gap-1.5 sm:inline-flex font-medium text-rose-700 dark:text-rose-300"
                    >
                        <Heart class="size-3" /> IGD {{ contact.igd }}
                    </span>
                </div>
                <div class="flex items-center gap-3 text-emerald-900 dark:text-emerald-200">
                    <span class="hidden items-center gap-1.5 md:inline-flex">
                        <Clock class="size-3" /> Senin–Sabtu 07.30–21.00
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse" />
                        AI Online 24/7
                    </span>
                </div>
            </div>
        </div>

        <!-- ============ NAVBAR ============ -->
        <header
            class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-950/90"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-sm"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-base font-semibold tracking-tight">
                            {{ hospitalName }}
                        </p>
                        <p class="text-xs text-slate-500">
                            Layanan Kesehatan Terpercaya · Makassar
                        </p>
                    </div>
                </div>

                <nav class="flex items-center gap-1 text-sm">
                    <a
                        href="#layanan"
                        class="hidden rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100 sm:inline-block dark:text-slate-300 dark:hover:bg-slate-800"
                        >Layanan</a
                    >
                    <a
                        href="#dokter"
                        class="hidden rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100 sm:inline-block dark:text-slate-300 dark:hover:bg-slate-800"
                        >Dokter</a
                    >
                    <a
                        href="#kontak"
                        class="hidden rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100 sm:inline-block dark:text-slate-300 dark:hover:bg-slate-800"
                        >Kontak</a
                    >
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                        >Dashboard</Link
                    >
                    <Link
                        v-else
                        :href="login()"
                        class="rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                        >Login Admin</Link
                    >
                    <Link
                        href="/chat"
                        class="ml-2 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        <MessageCircle class="size-4" />
                        <span class="hidden sm:inline">Chat Sekarang</span>
                        <span class="sm:hidden">Chat</span>
                    </Link>
                </nav>
            </div>
        </header>

        <!-- ============ HERO ============ -->
        <section class="relative overflow-hidden">
            <!-- decorative blobs -->
            <div
                class="pointer-events-none absolute -top-40 -right-40 size-[500px] rounded-full bg-emerald-200/30 blur-3xl dark:bg-emerald-900/20"
            />
            <div
                class="pointer-events-none absolute -bottom-40 -left-40 size-[500px] rounded-full bg-teal-200/30 blur-3xl dark:bg-teal-900/20"
            />

            <div
                class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 lg:grid-cols-2 lg:py-24"
            >
                <div>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-900"
                    >
                        <Sparkles class="size-3" />
                        Asisten Virtual Bertenaga AI · Online 24 Jam
                    </span>

                    <h1
                        class="mt-5 text-4xl font-semibold leading-tight tracking-tight md:text-5xl lg:text-6xl"
                    >
                        Tanya tentang
                        <span
                            class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent"
                            >layanan rumah sakit</span
                        >
                        kami, kapan saja.
                    </h1>

                    <p
                        class="mt-5 max-w-xl text-base leading-relaxed text-slate-600 md:text-lg dark:text-slate-400"
                    >
                        Jadwal dokter, pendaftaran, BPJS, biaya, hingga prosedur
                        rawat inap — semua bisa Anda tanyakan ke asisten virtual
                        kami dalam Bahasa Indonesia. Cepat, akurat, gratis.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            href="/chat"
                            class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:shadow-emerald-600/30"
                        >
                            <MessageCircle class="size-4" />
                            Mulai Chat Sekarang
                            <ArrowRight
                                class="size-4 transition group-hover:translate-x-0.5"
                            />
                        </Link>
                        <a
                            href="#layanan"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            Lihat Layanan
                        </a>
                    </div>

                    <!-- Quick stats -->
                    <div
                        class="mt-10 grid max-w-md grid-cols-4 gap-4 border-t border-slate-200 pt-6 dark:border-slate-800"
                    >
                        <div>
                            <p
                                class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"
                            >
                                {{ stats.specializations }}+
                            </p>
                            <p class="text-xs text-slate-500">Spesialis</p>
                        </div>
                        <div>
                            <p
                                class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"
                            >
                                {{ stats.doctors }}
                            </p>
                            <p class="text-xs text-slate-500">Dokter</p>
                        </div>
                        <div>
                            <p
                                class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"
                            >
                                {{ stats.faqs }}+
                            </p>
                            <p class="text-xs text-slate-500">Topik FAQ</p>
                        </div>
                        <div>
                            <p
                                class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"
                            >
                                24
                            </p>
                            <p class="text-xs text-slate-500">Jam IGD</p>
                        </div>
                    </div>
                </div>

                <!-- Chat preview card -->
                <div class="relative">
                    <div
                        class="absolute -inset-6 rounded-3xl bg-gradient-to-br from-emerald-300/40 to-teal-300/40 blur-2xl dark:from-emerald-900/30 dark:to-teal-900/30"
                    />
                    <div
                        class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
                    >
                        <div
                            class="flex items-center gap-3 border-b bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-4 dark:border-slate-800 dark:from-emerald-950/40 dark:to-teal-950/40"
                        >
                            <div
                                class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-semibold text-white"
                            >
                                AI
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Asisten Virtual
                                </p>
                                <p class="text-xs text-emerald-600">
                                    <span
                                        class="mr-1 inline-block size-1.5 rounded-full bg-emerald-500"
                                    />Online sekarang
                                </p>
                            </div>
                            <span class="text-xs text-slate-500">Demo</span>
                        </div>

                        <div class="space-y-3 px-5 py-5 text-sm">
                            <div
                                class="max-w-[85%] rounded-2xl rounded-tl-sm bg-slate-100 px-4 py-2.5 dark:bg-slate-800"
                            >
                                Halo! Saya bantu informasi jadwal dokter,
                                pendaftaran, BPJS, dan administrasi 🩺
                            </div>
                            <div
                                class="ml-auto max-w-[85%] rounded-2xl rounded-tr-sm bg-emerald-600 px-4 py-2.5 text-white"
                            >
                                Jadwal dokter anak hari Senin?
                            </div>
                            <div
                                class="max-w-[88%] rounded-2xl rounded-tl-sm bg-slate-100 px-4 py-2.5 dark:bg-slate-800"
                            >
                                <p class="font-medium mb-1">
                                    Poli Anak Senin:
                                </p>
                                <ul class="space-y-1 text-xs text-slate-600 dark:text-slate-300">
                                    <li>
                                        • dr. Bunga Citra Pratiwi, Sp.A —
                                        09:00–13:00 (A-2)
                                    </li>
                                </ul>
                            </div>
                            <div
                                class="ml-auto max-w-[85%] rounded-2xl rounded-tr-sm bg-emerald-600 px-4 py-2.5 text-white"
                            >
                                Apakah RS terima BPJS?
                            </div>
                            <div
                                class="max-w-[88%] rounded-2xl rounded-tl-sm bg-slate-100 px-4 py-2.5 text-xs dark:bg-slate-800"
                            >
                                Ya, RS Ibnu Sina adalah faskes rujukan tipe B
                                BPJS. Pastikan kartu aktif & bawa rujukan
                                faskes 1 ✅
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ ADVANTAGES ============ -->
        <section
            class="border-y border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50"
        >
            <div
                class="mx-auto grid max-w-7xl gap-3 px-4 py-8 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="adv in advantages"
                    :key="adv"
                    class="flex items-center gap-2.5 text-sm text-slate-700 dark:text-slate-300"
                >
                    <CheckCircle2
                        class="size-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400"
                    />
                    {{ adv }}
                </div>
            </div>
        </section>

        <!-- ============ SERVICES ============ -->
        <section id="layanan" class="mx-auto max-w-7xl px-4 py-20">
            <div class="mb-12 text-center">
                <span
                    class="inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                    >Layanan Kami</span
                >
                <h2
                    class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl"
                >
                    Apa yang bisa AI kami bantu?
                </h2>
                <p class="mt-3 text-slate-600 dark:text-slate-400">
                    Dari informasi jadwal hingga prosedur administrasi
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="s in services"
                    :key="s.title"
                    class="group rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-100/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-800 dark:hover:shadow-emerald-950/30"
                >
                    <div
                        class="flex size-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 text-emerald-700 transition group-hover:from-emerald-500 group-hover:to-teal-600 group-hover:text-white dark:from-emerald-950 dark:to-teal-950 dark:text-emerald-400"
                    >
                        <component :is="s.icon" class="size-6" />
                    </div>
                    <h3 class="mt-4 font-semibold text-lg">{{ s.title }}</h3>
                    <p
                        class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400"
                    >
                        {{ s.desc }}
                    </p>
                </div>
            </div>
        </section>

        <!-- ============ SPECIALIZATIONS ============ -->
        <section
            class="border-y border-slate-200 bg-gradient-to-b from-slate-50 to-white py-16 dark:border-slate-800 dark:from-slate-900 dark:to-slate-950"
        >
            <div class="mx-auto max-w-7xl px-4">
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-semibold tracking-tight md:text-3xl">
                        Spesialisasi yang Tersedia
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        {{ specializations.length }} poliklinik spesialis siap melayani
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-2">
                    <span
                        v-for="spec in specializations"
                        :key="spec"
                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm transition hover:border-emerald-400 hover:bg-emerald-50 dark:border-emerald-900 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-emerald-950/30"
                    >
                        <Stethoscope class="size-3.5 text-emerald-600" />
                        {{ spec }}
                    </span>
                </div>
            </div>
        </section>

        <!-- ============ DOCTORS ============ -->
        <section id="dokter" class="mx-auto max-w-7xl px-4 py-20">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <span
                        class="inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                        >Tim Medis Kami</span
                    >
                    <h2
                        class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl"
                    >
                        Dokter Spesialis Berpengalaman
                    </h2>
                </div>
                <Link
                    href="/chat"
                    class="hidden items-center gap-1.5 text-sm font-medium text-emerald-600 hover:text-emerald-700 sm:inline-flex"
                >
                    Tanya jadwal lengkap
                    <ArrowRight class="size-4" />
                </Link>
            </div>

            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="d in doctors"
                    :key="d.id"
                    class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-800"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex size-12 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 text-emerald-700 dark:from-emerald-950 dark:to-teal-950 dark:text-emerald-400"
                        >
                            <Users class="size-5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="truncate font-semibold"
                                :title="d.name"
                            >
                                {{ d.name }}
                            </p>
                            <p
                                class="text-xs text-emerald-600 dark:text-emerald-400 font-medium"
                            >
                                {{ d.specialization }}
                            </p>
                            <p
                                v-if="d.polyclinic"
                                class="mt-0.5 text-xs text-slate-500"
                            >
                                {{ d.polyclinic }}
                            </p>
                        </div>
                        <span
                            v-if="d.on_leave"
                            class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-800 dark:bg-amber-950 dark:text-amber-300"
                        >Cuti</span>
                    </div>
                    <div
                        v-if="d.schedule_summary"
                        class="mt-4 flex items-start gap-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600 dark:bg-slate-800/50 dark:text-slate-400"
                    >
                        <Clock class="size-3.5 mt-0.5 flex-shrink-0" />
                        <span>{{ d.schedule_summary }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ FAQ PEEK ============ -->
        <section
            class="border-y border-slate-200 bg-slate-50 py-20 dark:border-slate-800 dark:bg-slate-900/50"
        >
            <div class="mx-auto max-w-7xl px-4">
                <div class="mb-10 text-center">
                    <span
                        class="inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                        >Pertanyaan Populer</span
                    >
                    <h2
                        class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl"
                    >
                        Pertanyaan yang Sering Ditanyakan
                    </h2>
                    <p class="mt-3 text-slate-600 dark:text-slate-400">
                        Tanyakan langsung ke asisten virtual untuk jawaban
                        lengkap & terkini.
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div
                        v-for="faq in featuredFaqs"
                        :key="faq.id"
                        class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-9 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400"
                            >
                                <HelpCircle class="size-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <span
                                    class="text-[10px] font-medium uppercase tracking-wide text-emerald-600 dark:text-emerald-400"
                                    >{{ faq.category }}</span
                                >
                                <p class="mt-1 font-medium leading-snug">
                                    {{ faq.question }}
                                </p>
                                <p
                                    class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400"
                                >
                                    {{ faq.answer_preview }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 text-center">
                    <Link
                        href="/chat"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-700"
                    >
                        <MessageCircle class="size-4" />
                        Tanya Pertanyaan Anda Sendiri
                    </Link>
                </div>
            </div>
        </section>

        <!-- ============ CONTACT ============ -->
        <section id="kontak" class="mx-auto max-w-7xl px-4 py-20">
            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div>
                    <span
                        class="inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                        >Hubungi Kami</span
                    >
                    <h2
                        class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl"
                    >
                        Selalu siap melayani, 24 jam.
                    </h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">
                        Untuk pertanyaan cepat, gunakan asisten virtual kami.
                        Untuk kondisi darurat, langsung hubungi IGD.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400"
                            >
                                <MapPin class="size-5" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Alamat</p>
                                <p class="text-sm font-medium">
                                    {{ contact.address }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400"
                            >
                                <Phone class="size-5" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">
                                    Customer Service
                                </p>
                                <p class="text-sm font-medium">
                                    {{ contact.phone }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 flex-shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400"
                            >
                                <Heart class="size-5" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">
                                    IGD 24 Jam
                                </p>
                                <p class="text-sm font-medium text-rose-700 dark:text-rose-300">
                                    {{ contact.igd }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400"
                            >
                                <Mail class="size-5" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Email</p>
                                <p class="text-sm font-medium">
                                    {{ contact.email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 p-8 text-white shadow-xl"
                >
                    <div
                        class="pointer-events-none absolute -top-20 -right-20 size-60 rounded-full bg-white/10 blur-2xl"
                    />
                    <div
                        class="pointer-events-none absolute -bottom-20 -left-20 size-60 rounded-full bg-white/10 blur-2xl"
                    />
                    <div class="relative">
                        <Award class="size-10" />
                        <h3 class="mt-4 text-2xl font-semibold leading-snug">
                            Perlu jawaban cepat?
                        </h3>
                        <p class="mt-2 text-emerald-50">
                            Tanya asisten virtual kami. Tersedia 24 jam, gratis,
                            dalam Bahasa Indonesia.
                        </p>
                        <ul class="mt-5 space-y-2 text-sm text-emerald-50">
                            <li class="flex items-center gap-2">
                                <BadgeCheck class="size-4" /> Jawaban
                                berdasarkan data resmi RS
                            </li>
                            <li class="flex items-center gap-2">
                                <BadgeCheck class="size-4" /> Otomatis terhubung
                                ke petugas jika diperlukan
                            </li>
                            <li class="flex items-center gap-2">
                                <BadgeCheck class="size-4" /> Tidak menggantikan
                                konsultasi medis
                            </li>
                        </ul>
                        <Link
                            href="/chat"
                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-emerald-700 shadow-lg transition hover:bg-emerald-50"
                        >
                            <MessageCircle class="size-4" />
                            Mulai Chat Sekarang
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <div
                class="mt-12 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200"
            >
                <p class="flex items-start gap-2 font-medium">
                    <Shield class="size-5 flex-shrink-0" />
                    Catatan penting
                </p>
                <p class="mt-2 leading-relaxed">
                    Asisten virtual ini tidak menggantikan konsultasi medis. Bot
                    tidak memberikan diagnosis, saran obat, maupun tindakan
                    medis. Untuk kondisi darurat, segera hubungi
                    <strong>IGD {{ contact.igd }}</strong> atau
                    <strong>119</strong>.
                </p>
            </div>
        </section>

        <!-- ============ FOOTER ============ -->
        <footer
            class="border-t border-slate-200 bg-slate-50 py-10 text-sm dark:border-slate-800 dark:bg-slate-900/50"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 text-slate-600 dark:text-slate-400 md:flex-row"
            >
                <p>
                    © {{ new Date().getFullYear() }} {{ hospitalName }}. Semua
                    hak dilindungi.
                </p>
                <div class="flex items-center gap-4 text-xs">
                    <span>{{ contact.website }}</span>
                </div>
            </div>
        </footer>
    </div>
</template>
