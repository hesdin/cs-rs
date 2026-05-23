<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    ArrowLeft,
    Calendar,
    Check,
    HeartPulse,
    Lock,
    MapPin,
    Mail,
    MessageCircle,
    Phone,
    Play,
    ShieldCheck,
    Star,
    Sparkles,
    Stethoscope,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
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

interface Testimonial {
    name: string;
    role: string;
    message: string;
    featured?: boolean;
}

interface Insight {
    title: string;
    excerpt: string;
    date: string;
    category: string;
    image: string;
}

const props = defineProps<{
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
    testimonials: Testimonial[];
    insights: Insight[];
}>();

const values = [
    'Pelayanan Bermutu',
    'Kolaborasi Multidisipliner',
    'Transparansi Biaya',
    'Aksesibilitas Layanan',
    'Profesionalisme Klinis',
];
const activeValue = ref(0);
const setValue = (i: number) => {
    activeValue.value = Math.max(0, Math.min(values.length - 1, i));
};
const valueIndex = computed(() => `${activeValue.value + 1}/${values.length}`);
</script>

<template>
    <Head :title="`${hospitalName} — Customer Service Virtual`" />

    <div class="min-h-screen bg-white text-slate-900">
        <!-- ========== HERO BLUE BLOCK ========== -->
        <section class="relative px-3 pt-3 pb-10 sm:px-5 sm:pt-5">
            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#059669] via-[#10b981] to-[#34d399] px-6 pt-6 pb-12 text-white sm:px-10 sm:pt-8"
            >
                <!-- Decorative blobs -->
                <div
                    class="pointer-events-none absolute -top-24 -right-24 size-80 rounded-full bg-white/10 blur-3xl"
                />
                <div
                    class="pointer-events-none absolute -bottom-24 -left-24 size-80 rounded-full bg-white/10 blur-3xl"
                />

                <!-- ----- NAV ----- -->
                <header class="relative flex items-center justify-between">
                    <Link href="/" class="flex items-center gap-2">
                        <div
                            class="flex size-9 items-center justify-center rounded-xl bg-white text-[#059669] shadow-sm"
                        >
                            <HeartPulse class="size-5" stroke-width="2.5" />
                        </div>
                        <span class="text-base font-semibold tracking-tight"
                            >IbnuSinaCare</span
                        >
                    </Link>

                    <nav
                        class="hidden items-center gap-1 rounded-full bg-white/10 p-1.5 text-sm backdrop-blur lg:flex"
                    >
                        <a
                            href="#beranda"
                            class="rounded-full bg-white/15 px-4 py-1.5 font-medium"
                            >Beranda</a
                        >
                        <a
                            href="#tentang"
                            class="rounded-full px-4 py-1.5 text-white/80 transition hover:text-white"
                            >Profil RS</a
                        >
                        <a
                            href="#layanan"
                            class="rounded-full px-4 py-1.5 text-white/80 transition hover:text-white"
                            >Layanan</a
                        >
                        <a
                            href="#dokter"
                            class="rounded-full px-4 py-1.5 text-white/80 transition hover:text-white"
                            >Dokter</a
                        >
                        <a
                            href="#testimoni"
                            class="rounded-full px-4 py-1.5 text-white/80 transition hover:text-white"
                            >Testimoni</a
                        >
                        <a
                            href="#kontak"
                            class="rounded-full px-4 py-1.5 text-white/80 transition hover:text-white"
                            >Kontak</a
                        >
                    </nav>

                    <div class="flex items-center gap-2">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="hidden rounded-full px-4 py-2 text-sm text-white/90 hover:text-white sm:inline-block"
                            >Dashboard</Link
                        >
                        <Link
                            v-else
                            :href="login()"
                            class="hidden rounded-full px-4 py-2 text-sm text-white/90 hover:text-white sm:inline-block"
                            >Login Admin</Link
                        >
                        <Link
                            href="/chat"
                            class="inline-flex items-center gap-1.5 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-[#059669] shadow-sm transition hover:bg-emerald-50"
                        >
                            Tanya Asisten Virtual
                            <span
                                class="flex size-5 items-center justify-center rounded-full bg-[#059669] text-white"
                            >
                                <ArrowRight class="size-3" />
                            </span>
                        </Link>
                    </div>
                </header>

                <!-- ----- HERO CONTENT ----- -->
                <div
                    id="beranda"
                    class="relative mt-12 grid gap-8 lg:mt-16 lg:grid-cols-2 lg:items-center"
                >
                    <div class="relative">
                        <!-- Trust badge -->
                        <div
                            class="mb-6 inline-flex items-center gap-3 rounded-full bg-white/10 py-1.5 pr-4 pl-1.5 text-sm backdrop-blur"
                        >
                            <div class="flex -space-x-2">
                                <span
                                    v-for="(c, i) in ['#fca5a5', '#86efac', '#fcd34d', '#a5b4fc']"
                                    :key="i"
                                    class="flex size-7 items-center justify-center rounded-full ring-2 ring-white/40 text-[10px] font-semibold text-white"
                                    :style="{ background: c }"
                                >
                                    {{ ['H', 'A', 'S', 'M'][i] }}
                                </span>
                            </div>
                            <span class="font-medium"
                                >Telah dipercaya ribuan pasien sejak 1988</span
                            >
                        </div>

                        <h1
                            class="text-4xl leading-[1.05] font-semibold tracking-tight sm:text-5xl lg:text-6xl"
                        >
                            Sahabat Sehat
                            <br />Keluarga di Sulawesi
                            <br />Selatan
                        </h1>

                        <a
                            href="#layanan"
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-[#059669] shadow-md transition hover:shadow-lg"
                        >
                            Lihat Layanan Kami
                            <span
                                class="flex size-6 items-center justify-center rounded-full bg-[#059669] text-white"
                            >
                                <ArrowRight class="size-3.5" />
                            </span>
                        </a>

                        <!-- Bottom blurb -->
                        <div class="mt-16 max-w-md">
                            <p class="text-sm font-semibold tracking-wide">
                                Pelayanan Komprehensif &amp; Islami
                            </p>
                            <p class="mt-2 text-sm leading-relaxed text-white/80">
                                Rumah sakit umum tipe B yang memadukan
                                pelayanan medis modern dengan nilai-nilai
                                Islami. Pendaftaran online, jadwal dokter,
                                hingga konsultasi BPJS — semua bisa Anda akses
                                dengan mudah, kapan saja.
                            </p>
                        </div>
                    </div>

                    <!-- Hero image + floating stat card -->
                    <div class="relative">
                        <div
                            class="overflow-hidden rounded-3xl ring-1 ring-white/20"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=900&auto=format&fit=crop&q=70"
                                alt="Tim medis profesional"
                                class="h-[420px] w-full object-cover lg:h-[480px]"
                                loading="lazy"
                            />
                        </div>

                        <!-- Floating satisfaction card -->
                        <div
                            class="absolute -bottom-6 left-4 right-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:left-auto lg:right-4 lg:w-[420px]"
                        >
                            <div
                                class="rounded-2xl bg-white/15 p-4 backdrop-blur-md ring-1 ring-white/20"
                            >
                                <p class="text-xs text-white/70">
                                    Indeks Kepuasan Pasien
                                </p>
                                <p class="mt-1 text-3xl font-semibold">97%</p>
                                <p class="mt-1 text-[11px] text-white/70 leading-snug">
                                    Berdasarkan survei rutin
                                    <br />kepuasan pasien rawat jalan
                                </p>
                            </div>
                            <div
                                class="space-y-2 rounded-2xl bg-white/15 p-3 backdrop-blur-md ring-1 ring-white/20"
                            >
                                <span
                                    class="flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-xs"
                                >
                                    <Check class="size-3" /> Ramah
                                </span>
                                <span
                                    class="flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-medium text-[#059669]"
                                >
                                    <Check class="size-3" /> Islami
                                </span>
                                <span
                                    class="flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-xs"
                                >
                                    <Check class="size-3" /> Tepercaya
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== ABOUT US ========== -->
        <section id="tentang" class="px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                    <div>
                        <span
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-[#059669]"
                        >
                            <Sparkles class="size-3.5" /> Profil Rumah Sakit
                        </span>
                    </div>
                    <div class="flex items-start gap-5">
                        <div
                            class="size-16 flex-shrink-0 overflow-hidden rounded-full ring-4 ring-emerald-100"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=200&auto=format&fit=crop&q=70"
                                alt=""
                                class="h-full w-full object-cover"
                                loading="lazy"
                            />
                        </div>
                        <p class="text-lg leading-relaxed text-slate-700">
                            {{ hospitalName }} adalah rumah sakit umum tipe B
                            di bawah Yayasan Wakaf UMI yang melayani masyarakat
                            Sulawesi Selatan dengan pelayanan medis paripurna,
                            ramah, dan bernuansa Islami — dari rawat jalan,
                            rawat inap, hingga gawat darurat 24 jam.
                        </p>
                    </div>
                </div>

                <div
                    class="mt-12 grid items-end gap-8 lg:grid-cols-3 lg:gap-10"
                >
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="flex size-10 items-center justify-center rounded-full border border-slate-300 text-slate-600 transition hover:border-[#059669] hover:text-[#059669]"
                            @click="setValue(activeValue - 1)"
                        >
                            <ArrowLeft class="size-4" />
                        </button>
                        <button
                            type="button"
                            class="flex size-10 items-center justify-center rounded-full bg-[#059669] text-white shadow-md transition hover:bg-[#047857]"
                            @click="setValue(activeValue + 1)"
                        >
                            <ArrowRight class="size-4" />
                        </button>
                        <span class="ml-2 text-sm text-slate-500">{{
                            valueIndex
                        }}</span>
                    </div>

                    <div>
                        <p
                            class="text-5xl font-semibold tracking-tight text-[#059669]"
                        >
                            {{ stats.doctors * 8 }}+
                        </p>
                        <p class="mt-2 text-sm text-slate-600">
                            Tenaga medis &amp; staf profesional
                            <br />siap melayani 24 jam di Makassar
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5"
                        >
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-white text-[#059669] shadow-sm"
                            >
                                <HeartPulse class="size-5" />
                            </div>
                            <span
                                class="mt-3 inline-block rounded-full bg-white px-2.5 py-0.5 text-[10px] font-medium text-[#059669]"
                                >Akreditasi Paripurna</span
                            >
                            <p class="mt-2 font-semibold">Mutu Terjamin</p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                                Terakreditasi paripurna oleh KARS dan menjadi
                                faskes rujukan tipe B BPJS Kesehatan.
                            </p>
                        </div>
                        <div
                            class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5"
                        >
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-white text-[#059669] shadow-sm"
                            >
                                <Lock class="size-5" />
                            </div>
                            <span
                                class="mt-3 inline-block rounded-full bg-white px-2.5 py-0.5 text-[10px] font-medium text-[#059669]"
                                >Rekam Medis Aman</span
                            >
                            <p class="mt-2 font-semibold">Data Rahasia</p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                                Rekam medis pasien dijaga sesuai standar
                                privasi dan kerahasiaan dokter–pasien.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== WHY CHOOSE US ========== -->
        <section id="layanan" class="px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12">
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-[#059669]"
                    >
                        <Sparkles class="size-3.5" /> Mengapa RS Ibnu Sina
                    </span>
                    <div
                        class="mt-3 grid items-end gap-6 lg:grid-cols-2"
                    >
                        <h2
                            class="text-3xl leading-tight font-semibold tracking-tight md:text-4xl"
                        >
                            Pelayanan Paripurna untuk
                            <br />Setiap Pasien &amp; Keluarga
                        </h2>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Kami melayani dengan sepenuh hati — memadukan
                            keahlian dokter spesialis, fasilitas medis modern,
                            dan nilai-nilai Islami untuk kenyamanan dan
                            kesembuhan Anda.
                        </p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-3 lg:items-stretch">
                    <!-- Values list -->
                    <div
                        class="rounded-3xl border border-slate-200 bg-white p-6"
                    >
                        <div class="mb-5 flex items-center justify-between text-xs">
                            <span class="font-medium text-slate-500"
                                >Nilai Pelayanan</span
                            >
                            <span class="text-slate-400">{{ valueIndex }}</span>
                        </div>
                        <ul class="space-y-1">
                            <li
                                v-for="(v, i) in values"
                                :key="v"
                                class="flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm transition"
                                :class="
                                    i === activeValue
                                        ? 'bg-[#059669] text-white'
                                        : 'text-slate-700 hover:bg-slate-50'
                                "
                                @click="activeValue = i"
                            >
                                <span class="font-medium">{{ v }}</span>
                                <ArrowRight
                                    class="size-4"
                                    :class="
                                        i === activeValue
                                            ? 'text-white'
                                            : 'text-slate-300'
                                    "
                                />
                            </li>
                        </ul>
                        <Link
                            href="/chat"
                            class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Daftar Berobat
                            <span
                                class="flex size-5 items-center justify-center rounded-full bg-white text-slate-900"
                            >
                                <ArrowRight class="size-3" />
                            </span>
                        </Link>
                    </div>

                    <!-- Image card -->
                    <div
                        class="overflow-hidden rounded-3xl ring-1 ring-slate-200"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1581056771107-24ca5f033842?w=800&auto=format&fit=crop&q=70"
                            alt="Dokter sedang berkonsultasi dengan pasien"
                            class="h-full max-h-[460px] w-full object-cover"
                            loading="lazy"
                        />
                    </div>

                    <!-- Blue commitment card -->
                    <div
                        class="flex flex-col justify-between rounded-3xl bg-gradient-to-br from-[#059669] to-[#0d9488] p-7 text-white"
                    >
                        <div>
                            <p class="text-xs font-medium text-white/70">
                                Komitmen Kami
                            </p>
                            <h3
                                class="mt-3 text-2xl leading-snug font-semibold"
                            >
                                Memberikan pelayanan
                                kesehatan paripurna
                                dengan empati &amp; akhlak
                                Islami untuk setiap pasien.
                            </h3>
                        </div>
                        <div class="mt-8 flex flex-wrap gap-2">
                            <span
                                v-for="v in values"
                                :key="v"
                                class="rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur"
                            >
                                {{ v }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== TOTAL CARE MODEL ========== -->
        <section class="bg-emerald-50/60 px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-6xl text-center">
                <span
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-[#059669]"
                >
                    <Sparkles class="size-3.5" /> Pendekatan Pelayanan
                </span>
                <h2
                    class="mt-3 text-3xl leading-tight font-semibold tracking-tight md:text-4xl"
                >
                    Pelayanan Terpadu RS Ibnu Sina
                </h2>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600"
                >
                    Mulai dari pendaftaran, pemeriksaan, hingga rawat inap —
                    semua proses dirancang agar pasien dan keluarga merasa
                    nyaman, aman, dan dilayani sepenuh hati.
                </p>

                <div
                    class="relative mt-10 overflow-hidden rounded-3xl ring-1 ring-emerald-100"
                >
                    <img
                        src="https://images.unsplash.com/photo-1559757175-5700dde675bc?w=1400&auto=format&fit=crop&q=70"
                        alt="Tim dokter sedang melakukan visite pasien"
                        class="h-[360px] w-full object-cover sm:h-[480px]"
                        loading="lazy"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-[#059669]/80 via-[#059669]/20 to-transparent"
                    />
                    <button
                        type="button"
                        class="absolute top-1/2 left-1/2 flex size-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-[#059669] backdrop-blur transition hover:bg-white"
                    >
                        <Play class="size-6 fill-current" />
                    </button>
                    <p
                        class="absolute right-6 bottom-6 left-6 text-left text-sm text-white sm:max-w-xl"
                    >
                        Dokter umum, spesialis, perawat, hingga apoteker
                        bekerja sama dalam satu rangkaian pelayanan. Setiap
                        pasien didampingi dari awal kunjungan hingga pemulihan,
                        dengan informasi yang transparan dan ramah.
                    </p>
                </div>
            </div>
        </section>

        <!-- ========== DOCTORS ========== -->
        <section id="dokter" class="px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-10 text-center">
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-[#059669]"
                    >
                        <Stethoscope class="size-3.5" /> Tim Dokter
                    </span>
                    <h2
                        class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl"
                    >
                        Dokter Spesialis Kami
                    </h2>
                    <p class="mt-3 text-sm text-slate-600">
                        {{ stats.doctors }} dokter dengan
                        {{ stats.specializations }} bidang spesialisasi siap
                        melayani Anda
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="d in doctors"
                        :key="d.id"
                        class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-100/50"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-12 items-center justify-center rounded-full bg-emerald-100 text-[#059669] font-semibold"
                            >
                                {{ d.name.charAt(0) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold" :title="d.name">
                                    {{ d.name }}
                                </p>
                                <p
                                    class="text-xs font-medium text-[#059669]"
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
                                class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-800"
                                >Cuti</span
                            >
                        </div>
                        <div
                            v-if="d.schedule_summary"
                            class="mt-4 flex items-start gap-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600"
                        >
                            <Calendar class="mt-0.5 size-3.5 flex-shrink-0" />
                            <span>{{ d.schedule_summary }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap justify-center gap-2">
                    <span
                        v-for="spec in specializations"
                        :key="spec"
                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-1.5 text-xs text-slate-700 transition hover:bg-emerald-100"
                    >
                        <Stethoscope class="size-3 text-[#059669]" />
                        {{ spec }}
                    </span>
                </div>
            </div>
        </section>

        <!-- ========== TESTIMONIALS ========== -->
        <section id="testimoni" class="bg-slate-50 px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 text-center">
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-[#059669]"
                    >
                        <Star class="size-3.5" /> Testimoni Pasien
                    </span>
                    <h2
                        class="mt-3 text-3xl leading-tight font-semibold tracking-tight md:text-4xl"
                    >
                        Cerita Pasien &amp; Keluarga
                        <br />yang Pernah Berobat di Sini
                    </h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm text-slate-600">
                        Pengalaman nyata dari pasien dan keluarga yang
                        mempercayakan kesehatan mereka kepada RS Ibnu Sina.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div
                        v-for="(t, i) in testimonials"
                        :key="i"
                        class="rounded-2xl p-6 transition"
                        :class="
                            t.featured
                                ? 'bg-gradient-to-br from-[#059669] to-[#0d9488] text-white shadow-lg shadow-emerald-200'
                                : 'border border-slate-200 bg-white'
                        "
                    >
                        <p
                            class="font-semibold"
                            :class="t.featured ? 'text-white' : 'text-slate-900'"
                        >
                            {{ i === 0 ? 'Perawat Ramah, Dokter Sabar' : i === 1 ? 'Daftar Online Praktis' : 'Pelayanan Kebidanan Memuaskan' }}
                        </p>
                        <p
                            class="mt-3 text-sm leading-relaxed"
                            :class="t.featured ? 'text-white/90' : 'text-slate-600'"
                        >
                            "{{ t.message }}"
                        </p>
                        <div
                            class="mt-6 flex items-center gap-3 border-t pt-4"
                            :class="
                                t.featured
                                    ? 'border-white/20'
                                    : 'border-slate-100'
                            "
                        >
                            <div
                                class="flex size-9 items-center justify-center rounded-full font-semibold"
                                :class="
                                    t.featured
                                        ? 'bg-white/20 text-white'
                                        : 'bg-emerald-100 text-[#059669]'
                                "
                            >
                                {{ t.name.charAt(0) }}
                            </div>
                            <div class="text-xs">
                                <p
                                    class="font-medium"
                                    :class="t.featured ? 'text-white' : ''"
                                >
                                    {{ t.name }}
                                </p>
                                <p
                                    :class="
                                        t.featured
                                            ? 'text-white/70'
                                            : 'text-slate-500'
                                    "
                                >
                                    {{ t.role }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== INSIGHTS / BLOG ========== -->
        <section class="px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-10">
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-[#059669]"
                    >
                        <Sparkles class="size-3.5" /> Edukasi Kesehatan
                    </span>
                    <div class="mt-3 grid gap-4 lg:grid-cols-2 lg:items-end">
                        <h2
                            class="text-3xl leading-tight font-semibold tracking-tight md:text-4xl"
                        >
                            Informasi Kesehatan untuk
                            <br />Anda &amp; Keluarga
                        </h2>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Panduan kesehatan, tips pencegahan penyakit, dan
                            edukasi medis dari tim dokter RS Ibnu Sina untuk
                            membantu Anda hidup lebih sehat.
                        </p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <article
                        v-for="(post, i) in insights"
                        :key="i"
                        class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:shadow-lg"
                    >
                        <div class="overflow-hidden rounded-xl">
                            <img
                                :src="post.image"
                                :alt="post.title"
                                class="h-44 w-full object-cover transition duration-500 group-hover:scale-105"
                                loading="lazy"
                            />
                        </div>
                        <div class="px-2 pt-4 pb-2">
                            <span
                                class="text-[10px] font-medium tracking-wide text-[#059669] uppercase"
                                >{{ post.category }}</span
                            >
                            <h3
                                class="mt-2 line-clamp-2 font-semibold leading-snug"
                            >
                                {{ post.title }}
                            </h3>
                            <p
                                class="mt-2 line-clamp-2 text-sm text-slate-600"
                            >
                                {{ post.excerpt }}
                            </p>
                            <div
                                class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3"
                            >
                                <a
                                    href="#"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-[#059669]"
                                >
                                    Baca Selengkapnya
                                    <span
                                        class="flex size-5 items-center justify-center rounded-full bg-[#059669] text-white transition group-hover:translate-x-0.5"
                                    >
                                        <ArrowRight class="size-3" />
                                    </span>
                                </a>
                                <span class="text-xs text-slate-400">{{
                                    post.date
                                }}</span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- ========== DARK FOOTER ========== -->
        <footer
            id="kontak"
            class="bg-gradient-to-br from-[#064e3b] to-[#115e59] px-4 pt-16 pb-10 text-white sm:px-6"
        >
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-12 lg:grid-cols-[1.2fr_1fr_1fr_1fr]">
                    <!-- Brand & subscribe -->
                    <div>
                        <h3
                            class="text-3xl leading-tight font-semibold tracking-tight"
                        >
                            Kabar terbaru &amp;
                            <br />tips kesehatan
                        </h3>
                        <p class="mt-3 text-sm text-white/70">
                            Berlangganan untuk menerima informasi jadwal dokter,
                            promo MCU, dan tips kesehatan keluarga langsung ke
                            email Anda.
                        </p>
                        <form
                            class="mt-5 flex max-w-sm items-center gap-2 rounded-full bg-white/10 p-1.5 ring-1 ring-white/15"
                            @submit.prevent
                        >
                            <input
                                type="email"
                                placeholder="Alamat email Anda"
                                class="flex-1 bg-transparent px-3 py-2 text-sm placeholder:text-white/50 focus:outline-none"
                            />
                            <button
                                type="submit"
                                class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-[#064e3b] hover:bg-emerald-50"
                            >
                                Berlangganan
                            </button>
                        </form>
                    </div>

                    <!-- Quick links -->
                    <div>
                        <p class="mb-4 text-sm font-semibold">Tautan Cepat</p>
                        <ul class="space-y-2 text-sm text-white/70">
                            <li>
                                <a href="#beranda" class="hover:text-white"
                                    >Beranda</a
                                >
                            </li>
                            <li>
                                <a href="#tentang" class="hover:text-white"
                                    >Profil RS</a
                                >
                            </li>
                            <li>
                                <a href="#layanan" class="hover:text-white"
                                    >Layanan</a
                                >
                            </li>
                            <li>
                                <Link href="/chat" class="hover:text-white"
                                    >Tanya AI</Link
                                >
                            </li>
                            <li>
                                <a href="#kontak" class="hover:text-white"
                                    >Kontak</a
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Services -->
                    <div>
                        <p class="mb-4 text-sm font-semibold">Layanan Unggulan</p>
                        <ul class="space-y-2 text-sm text-white/70">
                            <li>IGD 24 Jam</li>
                            <li>Rawat Inap &amp; ICU</li>
                            <li>Poliklinik Spesialis</li>
                            <li>Persalinan &amp; Kebidanan</li>
                            <li>Medical Check-Up</li>
                            <li>MCU Haji &amp; Umroh</li>
                        </ul>
                    </div>

                    <!-- Doctors / contact -->
                    <div>
                        <p class="mb-4 text-sm font-semibold">Hubungi Kami</p>
                        <ul class="space-y-3 text-sm text-white/80">
                            <li class="flex items-start gap-2">
                                <MapPin
                                    class="mt-0.5 size-4 flex-shrink-0 text-white/60"
                                />
                                <span>{{ contact.address }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <Phone
                                    class="size-4 flex-shrink-0 text-white/60"
                                />
                                <span>CS {{ contact.phone }}</span>
                            </li>
                            <li
                                class="flex items-center gap-2 text-rose-200"
                            >
                                <ShieldCheck
                                    class="size-4 flex-shrink-0"
                                />
                                <span>IGD {{ contact.igd }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <Mail
                                    class="size-4 flex-shrink-0 text-white/60"
                                />
                                <span>{{ contact.email }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <MessageCircle
                                    class="size-4 flex-shrink-0 text-white/60"
                                />
                                <span>WA {{ contact.whatsapp }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div
                    class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-white/60 sm:flex-row"
                >
                    <p class="flex items-center gap-2">
                        <span
                            class="flex size-7 items-center justify-center rounded-lg bg-white/10"
                        >
                            <HeartPulse class="size-4" />
                        </span>
                        IbnuSinaCare
                    </p>
                    <p>
                        © {{ new Date().getFullYear() }} {{ hospitalName }}.
                        Hak cipta dilindungi.
                    </p>
                    <p>
                        Asisten virtual ini tidak menggantikan konsultasi
                        medis.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
