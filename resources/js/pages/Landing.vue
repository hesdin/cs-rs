<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    ArrowUp,
    Calendar,
    Check,
    HeartPulse,
    MapPin,
    Mail,
    Menu,
    MessageCircle,
    Phone,
    Play,
    ShieldCheck,
    Sparkles,
    Star,
    Stethoscope,
    X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
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
    rating: number;
    featured?: boolean;
}

interface Insight {
    title: string;
    excerpt: string;
    date: string;
    category: string;
    image: string;
    reading_time: number;
    author: string;
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
    testimonials: Testimonial[];
    insights: Insight[];
}>();

// ========== Why Choose Us values ==========
interface ValueItem {
    title: string;
    description: string;
}

const values: ValueItem[] = [
    {
        title: 'Pelayanan Bermutu',
        description:
            'Standar pelayanan paripurna terakreditasi KARS dengan protokol medis terkini.',
    },
    {
        title: 'Kolaborasi Multidisipliner',
        description:
            'Dokter umum, spesialis, perawat, dan apoteker bekerja terpadu untuk hasil terbaik.',
    },
    {
        title: 'Transparansi Biaya',
        description:
            'Estimasi biaya jelas sebelum tindakan. Tidak ada biaya tersembunyi.',
    },
    {
        title: 'Aksesibilitas Layanan',
        description:
            'Daftar online via website, aplikasi, atau WhatsApp. IGD buka 24 jam.',
    },
    {
        title: 'Profesionalisme Klinis',
        description:
            'Tim medis berpengalaman dengan pelatihan berkelanjutan dan akhlak Islami.',
    },
];
const activeValue = ref(0);
const setValue = (i: number) => {
    activeValue.value = (i + values.length) % values.length;
};
const valueIndex = computed(() => `${activeValue.value + 1}/${values.length}`);

// ========== Hero slider ==========
interface Slide {
    eyebrow: string;
    title: string[];
    desc: string;
    cta: { label: string; href: string };
    image: string;
    imageAlt: string;
    badge: { color: string; label: string };
}

const slides: Slide[] = [
    {
        eyebrow: 'Telah dipercaya ribuan pasien sejak 1988',
        title: ['Sahabat Sehat', 'Keluarga di Sulawesi', 'Selatan'],
        desc: 'Rumah sakit umum tipe B yang memadukan pelayanan medis modern dengan nilai-nilai Islami. Pendaftaran online, jadwal dokter, hingga konsultasi BPJS — semua bisa Anda akses dengan mudah, kapan saja.',
        cta: { label: 'Lihat Layanan Kami', href: '#layanan' },
        image: 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=900&auto=format&fit=crop&q=70',
        imageAlt: 'Tim medis profesional RS Ibnu Sina',
        badge: { color: 'bg-emerald-500', label: 'Akreditasi Paripurna KARS' },
    },
    {
        eyebrow: 'Siap Sedia 24 Jam · 7 Hari',
        title: ['IGD Buka', '24 Jam, Langsung', 'Tangani Anda'],
        desc: 'Tim dokter, perawat, dan ambulans siap melayani kondisi gawat darurat kapan saja. Pasien BPJS dapat langsung masuk IGD tanpa rujukan faskes 1.',
        cta: { label: 'Hubungi IGD Sekarang', href: '#kontak' },
        image: 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=900&auto=format&fit=crop&q=70',
        imageAlt: 'Tim IGD rumah sakit',
        badge: { color: 'bg-rose-500', label: 'IGD 24 Jam' },
    },
    {
        eyebrow: 'Asisten Virtual Berbasis AI',
        title: ['Tanya Apa Saja', 'tentang Layanan', 'RS Ibnu Sina'],
        desc: 'Jadwal dokter, biaya, BPJS, prosedur rawat inap — tanyakan langsung ke asisten virtual kami dalam Bahasa Indonesia. Cepat, gratis, tersedia 24 jam.',
        cta: { label: 'Mulai Chat Sekarang', href: '/chat' },
        image: 'https://images.unsplash.com/photo-1581595220892-b0739db3ba8c?w=900&auto=format&fit=crop&q=70',
        imageAlt: 'Pasien menggunakan asisten virtual',
        badge: { color: 'bg-amber-400', label: 'Online 24 Jam' },
    },
];

