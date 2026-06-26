<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Baby,
    Bone,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    Hand,
    HeartPulse,
    Mail,
    Menu,
    MessageCircle,
    Phone,
    Play,
    Quote,
    Sparkles,
    Star,
    Stethoscope,
    X,
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import type { Component } from 'vue';
import AssistantChatPanel from '@/components/AssistantChatPanel.vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { dashboard } from '@/routes';

interface ChatCategory {
    name: string;
    description: string | null;
}

interface ChatSuggestion {
    icon: string;
    label: string;
}

interface ServiceCard {
    title: string;
    description: string;
    icon: Component;
}

interface DoctorCard {
    name: string;
    specialization: string;
    image: string;
}

interface TestimonialCard {
    name: string;
    role: string;
    message: string;
    rating: number;
}

interface ArticleCard {
    category: string;
    title: string;
    excerpt: string;
    image: string;
    date: string;
    author: string;
}

const props = defineProps<{
    hospitalName: string;
    welcomeMessage: string;
    chatCategories: ChatCategory[];
    chatSuggestions: ChatSuggestion[];
    contact: {
        address: string;
        phone: string;
        igd: string;
        whatsapp: string;
        email: string;
        website: string;
    };
}>();

const serviceCards: ServiceCard[] = [
    {
        title: 'Dokter Umum',
        description:
            'Pemeriksaan awal, konsultasi keluhan ringan, dan rujukan cepat ke layanan yang tepat.',
        icon: Stethoscope,
    },
    {
        title: 'Pediatri',
        description:
            'Imunisasi, tumbuh kembang, dan konsultasi kesehatan anak dalam suasana yang ramah.',
        icon: Baby,
    },
    {
        title: 'Kardiologi',
        description:
            'Deteksi dini dan pemantauan kondisi jantung dengan pendekatan medis yang terukur.',
        icon: HeartPulse,
    },
    {
        title: 'Dermatologi',
        description:
            'Perawatan kulit, alergi, jerawat, dan keluhan kulit yang membutuhkan penanganan spesialis.',
        icon: Hand,
    },
    {
        title: 'Ginekologi',
        description:
            'Konsultasi kesehatan wanita, pemeriksaan rutin, dan layanan pendampingan yang nyaman.',
        icon: Sparkles,
    },
    {
        title: 'Ortopedi',
        description:
            'Penanganan tulang, sendi, cedera olahraga, dan pemulihan gerak secara menyeluruh.',
        icon: Bone,
    },
];

const aboutFeatures = [
    'Layanan Terintegrasi',
    'Perawatan Berpusat pada Pasien',
    'Lingkungan Ramah dan Nyaman',
    'Pendekatan Personal',
    'Perawatan Menyeluruh',
    'Teknologi Medis Modern',
    'Dokter Berpengalaman',
    'Ulasan Positif Pasien',
];

const doctorTeam: DoctorCard[] = [
    {
        name: 'dr. Fiona Wood',
        specialization: 'Kardiologi',
        image: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=900&auto=format&fit=crop&q=80',
    },
    {
        name: 'dr. Fiona Woods',
        specialization: 'Dokter Gigi',
        image: 'https://images.unsplash.com/photo-1559839734-7d3d2d6d2f3a?w=900&auto=format&fit=crop&q=80',
    },
    {
        name: 'dr. Fiona West',
        specialization: 'Neurologi',
        image: 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=900&auto=format&fit=crop&q=80',
    },
    {
        name: 'Dr. Charlie Tao',
        specialization: 'Kardiolog',
        image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=900&auto=format&fit=crop&q=80',
    },
];
const doctorMarqueeCards = computed(() => [...doctorTeam, ...doctorTeam]);

const emergencyCards = [
    {
        title: 'Nomor Telepon',
        value: props.contact.phone,
        icon: Phone,
    },
    {
        title: 'Alamat Email',
        value: props.contact.email,
        icon: Mail,
    },
];

const testimonials: TestimonialCard[] = [
    {
        name: 'Jack Wilson',
        role: 'Pasien',
        message:
            'Layanannya cepat, staff-nya sigap, dan proses konsultasinya terasa lebih tertata. Saya merasa lebih tenang sejak menggunakan halaman informasi ini.',
        rating: 5,
    },
    {
        name: 'Ella Lewis',
        role: 'Pasien',
        message:
            'Informasi dokter dan kontak mudah ditemukan. Tampilan halaman juga bersih sehingga saya cepat menemukan kebutuhan saya.',
        rating: 5,
    },
    {
        name: 'Maya Santoso',
        role: 'Pasien',
        message:
            'Section testimoni terasa lebih hidup ketika bisa digeser. Sekarang saya bisa melihat beberapa pengalaman pasien lain dengan lebih nyaman.',
        rating: 5,
    },
];

