<template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Contacts</h1>
            <Link :href="route('contacts.create')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <PlusIcon class="h-5 w-5 inline-block mr-1" /> Ajouter un contact
            </Link>
        </div>

        <!-- Message flash -->
        <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            {{ $page.props.flash.success }}
        </div>

        <!-- Grille de cartes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="contact in contacts.data" :key="contact.id" class="bg-white shadow-lg rounded-lg p-6 transition-transform transform hover:scale-105">
                <!-- En-tête avec drapeau et nom -->
                <div class="flex items-center mb-4">
                    <img v-if="getCountryFlag(contact.country)" :src="getCountryFlag(contact.country)" alt="Drapeau" class="h-8 w-12 mr-3 rounded" />
                    <span v-else class="text-gray-400 mr-3">Sans drapeau</span>
                    <h2 class="text-xl font-semibold text-gray-900">{{ contact.name }}</h2>
                </div>
                <!-- Informations -->
                <div class="space-y-2 mb-4">
                    <p class="text-gray-600"><span class="font-medium">Email :</span> {{ contact.email }}</p>
                    <p class="text-gray-600"><span class="font-medium">Pays :</span> {{ contact.country }}</p>
                    <p v-if="contact.company" class="text-gray-600"><span class="font-medium">Entreprise :</span> {{ contact.company }}</p>
                    <p v-if="contact.phone" class="text-gray-600"><span class="font-medium">Téléphone :</span> {{ contact.phone }}</p>
                    <p v-if="contact.country_fly" class="text-gray-600"><span class="font-medium">Pays de vol :</span> {{ contact.country_fly }}</p>
                </div>
                <!-- Actions -->
                <div class="flex space-x-3">
                    <Link :href="route('contacts.edit', contact.id)" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                        <PencilIcon class="h-5 w-5 mr-1" /> Modifier
                    </Link>
                    <button @click="openDeleteModal(contact.id)" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center">
                        <TrashIcon class="h-5 w-5 mr-1" /> Supprimer
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="contacts.data.length" class="mt-6 flex justify-center">
            <div class="flex space-x-2">
                <!-- Lien Précédent -->
                <Link v-if="contacts.prev_page_url" :href="contacts.prev_page_url" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Précédent
                </Link>
                <span v-else class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">Précédent</span>

                <!-- Numéros de page -->
                <template v-for="link in contacts.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url" v-html="link.label" :class="[
                        'px-4 py-2 rounded-lg',
                        link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                    ]"></Link>
                    <span v-else v-html="link.label" class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed"></span>
                </template>

                <!-- Lien Suivant -->
                <Link v-if="contacts.next_page_url" :href="contacts.next_page_url" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Suivant
                </Link>
                <span v-else class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">Suivant</span>
            </div>
        </div>
        <!-- Message si aucun contact -->
        <div v-else class="text-center text-gray-500 mt-6">
            Aucun contact trouvé.
        </div>

        <!-- Modal de confirmation de suppression -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <!-- En-tête du modal -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Confirmer la suppression</h2>
                    <button @click="closeDeleteModal" class="text-gray-500 hover:text-gray-700">
                        <XMarkIcon class="h-6 w-6" />
                    </button>
                </div>
                <!-- Contenu du modal -->
                <p class="text-gray-600 mb-6">
                    Voulez-vous vraiment supprimer ce contact ? Cette action est irréversible.
                </p>
                <!-- Boutons -->
                <div class="flex justify-end space-x-3">
                    <button @click="closeDeleteModal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 flex items-center">
                        <XCircleIcon class="h-5 w-5 mr-1" /> Annuler
                    </button>
                    <button @click="confirmDelete" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center">
                        <TrashIcon class="h-5 w-5 mr-1" /> Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { 
    PencilIcon, 
    TrashIcon, 
    PlusIcon, 
    XMarkIcon, 
    XCircleIcon 
} from '@heroicons/vue/24/solid';

const props = defineProps({
    contacts: {
        type: Object,
        default: () => ({ data: [], links: [], prev_page_url: null, next_page_url: null }),
    },
    countries: {
        type: Array,
        default: [],
    },
});

// État du modal
const showDeleteModal = ref(false);
const contactIdToDelete = ref(null);

// Afficher props.countries et props.contacts dans la console
onMounted(() => {
    console.log('props.countries:', props.countries);
    console.log('props.contacts:', props.contacts);
});

function getCountryFlag(countryName) {
    if (!Array.isArray(props.countries) || !countryName) {
        console.log('getCountryFlag: countries non valide ou countryName vide', { countryName, countries: props.countries });
        return null;
    }
    const country = props.countries.find(c => {
        const match = c.name === countryName;
        if (!match) {
            console.log(`Pas de correspondance pour ${countryName} avec ${c.name}`);
        }
        return match;
    });
    return country ? country.flag : null;
}

function openDeleteModal(id) {
    contactIdToDelete.value = id;
    showDeleteModal.value = true;
}

function closeDeleteModal() {
    showDeleteModal.value = false;
    contactIdToDelete.value = null;
}

function confirmDelete() {
    if (contactIdToDelete.value) {
        console.log('ID du contact à supprimer:', contactIdToDelete.value);
        const url = route('contacts.destroy', contactIdToDelete.value);
        console.log('URL générée pour DELETE:', url);
        router.delete(url, {
            onSuccess: () => {
                closeDeleteModal();
            },
            onError: (errors) => {
                console.log('Erreur lors de la suppression:', errors);
            },
        });
    } else {
        console.log('Erreur: Aucun ID de contact défini');
    }
}
</script>