const currentSlide = ref(0);
const slideDirection = ref<'left' | 'right'>('right');
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

const goToSlide = (idx: number, direction: 'left' | 'right' = 'right') => {
    slideDirection.value = direction;
    currentSlide.value = (idx + slides.length) % slides.length;
};
const nextSlide = () => goToSlide(currentSlide.value + 1, 'right');
const prevSlide = () => goToSlide(currentSlide.value - 1, 'left');

const startAutoplay = () => {
    stopAutoplay();
    autoplayTimer = setInterval(nextSlide, 6000);
};
const stopAutoplay = () => {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
};

// ========== Mobile menu & scroll state ==========
const mobileMenuOpen = ref(false);
const scrolled = ref(false);
const showScrollTop = ref(false);

const onScroll = () => {
    scrolled.value = window.scrollY > 80;
    showScrollTop.value = window.scrollY > 600;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

// ========== Lifecycle ==========
onMounted(() => {
    startAutoplay();
    window.addEventListener('scroll', onScroll, { passive: true });
});
onBeforeUnmount(() => {
    stopAutoplay();
    window.removeEventListener('scroll', onScroll);
});
</script>

<template>
    <Head :title="`${hospitalName} — Customer Service Virtual`" />

    <div class="min-h-screen bg-white text-slate-900">
        <!-- ========== HERO BLOCK ========== -->
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
                            class="hidden items-center gap-1.5 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-[#059669] shadow-sm transition hover:bg-emerald-50 sm:inline-flex"
                        >
                            Tanya AI
                            <span
                                class="flex size-5 items-center justify-center rounded-full bg-[#059669] text-white"
                            >
                                <ArrowRight class="size-3" />
                            </span>
                        </Link>

                        <!-- Mobile menu trigger -->
                        <button
                            type="button"
                            class="flex size-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25 lg:hidden"
                            :aria-label="
                                mobileMenuOpen
                                    ? 'Tutup menu'
                                    : 'Buka menu'
                            "
                            @click="mobileMenuOpen = !mobileMenuOpen"
                        >
                            <Menu v-if="!mobileMenuOpen" class="size-5" />
                            <X v-else class="size-5" />
                        </button>
                    </div>
                </header>

                <!-- Mobile drawer -->
                <Transition
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-active-class="transition duration-200 ease-out"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div
                        v-if="mobileMenuOpen"
                        class="absolute inset-x-6 top-20 z-50 rounded-2xl bg-white p-4 shadow-2xl ring-1 ring-black/5 lg:hidden"
                    >
                        <ul class="flex flex-col text-sm text-slate-700">
                            <li
                                v-for="item in [
                                    { href: '#beranda', label: 'Beranda' },
                                    { href: '#tentang', label: 'Profil RS' },
                                    { href: '#layanan', label: 'Layanan' },
                                    { href: '#dokter', label: 'Dokter' },
                                    { href: '#testimoni', label: 'Testimoni' },
                                    { href: '#kontak', label: 'Kontak' },
                                ]"
                                :key="item.href"
                            >
                                <a
                                    :href="item.href"
                                    class="block rounded-lg px-4 py-2.5 transition hover:bg-emerald-50 hover:text-[#059669]"
                                    @click="closeMobileMenu"
                                    >{{ item.label }}</a
                                >
                            </li>
                            <li class="mt-2 border-t pt-2">
                                <Link
                                    href="/chat"
                                    class="flex items-center justify-between rounded-lg bg-emerald-600 px-4 py-2.5 font-semibold text-white"
                                    @click="closeMobileMenu"
                                >
                                    Tanya Asisten AI
                                    <ArrowRight class="size-4" />
                                </Link>
                            </li>
                        </ul>
                    </div>
                </Transition>

                <!-- ----- HERO SLIDER ----- -->
                <div
                    id="beranda"
                    class="relative mt-12 lg:mt-16"
                    @mouseenter="stopAutoplay"
                    @mouseleave="startAutoplay"
                >
                    <div
                        v-for="(slide, idx) in slides"
                        :key="idx"
                        :aria-hidden="idx !== currentSlide"
                        class="grid gap-8 transition-all duration-700 ease-out lg:grid-cols-2 lg:items-center"
                        :class="
                            idx === currentSlide
                                ? 'opacity-100 translate-x-0'
                                : 'pointer-events-none absolute inset-0 opacity-0 ' +
                                  (slideDirection === 'right'
                                      ? '-translate-x-8'
                                      : 'translate-x-8')
                        "
                    >
                        <div class="relative">
                            <div
                                class="mb-6 inline-flex items-center gap-3 rounded-full bg-white/10 py-1.5 pr-4 pl-1.5 text-sm backdrop-blur"
                            >
                                <div class="flex -space-x-2">
                                    <span
                                        v-for="(c, i) in [
                                            '#fca5a5',
                                            '#86efac',
                                            '#fcd34d',
                                            '#a5b4fc',
                                        ]"
                                        :key="i"
                                        class="flex size-7 items-center justify-center rounded-full text-[10px] font-semibold text-white ring-2 ring-white/40"
                                        :style="{ background: c }"
                                    >
                                        {{ ['H', 'A', 'S', 'M'][i] }}
                                    </span>
                                </div>
                                <span class="font-medium">{{
                                    slide.eyebrow
                                }}</span>
                            </div>

                            <h1
                                class="text-4xl leading-[1.05] font-semibold tracking-tight sm:text-5xl lg:text-6xl"
                            >
                                <template
                                    v-for="(line, li) in slide.title"
                                    :key="li"
                                >
                                    {{ line
                                    }}<br v-if="li < slide.title.length - 1"
                                /></template>
                            </h1>

                            <component
                                :is="
                                    slide.cta.href.startsWith('/')
                                        ? Link
                                        : 'a'
                                "
                                :href="slide.cta.href"
                                class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-[#059669] shadow-md transition hover:shadow-lg"
                            >
                                {{ slide.cta.label }}
                                <span
                                    class="flex size-6 items-center justify-center rounded-full bg-[#059669] text-white"
                                >
                                    <ArrowRight class="size-3.5" />
                                </span>
                            </component>

                            <div class="mt-16 max-w-md">
                                <p
                                    class="text-sm font-semibold tracking-wide"
                                >
                                    Pelayanan Komprehensif &amp; Islami
                                </p>
                                <p
                                    class="mt-2 text-sm leading-relaxed text-white/80"
                                >
                                    {{ slide.desc }}
                                </p>
                            </div>
                        </div>

                        <div class="relative">
                            <div
                                class="overflow-hidden rounded-3xl ring-1 ring-white/20"
                            >
                                <img
                                    :src="slide.image"
                                    :alt="slide.imageAlt"
                                    class="h-[420px] w-full object-cover lg:h-[480px]"
                                    loading="lazy"
                                />
                            </div>
                            <div
                                class="absolute top-4 left-4 inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-md backdrop-blur"
                            >
                                <span
                                    class="size-2 rounded-full"
                                    :class="slide.badge.color"
                                />
                                {{ slide.badge.label }}
                            </div>
                        </div>
                    </div>

                    <!-- Floating cards (persistent) -->
                    <div
                        class="absolute right-4 -bottom-6 left-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:right-4 lg:left-auto lg:w-[420px]"
                    >
                        <div
                            class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20 backdrop-blur-md"
                        >
                            <p class="text-xs text-white/70">
                                Indeks Kepuasan Pasien
                            </p>
                            <p class="mt-1 text-3xl font-semibold">97%</p>
                            <p
                                class="mt-1 text-[11px] leading-snug text-white/70"
                            >
                                Berdasarkan survei rutin
                                <br />pasien rawat jalan
                            </p>
                        </div>
                        <div
                            class="space-y-2 rounded-2xl bg-white/15 p-3 ring-1 ring-white/20 backdrop-blur-md"
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

                <!-- Slider controls -->
                <div
                    class="relative mt-10 flex items-center justify-between"
                >
                    <div class="flex items-center gap-2">
                        <button
                            v-for="(s, i) in slides"
                            :key="i"
                            type="button"
                            :aria-label="`Slide ${i + 1}`"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="
                                i === currentSlide
                                    ? 'w-8 bg-white'
                                    : 'w-4 bg-white/30 hover:bg-white/50'
                            "
                            @click="
                                goToSlide(
                                    i,
                                    i > currentSlide ? 'right' : 'left',
                                )
                            "
                        />
                        <span class="ml-3 text-xs text-white/70">
                            {{ currentSlide + 1 }} / {{ slides.length }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            aria-label="Slide sebelumnya"
                            class="flex size-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25"
                            @click="prevSlide"
                        >
                            <ArrowRight
                                class="size-4 rotate-180"
                                aria-hidden="true"
                            />
                        </button>
                        <button
                            type="button"
                            aria-label="Slide berikutnya"
                            class="flex size-10 items-center justify-center rounded-full bg-white text-[#059669] shadow-md transition hover:shadow-lg"
                            @click="nextSlide"
                        >
                            <ArrowRight class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== PROFIL RUMAH SAKIT ========== -->
        <section id="tentang" class="px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="relative">
                        <div
                            class="overflow-hidden rounded-3xl ring-1 ring-emerald-100"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=900&auto=format&fit=crop&q=70"
                                alt="Gedung RS Ibnu Sina"
                                class="h-[460px] w-full object-cover"
                                loading="lazy"
                            />
                        </div>

                        <div
                            class="absolute -top-4 -right-4 rounded-2xl bg-white p-4 shadow-lg ring-1 ring-emerald-100 sm:-top-6 sm:-right-6"
                        >
                            <p class="text-xs text-slate-500">Berdiri Sejak</p>
                            <p class="text-2xl font-semibold text-[#059669]">
                                1988
                            </p>
                            <p class="text-[10px] text-slate-500">
                                37+ tahun melayani
                            </p>
                        </div>

                        <div
                            class="absolute -bottom-4 -left-4 max-w-[220px] rounded-2xl bg-[#064e3b] p-4 text-white shadow-lg sm:-bottom-6 sm:-left-6"
                        >
                            <div
                                class="flex size-9 items-center justify-center rounded-lg bg-white/15"
                            >
                                <ShieldCheck class="size-5" />
                            </div>
                            <p class="mt-3 text-sm font-semibold">
                                Akreditasi Paripurna
                            </p>
                            <p class="mt-1 text-xs text-white/70">
                                KARS · Faskes Rujukan Tipe B BPJS
                            </p>
                        </div>
                    </div>

                    <div>
                        <span
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-[#059669]"
                        >
                            <Sparkles class="size-3.5" /> Profil Rumah Sakit
                        </span>
                        <h2
                            class="mt-3 text-3xl leading-tight font-semibold tracking-tight md:text-4xl"
                        >
                            Pelayanan Kesehatan Paripurna
                            <br />Bernuansa Islami di Makassar
                        </h2>
                        <p
                            class="mt-5 text-base leading-relaxed text-slate-600"
                        >
                            {{ hospitalName }} adalah rumah sakit umum tipe B
                            di bawah Yayasan Wakaf Universitas Muslim Indonesia
                            (YW-UMI). Sejak 1988, kami merawat masyarakat
                            Sulawesi Selatan dengan pelayanan medis paripurna,
                            ramah, dan menjunjung nilai-nilai Islami — dari
                            rawat jalan, rawat inap, hingga gawat darurat 24
                            jam.
                        </p>

                        <ul class="mt-6 space-y-3">
                            <li
                                v-for="point in [
                                    'Faskes rujukan tipe B BPJS Kesehatan',
                                    'Tim dokter spesialis berpengalaman',
                                    'Fasilitas IGD, ICU, NICU, &amp; PICU 24 jam',
                                    'Asisten virtual cerdas berbahasa Indonesia',
                                ]"
                                :key="point"
                                class="flex items-start gap-3 text-sm text-slate-700"
                            >
                                <span
                                    class="mt-0.5 flex size-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[#059669]"
                                >
                                    <Check class="size-3" stroke-width="3" />
                                </span>
                                <span v-html="point" />
                            </li>
                        </ul>

                        <a
                            href="#layanan"
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#059669] px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#047857]"
                        >
                            Lihat Layanan Kami
                            <span
                                class="flex size-5 items-center justify-center rounded-full bg-white text-[#059669]"
                            >
                                <ArrowRight class="size-3" />
                            </span>
                        </a>
                    </div>
                </div>

                <div
                    class="mt-16 grid gap-4 rounded-3xl border border-emerald-100 bg-emerald-50/40 p-6 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div class="text-center">
                        <p
                            class="text-4xl font-semibold tracking-tight text-[#059669]"
                        >
                            37+
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            Tahun melayani masyarakat
                        </p>
                    </div>
                    <div class="text-center">
                        <p
                            class="text-4xl font-semibold tracking-tight text-[#059669]"
                        >
                            {{ stats.specializations }}
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            Bidang spesialisasi
                        </p>
                    </div>
                    <div class="text-center">
                        <p
                            class="text-4xl font-semibold tracking-tight text-[#059669]"
                        >
                            150+
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            Tempat tidur rawat inap
                        </p>
                    </div>
                    <div class="text-center">
                        <p
                            class="text-4xl font-semibold tracking-tight text-[#059669]"
                        >
                            24/7
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            Layanan IGD &amp; farmasi
                        </p>
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
                    <div class="mt-3 grid items-end gap-6 lg:grid-cols-2">
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
                        <div
                            class="mb-5 flex items-center justify-between text-xs"
                        >
                            <span class="font-medium text-slate-500"
                                >Nilai Pelayanan</span
                            >
                            <span class="text-slate-400">{{ valueIndex }}</span>
                        </div>
                        <ul class="space-y-1">
                            <li
                                v-for="(v, i) in values"
                                :key="v.title"
                                class="flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm transition"
                                :class="
                                    i === activeValue
                                        ? 'bg-[#059669] text-white'
                                        : 'text-slate-700 hover:bg-slate-50'
                                "
                                @click="activeValue = i"
                            >
                                <span class="font-medium">{{ v.title }}</span>
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

                    <!-- Image card with active value description overlay -->
                    <div
                        class="relative overflow-hidden rounded-3xl ring-1 ring-slate-200"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1581056771107-24ca5f033842?w=800&auto=format&fit=crop&q=70"
                            alt="Dokter sedang berkonsultasi dengan pasien"
                            class="h-full max-h-[460px] w-full object-cover"
                            loading="lazy"
                        />
                        <div
                            class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 via-slate-900/30 to-transparent p-6 text-white"
                        >
                            <Transition
                                mode="out-in"
                                enter-active-class="transition duration-300 ease-out"
                                leave-active-class="transition duration-150 ease-in"
                                enter-from-class="opacity-0 translate-y-2"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <div :key="activeValue">
                                    <p
                                        class="text-xs font-medium tracking-wide uppercase text-emerald-200"
                                    >
                                        {{ valueIndex }} ·
                                        {{ values[activeValue].title }}
                                    </p>
                                    <p
                                        class="mt-2 text-sm leading-relaxed"
                                    >
                                        {{ values[activeValue].description }}
                                    </p>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Commitment card -->
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
                                Memberikan pelayanan kesehatan paripurna dengan
                                empati &amp; akhlak Islami untuk setiap pasien.
                            </h3>
                        </div>
                        <div class="mt-8 flex flex-wrap gap-2">
                            <span
                                v-for="v in values"
                                :key="v.title"
                                class="rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur"
                            >
                                {{ v.title }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== PELAYANAN TERPADU ========== -->
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
                    Satu Tim, Satu Tujuan:
                    <br />Kesembuhan Anda
                </h2>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600"
                >
                    Mulai dari pendaftaran, pemeriksaan, hingga rawat inap —
                    setiap langkah dirancang agar pasien dan keluarga merasa
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
                        class="group absolute top-1/2 left-1/2 flex size-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-[#059669] backdrop-blur transition hover:size-20 hover:bg-white"
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
                        class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-100/50"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-12 items-center justify-center rounded-full bg-emerald-100 font-semibold text-[#059669] transition group-hover:bg-[#059669] group-hover:text-white"
                            >
                                {{ d.name.charAt(0) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate font-semibold"
                                    :title="d.name"
                                >
                                    {{ d.name }}
                                </p>
                                <p class="text-xs font-medium text-[#059669]">
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
                        <Link
                            href="/chat"
                            class="mt-4 inline-flex items-center gap-1 text-xs font-medium text-[#059669] opacity-0 transition group-hover:opacity-100"
                        >
                            Tanya jadwal lengkap
                            <ArrowRight class="size-3" />
                        </Link>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap justify-center gap-2">
                    <span
                        v-for="spec in specializations"
                        :key="spec"
                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-1.5 text-xs text-slate-700 transition hover:border-emerald-400 hover:bg-emerald-100"
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
                        Yang Mereka Rasakan
                        <br />Setelah Berobat di Sini
                    </h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm text-slate-600">
                        Pengalaman jujur dari pasien dan keluarga yang
                        mempercayakan kesehatan mereka kepada RS Ibnu Sina.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div
                        v-for="(t, i) in testimonials"
                        :key="i"
                        class="rounded-2xl p-6 transition hover:-translate-y-0.5"
                        :class="
                            t.featured
                                ? 'bg-gradient-to-br from-[#059669] to-[#0d9488] text-white shadow-lg shadow-emerald-200'
                                : 'border border-slate-200 bg-white hover:shadow-md'
                        "
                    >
                        <!-- Star rating -->
                        <div class="mb-3 flex gap-0.5">
                            <Star
                                v-for="n in 5"
                                :key="n"
                                class="size-4"
                                :class="
                                    n <= t.rating
                                        ? t.featured
                                            ? 'fill-amber-300 text-amber-300'
                                            : 'fill-amber-400 text-amber-400'
                                        : t.featured
                                          ? 'text-white/30'
                                          : 'text-slate-300'
                                "
                            />
                        </div>
                        <p
                            class="text-base leading-relaxed"
                            :class="t.featured ? 'text-white' : 'text-slate-800'"
                        >
                            “{{ t.message }}”
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
                        class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div class="relative overflow-hidden rounded-xl">
                            <img
                                :src="post.image"
                                :alt="post.title"
                                class="h-44 w-full object-cover transition duration-500 group-hover:scale-105"
                                loading="lazy"
                            />
                            <span
                                class="absolute top-3 left-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-semibold text-[#059669] backdrop-blur"
                            >
                                {{ post.category }}
                            </span>
                        </div>
                        <div class="px-2 pt-4 pb-2">
                            <h3
                                class="line-clamp-2 font-semibold leading-snug"
                            >
                                {{ post.title }}
                            </h3>
                            <p
                                class="mt-2 line-clamp-2 text-sm text-slate-600"
                            >
                                {{ post.excerpt }}
                            </p>
                            <div
                                class="mt-4 flex items-center justify-between text-xs text-slate-500"
                            >
                                <span class="flex items-center gap-1">
                                    <span
                                        class="flex size-5 items-center justify-center rounded-full bg-emerald-100 text-[10px] font-semibold text-[#059669]"
                                        >{{
                                            post.author.charAt(0)
                                        }}</span
                                    >
                                    {{ post.author }}
                                </span>
                                <span>{{ post.reading_time }} mnt baca</span>
                            </div>
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

        <!-- ========== CTA BANNER ========== -->
        <section class="px-4 py-12 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div
                    class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#059669] via-[#10b981] to-[#0d9488] p-8 text-white sm:p-12"
                >
                    <div
                        class="pointer-events-none absolute -top-20 -right-20 size-72 rounded-full bg-white/10 blur-3xl"
                    />
                    <div
                        class="pointer-events-none absolute -bottom-20 -left-20 size-72 rounded-full bg-white/10 blur-3xl"
                    />

                    <div
                        class="relative grid items-center gap-8 lg:grid-cols-[1.4fr_1fr]"
                    >
                        <div>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-medium backdrop-blur"
                            >
                                <Sparkles class="size-3" /> Mulai Sekarang
                            </span>
                            <h2
                                class="mt-4 text-3xl leading-tight font-semibold tracking-tight md:text-4xl"
                            >
                                Punya pertanyaan?
                                <br />Asisten kami siap menjawab.
                            </h2>
                            <p
                                class="mt-3 max-w-xl text-sm leading-relaxed text-white/85"
                            >
                                Tanyakan jadwal dokter, prosedur BPJS, biaya,
                                atau apa pun seputar layanan rumah sakit. Bot
                                tidak memberikan diagnosis medis dan akan
                                otomatis menghubungkan Anda ke petugas bila
                                diperlukan.
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <Link
                                    href="/chat"
                                    class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-[#059669] shadow-md transition hover:shadow-lg"
                                >
                                    <MessageCircle class="size-4" />
                                    Mulai Chat Sekarang
                                </Link>
                                <a
                                    :href="`tel:${contact.phone}`"
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-white/25"
                                >
                                    <Phone class="size-4" />
                                    {{ contact.phone }}
                                </a>
                            </div>
                        </div>

                        <div class="hidden lg:block">
                            <div
                                class="space-y-3 rounded-2xl bg-white/15 p-5 ring-1 ring-white/20 backdrop-blur"
                            >
                                <p class="text-xs text-white/70">
                                    Pertanyaan paling sering ditanyakan
                                </p>
                                <ul class="space-y-2 text-sm">
                                    <li
                                        v-for="faq in featuredFaqs.slice(0, 4)"
                                        :key="faq.id"
                                        class="flex items-start gap-2"
                                    >
                                        <Check
                                            class="mt-0.5 size-4 flex-shrink-0 text-emerald-200"
                                        />
                                        <span class="line-clamp-1">{{
                                            faq.question
                                        }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
                    <div>
                        <h3
                            class="text-3xl leading-tight font-semibold tracking-tight"
                        >
                            Tetap terhubung
                            <br />dengan kami
                        </h3>
                        <p class="mt-3 text-sm text-white/70">
                            Berlangganan untuk mendapatkan informasi jadwal
                            dokter, promo MCU, dan tips kesehatan keluarga
                            langsung ke email Anda.
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

                    <div>
                        <p class="mb-4 text-sm font-semibold">Tautan Cepat</p>
                        <ul class="space-y-2 text-sm text-white/70">
                            <li>
                                <a
                                    href="#beranda"
                                    class="transition hover:text-white"
                                    >Beranda</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#tentang"
                                    class="transition hover:text-white"
                                    >Profil RS</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#layanan"
                                    class="transition hover:text-white"
                                    >Layanan</a
                                >
                            </li>
                            <li>
                                <Link
                                    href="/chat"
                                    class="transition hover:text-white"
                                    >Tanya AI</Link
                                >
                            </li>
                            <li>
                                <a
                                    href="#kontak"
                                    class="transition hover:text-white"
                                    >Kontak</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="mb-4 text-sm font-semibold">
                            Layanan Unggulan
                        </p>
                        <ul class="space-y-2 text-sm text-white/70">
                            <li>IGD 24 Jam</li>
                            <li>Rawat Inap &amp; ICU</li>
                            <li>Poliklinik Spesialis</li>
                            <li>Persalinan &amp; Kebidanan</li>
                            <li>Medical Check-Up</li>
                            <li>MCU Haji &amp; Umroh</li>
                        </ul>
                    </div>

                    <div>
                        <p class="mb-4 text-sm font-semibold">Hubungi Kami</p>
                        <ul class="space-y-3 text-sm text-white/80">
                            <li class="flex items-start gap-2">
                                <MapPin
                                    class="mt-0.5 size-4 flex-shrink-0 text-white/60"
                                />
                                <span>{{ contact.address }}</span>
                            </li>
                            <li>
                                <a
                                    :href="`tel:${contact.phone}`"
                                    class="flex items-center gap-2 transition hover:text-white"
                                >
                                    <Phone
                                        class="size-4 flex-shrink-0 text-white/60"
                                    />
                                    <span>CS {{ contact.phone }}</span>
                                </a>
                            </li>
                            <li class="flex items-center gap-2 text-rose-200">
                                <ShieldCheck class="size-4 flex-shrink-0" />
                                <span>IGD {{ contact.igd }}</span>
                            </li>
                            <li>
                                <a
                                    :href="`mailto:${contact.email}`"
                                    class="flex items-center gap-2 transition hover:text-white"
                                >
                                    <Mail
                                        class="size-4 flex-shrink-0 text-white/60"
                                    />
                                    <span>{{ contact.email }}</span>
                                </a>
                            </li>
                            <li>
                                <a
                                    :href="`https://wa.me/${contact.whatsapp.replace(/[^0-9]/g, '')}`"
                                    target="_blank"
                                    rel="noopener"
                                    class="flex items-center gap-2 transition hover:text-white"
                                >
                                    <MessageCircle
                                        class="size-4 flex-shrink-0 text-white/60"
                                    />
                                    <span>WA {{ contact.whatsapp }}</span>
                                </a>
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

        <!-- ========== FLOATING ACTION BUTTONS ========== -->
        <div
            class="fixed right-4 bottom-4 z-40 flex flex-col items-end gap-3 sm:right-6 sm:bottom-6"
        >
            <Transition
                enter-from-class="opacity-0 translate-y-2"
                enter-active-class="transition duration-200 ease-out"
                leave-active-class="transition duration-150 ease-in"
                leave-to-class="opacity-0 translate-y-2"
            >
                <button
                    v-if="showScrollTop"
                    type="button"
                    aria-label="Kembali ke atas"
                    class="flex size-11 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:bg-slate-800"
                    @click="scrollToTop"
                >
                    <ArrowUp class="size-5" />
                </button>
            </Transition>

            <a
                :href="`https://wa.me/${contact.whatsapp.replace(/[^0-9]/g, '')}?text=Halo%20RS%20Ibnu%20Sina%2C%20saya%20ingin%20bertanya...`"
                target="_blank"
                rel="noopener"
                aria-label="Chat WhatsApp"
                class="group relative flex size-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl ring-4 ring-emerald-500/20 transition hover:-translate-y-0.5 hover:bg-emerald-600"
            >
                <span
                    class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-30"
                />
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="relative size-7"
                    aria-hidden="true"
                >
                    <path
                        d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02ZM12.04 20.15h-.01a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.12.82.83-3.04-.2-.31a8.21 8.21 0 0 1-1.26-4.39c0-4.54 3.7-8.24 8.25-8.24a8.18 8.18 0 0 1 5.83 2.42 8.19 8.19 0 0 1 2.41 5.82c0 4.54-3.7 8.24-8.25 8.24Zm4.52-6.16c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06a6.79 6.79 0 0 1-2-1.23 7.51 7.51 0 0 1-1.39-1.72c-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.43.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.4-.41-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.86.84-.86 2.05 0 1.21.88 2.38 1 2.55.13.17 1.74 2.66 4.21 3.73.59.25 1.05.4 1.41.52.59.19 1.13.16 1.55.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.3Z"
                    />
                </svg>
                <span
                    class="pointer-events-none absolute right-full mr-3 hidden whitespace-nowrap rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg sm:group-hover:block"
                >
                    Chat WhatsApp
                </span>
            </a>
        </div>
    </div>
</template>