const articles: ArticleCard[] = [
    {
        category: 'Edukasi',
        title: 'Cara hidup sehat tanpa rasa sakit atau penyakit',
        excerpt:
            'Panduan sederhana untuk menjaga kebugaran harian, tidur cukup, dan kebiasaan yang membantu tubuh tetap fit.',
        image: 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd2e?w=900&auto=format&fit=crop&q=80',
        date: '12 Mar, 2026',
        author: 'Asep Carey',
    },
    {
        category: 'Kesehatan',
        title: 'Mengapa Anda perlu meninggalkan kebiasaan buruk agar tetap sehat',
        excerpt:
            'Kebiasaan kecil punya dampak besar. Mulai dari konsumsi air, pola makan, sampai aktivitas fisik rutin.',
        image: 'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=900&auto=format&fit=crop&q=80',
        date: '11 Mar, 2026',
        author: 'Fiona Wood',
    },
    {
        category: 'Perawatan',
        title: 'Setiap rumah wajib memiliki kotak P3K',
        excerpt:
            'Perlengkapan dasar pertolongan pertama membantu penanganan cepat sebelum konsultasi ke tenaga medis.',
        image: 'https://images.unsplash.com/photo-1579154204601-01588f351e67?w=900&auto=format&fit=crop&q=80',
        date: '10 Mar, 2026',
        author: 'Charlee Tao',
    },
];

const mobileMenuOpen = ref(false);
const assistantOpen = ref(false);
const currentSlide = ref(0);
const currentTestimonial = ref(0);
const assistantAnchor = ref<{
    top: number;
    left: number;
    originX: number;
} | null>(null);
let heroSlideTimer: ReturnType<typeof window.setInterval> | null = null;

const whatsappUrl = computed(
    () =>
        `https://wa.me/${props.contact.whatsapp.replace(/[^0-9]/g, '')}?text=Halo%20${encodeURIComponent(
            props.hospitalName,
        )}%2C%20saya%20ingin%20bertanya%20tentang%20layanan.`,
);

const navigationItems = [
    { href: '#beranda', label: 'Beranda' },
    { href: '#layanan', label: 'Layanan' },
    { href: '#tentang', label: 'Tentang Kami' },
    { href: '#dokter', label: 'Dokter' },
    { href: '#bantuan', label: 'Bantuan' },
    { href: '#artikel', label: 'Artikel' },
    { href: '#kontak', label: 'Kontak' },
];

const heroSlides = [
    {
        eyebrow: '01/03',
        title: 'Kami merawat pasien dengan layanan yang hangat dan terpadu.',
        description:
            'Informasi jadwal, layanan unggulan, dan kontak penting disusun ringkas agar pasien bisa bergerak lebih cepat menuju tindakan yang dibutuhkan.',
        image: '/img/rs-ibnu-sina.png',
    },
    {
        eyebrow: '02/03',
        title: 'Tim dokter spesialis siap mendampingi Anda dengan sepenuh perhatian.',
        description:
            'Setiap layanan disiapkan agar lebih mudah ditemukan, lebih nyaman digunakan, dan tetap terasa personal.',
        image: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1800&auto=format&fit=crop&q=80',
    },
    {
        eyebrow: '03/03',
        title: 'Asisten virtual siap membantu 24 jam kapan pun Anda butuhkan.',
        description:
            'Tanyakan gejala ringan, cari dokter terdekat, atau konfirmasi jadwal melalui chat untuk mempercepat pelayanan.',
        image: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1800&auto=format&fit=crop&q=80',
    },
];

const currentYear = new Date().getFullYear();
const testimonialPageCount = Math.max(1, Math.ceil(testimonials.length / 2));

const assistantPopupStyle = computed(() => {
    return {
        top: assistantAnchor.value ? `${assistantAnchor.value.top}px` : '6rem',
        left: 'calc(100vw - 0.5rem - min(360px, calc(100vw - 1rem)))',
        '--assistant-origin': assistantAnchor.value
            ? `${assistantAnchor.value.originX}px -0.75rem`
            : 'calc(100% - 2.75rem) -0.75rem',
    };
});

const closeMobileMenu = (): void => {
    mobileMenuOpen.value = false;
};

const openAssistant = (event?: MouseEvent): void => {
    const trigger = event?.currentTarget;

    if (trigger instanceof HTMLElement) {
        const rect = trigger.getBoundingClientRect();
        const popupHeight = Math.min(720, window.innerHeight - 112);
        const top = Math.min(
            Math.max(96, rect.bottom + 14),
            window.innerHeight - popupHeight - 16,
        );

        assistantAnchor.value = {
            top,
            left: 0,
            originX: 320,
        };
    }

    assistantOpen.value = !assistantOpen.value;
};

const goToSlide = (index: number): void => {
    currentSlide.value = (index + heroSlides.length) % heroSlides.length;
};

const nextSlide = (): void => {
    goToSlide(currentSlide.value + 1);
};

const previousSlide = (): void => {
    goToSlide(currentSlide.value - 1);
};

const goToTestimonial = (index: number): void => {
    currentTestimonial.value =
        (index + testimonialPageCount) % testimonialPageCount;
};

const nextTestimonial = (): void => {
    goToTestimonial(currentTestimonial.value + 1);
};

