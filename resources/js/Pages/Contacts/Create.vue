<template>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-8">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Ajouter un contact</h1>
            <Link :href="route('contacts.index')" class="text-blue-600 hover:text-blue-800 flex items-center">
                <ArrowLeftIcon class="h-5 w-5 mr-1" /> Retour
            </Link>
        </div>

        <!-- Message flash -->
        <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            {{ $page.props.flash.success }}
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative">
                        <input id="name" v-model="form.name" type="text" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Entrez le nom" />
                        <UserIcon class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
                    </div>
                    <p v-if="errors.name" class="mt-1 text-sm text-red-500">{{ errors.name }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative">
                        <input id="email" v-model="form.email" type="email" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Entrez l'email" />
                        <EnvelopeIcon class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
                    </div>
                    <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email }}</p>
                </div>

                <!-- Téléphone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                    <div class="mt-1 relative">
                        <input id="phone" v-model="form.phone" type="text"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Entrez le numéro de téléphone" />
                        <PhoneIcon class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
                    </div>
                </div>

                <!-- Entreprise -->
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700">Entreprise</label>
                    <div class="mt-1 relative">
                        <input id="company" v-model="form.company" type="text"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Entrez le nom de l'entreprise" />
                        <BuildingOfficeIcon class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
                    </div>
                </div>

                <!-- Pays -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Pays <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative">
                        <select id="country" v-model="form.country" @change="updateSelectedCountry" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Sélectionner un pays</option>
                            <option v-for="country in countries" :key="country.code" :value="country.name">
                                {{ country.name }}
                            </option>
                        </select>
                        <GlobeAltIcon class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
                    </div>
                    <p v-if="errors.country" class="mt-1 text-sm text-red-500">{{ errors.country }}</p>
                </div>

                <!-- Drapeau -->
                <div v-if="selectedCountry" class="flex items-center">
                    <label class="block text-sm font-medium text-gray-700 mr-3">Drapeau</label>
                    <img :src="selectedCountry.flag" alt="Drapeau" class="h-8 w-12 rounded" />
                </div>

                <!-- Pays de vol -->
                <div>
                    <label for="country_fly" class="block text-sm font-medium text-gray-700">Pays de vol</label>
                    <div class="mt-1 relative">
                        <select id="country_fly" v-model="form.country_fly"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Aucun</option>
                            <option v-for="country in countries" :key="country.code" :value="country.name">
                                {{ country.name }}
                            </option>
                        </select>
                        <PaperAirplaneIcon class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex space-x-3">
                    <button type="submit" :disabled="submitting"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <CheckCircleIcon class="h-5 w-5 mr-1" />
                        {{ submitting ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                    <Link :href="route('contacts.index')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 flex items-center transition">
                        <XCircleIcon class="h-5 w-5 mr-1" /> Annuler
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { 
    UserIcon, 
    EnvelopeIcon, 
    PhoneIcon, 
    BuildingOfficeIcon, 
    GlobeAltIcon,
    PaperAirplaneIcon, // Remplacé PlaneIcon par PaperAirplaneIcon
    CheckCircleIcon, 
    XCircleIcon,
    ArrowLeftIcon 
} from '@heroicons/vue/24/solid';

const props = defineProps({
    countries: {
        type: Array,
        default: [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const form = reactive({
    name: '',
    email: '',
    phone: '',
    company: '',
    country: '',
    country_fly: '',
});

const selectedCountry = ref(null);
const submitting = ref(false);

function updateSelectedCountry() {
    selectedCountry.value = props.countries.find(c => c.name === form.country) || null;
}

function submit() {
    submitting.value = true;
    router.post(route('contacts.store'), form, {
        onSuccess: () => {
            submitting.value = false;
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
            submitting.value = false;
        },
    });
}
</script>