const previousTestimonial = (): void => {
    goToTestimonial(currentTestimonial.value - 1);
};

const testimonialPages = computed(() => {
    if (testimonials.length <= 2) {
        return [testimonials];
    }

    const pages: TestimonialCard[][] = [];

    for (let index = 0; index < testimonials.length; index += 2) {
        pages.push([
            testimonials[index],
            testimonials[(index + 1) % testimonials.length],
        ]);
    }

    return pages;
});

const testimonialTrackStyle = computed(() => {
    return {
        transform: `translateX(-${currentTestimonial.value * 100}%)`,
    };
});

onMounted(() => {
    heroSlideTimer = window.setInterval(() => {
        nextSlide();
    }, 5000);
});

onUnmounted(() => {
    if (heroSlideTimer !== null) {
        window.clearInterval(heroSlideTimer);
        heroSlideTimer = null;
    }
});

watch(
    () => currentSlide.value,
    () => {
        const slide = heroSlides[currentSlide.value];
        const image = new Image();
        image.src = slide.image;
    },
);
</script>

<template>
    <Head :title="`${hospitalName} - Layanan Informasi`" />

    <div class="min-h-screen bg-white text-slate-950">
        <div class="relative isolate overflow-hidden">
            <div
                class="absolute inset-x-0 top-0 -z-20 h-[720px] bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(15,23,42,0.08),_transparent_28%),linear-gradient(180deg,_#f8fafc_0%,_#ffffff_58%)]"
            />
            <div
                class="absolute inset-x-0 top-0 -z-10 h-[540px] bg-[linear-gradient(to_right,rgba(148,163,184,0.12)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.12)_1px,transparent_1px)] [mask-image:linear-gradient(to_bottom,white,transparent)] bg-[size:72px_72px]"
            />

            <header
                class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl"
            >
                <div
                    class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
                >
                    <Link href="/" class="flex items-center gap-3">
                        <img
                            src="/img/logo-rumah-sakit-ibnu-sina.png"
                            alt="Logo Rumah Sakit Ibnu Sina"
                            class="h-11 w-auto object-contain"
                            loading="eager"
                        />
                    </Link>

                    <nav
                        class="hidden items-center gap-7 text-sm text-slate-600 lg:flex"
                    >
                        <a
                            v-for="item in navigationItems"
                            :key="item.href"
                            :href="item.href"
                            class="transition hover:text-slate-950"
                        >
                            {{ item.label }}
                        </a>
                    </nav>

                    <div class="hidden items-center gap-3 lg:flex">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950"
                        >
                            Dasbor
                        </Link>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#339966] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#2b8055]"
                            @click="openAssistant($event)"
                        >
                            Tanya AI
                            <ArrowRight class="size-4" />
                        </button>
                    </div>

                    <button
                        type="button"
                        class="flex size-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden"
                        :aria-label="
                            mobileMenuOpen ? 'Tutup menu' : 'Buka menu'
                        "
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <Menu v-if="!mobileMenuOpen" class="size-5" />
                        <X v-else class="size-5" />
                    </button>
                </div>

                <Transition
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-active-class="transition duration-200 ease-out"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div
                        v-if="mobileMenuOpen"
                        class="border-t border-slate-200 bg-white lg:hidden"
                    >
                        <div
                            class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 sm:px-6"
                        >
                            <a
                                v-for="item in navigationItems"
                                :key="item.href"
                                :href="item.href"
                                class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950"
                                @click="closeMobileMenu"
                            >
                                {{ item.label }}
                            </a>
                            <button
                                type="button"
                                class="mt-2 inline-flex items-center justify-between rounded-xl bg-[#339966] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#2b8055]"
                                @click="
                                    closeMobileMenu();
                                    openAssistant($event);
                                "
                            >
                                Tanya AI
                                <ArrowRight class="size-4" />
                            </button>
                        </div>
                    </div>
                </Transition>
            </header>

            <main>
                <section
                    id="beranda"
                    class="relative mt-16 overflow-hidden bg-slate-950 text-white sm:mt-20"
                >
                    <div class="absolute inset-0">
                        <Transition
                            enter-active-class="transition duration-700 ease-out"
                            enter-from-class="opacity-0 scale-105"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="transition duration-700 ease-out"
                            leave-to-class="opacity-0 scale-105"
                            mode="out-in"
                        >
                            <img
                                :key="heroSlides[currentSlide].image"
                                :src="heroSlides[currentSlide].image"
                                alt="Hero rumah sakit"
                                class="absolute inset-0 h-full w-full object-cover"
                                loading="eager"
                            />
                        </Transition>
                        <div
                            class="absolute inset-0 bg-[linear-gradient(90deg,rgba(2,6,23,0.82)_0%,rgba(2,6,23,0.52)_42%,rgba(2,6,23,0.14)_100%),linear-gradient(to_top,rgba(6,95,70,0.3)_0%,transparent_44%)]"
                        />
                    </div>

                    <div
                        class="relative mx-auto flex min-h-[720px] max-w-7xl flex-col justify-center px-4 py-24 sm:px-6 lg:px-8"
                    >
                        <div
                            class="grid gap-12 lg:grid-cols-[1fr_0.7fr] lg:items-end"
                        >
                            <div class="max-w-3xl">
                                <div class="mb-6 flex items-center gap-5">
                                    <div class="h-20 w-px bg-white/40" />
                                    <p
                                        class="text-2xl font-medium tracking-[0.24em] text-white/80"
                                    >
                                        {{ heroSlides[currentSlide].eyebrow }}
                                    </p>
                                </div>

                                <h1
                                    class="max-w-3xl text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl"
                                >
                                    {{ heroSlides[currentSlide].title }}
                                </h1>
                                <p
                                    class="mt-4 max-w-2xl text-sm leading-7 text-white/75 sm:text-base"
                                >
                                    {{ heroSlides[currentSlide].description }}
                                </p>

                                <div
                                    class="mt-8 flex flex-col gap-3 sm:flex-row"
                                >
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#339966] px-6 py-3.5 text-sm font-medium text-white transition hover:bg-[#2b8055]"
                                        @click="openAssistant($event)"
                                    >
                                        Tanya AI
                                        <ArrowRight class="size-4" />
                                    </button>
                                    <a
                                        href="#kontak"
                                        class="inline-flex items-center justify-center rounded-xl border border-white/35 px-6 py-3.5 text-sm font-medium text-white transition hover:border-white/60 hover:bg-white/10"
                                    >
                                        Hubungi Kami
                                    </a>
                                </div>

                                <div class="mt-7 flex items-center gap-4">
                                    <button
                                        type="button"
                                        class="flex size-12 items-center justify-center rounded-full border border-white/35 text-white transition hover:border-white/70 hover:bg-white/10"
                                        aria-label="Slide sebelumnya"
                                        @click="previousSlide"
                                    >
                                        <ChevronLeft class="size-5" />
                                    </button>
                                    <button
                                        type="button"
                                        class="flex size-12 items-center justify-center rounded-full border border-white/35 text-white transition hover:border-white/70 hover:bg-white/10"
                                        aria-label="Slide berikutnya"
                                        @click="nextSlide"
                                    >
                                        <ChevronRight class="size-5" />
                                    </button>
                                </div>
                            </div>

                            <div
                                class="flex items-end justify-start lg:justify-end"
                            >
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-medium text-white backdrop-blur-md transition hover:bg-white/15"
                                    @click="openAssistant($event)"
                                >
                                    <span
                                        class="flex size-8 items-center justify-center rounded-lg bg-white/10"
                                    >
                                        <Play class="size-4" />
                                    </span>
                                    Lihat Video
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    id="layanan"
                    class="bg-white px-4 py-20 sm:px-6 lg:px-8"
                >
                    <div class="mx-auto max-w-7xl">
                        <div class="mb-8">
                            <div class="flex items-center gap-3">
                                <span class="h-5 w-px bg-[#339966]" />
                                <p
                                    class="text-sm font-semibold tracking-[0.08em] text-slate-500 uppercase"
                                >
                                    Solusi Kesehatan
                                </p>
                            </div>
                            <h2
                                class="mt-4 max-w-3xl text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl"
                            >
                                Solusi layanan kesehatan untuk kenyamanan Anda
                            </h2>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <article
                                v-for="item in serviceCards"
                                :key="item.title"
                                class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/3 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-100 hover:shadow-md"
                                :class="
                                    item.title === 'Kardiologi' &&
                                    'border-[rgba(51,153,102,0.18)] bg-[rgba(51,153,102,0.10)]'
                                "
                            >
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-[#339966] ring-1 ring-[rgba(51,153,102,0.16)]"
                                    >
                                        <component
                                            :is="item.icon"
                                            class="size-4"
                                        />
                                    </div>
                                </div>

                                <h3
                                    class="mt-6 text-sm font-semibold tracking-tight text-slate-950"
                                >
                                    {{ item.title }}
                                </h3>
                                <p
                                    class="mt-3 text-sm leading-7 text-slate-600"
                                >
                                    {{ item.description }}
                                </p>

                                <div class="mt-6 flex justify-start">
                                    <span
                                        class="flex size-10 items-center justify-center rounded-full bg-white text-[#339966] ring-1 ring-[rgba(51,153,102,0.2)] transition group-hover:bg-[#339966] group-hover:text-white"
                                    >
                                        <ArrowRight class="size-6 -rotate-45" />
                                    </span>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section
                    id="tentang"
                    class="bg-slate-50/70 px-4 py-20 sm:px-6 lg:px-8"
                >
                    <div class="mx-auto max-w-7xl">
                        <div
                            class="grid gap-12 lg:grid-cols-[0.95fr_1.05fr] lg:items-center"
                        >
                            <div class="relative">
                                <img
                                    src="/img/rs-ibnu-sina.png"
                                    alt="RS Ibnu Sina"
                                    class="h-[420px] w-full rounded-3xl object-cover"
                                    loading="lazy"
                                />

                                <div
                                    class="absolute top-6 left-6 rounded-2xl bg-white px-5 py-4 shadow-lg shadow-slate-950/10"
                                >
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex size-10 items-center justify-center rounded-full bg-[rgba(51,153,102,0.10)] text-[#339966]"
                                        >
                                            <MessageCircle class="size-5" />
                                        </span>
                                        <div>
                                            <p
                                                class="text-lg font-semibold text-slate-950"
                                            >
                                                24/7
                                            </p>
                                            <p class="text-sm text-slate-500">
                                                Kami siap membantu kapan pun
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="absolute right-6 bottom-6 max-w-xs rounded-2xl bg-white px-5 py-4 shadow-lg shadow-slate-950/10"
                                >
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-[rgba(51,153,102,0.10)] text-[#339966]"
                                        >
                                            <CheckCircle2 class="size-4" />
                                        </span>
                                        <div>
                                            <p
                                                class="font-semibold text-slate-950"
                                            >
                                                8 Tahun pengalaman
                                            </p>
                                            <p
                                                class="mt-1 text-sm leading-6 text-slate-500"
                                            >
                                                Melayani komunitas dengan
                                                layanan medis yang profesional,
                                                ramah, dan penuh perhatian.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="mb-5 flex items-center gap-3">
                                    <p
                                        class="text-sm font-semibold tracking-[0.08em] text-slate-500 uppercase"
                                    >
                                        | Tentang Kami
                                    </p>
                                </div>

                                <h2
                                    class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl"
                                >
                                    Kami menyediakan layanan perawatan pasien
                                    dan fasilitas terbaik
                                </h2>
                                <p
                                    class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base"
                                >
                                    Kami merancang layanan yang komprehensif
                                    agar pasien merasa nyaman, terbantu, dan
                                    lebih cepat mendapatkan tindakan yang tepat
                                    saat datang ke rumah sakit.
                                </p>

                                <div
                                    class="mt-8 grid gap-x-6 gap-y-3 sm:grid-cols-2"
                                >
                                    <p
                                        v-for="feature in aboutFeatures"
                                        :key="feature"
                                        class="flex items-center gap-3 text-sm font-medium text-slate-700"
                                    >
                                        <span
                                            class="flex size-5 items-center justify-center rounded bg-[rgba(51,153,102,0.18)] text-[#339966]"
                                        >
                                            <CheckCircle2 class="size-3.5" />
                                        </span>
                                        {{ feature }}
                                    </p>
                                </div>

                                <div class="mt-8">
                                    <a
                                        href="#dokter"
                                        class="inline-flex items-center gap-2 rounded-xl bg-[#339966] px-6 py-3 text-sm font-medium text-white transition hover:bg-[#2b8055]"
                                    >
                                        Selengkapnya Tentang Kami
                                        <ArrowRight class="size-4" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="dokter" class="px-4 py-20 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-7xl">
                        <div class="flex items-end justify-between gap-6">
                            <div>
                                <p
                                    class="text-sm font-semibold tracking-[0.08em] text-slate-500 uppercase"
                                >
                                    | Tim Dokter Kami
                                </p>
                                <h2
                                    class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl"
                                >
                                    Kenali dokter ahli kami
                                </h2>
                            </div>
                            <a
                                href="#kontak"
                                class="hidden items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-slate-950 sm:inline-flex"
                            >
                                Lihat Semua Tim
                                <ArrowRight class="size-4" />
                            </a>
                        </div>

                        <div class="mt-10 overflow-hidden">
                            <div class="doctor-track flex w-max gap-5">
                                <article
                                    v-for="(
                                        doctor, index
                                    ) in doctorMarqueeCards"
                                    :key="`${doctor.name}-${doctor.specialization}-${index}`"
                                    class="w-[260px] shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm shadow-slate-950/5 sm:w-[280px] lg:w-[300px]"
                                >
                                    <img
                                        :src="doctor.image"
                                        :alt="doctor.name"
                                        class="h-56 w-full object-cover"
                                        loading="lazy"
                                    />
                                    <div class="border-t border-slate-100 p-4">
                                        <p
                                            class="text-base font-semibold tracking-tight text-slate-950"
                                        >
                                            {{ doctor.name }}
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ doctor.specialization }}
                                        </p>
                                    </div>
                                    <div
                                        class="h-1 bg-[rgba(51,153,102,0.8)]"
                                    />
                                </article>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    id="bantuan"
                    class="bg-white px-4 py-20 sm:px-6 lg:px-8"
                >
                    <div
                        class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-center"
                    >
                        <div>
                            <p
                                class="text-sm font-semibold tracking-[0.08em] text-slate-500 uppercase"
                            >
                                | Layanan Darurat
                            </p>
                            <h2
                                class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl"
                            >
                                Layanan bantuan 24/7
                            </h2>
                            <p
                                class="mt-5 max-w-xl text-sm leading-7 text-slate-600 sm:text-base"
                            >
                                Kontak darurat dan informasi dasar selalu
                                tersedia agar keluarga pasien bisa bergerak
                                cepat saat dibutuhkan.
                            </p>

                            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                                <article
                                    v-for="card in emergencyCards"
                                    :key="card.title"
                                    class="rounded-[1.4rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/5"
                                >
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="flex size-10 items-center justify-center rounded-xl bg-[rgba(51,153,102,0.10)] text-[#339966]"
                                        >
                                            <component
                                                :is="card.icon"
                                                class="size-4"
                                            />
                                        </span>
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-slate-950"
                                            >
                                                {{ card.title }}
                                            </p>
                                            <p
                                                class="mt-2 text-sm text-slate-600"
                                            >
                                                {{ card.value }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1584515933487-779824d29309?w=1200&auto=format&fit=crop&q=80"
                                alt="Bantuan darurat"
                                class="h-[380px] w-full rounded-3xl object-cover"
                                loading="lazy"
                            />
                            <div
                                class="absolute top-6 left-6 rounded-2xl bg-white px-4 py-3 shadow-lg shadow-slate-950/10"
                            >
                                <p
                                    class="text-xs font-semibold tracking-[0.08em] text-slate-500 uppercase"
                                >
                                    Janji Temu
                                </p>
                                <p
                                    class="mt-1 text-sm font-medium text-slate-950"
                                >
                                    Buat janji kapan saja.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    id="aplikasi"
                    class="bg-slate-50/70 px-4 py-20 sm:px-6 lg:px-8"
                >
                    <div
                        class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center"
                    >
                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1551650975-87deedd944c3?w=1200&auto=format&fit=crop&q=80"
                                alt="Mobile app"
                                class="h-[380px] w-full rounded-3xl object-cover"
                                loading="lazy"
                            />
                            <div
                                class="absolute bottom-8 left-8 rounded-lg bg-white px-4 py-3 shadow-lg shadow-slate-950/10"
                            >
                                <p
                                    class="text-xs font-semibold tracking-normal text-slate-500 normal-case"
                                >
                                    Kunjungan rumah sakit
                                </p>
                            </div>
                            <div
                                class="absolute right-8 bottom-8 rounded-lg bg-white px-4 py-3 shadow-lg shadow-slate-950/10"
                            >
                                <p
                                    class="text-xs font-semibold tracking-normal text-slate-500 normal-case"
                                >
                                    Chat lewat aplikasi
                                </p>
                            </div>
                        </div>

                        <div>
                            <p
                                class="text-sm font-semibold tracking-[0.08em] text-slate-500 uppercase"
                            >
                                | Unduh Aplikasi
                            </p>
                            <h2
                                class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl"
                            >
                                Unduh aplikasi mobile kami untuk pengalaman
                                terbaik
                            </h2>
                            <p
                                class="mt-5 max-w-xl text-sm leading-7 text-slate-600 sm:text-base"
                            >
                                Akses jadwal, pesan layanan, dan pantau
                                kunjungan dari ponsel agar pengalaman pasien
                                lebih cepat dan praktis.
                            </p>

                            <div class="mt-6 flex items-center gap-3">
                                <div class="flex -space-x-2">
                                    <img
                                        v-for="avatar in doctorTeam.slice(0, 3)"
                                        :key="avatar.name"
                                        :src="avatar.image"
                                        :alt="avatar.name"
                                        class="size-10 rounded-full border-2 border-white object-cover"
                                    />
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        800+
                                    </p>
                                    <p class="text-sm text-slate-500">
                                        Dokter tersedia di aplikasi mobile
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <a
                                    href="#kontak"
                                    class="inline-flex items-center gap-2 rounded-xl bg-rose-500 px-5 py-3 text-sm font-medium text-white transition hover:bg-rose-600"
                                >
                                    Dapatkan di Google Play
                                </a>
                                <a
                                    href="#kontak"
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800"
                                >
                                    Dapatkan di App Store
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="testimoni" class="px-4 py-20 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-7xl">
                        <div>
                            <p
                                class="text-sm font-semibold tracking-[0.08em] text-slate-500 uppercase"
                            >
                                | Testimoni
                            </p>
                            <h2
                                class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl"
                            >
                                Apa kata pasien kami
                            </h2>
                        </div>

                        <div class="mt-10">
                            <div class="overflow-hidden">
                                <div
                                    class="testimonial-track flex"
                                    :style="testimonialTrackStyle"
                                >
                                    <article
                                        v-for="(
                                            page, pageIndex
                                        ) in testimonialPages"
                                        :key="pageIndex"
                                        class="min-w-full px-0 md:px-2"
                                    >
                                        <div class="grid gap-5 md:grid-cols-2">
                                            <article
                                                v-for="testimonial in page"
                                                :key="`${pageIndex}-${testimonial.name}`"
                                                class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-950/5"
                                            >
                                                <Quote
                                                    class="size-8 text-[rgba(51,153,102,0.7)]"
                                                />
                                                <p
                                                    class="mt-5 text-sm leading-7 text-slate-600"
                                                >
                                                    {{ testimonial.message }}
                                                </p>
                                                <div
                                                    class="mt-6 flex items-center gap-1"
                                                >
                                                    <Star
                                                        v-for="index in 5"
                                                        :key="index"
                                                        class="size-4"
                                                        :class="
                                                            index <=
                                                            testimonial.rating
                                                                ? 'fill-amber-400 text-amber-400'
                                                                : 'text-slate-200'
                                                        "
                                                    />
                                                </div>
                                                <div
                                                    class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-4"
                                                >
                                                    <div
                                                        class="flex size-10 items-center justify-center rounded-full bg-slate-100 text-slate-500"
                                                    >
                                                        {{
                                                            testimonial.name.charAt(
                                                                0,
                                                            )
                                                        }}
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-sm font-semibold text-slate-950"
                                                        >
                                                            {{
                                                                testimonial.name
                                                            }}
                                                        </p>
                                                        <p
                                                            class="text-sm text-slate-500"
                                                        >
                                                            {{
                                                                testimonial.role
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                    </article>
                                </div>
                            </div>

                            <div
                                class="mt-6 flex items-center justify-center gap-3"
                            >
                                <button
                                    type="button"
                                    class="flex size-10 items-center justify-center rounded-full border border-[rgba(51,153,102,0.22)] bg-white text-[#339966] shadow-sm transition hover:border-[rgba(51,153,102,0.32)] hover:bg-[rgba(51,153,102,0.06)] hover:text-[#2b8055]"
                                    aria-label="Testimoni sebelumnya"
                                    @click="previousTestimonial"
                                >
                                    <ChevronLeft class="size-5" />
                                </button>
                                <button
                                    type="button"
                                    class="flex size-10 items-center justify-center rounded-full border border-[rgba(51,153,102,0.22)] bg-white text-[#339966] shadow-sm transition hover:border-[rgba(51,153,102,0.32)] hover:bg-[rgba(51,153,102,0.06)] hover:text-[#2b8055]"
                                    aria-label="Testimoni berikutnya"
                                    @click="nextTestimonial"
                                >
                                    <ChevronRight class="size-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    id="artikel"
                    class="bg-white px-4 py-20 sm:px-6 lg:px-8"
                >
                    <div class="mx-auto max-w-7xl">
                        <div class="flex items-end justify-between gap-6">
                            <div>
                                <p
                                    class="text-sm font-semibold tracking-[0.08em] text-slate-500 uppercase"
                                >
                                    | Blog & Artikel
                                </p>
                                <h2
                                    class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl"
                                >
                                    Baca artikel terbaik dari dokter ahli
                                </h2>
                            </div>
                            <a
                                href="#kontak"
                                class="hidden items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-slate-950 sm:inline-flex"
                            >
                                Lihat Lainnya
                                <ArrowRight class="size-4" />
                            </a>
                        </div>

                        <div class="mt-10 grid gap-8 lg:grid-cols-3">
                            <article
                                v-for="article in articles"
                                :key="article.title"
                                class="rounded-xl border border-slate-200 bg-white p-2 shadow-sm shadow-slate-950/5"
                            >
                                <img
                                    :src="article.image"
                                    :alt="article.title"
                                    class="h-52 w-full rounded-lg object-cover"
                                    loading="lazy"
                                />
                                <div class="px-1 pt-2 pb-1">
                                    <div
                                        class="flex items-center justify-between text-xs font-semibold tracking-[0.08em] text-slate-500"
                                    >
                                        <span
                                            class="rounded-md bg-[rgba(51,153,102,0.10)] px-3 py-1 text-[#2b8055] lowercase normal-case"
                                        >
                                            {{ article.category }}
                                        </span>
                                        <span class="uppercase">{{
                                            article.date
                                        }}</span>
                                    </div>
                                    <h3
                                        class="mt-3 text-lg font-semibold tracking-tight text-slate-950"
                                    >
                                        {{ article.title }}
                                    </h3>
                                    <div class="mt-4">
                                        <a
                                            href="#kontak"
                                            class="inline-flex items-center gap-1 text-sm font-medium text-slate-950"
                                        >
                                            Baca Selengkapnya
                                            <ArrowRight
                                                class="size-4 -rotate-45"
                                            />
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section
                    id="kontak"
                    class="bg-slate-900 px-4 pt-16 pb-8 text-white sm:px-6 lg:px-8"
                >
                    <div class="mx-auto max-w-7xl">
                        <div class="grid gap-10 lg:grid-cols-[1.2fr_1fr_1fr]">
                            <div>
                                <Link
                                    href="/"
                                    class="inline-flex items-center rounded-lg bg-white px-3 py-2 shadow-sm ring-1 ring-black/5"
                                >
                                    <img
                                        src="/img/logo-rumah-sakit-ibnu-sina.png"
                                        alt="Logo Rumah Sakit Ibnu Sina"
                                        class="h-10 w-auto object-contain"
                                    />
                                </Link>
                                <p
                                    class="mt-4 max-w-md text-sm leading-7 text-white/65"
                                >
                                    Informasi rumah sakit disusun agar pasien
                                    dan keluarga dapat bergerak lebih cepat,
                                    lebih jelas, dan tetap nyaman.
                                </p>

                                <div
                                    class="mt-6 flex max-w-sm items-center gap-2 rounded-full bg-white/5 p-1.5 ring-1 ring-white/10"
                                >
                                    <input
                                        type="email"
                                        placeholder="Alamat email"
                                        class="min-w-0 flex-1 bg-transparent px-3 py-2 text-sm text-white placeholder:text-white/40 focus:outline-none"
                                    />
                                    <button
                                        type="button"
                                        class="rounded-xl bg-[#339966] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#2b8055]"
                                    >
                                        Berlangganan
                                    </button>
                                </div>
                            </div>

                            <div>
                                <p
                                    class="text-sm font-semibold tracking-[0.08em] text-white/85 uppercase"
                                >
                                    | Tautan Cepat
                                </p>
                                <ul
                                    class="mt-4 grid gap-3 text-sm text-white/60"
                                >
                                    <li>
                                        <a
                                            href="#beranda"
                                            class="transition hover:text-white"
                                            >Beranda</a
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
                                        <a
                                            href="#tentang"
                                            class="transition hover:text-white"
                                            >Tentang</a
                                        >
                                    </li>
                                    <li>
                                        <a
                                            href="#dokter"
                                            class="transition hover:text-white"
                                            >Dokter</a
                                        >
                                    </li>
                                    <li>
                                        <a
                                            href="#artikel"
                                            class="transition hover:text-white"
                                            >Artikel</a
                                        >
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <p
                                    class="text-sm font-semibold tracking-[0.08em] text-white/85 uppercase"
                                >
                                    | Alamat
                                </p>
                                <div
                                    class="mt-4 grid gap-4 text-sm text-white/65"
                                >
                                    <p>{{ contact.address }}</p>
                                    <p>{{ contact.phone }}</p>
                                    <p>{{ contact.email }}</p>
                                    <p>{{ contact.whatsapp }}</p>
                                    <p>{{ contact.website }}</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-white/45 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <p>
                                © {{ currentYear }} {{ hospitalName }}. Semua
                                hak dilindungi.
                            </p>
                            <div class="flex gap-6">
                                <a
                                    href="#kontak"
                                    class="transition hover:text-white"
                                    >Kebijakan Privasi</a
                                >
                                <a
                                    href="#kontak"
                                    class="transition hover:text-white"
                                    >Syarat & Ketentuan</a
                                >
                                <a
                                    href="#kontak"
                                    class="transition hover:text-white"
                                    >Lisensi</a
                                >
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <div
            class="fixed right-4 bottom-4 z-40 flex flex-col items-end gap-3 sm:right-6 sm:bottom-6"
        >
            <a
                :href="whatsappUrl"
                target="_blank"
                rel="noopener"
                aria-label="Chat WhatsApp"
                class="group relative flex size-14 items-center justify-center rounded-xl bg-[#339966] text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-[#2b8055]"
            >
                <span
                    class="absolute inline-flex size-full animate-ping rounded-xl bg-[rgba(51,153,102,0.25)] opacity-20"
                />
                <MessageCircle class="relative size-6" />
                <span
                    class="pointer-events-none absolute right-full mr-3 hidden rounded-xl bg-slate-950 px-3 py-2 text-xs font-medium whitespace-nowrap text-white shadow-lg sm:group-hover:block"
                >
                    Hubungi via WhatsApp
                </span>
            </a>
        </div>

        <Dialog v-model:open="assistantOpen" :modal="false">
            <DialogContent
                :show-close-button="false"
                overlay-class="pointer-events-none bg-transparent"
                :style="assistantPopupStyle"
                class="top-0 left-0 h-[min(720px,calc(100dvh-7rem))] min-h-0 w-[360px] max-w-[calc(100vw-2rem)] [transform-origin:var(--assistant-origin)] translate-x-0 translate-y-0 gap-0 overflow-hidden rounded-2xl border border-slate-200 p-0 shadow-[0_28px_90px_-30px_rgba(15,23,42,0.35)] duration-300 data-[state=closed]:zoom-out-95 data-[state=closed]:slide-out-to-top-4 data-[state=open]:zoom-in-95 data-[state=open]:slide-in-from-top-4"
            >
                <AssistantChatPanel
                    :hospital-name="hospitalName"
                    :welcome-message="welcomeMessage"
                    :categories="chatCategories"
                    :suggestions="chatSuggestions"
                    :contact="{
                        phone: contact.phone,
                        igd: contact.igd,
                        whatsapp: contact.whatsapp,
                    }"
                    @close="assistantOpen = false"
                />
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
.doctor-track {
    animation: doctor-marquee 28s linear infinite;
    will-change: transform;
}

.doctor-track:hover {
    animation-play-state: paused;
}

.testimonial-track {
    transition: transform 0.45s ease;
    will-change: transform;
}

@keyframes doctor-marquee {
    from {
        transform: translateX(0);
    }

    to {
        transform: translateX(-50%);
    }
}
</style>
